<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding:16px;">
			<p> @lang('messages.hi') {{ $admin_name }},</p>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="left">
							<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0" style="border-collapse: collapse;border: 1px solid #d2d2d2;">
								<tbody>
									<tr>
										<td style="padding: 20px;border-bottom: 1px solid #d2d2d2;">
											<h1 style="margin: 0;font-size: 22px;"> {{ $subject }} </h1>
										</td>
									</tr>
									<tr>
										<td style="padding: 20px;border-bottom: 1px solid #d2d2d2;">
											<p> <span style="font-weight: 600;"> @lang('messages.name') </span> {{ $name }} </p>
											<p> <span style="font-weight: 600;"> @lang('messages.email') </span> {{ $email }} </p>
											<p> <span style="font-weight: 600;"> @lang('messages.feedback') </span> {{ $feedback }} </p>
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