<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th >#</th>
            <!-- <th>Type</th> -->
            <th>Start Date</th>
            <th>End Date</th>
            <th>Permission</th>
            <th>Created Date / Created By</th>
            <th>Updated Date / Updated By</th>
            <th>Action</th>
        </tr>
    </thead>
    
    <tbody>
        @if(count($query) > 0)
            @php $i = 1 + (($query->currentPage() - 1) * $query->perPage()); @endphp
            @foreach ($query as $row)
                <tr>
                    <td><?= $i++ ?></td>
                    <!-- <td>{{ $row->type }}</td> -->
                    <td>{{ Common::convertMDY($row->start_date)}}</td>
                    <td>{{ Common::convertMDY($row->end_date)}}</td>
                    <td>{{ $row->permission}}</td>
                    <td>{{ Common::convertMDYTime($row->created_date) }} <br> {{ $row->createdUserDetails->first_name.' '.$row->createdUserDetails->last_name}}</td>
                    <td>
                    {{ Common::convertMDYTime($row->updated_date) }} <br>
                    @if(isset($row->updatedUserDetails))
                    {{ $row->updatedUserDetails->first_name.' '.$row->updatedUserDetails->last_name}}
                    @endif
                    
                    </td>
                    <td>
                        <a href="javascript:void(0)" onclick="editDetailsDateWiseAgencyAccess('{{ $row->id}}')"><i class="fa fa-edit"></i></a>
                        <a href="javascript:void(0)" onclick="deleteDetailsDateWiseAgencyAccess('{{ $row->id}}')"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @else
        <tr>
            <td colspan="7">
                <div class="text-center"><b>No record available</b></div>
            </td>
        </tr>
        @endif
    </tbody>
</table>

<div class="pull-right pegination-margin user-date-wise-access-permission">
    {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>