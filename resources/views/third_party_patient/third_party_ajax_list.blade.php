<input type="hidden" id="third_party_doc_id" value="{{ $searchData['doc_id'] ?? '' }}">
<div class="row">
    <div class="col-12">
        <div class="tableData">
            <table id="order-listing1" class="table table-bordered table-width1">
                <thead>
                    <tr>
                        <th nowrap>Record Id</th>
                        <th nowrap>Name/Mobile/DOB/Services/Gender</th>
                        <th nowrap>Service Name</th>
                        <th nowrap>Service Status</th>
                        <th nowrap>Created Date</th>
                        <th nowrap>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($query) > 0)
                        @foreach ($query as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>
                                    {{ $row->first_name }} {{ $row->last_name }}<br />
                                    {{ $row->mobile }}<br />

                                    @if(isset($row->dob) && $row->dob != '0001-01-01' && $row->dob !="0000-00-00" && $row->dob != '1000-01-01')
                                        {{ date('m/d/Y', strtotime($row->dob)) }}<br />
                                    @endif
                                    {{$row->gender}}<br />
                                </td>
                                <td>{{ $row->serviceName }}</td>
                                <td>
                                    @if(isset($row->serviceDetails->status) && strtolower($row->serviceDetails->status))
                                        {{ ucfirst($row->serviceDetails->status)}}
                                    @endif
                                </td>
                                <td>{{ date('m/d/Y h:i A', strtotime($row->created_date)) }}</td>
                                <td>
                                   <input type="checkbox" name="link_third_party[]" id="va{{ $row->id}}" class="form-check-input cbox ml-0" value="{{ $row->id }}" data-service-id="{{ $row->service_id }}" data-service-request-id="{{ $row->requested_service_id}}">
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="15">
                                <center><b>Data not found</b></center>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
