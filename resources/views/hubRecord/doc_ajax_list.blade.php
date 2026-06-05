<style>
    #dooc .table-responsive1 {
    display: block;
    width: calc(100vh - -700px);
    -webkit-overflow-scrolling: touch;
    overflow: visible !important;
}

        .table-responsive td {
    position: static !important; /* Prevent dropdown clipping */
}
#dooc .table-responsive {
    overflow: visible !important;
}

#dooc .table-responsive1 .dropdown-menu {
    position: absolute !important; /* Ensures proper rendering */
    will-change: transform; /* Fix dropdown positioning */
}
    </style>
<div id="dooc" class="table-responsive1">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="30%">Document Name</th>
                <th width="25%">Attachment</th>
                <th width="20%">Created Date/ Created By</th>
                <th width="15%">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
           
            if (count($document_list) > 0) {
                $cnt = ($document_list->currentPage() - 1) * $document_list->perPage() + 1;
                $final = [];
                foreach ($document_list as $va) {
                    $docFlagClasss = '';
                      if($va->flag =='1'){
                            $docFlagClasss = "pale-yellow-color";
                        }
                    ?>
                
                    <tr class="{{ $docFlagClasss }}">
                        <td><?php echo $cnt; ?></td>
                        <td ><?php echo $va->document_name; ?></td>
                        <td nowrap>
                            @if ($va->attachment != '' && $va->deleted_flag == 'N')
                            <a target="_blank" href="<?php echo URL::to('/'); ?>/view-hub-doc/<?php echo $va->id; ?>"><i class="fa fa-download"></i> Download</a>
                            <br>          
                            <a href="{{ url('hub-view-pdf-response')}}?id={{ $va->id}}" data-fancybox="" data-type="iframe" class="fancybox"><i class="fa fa-eye"></i>View</a>
                            @endif
                        </td>
                        
                        <td><?php echo Common::convertMDY($va->created_date); ?><br>{{ $va->first_name }} {{ $va->last_name }} </td>
                        <td style="overflow: unset !important">
                            <div class="btn-group pull-right status-dropdoown" style="margin-right:4rem" @if ($va->deleted_flag == 'Y') disabled @endif>
                                <button type="button" class="btn btn-warning" title="Status">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" @if ($va->deleted_flag == 'Y') disabled @endif id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                    <a class="dropdown-item" href="javascriopt:void(0);" onclick="deleteRecordDocument('{{$va->hub_record_id}}','{{$va->id}}')" title="Delete">Delete</a>
                                    <a class="dropdown-item" data-toggle="modal" data-target="#hub-document-add" data-service="" data-whatever="@mdo" onclick="editHubRecordDoc('{{$va->id}}','{{ $va->document_name }}')" title="Edit Service" >Edit</a> 
                                    
                                    @can('hub-flag-doc-change-status')
                                       
                                            @if($va->flag == 0)
                                                @php $flag = 'Flag'; @endphp
                                            @else
                                                @php $flag = 'Flagged'; @endphp
                                            @endif
                                            <a onclick="flagDocumentChange('{{$va->id}}');" class="dropdown-item" title="Flag">{{$flag}}</a>
                                       
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php $cnt++;
                }
            }
            if (count($document_list) == 0) { ?>
                <tr class="text-align" style="text-align: center;"> 
                    <td colspan="10"><b>Data not found</b></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="pagination pull-right pegination-margin">
            {{ $document_list->links() }}
        </div>
</div>