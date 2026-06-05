<style>

</style>
<div class="">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="25%">Title</th>
                <th width="25%"> Type </th>
                <th width="25%"> Created Date/ Created By </th>
                <th width="15%"> Action </th>
            </tr>
        </thead>
        <tbody>
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $row)
            <tr>
                <td>{{ $i++}}</td>
                <td>{{ $row->title}}</td>
                <td>
                    @php $types = explode(',',$row->type); @endphp
                    @if(in_array(0,$types))
                        @php $type = 'All'; @endphp
                        <span class="badge badge-primary">{{$type}}</span>
                    @endif
                    @if(in_array(1,$types))
                        @php $type = 'Super Admin'; @endphp
                        <span class="badge badge-success">{{$type}}</span>
                    @endif
                    @if(in_array(2,$types))
                        @php $type = 'NyBest User'; @endphp
                        <span class="badge badge-info">{{$type}}</span>
                    @endif
                    @if(in_array(3,$types))
                        @php $type = 'Agency User'; @endphp
                        <span class="badge badge-danger">{{$type}}</span>
                    @endif
                </td>
                <td>{{ date('m-d-Y',strtotime($row->created_at))}} <br> {{ $row->first_name}}  {{ $row->last_name}}</td>
                <td> 
                    
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#videoModal" onclick="viewEbook('{{$row->id}}')"><i class="fa fa-eye"></i></a>
                  
                    @if(Auth()->user()->id == 482)
                        @can('ebook-edit')
                            <a href="javascript:void(0)" onclick="getEditEbook('{{$row->id}}')"><i class="fa fa-edit"></i></a>
                        @endcan
                        @can('ebook-delete')
                            <a href="javascript:void(0)" onclick="deleteEbook('{{$row->id}}')"><i class="fa fa-trash"></i></a>
                        @endcan
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="8">
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