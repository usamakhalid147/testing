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
							<hr>
						</td>
					</tr>
					<tr>
						<td align="center" width="100%">
							@include('emails.common.user_profile',['user_type' => "Guest",'user_data' => $user_data])
						</td>
					</tr>
					<tr>
						<td align="center" width="100%">
							@include('emails.common.user_profile',['user_type' => "Host",'user_data' => $host_data])
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
@endsection