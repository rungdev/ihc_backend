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
                        <h4 class="mb-sm-0">หมวดหมู่สินค้าสินค้า</h4>
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
                                    <h5 class="card-title mb-0">หมวดหมู่สินค้าสินค้า</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <button type="button" class="btn btn-primary btn-add-child" data-id="0">
                                        <i class="mdi mdi-plus align-middle"></i>
                                        เพิ่มหมวดหมู่สินค้า
                                    </button>
                                </div>
                            </div>
                            <div class="row border border-dashed border-end-0 border-start-0 mt-3 pb-3 pt-2 ">
                                <div class="col"></div>
                                <div class="col-3">
                                    <label for="basic-url" class="form-label">ค้นหา</label>
                                    <input type="text" class="form-control" id="searchInp" aria-describedby="basic-addon3" placeholder="หมวดหมู่สินค้าสินค้า">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <table class="table" id="basic" border="1">
                                <thead>
                                    <tr>
                                        <th style="width: 80%">ชื่อหมวดหมู่</th>
                                        <th style="width: 10%" class="text-center">สถานะ</th>
                                        <th style="width: 10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableData">
                                    {!! $table !!}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModal" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">หมวดหมู่สินค้า</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-xxl-6">
                                <label for="firstName" class="form-label">ชื่อหมวดหมู่ (TH)</label>
                                <input type="text" class="form-control" id="categoryth" placeholder="ชื่อหมวดหมู่สินค้า (th)">
                                <input type="hidden" value="" id="maincatid">
                                <input type="hidden" value="" id="categoryid">
                            </div><!--end col-->
                            <div class="col-xxl-6">
                                <label for="lastName" class="form-label">ชื่อหมวดหมู่ (EN)</label>
                                <input type="text" class="form-control" id="categoryen" placeholder="ชื่อหมวดหมู่สินค้า (en)">
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
@endsection

@push('script')
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="{{ asset('assets/libs/simple-tree-table/dist/jquery-simple-tree-table.js') }}"></script>
    <script src="assets/js/views/Product/category.js?v={{time()}}"></script>
@endpush