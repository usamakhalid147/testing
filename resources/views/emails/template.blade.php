<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title> {{ $subject }} </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .link {
            color: #008276;
        }

        .btn>tbody>tr>td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #008276;
            border-radius: 5px;
            box-sizing: border-box;
            color: #008276;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #008276;
        }

        .btn-primary a {
            background-color: #008276;
            border-color: #008276;
            color: #ffffff;
        }

        hr {
            max-width: 580px;
            border-right: 0;
            border-top: 0;
            border-bottom: 1px solid #cacaca;
            border-left: 0;
            clear: both;
            background-color: #dbdbdb;
            height: 2px;
            width: 100%;
            border: none;
            margin: auto
        }

        .h1 {
            font-size: 32px;
            line-height: 36px;
        }
    </style>
</head>

<body style="background-color:#F4F4F4;margin: 0; padding: 0;color: #484848">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 10px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                    style="border-spacing: 0;
                    border-collapse: collapse;
                    padding: 0;
                    vertical-align: top;
                    background: #fefefe;
                    width: 100%;
                    margin: 0 auto;
                    text-align: inherit;
                    max-width: 53.5%;">
                    <tbody>
                        @if (isset($header_name))
                            @include('emails.common.' . $header_name)
                        @else
                            <tr style="padding:0;vertical-align:top;text-align:left">
                                <td
                                    style="word-wrap:break-word;vertical-align:top;color:#6f6f6f;font-family:'Cereal',Helvetica,Arial,sans-serif;font-weight:normal;padding:0;margin:0;text-align:left;font-size:16px;line-height:19px;border-collapse:collapse!important">
                                    @include('emails.common.basic_header')
                                </td>
                            </tr>
                        @endif
                        <tr style="padding:0;vertical-align:top;text-align:left">
                            <td
                                style="padding:16px;word-wrap:break-word;vertical-align:top;color:#6f6f6f;font-family:'Cereal',Helvetica,Arial,sans-serif;font-weight:normal;padding:0;margin:0;text-align:left;font-size:16px;line-height:19px;border-collapse:collapse!important">
                                @yield('content')
                            </td>
                        </tr>
                        <tr style="padding:0;vertical-align:top;text-align:left">
                            <td
                                style="word-wrap:break-word;vertical-align:top;color:#6f6f6f;font-family:'Cereal',Helvetica,Arial,sans-serif;font-weight:normal;padding:0;margin:0;text-align:left;font-size:16px;line-height:19px;border-collapse:collapse!important">
                                @include('emails.common.basic_footer')
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
