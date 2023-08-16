<div class="row">
	<div class="col-md-12 mt-4 px-0">
		<div class="container transaction_history_section">
			<div class="justify-content-md-center">
				<p class="text-black d-none" v-show="transaction_history.filter == 'completed'"> @lang('messages.total_transaction') : <span> @{{ transaction_history.summary_amount }} </span> </p>
				<div class="col-md-12 px-0">
					<div class="card">
						<div class="card-body" :class="{'loading': isLoading}">
							<div v-if="transaction_history.data.length == 0">
								<p class="fw-bold"> @lang('messages.we_are_unable_to_process_your_data') </p>
							</div>
							<div class="transaction-history" v-else>
								<div class="col-12 p-0 table-responsive">
									<table id="transaction_history" class="table table-hover mb-4">
										<thead>
											<tr>
												<th> @lang('messages.property_id') </th>
												<th> @lang('messages.property_name') </th>
												<th> @lang('Property Star Rating') </th>
												<th> @lang('Property Type') </th>
												<th> @lang('Property Telephone Number') </th>
												<th> @lang('Property Ext Number') </th>
												<th> @lang('Property Fax Number') </th>
												<th> @lang('Property Address') </th>
												<th> @lang('Ward') </th>
												<th> @lang('Town') </th>
												<th> @lang('City') </th>
												<th> @lang('Country') </th>
												<th> @lang('Postal Code') </th>
												<th> @lang('Property Website') </th>
												<th> @lang('Property Email') </th>
												<th> @lang('Booking Confirmation Code') </th>
												<th> @lang('Booking Made On') </th>
												<th> @lang('Room Category') </th>
												<th> @lang('Check In Date') </th>
												<th> @lang('Check In Time') </th>
												<th> @lang('Check Out Date') </th>
												<th> @lang('Check Out Time') </th>
												<th> @lang('Adults') </th>
												<th> @lang('Children’s') </th>
												<th> @lang('Room Rate Per Night') </th>
												<th> @lang('Total Room Night') </th>
												<th> @lang('Total Room Charges') </th>
												<th> @lang('Number of Extra Adult') </th>
												<th> @lang('Total Extra Adult Charges') </th>
												<th> @lang('Number of Extra Children') </th>
												<th> @lang('Total Extra Children Charges') </th>
												<th> @lang('Total Meal Charges') </th>
												<th> @lang('Number of Extra Bed') </th>
												<th> @lang('Total Extra Bed Charges') </th>
												<th> @lang('Total Extra Charges') </th>
												<th> @lang('Discount Amount') </th>
												<th> @lang('Payment Method') </th>
												<th> @lang('Total Amount') </th>
												<th> @lang('Property Tax') </th>
												<th> @lang('Property Service Charge') </th>
												<th> @lang('DU HI VIET Service Fees') </th>
												<th> @lang('Grand Total') </th>
												<th> @lang('Property Policy') </th>
												<th> @lang('Status') </th>
											</tr>
										</thead>
										<tbody>
											<tr v-for="reservation in transaction_history.data">
												<td> @{{ reservation.property_id }} </td>
												<td> @{{ reservation.property_name }} </td>
												<td> @{{ reservation.property_star_rating }} </td>
												<td> @{{ reservation.property_type }} </td>
												<td> @{{ reservation.property_telephone_number }} </td>
												<td> @{{ reservation.property_ext_number }} </td>
												<td> @{{ reservation.property_fax_number }} </td>
												<td> @{{ reservation.property_address }} </td>
												<td> @{{ reservation.ward }} </td>
												<td> @{{ reservation.city }} </td>
												<td> @{{ reservation.state }} </td>
												<td> @{{ reservation.country }} </td>
												<td> @{{ reservation.postal_code }} </td>
												<td> @{{ reservation.property_website }} </td>
												<td> @{{ reservation.property_email }} </td>
												<td> @{{ reservation.booking_confirmation_code }} </td>
												<td> @{{ reservation.booking_made_on }} </td>
												<td> @{{ reservation.room_category }} </td>
												<td> @{{ reservation.check_in_date }} </td>
												<td> @{{ reservation.check_in_time }} </td>
												<td> @{{ reservation.check_out_date }} </td>
												<td> @{{ reservation.check_out_time }} </td>
												<td> @{{ reservation.adults }} </td>
												<td> @{{ reservation.children }} </td>
												<td> @{{ reservation.room_rate_per_night }} </td>
												<td> @{{ reservation.total_room_nights }} </td>
												<td> @{{ reservation.total_room_charges }} </td>
												<td> @{{ reservation.number_of_extra_adults }} </td>
												<td> @{{ reservation.total_extra_adult_charges }} </td>
												<td> @{{ reservation.number_of_extra_children }} </td>
												<td> @{{ reservation.total_extra_children_charges }} </td>
												<td> @{{ reservation.meal_plan }} </td>
												<td> @{{ reservation.number_of_extra_beds }} </td>
												<td> @{{ reservation.total_extra_beds_charges }} </td>
												<td> @{{ reservation.total_extra_charges }} </td>
												<td> @{{ reservation.discount_amount }} </td>
												<td> @{{ reservation.payment_method }} </td>
												<td> @{{ reservation.total_amount }} </td>
												<td> @{{ reservation.property_tax }} </td>
												<td> @{{ reservation.property_service_charge }} </td>
												<td> @{{ reservation.duhiviet_service_fee }} </td>
												<td> @{{ reservation.grand_total }} </td>
												<td> @{{ reservation.property_policy }} </td>
												<td> @{{ reservation.status }} </td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@push('scripts')
	<link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables/datatables.min.css') }}">
	<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/datatables.min.js') }}"></script>
@endpush