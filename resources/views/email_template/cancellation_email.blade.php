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
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        Hello NY Best Medical,
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td style="font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <b>Appointment has been Cancelled.
                                        </b>
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <b>Agency Name:</b> {{$agencyname}}
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <b>Portal Id :</b>  {{$insert}}
                                    </td>
                                </tr>    

                                @if($type == 'Caregiver')
                                    <tr>
                                        <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                            <b>Caregiver Name :</b> {{$first_name}}  {{$last_name}}
                                        </td>
                                    </tr>
                                @else
                                <tr>
                                        <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                            <b>Patient Name :</b>  {{$first_name}}  {{$last_name}}
                                        </td>
                                    </tr>
                                @endif

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