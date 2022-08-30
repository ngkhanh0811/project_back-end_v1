@extends('front.layout.master')

@section('title', 'Result')

@section('body')
    <!-- Breadcrumb Section Begin -->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-text">
                        <a href="./index"><i class="fa-fa-home"></i>Home</a>
                        <a href="./checkout">Check Out</a>
                        <span>Result</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End -->

    <!--section begin-->
    <div class="checkout-section spad">
        <div class="container">
            <div class="col-lg-12">
                <h4>{{ $notification }}</h4>
            </div>

            <a href="./" class="primary-btn nt-5">Continue Shopping</a>
        </div>
    </div>
    <!-- End -->
@endsection
