@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p> @lang('messages.hi_user',['replace_key_1' => $name]), </p>
			<p>
				@lang('messages.thanks_for_hotel',['replace_key_1' => $site_name])! @lang('messages.your_space') <a class="link" href="{{ $hotel_link }}"> {{ $hotel_name }}</a>  
				@lang('messages.sent_to_admin_for_approval',['replace_key_1' => $site_name]).
			</p>			
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection