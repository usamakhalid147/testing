@extends('emails.template')
@section('content')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding:16px;">
			<p>Hi {{ $name }},</p>
			<p>
				@lang('messages.your_listing_rejected',['resubmit_by' => $resubmit_by]) <a class="link" href="{{ $listing_link }}"> {{ $listing_name }} </a>
			</p>
			<p>
				@lang('admin_messages.resubmit_reason') : {{ $resubmit_reason }}
			</p>
			<p>
				@lang('messages.goto_manage_listings')
			</p>
			@include('emails.common.thanks_footer')
		</td>
	</tr>
</table>
@endsection