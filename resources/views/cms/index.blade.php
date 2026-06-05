@include('include/header')
@include('include/sidebar')
<link href="{{ asset('assets/jquery-confirmation/css/jquery-confirm.min.css')}}">
<div class="main-panel">
   
    <div class="content-wrapper">


        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">CMS List</h5>
            <div class="page-rightbtns">
                <div>



                </div>
            </div>
        </div>


        <div class="card" style="margin-top: 10px">
          

            <div class="row">
                <div class="col-12">

                   <table class="table table-bordered">
                        <thead>
                            <th>No</th>
                            <th>Type</th>
                            <th>Description</th>
                            
                            <th>Created Date / Created By</th>
                            <th>Updated Date / UpdatedBy</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @php 
                                $cnt =1
                            @endphp

                            @if(count($cms_list) >0)
                                @foreach($cms_list as $val)
                                <span style="display: none;" id="{{ $val->id}}">
                                    {!! $val->message !!}
                                </span>
                                <span style="display: none;" id="type{{ $val->id}}">
                                    {{$val->type}}
                                </span>
                                    <tr>
                                        <td>{{ $cnt++}}</td>
                                        <td>{{ $val->type}}</td>
                                        <td><a style="text-decoration:none" href="javascript:void(0)" onclick="commonModal('{{ $val->id}}')">{!! strlen(strip_tags($val->message)) > 50 ? substr(strip_tags($val->message), 0, 50) . '...' : strip_tags($val->message) !!}</td>
                                        <td>{{ date('m/d/Y h:i A',strtotime($val->created_at))}}
                                           <br>
                                            @if(isset($val->createdUser->id))
                                                {{ $val->createdUser->first_name.' '.$val->createdUser->last_name}}
                                            @endif
                                        </td>
                                        <td>
                                        @if($val->updated_at !="")    
                                        {{ date('m/d/Y h:i A',strtotime($val->updated_at))}}
                                        @endif
                                        <br>
                                            @if(isset($val->updatedUsers->id))
                                                {{ $val->updatedUsers->first_name.' '.$val->updatedUsers->last_name}}
                                            @endif</td>
                                       
                                        <td>
                                            @can('cms-update')
                                            <a href="{{ url('cms-edit') }}?id={{ $val->id}}"><i class="fa fa-edit"></i></a>
                                            @endcan
                                            @can('cms-send-mail')
                                            <a href="javascript:void(0)" onclick="sendMail('{{ $val->id}}')"><i class="mdi mdi-message"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            @if(count($cms_list) ==0)
                            <tr>
                                        <td colspan="9">No record available</td>
                                       
                                    </tr>

                            @endif
                        </tbody>
                   </table>
                </div>
            </div>
         
        </div>
    </div>

    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>


@include('include/footer')
</div>
<script src="{{ asset('assets/jquery-confirmation/js/jquery-confirm.min.js') }}"></script>

<script>
    function commonModal(id){
        var text = $('#'+id).html();
        var title = $('#type'+id).html();
        $.confirm({
            title: title,
            content: "<p style='white-space:pre-line'>"+text+"</p>",
            type: 'blue',
            columnClass: 'col-md-9',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    action: function () {
                        
                    }
                }
            }
        });
    }

    function sendMail(id){
       var title="Terms and Conditions"; 
        if(id ==1){
            title="Privacy Policy";
        }
        $.confirm({
            title: "Are you sure?",
            content: "you want to send the email notification regarding the "+title,
            type: 'blue',
            columnClass: 'col-md-6',
            buttons: {
                submit: {
                    text: 'Send',
                    btnClass: 'btn-blue',
                    action: function () {
                        $.ajax({
                            type:"get",
                            url:"{{ url('send-cms-notification')}}",
                            data:{'id':id},
                            success:function(res){
                                toastr.success(res.error_msg);
                            },
                            error:function(jqr){
                                toastr.error(jqr.responseJSON.error_msg);
                            }
                        })
                    }
                },
                cancel: {
                    text: 'Cancel',
                    action: function () {
                        
                    }
                }
            }
        });
    }
</script>