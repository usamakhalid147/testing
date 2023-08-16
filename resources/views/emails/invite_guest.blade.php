@extends('emails.template')
@section('content')
<h1>
	<b> 
		@lang('messages.user_sent_to_invite',['site_name' => $site_name,'user' => $user_data['user_name'],'new_referral_credit' => $new_referral_credit])
	</b>
</h1>
<h5>
	@lang('messages.user_sent_to_invite',['site_name' => $site_name,'today' => getDateInFormat(date('Y-m-d')),'user' => $user_data['user_name'],'new_referral_credit' => $new_referral_credit])
</h5>
<a href="{{ $referral_link }}" class="btn btn-primary"> @lang('messages.accept_invitation')</a>
<div class="col-3 col-md-2 col-lg-1">
	<a href="{{ $view_profile }}"><img class="rounded-profile-image-normal" title="{{ $user_data['user_name'] }}" src="{{ $user_data['user_profile_pic'] }}"></a>
</div>
<div class="col-9 col-md-10 col-lg-11 p-0 d-md-flex mt-1">
	<div class="ps-md-0 ps-lg-2 col-12 col-md-3">
		<h5 class="user-name">{{ $user_data['user_name'] }}</h5>
		<p class="text-truncate">												@lang('messages.user_sent_to_invite',['site_name' => $site_name,'today' => getDateInFormat(date('Y-m-d')),'user' => $user_data['user_name'],'new_referral_credit' => $new_referral_credit])
		</p>
	</div>
</div>
@endsection