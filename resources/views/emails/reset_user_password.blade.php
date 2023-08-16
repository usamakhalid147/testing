@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $name }},</p>
			<p> @lang('messages.received_request_to_reset_password') </p>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" width="100%">
				<tbody>
					<tr>
						<td align="center">
							<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
								<tbody>
									<tr>
										<td>
											<a href="{{ $reset_link }}"> @lang('messages.click_to_reset_password') </a>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection