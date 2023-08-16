@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $name }},</p>
			<p>@lang('messages.sent_user_details')</p>
			<p>@lang('messages.sent_user') : {{ $user_data['user_name'] }}</p>
			<p>@lang('messages.member_since') : {{ $user_data['since'] }}</p>

			<p>@lang('messages.your_room_details')</p>
			<p>@lang('messages.your_room') <a class="link" href="{{ $room_data['listing_link'] }}"> {{ $room_data['listing_name'] }} </a>
				@lang('messages.was_listed_on',['replace_key_1' => $site_name]).</p>
			<p>@lang('messages.address_line') : {{ $room_data['address_line'] }} </p>

			<p>@lang('messages.message') : {{ $message_data['message'] }}</p>
			
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection