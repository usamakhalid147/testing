@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p> @lang('messages.hi') {{ $admin_name }},</p>
			<p>
				@lang('messages.new_hotel_named_as',['hotel_name' => $hotel_name]) <a class="link" href="{{ $hotel_link }}"> {{ $hotel_name }}</a>  
				@lang('messages.waiting_for_your_approval_to_listed')
			</p>			
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection