<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'ltr' : 'ltr'}}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:fb="http://ogp.me/ns/fb#">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ $favicon ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#008276">
    <title> @lang('admin_messages.hotel_panel') - {{ $site_name }}</title>

    <!-- Fonts and icons -->
    <script src="{{ asset('admin_assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {"families":["Lato:300,400,700,900"]},
            custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ["{{ asset('admin_assets/css/fonts.min.css') }} "]},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    @if(in_array(Route::currentRouteName(),['host.hotels.edit','host.rooms.edit']))
    {!! Html::style('plugins/fullcalendar/main.min.css') !!}
    @endif
    <!-- Include App Style Sheet -->
    {!! Html::style('host_assets/css/host_app.css?v='.$version) !!}
</head>
<body data-background-color="bg3">
    <div id="app" v-cloak>
        <div class="wrapper">
            <div class="main-header">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="blue">
                    <a href="{{ route('home') }}" target="_blank" class="font-weight-bold h3 text-white logo"> {{ $site_name }} </a>
                    <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-bs-toggle="collapse" data-bs-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <i class="icon-menu"></i>
                        </span>
                    </button>
                    <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="icon-menu"></i>
                        </button>
                    </div>
                </div>
                <!-- End Logo Header -->
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">

                    <div class="container-fluid">
                        <div class="collapse" id="search-nav">
                            <form class="navbar-left navbar-form nav-search mr-md-3 d-none">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="submit" class="btn btn-search pr-1">
                                            <i class="fa fa-search search-icon"></i>
                                        </button>
                                    </div>
                                    <input type="text" placeholder="Search ..." class="form-control">
                                </div>
                            </form>
                        </div>
                        <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                            <li class="nav-item dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="{{ getCurrentUser()->profile_picture_src }}" class="avatar-img rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box d-flex align-items-center">
                                                <div class="avatar-lg"><img src="{{ getCurrentUser()->profile_picture_src }}" alt="image profile" class="avatar-img rounded"></div>
                                                <div class="u-text">
                                                    <h4> {{ getCurrentUser()->full_name }} </h4>
                                                    <p class="text-muted"> {{ getCurrentUser()->email }} </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('host.edit') }}"> @lang('admin_messages.edit_profile') </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="{{ route('host.logout') }}"> @lang('admin_messages.logout') </a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
            <!-- Sidebar -->
            @include('layouts.hostLayout.navigation')
            <!-- End Sidebar -->
            <div class="main-panel">
                @yield('content')
                <footer class="footer">
                    <div class="container-fluid">
                        <nav class="float-left d-none">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">
                                        Help
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <div class="copyright ms-auto info">
                            © {{ $site_name }} {{ date("Y") }} @lang('admin_messages.all_rights_reserved')
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <!-- Popups -->
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    {!! Form::open(['url' => '#', 'class' => 'form-horizontal','id'=>'confirmDeleteForm','method' => "DELETE"]) !!}
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"> @lang('admin_messages.confirm_delete') </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <p> @lang('admin_messages.this_process_is_irreverible') </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-bs-dismiss="modal">
                            @lang('admin_messages.cancel')
                        </button>
                        <button type="submit" class="btn btn-danger">
                            @lang('admin_messages.proceed')
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <!--  Delete Confirmation Modal -->
        <!-- Show Payout Details Modal -->
        <div class="modal fade" id="confirm-payout" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    {!! Form::open(['url' => '#', 'class' => 'form-horizontal','id'=>'common_payout-form','method' => "POST"]) !!}
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">
                            <p class="payout_text confirm_text"> @lang('admin_messages.make_payout') </p>
                            <p class="refund_text confirm_text"> @lang('admin_messages.refund_amount') </p>
                            <p class="need_payout_text confirm_text"> @lang('admin_messages.need_payout_info') </p>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        {!! Form::hidden('payout_id','',['id' => 'payout_id']) !!}
                        <p class="payout_text confirm_text"> @lang('admin_messages.payout_confirmation') </p>
                        <p class="refund_text confirm_text"> @lang('admin_messages.refund_confirmation') </p>
                        <table class="table" id="payout-info">
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-bs-dismiss="modal">
                            @lang('admin_messages.cancel')
                        </button>
                        <button type="submit" class="btn btn-link btn-danger confirm_payout-btn">
                            @lang('admin_messages.proceed')
                        </button>
                        <button type="submit" class="send_notification_to_user btn btn-link d-none">
                            @lang('admin_messages.send_notification_to_user')
                        </a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <!--  End Modal -->
        <!-- End Popups -->
    </div>

    <script type="text/javascript">
        const APP_URL = {!! json_encode(url('/')) !!};
        const HOST_URL = {!! json_encode(route('host.dashboard')) !!};
        const SITE_NAME = '{!! $site_name !!}';
        const userCurrency = '{!! global_settings("default_currency") !!}';
        const userLanguage = '{!! session("language") !!}';
        const CURRENCY_SYMBOL = '{!! session("currency_symbol") !!}';
        const STRIPE_PUBLISH_KEY = "{!! credentials('publish_key','Stripe') !!}";
        const DEFAULT_LANGUAGE = '{!! App::getLocale() !!}';
        const currentRouteName = "{!! Route::currentRouteName() !!}";
        const flatpickrFormat = "{!! $selected_format['flatpickr_format'] !!}";
        const IN_ONLINE = window.navigator.onLine;
        const default_init_data = {!! json_encode([
            'error_messages' => $errors->getMessages()
            ]) !!};
        const routeList = {!!
            json_encode([
                "host_dashboard" => route("host.dashboard"),
                "fetch_report" => route("host.reports.fetch"),
                "update_hotel_options" => route("host.hotels.update_options"),
                "update_room_options" => route("host.rooms.update_options"),
                "create_payout" => route("host.payout_methods.store"),
                "reservations" => route("host.reservations"),
                "payouts" => route("host.payouts"),
                "get_price_view" => route("host.hotels.get_price_view"),
                "update_calendar_event" => route("host.rooms.update_calendar_event"),
                "get_calendar_data" => route("host.rooms.get_calendar_data"),
                ]);
            !!}
        </script>

        @if(in_array(Route::currentRouteName(),['host.dashboard']))
        {!! Html::script('admin_assets/js/plugin/chart.js/chart.min.js?v='.$version) !!}
        {!! Html::script('admin_assets/js/plugin/chart-circle/circles.min.js?v='.$version) !!}
        @endif

        @if(in_array(Route::currentRouteName(),['host.hotels.create','host.hotels.edit']))
        <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={{ credentials('map_api_key','googleMap') }}&libraries=places&language={{ session('language') ?? global_settings('default_language') }}"></script>
        @endif

        <!-- Include JS files -->
        {!! Html::script('host_assets/js/host_app.js?v='.$version) !!}
        {!! Html::script('admin_assets/js/common.js?v='.$version) !!}
        {!! Html::script('admin_assets/js/atlantis.js?v='.$version) !!}
        {!! Html::script('plugins/moment/moment.min.js') !!}

        @if(in_array(Route::currentRouteName(),['host.hotels.edit','host.rooms.edit']))
        {!! Html::script('plugins/fullcalendar/main.min.js') !!}
        {!! Html::script('plugins/fullcalendar/locales-all.min.js') !!}
        {!! Html::script('plugins/moment/moment-timezone.min.js') !!}
        <script type="text/javascript">
            const APP_TIMEZONE = '{{ config('app.timezone') }}';
            const CURRENT_TIMEZONE = moment.tz.guess(true);
        </script>
        @endif



        <!-- Jquery UI -->
        <script src="{{ asset('admin_assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>

        <!-- jQuery Scrollbar -->
        <script src="{{ asset('admin_assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

        <!-- Datatables -->
        <script src="{{ asset('admin_assets/js/plugin/datatables/datatables.min.js') }}"></script>

        <!-- Bootstrap Notify -->
        <script src="{{ asset('admin_assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

        <!-- SummerNote Editor -->
        <script src="{{ asset('admin_assets/js/plugin/summernote/summernote-bs4.min.js') }}"></script>
        
        @if(Session::has('message'))
        <script type="text/javascript">
          document.addEventListener('DOMContentLoaded',function() {
            var content = {};
            content.message = "{!! Session::get('message') !!}";
            content.title = "{!! Session::get('title') !!}";
            state = "{!! Session::get('state') !!}";

            flashMessage(content,state);
        });
        @if(checkEnabled('Conveythis'))
            <!-- ConveyThis code -->
            <script src="//cdn.conveythis.com/javascript/conveythis-initializer.js"></script>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function(e) {
                    ConveyThis_Initializer.init({
                        api_key: "pub_d24c9fdccf20e0b371d35cdddf32e0be"
                    });
                });
            </script>
            <!-- End ConveyThis code -->
        @endif
    </script>
    @endif

    @stack('scripts')
</body>
</html>