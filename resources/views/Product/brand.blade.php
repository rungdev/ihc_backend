@extends('Layouts.main_layout')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/views/Product/list.css')}}">
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">แบรนด์สินค้า</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Grids in modals -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="orderList">
                        <div class="card-header border-0">
                            <div class="row align-items-center gy-3">
                                <div class="col-sm">
                                    <h5 class="card-title mb-0">แบรนด์สินค้า</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <button type="button" class="btn btn-primary btn-add-brand">
                                        <i class="mdi mdi-plus align-middle"></i>
                                        เพิ่มแบรนด์
                                    </button>
                                </div>
                            </div>
                            <div class="row border border-dashed border-end-0 border-start-0 mt-3 pb-3 pt-2 ">
                                <div class="col"></div>
                                <div class="col-3">
                                    <label for="basic-url" class="form-label">ค้นหา</label>
                                    <input type="text" class="form-control" id="searchInp" aria-describedby="basic-addon3" placeholder="แบรนด์สินค้า">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            
                            <div class="table-card mb-1">
                                <table 
                                    class="table align-middle" 
                                    id="table"
                                    data-toggle="table"
                                    data-ajax="ajaxRequest"
                                    data-search="false"
                                    data-side-pagination="server"
                                    data-pagination="true"
                                    data-sortable="true"
                                    data-page-size="30"
                                    >
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <th class="text-center" data-width="50" data-field="brand_id">ID</th>
                                            <th class="" data-width="700" data-field="brand_name_th">ชื่อแบรนด์</th>
                                            <th class="text-center" data-width="100" data-field="active_status">สถานะ</th>
                                            <th class="text-center" data-width="100" data-field="manager">Action</th>
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
            <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModal" aria-modal="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="brandModalLabel">ตัวเลือกสินค้า</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-xxl-6">
                                    <label for="firstName" class="form-label">ตัวเลือก (TH)</label>
                                    <input type="text" class="form-control" id="brandth" placeholder="ตัวเลือกสินค้า (th)">
                                    <input type="hidden" value="" id="brandid">
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <label for="lastName" class="form-label">ตัวเลือก (EN)</label>
                                    <input type="text" class="form-control" id="branden" placeholder="ตัวเลือกสินค้า (en)">
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                                        <label class="form-check-label" for="flexSwitchCheckDefault">ไม่ใช้งาน/ใช้งาน</label>
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary btn-save">Save</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="assets/js/views/Product/brand.js?v={{time()}}"></script>
@endpush