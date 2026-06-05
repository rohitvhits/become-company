@include('include/header')
@include('include/sidebar')
<style>
    .dl-horizontal dt {
        float: left;
        width: 72px;
        clear: left;
        /* text-align: right; */
        /* overflow: hidden; */
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dl-horizontal dd {
        margin-left: 90px;
        /* margin-bottom: 0px; */
    }
</style>

<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body mini-card">

            <div class="row">
                <div class="profile-feed col-12 pull-right" id="edit_medical">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 15px;">
                            <h6 class="card-title">Appointment Logs Details</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4" style="margin-bottom: 15px;">
                    <h6 class="card-title">Basic Details</h6>
                        <dl class="dl-horizontal">
                            <dt>Type:</dt>
                            <dd>{{$logList->type ?? ""}} </dd>
                            <dt>link :</dt>
                            <dd>{{$logList->link ?? ""}} </dd>
                            <dt>Module :</dt>
                            <dd>{{$logList->type ?? ""}} </dd>
                            <dt>Patient Id </dt>
                            <dd>{{$logList->object_id ?? ""}} </dd>
                            <dt>Message</dt>
                            <dd>{{$logList->message ?? "" }} </dd>
                            <dt>Ip</dt>
                            <dd>{{$logList->ip ?? ""}} </dd>
                            <dt>Created By</dt>
                            <dd>{{$logList->user->first_name ?? ""}} {{$logList->user->last_name ?? ""}} </dd>
                            <dt>Address</dt>
                            <dd>{{$logList->address ?? ""}} </dd>
                        </dl>
                </div>
            
                
            </div>
        </div>
        <div class="card-body mini-card">
        <table id="order-listing1" class="table table-bordered table-head-fix">
                <thead>
                   
                    <th style="white-space:nowrap">
                        <div class="sorting-div"><span>Old Response</span>
                        
                        </div>
                    </th>
                    <th style="white-space:nowrap">
                        <div class="sorting-div"><span>New Response</span>
                        
                        </div>
                    </th>
                   
                </thead>
                <tbody >
                    <tr>
                        <td>
                           
                            @if(!empty($logList->old_response))
                            @foreach($logList->old_response  as  $fields=>$value)
                            <b> {{ $fields}}</b> : {{$value}} <br>

                            @endforeach
                            @endif
                        </td>
                        <td> 
                        <?php
                            foreach($logList->new_response  as  $fields=>$value){ ?>
                                 <b> <?php echo $fields;?></b> : <?php echo $value;?> <br>
                             <?php }
                            ?>
                            
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
   
</div>
</div>
@include('include/footer')
