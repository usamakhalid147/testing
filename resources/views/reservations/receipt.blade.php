@extends('layouts.app')
@section('content')
<main role="main" id="receipt" class="main-container pt-4">
    <div class="container">
        <div class="col-md-6 mx-auto">
            <div class="card p-4 w-100">
                <!-- <div class="col-12 mb-2 text-center text-md-end d-print-none">
                    <button type="button" class="btn btn-default" onclick="printRecipt()"> @lang('messages.print')
                    </button>
                </div> -->
                <!-- start -->
                <table class="es-content" cellspacing="0" cellpadding="0" align="center"
                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                        <td align="center" style="padding:0;Margin:0">
                            <table class="es-content-body w-100"
                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;"
                                cellspacing="0" cellpadding="0" align="center">
                                <tr>
                                    <td align="left" bgcolor="#ffffff"
                                        style="padding:20px;Margin:0;background-color:#ffffff;border-radius:5px 5px 0px 0px">
                                        <table class="w-100" cellspacing="0" cellpadding="0"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td class="es-m-p0r es-m-p20b" valign="top" align="center"
                                                    style="padding:0;Margin:0;border-radius:5px;overflow:hidden;width:560px">
                                                    <table width="100%" cellspacing="0" cellpadding="0"
                                                        bgcolor="#f2fcfe"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-left:1px solid #a4cfd7;border-right:1px solid #a4cfd7;border-top:1px solid #a4cfd7;border-bottom:1px solid #a4cfd7;background-color:#f2fcfe;border-radius:5px"
                                                        role="presentation">
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0;font-size:0px">
                                                                <a target="_blank" href="https://duhiviet.com"
                                                                    style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#022B3A;font-size:14px"><img
                                                                        src="https://dbhfvn.stripocdn.email/content/guids/CABINET_7a7b94e95c36edc56ed15c8d13d4a5d3/images/nouncheckout5155691_1.png"
                                                                        alt="Đặt phòng khách sạn giá rẻ"
                                                                        style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"
                                                                        width="100"
                                                                        title="Đặt phòng khách sạn giá rẻ"></a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"
                                                                style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                                                                <h1
                                                                    style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:30px;font-style:normal;font-weight:bold;color:#022B3A">
                                                                    <b>Thank you for
                                                                        booking<br>{{ $reservation->user->first_name ? $reservation->user->first_name : ''}}
                                                                        {{$reservation->user->last_name ? $reservation->user->last_name : '' }}</b><b>!</b>
                                                                </h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"
                                                                style="Margin:0;padding-top:10px;padding-bottom:20px;padding-left:20px;padding-right:20px">
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                    The property has been informed of your booking
                                                                    request. Should you need more information relating
                                                                    to your stay at this property, you can always
                                                                    contact them with the information that <br> is
                                                                    provided below.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                        <td align="center" style="padding:0;Margin:0">
                            <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0"
                                cellspacing="0"
                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                <tr>
                                    <td align="left"
                                        style="Margin:0;padding-bottom:10px;padding-left:20px;padding-right:20px;padding-top:40px">
                                        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:270px" valign="top"><![endif]-->
                                        @if($reservation->status == 'Cancelled')
                                        <h6 class="text-center">@lang('messages.cancelled')</h6>
                                        @else
                                        <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                            <tr>
                                                <td class="es-m-p20b" align="center" valign="top"
                                                    style="padding:0;Margin:0;width:270px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="left" style="padding:0;Margin:0">
                                                                <h3
                                                                    style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:14px;font-style:normal;font-weight:bold;color:#022B3A">
                                                                    Booking made
                                                                    on:<br>{{ getDateInFormat($reservation->created_at) }}
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if mso]></td><td style="width:20px"></td><td style="width:270px" valign="top"><![endif]-->
                                        <table cellpadding="0" cellspacing="0" class="es-right" align="right"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                            <tr>
                                                <td align="left" style="padding:0;Margin:0;width:270px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="right" style="padding:0;Margin:0">
                                                                <h3
                                                                    style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:14px;font-style:normal;font-weight:bold;color:#022B3A">
                                                                    Confirmation number:<br>{{$reservation->code}}</h3>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif


                                        <!--[if mso]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding:0;Margin:0">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:600px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="center"
                                                                style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;font-size:0">
                                                                <table border="0" width="100%" height="100%"
                                                                    cellpadding="0" cellspacing="0" role="presentation"
                                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tr>
                                                                        <td
                                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="esdev-adapt-off" align="left"
                                        style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                                        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:335px" valign="top"><![endif]-->
                                        <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                            <tr>
                                                <td class="es-m-p20b" align="center" valign="top"
                                                    style="padding:0;Margin:0;width:335px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="left" style="padding:0;Margin:0">
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:18px;color:#022B3A;font-size:12px">
                                                                    <span style="font-size:14px"><strong>Property
                                                                            Detail:</strong></span><br>Property
                                                                    ID:<br>Property Name:<br>Address:<br>Ward
                                                                </p>
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:18px;color:#022B3A;font-size:12px">
                                                                    Province / State /
                                                                    City&nbsp;<br>Country:<br>Tel:<br>Website:<br>Email:
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if mso]></td><td style="width:40px"></td><td style="width:185px" valign="top"><![endif]-->
                                        <table cellpadding="0" cellspacing="0" class="es-right" align="right"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                            <tr>
                                                <td align="left" style="padding:0;Margin:0;width:185px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="right" style="padding:0;Margin:0">
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:18px;color:#022B3A;font-size:12px">
                                                                    <span
                                                                        style="font-size:14px"><strong></strong></span><br>

                                                                    {{ $reservation->hotel->id }}<br>
                                                                    {{ $reservation->hotel->name }} <br>
                                                                    {{ $reservation->hotel->hotel_address->address_line_1 ? $reservation->hotel->hotel_address->address_line_1 : '-' }}<br>
                                                                    {{ $reservation->hotel->hotel_address->address_line_2 ? $reservation->hotel->hotel_address->address_line_2 : '-' }}<br>
                                                                </p>
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:18px;color:#022B3A;font-size:12px">
                                                                    {{ $reservation->hotel->hotel_address->state ? $reservation->hotel->hotel_address->state : '-'  }}
                                                                    /
                                                                    {{ $reservation->hotel->hotel_address->city ? $reservation->hotel->hotel_address->city : '-' }}
                                                                    &nbsp;<br>Vietnam<br>{{ $reservation->hotel->tele_phone_number ? $reservation->hotel->tele_phone_number : '-' }}<br>
                                                                    {{ $reservation->hotel->website ? $reservation->hotel->website : '-' }}
                                                                    <br>
                                                                    {{ $reservation->hotel->email ? $reservation->hotel->email : '-' }}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if mso]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding:0;Margin:0">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:600px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="center"
                                                                style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;font-size:0">
                                                                <table border="0" width="100%" height="100%"
                                                                    cellpadding="0" cellspacing="0" role="presentation"
                                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tr>
                                                                        <td
                                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="esdev-adapt-off" align="left"
                                        style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                                        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:335px" valign="top"><![endif]-->
                                        <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                            <tr>
                                                <td class="es-m-p20b" align="center" valign="top"
                                                    style="padding:0;Margin:0;width:335px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="left" style="padding:0;Margin:0">
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:18px;color:#022B3A;font-size:12px">
                                                                    <span style="font-size:14px"><strong>Guest
                                                                            Information:</strong></span>:<br>Guest
                                                                    Name:<br>Contact:<br>Email Address:
                                                                </p>
                                                            </td>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!--[if mso]></td><td style="width:40px"></td><td style="width:185px" valign="top"><![endif]-->
                            <table cellpadding="0" cellspacing="0" class="es-right" align="right"
                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                <tr>
                                    <td align="left" style="padding:0;Margin:0;width:185px">
                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="right" style="padding:0;Margin:0">
                                                    <p
                                                        style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:18px;color:#022B3A;font-size:12px">
                                                        <span
                                                            style="font-size:14px"><strong></strong></span><br>{{ $reservation->user->first_name ? $reservation->user->first_name : ''}}
                                                        {{ $reservation->user->last_name ? $reservation->user->last_name : '' }}<br>{{ $reservation->user->phone_number ? $reservation->user->phone_number : '' }}<br>
                                                        {{ $reservation->user->email ? $reservation->user->email : '' }}
                                                        Address:
                                                    </p>
                                                </td>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
                </td>
                </tr>
                <tr>
                    <td align="left" style="padding:0;Margin:0">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:600px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center"
                                                style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>

                </tr>
                <tr>
                    <td align="left" bgcolor="#f2fcfe" style="padding:20px;Margin:0;background-color:#f2fcfe">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0">
                                                <h2
                                                    style="Margin:0;line-height:17px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:14px;font-style:normal;font-weight:bold;color:#022B3A">
                                                    Guest Special Request:</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left"
                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px">
                                                <p
                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                    {{ $reservation->special_request }}
                                                    <br><br><br>
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
                    <td align="left" style="padding:0;Margin:0;padding-top:10px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center"
                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="left" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center" class="es-m-txt-l"
                                                style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                                <h3
                                                    style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#022B3A">
                                                    Room Detail</h3>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-top:5px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" class="esdev-mso-table"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px">
                            <tr>
                                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                    <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                        @foreach($pricing_data as $room_pricing_form)
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0;width:335px">
                                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            Room Category:
                                                        </td>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $room_pricing_form['room_name'] }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            Check In Date:
                                                        </td>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $reservation->checkin }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            Check In Time:
                                                        </td>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $reservation->checkin_at }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            Check Out Date:
                                                        </td>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $reservation->checkout }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            Check Out Time:
                                                        </td>
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $reservation->checkout_at }}
                                                        </td>
                                                    </tr>
                                                    @php
                                                    $room_pricing = array_slice($room_pricing_form['pricing_form'], 0,
                                                    3);
                                                    $room_pricing_after_discount =
                                                    array_slice($room_pricing_form['pricing_form'],
                                                    3);

                                                    @endphp
                                                    @foreach($room_pricing as $price_details)
                                                    <tr>
                                                        @if($price_details['key'] === 'Total Room Charges')
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;font-weight:bold;">
                                                            {{ $price_details['key'] }}
                                                        </td>
                                                        @else
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $price_details['key'] }}
                                                        </td>
                                                        @endif
                                                        <td class="col-6"
                                                            style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                            {{ $price_details['value'] }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>
                @if($reservation->promotion_amount > 0)
                <tr>
                    <td align="left"
                        style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center"
                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:0;Margin:0;width:560px">
                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" class="es-m-txt-l"
                                    style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                    <h3
                                        style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#022B3A">
                                        Promotion & Offer's</h3>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td class="col-6"
                                    style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                    Promotion Title:
                                </td>
                                <td class="col-6"
                                    style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                    123
                                </td>
                            </tr>
                            <tr>
                                <td class="col-6"
                                    style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                    Discount Amount:
                                </td>
                                <td class="col-6"
                                    style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                    123
                                </td>
                            </tr>
                            <tr style="height: 10px;"></tr> <!-- Empty row for spacing with height -->
                            <tr>
                                <td class="col-6"
                                    style="font-family:Poppins, sans-serif;font-size:14px;font-style:normal;font-weight:bold;">
                                    Total Room Charges After Discount:
                                </td>
                                <td class="col-6"
                                    style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                    123
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
                @endif
                <tr>
                    <td align="left"
                        style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center"
                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if($reservation->extra_charges > 0)
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="left" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center" class="es-m-txt-l"
                                                style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                                <h3
                                                    style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#022B3A">
                                                    <b></b><strong>Details of Your Additional
                                                        Services</strong><b></b>
                                                </h3>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @foreach($reservation->room_reservations as $room_reservation)
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-top:5px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="left" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Number of Extra Adult:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_adults }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Charges per Adult:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_adults_amount }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Total Extra Adult Charges:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_adults_amount }}
                                            </td>
                                        </tr>
                                        <tr style="height: 10px;"></tr> <!-- Empty row for spacing with height -->
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Number of Extra Children:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_children }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Charges per Child:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_children }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Total Extra Children Charges:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_children_amount }}
                                            </td>
                                        </tr>
                                        <tr style="height: 10px;"></tr> <!-- Empty row for spacing with height -->
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Number of Extra Meal:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_children_amount }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Charges per Meal:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->meal_plan }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Total Meal Charges:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->meal_plan_amount }}
                                            </td>
                                        </tr>
                                        <tr style="height: 10px;"></tr> <!-- Empty row for spacing with height -->

                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Number of Extra Bed:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_bed }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Charges per Extra Bed:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_bed_amount }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                Total Extra Bed Charges:
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->extra_bed_amount }}
                                            </td>
                                        </tr>
                                        <tr style="height: 10px;"></tr> <!-- Empty row for spacing with height -->
                                        <tr>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                <strong>Total Additional Service Charges:</strong>
                                            </td>
                                            <td class="col-6"
                                                style="font-family:Poppins, sans-serif;font-size:12px;font-style:normal;">
                                                {{ $room_reservation->service_charge + $room_reservation->service_fee  }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td align="left"
                        style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center"
                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="left" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center" class="es-m-txt-l"
                                                style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                                <h3
                                                    style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#022B3A">
                                                    Amount Due</h3>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="Margin:0;padding-top:5px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" class="esdev-mso-table"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px">
                            <tr>
                                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                    <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0;width:270px">
                                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td align="right" class="es-m-txt-l"
                                                            style="padding:0;margin:0;padding-top:5px;padding-bottom:5px">
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                <strong>Total Amount:</strong>
                                                            </p>
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                <strong>Property Tax:</strong>
                                                            </p>
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                <strong>Property Service
                                                                    Charge:</strong>
                                                            </p>
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                <strong>Grand Total:</strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="padding:0;Margin:0;width:20px"></td>
                                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                    <table cellpadding="0" cellspacing="0" class="es-right" align="right"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0;width:270px">
                                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td align="left"
                                                            style="padding:0;margin:0;margin-bottom:0 !important;padding-top:5px;padding-bottom:5px">
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                VND {{ $reservation->sub_total }}
                                                            </p>
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                VND {{ $reservation->security_fee }}
                                                            </p>
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                VND {{ $reservation->sub_total }}
                                                            </p>
                                                            <p
                                                                style="margin:0;margin-bottom:0 !important;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                VND {{ $reservation->total }}
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="left" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center"
                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #a4cfd7;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" class="es-m-txt-l"
                                                style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                                <h3
                                                    style="Margin:0;line-height:24px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#022B3A">
                                                    Payment Details</h3>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="esdev-adapt-off" align="left"
                        style="Margin:0;padding-top:5px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                        <table cellpadding="0" cellspacing="0" class="esdev-mso-table"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px">
                            <tr>
                                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                    <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0;width:270px">
                                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td align="right" class="es-m-txt-l"
                                                            style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                                            <p
                                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                <strong>Payment Method:</strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="padding:0;Margin:0;width:20px"></td>
                                <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                    <table cellpadding="0" cellspacing="0" class="es-right" align="right"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0;width:270px">
                                                <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td align="left"
                                                            style="padding:0;Margin:0;padding-top:5px;padding-bottom:5px">
                                                            <p
                                                                style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                @lang($reservation->payment_method)<br>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="left"
                        style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:40px">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center" style="padding:0;Margin:0">
                                                <!--[if mso]><a href="" target="_blank" hidden>
					<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" esdevVmlButton href="" 
								style="height:41px; v-text-anchor:middle; width:136px" arcsize="12%" stroke="f"  fillcolor="#1f7a8c">
						<w:anchorlock></w:anchorlock>
						<center style='color:#ffffff; font-family:Poppins, sans-serif; font-size:15px; font-weight:400; line-height:15px;  mso-text-raise:1px'>Successful</center>
						</v:roundrect></a>
					<![endif]-->
                                                <!--[if !mso]><!-- --><span class="msohide es-button-border"
                                                    style="border-style:solid;border-color:#2CB543;background:#1F7A8C;border-width:0px;display:inline-block;border-radius:5px;width:auto;mso-border-alt:10px;mso-hide:all"><a
                                                        href="" class="es-button" target="_blank"
                                                        style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:18px;padding:10px 20px 10px 20px;display:inline-block;background:#1F7A8C;border-radius:5px;font-family:Poppins, sans-serif;font-weight:normal;font-style:normal;line-height:22px;width:auto;text-align:center">{{ $reservation->status }}</a></span>
                                                <!--<![endif]-->
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                        <td align="center" style="padding:0;Margin:0">
                            <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0"
                                cellspacing="0"
                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                <tr>
                                    <td align="left" bgcolor="#f2fcfe"
                                        style="padding:20px;Margin:0;background-color:#f2fcfe">
                                        <!--[if mso]><table style="width:560px" cellpadding="0" cellspacing="0"><tr><td style="width:270px" valign="top"><![endif]-->
                                        <table cellpadding="0" cellspacing="0" align="left" class="es-left"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                            <tr>
                                                <td class="es-m-p20b" align="center" valign="top"
                                                    style="padding:0;Margin:0;width:270px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <h2
                                                                    style="Margin:0;line-height:34px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:28px;font-style:normal;font-weight:bold;color:#022B3A">
                                                                    Need to change something?</h2>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"
                                                                style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px">
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                    You can cancel within one hour.</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if mso]></td><td style="width:20px"></td><td style="width:270px" valign="top"><![endif]-->
                                        <table cellpadding="0" cellspacing="0" class="es-right" align="right"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                            <tr>
                                                <td align="left" style="padding:0;Margin:0;width:270px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        role="presentation"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                            <td align="center" class="es-m-p0t es-m-p0b"
                                                                style="padding:0;Margin:0;padding-top:25px;padding-bottom:25px">
                                                                <!--[if !mso]><!-- --><span
                                                                    class="es-button-border msohide"
                                                                    style="border-style:solid;border-color:#2CB543;background:#1F7A8C;border-width:0px;display:inline-block;border-radius:5px;width:auto;mso-border-alt:10px;mso-hide:all">
                                                                    <a href="#" class="es-button"
                                                                        style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:18px;padding:10px 20px 10px 20px;display:inline-block;background:#1F7A8C;border-radius:5px;font-family:Poppins, sans-serif;font-weight:normal;font-style:normal;line-height:22px;width:auto;text-align:center"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#cancelReservationModal"
                                                                        data-reservation-id="{{ $reservation->id }}">Cancel
                                                                        order</a>
                                                                </span>
                                                                <!--<![endif]-->
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if mso]></td></tr></table><![endif]-->
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" class="es-content" align="center"
                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                        <td align="center" style="padding:0;Margin:0">
                            <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0"
                                cellspacing="0"
                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                <tr>
                                    <td align="left"
                                        style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:40px">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top"
                                                    style="padding:0;Margin:0;border-radius:5px;overflow:hidden;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%"
                                                        bgcolor="#f2fcfe"
                                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-left:1px solid #a4cfd7;border-right:1px solid #a4cfd7;border-top:1px solid #a4cfd7;border-bottom:1px solid #a4cfd7;background-color:#f2fcfe;border-radius:5px"
                                                        role="presentation">
                                                        <tr>
                                                            <td align="center"
                                                                style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                                                                <h2
                                                                    style="Margin:0;line-height:34px;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;font-size:28px;font-style:normal;font-weight:bold;color:#022B3A">
                                                                    Property Cancellation Policy</h2>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"
                                                                style="padding:0;Margin:0;padding-top:15px;padding-bottom:15px">
                                                                @if(count($cancellation_policies) > 0)
                                                                @foreach($cancellation_policies as $cancellation_policy)
                                                                @foreach($cancellation_policy['policies'] as $policy)
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                    {{ $policy['days'] }}
                                                                    @lang('messages.days')
                                                                    @lang('messages.before_checkin_date'):
                                                                    {{ $policy['percentage'] }} <span class="">%</span>
                                                                </p>
                                                                @endforeach
                                                                @endforeach
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"
                                                                style="padding:0;Margin:0;padding-top:15px;padding-bottom:15px">
                                                                <p
                                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#022B3A;font-size:14px">
                                                                    Need more information?<br><a target="_blank"
                                                                        href="https://duhiviet.com/refund-cancellation"
                                                                        style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#022B3A;font-size:14px">View&nbsp;our&nbsp;Refund
                                                                        &amp; Cancellation Policy</a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table cellpadding="0" cellspacing="0" class="es-footer" align="center"
                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                    <tr>
                        <td align="center" style="padding:0;Margin:0">
                            <table bgcolor="#ffffff" class="es-footer-body" align="center" cellpadding="0"
                                cellspacing="0"
                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px">
                                <tr>
                                    <td class="esdev-adapt-off" align="left"
                                        style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- end -->
            </div>
        </div>
    </div>
    </div>
</main>
<div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4>@lang('messages.cancel_your_reservation')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form">
                    <form action="{{ resolveRoute('cancel_reservation') }}" method="POST" class="cancel_form"
                        id="cancel-form">
                        @csrf
                        <input type="hidden" name="reservation_id" value="{{$reservation->id}}">

                        <div class="form-group">
                            <label for="selected_rooms" class="form-label"> @lang('messages.rooms') </label>
                            <select name="room_reservations" class="form-select">
                                <option value="all" selected> @lang('messages.all') </option>
                                @foreach ($reservation->room_reservations as $room)
                                @if ($room->status == 'Accepted')
                                <option value="{{ $room->id }}"> {{ $room->hotel_room->name }} </option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cancelReason" class="form-label">
                                @lang('messages.why_are_you_cancel')
                            </label>
                            <p class="text-xsmall m-0"> @lang('messages.info_not_shared_with_host') </p>
                            <select name="cancel_reason" class="form-select mt-1 px-2"
                                v-model="cancelDetails.cancelReason">
                                <option value="" selected> @lang('messages.select') </option>
                                @foreach(GUEST_CANCEL_REASONS as $reason)
                                <option value="{{ $reason }}"> @lang('messages.'.$reason) </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Remaining form fields -->

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary float-end"
                                :disabled="cancelDetails.cancelReason == ''">
                                @lang('messages.cancel_booking')
                            </button>
                            <button type="button" class="btn btn-default float-end me-3" data-bs-dismiss="modal"
                                aria-label="Close">
                                @lang('messages.close')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
function printRecipt() {
    window.open('{{ $redirect_url }}', 'receipt');
}
</script>
<script>
$(document).ready(function() {
    $('#cancelReservationModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var reservationId = button.data('reservation-id'); // Extract reservation ID from data attribute
        $('#reservationIdInput').val(reservationId); // Set the value of the hidden input field
    });
});
</script>
@endpush