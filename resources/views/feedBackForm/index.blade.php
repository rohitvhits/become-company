<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Form</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background: white;
      width: 100%;
      max-width: 600px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 1px;
    }

    h1 {
      text-align: center;
      color: #333;
      font-size: 1.8rem;
      margin-bottom: 1rem;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    input[type="text"], textarea {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }

    textarea {
      height: 80px;
      resize: none;
    }

    button {
      display: block;
      width: 100%;
      background-color: #007bff;
      color: white;
      padding: 0.9rem;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #0056b3;
    }

    .details {
      font-size: 13px;
      background-color: #f4f4f4;
      padding: 5px;
      margin-bottom: 1.5rem;
    }

    .details p {
      margin: 0.2rem 0;
      padding: 0px 5px 0px 5px;
    }
    .submit-btn {
        background-color: #007BFF;
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        width: 28%;
        font-size: 15px;
        display: block; /* Makes the button a block element */
        margin: 0 auto; /* Centers horizontally */
    }
    .logo {
      display: block;
      margin: 0 auto 1rem;
    }
    textarea {
        width: 100%;
        padding: 8px;
        margin-bottom: 12px;
        box-sizing: border-box;
        border-radius: 10px;
        width: 100%;
        padding: 12px 20px;
        box-sizing: border-box;
        border: 2px solid #ccc;
        background-color: #f8f8f8;
        font-size: 16px;
        resize: none;
    }
    .container form{
      margin: 0.2rem 0;
      padding: 0px 10px 0px 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div style="padding: 17px 0 1px 0; background: #000000; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
        <img src="{{ asset('img/logo-ny.png')}}" class="logo" style="width:190px;vertical-align:middle">
    </div>
        <div class="details">
            <table>
                <tr>
                    <td>
                        <p><strong>Name</strong></p>
                    </td>
                    <td>
                        <p>: &nbsp;{{ ucwords($patientDetails->first_name) }} {{ ucwords($patientDetails->last_name) }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Agency Name</strong> </p>
                    </td>
                    <td>
                        <p>: &nbsp;{{ $patientDetails->agency_name}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Mobile No</strong></p>
                    </td>
                    <td>
                        <p>: &nbsp;{{ $patientDetails->mobile}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Services</strong></p>
                    </td>
                    <td>
                        <p>@if (isset($patientDetails->service) && $patientDetails->service != '')
                                    : &nbsp;{{$patientDetails->service}}
                            @else 
                                    : &nbsp;N/A
                            @endif </p>
                    </td>
                </tr>
                @if($patientDetails->appointment_date)
                <tr>
                    <td>
                          <p><strong>Appointment Date</strong></p>
                    </td>
                    <td>
                      <p>
                        : &nbsp;{{ date('m/d/Y', strtotime($patientDetails->appointment_date)) }}
                        @if ($patientDetails->type == 'Caregiver' && $patientDetails->start_time)
                            {{ date('h:i A', strtotime($patientDetails->start_time)) . ' - ' . date('h:i A', strtotime($patientDetails->edate)) }}
                        @else
                            @if($patientDetails->appointment_date!='')
                                {{date('h:i A', strtotime($patientDetails->appointment_date))}}
                            @endif
                        @endif  
                      </p>
                    </td>
                </tr>
                @endif
                 <tr>
                    <td>
                        <p><strong>Gender</strong></p>
                    </td>
                    <td>
                        <p>: &nbsp;{{ ucwords($patientDetails->gender) }}</p>
                    </td>
                </tr>
            </table>
          
        </div>
        <form action="{{url('/submit-feedback-form')}}" method="POST">
            @csrf
            <input type="hidden" name="service_request_id" value="{{$service_request_id}}">
            @foreach($details as $key => $que)
                <div class="form-group">
                    <label for="q1"> {{$key + 1 }}. {{$que->title}}</label>
                    <input type="hidden" name="question[{{$key}}][que_id]" value="{{$que->id}}">
                    <input type="hidden" name="question[{{$key}}][question]" value="{{$que->title}}">
                    <textarea name="question[{{$key}}][answer]" id="question_{{$key}}" rows="3" placeholder="Your comments here..."></textarea>
                </div>
            @endforeach 
            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
  </div>
</body>
</html>
