@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container">
		<div class="py-4 text-wrap table-responsive">
			<h4 class="text-black text-header"> @lang("messages.what_is_cancel_refund_policy") </h4>
			<p class="mt-2">
				@lang("messages.why_cancel_policy")
			</p>
			<p class="mt-2">
				@lang("messages.we_developed_cancel_policy",['replace_key_1' => $site_name])
			</p>
			<p class="mt-2">
				<ul>
					<li> @lang("messages.cleaning_fees_always_paid_to_host") </li>
					<li> @lang("messages.service_fee_not_refundable") </li>
				</ul>
			</p>
			<p class="mt-2 text-header text-black h5">
				@lang('messages.flexible')
			</p>
			<table class="table table-hover">
				<tr>
					<th class="w-50"> @lang('messages.guest_cancellation_timeline') </th>
					<th> @lang('messages.guest_refund') </th>
					<th> @lang('messages.host_payout') </th>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_when_more_than_24_hours')
					</td>
					<td>
						100% <span class="text-gray">@lang('messages.minus_service_fee') </span>
					</td>
					<td>
						0%
					</td>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_when_less_than_24_hours')
					</td>
					<td>
						@lang('messages.non_refundable')
					</td>
					<td>
						100% <span class="text-gray">@lang('messages.minus_host_fee') </span>
					</td>
				</tr>
			</table>

			<p class="mt-2 text-header text-black h5">
				@lang('messages.moderate')
			</p>
			<table class="table table-hover">
				<tr>
					<th class="w-50"> @lang('messages.guest_cancellation_timeline') </th>
					<th> @lang('messages.guest_refund') </th>
					<th> @lang('messages.host_payout') </th>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_before_5_days')
					</td>
					<td>
						100% <span class="text-gray">@lang('messages.minus_service_fee') </span>
					</td>
					<td>
						0%
					</td>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_within_5_1_days')
					</td>
					<td>
						50% <span class="text-gray">@lang('messages.minus_service_fee') </span>
					</td>
					<td>
						50% <span class="text-gray">@lang('messages.minus_host_fee') </span>
					</td>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_when_less_than_24_hours')
					</td>
					<td>
						@lang('messages.non_refundable')
					</td>
					<td>
						100% <span class="text-gray">@lang('messages.minus_host_fee') </span>
					</td>
				</tr>
			</table>

			<p class="mt-2 text-header text-black h5">
				@lang('messages.strict')
			</p>
			<table class="table table-hover">
				<tr>
					<th class="w-50"> @lang('messages.guest_cancellation_timeline') </th>
					<th> @lang('messages.guest_refund') </th>
					<th> @lang('messages.host_payout') </th>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_before_14_days')
					</td>
					<td>
						100% <span class="text-gray">@lang('messages.minus_service_fee') </span>
					</td>
					<td>
						0%
					</td>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_within_7_1_days')
					</td>
					<td>
						50% <span class="text-gray">@lang('messages.minus_service_fee') </span>
					</td>
					<td>
						50% <span class="text-gray">@lang('messages.minus_host_fee') </span>
					</td>
				</tr>
				<tr>
					<td>
						@lang('messages.cancel_less_than_7_days')
					</td>
					<td>
						@lang('messages.non_refundable')
					</td>
					<td>
						100% <span class="text-gray">@lang('messages.minus_host_fee') </span>
					</td>
				</tr>
			</table>

			<h4 class="text-black text-header mt-4">
				@lang("messages.cancellation_by_hosts")
			</h4>
			<p class="mt-2">
				@lang("messages.cancellation_policy_fair_both",['replace_key_1' => $site_name]) 
				@lang("messages.cancel_before_checkin_amount_refunded")
			</p>
		</div>
	</div>
</main>
@endsection