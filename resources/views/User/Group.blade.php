@extends('Layouts.main_layout')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">สิทธิ์กลุ่มผู้ใช้งาน</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/orderlist">สิทธิ์กลุ่มผู้ใช้งาน</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="orderList">
                        <div class="card-header border-0">
                            <div class="row align-items-center gy-3">
                                <div class="col-sm">
                                    <h5 class="card-title mb-0">สิทธิ์กลุ่มผู้ใช้งาน</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="table-card mb-1">
                                <table 
                                    class="table align-middle" 
                                    id="groupTable"
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
                                            <th class="text-center" data-width="20" data-field="no">#</th>
                                            <th class="text-center" data-width="600" data-field="name" data-sortable="true">ชื่อกลุ่ม</th>
                                            <th class="text-center" data-width="50" data-field="status">สถานะ</th>
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
            
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="assets/js/views/User/User.js?v={{time()}}"></script>
@endpush