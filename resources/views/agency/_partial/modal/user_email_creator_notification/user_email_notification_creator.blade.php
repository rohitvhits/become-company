<div class="modal fade" id="add-created-user-email-notification-popup" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title notification-emails" id="ModalLabel">User Email Notification Creator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="forms-sample" enctype="multipart/form-data" action='' method="post" id="userCreatorNotification">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="agency_id" name="agency_id" value="{{ $id }}">
                   
                   
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label"><b>Email Notification</b></label>
                        <br>
                        <div class="row">
                            @php $count = 0; @endphp
                            @php 

                                $staticStatus = ['Add Appointment','Document Upload','Add Notes','Status No Show','Status Checkin','Status Completed']
                            @endphp
                            @if(!empty($staticStatus[0]))
                            @foreach($staticStatus as $item)

                            @if($count % 3 == 0 && $count > 0)
                        </div>
                        <div class="row">
                            @endif

                            <div class="col-md-4">
                               
                                <label>
                                    <input type="checkbox" id="user_creator_{{ $count }}" name="user_creator_notification[]" value="{{ $item }}" data-id="{{ $item}}" class="user_creator_notification">
                                    {{ $item }}
                                </label>
                               

                            </div>
                            @php $count++; @endphp
                            @endforeach
                            @endif

                        </div>
                        <span id="created_user_email_notification_error" class="error"></span>
                    </div>
                    
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="user-notification-email-saveId" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-light" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>