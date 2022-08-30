@extends('front.layout.master')

@section('title', 'Shop')

@section('body')
    <!-- Breadcrumb Section Begin -->
    <div class="breadcrumb-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-text">
                        <a href="index.html"><i class="fa-fa-home"></i>Home</a>
                        <span>Shop</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End -->

    <!-- Product Shop Section Begin-->
    <section class="product-shop spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-8 order-2 order-lg-1 produts-sidebar-filter">
                    @include('front.shop.components.products-sidebar-filter')
                </div>
                <div class="col-lg-9 order-1 order-lg-2">
                    <div class="product-show-option">
                        <div class="row">
                            <div class="col-lg-7 col-md-7">
                                <form action="">
                                    <div class="select-option">
                                        <select name="sort_by" class="sorting" onchange="this.form.submit();">
                                            <option {{ request('sort_by') == 'latest' ? 'selected' : '' }} value="latest">Sorting: Lastest</option>
                                            <option {{ request('sort_by') == 'oldest' ? 'selected' : '' }} value="oldest">Sorting: Oldest</option>
                                            <option {{ request('sort_by') == 'name-ascending' ? 'selected' : '' }} value="name-ascending">Sorting: Name A-Z</option>
                                            <option {{ request('sort_by') == 'name-descending' ? 'selected' : '' }} value="name-descending">Sorting: Name Z-A</option>
                                            <option {{ request('sort_by') == 'price-ascending' ? 'selected' : '' }} value="price-ascending">Sorting: Price Ascending</option>
                                            <option {{ request('sort_by') == 'price-descending' ? 'selected' : '' }} value="price-descending">Sorting: Price Decrease</option>
                                        </select>
                                        <select name="show" class="p-show" onchange="this.form.submit();">
                                            <option {{ request('show') == '3' ? 'selected' : '' }} value="3">Show: 3</option>
                                            <option {{ request('show') == '9' ? 'selected' : '' }} value="9">Show: 9</option>
                                            <option {{ request('show') == '15' ? 'selected' : '' }} value="15">Show: 15</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="product-list">
                        <div class="row">
                            @foreach($products as $product)
                            <div class="col-lg-4 col-sm-6">
                                @include('front.component.product-item')
                            </div>
                            @endforeach
                        </div>
                    </div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </section>
    <!-- End -->
@endsection
