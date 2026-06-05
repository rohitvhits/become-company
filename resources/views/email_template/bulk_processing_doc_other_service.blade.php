<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>NY BEST MEDICAL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </link>
</head>
<style>


.table-wrapper table tbody tr:hover {
    background-color: #eef6ff;
    transition: background-color 0.3s ease;
}

.table-wrapper table th {
    font-weight: 600;
    white-space: nowrap;
}
</style>
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
                                        Hello,
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 29px 0; color: #00BBE0 !important; font-family: Arial, sans-serif; font-size: 20px; line-height: 20px;">
                                        <b> Action Needed: Please review the pending documents listed below.</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <b>Agency Name :</b> {{$agency_name}}
                                    </td>
                                </tr> 
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <b>Portal Id :</b> <a target="_blank" href="{{url('patient/view/')}}/{{$portal_id}}"> {{$portal_id}} </a>
                                    </td>
                                </tr>    
                                <tr>
                                    <td style="color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <b>Patient Name :</b> {{$first_name}}  {{$last_name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 0 10px 0; color: #153643; font-family: Arial, sans-serif; font-size: 13px; line-height: 20px;">
                                        <div class="table-wrapper" style="margin: 20px 0;overflow-x: auto;">
                                        <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth" style=" width: 100%;border-collapse: collapse;font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-size: 14px;background-color: #f0f4f8;color: #333;">
                                                <tr>
                                                    <td style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;"><b>No.</b></td>
                                                    <td style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;" nowrap><b>Document name</b></td>
                                                    <td style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;" nowrap><b>Attachment Service</b></td>
                                                    <td style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;" nowrap><b>Created Date</b></td>
                                                    <td style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;" nowrap><b>Created By</b></td>
                                                </tr>
                                                @foreach($docData as $key => $doc)
                                                    <tr style=" background-color: #f9f9f9;">
                                                        <td  style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;">
                                                            {{$key+1}}
                                                        </td>
                                                        <td  nowrap   style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;">
                                                            {{$doc->document_name}}
                                                        </td>
                                                        <td  nowrap  style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;">
                                                            {{$doc->services??'-'}}
                                                        </td>
                                                        <td  nowrap style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;">
                                                            {{date('m/d/Y h:i A',strtotime($doc->created_date))}}
                                                        </td>
                                                        <td  nowrap style="padding: 10px 15px;text-align: left;border: 1px solid #e0e0e0;vertical-align: middle;">
                                                            {{$doc->userDetails->first_name}} {{$doc->userDetails->last_name}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </table>
                                        </div>
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