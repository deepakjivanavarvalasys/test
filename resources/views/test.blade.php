<!DOCTYPE html>
<html lang="en">

<head>
    <title>Datta Able - Echart</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Datta Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords" content="admin templates, bootstrap admin templates, bootstrap 4, dashboard, dashboard templets, sass admin templets, html admin templates, responsive, bootstrap admin templates free download,premium bootstrap admin templates, datta able, datta able bootstrap admin template">
    <meta name="author" content="Codedthemes" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('public/template') }}/assets/images/favicon.ico" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- animation css -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/plugins/animation/css/animate.min.css">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/css/style.css">

</head>

<body>
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Echart Chart</h5>
                                </div>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                    <li class="breadcrumb-item"><a href="#!">Chart</a></li>
                                    <li class="breadcrumb-item"><a href="#!">Echart Chart</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->
                <div class="main-body">
                    <div class="page-wrapper">
                        <!-- [ Main Content ] start -->
                        <div class="row">
                            <div class="col-xl-6 col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Bar [ Basic Bar ] Chart</h5>
                                        <div class="card-block  text-center">
                                            <div id="chart-Bar-besic-bar" style="width: 100%; height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [ Main Content ] end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Required Js -->
<script src="{{ asset('public/template') }}/assets/js/vendor-all.min.js"></script>
<script src="{{ asset('public/template') }}/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="{{ asset('public/template') }}/assets/js/pcoded.min.js"></script>

<!-- pnotify Js -->
<script src="{{ asset('public/template/assets/plugins/pnotify/js/pnotify.custom.min.js') }}"></script>

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




<!--echarts chart -->
<script src="http://echarts.baidu.com/echarts2/doc/example/timelineOption.js"></script>
<script src="{{ asset('public/template') }}/assets/plugins/chart-echarts/js/echarts-en.min.js"></script>
{{--<script src="{{ asset('public/template') }}/assets/js/pages/chart-echart-custom.js"></script>--}}
<script src="{{ asset('public/js/agent/dashboard.js?='.time()) }}"></script>

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

    document.body.style.zoom="81%"
</script>

</body>
</html>
