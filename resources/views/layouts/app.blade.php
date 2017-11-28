<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="admin-themes-lab">
    <meta name="author" content="themes-lab">
    <link rel="shortcut icon" href="{{ asset('global/images/favicon.png') }}" type="image/png">
    <title>@yield('title') | SISVAN</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}"  value="{{ csrf_token() }}">
    <!-- Styles -->
    <link href="{{ asset('global/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('global/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('global/css/ui.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/layout1/css/layout.css') }}" rel="stylesheet">
    <link href="{{ asset('global/plugins/bootstrap-loading/lada.min.css') }}" rel="stylesheet">
    <link href="{{ asset('global/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('global/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js') }}"></script>
    <style>
        #nav-top-f{
            background: url({{ asset('global/images/logo/logo-white.png') }}) no-repeat !important;
            height:inherit !important;
        }
        .select2-container{
            width: 100% !important;
        }
        input[type=date]{
            height: 34px !important;
        }
        .modal-dialog{
            margin-top: 1% !important;
        }
    </style>
</head>
<body class="fixed-topbar sidebar-hover fixed-sidebar theme-sltd color-primary">
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<section>
    <div class="main-content">
        <!-- BEGIN TOPBAR -->
        <div class="topbar">
            <div class="header-left" >
                <div class="topnav" >
                    <div class="logopanel2" ><h1 id="nav-top-f"></h1></div>
                    <ul class="nav nav-horizontal mmenu">
                        <!-- Mega Menu -->
                        <li class="mmenu-fw" id="mmenu-fw-upgds">
                            <a href="{{route('upgds')}}" data-delay="100"><span class="fa fa-building"></span> UPGDS</a>
                        </li>
                        <li class="mmenu-fw" id="mmenu-fw-usuarios">
                            <a href="{{route('usuarios')}}" data-delay="100"><span class="fa fa-user-md"></span> Usuarios</a>
                        </li>
                        <li class="mmenu-fw" id="mmenu-fw-pacientes">
                            <a href="{{route('pacientes')}}" data-delay="100"><span class="fa fa-users"></span> Pacientes</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="header-right">
                <ul class="header-menu nav navbar-nav">
                    <li class="dropdown" id="user-header">
                        <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img src="{{ asset('global/images/avatars/avatar12.png') }}" alt="user image">
                            <span class="username">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="icon-logout"></i>
                                    <span>Cerrar sesión</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER DROPDOWN -->
                </ul>
            </div>
            <!-- header-right -->
        </div>
        <!-- END TOPBAR -->
        <!-- BEGIN PAGE CONTENT -->
        <div class="page-content">
            <div class="header">
                <h2><strong>@yield('title')</strong> @yield('title_aux')</h2>
            </div>
            <div class="row">
                @yield('content')
            </div>
            <div class="footer">
                <div class="copyright">
                    <p class="pull-left sm-pull-reset">
                        <span>Copyright <span class="copyright">©</span> 2017 </span>
                        <span>Juan David Cardona</span>.
                        <span>All rights reserved. </span>
                    </p>
                    <p class="pull-right sm-pull-reset">
                        <span><a href="#" class="m-r-10">Soporte</a> | <a href="#" class="m-l-10 m-r-10">Terminos de uso</a> | <a href="#" class="m-l-10">Políticas de privacidad</a></span>
                    </p>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <!-- END MAIN CONTENT -->
    <!-- BEGIN BUILDER -->
    <div class="builder hidden-sm hidden-xs" id="builder">
        <a class="builder-toggle"><i class="icon-wrench"></i></a>
        <div class="inner">
            <div class="builder-container">
                <h4 class="border-top">Color</h4>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="theme-color background-primary" data-main="primary" data-color="#319DB5"></div>
                        <div class="theme-color bg-red" data-main="red" data-color="#C75757"></div>
                        <div class="theme-color bg-green" data-main="green" data-color="#1DA079"></div>
                        <div class="theme-color bg-orange" data-main="orange" data-color="#D28857"></div>
                        <div class="theme-color bg-purple" data-main="purple" data-color="#B179D7"></div>
                        <div class="theme-color bg-blue" data-main="blue" data-color="#4A89DC"></div>
                    </div>
                </div>
                <h4 class="border-top">Background</h4>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="bg-color bg-clean" data-bg="clean" data-color="#F8F8F8"></div>
                        <div class="bg-color bg-lighter" data-bg="lighter" data-color="#EFEFEF"></div>
                        <div class="bg-color bg-light-default" data-bg="light-default" data-color="#E9E9E9"></div>
                        <div class="bg-color bg-light-blue" data-bg="light-blue" data-color="#E2EBEF"></div>
                        <div class="bg-color bg-light-purple" data-bg="light-purple" data-color="#E9ECF5"></div>
                        <div class="bg-color bg-light-dark" data-bg="light-dark" data-color="#DCE1E4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END BUILDER -->
</section>
<!-- BEGIN PRELOADER -->
<div class="loader-overlay">
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>
<!-- END PRELOADER -->
<a href="#" class="scrollup"><i class="fa fa-angle-up"></i></a>
<script src="{{asset('js/app.js')}}"></script>
    <!-- Scripts -->
    <script src="{{ asset('global/plugins/jquery/jquery-3.1.0.min.js') }}"></script>
    <script src="{{ asset('global/plugins/jquery/jquery-migrate-3.0.0.min.js') }}"></script>
    <script src="{{ asset('global/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('global/plugins/gsap/main-gsap.min.js') }}"></script>
    <script src="{{ asset('global/plugins/tether/js/tether.min.js') }}"></script>
    <script src="{{ asset('global/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('global/plugins/appear/jquery.appear.js') }}"></script>

    <script src="{{ asset('global/plugins/jquery-cookies/jquery.cookies.min.js') }}"></script> <!-- Jquery Cookies, for theme -->
    <script src="{{ asset('global/plugins/jquery-block-ui/jquery.blockUI.min.js') }}"></script> <!-- simulate synchronous behavior when using AJAX -->
    <script src="{{ asset('global/plugins/bootbox/bootbox.min.js') }}"></script> <!-- Modal with Validation -->
    <script src="{{ asset('global/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script> <!-- Custom Scrollbar sidebar -->
    <script src="{{ asset('global/plugins/bootstrap-dropdown/bootstrap-hover-dropdown.min.js') }}"></script> <!-- Show Dropdown on Mouseover -->
    <script src="{{ asset('global/plugins/charts-sparkline/sparkline.min.js') }}"></script> <!-- Charts Sparkline -->
    <script src="{{ asset('global/plugins/retina/retina.min.js') }}"></script> <!-- Retina Display -->
    <script src="{{ asset('global/plugins/select2/dist/js/select2.js') }}"></script> <!-- Select Inputs -->
    <script src="{{ asset('global/plugins/icheck/icheck.min.js') }}"></script> <!-- Checkbox & Radio Inputs -->
    <script src="{{ asset('global/plugins/backstretch/backstretch.min.js') }}"></script> <!-- Background Image -->
    <script src="{{ asset('global/plugins/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script> <!-- Animated Progress Bar -->
    <script src="{{ asset('global/plugins/charts-chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('global/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('global/js/builder.js') }}"></script> <!-- Theme Builder -->
    <script src="{{ asset('global/js/sidebar_hover.js') }}"></script> <!-- Sidebar on Hover -->
    <script src="{{ asset('global/js/application.js') }}"></script> <!-- Main Application Script -->
    <script src="{{ asset('global/js/plugins.js') }}"></script> <!-- Main Plugin Initialization Script -->
{{--    <script src="{{ asset('global/js/widgets/notes.js') }}"></script> <!-- Notes Widget -->--}}
{{--    <script src="{{ asset('global/js/quickview.js') }}"></script> <!-- Chat Script -->--}}
{{--    <script src="{{ asset('global/js/pages/search.js') }}"></script> <!-- Search Script -->--}}
    <!-- BEGIN PAGE SCRIPT -->
    <script src="{{ asset('global/plugins/bootstrap-loading/lada.min.js') }}"></script>
    <script src="{{ asset('global/plugins/switchery/switchery.js') }}"></script>
    <script src="{{ asset('global/plugins/moment/moment.min.js') }}"></script>
    @yield('scripts')
    <!-- END PAGE SCRIPTS -->
    <script src="{{ asset('admin/layout1/js/layout.js') }}"></script>
</body>
</html>
