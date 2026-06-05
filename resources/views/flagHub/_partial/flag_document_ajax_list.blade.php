<style>

</style>
<div class="tableData table-responsive">
    <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
        <thead>
            <tr>
                <th nowrap>#</th>
                <th nowrap>Hub Record ID</th>
                <th nowrap>Document Name</th>
                <th nowrap> Attachment </th>
                <th nowrap> Created Date/Created By </th>
                <th nowrap> Reason </th>
            </tr>
        </thead>
        <tbody>
          
            @php
            $i = 1 + ($query->currentPage() - 1) * $query->perPage();
            @endphp
            @if (count($query) > 0)
            @foreach ($query as $key => $va)
            @if($va->is_flag_read == 0)
                @php
                   
                    $clickHtml = "makeFlagRead('" . addslashes($va->flag_id) . "', '". addslashes($va->hub_record_id) ."','Document');";
                    $href = "javascript:void(0)";
                    $target = '';
                @endphp
            @else
                @php
                    $clickHtml = "";
                    $href = url('hub-record/view/' . $va->hub_record_id);
                    $target = '_blank';
                @endphp
            @endif
            <tr>
            <div id="preasondoc{{ $key}}" style="display:none">
                    {{ $va->reasonNotes}}
                </div>
                <td nowrap>{{ $key+1 }}</td>
                <td>
                    <a target="{{$target}}" onclick="{{$clickHtml}}" href="{{$href}}"><?= '#' . '' . $va->hub_record_id ?></a>
                    @if($va->is_flag_read == 0)
                        <div style="position:relative"><span class="add_new_record left_record" >New</span></div>
                    @endif
                </td>
                <td nowrap>{{ $va->document_name }}</td>
              
                <td nowrap>
                    @if ($va->attachment != '' && $va->deleted_flag == 'N')
                        <a target="_blank" href="{{ url('/view-hub-doc') }}/{{$va->id}}"><i class="fa fa-download"></i> Download</a>
                        <br>          
                        <a href="{{ url('hub-view-pdf-response')}}?id={{ $va->id}}" data-fancybox="" data-type="iframe" class="fancybox"><i class="fa fa-eye"></i>View</a>
                    @else
                        @if( $va->deleted_flag != 'Y')
                            <a data-toggle="modal" data-target="#exampleModal-upload-doc" data-whatever="@mdo" onclick="getUploadDocument('{{ $va->id }}')"><i class="fa fa-upload"></i> Upload document </a>
                        @endif
                    @endif
                </td>
                <td nowrap>
                    {{ Common::convertMDY($va->created_date) }} <br> {{ $va->first_name }} {{ $va->last_name }} 
                </td>
                <td nowrap title ="{{ $va->reasonNotes }}">
                @php 
                $out = strlen($va->reasonNotes) > 50 ? substr($va->reasonNotes,0,50)."..." : $va->reasonNotes;
                @endphp
               
                <a href="javascript:void(0)"  style="text-decoration: none; color: inherit;" onclick="patientDocumentReasonDescription('{{  $key}}')">{{$out}} </a></td>
            </td>
            </tr>
            @endforeach
            @endif
            @if (count($query) == 0)
            <tr>
                <td colspan="7">
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