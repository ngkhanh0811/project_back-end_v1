<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductComment;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function show($id){
        $product = Product::findOrFail($id);

        $categories = ProductCategory::all();
        $brands = Brand::all();

//        Calc to average Rating:
        $avgRating = 0;
        $sumRating = array_sum(array_column($product->productComments->toArray(), 'rating'));
        $countRating = count($product->productComments);
        if ($countRating != 0) {
            $avgRating = $sumRating/$countRating;
        }

        $relatedProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('tag', $product->tag)
            ->limit(4)->get();

        return view('front.shop.product', compact('product', 'avgRating', 'relatedProducts', 'categories', 'brands'));
    }

    public function postComment(Request $request){
        ProductComment::create($request->all());

        return redirect()->back();
    }

    public function index(Request $request){
//        Get Category, Brands:
        $categories = ProductCategory::all();
        $brands = Brand::all();

//        Get Products:
        $perPage = $request->show ?? 3;
        $sortBy = $request->sort_by ?? 'latest';
        $search = $request->search ?? '';

//        Search Products by name:
        $products = Product::where('name', 'like', '%' . $search . '%');

        $products = $this->filter($products, $request);

        $products = $this->sortAndPaginate($products, $sortBy, $perPage);

        return view('front.shop.index', compact('products', 'categories', 'brands'));
    }

    public function category($categoryName, Request $request){
        //        Get Category, Brands:
        $categories = ProductCategory::all();
        $brands = Brand::all();

//        Get Product:
        $products = ProductCategory::where('name', $categoryName)->first()->products->toQuery();

        $products = $this->filter($products, $request);

        $perPage = $request->show ?? 3;
        $sortBy = $request->sort_by ?? 'latest';
        $products = $this->sortAndPaginate($products, $sortBy, $perPage);

        return view('front.shop.index', compact('categories', 'products', 'brands'));
    }

    public function sortAndPaginate($products, $sortBy, $perPage){
        //        Switch-case orderBy:
        switch ($sortBy){
            case 'latest':
                $products = $products->orderBy('id');
                break;
            case 'oldest':
                $products = $products->orderByDesc('id');
                break;
            case 'name-ascending':
                $products = $products->orderBy('name');
                break;
            case 'name-descending':
                $products = $products->orderByDesc('name');
                break;
            case 'price-ascending':
                $products = $products->orderBy('price');
                break;
            case 'price-descending':
                $products = $products->orderByDesc('price');
                break;
            default:
                $products = $products->orderBy('id');
        }

//        Display item in page:
        $products = $products->paginate($perPage);

//        Sort in page:
        $products->appends(['sort_by' => $sortBy, 'show' => $perPage]);

        return $products;
    }

    public function filter($products, Request $request)
    {
//        Brand
        $brands = $request->brand ?? [];
        $brands_ids = array_keys($brands);
        $products = $brands_ids != null ? $products->whereIn('brand_id', $brands_ids) : $products;

//        Price
        $priceMin = $request->price_min;
        $priceMax = $request->price_max;
        $priceMin = str_replace('$', '', $priceMin);
        $priceMax = str_replace('$', '', $priceMax);

        $products = ($priceMin != null && $priceMin != null) ? $products->whereBetween('price', [$priceMin, $priceMax]) : $products;

//        Color
        $color = $request->color;
        $products = $color != null
                ? $products->whereHas('productDetails', function($query) use ($color){
                    return $query->where('color', $color)->where('qty', '>', 0);
                })
                : $products;

//        Size
        $size = $request->size;
        $products = $size != null
            ? $products->whereHas('productDetails', function ($query) use ($size){
                return $query->where('size', $size)-> where('qty', '>', 0);
            })
            :$products;

        return $products;
    }
}
