@extends('emails.template')
@section('content')
<h1>
	<b> 
		@lang('messages.earning_amount',['site_name' => $site_name,'available_credit' => $available_credit])
	</b>
</h1>
<h6>
	Hi, {{ $user_data['user_name'] }}
</h6>
<div>@lang('messages.earning_amount_text')</div>
@endsection