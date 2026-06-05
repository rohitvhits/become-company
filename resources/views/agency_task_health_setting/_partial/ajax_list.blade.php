<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 20px;">#</th>
            <th nowrap>ID</th>
            <th nowrap>Agency Name</th>
            <th nowrap>Email</th>
            <th nowrap>Phone</th>
            <th nowrap>Configure</th>
        </tr>
    </thead>
    <tbody>
        @if($query->total() != 0)
            @php
                $i = 1 + (($query->currentPage() - 1) * $query->perPage());
            @endphp

            @foreach($query as $row)
                @php
                    $rowSettings = [];
                    foreach($settingFields as $sf) {
                        $rowSettings[$sf['field']] = ($row->{$sf['field']} ?? 0) ? 1 : 0;
                    }
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td><a href="{{ url('agency-view/') }}/{{ $row->id }}"># {{ $row->id }}</a></td>
                    <td style="white-space: nowrap;"><a href="{{ url('agency-view/') }}/{{ $row->id }}">{{ ucwords($row->agency_name) }}</a></td>
                    <td style="white-space: nowrap;">{{ $row->email }}</td>
                    <td style="white-space: nowrap;">{{ $row->phone }}</td>

                    <td class="text-center">
                        <button type="button"
                            class="btn btn-sm agency-configure-btn"
                            style="background:#00879E;color:#fff;border-radius:6px;font-size:12px;padding:4px 10px;"
                            data-agency="{{ $row->id }}"
                            data-sha1="{{ sha1($row->id) }}"
                            data-name="{{ ucwords($row->agency_name) }}"
                            data-settings="{{ json_encode($rowSettings) }}"
                            data-poc-doc-id="{{ $row->poc_document_type_id ?? '' }}"
                            data-poc-doc-name="{{ $row->poc_document_type_name ?: ($row->poc_document_type_id ? 'ID:'.$row->poc_document_type_id : 'Not Set') }}"
                            data-sup-doc-id="{{ $row->supervision_document_type_id ?? '' }}"
                            data-sup-doc-name="{{ $row->supervision_document_type_name ?: ($row->supervision_document_type_id ? 'ID:'.$row->supervision_document_type_id : 'Not Set') }}"
                            data-assessment-doc-id="{{ $row->patient_assessment_document_type_id ?? '' }}"
                            data-assessment-doc-name="{{ $row->patient_assessment_document_type_name ?: ($row->patient_assessment_document_type_id ? 'ID:'.$row->patient_assessment_document_type_id : 'Not Set') }}"
                            data-package-doc-id="{{ $row->patient_package_document_type_id ?? '' }}"
                            data-package-doc-name="{{ $row->patient_package_document_type_name ?: ($row->patient_package_document_type_id ? 'ID:'.$row->patient_package_document_type_id : 'Not Set') }}"
                            data-cms485-doc-id="{{ $row->cms_485_document_type_id ?? '' }}"
                            data-cms485-doc-name="{{ $row->cms_485_document_type_name ?: ($row->cms_485_document_type_id ? 'ID:'.$row->cms_485_document_type_id : 'Not Set') }}"
                            data-kardex-doc-id="{{ $row->emergency_kardex_document_type_id ?? '' }}"
                            data-kardex-doc-name="{{ $row->emergency_kardex_document_type_name ?: ($row->emergency_kardex_document_type_id ? 'ID:'.$row->emergency_kardex_document_type_id : 'Not Set') }}"
                            data-poc-notes="{{ $row->poc_group_notes ?? '' }}">
                            <i class="mdi mdi-cog-outline"></i> Configure
                        </button>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" class="text-center">No records found.</td>
            </tr>
        @endif
    </tbody>
</table>

@if($query->total() != 0)
    <div class="d-flex justify-content-end mt-2">
        {{ $query->links() }}
    </div>
@endif
