<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Inicio de sesión | SISVAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="" name="description" />
    <meta content="themes-lab" name="author" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <link href="{{ asset('global/images/favicon.png') }}" rel="shortcut icon">
    <link href="{{ asset('global/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('global/css/ui.css') }}" rel="stylesheet">
    <link href="{{ asset('global/plugins/bootstrap-loading/lada.min.css') }}" rel="stylesheet">
</head>
<body class="sidebar-light account2" data-page="login">
    <!-- BEGIN LOGIN BOX -->
    <div class="container" id="login-block">
        <i class="user-img icons-faces-users-03"></i>
        <div class="account-info">
            <span class="logo"></span>
            <h3>Modular &amp; Flexible Admin.</h3>
            <ul>
                <li><i class="icon-magic-wand"></i> Fully customizable</li>
                <li><i class="icon-layers"></i> Various sibebars look</li>
                <li><i class="icon-arrow-right"></i> RTL direction support</li>
                <li><i class="icon-drop"></i> Colors options</li>
            </ul>
        </div>
        <div class="account-form">
            @yield('content')
        </div>
    </div>
    <!-- END LOCKSCREEN BOX -->
    <p class="account-copyright">
        <span>Copyright © 2017 </span><span>Juan David Cardona</span>.<span> All rights reserved.</span>
    </p>
    <!-- Scripts -->
    <script src="{{ asset('global/plugins/jquery/jquery-3.1.0.min.js') }}"></script>
    <script src="{{ asset('global/plugins/jquery/jquery-migrate-3.0.0.min.js') }}"></script>
    <script src="{{ asset('global/plugins/gsap/main-gsap.min.js') }}"></script>
    <script src="{{ asset('global/plugins/tether/js/tether.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('global/plugins/backstretch/backstretch.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap-loading/lada.min.js') }}"></script>
    <script src="{{ asset('global/js/pages/login-v2.js') }}"></script>
    <script src="{{ asset('admin/layout3/js/layout.js') }}"></script>
</body>
</html>
