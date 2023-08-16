@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p style="line-height: 25px;"> @lang('messages.hi_user',['replace_key_1' => $name]), </p>
			<p style="line-height: 25px;"> @lang('messages.refund_issued_to_your_account',['replace_key_1' => $amount, 'replace_key_2' => $date, 'replace_key_3' => $room_type, 'replace_key_4' => $hotel_name]) @lang('messages.while_refund_is_immediate_on_our_part') </p>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection