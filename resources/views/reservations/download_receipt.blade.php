<html>
<head>
	<style type="text/css">
		.tl-lg-pdf {
			font-size: 34px;
			font-weight: 700;
			color: #000;
		}
		.title-pdf {
			font-size: 16px;
			font-weight: 500;
			color: #383838;
			margin-bottom: 10px;
			font-family: Open Sans, sans-serif;
		}
		.bg-exact{
			-webkit-print-color-adjust: exact;
		}
		.title-xl-pdf {
			font-size: 20px;
			font-weight: 500;
			color: #383838;
			margin-bottom: 10px;
			font-family: Open Sans, sans-serif;
		}
		.para-pdf {
			font-size: 15px;
			font-weight: 400;
			color: #7c7c7c;
			margin-bottom: 10px;
		}
		.td-grey {
			font-size: 15px;
			font-weight: 400;
			color: #514F4F;
			margin-bottom: 10px;
		}
		.d-block{
			display: block;
		}
		.total {
			font-size: 18px !important;
			color: #395B64 !important;
			font-weight: 600 !important;
		}
		.border{
			border: 1px solid #dbdbdb !important;
		}
		.border-bottom{
			border-bottom: 1px solid #dbdbdb !important;
		}
		.border-top{
			border-top: 1px solid #dbdbdb !important;
		}
		.fee-table th,.fee-table td{
			padding: 0.75rem;
			vertical-align: top;
		}
		.fee-table th{
			border: 0;
			background: #F6F6F6;
		}
		.text-left{
			text-align: left;
		}
		.text-right{
			text-align: right;
		}
		.text-center{
			text-align: center;
		}
		.ms-auto{
			margin-left: auto;
		}
		.border-0{
			border: unset !important;
		}
		.w-15 {
		    width: 15%;
		}
		.w-50{
			width:50%
		}
		.w-100{
			width: 100%;
		}
		.w-40{
			width: 40%;
		}
		.w-20{
			width: 20%;
		}
		.w-10{
			width: 10%;
		}
		.ps-0{
			padding-left: 0 !important;
		}
	</style>
</head>

<body style="font-family: Open Sans, sans-serif;font-size:100%;font-weight:400;line-height:1.4;color:#000;height: auto;width: auto;">
	<table style="width:100%;margin:0 auto;background-color:#fff;">
		<thead>
			<tr>
				<td colspan="2">
					<table style="padding: 24px 24px 0 24px;width: 100%;">
						<tr>
							<th style="text-align:center;">
								<p class="tl-lg-pdf">@lang('messages.confirmation_code')</p>
								<span class="title-pdf">
									{{$reservation->code}}
								</span>
								<h4 class="text-gray">@lang('admin_messages.booking_date'):								
									{{getDateInFormat($reservation->created_at)}}
								</h4>
								
							</th>
						</tr>
					</table>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3">
					<table class="" style="width: 100%;margin-top: 30px;">
						<tr>
							<td style="width:80%;vertical-align:top" class="text-right">
								<p style="margin:0 0 10px 0;padding:0;">
									<span class="title-pdf">
										@lang('messages.hotel_id'):
										{{$reservation->hotel->id}}
									</span> 
								</p>
							</td>
						</tr>
						<tr>
							<td style="width:80%;vertical-align:top" class="text-right">
								<p style="margin:0 0 10px 0;padding:0;">
									<span class="title-pdf">
										@lang('messages.property'):
										{{$reservation->hotel->name}}
									</span> 
								</p>
							</td>
						</tr>
						<tr>
							<td style="width:80%;vertical-align:top" class="text-right">
								<p style="margin:0 0 10px 0;padding:0;">
									<span class="title-pdf">
										@lang('messages.address'):
										{{$reservation->hotel->hotel_address->city}}
									</span> 
								</p>
							</td>
						</tr>
						<tr>
							<td style="width:80%;vertical-align:top" class="text-right">
								<p style="margin:0 0 10px 0;padding:0;">
									<span class="title-pdf">
										@lang('messages.tele_phone_number'):
										{{$reservation->hotel->tele_phone_number}}
									</span> 
								</p>
							</td>
						</tr>
						<tr>
							<td style="width:80%;vertical-align:top" class="text-right">
								<p style="margin:0 0 10px 0;padding:0;">
									<span class="title-pdf">
										@lang('messages.website'):
										{{$reservation->hotel->website}}
									</span> 
								</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="height: 30px;"></td>
			</tr>
			<tr>
				<td class="w-100 text-center bg-exact" style="padding: 10px 0;background: #395B64;">
					<p style="color: white;font-size: 22px;font-weight: 600;">@lang('messages.booking_details')</p>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table style="width: 100%;padding: 30px 0;" class="">
						<tr>
							<td>
								<table class="w-100">
									<tr>
										<td style="vertical-align:middle;">
											<p>
												<span class="title-pdf d-block">
													@lang('messages.guest_id')
												</span> 
											</p>
										</td>
										<td>
											<p>
												<span class="para-pdf d-block">
													{{ $reservation->user_id }}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p>
												<span class="title-pdf d-block">
													@lang('messages.user_name')
												</span> 
											</p>
										</td>
										<td>
											<p>
												<span class="para-pdf d-block">
													{{ $reservation->user->first_name }}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p>
												<span class="title-pdf d-block">
													@lang('messages.guest_mobile_number')
												</span> 
											</p>
										</td>
										<td>
											<p>
												<span class="para-pdf d-block">
													{{ $reservation->user->phone_number }}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.email_address')
												</span> 
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ $reservation->user->email }}  
												</span>
											</p>
										</td>
									</tr>

									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.checkin_date')
												</span> 
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ $reservation->checkin }}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.checkin_time')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$reservation->checkin_at}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.checkout_date')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$reservation->checkout}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.checkout_time')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$reservation->checkout_at}}
												</span>
											</p>
										</td>
									</tr>
									@foreach($reservations as $room_reservation)
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.room_category')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->hotel_room->name}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.status')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->status}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.adults')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->adults}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.childrens')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->children}}
												</span>
											</p>
										</td>
									</tr>	
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.room_rate_per_night')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$room_reservation->total_with_discount/$reservation->total_nights}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.room_charges')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$room_reservation->total_with_discount}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.offers')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->promotion_amount}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.room_charges_with_discount')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$reservation->sub_total-$reservation->coupon_price}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.extra_charges')
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.number_of_extra_adult')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->extra_adults}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.total_extra_adults_charges')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ session('currency_symbol')}}
													{{$room_reservation->extra_adults_amount}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.number_of_extra_children')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->extra_children}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.total_extra_children_charges')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ session('currency_symbol')}}
													{{$room_reservation->extra_children_amount}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.number_of_extra_meal')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->meal_plan}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.total_meal_charges')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ session('currency_symbol')}}
													{{$room_reservation->meal_plan_amount}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.number_of_extra_bed')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{$room_reservation->extra_bed}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.total_extra_bed_charges')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ session('currency_symbol')}}
													{{$room_reservation->extra_bed_amount}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('admin_messages.total_extra_charges')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{ session('currency_symbol') }}
													{{$room_reservation->hotel_room->hotel_room_price->adult_price + $room_reservation->hotel_room->hotel_room_price->children_price + $room_reservation->meal_plan_amount + $room_reservation->extra_bed_amount}}
												</span>
											</p>
										</td>
									</tr>
									@endforeach
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.total_amount')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$reservation->total}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.payment_method')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													@lang('messages.'.$reservation->payment_method)
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.property_tax')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$reservation->property_tax}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.property_service_charge')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$reservation->service_charge}}
												</span>
											</p>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="title-pdf d-block">
													@lang('messages.service_fees')
												</span>
											</p>
										</td>
										<td style="vertical-align:middle;">
											<p class="">
												<span class="para-pdf d-block">
													{{session('currency_symbol')}}
													{{$reservation->service_fee}}
												</span>
											</p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table style="width: 100%;margin-top: 40px;">
						<tr>
							<td class="border-top" style="font-size:14px;padding-top: 40px;">
								<table>
									@if(count($cancellation_policies) > 0)
									<tr>
										<td class="w-100" style="padding-bottom: 40px;">
											<h2 class="title-xl-pdf">@lang('messages.cancellation_policy')</h2>
											@foreach($cancellation_policies as $cancellation_policy)
											 <p> @lang('messages.room_name'): <span class="fw-bold"> {{ $cancellation_policy['room_name'] }} </span></p>
													<div class="col-md-6">
													@foreach($cancellation_policy['policies'] as $policy)
														<p>{{ $policy['days'] }}
															@lang('messages.days')
															@lang('messages.before_checkin_date'):
														   {{ $policy['percentage'] }}<span class="">%</span>
														</p>
													@endforeach
												    </div>
											@endforeach
										</td>
									</tr>
									@endif
									<tr>
										<td class="title-pdf w-100">
											<h2 class="title-xl-pdf">@lang('messages.customer_support')</h2>
											<p class="para-pdf"> @lang('messages.customer_support_desc')</p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
<script type="text/javascript">
  window.print();
  window.onafterprint = window.close;	
</script>