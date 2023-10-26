@extends('Layouts.main_layout')

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/views/Product/supplierForm.css')}}">
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ผู้ผลิต/จำหน่าย</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Grids in modals -->
            <form id="formSupplier" method="post" enctype="multipart/form-data" target="calldata" class="need-validation" novalidate>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="orderList">
                        <div class="card-header border-0">
                            <div class="row align-items-center gy-3">
                                <div class="col-sm">
                                    <h5 class="card-title mb-0">ผู้ผลิต/จำหน่าย</h5>
                                </div>
                                <div class="col-sm-auto">
                                    <button type="submit" class="btn btn-primary btn-save-supplier">
                                        <i class="ri-save-3-line align-bottom"></i>
                                        บันทึก
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $id }}">
                                            <label class="form-label">ชื่อผู้ผลิต/จำหน่าย (th)<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="name_th" name="name_th" value="{{ $data != "" ? $data->name_th : '' }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">ชื่อผู้ผลิต/จำหน่าย (en)<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="name_gb" name="name_gb" value="{{ $data != "" ? $data->name_gb : '' }}" required>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="form-label">หมายเลขประจำตัวผู้เสียภาษี</label>
                                            <input type="text" class="form-control" id="taxid" name="taxid" value="{{ $data != "" ? $data->taxid : '' }}">
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
                                                    <textarea class="discript_th" name="description_th" id="description_th">
                                                        {!! $data != "" ? $data->description_th : '' !!}
                                                    </textarea>
                                                    <div class="mt-2">
                                                        <label class="form-label">ที่อยู่ บรรทัดที่ 1</label>
                                                        <input type="text" class="form-control" id="address1_th" name="address1_th" value="{{ $data != "" ? $data->address1_th : '' }}">
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="form-label">ที่อยู่ บรรทัดที่ 2</label>
                                                        <input type="text" class="form-control" id="address2_th" name="address2_th" value="{{ $data != "" ? $data->address2_th : '' }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 mt-2">
                                                            <label class="form-label">เขต/อำเภอ</label>
                                                            <input type="text" class="form-control" id="city_th" name="city_th" value="{{ $data != "" ? $data->city_th : '' }}">
                                                        </div>
                                                        <div class="col-6 mt-2">
                                                            <label class="form-label">จังหวัด</label>
                                                            <input type="text" class="form-control" id="state_th" name="state_th" value="{{ $data != "" ? $data->state_th : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="discriptionEN" role="tabpanel">
                                                    <textarea class="description_en" name="description_gb" id="description_gb">
                                                        {!! $data != "" ? $data->description_gb : '' !!}
                                                    </textarea>
                                                    <div class="mt-2">
                                                        <label class="form-label">ที่อยู่ บรรทัดที่ 1</label>
                                                        <input type="text" class="form-control" id="address1_gb" name="address1_gb" value="{{ $data != "" ? $data->address1_gb : '' }}">
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="form-label">ที่อยู่ บรรทัดที่ 2</label>
                                                        <input type="text" class="form-control" id="address2_gb" name="address2_gb" value="{{ $data != "" ? $data->address2_gb : '' }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 mt-2">
                                                            <label class="form-label">เขต/อำเภอ</label>
                                                            <input type="text" class="form-control" id="city_gb" name="city_gb" value="{{ $data != "" ? $data->city_gb : '' }}">
                                                        </div>
                                                        <div class="col-6 mt-2">
                                                            <label class="form-label">จังหวัด</label>
                                                            <input type="text" class="form-control" id="state_gb" name="state_gb" value="{{ $data != "" ? $data->state_gb : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6 mt-2">
                                            <label class="form-label">ประเทศ</label>
                                            <select class="selectpicker" id="country_id" name="country_id" data-live-search="true" data-size="5">
                                                <option value="">ประเทศ</option>
                                                @foreach ($country as $item)
                                                    <option value="{{$item->country_id}}" {{$data != "" && $data->country_id == $item->country_id ? 'selected' : ''}}>{{$item->country_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <label class="form-label">รหัสไปรษณีย์</label>
                                            <input type="text" class="form-control" id="postcode" name="postcode" value="{{ $data != "" ? $data->postcode : '' }}">
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">ชื่อผู้ติดต่อ(th)<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="contact_name_th" name="contact_name_th" value="{{ $data != "" ? $data->contact_name_th : '' }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">ชื่อผู้ติดต่อ(en)<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="contact_name_gb" name="contact_name_gb" value="{{ $data != "" ? $data->contact_name_gb : '' }}" required>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="form-label">อีเมล</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{ $data != "" ? $data->email : '' }}">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <label class="form-label">เบอร์มือถือ</label>
                                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $data != "" ? $data->mobile : '' }}">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <label class="form-label">เบอร์โทร</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $data != "" ? $data->phone : '' }}">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <label class="form-label">แฟกซ์</label>
                                            <input type="text" class="form-control" id="fax" name="fax" value="{{ $data != "" ? $data->fax : '' }}">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <label class="form-label">LINE ID</label>
                                            <input type="text" class="form-control" id="lineid" name="lineid" value="{{ $data != "" ? $data->lineid : '' }}">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="form-label">เว็บไซต์</label>
                                            <input type="text" class="form-control" id="website" name="website" value="{{ $data != "" ? $data->website : '' }}">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" value="{{ $data != "" ? $data->facebook : '' }}">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="form-label">สถานะ</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="active_status" {{ $data != "" && $data->active_status == 'Y'? 'checked' : '' }}>
                                                <label class="form-check-label" for="active_status">ไม่ใช้งาน/ใช้งาน</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdn.tiny.cloud/1/3of8d3z2fu4pp5rkbjgt8r62bfhpoi3ql8jp3p8tvncs2dlp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{asset('assets/js/views/Product/supplierForm.js?v='.time())}}"></script>
@endpush