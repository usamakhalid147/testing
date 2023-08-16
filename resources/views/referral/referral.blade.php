@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
    <div class="referral-container">
        <div class="referral-banner d-flex justify-content-between align-items-center">
            <div class="col-6 mx-auto">
            	<div class="text-center referral-profile mx-auto">
                    <img src="{{ $user->profile_picture_src }}">
                </div>
                <h1 class="text-center text-light my-3"> @lang('messages.user_gave_to_travel',['site_name' => $site_name,'user' => $user->first_name,'new_referral_credit' => $new_referral_credit]) </h1>
                <div class="text-center">
                    <a href="javascript:;" class="btn btn-primary my-4 text-center" data-bs-toggle="modal" data-bs-target="#signupEmailModal">
                        @lang('messages.signup_to_claim')
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 mt-5">
            <div class="col-md-10 mx-auto">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h1 class="text-center"> @lang('messages.how_it_works') </h1>
                    </div>
                    <p class="text-justify">
                        @lang('messages.how_to_refer_friends',['site_name' => $site_name,'new_referral_credit' => $new_referral_credit, 'user_become_guest_credit' => $user_become_guest_credit])
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
    window.vueInitData = {!! json_encode([
            'countries' => $countries,
            'cities' => $city_list,
            'user' => [
                'email' => old('email', ''),
                'first_name' => old('first_name', ''),
                'last_name' => old('last_name', ''),
                'password' => old('password', ''),
                'password_confirmation' => old('password_confirmation', ''),
                'country' => old('country', ''),
                'city' => old('password', ''),
            ],
        ]);
    !!}
</script>
@endpush