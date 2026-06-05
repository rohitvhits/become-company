<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
            <th >#</th>
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
                <tr @if($row->permanent_access == 1) style="background-color: #ffe6e6;" @endif>
                    <td><?= $i++ ?></td>
                    <td>
                        @if($row->permanent_access == 1)
                            <span class="badge badge-danger">PERMANENT</span>
                        @else
                            {{ Common::convertMDY($row->start_date)}}
                        @endif
                    </td>
                    <td>
                        @if($row->permanent_access == 1)
                            <span class="badge badge-danger">PERMANENT</span>
                        @else
                            {{ Common::convertMDY($row->end_date)}}
                        @endif
                    </td>
                    <td>
                        {{ $row->permission}}
                        @if($row->permanent_access == 1)
                            <br><small class="text-danger"><i class="mdi mdi-block-helper"></i> <b>Permanently Restricted</b></small>
                        @endif
                    </td>
                    <td>{{ Common::convertMDYTime($row->created_date) }} <br> {{ $row->createdUserDetails->first_name.' '.$row->createdUserDetails->last_name}}</td>
                    <td>
                    {{ Common::convertMDYTime($row->updated_date) }} <br>
                    @if(isset($row->updatedUserDetails))
                    {{ $row->updatedUserDetails->first_name.' '.$row->updatedUserDetails->last_name}}
                    @endif

                    </td>
                    <td>
                        @if($row->permanent_access == 1)
                            {{-- Permanent entry - always editable, but no delete --}}
                            @can('delete-date-wise-user-permission')
                            <a href="javascript:void(0)" onclick="deleteDetailsDateWiseUserAccess('{{ $row->id}}')"><i class="fa fa-trash"></i></a>
                            @endcan
                        @elseif(isset($hasPermanentRestriction) && $hasPermanentRestriction)
                            {{-- Other entries when permanent exists - disabled --}}
                            <span class="text-muted" title="Cannot edit/delete when permanent restriction exists">
                                <i class="fa fa-edit" style="opacity: 0.3;"></i>
                                <i class="fa fa-trash" style="opacity: 0.3;"></i>
                            </span>
                        @else
                            {{-- Normal entries when no permanent - editable --}}
                            @can('edit-date-wise-user-permission')
                            <a href="javascript:void(0)" onclick="editDetailsDateWiseUserAccess('{{ $row->id}}')"><i class="fa fa-edit"></i></a>
                            @endcan
                            @can('delete-date-wise-user-permission')
                            <a href="javascript:void(0)" onclick="deleteDetailsDateWiseUserAccess('{{ $row->id}}')"><i class="fa fa-trash"></i></a>
                            @endcan
                        @endif
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