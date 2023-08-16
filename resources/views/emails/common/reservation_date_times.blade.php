<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0" style="border-collapse: collapse;">
	<tbody>
		<tr>
			<td style="border-bottom: 1px solid #d2d2d2;">
				<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0">
					<tbody>
						<tr>
							@if($reservation_data['list_type'] != 'room')
							<td><p style="margin-bottom:0;font-weight: 600;"> @lang('messages.reserve_date') </p></td>
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;"> {{ $reservation_data['checkin'] }} ({{ $reservation_data['start_time'] }}) </p>
							</td>
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;"> {{ $reservation_data['checkout'] }} ({{ $reservation_data['end_time'] }}) </p>
							</td>							
							@else
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;font-weight: 600;"> @lang('messages.checkin') </p>
								<p style="margin-bottom:0;"> {{ $reservation_data['checkin'] }} ({{ $reservation_data['checkin_at'] }}) </p>
							</td>
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;font-weight: 600;"> @lang('messages.checkout') </p>
								<p style="margin-bottom:0;"> {{ $reservation_data['checkout'] }} ({{ $reservation_data['checkout_at'] }}) </p>
							</td>
							@endif
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0">
					<tbody>
						<tr>
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;font-weight: 600;"> @lang('messages.guests') </p>
								<p style="margin-bottom:0;"> {{ $reservation_data['guests'] }} </p>
							</td>
							@if($reservation_data['status'] == 'Accepted')
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;font-weight: 600;"> @lang('messages.reservation_code') </p>
								<p style="margin-bottom:0;"> {{ $reservation_data['code'] }} </p>
							</td>
							@else
							<td style="padding: 16px;" width="50%">
								<p style="margin-bottom:0;font-weight: 600;"> @lang('messages.room_type') </p>
								<p style="margin-bottom:0;"> {{ $reservation_data['room_type'] }} </p>
							</td>
							@endif
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>