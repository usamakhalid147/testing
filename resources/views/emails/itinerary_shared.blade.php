@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding: 16px;">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="left">
							@include('emails.common.listing_detail',['room_data' => $list_data])
						</td>
					</tr>
					<tr>
						<td align="left">
							<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0" style="border-collapse: collapse;">
								<tbody>
									<tr>
										<td style="padding: 0;margin:0;border-bottom: 1px solid #d2d2d2;background-color: white;">
											<table>
												<tr>
													<td width="100%" style="padding: 16px;">
														<p style="margin-bottom:0;font-weight: 600;"> @lang('messages.address') </p>
														<p style="color: #7c7c7c;"> {{ $list_data['address_line'] }} </p>
														<a href="http://maps.google.com/maps?daddr={{ $list_data['latitude'] }},{{ $list_data['longitude'] }}" style="color: #ec8d37;text-decoration: none;"> @lang('messages.get_directions') </a>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td align="left">
							@include('emails.common.reservation_date_times',['reservation_data' => $reservation_data])
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