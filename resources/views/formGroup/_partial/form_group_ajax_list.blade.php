<style>

</style>
<div class="">
    <table id="sortableTable" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
                @foreach ($query as $row)
                    <tr>
                        <td><span id="rowIndex">{{ $i++ }}</span></td>
                        <td>{{ $row->title }}</td>
                        <td>
                            @can('form-group-show')
                                <a href="javascript:void(0);" data-eid="{{ $row->id }}" data-name="{{ $row->label }}"
                                    class="pull-left ml-1 viewData">
                                    <i class="fa fa-eye"></i>
                                </a>
                            @endcan
                            @can('form-group-edit')
                                <a href="javascript:void(0);" data-eid="{{ $row->id }}" data-name="{{ $row->label }}"
                                    class="pull-left ml-1 editData">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endcan
                            @can('form-group-delete')
                                <a class="pull-left ml-1 deleteData" href="javascript:void(0)"
                                    data-did="{{ $row->id }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endcan
                            <input class="sortID" type="hidden" name="sortID[]" value="" />
                            <input class="formFieldsID" type="hidden" name="formFieldsID[]" value="{{ $row->id }}" />
                            <input class="formID" type="hidden" name="formID[]" value="{{ $form_id }}" />
                        </td>
                    </tr>
                @endforeach
            @endif
            @if (count($query) == 0)
                <tr>
                    <td colspan="5">
                        <center><b>Data not found</b></center>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<div class="pull-right pegination-margin">
    {{ $query->appends(request()->input())->links('pagination::bootstrap-4') }}
</div>
