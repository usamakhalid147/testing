@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding: 16px;padding-top: 0px;">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td align="center" width="100%">
							@include('emails.common.user_profile',['user_type' => "Host",'user_data' => $user_data])
						</td>
					</tr>
					<tr>
						<td align="center" width="100%">
							<h2 style="color: #000000;margin-bottom: 50px;"> @lang('messages.here_what_user_wrote',['replace_key_1' => $user_data['user_name']]) </h2>
						</td>
					</tr>
					<tr>
						<td align="center" width="100%" style="border: solid 1px;padding: 30px 10px;">
							"{{ $review['public_comment'] }}"
						</td>
					</tr>
					<tr>
						<td width="100%">
							<p style="margin: 50px 0px;"> @lang('messages.users_private_feedback_for_you',['replace_key_1' => $user_data['user_name']]) </p>
						</td>
					</tr>
					<tr>
						<td align="center" width="100%" style="border: solid 1px;padding: 30px 10px;">
							"{{ $review['private_comment'] }}"
						</td>
					</tr>
					<tr>
						<td style="padding:16px;">
							<p>
								@lang('messages.now_both_written_reviews_posted_them_to_profiles',['replace_key_1' => $site_name])
							</p>
						</td>
					</tr>
					<!-- <tr>
						<td style="padding: 16px;">
							<a href="{{ $inbox_url }}" target="_blank" class="theme_back" style="width: 100%;background-color: #008276;border: solid 1px #008276;border-radius: 5px;box-sizing: border-box;color: white;display: inline-block;font-weight: bold;padding: 12px 25px;text-decoration: none;text-align: center;"> @lang('messages.write_a_response') </a>
						</td>
					</tr> -->
				</tbody>
			</table>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection