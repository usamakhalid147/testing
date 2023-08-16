@extends('emails.template')
@section('content')
<style type="text/css">
	body{
		font-family: Open Sans, sans-serif;
	}
	.tl-lg-pdf {
		font-size: 24px;
		font-weight: 700;
		color: #000;
	}
	.title-pdf {
		font-size: 16px;
		font-weight: 700;
		color: #989898;
		margin-bottom: 10px;
		font-family: Open Sans, sans-serif;
	}
	.para-pdf {
		font-size: 16px;
		font-weight: 500;
		color: #000;
		margin-bottom: 10px;
	}
	.text-white{
		color: white !important;
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
		font-size: 21px !important;
		color: black !important;
		font-weight: 600 !important;
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

	.total{
		font-size: 18px !important;
		color: black !important;
		font-weight: 600 !important;
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
	.w-5{
		width: 5%;
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
	.w-30{
		width: 30%;
	}
	.w-70{
		width: 70%;
	}
	.ps-0{
		padding-left: 0 !important;
	}
	.m-0{
		margin: 0 !important;
	}
	.patient-details,.patient-details th,.patient-details td,.test-detail,.test-detail th,.test-detail td {
		border: 1px solid black;
		border-collapse: collapse;
		padding: 0 4px;
	}
	.test-detail td{
		padding: 6px;
	}
	.p-4{
		padding: 14px 25px;
	}
	.tab-th{
		background-color: #3980b5;
		padding: 6px;
		text-align: left;
	}
</style>
	<table style="width:100%;margin:0 auto;background-color:#fff;">
		<tbody>
			<tr>
				<td>
					<table style="width: 100%;">
						<tr>
							<td style="width:20%;vertical-align:top">
								<img style="width: 90%;height: 126px;" src="{{ $host_user->profile_picture_src }}" alt="mini-doctor">
							</td>
							<td style="width:80%;padding:5px 15px;vertical-align:top;border-radius: 6px;    border: 1px solid #215780;">
								<p style="padding:0;margin: 3px 10px;font-size: 13px;font-weight: 400;" class="mb-4">
									<span class="para-pdf">
										{{ $host_user->first_name." ".$host_user->last_name }}
									</span> 
								</p>
{{--								<p style="padding:0;margin: 3px 10px;font-size: 13px;font-weight: 400;" class="mb-4">
									<span class="para-pdf">
										{{ $result->hotel->name }}
									</span>
								</p>--}}
								<p style="padding:0;margin: 3px 10px;font-size: 13px;font-weight: 400;" class="mb-4">
									<span class="para-pdf">
										{{ $host_user->phone_number }}
									</span>
								</p>
								<p style="padding:0;margin: 3px 10px;font-size: 13px;font-weight: 400;" >
									<span class="para-pdf">
										{{ $host_user->email }}
									</span>
								</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 60px 0 20px;">
					<p class="mb-0 tl-lg-pdf text-center">Guest Report</p>
				</td>
			</tr>
			<tr>
				<td colspan="">
					<table style="width: 100%;" class="patient-details">
						<tbody>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Name</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										<span>
											{{ $user->first_name." ".$user->last_name }}
										</span>
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Phone</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $user->phone_number }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Email</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $user->email }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Hotel Name</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $result->hotel->name }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Hotel Address</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $result->hotel->hotel_address->address_line_display }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Booking Date</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ getDateInFormat($result->checkin)."-".getDateInFormat($result->checkout) }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Number Of Adult</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $result->room_reservations->sum('adults') }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Number Of Children</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $result->room_reservations->sum('children') }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Cancelation Policy</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $result->hotel->cancellation_policy }}
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Status</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										{{ $result->status }}
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 60px 0 20px;">
					<p class="mb-0 tl-lg-pdf text-center">Pricing Details</p>
				</td>
			</tr>
			<tr>
				<td colspan="">
					<table style="width: 100%;" class="patient-details">
						<tbody>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Service Fee</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										<span>
											{{ $result->currency_symbol." ".$result->service_fee }}
										</span>
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Host Fee</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										<span>
											{{ $result->currency_symbol." ".$result->host_fee }}
										</span>
									</p>
								</td>
							</tr>
							<tr>
								<td class="w-30 ">
									<p class="m-0 para-pdf">Total</p>
								</td>
								<td class="w-70 ">
									<p class="m-0 para-pdf">
										<span>
											{{ $result->currency_symbol." ".$result->getTotalAdminAmount() }}
										</span>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
@endsection