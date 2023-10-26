@extends('Layouts.main_layout')

@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/views/Product/form.css')}}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endpush

@section('content')

<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Create Product</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/product/index">Ecommerce</a></li>
                                <li class="breadcrumb-item active">Create Product</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <form id="createproduct-form" autocomplete="off" class="needs-validation" novalidate="" target="calldata" enctype="multipart/form-data" >
                <input type="hidden" value="{{ $product ? $product->product_id : ''}}" id="product_id" name="product_id" >
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label" for="name_th">ชื่อสินค้า (TH) <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="name_th" id="name_th" value="{{ $product ? $product->name_th : ''}}" placeholder="ชื่อสินค้า (TH)" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="name_en">ชื่อสินค้า (EN)</label>
                                        <input type="text" class="form-control" name="name_en" id="name_en" value="{{ $product ? $product->name_gb : ''}}" placeholder="ชื่อสินค้า (EN)">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="product_code">รหัสสินค้า</label>
                                        <input type="text" class="form-control" name="product_code" id="product_code" value="{{ $product ? $product->product_code : ''}}" placeholder="รหัสสินค้า">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="barcode">บาร์โค้ด</label>
                                        <input type="text" class="form-control" name="barcode" id="barcode" value="{{ $product ? $product->barcode : ''}}" placeholder="บาร์โค้ด">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="sku">รุ่น</label>
                                        <input type="text" class="form-control" name="sku" id="sku" value="{{ $product ? $product->sku : ''}}" placeholder="รุ่น">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="cost_price">ราคาทุน</label>
                                        <input type="text" class="form-control" name="cost_price" id="cost_price" value="{{ $product ? $product->cost_price : ''}}" placeholder="ราคาทุน">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="sell_price">ราคาขาย <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="sell_price" id="sell_price" value="{{ $product ? $product->sell_price : ''}}" placeholder="ราคาขาย" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#discriptionTH" role="tab" aria-selected="true">
                                                    รายละเอียด (TH)
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" data-bs-toggle="tab" href="#discriptionEN" role="tab" aria-selected="false" tabindex="-1">
                                                    รายละเอียด (EN)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="tab-content mt-4">
                                            <div class="tab-pane show active" id="discriptionTH" role="tabpanel">
                                                <textarea class="discript_th" name="description_th" id="discript_th">
                                                    {!! $product ? $product->description_th : '' !!}
                                                </textarea>
                                            </div>
                                            <div class="tab-pane" id="discriptionEN" role="tabpanel">
                                                <textarea class="description_en" name="description_en" id="description_en">
                                                    {!! $product ? $product->description_gb : '' !!}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">ตัวเลือกสินค้า/สต็อคสินค้า</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            {{$product->option_status}}
                                            <input class="form-check-input" type="checkbox" name="option_status" role="switch" id="checkOptionST" value="Y" {{ $product && $product->option_status == 'Y' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="checkOptionST">ตัวเลือกสินค้า</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="box-not-option">
                                    <div class="col-12 mb-3">
                                        <b>จำนวน: </b>ควบคุมจำนวนหรือ <input class="form-check-input" name="stock_status" type="checkbox" id="stock_status" name="stock_status">  ไม่จำกัด
                                    </div>
                                    <div class="col-12"  id="branchBox">
                                        <div class="row">
                                            <div class="col-4"><b>สาขา</b></div>
                                            <div class="col-4"><b>จำนวนสินค้าตอนนี้</b></div>
                                            <div class="col-4"><b>จำนวนสินค้าขั้นต่ำ(ซ่อนสินค้าหน้าเว็บ)</b></div>
                                            <div class="col-12"><hr></div>
                                            @foreach ($branch as $key => $item)
                                                <div class="col-4 mb-2">{{$item->branch_name_th}}</div>
                                                <div class="col-4 mb-2">
                                                    <input type="hidden" name="branchid[]" value="{{$item->branch_id}}">
                                                    <input type="text" name="stock{{$item->branch_id}}" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าตอนนี้">
                                                </div>
                                                <div class="col-4 mb-2">
                                                    <input type="text" name="stock_alert{{$item->branch_id}}" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าขั้นต่ำ">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    
                                </div>

                                <div id="box-option">
                                    @php
                                        $option_opt = [];
                                        $count = 1;
                                        if ($product && $product->sel_option != '') {
                                            $option_opt = explode(',', $product->sel_option);
                                        }
                                    @endphp
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="option-box">
                                                <div class="row">
                                                    @foreach ($masterOption as $item)
                                                        <div class="col-4">
                                                            <div class="form-check mb-3">
                                                                <input class="form-check-input sel_option" 
                                                                data-name="{{$item->option_name}}" {{in_array($item->m_option_id,$option_opt)  ? 'checked' : ''}}
                                                                type="checkbox" id="checkOption{{$item->m_option_id}}" name="sel_option[]" value="{{$item->m_option_id}}">
                                                                <label class="form-check-label" for="checkOption{{$item->m_option_id}}">
                                                                    {{$item->option_name}}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @if ($product)
                                                    @foreach ($product->option as $key => $item)
                                                    <div class="option-choose option-{{$key+1}}" data-id="{{$key+1}}">
                                                        @foreach ($item->picture as $pic)
                                                            <input type="text" class="fileoption{{$key+1}}" value="{{ asset('data/img/product_option/images/'.$pic->picture_name."1200".$pic->picture_extension) }}" data-id="{{$pic->id}}" data-file="{{$pic->picture_name."1200".$pic->picture_extension}}">
                                                        @endforeach
                                                        <input type="text" class="option_id{{$key+1}}" name="option_id{{$key+1}}" value="{{ $item->option_id }}">
                                                        <div class="form-group">
                                                            <input type="hidden" name="hiddenoption{{$key+1}}" id="hiddenoption{{$key+1}}" value="">
                                                            <input type="hidden" name="hidden_option_rowno[]" class="hidden_option_rowno" value="{{$key+1}}">
                                                            <input type="hidden" id="option_name_sectionhidden_option_rowno_{{$key+1}}" value="">
                                                        </div>
                                                        <h5>ตัวเลือกที่ {{$key+1}}</h5>
                                                        <div class="row mb-3">
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">บาร์โค้ด</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="barcode_option{{$key+1}}[]" value="{{ $item->barcode ? $item->barcode : '' }}" placeholder="บาร์โค้ด">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">รุ่น</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="sku_option{{$key+1}}[]" value="{{ $item->sku ? $item->sku : '' }}" placeholder="รุ่น">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">Order</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="orderby_option{{$key+1}}[]" value="{{ $item->orderby ? $item->orderby : '' }}" placeholder="Order">
                                                            </div>
                                                        </div>
                                                        <div class="row box-option-add">
                                                            @foreach ($masterOption as $option_c)
                                                                
                                                                @if (in_array($option_c->m_option_id,$option_opt))
                                                                    <div class="col-4 mb-3 option-{{$key+1}}" data-id="{{$key+1}}">
                                                                        <label class="form-label" for="product-title-input">{{$option_c->option_name}}</label>
                                                                        <input type="hidden" name="sel_master_option_{{$option_c->m_option_id}}[]" class="sel_master_option" value="{{$option_c->m_option_id}}" />
                                                                        <select class="selectpicker" name="sel_m_option_id_{{$option_c->m_option_id}}[]" id="">
                                                                            @foreach ($option_c->option_parent as $parent)
                                                                                <option value="{{$parent->m_option_id}}" {{in_array($parent->m_option_id,$item->subs)  ? 'selected' : ''}}>{{$parent->option_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">ราคาทุน</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="cost_price_option{{$key+1}}[]" value="{{ $item->cost_price ? $item->cost_price : '' }}" placeholder="ราคาทุน">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">ราคาตลาด</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="market_price_option{{$key+1}}[]" value="{{ $item->market_price ? $item->market_price : '' }}" placeholder="ราคาตลาด" >
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">ราคาขาย <span class="text-red">*</span></label>
                                                                <input type="text" class="form-control" id="product-title-input" name="sell_price_option{{$key+1}}[]" value="{{ $item->sell_price ? $item->sell_price : '' }}" placeholder="ราคาขาย" required>
                                                            </div>
                                                            <div class="col-12 mt-3">
                                                                <div class="dropzone" id="productOption_{{$key+1}}"></div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4"><b>สาขา</b></div>
                                                            <div class="col-4"><b>จำนวนสินค้าตอนนี้</b></div>
                                                            <div class="col-4"><b>จำนวนสินค้าขั้นต่ำ(ซ่อนสินค้าหน้าเว็บ)</b></div>
                                                            <div class="col-12"><hr></div>
                                                            @if ($product)
                                                                @foreach ($item->stock as $stock)
                                                                    <div class="col-4 mb-2">{{$stock->branch_name_th}}</div>
                                                                    <div class="col-4 mb-2">
                                                                        <input type="hidden" name="branchid_{{$key+1}}[]" value="{{$stock->branch_id}}">
                                                                        <input type="text" name="stock_{{$key+1}}{{$stock->branch_id}}[]" class="form-control" id="product-title-input" value="{{$stock->stock}}" placeholder="จำนวนสินค้าตอนนี้">
                                                                    </div>
                                                                    <div class="col-4 mb-2">
                                                                        <input type="text" name="stock_alert_{{$key+1}}{{$stock->branch_id}}[]" class="form-control" id="product-title-input" value="{{$stock->stock_alert}}" placeholder="จำนวนสินค้าขั้นต่ำ">
                                                                </div>
                                                                @endforeach
                                                            @else
                                                                @foreach ($branch as $k => $item)
                                                                    <div class="col-4 mb-2">{{$item->branch_name_th}}</div>
                                                                    <div class="col-4 mb-2">
                                                                        <input type="hidden" name="branchid_{{$key+1}}[]" value="{{$item->branch_id}}">
                                                                        <input type="text" name="stock_{{$key+1}}{{$item->branch_id}}[]" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าตอนนี้">
                                                                    </div>
                                                                    <div class="col-4 mb-2">
                                                                        <input type="text" name="stock_alert_{{$key+1}}{{$item->branch_id}}[]" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าขั้นต่ำ">
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            
                                                        </div>
                                                    </div>
                                                    @php
                                                        $count++;
                                                    @endphp
                                                    @endforeach
                                                @else
                                                    <div class="option-choose option-1" data-id="1">                                                        
                                                        <div class="form-group">
                                                            <input type="hidden" name="hiddenoption1" id="hiddenoption1" value="">
                                                            <input type="hidden" name="hidden_option_rowno[]" class="hidden_option_rowno" value="1">
                                                            <input type="hidden" id="option_name_section_1" value="">
                                                        </div>
                                                        <h5>ตัวเลือกที่ 1</h5>
                                                        <div class="row mb-3">
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">บาร์โค้ด</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="barcode_option1[]" value="" placeholder="บาร์โค้ด">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">รุ่น</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="sku_option1[]" value="" placeholder="รุ่น">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">Order</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="orderby_option1[]" value="" placeholder="Order">
                                                            </div>
                                                        </div>
                                                        <div class="row box-option-add">
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">ราคาทุน</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="cost_price_option1[]" value="" placeholder="ราคาทุน">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">ราคาตลาด</label>
                                                                <input type="text" class="form-control" id="product-title-input" name="market_price_option1[]" value="" placeholder="ราคาตลาด" >
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label" for="product-title-input">ราคาขาย <span class="text-red">*</span></label>
                                                                <input type="text" class="form-control" id="product-title-input" name="sell_price_option1[]" value="" placeholder="ราคาขาย" required>
                                                            </div>
                                                            <div class="col-12 mt-3">
                                                                <div class="dropzone" id="productOption_1"></div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4"><b>สาขา</b></div>
                                                            <div class="col-4"><b>จำนวนสินค้าตอนนี้</b></div>
                                                            <div class="col-4"><b>จำนวนสินค้าขั้นต่ำ(ซ่อนสินค้าหน้าเว็บ)</b></div>
                                                            <div class="col-12"><hr></div>
                                                            @foreach ($branch as $k => $item)
                                                                <div class="col-4 mb-2">{{$item->branch_name_th}}</div>
                                                                <div class="col-4 mb-2">
                                                                    <input type="hidden" name="branchid_1[]" value="{{$item->branch_id}}">
                                                                    <input type="text" name="stock_1{{$item->branch_id}}[]" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าตอนนี้">
                                                                </div>
                                                                <div class="col-4 mb-2">
                                                                    <input type="text" name="stock_alert_1{{$item->branch_id}}[]" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าขั้นต่ำ">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                </div>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <button class="btn btn-success add-option-box" type="button" data-count="{{$count}}" > <i class="mdi mdi-plus-circle"></i> เพิ่มตัวเลือก</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Product Gallery</h5>
                            </div>
                            <div class="card-body">
                                @if ($product)
                                    @foreach ($product->picture as $pic)
                                        <input type="text" class="fileGallery" value="{{ asset('data/img/product/'.$pic->picture_name."800".$pic->picture_extension) }}" data-file="{{$pic->picture_name."800".$pic->picture_extension}}">
                                    @endforeach
                                @endif
                                
                                <div class="dropzone" id="productGallery"></div>
                            </div>
                        </div>
                        <!-- end card -->

                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                    {{-- <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#addproduct-general-info" role="tab" aria-selected="true">
                                            General Info
                                        </a>
                                    </li> --}}
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#addproduct-metadata" role="tab" aria-selected="false" tabindex="-1">
                                            Meta Data
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- end card header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane" id="addproduct-general-info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="manufacturer-name-input">Manufacturer Name</label>
                                                    <input type="text" class="form-control" name="manufacturerName" id="manufacturer-name-input" placeholder="Enter manufacturer name">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="manufacturer-brand-input">Manufacturer Brand</label>
                                                    <input type="text" class="form-control" name="manufacturerBrand" id="manufacturer-brand-input" placeholder="Enter manufacturer brand">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="stocks-input">Stocks</label>
                                                    <input type="text" class="form-control" id="stocks-input" placeholder="Stocks">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-price-input">Price</label>
                                                    <div class="input-group has-validation mb-3">
                                                        <span class="input-group-text" id="product-price-addon">$</span>
                                                        <input type="text" class="form-control" id="product-price-input" placeholder="Enter price" aria-label="Price" aria-describedby="product-price-addon">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="product-discount-input">Discount</label>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text" id="product-discount-addon">%</span>
                                                        <input type="text" class="form-control" id="product-discount-input" placeholder="Enter discount" aria-label="discount" aria-describedby="product-discount-addon">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="orders-input">Orders</label>
                                                    <input type="text" class="form-control" id="orders-input" placeholder="Orders" >
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end tab-pane -->

                                    <div class="tab-pane active show" id="addproduct-metadata" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="meta-title-input">Meta title</label>
                                                    <input type="text" class="form-control" name="meta_title_th" placeholder="Enter meta title" value="{{ $product ? $product->meta_title_th : '' }}" id="meta-title-input">
                                                </div>
                                            </div>
                                            <!-- end col -->

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                                    <input type="text" class="form-control" name="meta_title_en" placeholder="Enter meta keywords" value="{{ $product ? $product->meta_title_gb : '' }}" id="meta-keywords-input">
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->

                                        <div>
                                            <label class="form-label" for="meta-description-input">Meta Description</label>
                                            @php
                                                $newcontent = "";
                                                if($product){
                                                    $newcontent = preg_replace("/<p[^>]*?>/", "", $product->meta_description_th);
                                                    $newcontent = str_replace("</p>", "\n\n", $newcontent);
                                                }
                                            @endphp
                                            <textarea class="form-control" name="meta_description_th" id="meta-description-input" placeholder="Enter meta description" rows="3">{!! nl2br($newcontent) !!}</textarea>
                                        </div>
                                    </div>
                                    <!-- end tab pane -->
                                </div>
                                <!-- end tab content -->
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                        <div class="text-end mb-3">
                            <button type="submit" class="btn btn-success w-sm" id="uploadfiles">Submit</button>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline"> จัดการหมวดหมู่</a>
                                        หมวดหมู่หลัก <span class="text-red">*</span></p>
                                    <select class="selectpicker" id="cat_id" name="cat_id" data-live-search="true" data-size="5">
                                        <option value="">หมวดหมู่หลัก</option>
                                        @foreach ($cat as $item)
                                            <option value="{{$item['cat_id']}}" {{ $product && $product->cat_id == $item['cat_id'] ? 'selected' : '' }} >{{$item['cat_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline"> จัดการหมวดหมู่</a>
                                        หมวดหมู่เพิ่มเติม</p>
                                    <select class="selectpicker" id="other_cat_id" name="other_cat_id[]" data-live-search="true" data-size="5"  multiple data-selected-text-format="count > 4">
                                        @foreach ($cat as $item)
                                            <option value="{{$item['cat_id']}}">{{$item['cat_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline"> จัดการแบรนด์</a>
                                        แบรนด์</p>
                                    <select class="selectpicker" id="brand_id" name="brand_id" data-live-search="true" data-size="5">
                                        <option value="">แบรนด์</option>
                                        @foreach ($brand as $item)
                                            <option value="{{$item->brand_id}}">{{$item->brand_name_th}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline">  จัดการผู้ผลิต/จำหน่าย</a>
                                        ผู้ผลิต/จำหน่าย</p>
                                    <select class="selectpicker" id="supplier_id" name="supplier_id" data-live-search="true" data-size="5">
                                        <option value="">ผู้ผลิต/จำหน่าย</option>
                                        @foreach ($supplier as $item)
                                            <option value="{{$item->supplier_id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

            </form>

        </div>
    </div>
</div>

@endsection

@push('script')    
    <script src="https://cdn.tiny.cloud/1/3of8d3z2fu4pp5rkbjgt8r62bfhpoi3ql8jp3p8tvncs2dlp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <!-- dropzone min -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>

    <script src="{{ asset('assets/js/views/Product/form.js?v='.time()) }}"></script>
@endpush