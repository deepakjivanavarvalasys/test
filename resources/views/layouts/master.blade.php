@php
    $module = \App\Models\Module::whereRoleId(Auth::user()->role_id)->first();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <title>@if(!empty($notifications) && $notifications->count()) ({{ $notifications->count() }}) @endif {{ $module->name }} @yield('title')</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Valasys Media is a top lead generation company in Dubai & USA providing 360° custom-made & personalized, B2B lead generation services." />
    <meta name="keywords" content="valasys, marketing, lead, generation, b2b">
    <meta name="author" content="Valasys Media" />

    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}">

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="base-path" content="{{ url('/') }}" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('public/template/assets/images/favicon.png') }}" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/fonts/fontawesome/css/fontawesome-all.min.css') }}">
    <!-- animation css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/animation/css/animate.min.css') }}">
    <!-- pnotify css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/pnotify/css/pnotify.custom.min.css') }}">
    <!-- pnotify-custom css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/css/pages/pnotify.css') }}">
    <!-- select2 css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/select2/css/select2.min.css') }}">
    <!-- Bootstrap datetimepicker css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/bootstrap-datepicker-1.9.0/css/bootstrap-datepicker.min.css') }}">
    <!-- notification css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/notification/css/notification.min.css') }}">
    <script>
        var page = {
            bootstrap: 3
        };

        function swap_bs() {
            page.bootstrap = 3;
        }
    </script>
    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        .watermarked {
            position: fixed;
            right: 35px;
            bottom: 10px;
            z-index: 996;
            transition: all 0.4s;
            opacity: 0.5;
        }
    </style>
    <!-- vendor css -->
    @yield('stylesheet')

    <link rel="stylesheet" href="{{ asset('public/template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/custom.css') }}">

    @yield('style')
</head>

<body>
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar navbar-image navbar-image-1" style="background-position: center top;height: 100% !important;">
    <div class="navbar-wrapper">
        <div class="navbar-brand header-logo">

            <a href="{{route($module->route_name)}}" class="b-brand">
                <span class="b-title">Valasys Media - CRM</span>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="javascript:void(0);"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            @include('layouts.sidebars.'.$module->slug)
        </div>
    </div>
</nav>

<!-- [ navigation menu ] end -->

<!-- [ Header ] start -->
@include('layouts.header', ['module' => $module])
<!-- [ Header ] end -->

<!-- [ Main Content ] start -->
@yield('content')
<!-- [ Main Content ] end -->

    <div id="modal-loader" class="modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7) !important;display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 300px !important;">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="watermarked">
        <img src="{{ asset('public/valasys-media-dark-logo.png') }}" width="180">
    </div>

    <script> var BASE_PATH = "{{ url('/') }}"; </script>
    <!-- Required Js -->
    <script src="{{ asset('public/template/assets/js/vendor-all.js') }}"></script>
    <script src="{{ asset('public/template/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/template/assets/js/pcoded.min.js') }}"></script>
    <!-- pnotify Js -->
    <script src="{{ asset('public/template/assets/plugins/pnotify/js/pnotify.custom.min.js') }}"></script>
    <!-- datepicker js -->
    <script src="{{ asset('public/template/assets/plugins/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- select2 Js -->
    <script src="{{ asset('public/template/assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Moment Js -->
    <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <!-- jquery-validation Js -->
    <script src="{{ asset('public/template/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>
    <!-- Custom Js -->
    <script src="{{asset('public/js/custom.js?='.time()) }}"></script>
    <script src="{{asset('public/js/my_profile.js?='.time()) }}"></script>
    @yield('javascript')
    <script>
        $(function (){
            @if(session('default'))
            trigger_pnofify('default', '{{ session('default')['title'] }}', '{{ session('default')['message'] }}');
            @endif
            @if(session('primary'))
            trigger_pnofify('primary', '{{ session('primary')['title'] }}', '{{ session('primary')['message'] }}');
            @endif
            @if(session('success'))
            trigger_pnofify('success', '{{ session('success')['title'] }}', '{{ session('success')['message'] }}');
            @endif
            @if(session('info'))
            trigger_pnofify('info', '{{ session('info')['title'] }}', '{{ session('info')['message'] }}');
            @endif
            @if(session('error'))
            trigger_pnofify('error', '{{ session('error')['title'] }}', '{{ session('error')['message'] }}');
            @endif
            @if(session('warning'))
            trigger_pnofify('warning', '{{ session('warning')['title'] }}', '{{ session('warning')['message'] }}');
            @endif
        });

        $(document).ready(function() {
            //document.body.style.zoom="100%";
        });
    </script>

</body>
</html>
