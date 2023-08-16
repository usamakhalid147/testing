@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<h1 class="h1">A payment method was added to your account</h1>
			<p style="line-height: 25px;">The payment method below was added to your account on Sat, Feb3, 2018, 6:02 AM.</p>
		</td>
	</tr>
	<tr>
		<td>
			<table role="presentation" border="0" cellpadding="16" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="left" width="50%">
							<table role="presentation" border="0" cellpadding="0" width="auto" cellspacing="0">
								<tbody>
									<tr>
										<td>
											<p style="line-height: 25px;margin: 0;">Payment Method</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td align="right" width="50%">
							<table role="presentation" border="0" cellpadding="0" width="auto" cellspacing="0">
								<tbody>
									<tr>
										<td>
											<p style="line-height: 25px;margin: 0;">VISA xxxxxxxxxxxxOOOO</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<h1 style="font-size: 21px;line-height: 36px;color: #545454;">Updated by Mac</h1>
							<p style="margin-top:8px;margin-bottom:8px">MD, Spain</p>
							<p style="margin-top:8px;margin-bottom:8px">Chrome</p>
							<h1 style="font-size: 21px;line-height: 36px;color: #545454;">Don't recognize this?</h1>
							<p style="line-height: 26px;"><span> <a href="#" style="text-decoration: none;color: #008276;">Let us know </a> </span>—we'll help secure and review your account. Otherwise,no action is required. </p>
							<h1 style="font-size: 21px;line-height: 36px;color: #545454;">Why we send you these emails</h1>
							<p style="margin-top:8px;margin-bottom:8px;line-height: 26px;">Staying informed about changes to your account is one of the best ways to keep it secure. You might see this email again when you update your info, sign in for the first time on a new computer, phone or browser, or clear your cookies.</p>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
@endsection