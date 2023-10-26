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
                        <h4 class="mb-sm-0">การสั่งซื้อ</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/orderlist">การสั่งซื้อ</a></li>
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
                                    <h5 class="card-title mb-0">การสั่งซื้อ</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-file-excel-line align-bottom me-1"></i> ส่งออกข้อมูล</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border border-dashed border-end-0 border-start-0">
                            <form>
                                <div class="row g-3">
                                    <div class="col-xxl-5 col-sm-6">
                                        <div>
                                            <input type="text" class="form-control" placeholder="Search for order ID, customer, order status or something..." id="searchInput">
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-sm-6">
                                        <input type="text" name="daterange" id="daterange" class="form-control text-center" value="" autocomplete="off" />
                                    </div>
                                    <div class="col-xxl-2 col-sm-4">
                                        <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="idStatus">
                                            <option value="">สถานะทั้งหมด</option>
                                            <option value="Pending">ทำรายการไม่สำเร็จ</option>
                                            <option value="Waiting_D">รอยืนยันการสั่งซื้อ</option>
                                            <option value="Paid">รอดำเนินการ</option>
                                            <option value="Paid_D">เตรียมการจัดส่ง</option>
                                            <option value="Shipped">จัดส่งแล้ว</option>
                                            <option value="Completed">เสร็จสิ้น</option>
                                            <option value="Cancel">ยกเลิก</option>
                                        </select>
                                    </div>
                                    <div class="col-xxl-2 col-sm-4">
                                        <div>
                                            <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="idPayment">
                                                <option value="all">All</option>
                                                <option value="Mastercard">Mastercard</option>
                                                <option value="Paypal">Paypal</option>
                                                <option value="Visa">Visa</option>
                                                <option value="COD">COD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-1 col-sm-4">
                                        <button type="button" class="btn btn-primary w-100" id="btn-filter"> <i class="ri-search-line me-1 align-bottom"></i>
                                            Filters
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body pt-4">
                            <div class="table-card mb-1">
                                <table 
                                    class="table align-middle" 
                                    id="orderTable"
                                    data-toggle="table"
                                    data-ajax="ajaxRequest"
                                    data-search="false"
                                    data-side-pagination="server"
                                    data-pagination="true"
                                    data-sortable="true"
                                    data-page-size="20">
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <th class="text-center" data-field="order_number" data-sortable="true">เลขคำสั่งซื้อ</th>
                                            <th class="text-center" data-field="fullname" data-sortable="true">ลูกค้า</th>
                                            <th class="text-center" data-field="create_dtm" data-sortable="true">วันที่</th>
                                            <th class="text-center" data-field="order_price" data-sortable="true">ยอดรวม</th>
                                            <th class="text-center" data-field="order_payment_name" data-sortable="true">การชำระเงิน</th>
                                            <th class="text-center" data-field="order_shipping_name" data-sortable="true">การจัดส่ง</th>
                                            <th class="text-center" data-field="order_status" data-sortable="true">สถานะ</th>
                                            <th class="text-center" data-field="manager">Action</th>
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
    <script src="assets/js/views/Orders/index.js?v={{time()}}"></script>
@endpush