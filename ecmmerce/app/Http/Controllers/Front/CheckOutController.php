<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Utilities\VNPay;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Mail;

class CheckOutController extends Controller
{
    public function index(){
        $carts = Cart::content();
        $total = Cart::total();
        $subtotal = Cart::subtotal();

        return view('front.checkout.index', compact('carts', 'total', 'subtotal'));
    }

    public function addOrder(Request $request)
    {
            //        01. Thêm đơn hàng
            $order = Order::create($request->all());

//        02. Thêm chi tiết đơn hàng
            $carts = Cart::content();

            foreach($carts as $cart) {
                $data = [
                    'order_id' => $order->id,
                    'product_id' => $cart->id,
                    'qty' =>$cart->qty,
                    'amount' =>$cart->price,
                    'total' =>$cart->price * $cart->qty,
                ];

                OrderDetail::create($data);
            }
        if ($request->payment_type == 'payment_later'){
//            03. Gửi email
            $total = Cart::total();
            $subtotal = Cart::subtotal();
            $this->sendEmail($order, $total, $subtotal);

//        04. Xóa giỏ hàng
            Cart::destroy();

//        05. Trả về kết quả
            return redirect('checkout/result')
                ->with('notification', 'Success! You will pay on delivery. Please check your email.');
        }
        if ($request->payment_type == 'online_payment'){
//            01. Lấy url thanh toán VNPAY
            $data_url = VNPay::vnpay_create_payment([
                'vnp_Txnref' => $order->id, // ID của đơn hàng
                'vnp_OrderInfo' => '', //
                'vnp_Amount' => Cart::total(0, '', '') * 23075,
            ]);
//            02. Chuyển hướng tới url lấy được
            return redirect()->to($data_url);

        }
}

    public function vnPayCheck(Request $request)
    {
//        01. Lấy dữ liệu từ url do VNPAY gửi về qua $vnp_Returnurl
        $vnp_ResponseCode = $request->get('vnp_ResponseCode'); //Mã phản hồi kết quả thanh toán. 00 = Thành công
        $vnp_TxnRef = $request->get('vnp_TxnRef'); //ticket_id
        $vnp_Amount = $request->get('vnp_Amount'); //Số tiền thanh toán

//        02. Kiểm tra kết quả giao dịch trả về từ VN Pay
        if ($vnp_ResponseCode != null) {
            //Nếu thành công:
            if ($vnp_ResponseCode == 00) {
                //Gửi email
                $order = Order::find($vnp_TxnRef);
                $total = Cart::total();
                $subtotal = Cart::subtotal();
                $this->sendEmail($order, $total, $subtotal);

                //Xóa giỏ hàng
                Cart::destroy();

                //Thông báo kết quả
                return redirect('checkout/result')
                    ->with('notification', 'Success! You will pay on delivery. Please check your email.');
            } else { //Nếu không thành công
                // Xóa đơn hàng đã thêm vào database
                Order::find($vnp_TxnRef)->delete();

                // trả về thông báo lỗi
                return redirect('checkout/result')
                    ->with('notification', 'ERROR: Payment failed or canceled!');
            }
        }
    }

    public function result(){

        $notification = session('notification');

        return view('front.checkout.result',  compact('notification'));
    }


    private function sendEmail($order, $total, $subtotal){
        $email_to = $order->email;

        Mail::send('front.checkout.email', compact('order', 'total', 'subtotal'), function($message) use ($email_to){
            $message->from('hex.4b68616e68@gmail.com', '14th Eleventh');
            $message->to($email_to, $email_to);
            $message->subject('Order Notification');
        });
    }
}
