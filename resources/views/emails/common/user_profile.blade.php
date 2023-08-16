<table role="presentation" border="0" cellpadding="0" width="100%" cellspacing="0">
	<tbody>
		<tr>
			<td align="center">
				<img alt="{{ $user_data['user_name'] }}" src="{{ $user_data['user_profile_pic'] }}" style="outline:none;text-decoration:none;width:100px;clear:both;display:block;border:none;height:100px;border-radius: 50%;">
			</td>
		</tr>
		<tr>
			<td align="center" style="padding: 16px 0;">
				<h1 style="font-size: 25px;line-height: 36px;margin: 5px 0;"> {{ $user_data['user_name'] }} </h1>
				<p>
					{{ $site_name }} @lang('messages.member_since',['replace_key_1' => $user_data['since']])
				</p>
			</td>
		</tr>
	</tbody>
</table>