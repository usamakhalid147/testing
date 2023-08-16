<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0" style="border-collapse: collapse;">
	<tbody>
		<tr>
			<td style="padding: 0;border-bottom: 1px solid #d2d2d2;background: white;">
				<img alt="{{ $hotel_data['hotel_name'] }}" src="{{ $hotel_data['hotel_thumb'] }}" style="outline:none;text-decoration:none;    width: 100%;clear:both;display:block;border:none;height:400px;object-fit: cover;">
			</td>
		</tr>
		<tr>
			<td style="padding: 0;margin:0;border-bottom: 1px solid #d2d2d2;background-color: white;">
				<table>
					<tr>
						<td width="100%" style="padding: 16px;">
							<p style="margin-bottom:0;font-weight: 600;"> {{ $hotel_data['hotel_name'] }} </p>
							<p style="margin-bottom:0;color: #7c7c7c;"> {{ $hotel_data['room_type_name'] }} </p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>