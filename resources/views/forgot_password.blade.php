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

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300&display=swap" rel="stylesheet">
</head>

<body>
<div class="auth-wrapper aut-bg-img-side cotainer-fiuid align-items-stretch">
    <div class="row align-items-center w-100 align-items-stretch bg-white">
        <div class="d-none d-lg-flex col-lg-8 aut-bg-img align-items-center d-flex justify-content-center">
            <div class="col-md-8">
                <small>Version 2.0</small>
                <br><br>
                <span class="text-dark bg-white" style="border-radius:4px; font-size: 20px;padding: 5px 10px 5px 10px;font-family: 'Nunito', sans-serif;font-weight: 300">Valasys Media</span>
                <h1 class="text-white mb-5" style="font-size: 70px; font-weight: 900;font-family: 'Roboto Slab', serif;">Program<br>Management</h1>
            </div>
        </div>
        <div class="col-lg-4 align-items-stret h-100 align-items-center d-flex justify-content-center">
            <div class=" auth-content text-center">
                <div class="mb-4">
                    <i class="feather icon-mail auth-icon"></i>
                </div>
                <h3 class="mb-4">Reset Password</h3>
                <form method="post" action="{{ route('send_reset_link') }}">
                    @csrf
                    @if(session('success'))
                        <div class="input-group mb-3">
                            <span class="text-success">{{session('success')}}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="input-group mb-3">
                            <span class="text-danger">{{session('error')}}</span>
                        </div>
                    @endif
                    <div class="input-group mb-3">
                        <input @if(Cookie::has('login_email')) value="{{ Cookie::get('login_email') }}" @endif type="email" class="form-control" placeholder="Email" name="email" required>
                    </div>
                    <button class="btn btn-primary shadow-2 mb-4" type="submit">Reset Password</button>
                    <p class="mb-0 text-muted">Already have an account? <a href="{{ route('login') }}">Login</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Required Js -->
<script src="{{ asset('public/template') }}/assets/js/vendor-all.min.js"></script><script src="{{ asset('public/template') }}/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="{{ asset('public/template') }}/assets/js/pcoded.min.js"></script>

</body>
</html>
