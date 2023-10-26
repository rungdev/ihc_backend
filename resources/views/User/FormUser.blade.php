@extends('Layouts.main_layout')

@push('css')
    <!-- dropzone css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}" type="text/css" />

    <!-- Filepond css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
    <link rel="stylesheet"
        href="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.css') }}">

    <style>
        .avatar-xl {
            height: 148px;
        }
    </style>
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
                                    <li class="breadcrumb-item">แก้ไขผู้ใช้งาน</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card" id="orderList">
                            <div class="card-header border border-dashed border-end-0 border-start-0 border-top-0">
                                <div class="row align-items-center gy-3">
                                    <div class="col-sm">
                                        <h5 class="card-title mb-0">แก้ไขผู้ใช้งาน</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body ">
                                <form action="" id="formUser" target="calldata">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <input type="hidden" name="user_id" value="{{isset($user->user_id) ? $user->user_id : '' }}">
                                                    <label class="form-label">สิทธิ์ผู้ใช้งาน <span
                                                            class="text-red">*</span></label>
                                                    <select name="user_group" id="selGroup" class="selectpicker">
                                                        @foreach ($group as $item)
                                                            <option value="{{ $item->auto_id }}"
                                                                {{ isset($user->user_group) && $user->user_group == $item->auto_id ? 'selected' : '' }}>
                                                                {{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">ชื่อ <span class="text-red">*</span></label>
                                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                                        value="{{isset($user->firstname) ? $user->firstname : ''}}">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">นามสกุล <span
                                                            class="text-red">*</span></label>
                                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                                        value="{{isset($user->lastname) ? $user->lastname : ''}}">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <label class="form-label">ชื่อผู้ใช้งาน <span
                                                            class="text-red">*</span></label>
                                                    <input type="text" class="form-control" id="user_name" name="user_name"
                                                        value="{{isset($user->user_name) ? $user->user_name : ''}}">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <label class="form-label">รหัสผ่าน <span
                                                            class="text-red">*</span></label>
                                                    <input type="password" class="form-control" id="user_password"
                                                        name="user_password">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <label class="form-label">ยืนยันรหัสผ่าน <span
                                                            class="text-red">*</span></label>
                                                    <input type="password" class="form-control" id="confirm_password"
                                                        name="confirm_password">
                                                </div>
                                                <div class="col-12 mt-3">
                                                    <label class="form-label">สาขาที่ถูกมอบหมาย <span
                                                            class="text-red">*</span></label>
                                                    <select name="selBranch" id="selBranch" class="selectpicker" multiple
                                                        data-selected-text-format="count > 3">
                                                        @foreach ($branch as $item)
                                                            <option value="{{ $item->branch_id }}"
                                                                {{ array_search($item->branch_id, $b_arr) !== false ? 'selected' : '' }}>
                                                                {{ $item->branch_name_th }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="row">
                                                <div class="col-4">
                                                    <label class="form-label">รูปภาพ</label>

                                                    <div class="avatar-xl mx-auto">
                                                        <input type="hidden"
                                                            value="{{isset($user->picture_name) ? asset('uploads/img/user/' . $user->picture_name . '300' . $user->picture_extension) : ''}}"
                                                            id="urlPreview" />
                                                        <input type="file" class="filepond filepond-input-circle"
                                                            name="filepond" accept="image/png, image/jpeg, image/gif" />
                                                    </div>
                                                </div>
                                                <div class="col-8">
                                                </div>
                                                <div class="12 mt-3 mb-2">
                                                    <label class="form-label h5">ข้อมูลติดต่อ</label>
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">ชื่อเล่น</label>
                                                    <input type="text" class="form-control" placeholder="ชื่อเล่น"
                                                        id="nickname" name="nickname" value="{{isset($user->nickname) ? $user->nickname : ''}}">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">อีเมล</label>
                                                    <input type="email" class="form-control" placeholder="อีเมล"
                                                        id="user_email" name="user_email"
                                                        value="{{isset($user->user_email) ? $user->user_email : ''}}">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">เบอร์มือถือ</label>
                                                    <input type="text" class="form-control" placeholder="เบอร์มือถือ"
                                                        id="mobile" name="mobile" value="{{isset($user->mobile) ? $user->mobile : ''}}">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">เบอร์โทร</label>
                                                    <input type="text" class="form-control" placeholder="เบอร์โทร"
                                                        id="user_phone" name="user_phone"
                                                        value="{{isset($user->user_phone) ? $user->user_phone : ''}}">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">LINE ID</label>
                                                    <input type="text" class="form-control" placeholder="LINE ID"
                                                        id="user_line" name="user_line" value="{{isset($user->user_line) ? $user->user_line : ''}}">
                                                </div>
                                                <div class="col-6 mt-3">
                                                    <label class="form-label">วันเกิด</label>
                                                    <input type="text" class="form-control" placeholder="วันเกิด"
                                                        id="birthday" name="birthday" autocomplete="off"
                                                        value="{{isset($user->birthday) ? $user->birthday : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="border border-dashed border-end-0 border-start-0 border-bottom-0 mt-3 pt-3">
                                        <a href="{{ asset('usergroup') }}" class="btn btn-light waves-effect">Back</a>
                                        <button type="submit"
                                            class="btn btn-success waves-effect waves-light">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- dropzone min -->
    <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
    <!-- filepond js -->
    <script src="{{ asset('assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
    </script>
    <script
        src="{{ asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
    </script>
    <script src="{{ asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.js') }}"></script>

    <script src="{{ asset('assets/js/views/User/User.js?v=' . time()) }}"></script>
@endpush
