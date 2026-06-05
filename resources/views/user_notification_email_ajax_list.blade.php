
<form class="forms-sample" enctype="multipart/form-data" action='' name="addUsernotificationemail" method="post" id="addnotificationemail">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <table style="width:100%" class="user-notification-table table">
    <tr>
      <th>Patient</th>
      <td>
        
           
            @if(!empty($UserNotificationEmail[0]))
            @foreach($UserNotificationEmail as $item)
            <label>
                <input type="checkbox" id="patient_notification_email{{ $item->id }}" name="patient[]" 
                value="{{ $item->name }}" data-id="{{ $item->id}}" class="notification_checkbox patient_checkbox"
                @if(isset($query->status_update_patient_id) && $item->name == $query->status_update_patient_id) checked 
                @elseif(isset($query->upload_doc_patient_id) && $item->name == $query->upload_doc_patient_id) checked 
                @elseif(isset($query->send_notes_patient_id) && $item->name == $query->send_notes_patient_id) checked 
                @elseif(isset($query->add_new_record_patient_id) && $item->name == $query->add_new_record_patient_id) checked 
                @endif
                >
                {{ $item->name }}
            </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            @endforeach
            @endif
        
      </td>
    </tr>
    <tr>
      <th>Caregiver</th>
      <td>
        @if(!empty($UserNotificationEmail[0]))
            @foreach($UserNotificationEmail as $item)
            <label>
                <input type="checkbox" id="caregiver_notification_email{{ $item->id }}" name="caregiver[]" data-id="{{ $item->id }}" value="{{ $item->name }}" class="notification_checkbox caregiver_checkbox"
                @if(isset($query->status_update_caregiver_id) && $item->name == $query->status_update_caregiver_id)checked 
                @elseif(isset($query->upload_doc_caregiver_id) && $item->name == $query->upload_doc_caregiver_id) checked 
                @elseif(isset($query->send_notes_caregiver_id) && $item->name == $query->send_notes_caregiver_id) checked 
                @elseif(isset($query->add_new_record_caregiver_id) && $item->name == $query->add_new_record_caregiver_id) checked 
                @endif
                >
                {{ $item->name }}
            </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
        @endif
      </td>
    </tr>
    

  </table>
  <span id="notification_email_error" class="error"></span>
  <div class="modal-footer">
    <button type="button" id="notification-email-saveId" class="btn btn-success">Save</button>
   
</div>
</form>
