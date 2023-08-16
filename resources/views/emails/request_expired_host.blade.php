@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding: 16px;">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="left">
							@include('emails.common.hotel_detail',['hotel_data' => $hotel_data])
						</td>
					</tr>
					<tr>
						<td align="left">
							@include('emails.common.reservation_date_times',['reservation_data' => $reservation_data])
						</td>
					</tr>
					<tr>
						<td style="padding: 16px;">
							<a href="{{ $reservation_data['inbox_link'] }}" target="_blank" class="theme_back" style="width: 100%;background-color: #FFFFFF;border: solid 1px #008276;border-radius: 5px;box-sizing: border-box;color: #008276;display: inline-block;font-weight: bold;padding: 12px 25px;text-decoration: none;text-align: center;"> @lang('messages.message_history') </a>
						</td>
					</tr>
					<tr>
						<td align="center" width="100%">
							@include('emails.common.user_profile',['user_type' => "Host",'user_data' => $host_data])
						</td>
					</tr>
					<tr>
						<td align="center" width="100%">
							<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0">
								<tbody>
									<tr>
										<td align="center" style="padding: 16px 0;border-top: 1px solid #dbdbdb;">
											<h1 style="font-size: 25px;line-height: 36px;margin: 5px 0;"> @lang('messages.customer_support') </h1>
											<p style="margin-top: 0;">
												<span style="padding: 0 8px;">
													<a href="{{ $help_link }}" style="color: #008276;text-decoration: none;"> @lang('messages.view_help_center') </a>
												</span>
												•
												<span style="padding: 0 8px;">
													<a href="{{ $contact_link }}" style="color: #008276;text-decoration: none;"> @lang('messages.contact') {{ $site_name }} </a>
												</span>
											</p>
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