@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $name }},</p>
			<p> {!! $content !!} </p>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection