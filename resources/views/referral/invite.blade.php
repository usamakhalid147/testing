@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
    @auth
    <div class="container py-4">
        <h1 class="fw-bold"> @lang('messages.earn_travel_credit_for_future_bookings') </h1>
        <div class="row justify-content-between">
            <div class="col-md-8">
                <p class="text-justify mb-4">
                    @lang('messages.invite_friends_who_are_not_in_site',['site_name' => $site_name])
                    @lang('messages.invite_friends_credit',['new_referral_credit' => $new_referral_credit,'user_become_guest_credit' => $user_become_guest_credit])
                </p>
                <div class="referral-form pt-3" v-show="!show_invite_details" :class="{'loading' : isLoading}">
                    <div class="d-flex">
                        <div class="form-group d-flex flex-grow-1">
                            <input type="text" name="email" class="form-control" placeholder="@lang('messages.enter_email_address')" v-model="email">
                        </div>
                        <button class="btn btn-primary mb-3 flex-shrink-0 ms-3" v-on:click="inviteUser();"> @lang('messages.send_invites') </button>
                    </div>
                    <p class="text-danger" v-show="error_messages.email"> @lang('messages.email_field_is_required') </p>
                    <div class="invite-link mt-5">
                        <h5> @lang('messages.share_your_invite_link') </h5>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="referral-link" value="{{ $referral_link }}" readonly>
                            <button type="button" class="btn btn-secondary" v-on:click="copyText('#referral-link')"> @lang('messages.copy') </button>
                        </div>
                    </div>
                </div>
                <div class="refer-earn" v-show="show_invite_details">
                    <div class="text-center">
                        <h3 class="mb-4">You have invite @{{ recruited_users.length }} new users and made @{{ available_credit }} </h3>
                    </div>
                    <div class="row g-4" v-if="recruited_users.length > 0">
                        <div class="col-md-4" v-for="user in recruited_users">
                            <div class="box-earn w-100 border">
                                <div class="d-flex justify-content-center align-items-center earn-pro w-100 bg-primary">
                                    <img :src="user.profile_picture_src">
                                </div>
                                <div class="d-flex flex-column justify-content-center p-3 text-center">
                                    <p>@{{ user.username }}</p>
                                    <div class="border-top pt-2">
                                        <p class="m-0 earn-value">@{{ user.total_earnings }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 bg-light earn-notifi mt-4" v-if="pending_recruited_users.length > 0">
                        <h3 class="text-center mb-4">@{{ pending_recruited_users.length }} friend hasn't listed their space yet</h3>
                        <div class="col-md-10 mx-auto bg-white p-3" v-for="pending_recruited_user in pending_recruited_users">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img :src="pending_recruited_user.profile_picture_src">
                                    </div>
                                    <h6 class="ms-2 mb-0">@{{ pending_recruited_user.username }}</h6>   
                                    <span class="flex-column">Pending Booking Referral Amount - @{{ pending_recruited_user.user_become_guest_amount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <img class="round-sm-img" src="{{ asset('images/savings.png') }}">
                        <h2 class="h1 my-2"> @lang('messages.your_travel_credit') </h2>
                        <div class="invite-detail">
                            <div class="d-flex justify-content-between border-bottom mb-3">
                                <p class="m-0"> @lang('messages.pending') </p>
                                <p class="m-0"> {{ $pending_credit }} </p>
                            </div>
                            <div class="d-flex justify-content-between border-bottom mb-3">
                                <p class="m-0"> @lang('messages.available') </p>
                                <p class="m-0"> {{ $available_credit }} </p>
                            </div>
                            <button class="btn btn-primary w-100" v-on:click="show_invite_details = true;" v-show="!show_invite_details"> @lang('messages.show_invite_details') </button>
                            <button class="btn btn-primary w-100" v-on:click="show_invite_details = false;" v-show="show_invite_details"> @lang('messages.close') </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="referral-container">
        <div class="referral-banner d-flex justify-content-between align-items-center">
            <div class="col-6 mx-auto">
                <h1 class="text-center text-light my-3"> @lang('messages.earn_money_to_user_site',['site_name'=>$site_name]) <br> @lang('messages.get_up_to_amount_for_every_invite',['referral_amount' => $referral_amount]) </h1>
                <div class="text-center">
                    <a href="{{ resolveRoute('login') }}" class="btn btn-primary my-4 text-center">
                        @lang('messages.login_invite_friends')
                    </a>
                </div>
                <h4 class="text-center">
                    <span class="text-white"> @lang('messages.dont_have_an_account') </span>
                    <a class="theme-link ms-2" href="{{ resolveRoute('signup') }}" data-bs-toggle="modal" data-bs-target="#signupModal">
                        @lang('messages.signup')
                    </a>
                </h4>
            </div>
        </div>
        <div class="col-12 mt-5">
            <div class="col-md-10 mx-auto">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h1 class="text-center"> @lang('messages.its_easy_to_get_started') </h1>
                    </div>
                    <p class="text-justify">
                        @lang('messages.how_to_refer_friends',['site_name' => $site_name,'new_referral_credit' => $new_referral_credit, 'user_become_guest_credit' => $user_become_guest_credit])
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endauth
</main>
@endsection