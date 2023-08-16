@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding: 16px;padding-top: 0px;">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td style="padding:16px;">
							<p> @lang('messages.hi_user',['replace_key_1' => $user_data['user_name']]),</p>
							<p>
								@lang('messages.leave_review_for',['replace_key_1' => $host_data['user_name']]).@lang('messages.you_each_have_days_to_complete_review',['replace_key_1' => MAX_REVIEW_DAYS]).
								@lang('messages.you_can_read_review_after_you_write')
							</p>
						</td>
					</tr>
					<tr>
						<td style="padding: 16px;">
							<a href="{{ $review_url }}" target="_blank" class="theme_back" style="width: 100%;background-color: #008276;border: solid 1px #008276;border-radius: 5px;box-sizing: border-box;color: white;display: inline-block;font-weight: bold;padding: 12px 25px;text-decoration: none;text-align: center;"> @lang('messages.leave_a_review') </a>
						</td>
					</tr>
				</tbody>
			</table>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection