<table id="order-listing1" class="table table-bordered table-width1">
    <thead>
        <tr>
        <th>No</th>
                                    <th style="white-space:nowrap">Agency Name</th>
                                    <th style="white-space:nowrap">Office Name</th>
                                    <th style="white-space:nowrap">Medical ID</th>
                                    <th style="white-space:nowrap">Medical Name</th>
                                    <th style="white-space:nowrap">Status</th>
                                    <th style="white-space:nowrap">Last Sync Date</th>
                                    <th style="white-space:nowrap">Action</th>
        </tr>
    </thead>
    <tbody>

    @php
    $i = 1 + (($query->currentPage() - 1) * $query->perPage());
        $officeCode = "";
        $officeName = "";
        @endphp
        @if (count($query) > 0)
            @foreach ($query as $row)



            <tr>

                <td>{{ $i++}}</td>
                <td>
                    {{ $row->agency->agency_name ?? 'N/A' }}
                </td>
                <td>
                    {{ $row->office->office_name ?? 'N/A' }}
                </td>
                <td>{{ $row->medical_id}}</td>
                <td>{{ $row->medical_name}}</td>
                <td>
                    @if($row->status == 1)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </td>
                <td>@if($row->last_sync_date !=""){{ date('m/d/Y h:i A',strtotime($row->last_sync_date))}} @endif</td>
                <td>
                    @can('active-hha-medical-service')
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input status-toggle"
                               id="status_{{ $row->id }}"
                               data-id="{{ $row->id }}"
                               data-current-status="{{ $row->status }}"
                               data-medical-name="{{ $row->medical_name }}"
                               {{ $row->status == 1 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_{{ $row->id }}">
                            {{ $row->status == 1 ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                    @endcan
                </td>

            </tr>
            @endforeach
        @endif
        @if (count($query) == 0)
            <tr>
                <td colspan="20">
                    <span style="text-align:center">No record available</span>

                </td>
            </tr>
            @endif
    </tbody>
</table>

<div class="pull-right pegination-margin hha_appointment_paginate">
{{ $query->links() }}
</div>
