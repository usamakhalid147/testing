@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $name }},</p>
			<p>
				@lang('messages.information_updated_on',['replace_key_1' => $field])
				<a class="link" href="{{ $hotel_link }}"> {{ $hotel_name }} </a>
				@lang('messages.at') {{ $date }}.
			</p>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection