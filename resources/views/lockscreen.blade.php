<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | Valasys Media - CRM</title>
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
    <meta name="description" content="Valasys Media"/>
    <meta name="keywords" content="Valasys Media"/>
    <meta name="author" content="Valasys Media" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('public/template') }}/assets/images/favicon.png" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- animation css -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/plugins/animation/css/animate.min.css">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/css/style.css">
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/css/layouts/dark.css">

</head>

<body>
    <div class="auth-wrapper aut-bg-img" style="background-image: url('{{ asset('public/template') }}/assets/images/bg-images/bg3.jpg');">
        <div class="auth-content">
            <div class="text-white">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="feather icon-unlock auth-icon"></i>
                    </div>
                    <h3 class="mb-4 text-white">Session Timeout</h3>
                    <form method="post" action="{{ route('unlockscreen') }}" autocomplete="off">
                        @csrf
                        @if(session('error'))
                            <div class="input-group mb-3">
                                <span class="text-danger">{{session('error')}}</span>
                            </div>
                        @endif
                        <div class="input-group mb-3" style="display: none;">
                            <input value="{{ $email }}" type="email" class="form-control" placeholder="Email" name="email" required disabled>
                        </div>
                        <div class="input-group mb-4" style="display: none;">
                            <input type="password" class="form-control" placeholder="password" value="" autocomplete="off">
                        </div>
                        <button class="btn btn-outline-light shadow-2 mb-4" type="submit">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Required Js -->
<script src="{{ asset('public/template') }}/assets/js/vendor-all.js"></script>
<script src="{{ asset('public/template') }}/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="{{ asset('public/template') }}/assets/js/pcoded.js"></script>

</body>
</html>
