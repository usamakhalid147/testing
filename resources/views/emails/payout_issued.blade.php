@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p style="line-height: 25px;"> @lang('messages.hi_user',['replace_key_1' => $name]), </p>
			<p style="line-height: 25px;"> @lang('messages.issued_a_payout_of',['replace_key_1' => $amount]) @lang('messages.payout_arrive_to_account') </p>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table role="presentation" border="0" cellpadding="16" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="left" width="100%">
							<table role="presentation" border="0" cellpadding="0" width="auto" cellspacing="0">
								<tbody align="left">
									<tr>
										<th style="line-height: 28px;">
											@lang('messages.date')
										</th>
										<th style="line-height: 28px;">
											@lang('messages.detail')
										</th>
										<th style="line-height: 28px;">
											@lang('messages.amount')
										</th>
									</tr>
									<tr>
										<td style="line-height: 28px;">
											{{ $date }}
										</td>
										<td style="line-height: 28px;">
											{{ $detail }}
										</td>
										<td style="line-height: 28px;">
											{{ $amount }}
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
	<tr>
		<td>
			<table style="padding: 16px;">
				<tbody>
					<tr>
						<td>
							<p> @lang('messages.you_can_view_status_in_your') <span> <a href="{{ $transaction_history_link }}" style="text-decoration: none;color: #008276;"> @lang('messages.transaction_history') </a> </span> </p>
							@include('emails.common.thanks_footer')
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
@endsection