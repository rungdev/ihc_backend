@extends('Layouts.main_layout')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/views/Product/list.css')}}">
@endpush

@section('content')

<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Products</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                                <li class="breadcrumb-item active">Products</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="fs-16">Filters</h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="#" class="text-decoration-underline" id="clearall">Clear All</a>
                                </div>
                            </div>

                            <div class="filter-choices-input">
                                <input class="form-control" type="text" id="inpSearch" value="" placeholder="บาร์โค้ด / ชื่อ / รุ่น / รหัสสินค้า" />
                            </div>
                        </div>

                        <div class="accordion accordion-flush filter-accordion">    
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingBrands2">
                                    <button class="accordion-button bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBrands2" aria-expanded="true" aria-controls="flush-collapseBrands2">
                                        <span class="text-muted text-uppercase fs-12 fw-medium">หมวดหมู่สินค้า</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                    </button>
                                </h2>

                                <div id="flush-collapseBrands2" class="accordion-collapse collapse" aria-labelledby="flush-headingBrands2">
                                    <div class="accordion-body text-body pt-0">
                                        <div class="box-catagory">
                                            {!! $cathtml !!}
                                        </div>
                                    </div>
                                </div>
                            </div>   
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingBrands3">
                                    <button class="accordion-button bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBrands3" aria-expanded="true" aria-controls="flush-collapseBrands3">
                                        <span class="text-muted text-uppercase fs-12 fw-medium">แบรนด์</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                    </button>
                                </h2>

                                <div id="flush-collapseBrands3" class="accordion-collapse collapse" aria-labelledby="flush-headingBrands3">
                                    <div class="accordion-body text-body pt-0">
                                        <div class="box-catagory">
                                            <ul style="padding: 0px;">
                                                <li>
                                                    <input class="form-check-input checkbrand" type="checkbox" name="formCheck-1" id="formCheck-1" value="">
                                                    <label class="form-check-label" for="formCheck-1">ทั้งหมด</label>
                                                    <ul>
                                                        @foreach ($brand as $item)
                                                            
                                                                <li>
                                                                    <input class="form-check-input checkbrand" type="checkbox" name="formCheck-1-{{$item->brand_id}}" id="formCheck-1-{{$item->brand_id}}" value="{{$item->brand_id}}">
                                                                    <label class="form-check-label" for="formCheck-1-{{$item->brand_id}}">
                                                                        {{$item->brand_name_th}}
                                                                    </label>
                                                                </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingBrands">
                                    <button class="accordion-button bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBrands" aria-expanded="true" aria-controls="flush-collapseBrands">
                                        <span class="text-muted text-uppercase fs-12 fw-medium">สถานะ</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                    </button>
                                </h2>

                                <div id="flush-collapseBrands" class="accordion-collapse collapse" aria-labelledby="flush-headingBrands">
                                    <div class="accordion-body text-body pt-0">
                                        <ul style="padding: 0px;">
                                            <li>
                                                <input class="form-check-input checkstatus" name="checkstatus" id="checkstatus" type="checkbox" value="" checked>
                                                <label class="form-check-label" for="statusAll">ทั้งหมด</label>
                                                <ul>
                                                    <li>
                                                        <input class="form-check-input checkstatus" name="checkstatus-1" type="checkbox" value="Y">
                                                        <label class="form-check-label" for="statusY">ใช้งาน</label>
                                                    </li>
                                                    <li>
                                                        <input class="form-check-input checkstatus" name="checkstatus-1" type="checkbox" value="N">
                                                        <label class="form-check-label" for="statusY">ไม่ใช้งาน</label>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body border-bottom">
                                <p class="text-muted text-uppercase fs-12 fw-medium mb-4">ราคา</p>
                                <div id="product-price-range" data-slider-color="primary"></div>
                                <div class="formCost d-flex gap-2 align-items-center mt-3">
                                    <input class="form-control form-control-sm" type="text" id="minCost" value="0.00" /> 
                                    <span class="fw-semibold text-muted">to</span> 
                                    <input class="form-control form-control-sm" type="text" id="maxCost" value="100000.00" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div>
                        <div class="card">
                            <div class="card-header border-0">
                                <a href="/product/create" class="btn btn-success" id="addproduct-btn"><i class="ri-add-line align-bottom me-1"></i> Add Product</a>
                                <button class="btn btn-info" id="btn-sync-all"> <i class="ri-download-cloud-fill align-middle"></i> <span>Sync Data</span></button>
                            </div>
                            <div class="card-body">
                                <table 
                                    class="table align-middle table_product" 
                                    id="productTable"
                                    data-toggle="table"
                                    data-ajax="ajaxRequest"
                                    data-search="false"
                                    data-side-pagination="server"
                                    data-pagination="true"
                                    data-sortable="true"
                                    data-page-size="20">
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <th class="text-center" data-width="30" data-field="checkbox">#</th>
                                            <th class="align-top" data-width="600" data-field="name_product" data-sortable="true">สินค้า</th>
                                            <th class="align-top" data-width="100" data-field="product_code" data-sortable="true">รหัสสินค้า</th>
                                            <th class="align-top" data-width="150" data-field="cat_id">หมวดหมู่</th>
                                            <th class="align-top" data-width="150" data-field="brand_name_th" >แบรนด์</th>
                                            <th class="align-top" data-width="150" data-field="sell_price" data-sortable="true">ราคา</th>
                                            <th class="align-top" data-width="50" data-field="stocktt" data-sortable="true">จำนวน</th>
                                            <th class="text-center align-top" data-width="100" data-field="status">สถานะ</th>
                                            <th class="text-center align-top" data-width="100" data-field="manager">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <!-- nouisliderribute js -->
    <script src="{{ asset('assets/libs/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/libs/wnumb/wNumb.min.js') }}"></script>

    <!-- gridjs js -->
    <script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>
    
    <script src="{{ asset('assets/js/views/Product/list.js?v='.time()) }}"></script>
@endpush