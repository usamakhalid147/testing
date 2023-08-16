@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<h1 class="h1"> @lang('messages.did_you_change_your_password') </h1>
			<p style="line-height: 25px;"> @lang('messages.we_notice_password_changed',['replace_key_1' => $site_name]) </p>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="left">
							<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0" style="border-collapse: collapse;border: 1px solid #d2d2d2;">
								<tbody>
									<tr>
										<td style="padding: 20px;border-bottom: 1px solid #d2d2d2;">
											<h1 style="margin: 0;font-size: 22px;"> @lang('messages.password_changed')</h1>
										</td>
									</tr>
									<tr>
										<td style="padding: 20px;border-bottom: 1px solid #d2d2d2;">
											<p> <span style="font-weight: 600;"> @lang('messages.when') </span> {{ $when }} </p>
											@if($where != '')
											<p> <span style="font-weight: 600;"> @lang('messages.where') </span> {{ $where }} </p>
											@endif
											@if($device != '')
											<p> <span style="font-weight: 600;"> @lang('messages.device') </span> {{ $device }} </p>
											@endif
										</td>
									</tr>
									<tr>
										<td style="padding: 20px;text-align: center;" class="btn">
											<a href="{{ $review_link }}" style="background-color: #008276;color: #FFFFFF;width: 100%;">
												@lang('messages.review_my_account')
											</a>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
@endsection