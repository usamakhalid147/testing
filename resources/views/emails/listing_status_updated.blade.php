@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $name }},</p>
			@if($status == "listed")
			<p>
				@lang('messages.congratulations')! @lang('messages.your_room') <a class="link" href="{{ $listing_link }}"> {{ $listing_name }} </a>
				@lang('messages.was_listed_on',['replace_key_1' => $site_name]) {{ $date }}.
			</p>
			<p>
				@lang('messages.if_you_are_not_ready')
				<a class="link" href="{{ $hotel_edit_link }}"> @lang('messages.manage_hotel') </a>
				@lang('messages.to_unlist')
			</p>
			@else
			<p> 
			@lang('messages.your_hotel') <a class="link" href="{{ $hotel_link }}"> {{ $hotel_name }} </a>
				@lang('messages.hotel_deactivated',['replace_key_1' => $site_name]) {{ $date }}.
			</p>
			<p>
				@lang('messages.go_to')
				<a class="link" href="{{ $hotel_edit_link }}"> @lang('messages.manage_hotel') </a>
				@lang('messages.to_list_it_again')
			</p>
			@endif
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection