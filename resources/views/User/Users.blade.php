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
                        <h4 class="mb-sm-0">ผู้ใช้งาน</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/users">ผู้ใช้งาน</a></li>
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
                                    <h5 class="card-title mb-0">ผู้ใช้งาน</h5>
                                </div>
                            </div>
                            <div class="row border border-dashed border-end-0 border-start-0 mt-3 pb-3 pt-2">
                                <div class="col-3">
                                    <label for="basic-url" class="form-label">ชื่อผู้ใช้</label>
                                    <input type="text" class="form-control" id="username" aria-describedby="basic-addon3">
                                </div>
                                <div class="col-3">
                                    <label for="basic-url" class="form-label">สิทธิ์ผู้ใช้งาน</label>
                                    <select name="selGroup" id="selGroup" class="selectpicker" multiple data-selected-text-format="count > 3">
                                        @foreach ($group as $item)
                                            <option value="{{$item->auto_id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label for="basic-url" class="form-label">สาขา</label>
                                    <select name="selBranch" id="selBranch" class="selectpicker" multiple data-selected-text-format="count > 3">
                                        @foreach ($branch as $item)
                                            <option value="{{$item->branch_id}}">{{$item->branch_name_th}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label for="basic-url" class="form-label text-white">.</label>
                                    <div>
                                        <button type="button" class="btn btn-primary w-50" id="btn-filter"> <i class="ri-search-line me-1 align-bottom"></i>
                                            Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            
                            <div class="table-card mb-1">
                                <table 
                                    class="table align-middle" 
                                    id="usersTable"
                                    data-toggle="table"
                                    data-ajax="ajaxRequestUsers"
                                    data-search="false"
                                    data-side-pagination="server"
                                    data-pagination="true"
                                    data-sortable="true"
                                    data-page-size="30"
                                    
                                    >
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <th class="text-center" data-width="400" data-field="user_name" data-sortable="true">ชื่อผู้ใช้</th>
                                            <th class="text-center" data-width="200" data-field="gname">สิทธิ์ผู้ใช้งาน</th>
                                            <th class="text-center" data-width="200" data-field="branch">สาขา</th>
                                            <th class="text-center" data-width="100" data-field="log">เข้าสู่ระบบล่าสุด</th>
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
            
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="assets/js/views/User/User.js?v={{time()}}"></script>
@endpush