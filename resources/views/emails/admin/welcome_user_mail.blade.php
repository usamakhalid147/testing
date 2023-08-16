@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $admin_name }},</p>
			<p>@lang('messages.welcome_to') {{ $site_name}}!.</p>
			<p> {{ $site_name}} {{ $text }} </p>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection