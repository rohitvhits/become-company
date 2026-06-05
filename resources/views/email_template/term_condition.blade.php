<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>NY BEST MEDICAL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </link>
</head>

<body style="margin: 0; padding: 0;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 10px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                    <tr>
                        <td align="center" bgcolor="#000000" style="padding: 20px 0 20px 0; color: #000000; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                           <img src="{{ asset('img/logo-ny.png')}}" style="width:190px;vertical-align:middle">
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 20px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="font-family: Arial, sans-serif; font-size: 13px;">
                                        Hello {{$username}},
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 29px 0; color: #00BBE0 !important; font-family: Arial, sans-serif; font-size: 20px; line-height: 20px;">
                                        <b> {{$sub}}.</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0px 0;  font-family: Arial, sans-serif; font-size: 15px; line-height: 20px;">
                                    We hope you're doing well. We are writing to inform you of an important update to our Terms and Conditions. We have made some changes to better serve you and ensure compliance with legal and regulatory standards.

                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0px 0;  font-family: Arial, sans-serif; font-size: 15px; line-height: 20px;">
                                    We encourage you to carefully review the updated Terms and Conditions by visiting the link below: <a href="{{ url('term-condition')}}">[Link to Terms and Conditions]</a>

                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0px 0;  font-family: Arial, sans-serif; font-size: 15px; line-height: 20px;">
                                    By continuing to use our services, you are agreeing to the updated Terms and Conditions.

                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0px 0;  font-family: Arial, sans-serif; font-size: 15px; line-height: 20px;">
                                    Thank you for being a valued member of our community.

                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 0 10px 0; color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        Thank you,<br/>
                                        NyBest Medical 
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>