@extends('layouts.master')

@section('content')
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
                                        <h5 class="m-b-10">Dashboard</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);"><i
                                                    class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [ breadcrumb ] end -->
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- [ Main Content ] start -->
                            <h1 class="text-info">Hello Admin</h1>
                            <!-- [ Main Content ] end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @parent
    <!-- amchart js -->
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/amcharts.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/gauge.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/serial.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/light.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/pie.min.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/ammap.min.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/usaLow.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/radar.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/amchart/js/worldLow.js"></script>

    <!-- Float Chart js -->
    <script src="{{ asset('public/template') }}/assets/plugins/flot/js/jquery.flot.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/flot/js/jquery.flot.categories.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/flot/js/curvedLines.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/flot/js/jquery.flot.tooltip.min.js"></script>

    <!-- dashboard-custom js -->
    <script src="{{ asset('public/template') }}/assets/js/pages/dashboard-crypto.js"></script>
@append
