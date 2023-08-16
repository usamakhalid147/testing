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
							<a href="{{ $reservation_data['receipt_link'] }}" target="_blank" class="theme_back" style="width: 100%;background-color: #ec8d37;border: solid 1px #ec8d37;border-radius: 5px;box-sizing: border-box;color: white;display: inline-block;font-weight: bold;padding: 12px 25px;text-decoration: none;text-align: center;"> @lang('messages.view_receipt') </a>
						</td>
					</tr>
					<tr>
						<td align="center" width="100%">
							@include('emails.common.user_profile',['user_type' => "Guest",'user_data' => $user_data])
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
													<a href="{{ $help_link }}" style="color: #ec8d37;text-decoration: none;"> @lang('messages.view_help_center') </a>
												</span>
												•
												<span style="padding: 0 8px;">
													<a href="{{ $contact_link }}" style="color: #ec8d37;text-decoration: none;"> @lang('messages.contact') {{ $site_name }} </a>
												</span>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<div style="padding:10px 0">
								<hr>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div style="padding:0">
								@include('emails.common.price_details',['price_data' => $price_data])
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
@endsection