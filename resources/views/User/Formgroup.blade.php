@extends('Layouts.main_layout')

@push('css')
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
                                    <li class="breadcrumb-item"><a href="/usergroup">สิทธิ์กลุ่มผู้ใช้งาน</a></li>
                                    <li class="breadcrumb-item">แก้ไขกลุ่ม</li>
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
                                        <h5 class="card-title mb-0">แก้ไขกลุ่ม</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-4 border border-dashed border-end-0 border-start-0">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">ชื่อกลุ่ม <span
                                                    class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="groupname"
                                                placeholder="ชื่อกลุ่ม"
                                                value="{{ isset($group->name) ? $group->name : '' }}">
                                            <input type="hidden" class="form-control" id="groupid"
                                                value="{{ isset($group->auto_id) ? $group->auto_id : '' }}">
                                            <input type="hidden" class="form-control" id="userid"
                                                value="{{ $user->user_id }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tabA" role="tab"
                                            aria-selected="false">
                                            ผู้ดูแลระบบ
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tabE" role="tab"
                                            aria-selected="false">
                                            E-Commerce
                                        </a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content  text-muted">
                                    <div class="tab-pane active" id="tabA" role="tabpanel">
                                        @foreach ($data['A'] as $item)
                                            <div class="mt-2"> <b>{{ $item->module_name_th }}</b> </div>
                                            @if ($item->module_permission == '')
                                                @foreach ($item->modulesub as $items)
                                                    <div class="mt-2 mb-2">{{ $items->module_name_th }}</div>
                                                    <div class="row">
                                                        @foreach ($items->per_txt as $i)
                                                            <div class="col-2">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input premission-check"
                                                                        type="checkbox" data-id="{{ $items->module_id }}"
                                                                        data-type="{{ $i }}"
                                                                        id="{{ $items->module_id . '_' . $i }}"
                                                                        {{ isset($permission[$items->module_id . '_' . $i]) && $permission[$items->module_id . '_' . $i] == '1' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="{{ $items->module_id . '_' . $i }}">
                                                                        {{ $i }} </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row">
                                                    @foreach (explode(',', $item->module_permission) as $items)
                                                        <div class="col-2">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input premission-check"
                                                                    type="checkbox" data-id="{{ $item->module_id }}"
                                                                    data-type="{{ $items }}"
                                                                    id="{{ $item->module_id . '_' . $items }}"
                                                                    {{ isset($permission[$item->module_id . '_' . $items]) && $permission[$item->module_id . '_' . $items] == '1' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="{{ $item->module_id . '_' . $items }}">
                                                                    {{ $items }} </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div
                                                class="mt-3 mb-3 border border-dashed border-end-0 border-start-0 border-top-0 ">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="tab-pane" id="tabE" role="tabpanel">
                                        @foreach ($data['E'] as $item)
                                            <div class="mt-2"> <b>{{ $item->module_name_th }}</b> </div>
                                            @if ($item->module_permission == '')
                                                @foreach ($item->modulesub as $items)
                                                    <div class="mt-2 mb-2">{{ $items->module_name_th }}</div>
                                                    <div class="row">
                                                        @foreach ($items->per_txt as $i)
                                                            <div class="col-2">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input premission-check"
                                                                        type="checkbox" data-id="{{ $items->module_id }}"
                                                                        data-type="{{ $i }}"
                                                                        id="{{ $items->module_id . '_' . $i }}"
                                                                        {{ isset($permission[$items->module_id . '_' . $i]) && $permission[$items->module_id . '_' . $i] == '1' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="{{ $items->module_id . '_' . $i }}">
                                                                        {{ $i }} </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row">
                                                    @foreach (explode(',', $item->module_permission) as $items)
                                                        <div class="col-2">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input premission-check"
                                                                    type="checkbox" data-id="{{ $item->module_id }}"
                                                                    data-type="{{ $items }}"
                                                                    id="{{ $item->module_id . '_' . $items }}"
                                                                    {{ isset($permission[$item->module_id . '_' . $items]) && $permission[$item->module_id . '_' . $items] == '1' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="{{ $item->module_id . '_' . $items }}">
                                                                    {{ $items }} </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div
                                                class="mt-3 mb-3 border border-dashed border-end-0 border-start-0 border-top-0 ">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <a href="{{ asset('usergroup') }}" class="btn btn-light waves-effect">Back</a>
                                <button type="button" class="btn btn-success waves-effect waves-light"
                                    id="btn-save">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/views/User/User.js?v=' . time()) }}"></script>
@endpush
