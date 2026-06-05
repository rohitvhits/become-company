<table id="order-listing1" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Hub Record</th>
            <th>Company Name</th>
            <th>Name</th>
            <th>Document Name</th>
            <th>Attachment</th>
            <th>Created Date / Created By</th>
        </tr>
    </thead>

    <tbody>
        @if(count($query) >0)
            @php
                $cnt = ($query->currentPage() - 1) * $query->perPage() + 1;
            @endphp
        
            @foreach($query as $val)
                <tr>
                    <td>{{$cnt++}}</td>
                    <td>
                        @if(isset($val->hub_record_id))
                           <a href="{{ url('hub-record/view')}}/{{ $val->hub_record_id}}" target="_blank"> {{ $val->hub_record_id}}</a>
                        @endif
                        <br/>
                    </td>
                    <td>
                        {{ $val->agency_name }}
                    </td>
                    <td>
                        {{ $val->first_name }} {{ $val->last_name }}
                    </td>
                    <td>
                        {{ $val->document_name }}
                    </td>
                    <td>
                        @if ($val->attachment != '')
                        <a target="_blank" href="<?php echo URL::to('/'); ?>/view-hub-doc/<?php echo $val->id; ?>"><i class="fa fa-download"></i> Download</a>
                        <br>
                        <a href="{{ url('hub-view-pdf-response')}}?id={{ $val->id}}" data-fancybox="" data-type="iframe" class="fancybox"><i class="fa fa-eye"></i>View</a>
                        @endif
                    </td>
                    <td>
                        {{ Common::convertMDYTime($val->created_date)}} <br>
                        @if(isset($val->userDetails->first_name))
                        {{ $val->userDetails->first_name.' '.$val->userDetails->last_name}}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif

        @if(count($query) == 0)
            <tr class="txt-center">
                <td colspan="7">No record available</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="pull-right hub_record_report_paginate pegination-margin" id="hub_record_report_paginate">
{{ $query->links() }}
</div>