@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>@lang('messages.hi_user',['replace_key_1' => $name]),</p>
			<p>
				@lang('messages.congratulations')! @lang('messages.your_hotel') <a class="link" href="{{ $hotel_link }}"> {{ $hotel_name }} </a>
				@lang('messages.was_approved_by_admin',['replace_key_1' => $site_name]).
			</p>
			<p>
				@lang('messages.if_you_are_not_ready')
				<a class="link" href="{{ $hotel_edit_link }}"> @lang('messages.manage_hotel') </a>
				@lang('messages.to_unlist')
			</p>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection