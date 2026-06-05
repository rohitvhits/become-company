<div class="table-responsive">
    <table id="order-listing1" class="table table-bordered table-width1">
        <thead>
            <tr>

                <th>ID</th>
                <th>Record Id</th>
                <th class="no_warp">Full Name</th>
                
                <th class="no_warp">Date of Birth</th>
                <th class="no_warp">Gender</th>
                <th class="no_warp">Language</th>
                <th class="no_warp">Mobile</th>
                <th class="no_warp">Insurance</th>
                <th class="no_warp">Referral Uid</th>
                <th class="no_warp">Emmacare Response</th>
                <th class="no_warp">Created Date / Created By</th>
                
            </tr>
        </thead>
        <tbody>
            
            @if(count($query) >0)
                @php 
                    $i = 1 + (($query->currentPage() - 1) * $query->perPage())
                @endphp
                @foreach ($query as $row)
                    @php 
                        $response = unserialize($row->return_response);
                    @endphp
                    <tr>
                        <td>{{ $i++}}</td>
                        <td>
                        <a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->record_id; ?>" target="_blank"><?= '#' . '' . $row->record_id ?></a>
                        </td>
                        <td>{{ $row->first_name.' '.$row->middle_name.' '.$row->last_name}}</td>
                        <td>{{ $row->dob}}</td>
                        <td>{{ ucfirst($row->gender)}}</td>
                        <td>{{ $row->primaryLanguage}}</td>
                        <td>{{ $row->phones}}</td>
                        <td>{{ $row->insurance}}</td>
                        <td>{{ $row->referral_uid}}</td>
                        <td>
                        @if(isset($response['uuid']))
                            {{ $response['uuid']}}
                        @endif

                        </td>
                        <td>{{ date('m/d/Y h:i A',strtotime($row->created_at))}} /<br>
                        @if(isset( $row->userDetaials->first_name))    
                        {{$row->userDetaials->first_name .' '.$row->userDetaials->last_name}}
                    @endif
                        </td>
                        
                    </tr>
                @endforeach
            @endif
            @if(count($query) ==0)
                <tr>
                    <td colspan="11">No record available</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="pull-right pegination-margin">

    {{ $query->appends(request()->query())->links() }}
    </div>
</div>