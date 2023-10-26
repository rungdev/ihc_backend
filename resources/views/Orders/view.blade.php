@extends('Layouts.main_layout')

@push('css')

<link rel="stylesheet" href="{{asset('/assets/css/views/Orders/view.css?v='.time())}}">

@endpush

@php
    $total_bill = 0;
@endphp

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Order Details</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                                <li class="breadcrumb-item active">Order Details</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title flex-grow-1 mb-0 d-flex justify-content-start">
                                    <div>
                                        เลขที่คำสั่งซื้อ {{$order_data->order_number}}<br>
                                        <span class="dateCreate">สร้าง : {{$order_data->create_dtm}}</span>
                                    </div>
                                    <img alt="TESTING" src="https://shop.posvision.co/paeshop/utility/barcode/?codetype=Code39&size=25&text={{$order_data->order_number}}&print=true" />
                                </h5>
                                <div class="flex-shrink-0 d-flex justify-content-start">
                                    <a href="{{asset('/printOrder/'.$order_data->order_id)}}" target="_blank" class="btn btn-warning btn-print-order"><i class="mdi mdi-file-powerpoint align-middle me-1"></i> พิมพ์ใบสั่งซื้อ</a>
                                    <div class="input-group">
                                        <select class="form-select {{!empty($update) ? '' : 'disable_by_role'}}" id="status_select">
                                            <option value="">Choose...</option>
                                            <option value="Pending" {{$order_data->order_status == 'Pending' ? 'selected' : ''}}>ทำรายการไม่สำเร็จ</option>
                                            <option value="Waiting_D" {{$order_data->order_status == 'Waiting_D' ? 'selected' : ''}}>รอยืนยันการสั่งซื้อ</option>
                                            <option value="Paid" {{$order_data->order_status == 'Paid' ? 'selected' : ''}}>รอดำเนินการ</option>
                                            <option value="Paid_D" {{$order_data->order_status == 'Paid_D' ? 'selected' : ''}}>เตรียมการจัดส่ง</option>
                                            <option value="Shipped" {{$order_data->order_status == 'Shipped' ? 'selected' : ''}}>จัดส่งแล้ว</option>
                                            <option value="Completed" {{$order_data->order_status == 'Completed' ? 'selected' : ''}}>เสร็จสิ้น</option>
                                            <option value="Cancel" {{$order_data->order_status == 'Cancel' ? 'selected' : ''}}>ยกเลิก</option>
                                        </select>
                                        @if (!empty($update))
                                            <button class="btn btn-success shadow-none btn-update-status" type="button"  
                                                    data-burn-point="{{$order_data->royalty_burn_point}}"
                                                    data-point="{{$order_data->royalty_earn_point}}"
                                                    data-customer="{{$order_data->customer_id}}"
                                                    data-status-earn="{{$order_data->status_earn}}">
                                                <i class="ri-checkbox-circle-line align-middle me-1"></i> บันทึก
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-borderless mb-0">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th style="width: 60%">รายละเอียด</th>
                                            <th>ราคาต่อหน่วย</th>
                                            <th>จำนวน</th>
                                            <th>Rating</th>
                                            <th class="text-end">ราคารวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemlist as $item)
                                        @php
                                            $total_line = number_format($item->item_price * $item->item_qty, 2);
                                            $total_bill += $item->item_price * $item->item_qty;
                                            $total_discount = 0;
                                            if($order_data->coupon_price > 0){
                                                $total_discount += $order_data->coupon_price;
                                            }
                                            if($order_data->discount_price > 0){
                                                $total_discount += $order_data->discount_price;
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
                                                        
                                                        <img src="{{ asset($item->picture) }}" alt="" class="img-fluid d-block">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="fs-15"><a href="apps-ecommerce-product-details.html" class="link-primary">{{$item->name_th}}</a></h5>
                                                        {{-- <p class="text-muted mb-0">Color: <span class="fw-medium">Pink</span></p>
                                                        <p class="text-muted mb-0">Size: <span class="fw-medium">M</span></p> --}}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{$item->item_price}}</td>
                                            <td>{{$item->item_qty}}</td>
                                            <td>
                                                <div class="text-warning fs-15">
                                                    <i class="ri-star-fill"></i>
                                                    <i class="ri-star-fill"></i>
                                                    <i class="ri-star-fill"></i>
                                                    <i class="ri-star-fill"></i>
                                                    <i class="ri-star-half-fill"></i>
                                                </div>
                                            </td>
                                            <td class="fw-medium text-end">
                                                {{number_format($total_line, 2)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        
                                        <tr class="border-top border-top-dashed">
                                            <td colspan="3"></td>
                                            <td colspan="2" class="fw-medium p-0">
                                                <table class="table table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td>ยอดรวม :</td>
                                                            <td class="text-end">{{number_format($total_line, 2)}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>ส่วนลด <span class="text-muted">(VELZON15)</span> :</td>
                                                            @if ($total_discount > 0)
                                                                <td class="text-end text-success">{{'-'.number_format($total_discount, 2)}}</td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <td>ค่าจัดส่ง :</td>
                                                            <td class="text-end">{{$order_data->order_shipping_rate}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>ภาษี 7% :</td>
                                                            <td class="text-end">{{$order_data->tax_price}}</td>
                                                        </tr>
                                                        @if ($order_data->order_payment_id == "8")
                                                        <tr>
                                                            <td>ดอกเบี้ยผ่อนชำระ :</td>
                                                            <td class="text-end">$44.99</td>
                                                        </tr>
                                                        @endif
                                                        <tr class="border-top border-top-dashed text-danger">
                                                            <th scope="row">ยอดรวมสุทธิ :</th>
                                                            <th class="text-end order_price">{{number_format($order_data->order_price, 2)}}</th>
                                                        </tr>
                                                        <tr class="border-top border-top-dashed">
                                                            <td scope="row">คะแนนที่ได้รับ :</td>
                                                            <td class="text-end">{{number_format($order_data->royalty_earn_point, 2)}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr class="border-top border-top-dashed">
                                            <td colspan="3">
                                                
                                                <div class="input-group w-50">
                                                    <label class="input-group-text" for="inputGroupSelect01">ยอดเก็บเงินมัดจำล่วงหน้า</label>
                                                    <input type="number" class="form-control {{!empty($update) ? '' : 'disable_by_role'}}" id="deposit_advance" value="{{$order_data->receive_advance_deposit}}" >
                                                    @if (!empty($update))
                                                        <button class="btn btn-outline-warning" id="btn_deposit_advance" type="button"><i class="las la-save"></i> บันทึก</button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td colspan="2" class="fw-medium p-0">
                                                <table class="table table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td scope="row">ยอดเก็บเงินปลายทางคงเหลือ</td>
                                                            <td class="text-end">
                                                                <span class="advance_deposit">{{number_format($order_data->order_price - $order_data->receive_advance_deposit, 2)}}</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="w-100 mb-5 mt-3">
                                    <tr>
                                        <td class="align-top">
                                            <label for="">หมายเหตุชำระเงิน</label>
                                            <p for="">ลูกค้า อาจจะต้องชำระค่าเงินมัดจำบางส่วน ขึ้นอยู่กับมูลค่าสินค้า</p>
                                        </td>
                                        <td>
                                            <label for="">หมายเหตุภายใน</label>
                                            <textarea class="{{!empty($update) ? '' : 'disable_by_role'}}" id="note_internal">{{$order_data->order_note_internal}}</textarea>
                                            @if (!empty($update))
                                                <button class="btn btn-warning btn_save_note mt-3"><i class="las la-save"></i> บันทึก</button>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end card-->
                    {{-- <div class="card">
                        <div class="card-header">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="card-title flex-grow-1 mb-0">สถานะคำสั่งซื้อ</h5>
                                <div class="flex-shrink-0 mt-2 mt-sm-0">
                                    <a href="javasccript:void(0;)" class="btn btn-soft-info btn-sm mt-2 mt-sm-0 shadow-none"><i class="ri-map-pin-line align-middle me-1"></i> เปลี่ยนที่อยู่</a>
                                    <a href="javasccript:void(0;)" class="btn btn-soft-danger btn-sm mt-2 mt-sm-0 shadow-none"><i class="mdi mdi-archive-remove-outline align-middle me-1"></i> ยกเลิกคำสั่งซื้อ</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="profile-timeline">
                                <div class="accordion accordion-flush" id="accordionFlushExample">
                                    <div class="accordion-item border-0">
                                        <div class="accordion-header" id="headingOne">
                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 avatar-xs">
                                                        <div class="avatar-title bg-success rounded-circle shadow">
                                                            <i class="ri-shopping-bag-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="fs-15 mb-0 fw-semibold">มีการสั่งซื้อ - <span class="fw-normal">Wed, 15 Dec 2021</span></h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body ms-2 ps-5 pt-0">
                                                <h6 class="mb-1">ชำระเงินแล้ว</h6>
                                                <p class="text-muted">Wed, 15 Dec 2021 - 05:34PM</p>

                                                <h6 class="mb-1">ยืนยันคำสั่งซื้อแล้ว</h6>
                                                <p class="text-muted mb-0">Thu, 16 Dec 2021 - 5:48AM</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <div class="accordion-header" id="headingTwo">
                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 avatar-xs">
                                                        <div class="avatar-title bg-success rounded-circle shadow">
                                                            <i class="mdi mdi-gift-outline"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="fs-15 mb-1 fw-semibold">กำลังดำเนินการ - <span class="fw-normal">Thu, 16 Dec 2021</span></h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                            <div class="accordion-body ms-2 ps-5 pt-0">
                                                <h6 class="mb-1">กำลังทำการแพ็คสินค้า</h6>
                                                <p class="text-muted mb-0">Fri, 17 Dec 2021 - 9:45AM</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <div class="accordion-header" id="headingThree">
                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 avatar-xs">
                                                        <div class="avatar-title bg-success rounded-circle shadow">
                                                            <i class="ri-truck-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="fs-15 mb-1 fw-semibold">เตรียมการจัดส่ง - <span class="fw-normal">Thu, 16 Dec 2021</span></h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                            <div class="accordion-body ms-2 ps-5 pt-0">
                                                <h6 class="fs-14">หมายเลขจัดส่ง - MFDS1400457854</h6>
                                                <h6 class="mb-1">รายการของคุณได้ถูกจัดส่งแล้ว</h6>
                                                <p class="text-muted mb-0">Sat, 18 Dec 2021 - 4.54PM</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <div class="accordion-header" id="headingFour">
                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFour" aria-expanded="false">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 avatar-xs">
                                                        <div class="avatar-title bg-light text-success rounded-circle shadow">
                                                            <i class="ri-takeaway-fill"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="fs-14 mb-0 fw-semibold">อยู่ระหว่างจัดส่ง</h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <div class="accordion-header" id="headingFive">
                                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFile" aria-expanded="false">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 avatar-xs">
                                                        <div class="avatar-title bg-light text-success rounded-circle shadow">
                                                            <i class="mdi mdi-package-variant"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="fs-14 mb-0 fw-semibold">จัดส่งสำเร็จ</h6>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!--end accordion-->
                            </div>
                        </div>
                    </div> --}}
                    <!--end card-->
                </div>
                <!--end col-->
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0"><i class="mdi mdi-truck-fast-outline align-middle me-1 text-muted"></i> รายละเอียดการจัดส่ง</h5>
                                <div class="flex-shrink-0">
                                    <a href="javascript:void(0);" class="badge bg-primary-subtle text-primary fs-11">ติดตามการสั่งซื้อ</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/uetqnvvg.json" trigger="loop" colors="primary:#4b38b3,secondary:#0ab39c" style="width:80px;height:80px"></lord-icon>
                                <h5 class="fs-16 mt-2">{{$order_data->order_shipping_name}}</h5>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Tracking</span>
                                    <input type="text" id="trackingnumber" class="form-control {{!empty($update) ? '' : 'disable_by_role'}}" placeholder="Tracking" value="{{$order_data->tracking_no}}">
                                    <input type="hidden" id="order_id" class="form-control" value="{{$order_data->order_id}}">
                                    @if (!empty($update))
                                        <button class="btn btn-success save-tracking" type="button">Save</button>
                                    @endif
                                </div>
                                <p class="text-muted mb-0">Payment Mode : {{$order_data->order_payment_name}}</p>
                                
                            </div> 
                        </div>
                    </div>
                    <!--end card-->

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0">รายละเอียดลูกค้า</h5>
                                <div class="flex-shrink-0">
                                    @if ($customer->customer_id != '')
                                        <a href="customerview/{{$customer->customer_id}}" class="link-secondary">View Profile</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 vstack gap-3">
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img src="{{asset ('/uploads/img/avatar-bg.png')}}" alt="" class="avatar-sm rounded shadow">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">{{$customer->firstname . " " . $customer->lastname}}</h6>
                                            <p class="text-muted mb-0">Customer</p>
                                        </div>
                                    </div>
                                </li>
                                <li><i class="ri-mail-line me-2 align-middle text-muted fs-16"></i>{{$customer->email}}</li>
                                <li><i class="ri-phone-line me-2 align-middle text-muted fs-16"></i>{{$customer->phone}}</li>
                            </ul>
                        </div>
                    </div>
                    <!--end card-->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> ที่อยู่ออกใบกำกับภาษี</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                                <li class="fw-medium fs-14">{{$taxaddr->firstname . " " . $taxaddr->lastname}}</li>
                                <li>{{$taxaddr->mobile}}</li>
                                <li>{{$taxaddr->address1}}</li>
                                <li>{{$taxaddr->subdistrict . " " . $taxaddr->city}}</li>
                                <li>{{$taxaddr->state . " " . $taxaddr->postcode}}</li>
                            </ul>
                        </div>
                    </div>
                    <!--end card-->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> ที่อยู่ในการจัดส่ง</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                                <li class="fw-medium fs-14">{{$address->firstname . " " . $address->lastname}}</li>
                                <li>{{$address->mobile}}</li>
                                <li>{{$address->address1}}</li>
                                <li>{{$address->subdistrict . " " . $address->city}}</li>
                                <li>{{$address->state . " " . $address->postcode}}</li>
                            </ul>
                        </div>
                    </div>
                    <!--end card-->

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i> รายละเอียดการชำระเงิน</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <p class="text-muted mb-0">Transactions:</p>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0">#VLZ124561278124</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <p class="text-muted mb-0">Payment Method:</p>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0">Debit Card</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <p class="text-muted mb-0">Card Holder Name:</p>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0">Joseph Parker</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <p class="text-muted mb-0">Card Number:</p>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0">xxxx xxxx xxxx 2456</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <p class="text-muted mb-0">Total Amount:</p>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0">$415.96</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
    </div>
</div>
@endsection

@push('script')
    {{-- <script src="{{asset('/assets/libs/tinymce/tinymce.min.js')}}"></script> --}}
    <script src="https://cdn.tiny.cloud/1/3of8d3z2fu4pp5rkbjgt8r62bfhpoi3ql8jp3p8tvncs2dlp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    {{-- <script type="text/javascript" src='https://cdn.tiny.cloud/1/no-api-key/tinymce/4/tinymce.min.js'></script> --}}
    <script src="{{asset('/assets/js/views/Orders/view.js?v='.time())}}"></script>
@endpush