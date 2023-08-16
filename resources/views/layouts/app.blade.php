<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:fb="http://ogp.me/ns/fb#">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="shortcut icon" href="{{ $favicon ?? '' }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="keywords" content="{{ getMetaData('keywords') }}">
        <meta name="description" content="{{ getMetaData('description') }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#008276">
        <title> {{ $title ?? getMetaData('title') }} </title>
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{!! global_settings('font_script_url') !!}" rel="stylesheet">

        <style type="text/css">
            :root {
                --font-family: {!! global_settings('font_family') !!};
            }
        </style>
        <!-- Include App Style Sheet -->
        {!! Html::style('css/app.css?v='.$version) !!}

        @if(in_array(Route::currentRouteName(),['hotel_details', 'manage_hotel']))
            {!! Html::style('plugins/lightgallery/css/lightgallery.min.css') !!}
            {!! Html::style('plugins/lightgallery/css/lg-transitions.min.css') !!}
            {!! Html::style('plugins/lightslider/css/lightslider.min.css') !!}
        @endif
        @if(in_array(Route::currentRouteName(),['hotel_details','manage_hotel','edit_review']))
            <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
        @endif
        @if(in_array(Route::currentRouteName(),['manage_hotel']))
            {!! Html::style('plugins/fullcalendar/main.min.css') !!}
        @endif
        {!! global_settings('head_code') !!}

        @if((Str::contains(Route::currentRouteName(),'search')) && (credentials('is_enabled' ,'googleMap') == 0))
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="">
        @endif
    </head>
    @php
        $body_class = '';
        if(!isset($exception)) {
            $body_class .= (Str::contains(Route::currentRouteName(),'search')) ? ' search-page' : '';
            $body_class .= (Route::currentRouteName() == 'wishlist.list') ? ' search-page' : '';
        }
    @endphp
    <body class="{{ trim($body_class) }}">
        <div id="app" v-cloak>
            @if(session('is_mobile'))
                @yield('content')
            @else
                @if(in_array(Route::currentRouteName(),NO_HEADER_ROUTES))
                    @include('layouts.header_no_menu')
                @else
                    @include('layouts.header')
                @endif
                
                @yield('content')
                
                @include('layouts.popups')
                @if(!in_array(Route::currentRouteName(),NO_FOOTER_ROUTES) || isset($exception))
                    @include('layouts.footer')
                @endif

                <!-- Cookie Alert -->
                <div id="cookie-alert" class="alert alert-dismissible fade d-none" role="alert">
                    <div class="d-flex flex-wrap px-3 py-2 h5 justify-content-center m-0">
                        <p class="m-0">
                            <b> @lang('messages.cookies_text') </b>
                        </p>
                        <div class="mt-3">
                            <a target="_blank" href="{{ resolveRoute('static_page',['slug' => 'cookie-policy']) }}" class="text-white px-2 me-2 border-end"> @lang('messages.learn_more') </a>
                            <a href="#" role="button" class="text-white p-2 me-2" data-bs-dismiss="alert" aria-label="Close"> @lang('messages.ok_got_it') </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <script type="text/javascript">
            const APP_URL = {!! json_encode(url('/')) !!};
            const SITE_NAME = '{!! $site_name !!}';
            const USER_ID = '{!! Auth::check() ? Auth::id() : 0 !!}';
            const HOST_ID = '{!! getHostId() ?? 0 !!}';
            const CURRENCY_SYMBOL = '{!! session("currency_symbol") !!}';
            const userCurrency = '{!! session("currency") !!}';
            const userLanguage = '{!! session("language") !!}';
            const inbox_count = '{!! Auth::check() ? Auth::user()->inbox_count : 0 !!}';
            const popup_code  = {!! session('popup_code') ? session('popup_code') : 0  !!};
            const DEFAULT_LANGUAGE = '{!! App::getLocale() !!}';
            const currentRouteName = "{!! Route::currentRouteName() !!}";
            const flatpickrFormat = "{!! $selected_format['flatpickr_format'] !!}";
            const STRIPE_PUBLISH_KEY = "{!! credentials('publish_key','Stripe') !!}";
            const max_guests = "{!! $max_guests !!}"
            const GOOGLE_CLIENT_ID = "{!! credentials('client_id','Google') !!}";
            const FACEBOOK_APP_ID = "{!! credentials('app_id','Facebook') !!}";
            const GOOGLE_MAP_ENABLED = {!! credentials('is_enabled' ,'googleMap') !!};
            // Check User has valid internet connection
            const IN_ONLINE = window.navigator.onLine;
            const default_init_data = {!! json_encode([
                'error_messages' => $errors->getMessages()
            ]) !!};
            const routeList = {!!
                json_encode([
                    "complete_social_signup" => resolveRoute("complete_social_signup"),
                    "update_user_default" => resolveRoute("update_user_default"),
                    "update_profile" => resolveRoute("update_profile"),
                    "number_verification" => resolveRoute("number_verification"),
                    "transaction_history" => resolveRoute("transaction_history"),
                    "remove_profile_picture" => resolveRoute("remove_profile_picture"),
                    "update_profile_picture" => resolveRoute("update_profile_picture"),
                    "all_wishlists" => resolveRoute("all_wishlists"),
                    "create_wishlist" => resolveRoute("wishlist.create"),
                    "save_to_wishlist" => resolveRoute("wishlist.save"),
                    "remove_from_wishlist" => resolveRoute("wishlist.remove"),
                    "destroy_wishlist" => resolveRoute("wishlist.destroy"),
                    "search_result" => resolveRoute("hotel_search_result"),
                    "help_search_result" => resolveRoute("help_search_result"),
                    "confirm_reserve" => resolveRoute("confirm_reserve"),
                    "get_reservations" => resolveRoute("get_reservations"),
                    "request_action" => resolveRoute("request_action"),
                    "complete_payment" => resolveRoute("payment.complete"),
                    "validate_coupon" => resolveRoute("payment.validate_coupon"),
                    "authenticate_mobile" => resolveRoute("authenticate_mobile"),
                    "get_home_data" => resolveRoute("get_home_data"),
                    "get_referral" => resolveRoute("get_referral"),
                    "invite_user" => resolveRoute("invite_user"),
                    "share_itinerary" => resolveRoute("share_itinerary"),
                    "check_availability" => resolveRoute("check_availability"),
                    "message_list" => resolveRoute("message_list"),
                    "inbox_action" => resolveRoute("inbox_action"),
                    "send_message" => resolveRoute("send_message"),
                    "update_read_status" => resolveRoute('update_read_status'),
                    "search_hotels" => resolveRoute('search_hotels'),
                    "upload_user_document" => resolveRoute('upload_user_document'),
                    "host_signup" => route("host.create_host"),
                    "update_review" => route("update_review"),
                    "create_host_validation" => route("host.create_host_validation"),
                ]);
            !!}
            	const occupancy_messages = {!! json_encode([
                    "room" => Lang::get('messages.room'),
                    "rooms" => Lang::get('messages.rooms'),
                    "adult" => Lang::get('messages.adult'),
                    "adults" => Lang::get('messages.adults'),
                    "child" => Lang::get('messages.child'),
                    "children" => Lang::get('messages.children'),
                ]);
            !!}
        </script>

        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ credentials('map_api_key','googleMap') }}&libraries=places&language={{ session('language') ?? 'en' }}"></script>

        <!-- Include JS files -->
        {!! Html::script('js/app.js?v='.$version) !!}
        {!! Html::script('js/common.js?v='.$version) !!}
        {!! Html::script('plugins/moment/moment.min.js') !!}

        @if(in_array(Route::currentRouteName(),['conversation','hotel_details','manage_hotel']))
            {!! Html::script('plugins/moment/moment-timezone.min.js') !!}
        @endif

        @if(in_array(Route::currentRouteName(),['hotel_details', 'manage_hotel']))
            {!! Html::script('plugins/lightslider/js/lightslider.min.js') !!}
            {!! Html::script('plugins/lightgallery/js/lightgallery.min.js') !!}
        @endif

        @if(in_array(Route::currentRouteName(),['home', 'hotel_search', 'hotel_details','view_profile','wishlist.list','manage_hotel']))
            {!! Html::script('plugins/tinyslider/tiny-slider.js') !!}
        @endif

        @if(in_array(Route::currentRouteName(),['hotel_details','edit_review','manage_hotel']))
            <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
        @endif

        @if(in_array(Route::currentRouteName(),['manage_hotel']))
            {!! Html::script('plugins/fullcalendar/main.min.js') !!}
            {!! Html::script('plugins/fullcalendar/locales-all.min.js') !!}
            {!! Html::script('plugins/moment/moment-timezone.min.js') !!}
        @endif

        @if(Route::currentRouteName() == 'manage_hotel')
            {!! Html::script('plugins/jquery-ui/jquery-ui.js') !!}
        @endif
        
        @if(Auth::guest() || (Route::currentRouteName() == 'update_account_settings' && request()->page == 'login-and-security'))
            @if(checkEnabled('Google') && isSecure())
            <script src="https://accounts.google.com/gsi/client" async defer></script>
            <script type="text/javascript">
                window.onGoogleLibraryLoad = function () {
                    google.accounts.id.initialize({
                        client_id: "{{ credentials('client_id','Google') }}",
                        context: "signin",
                        ux_mode: "popup",
                        auto_prompt: false,
                        auto_select: true,
                        nonce: "{{ Str::uuid() }}",
                        callback: (response) => {
                            window.location = routeList.complete_social_signup+'?auth_type=Google&id_token='+response.credential;
                        },
                    });

                    if(USER_ID == '') {
                        window.google.accounts.id.prompt();
                    }

                    let google_login_elems = document.querySelectorAll(".g-signin");
                    google_login_elems.forEach(function(element) {
                        google.accounts.id.renderButton(element, {
                            type: "standard",
                            shape: "rectangular",
                            theme: "outlined",
                            text: "continue_with",
                            size: "medium",
                            logo_alignment: "left",
                            width: element.offsetWidth+"px",
                            locale : "{{ session('language') }}",
                        });
                    });
                };
            </script>
            @endif
            @if(checkEnabled('Facebook') && isSecure())
            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=0&version=v6.0&appId={{ credentials('app_id','Facebook') }}" onload="initFacebookSignIn();"></script>
            @endif
            @if(checkEnabled('Apple') && isSecure())
            <script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js" onload="initAppleSignIn()"></script>
            <script type="text/javascript">
                AppleID.auth.init({
                    clientId: "{{ credentials('service_id','Apple') }}",
                    scope: 'name email',
                    redirectURI: "{{ url('/') }}",
                    state: "{{ bin2hex(random_bytes(5)) }}",
                    nonce: "{{ Str::uuid() }}",
                    usePopup : true
                });
            </script>
            @endif
        @endif
        @if(checkEnabled('ReCaptcha'))
            @if(credentials('version','ReCaptcha') == '3')
            <script type="text/javascript">
                function initReCaptcha()
                {
                    document.querySelectorAll("button[type='submit']").forEach(function(element, index) {
                        grecaptcha.render(element, {
                            'sitekey' : "{{ credentials('site_key','ReCaptcha') }}",
                            'callback' : (token) => {
                                let form = element.closest('form');
                                var input = document.createElement("input");
                                input.setAttribute("type", "hidden");
                                input.setAttribute("name", "g-recaptcha-response");
                                input.setAttribute("value", token);
                                form.appendChild(input);
                                form.submit();
                            }
                        });
                    });
                }
            </script>
            <script src="//google.com/recaptcha/api.js?onload=initReCaptcha&render=explicit" async defer></script>
            @else
            <script src="//google.com/recaptcha/api.js" async defer></script>
            @endif
        @endif

        @if(Auth::check() && checkEnabled('Firebase'))
            <script src="https://www.gstatic.com/firebasejs/8.5.0/firebase-app.js"></script>
            <script src="https://www.gstatic.com/firebasejs/8.5.0/firebase-auth.js"></script>
            <script src="https://www.gstatic.com/firebasejs/8.5.0/firebase-database.js"></script>

            <script>
                const firebaseConfig = {
                    apiKey: "{{ credentials('api_key','Firebase') }}",
                    authDomain: "{{ credentials('auth_domain','Firebase') }}",
                    databaseURL: "{{ credentials('database_url','Firebase') }}",
                    projectId: "{{ credentials('project_id','Firebase') }}",
                    storageBucket: "{{ credentials('storage_bucket','Firebase') }}",
                    messagingSenderId: "{{ credentials('messaging_sender_id','Firebase') }}",
                    appId: "{{ credentials('app_id','Firebase') }}",
                };

                const authToken = "{{ session('firebase_auth_token') }}";
                const firebasePrefix = "{{ env('APP_ENV') }}";
            </script>
        @endif

        @if(in_array(Route::currentRouteName(),['conversation','hotel_details','manage_listing']))
            <script type="text/javascript">
                const APP_TIMEZONE = '{{ config('app.timezone') }}';
                const CURRENT_TIMEZONE = moment.tz.guess(true);
            </script>
        @endif
        @if((Str::contains(Route::currentRouteName(),'search')) && (credentials('is_enabled' ,'googleMap') == 0))
            <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>
        @endif

        <!-- Show Popup Based on Popup code -->
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                if(popup_code == 1) {
                    openModal('signupEmailModal');
                }
                else if(popup_code == 2) {
                    openModal('loginModal');
                }
                else if(popup_code == 3) {
                    openModal('headerSearchModal');
                }
                else if(popup_code == 4) {
                    openModal('importCalendarModal');
                }
            });
        </script>

        <script type="text/javascript">
        /* <![CDATA[ */
        function doGTranslate(lang_pair) {if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];var plang=location.pathname.split('/')[1];if(plang.length !=2 && plang.toLowerCase() != 'zh-cn' && plang.toLowerCase() != 'zh-tw')plang='en';if(lang == 'en')location.href=location.protocol+'//'+location.host+location.pathname.replace('/'+plang+'/', '/')+location.search;else location.href=location.protocol+'//'+location.host+'/'+lang+location.pathname.replace('/'+plang+'/', '/')+location.search;}
        /* ]]> */
        </script>

        @if((Auth::guest() || (Route::currentRouteName() == 'update_account_settings' && request()->page == 'login-and-security')) && !isSecure())
            <script type="text/javascript">
                $(document).on('click','.fb-login-btn',function() {
                    $('.modal').modal('hide');
                    var content = {};
                    content.message = "{!! Lang::get('messages.facebook_https_error') !!}";
                    content.title = "{!! Lang::get('messages.failed') !!}";
                    flashMessage(content,'danger');
                });
                $(document).on('click','.g-signin',function() {
                    $('.modal').modal('hide');
                    var content = {};
                    content.message = "{!! Lang::get('messages.google_https_error') !!}";
                    content.title = "{!! Lang::get('messages.failed') !!}";
                    flashMessage(content,'danger');
                });
                $(document).on('click','.apple-signin',function() {
                    $('.modal').modal('hide');
                    var content = {};
                    content.message = "{!! Lang::get('messages.apple_https_error') !!}";
                    content.title = "{!! Lang::get('messages.failed') !!}";
                    flashMessage(content,'danger');
                });
            </script>
        @endif

        @if(Session::has('message'))
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded',function() {
                var content = {};
                content.message = "{!! Session::get('message') !!}";
                content.title = "{!! Session::get('title') !!}";
                state = "{!! Session::get('state') !!}";

                flashMessage(content,state);
            });
        </script>
        @endif
        @stack('scripts')
        {!! global_settings('foot_code') !!}
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