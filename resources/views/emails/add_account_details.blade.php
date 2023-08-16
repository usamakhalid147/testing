@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<h1 class="h1"> @lang('messages.add_account_details_to_keep_payouts') </h1>
			<p> @lang('messages.hi_user',['replace_key_1' => $name]), </p>
			<p style="line-height: 25px;"> @lang('messages.its_great_to_earning',['replace_key_1' => $site_name]) </p>
			<p style="line-height: 25px;"> @lang('messages.this_info_is_required_for_coming_payout')
			 @lang('messages.this_take_only_few_minutes'). </p>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
				<tbody>
					<tr>
						<td align="left">
							<table role="presentation" border="0" cellpadding="0" width="auto" cellspacing="0">
								<tbody>
									<tr>
										<td> <a href="{{ $add_account_link }}"> @lang('messages.add_account_details') </a> </td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection