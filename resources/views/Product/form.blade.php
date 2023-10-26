@extends('Layouts.main_layout')

@push('css')
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"  />
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}"/>
    <link rel="stylesheet" href="{{asset('assets/css/views/Product/form.css')}}">
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

            <form id="createproduct-form" autocomplete="off" class="needs-validation" novalidate="" target="calldata" enctype="multipart/form-data">
                <input type="hidden" value="{{ $product ? $product->product_id : ''}}" id="product_id" name="product_id" >
                
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="optionsType" id="success-outlined" value="N" {{ $product && $product->product_type_option == 'N' ? 'checked' : (!$product ? 'checked' : '')}}>
                    <label class="btn btn-outline-primary shadow-none" for="success-outlined"><i class="las la-cube"></i> ไม่มีตัวเลือก</label>
                    
                    <input type="radio" class="btn-check" name="optionsType" id="danger-outlined" value="Y" {{ $product && $product->product_type_option == 'Y' ? 'checked' : ''}}>
                    <label class="btn btn-outline-primary shadow-none" for="danger-outlined"><i class="las la-cubes"></i> มีตัวเลือก</label>
                </div>

                <div class="row">
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <h2>ข้อมูลทั่วไปของสินค้า</h2>
                                <div class="row mt-5 mb-3">
                                    <div class="col-6">
                                        <label class="form-label" for="name_th">ชื่อสินค้า (TH) <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="name_th" id="name_th" value="{{ $product ? $product->name_th : ''}}" placeholder="ชื่อสินค้า (TH)" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" for="name_en">ชื่อสินค้า (EN)</label>
                                        <input type="text" class="form-control" name="name_en" id="name_en" value="{{ $product ? $product->name_gb : ''}}" placeholder="ชื่อสินค้า (EN)">
                                    </div>
                                    <div class="col-3 mt-3">
                                        <p class="mb-2"> <a href="#" class="float-end "><i class="ri-add-circle-line align-middle"></i> เพิ่ม</a>
                                            หมวดหมู่หลัก <span class="text-red">*</span></p>
                                        <select class="selectpicker" id="cat_id" name="cat_id" data-live-search="true" data-size="5">
                                            <option value="">หมวดหมู่หลัก</option>
                                            @foreach ($cat as $item)
                                                <option value="{{$item['cat_id']}}" {{ $product && $product->cat_id == $item['cat_id'] ? 'selected' : '' }} >{{$item['cat_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3 mt-3">
                                        <p class="mb-2"> <a href="#" class="float-end"> <i class="ri-add-circle-line align-middle"></i> เพิ่ม</a> หมวดหมู่เพิ่มเติม</p>
                                        <select class="selectpicker" id="other_cat_id" name="other_cat_id[]" data-live-search="true" data-size="5"  multiple data-selected-text-format="count > 4">
                                            <option value="">หมวดหมู่เพิ่มเติม</option>
                                            @foreach ($cat as $item)
                                                @php
                                                    $cata = false;
                                                    if($product != ''){
                                                        $cata = array_search($item['cat_id'], $product->other_cata);
                                                    }
                                                @endphp
                                                <option value="{{$item['cat_id']}}" {{ $cata !== false ? 'selected' : ''}}>{{$item['cat_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3 mt-3">
                                        <p class="mb-2"> <a href="#" class="float-end"> <i class="ri-add-circle-line align-middle"></i> เพิ่ม</a>
                                            แบรนด์</p>
                                        <select class="selectpicker" id="brand_id" name="brand_id" data-live-search="true" data-size="5">
                                            <option value="">แบรนด์</option>
                                            @foreach ($brand as $item)
                                                <option value="{{$item->brand_id}}" {{ $product && $product->brand_id == $item->brand_id ? 'selected' : '' }}>{{$item->brand_name_th}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3 mt-3">
                                        <p class="mb-2"> <a href="#" class="float-end"> <i class="ri-add-circle-line align-middle"></i> เพิ่ม</a>
                                            ผู้ผลิต/จำหน่าย</p>
                                        <select class="selectpicker" id="supplier_id" name="supplier_id" data-live-search="true" data-size="5">
                                            <option value="">ผู้ผลิต/จำหน่าย</option>
                                            @foreach ($supplier as $item)
                                                <option value="{{$item->supplier_id}}" {{ $product && $product->supplier_id == $item->supplier_id ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 mt-4">
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

                                    <div class="col-12">
                                        <div class="tab-content mt-4">
                                            <div class="tab-pane show active" id="discriptionTH" role="tabpanel">
                                                <h5>รายละเอียดสินค้า (TH)</h5>
                                                <textarea class="discript_th" name="description_th" id="discript_th">
                                                    {!! $product ? $product->description_th : '' !!}
                                                </textarea>
                                                <h5 class="mt-3">รายละเอียดโดยย่อ (TH)</h5>
                                                <textarea class="guide_th" name="guide_th" id="guide_th">
                                                    {!! $product ? $product->description_th : '' !!}
                                                </textarea>
                                            </div>
                                            <div class="tab-pane" id="discriptionEN" role="tabpanel">
                                                <h5>รายละเอียดสินค้า (EN)</h5>
                                                <textarea class="description_en" name="description_en" id="description_en">
                                                    {!! $product ? $product->description_gb : '' !!}
                                                </textarea>
                                                <h5 class="mt-3">รายละเอียดโดยย่อ (EN)</h5>
                                                <textarea class="guide_en" name="guide_en" id="guide_en">
                                                    {!! $product ? $product->description_th : '' !!}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>



                        <div class="card">

                            <div class="card-body">
                                <h2>คลังสินค้า</h2>
                                <div class="row mt-4">
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="product_code">รหัสสินค้า</label>
                                        <input type="text" class="form-control" name="product_code" id="product_code" value="{{ $product ? $product->product_code : ''}}" placeholder="sku-xxxxxx">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="barcode">บาร์โค้ด</label>
                                        <input type="text" class="form-control" name="barcode" id="barcode" value="{{ $product ? $product->barcode : ''}}" placeholder="บาร์โค้ด">
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label class="form-label" for="barcode">รุ่น</label>
                                        <input type="text" class="form-control" name="barcode" id="barcode" value="{{ $product ? $product->barcode : ''}}" placeholder="รุ่น">
                                    </div>
                                    
                                    <div class="col-4 mb-3 price-all {{$product != "" && ($product->product_type_option == 'Y' && $product->product_type_option == 'Y') ? 'dis-none' : ''}}">
                                        <label class="form-label" for="cost_price">ราคาทุน</label>
                                        <input type="text" class="form-control" name="cost_price" id="cost_price" value="{{ $product ? $product->cost_price : ''}}" placeholder="ราคาทุน">
                                    </div>
                                    <div class="col-4 mb-3 price-all {{$product != "" && ($product->product_type_option == 'Y' && $product->product_type_option == 'Y') ? 'dis-none' : ''}}">
                                        <label class="form-label" for="sell_price">ราคาขาย<span class="text-red">*</span></label>
                                        <input type="text" class="form-control" name="sell_price" id="sell_price" value="{{ $product ? $product->sell_price : ''}}" placeholder="ราคาขาย" required>
                                    </div>
                                    <div class="col-4 mb-3 {{$product != "" && ($product->product_type_option == 'Y' && $product->product_type_option == 'Y') ? 'dis-none' : ''}}" id="quatation">
                                        <label class="form-label" for="sell_price">จำนวน (ชิ้น)<span class="text-red">*</span></label>
                                        <div>
                                            <div class="input-step w-100">
                                                <button type="button" class="minus shadow">–</button>
                                                <input type="number" class="product-quantity w-100" value="0" min="0" max="99999">
                                                <button type="button" class="plus shadow">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2 {{$product != "" && $product->product_type_option == 'Y' ? '' : 'dis-none'}}" id="price-switch">
                                        <label class="form-label" for="sell_price">ราคาของแต่ละแบบสินค้า</label>
                                        <div class="btn-group w-100" role="group" aria-label="Basic example">
                                            <input type="radio" class="btn-check" name="price_type" id="price_type" value="N" {{$product != "" && $product->product_type_price == 'N' ? 'checked' : ($product == "" ? 'checked' : '')}}>
                                            <label class="btn btn-outline-primary shadow-none" for="price_type">ราคาเท่ากันทุกแบบ {{ $product->product_type_price }}</label>
                                            
                                            <input type="radio" class="btn-check" name="price_type" id="price_types" value="Y" {{$product != "" && $product->product_type_price == 'Y' ? 'checked' : ''}}>
                                            <label class="btn btn-outline-primary shadow-none" for="price_types">ราคาแตกต่างกัน</label>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- end card -->

                        <div class="card {{$product != "" && $product->product_type_option == 'N' ? '' : ($product == "" ? 'dis-none' : '')}}" id="box-option">
                            <div class="card-body">
                                <h2>ตัวเลือกสินค้า</h2>
                                <div class="row">
                                    @foreach ($masterOption as $key => $item)
                                        <div class="col-3">
                                            <div class="form-check mb-2">
                                                @php
                                                    $search = false;
                                                    if($product != ''){
                                                        $search = array_search($item->m_option_id, array_column($product->option, 'm_option_id'));
                                                    }
                                                @endphp
                                                <input class="form-check-input sel_option" name="sel_option[]" type="checkbox" {{ $search !== false ? 'checked' : ''}} id="formCheck{{$key}}" value="{{$item->m_option_id}}" data-name="{{$item->option_name}}">
                                                <label class="form-check-label" for="formCheck{{$key}}">{{$item->option_name}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="box-option-choose {{$product != "" && $product->product_type_option == 'N' ? '' : ($product == "" ? 'dis-none' : '')}}">
                                    <hr>
                                    @if ($product != '')
                                        @foreach ($product->option as $key => $item)
                                        <div class="row" id="choose{{ $item->m_option_id }}" data-id="{{ $item->m_option_id }}">
                                            <div class="col-12"><h5>{{ $item->m_option_name_th }}</h5></div>
                                            <div class="col-10">
                                                <input type="text" class="form-control inp-choose" id="inp-choose-{{ $item->m_option_id }}" placeholder="กรอกคำที่ต้องการแล้ว Enter" data-id="{{ $item->m_option_id }}">
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-soft-secondary btn-option-choose form-control shadow-none btn-add-choose" type="button" data-id="{{ $item->m_option_id }}">เพิ่ม</button>
                                            </div>
                                            <div class="col-12 mt-3" id="choose-box-{{ $item->m_option_id }}">
                                            @foreach ($item->subs as $ks => $sub)
                                                <div class="option-tags option-{{ $item->m_option_id }} me-2 mb-2" id="option-{{ $item->m_option_id }}-{{$ks}}" data-no="{{$ks}}"  data-id="{{ $item->m_option_id }}">
                                                    <img class="img-preview-option" src="{{ asset('/data/img/product_option/images/'.$sub->sub_path_img) }}" alt="" 
                                                    id="preview-option-{{ $item->m_option_id }}-{{$ks}}" onerror="this.onerror=null;this.src='{{ asset('/assets/images/notImageAva2.jpg') }}'" data-id="{{ $item->m_option_id }}" data-no="{{$ks}}">
                                                    <input type="file" name="img-option-{{ $item->m_option_id }}[]" id="img-option-{{ $item->m_option_id }}-{{$ks}}" class="d-none img-inp" data-id="{{ $item->m_option_id }}" data-no="{{$ks}}">
                                                    <input type="hidden" name="option_text{{ $item->m_option_id }}[]" value="{{ $sub->sub_text }}" id="option-text-{{ $item->m_option_id }}-{{$ks}}">
                                                    <input type="hidden" name="sub_id{{ $item->m_option_id }}[]" value="{{ $sub->sub_id }}">
                                                    <span id="text-option-{{ $item->m_option_id }}-{{$ks}}">{{ $sub->sub_text }}</span>
                                                    <i class="mdi mdi-pencil btn-edit-option" data-id="{{ $item->m_option_id }}" data-no="{{$ks}}" data-text="{{ $sub->sub_text }}"></i>
                                                </div>
                                            @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="filter-option-box {{$product != "" && $product->product_type_option == 'N' ? '' : ($product == "" ? 'dis-none' : '')}}">
                                    <hr>
                                    @if ($product != '')
                                        แบบสินค้า : <span class="filter-option active">ทั้งหมด</span>
                                        @foreach ($product->option as $key => $item)
                                            @foreach ($item->subs as $sub)
                                                <span class="filter-option">{{ $sub->sub_text }}</span>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                                <div class="scrollme {{$product != "" && $product->product_type_option == 'N' ? '' : ($product == "" ? 'dis-none' : '')}}">
                                    @if ($product != "")
                                        <table class="table table-option table-responsive">
                                            <thead>
                                                <tr>
                                                    @foreach ($product->option as $key => $item)
                                                        <th width="120" class="text-title-option">{{ $item->m_option_name_th }}</th>
                                                    @endforeach
                                                    <th width="120">บาร์โค้ด</th>
                                                    <th width="100">รุ่น</th>
                                                    <th width="100">ราคาทุน</th>
                                                    <th width="100">ราคาขาย</th>
                                                    <th width="100">จำนวน</th>
                                                    <th width="100" class="text-center">แสดง/ซ่อน</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $chk_row = [];
                                                @endphp
                                                @foreach ($option as $item)
                                                    <tr>
                                                        @foreach ($item->choose as $c => $choose)
                                                            @php
                                                                $row = 0;
                                                                $choose->sub_text = trim($choose->sub_text);
                                                                $rows = array_search($choose->sub_m_option_id, array_column($chk_row, 'key'));

                                                                if($rows === false){
                                                                    $chk_row[] = [
                                                                        "key" => $choose->sub_m_option_id,
                                                                        "data" => [$choose->sub_text]
                                                                    ];
                                                                }else{
                                                                    $rowc = array_search($choose->sub_text, $chk_row[$rows]["data"]);
                                                                    if($rowc === false){
                                                                        $row = count($chk_row[$rows]["data"]);
                                                                        $chk_row[$rows]["data"][] = $choose->sub_text;
                                                                    }else{
                                                                        $row = $rowc;
                                                                    }
                                                                }
                                                            @endphp
                                                            <td>
                                                                <span class="option-table-{{$choose->sub_m_option_id}}-{{$row}}">{{ $choose->sub_text }}</span>
                                                                <input type="hidden" value="{{ $choose->sub_text }}" class="option-input-{{$choose->sub_m_option_id}}-{{$row}}" name="option_table{{$choose->sub_m_option_id}}[]">
                                                                <input type="hidden" value="{{ $choose->ch_id }}" name="ch_id{{$choose->sub_m_option_id}}[]">
                                                            </td>
                                                        @endforeach
                                                        
                                                        <td>
                                                            <input type="hidden" class="form-control inp-data" name="option_id[]" value="{{ $item->option_id }}">
                                                            <input type="text" class="form-control inp-data" name="barcode_option[]" placeholder="บาร์โค้ด" value="{{ $item->barcode }}">
                                                        </td>
                                                        <td><input type="text" class="form-control inp-data" name="sku_option[]" placeholder="รุ่น" value="{{ $item->sku }}"></td>
                                                        <td><input type="text" class="form-control inp-data" name="cost_option[]" placeholder="ราคาทุน" value="{{ $item->cost_price }}"></td>
                                                        <td><input type="text" class="form-control inp-data" name="sell_option[]" placeholder="ราคาขาย" value="{{ $item->sell_price }}"></td>
                                                        <td><input type="text" class="form-control inp-data" name="quatity_option[]" placeholder="จำนวน" value="{{ $item->stock }}"></td>
                                                        <td>
                                                            <div class="form-check form-switch form-switch text-center" dir="ltr">
                                                                <input type="checkbox" class="form-check-input" id="customSwitchsizelg" name="webshow[]" value="1" {{ $item->option_view == '1' ? 'checked' : ''}}>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>

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
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" data-bs-toggle="tab" href="#addproduct-property" role="tab" aria-selected="false" tabindex="-1">
                                            คุณสมบัติสินค้า
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

                                    <div class="tab-pane" id="addproduct-metadata" role="tabpanel">
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
                                    <div class="tab-pane active show" id="addproduct-property" role="tabpanel">
                                        <b>จับคู่ตัวกรองสินค้า</b>
                                        <div class="col-6">
                                            <div class="input-group mt-3">
                                                <select class="selectpicker w-auto" id="filter_id" name="filter_id" data-live-search="true" data-size="5">
                                                    <option value="">จับคู่ตัวกรองสินค้า</option>
                                                    @foreach ($filter as $item)
                                                        <option value="{{ $item->filter_id }}">{{ $item->name_th }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-secondary" id="btn-add-filter"><i class="ri-add-circle-line align-bottom"></i> เพิ่ม</button>
                                            </div>
                                            <div class="mt-2" id="box-filter">
                                                @if (isset($flist))
                                                    @foreach ($flist as $key  => $item)
                                                    @php
                                                        $key++;
                                                        $subx = explode(',',$item->filter_sub_id);
                                                    @endphp
                                                    <div id="list-of-filter-{{$key}}">
                                                        <div class="d-flex align-self-center">
                                                            <label class="mt-3">{{$item->name_th}}</label>
                                                            <i class="ri-delete-bin-fill m-auto mt-3 me-0 btn-remove-filter" data-id="{{$key}}"></i>
                                                        </div>
                                                        <input name="product_filter[]" value="{{$item->id}}" type="hidden" >
                                                        <input name="filter[]" value="{{$item->filter_id}}" type="hidden" >
                                                        <select class="form-control select-filter-option select-filter-{{$item->filter_id}}" multiple="multiple" name="select-filter-{{$item->filter_id}}[]">
                                                            @foreach ($item->subs as $i)
                                                                @php
                                                                    $filter_sel = '';
                                                                    $filter_sel = array_search($i->filter_id, $subx);
                                                                @endphp
                                                                <option value="{{ $i->filter_id }}" {{ $filter_sel !== false ? "selected" : "" }} >{{$i->name_th }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
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
                            <button type="submit" class="btn btn-success w-sm" id="uploadfiles" value="submit">Submit</button>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <h5>สถานะของสินค้า</h5>
                                <div class="mt-2">
                                    <label class="form-label">แสดงสินค้า</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="active_status" id="active_status" value="Y" {{ $product != "" && $product->active_status == 'Y'? 'checked' : '' }}>
                                        <label class="form-check-label" for="active_status">ไม่ใช้งาน/ใช้งาน</label>
                                    </div>
                                </div>
                                <hr>
                                <div class="mt-2">
                                    <label class="form-label">แสดงหน้าเว็บ</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="visibility_online_store" value="Y" name="visibility_online_store" {{ $product != "" && $product->visibility_online_store == 'Y'? 'checked' : '' }}>
                                        <label class="form-check-label" for="visibility_online_store">ไม่ใช้งาน/ใช้งาน</label>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                        <div class="card">
                            <div class="card-body">
                                <h5>Tag สินค้า</h5>
                                <div class="mt-2">
                                    <input type="text" class="form-control p-4" data-role="tagsinput" name="tags" value="{{$product != "" ? $product->tags : ''}}" />                              
                                </div>
                                <h5 class="mt-3">ไอคอนโปรโมชั่น (สูงสุด 5 รูป)</h5>
                                <div class="mt-2">
                                    <select name="icon_promotion[]" class="select-icon-promation"  multiple="multiple" id="promotion-select2">
                                        @foreach ($icon as $item)
                                            @php
                                                $promo = false;
                                                if($product != ''){
                                                    $promo = array_search($item->id, explode(',', $product->icon_promotion));
                                                }
                                            @endphp
                                            <option value="{{ $item->id }}" {{$promo !== false? 'selected' : ''}}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>                             
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                        <div class="card">
                            <div class="card-body">
                                <h5>สินค้าแนะนำ</h5>
                                <div class="mt-2">
                                    <label class="form-label">สถานะการแสดงสินค้า</label>
                                    <div class="form-check form-radio-outline form-radio-secondary mb-3 radio-recoment">
                                        <input class="form-check-input" type="radio" name="recomment" id="recomment1" value="0">
                                        <label class="form-check-label" for="recomment1">
                                            ไม่แสดงสินค้าแนะนำ
                                        </label>
                                    </div>
                                    <div class="form-check form-radio-outline form-radio-secondary mb-3 radio-recoment">
                                        <input class="form-check-input" type="radio" name="recomment" id="recomment2" value="1" {{ $product && $product->product_related_type == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recomment2">
                                            สุ่มจากสินค้าทั้งหมด
                                        </label>
                                    </div>
                                    <div class="form-check form-radio-outline form-radio-secondary mb-3 radio-recoment">
                                        <input class="form-check-input" type="radio" name="recomment" id="recomment3" value="2" {{ $product && $product->product_related_type == '2' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recomment3">
                                            สุ่มจากหมวดหมู่เดียวกัน
                                        </label>
                                    </div>
                                    <div class="form-check form-radio-outline form-radio-secondary mb-3 radio-recoment">
                                        <input class="form-check-input" type="radio" name="recomment" id="recomment4" value="3" {{ $product && $product->product_related_type == '3' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recomment4">
                                            สุ่มจากหมวดหมู่ที่เลือก
                                        </label>
                                    </div>
                                    <div class="box-select {{ $product && $product->product_related_type == '3' ? '' : 'dis-none' }}" >
                                        <select class="selectpicker" id="recoment_cat" name="recoment_cat[]" data-live-search="true" data-size="5"  multiple data-selected-text-format="count > 4">
                                            <option value="">หมวดหมู่</option>
                                            @foreach ($cat as $item)
                                                @php
                                                    $cata = false;
                                                    if($product != ''){
                                                        $cata = array_search($item['cat_id'], $product->other_cata);
                                                    }
                                                @endphp
                                                <option value="{{$item['cat_id']}}" {{ $cata !== false ? 'selected' : ''}}>{{$item['cat_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="form-check form-radio-outline form-radio-secondary mb-3">
                                        <input class="form-check-input" type="radio" name="recomment" id="recomment5" value="4">
                                        <label class="form-check-label" for="recomment5">
                                            เลือกสินค้าแนะนำ
                                        </label>
                                    </div> --}}
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


<!-- Grids in modals -->
<div class="modal fade modal_option" id="modelOption" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog" style="width: 300px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">แก้ไขตัวเลือก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <span>ภาพของตัวเลือก</span>
                        <img src="" id="img-option-modal" alt="" onerror="this.onerror=null;this.src='{{asset('/assets/images/notImageAva2.jpg')}}'">
                        {{-- <div class="mt-2">
                            <span class="change" id="change-img-modal">เปลี่ยน</span> | <span class="remove" id="remove-img-modal">นำออก</span>
                            <input type="file" name="" id="image-modal" class="dis-none">
                        </div> --}}
                    </div>
                    <div class="col-12">
                        <span>ข้อความตัวเลือก</span>
                        <input type="text" class="form-control" id="text-option-modal">
                    </div>
                    <div class="col-6">
                        <button class="btn btn-danger" type="button">ลบตัวเลือก</button>
                    </div>
                    <div class="col-6">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btn-save-modal">ตกลง</button>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')    
    <script src="https://cdn.tiny.cloud/1/3of8d3z2fu4pp5rkbjgt8r62bfhpoi3ql8jp3p8tvncs2dlp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <!-- dropzone min -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/libs/bootstrap-tagsinput/src/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('assets/js/views/Product/form.js?v='.time()) }}"></script>
@endpush