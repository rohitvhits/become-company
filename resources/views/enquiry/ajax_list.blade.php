<table id="order-listing1" class="table table-bordered">
                    <thead>
                        <tr>
                            <th nowrap>No.</th>
                            <th nowrap>Email</th>
                            <th nowrap>Mobile</th>
                            <th nowrap>Subject</th>
                            <th nowrap>Message</th>
                            <th nowrap>Status</th>
                            <th nowrap>Created Date/Created By</th>
                            <th nowrap>Action</th>
                        </tr>
                    </thead>
                    <tbody id="refreshDiv">
                        <?php if ($query->total() != 0) {
                            
                            $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                            foreach ($query as $key => $row) {  ?>
                            <span style="display:none" id="{{ $row->id}}">
{{$row->message}}
                            </span>
                        <tr id="<?php echo $row->id; ?>">
                            <td nowrap><?= '#' . ' ' . $i++ ?></td>
                            <td ><?php echo $row->email; ?></td>
                            <td  ><?php echo $row->mobile; ?></td>
                            <td  nowrap><?php echo $row->subject; ?></td>
                            <td nowrap><a onclick="common('{{ $row->id}}')"><?php echo strlen($row->message) > 50 ? substr($row->message,0,50)."..." : $row->message; ?></a></td>
                            <td nowrap><?php echo $row->status; ?></td>
                            <td nowrap><?php echo date('m/d/Y h:i A',strtotime($row->created_at)); ?> <br> {{ $row->usersDetail->first_name.' '.$row->usersDetail->last_name}}</td>
                           
                            <td style="overflow: unset !important">
                            <div class="btn-group pull-right status-dropdoown mr-2">
                                <button type="button" class="btn btn-warning" title="Status">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                @can('send-enquiry-reply')
                                        <a class="dropdown-item" href="javascriopt:void(0);" onclick="sendReply('{{$row->id}}')" title="Send Reply">Send Reply</a>
                                    @endcan
                                    @can('enquiry-change-status')
                                        <a class="dropdown-item" onclick="changeStatus('{{$row->id}}')"  title="Change Status" >Change Status</a> 
                                        @endcan
                                        @can('enquiry-view-reply-log')
                                        <a class="dropdown-item" onclick="viewReplyLog('{{ $row->id}}')">View Reply Log</a>
                                        @endcan
                                        @can('enquiry-view-log')
                                        <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal-5" data-whatever="@mdo" >View Log</a>
                                        @endcan
                                    
                                </div>
                            </div>
                        </td>
                           
                        </tr>
                        <?php } ?>
                        <?php } ?>

                        <tr id="hidedis" class=" @if ($query->total() != 0) hide @else @endif">
                            <td colspan="12">
                                <center><b>Data not found</b></center>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="pull-right pegination-margin">
                    {{ $query->links('pagination::bootstrap-4') }}
                </div>