<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:fb="http://ogp.me/ns/fb#">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="{{ $favicon ?? '' }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="keywords" content="{{ $keywords ?? '' }}">
        <meta name="description" content="{{ $description ?? '' }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#008276">
        <title> @lang('admin_messages.admin_panel') - {{ $site_name }}</title>
        
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
        @if(in_array(Route::currentRouteName(),['admin.hotels.edit','admin.rooms.edit']))
            {!! Html::style('plugins/fullcalendar/main.min.css') !!}
        @endif
        <!-- Include App Style Sheet -->
        {!! Html::style('admin_assets/css/admin_app.css?v='.$version) !!}
         
    </head>
    <body data-background-color="bg3">
        <div id="app" v-cloak>
            <div class="wrapper">
                <div class="main-header">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="blue">
                        <a href="{{ route('home') }}" target="_blank" class="font-weight-bold h3 text-white logo"> {{ $site_name }} </a>
                        <button class="navbar-toggler sidenav-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
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
                            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                                <li class="me-3">
                                    <div style="width: 180px;" id="conveythis-language">
                                    </div>
                                </li>
                                <li class="nav-item dropdown hidden-caret">
                                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                        <div class="avatar-sm">
                                            <img src="{{ asset('images/profile.png') }}" class="avatar-img rounded-circle">
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                                        <div class="dropdown-user-scroll scrollbar-outer">
                                            <li>
                                                <div class="user-box d-flex align-items-center">
                                                    <div class="avatar-lg"><img src="{{ asset('images/profile.png') }}" alt="image profile" class="avatar-img rounded"></div>
                                                    <div class="u-text">
                                                        <h4> {{ getCurrentUser()->username }} </h4>
                                                        <p class="text-muted"> {{ getCurrentUser()->role_name }} </p>
                                                        <p class="text-muted"> {{ getCurrentUser()->email }} </p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                @checkPermission('update-admin_users')
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('admin.admin_users.edit',['id' => getCurrentUser()->id]) }}"> @lang('admin_messages.edit_profile') </a>
                                                @endcheckPermission
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('admin.logout') }}"> @lang('admin_messages.logout') </a>
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
                @include('layouts.adminLayout.navigation')
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
                            <div class="copyright ms-auto">
                                <a href="{{ global_settings('copyright_link') }}"> © {{ global_settings('copyright_text') }} </a>
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
            <div class="modal fade" id="confirmChangeCurrencyModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold"> @lang('admin_messages.confirm') </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <p> If you are changing the currency, you must change the amount values that corresponds to the currency in the below sections as well.</p>
                            <ol class="list-group list-group-numbered d-inline-block mb-3">
                                <li class="list-group-item list-group-item-secondary fw-bold">Admin panel > Site Management > Minimum Price  and Maximum Price.</li>
                                <li class="list-group-item list-group-item-secondary fw-bold">Admin panel > Fees</li>
                                <li class="list-group-item list-group-item-secondary fw-bold">Admin panel > Referral Settings</li>
                            </ol>

                            <p>Are you sure that you want to change the currency? </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-bs-dismiss="modal" v-on:click="changeCurrency('no');">
                            @lang('admin_messages.no')
                            </button>
                            <button type="button" class="btn btn-danger" v-on:click="changeCurrency('yes');">
                            @lang('admin_messages.yes')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--  Delete Confirmation Modal -->
            <!-- Show Payout Details Modal -->
            <div class="modal fade" id="confirmPayoutModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {!! Form::open(['url' => '#', 'class' => 'form-horizontal','id'=>'confirmPayoutForm']) !!}
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <p class="payout_text confirm_text"> @lang('admin_messages.make_payout') </p>
                                <p class="refund_text confirm_text"> @lang('admin_messages.refund_amount') </p>
                                <p class="payhotel_text confirm_text"> @lang('messages.pay_at_hotel') </p>
                                <p class="send_notification_to_user confirm_text"> @lang('admin_messages.need_payout_info') </p>
                                </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            {!! Form::hidden('payout_id','',['id' => 'payout_id']) !!}
                            <p class="payout_text confirm_text" id="payout_text"> @lang('admin_messages.payout_confirmation') </p>
                            <p class="refund_text confirm_text" id="refund_text"> @lang('admin_messages.refund_confirmation') </p>
                            <p class="payhotel_text confirm_text" id="payhotel_text"> @lang('messages.payhotel') </p>
                            <table class="table" id="payout-info">
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-bs-dismiss="modal">
                                @lang('admin_messages.cancel')
                            </button>
                            <button type="submit" class="btn btn-link btn-danger confirm-payout-btn" id="confirm-payout-btn">
                                @lang('admin_messages.proceed')
                            </button>
                            <button type="submit" class="btn btn-link btn-danger pay_hotel-btn" id="pay_hotel-btn">
                                @lang('admin_messages.mark_ad_paid')
                            </button>
                            <button type="submit" class="send_notification_to_user btn btn-link d-none" id="send_notification_to_user">
                                @lang('admin_messages.send_notification_to_user')
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        @if(in_array(Route::currentRouteName(),['admin.hotels.edit','admin.rooms.edit']))
            {!! Html::script('plugins/fullcalendar/main.min.js') !!}
            {!! Html::script('plugins/fullcalendar/locales-all.min.js') !!}
            {!! Html::script('plugins/moment/moment.min.js') !!}
        @endif

        @if(in_array(Route::currentRouteName(),['admin.hotels.edit','admin.rooms.edit']))
            {!! Html::script('plugins/moment/moment-timezone.min.js') !!}
            <script type="text/javascript">
                const APP_TIMEZONE = '{{ config('app.timezone') }}';
                const CURRENT_TIMEZONE = moment.tz.guess(true);
            </script>
        @endif
        <!--  End Modal -->
        <!-- End Popups -->

        <script type="text/javascript">
            const APP_URL = {!! json_encode(url('/')) !!};
            const ADMIN_URL = {!! json_encode(route('admin.dashboard')) !!};
            const SITE_NAME = '{!! $site_name !!}';
            const userCurrency = '{!! session("currency") !!}';
            const userLanguage = '{!! session("language") !!}';
            const CURRENCY_SYMBOL = '{!! session("currency_symbol") !!}';
            const DEFAULT_LANGUAGE = '{!! App::getLocale() !!}';
            const currentRouteName = "{!! Route::currentRouteName() !!}";
            const flatpickrFormat = "{!! $selected_format['flatpickr_format'] !!}";
            const popup_code  = {!! session('popup_code') ? session('popup_code') : 0  !!};
            const IN_ONLINE = window.navigator.onLine;
            const default_init_data = {!! json_encode([
                'error_messages' => $errors->getMessages()
            ]) !!};
            const routeList = {!!
                json_encode([
                    "admin_dashboard" => route("admin.dashboard"),
                    "fetch_report" => route("admin.reports.fetch"),
                    "update_hotel_options" => route("admin.hotels.update_options"),
                    "update_room_options" => route("admin.rooms.update_options"),
                    "upload_image" => route("admin.upload_image"),
                    "update_calendar_event" => route("admin.rooms.update_calendar_event"),
                    "get_calendar_data" => route("admin.rooms.get_calendar_data"),
                    "get_translations" => route("admin.translations"),
                    "update_translations" => route("admin.update_translations"),
                ]);
            !!}
        </script>

        <!-- Include JS files -->
        {!! Html::script('admin_assets/js/admin_app.js?v='.$version) !!}
        {!! Html::script('admin_assets/js/common.js?v='.$version) !!}
        {!! Html::script('admin_assets/js/atlantis.js?v='.$version) !!}

        @if(in_array(Route::currentRouteName(),['admin.dashboard']))
            {!! Html::script('admin_assets/js/plugin/chart.js/chart.min.js?v='.$version) !!}
            {!! Html::script('admin_assets/js/plugin/chart-circle/circles.min.js?v='.$version) !!}
        @endif

        @if(in_array(Route::currentRouteName(),['admin.featured_cities.create','admin.featured_cities.edit','admin.hotels.create','admin.hotels.edit','admin.popular_cities.create','admin.popular_cities.edit','admin.popular_localities.create','admin.popular_localities.edit']))
            <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={{ credentials('map_api_key','googleMap') }}&libraries=places&language={{ session('language') ?? global_settings('default_language') }}"></script>
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
        <script src="{{ asset('admin_assets/js/plugin/summernote/summernote-bs5.min.js') }}"></script>
        
        @if(Session::has('message'))
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded',function() {
                var content = {};
                content.message = '{!! Session::get("message") !!}';
                content.title = "{!! Session::get('title') !!}";
                state = "{!! Session::get('state') !!}";

                flashMessage(content,state);
            });
        </script>
        @endif

        @stack('scripts')
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
    </body>
</html>