<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>iHAVECPU</title>

    <!-- nouisliderribute css -->
    <link rel="stylesheet" href="{{asset('/assets/libs/nouislider/nouislider.min.css')}}">
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{asset('/assets/libs/gridjs/theme/mermaid.min.css')}}">
    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('/assets/css/bootstrap.min.css?v=1')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{asset('/assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('/assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('/assets/css/main.css?v='.time())}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{asset('assets/libs/bootstrap-select/dist/css/bootstrap-select.min.css')}}">

    <link rel="stylesheet" href="{{asset('/assets/libs/select2/original/select2.min.css')}}" />
    {{-- <link rel="stylesheet" href="{{asset('/assets/libs/select2/css/select2.min.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/libs/select2/css/select2-bootstrap-5-theme.min.css')}}" /> --}}

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{asset('assets/libs/datetimepicker/jquery.datetimepicker.css')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        *{
            font-family: 'Kanit', sans-serif;
        }
    </style>
    @stack('css')

</head>
<body>
    <div class="loader-frame">
        <div class="loader">
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--text"></div>
        </div>
    </div>
    
    <iframe src="" id="calldata" name="calldata" style="display: none;" frameborder="0"></iframe>
    @if (Request::is('signin'))
        @yield('content')
    @else
        <div id="layout-wrapper">
            @include('Layouts.header')
            @include('Layouts.menu')
            @yield('content')
        </div>
    @endif
    <!-- JAVASCRIPT -->
    <script src="{{asset('/assets/libs/jquery/jquery-3.7.0.min.js')}}"></script>
    <script src="{{asset('/assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('/assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('/assets/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('/assets/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('/assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('/assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <!-- sortablejs -->
    <script src="{{asset('/assets/libs/sortablejs/Sortable.min.js')}}"></script>

    <!-- nestable init js -->
    <script src="{{asset('/assets/js/pages/nestable.init.js')}}"></script>

    {{-- <script src="{{asset('/assets/libs/select2/js/select2.full.min.js')}}"></script> --}}
    <script src="{{asset('/assets/libs/select2/original/select2.min.js')}}"></script>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="{{asset('assets/libs/datetimepicker/build/jquery.datetimepicker.full.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>


    <script>
        var user_id     = {{ (isset(session('user')->user_id) ? session('user')->user_id : 'null') }};
        var token_login = "{{ (isset(session('user')->tokenlogin) ? session('user')->tokenlogin : 'null') }}";
        var group_id    = {{ isset(session('user')->user_group) ? session('user')->user_group : 'null' }};
        user_id         = (user_id == 'null' ? '': user_id);
        group_id        = (group_id == 'null' ? '': group_id);
        token_login     = (token_login == 'null' ? '': token_login);
        $(function () {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'USER-GROUP': group_id
                }
            });
        });
    </script>



    @stack('script')

    
</body>
</html>