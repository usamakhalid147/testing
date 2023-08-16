<div>
	<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0;width:100%;display:table">
		<tbody>
			<tr style="padding:0;vertical-align:top;">
				<th style="font-family:'Cereal',Helvetica,Arial,sans-serif;" >
					<h3 style="margin-bottom: 5px;" > @lang('messages.price_details') </h3>
				</th>
			</tr>
			<tr style="padding:0;vertical-align:top;">
				<td align="left">
					<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0" style="border-collapse: collapse;">
						<tbody>
							<tr>
								<td style="padding: 0;margin:0;background-color: white;">
									<table width="100%">
										@foreach($price_data['pricing_form'] as $pricing_form)
										<tr>
											<td>
												<p style="margin-bottom:0;{{ $pricing_form['key_style'] }}">
													{{ $pricing_form['key'] }}
												</p>
											</td>
											<td>
												<p style="margin-bottom:0;font-weight: 600;{{ $pricing_form['value_style'] }}">
													{{ $pricing_form['value'] }}
												</p>
											</td>
										</tr>
										@endforeach
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>