<div class="table-responsive ">
    <table id="" class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap>Document Name</th>
                <th nowrap>Requested Id</th>
                <th nowrap>Attachment</th>
                <th nowrap>Attachment Service</th>
                <th nowrap>Document Completion Date</th>
                <th nowrap>Created Date/ Created By</th>
            
                <!-- <th>Last Updated By</th> -->
                @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                <th>Action</th>
                @endif

            </tr>
        </thead>
        <tbody>
            <?php
            if (count($document_list) > 0) {
                $cnt = 1;
                $final = [];
                foreach ($document_list as $va) {
                    if(!empty($va->services[0])){
                        foreach($va->services as $srd){
                            $final[] = $srd->id;
                        }
                    }
                    $color = $va->deleted_flag == 'Y' ?     'deleted' : '';
            ?>

                    <tr class="{{ $color }}">
                        <input type="hidden" name="src{{$va->id}}" value="<?php echo json_encode($final);?>"> 
                        <td><?php echo $cnt; ?></td>
                        <td ><?php echo $va->document_name; ?></td>
                        <td ><?php echo $va->request_service_id; ?></td>
                        <td nowrap>
                            @if ($va->attachment != '' && $va->deleted_flag == 'N')
                            <a target="_blank" href="<?php echo URL::to('/'); ?>/dpp/<?php echo $va->id; ?>"><i class="fa fa-download"></i> Download</a>
                            <br>          
                            <a href="{{ url('view-pdf-response')}}?id={{ $va->id}}" data-fancybox="" data-type="iframe" class="fancybox"><i class="fa fa-eye"></i>View</a>
                            @else
                            @if( $va->deleted_flag != 'Y')
                            <a data-toggle="modal" data-target="#exampleModal-upload-doc" data-whatever="@mdo" onclick="getUploadDocument('{{ $va->id }}')"><i class="fa fa-upload"></i> Upload document </a>
                            @endif
                            @endif
                        </td>
                        <td>
                            <?php 
                            $serviceArray = [];
                                $names = array('badge-primary','badge-success','badge-info','badge-warning','badge-dark');
                                if(!empty($va->services[0])){
                                    $tempCountercc = 0;
                                    foreach($va->services as $srv){
                                        $serviceArray[] = $srv->id;
                                        ?>
<span
class="badge <?php echo $names[$tempCountercc % count($names)]; ?>"><?php echo $srv->name; ?></span>
                                    <?php 
                                    $tempCountercc++;
                                    }
                                }
                            ?>
                        </td>
                        <td>
                        @if($va->document_completed_date !="")
                        {{Common::convertMDY($va->document_completed_date)}}
                        @endif
                        
                        </td>
                        <td><?php echo Common::convertMDY($va->created_date); ?><br>{{ $va->first_name }} {{ $va->last_name }} </td>


                        @if (auth()->user()->login_type_fk != 2 && auth()->user()->user_type_fk != 6)
                        <td>

                            @if ($va->deleted_flag != 'Y')
                            <a href="javascriopt:void(0);" onclick="deleteRecordDocument('{{$record->id}}','{{$va->id}}')" title="Delete"><i class="fa fa-trash-o"></i></a>
                            @else
                            <a href="javascript:void(0)">Deleted</a>
                            @endif
                            <input type="hidden" name="existing_services" id="ser{{ $va->id}}" value="<?php echo json_encode($serviceArray);?>">
                            @if($va->request_service_id !="")
<a data-toggle="modal" data-target="#edit-exampleModal-services" data-service="" data-whatever="@mdo" onclick="editPatientRequestService('{{ $va->request_service_id}}','{{$va->id}}','{{ $va->document_name }}','{{ $va->document_completed_date }}')" title="Edit Service" ><i class="fa fa-edit"></i></a> 
                            @else
<a data-toggle="modal" data-target="#edit-exampleModal-services" data-service="<?php echo json_encode($serviceArray);?>" data-whatever="@mdo" onclick="viewServices('{{ $va->id}}');editPatientRequestService('{{ $va->request_service_id}}','{{$va->id}}','{{ $va->document_name }}','{{ $va->document_completed_date }}')" title="Edit Service" ><i class="fa fa-edit"></i></a> 
                            @endif
                    
                            <?php
                            if (isset($record->inflowcare_id) && $record->inflowcare_id != '') {
                            ?>
                                <a data-toggle="modal" data-target="#exampleModal-5" data-whatever="@mdo" onclick="getEditDocument('{{ $va->id }}','{{ $va->document_name }}')"><i class="fa fa-edit"></i></a>
                            <?php } ?>
                            <div class="btn-group pull-right status-dropdoown mr-2">
                                <button type="button" class="btn btn-warning" title="Status">Action</button>
                                <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton6">
                                    @if($record->type == "Caregiver")
                                   
                                        @if ($va->attachment != '' && isset($agencyDetails->app_name) &&
                                            $agencyDetails->app_name != '' &&
                                                $user['user_type_fk'] == 184 &&
                                                ($va->uploaded_to_hha == 0 || true ) && ( $record->hha_id !='' || $record->link_hha_caregiver !="") )

                                            <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal-hha-update" data-whatever="@mdo" onclick="getMedicalResult({{ $record->agency_id }},{{ $va->id }})">Update HHX</a>
                                        @endif
                                        @if ($va->attachment != '' && isset($agencyDetails->app_name) &&
                                            $agencyDetails->app_name != '' && $user['user_type_fk'] == 184 &&
                                                $va->uploaded_complience_hha == 0 &&( $record->hha_id !='' || $record->link_hha_caregiver) )

                                            <a class="dropdown-item" data-toggle="modal" data-target="#other-complience-hha-update" data-whatever="@mdo" onclick="getOtherMedicalResult({{ $record->agency_id }},{{ $va->id }})">Other Complience Update HHX</a>
                                        @endif
                                    @else
                                        @if ($va->attachment != '' && isset($agencyDetails->app_name) && $agencyDetails->app_name != '' && $user['user_type_fk'] == 184 && $va->uploaded_to_hha == 0 &&( $record->hha_id !='' || $record->link_hha_caregiver) )
                                        <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal-hha-update-patient" data-whatever="@mdo" onclick="uploadPatientDocToHHA('{{ $record->agency_id }}','{{ $va->id }}')">Upload patient document to HHA</a>
                                        @endif
                                    @endif
                                        @if ($va->attachment != '' && $record->alaycare_id !="")
                                        <a class="dropdown-item" data-toggle="modal" onclick="uploadToAlayaCare('{{ $record->alayacare_id }}','{{ $va->attachment }}')">Send To AlayaCare</a>
                                        @endif
                                    @can('document-send-mail')
                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" onclick="showMailDocument('{{$va->id}}')" data-whatever="@mdo" data-target="#exampleModal-send-mail">Send Mail</a>
                                    @endcan

                                    @can('link-to-visiting-aid')
                                    
                                    <!-- <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" onclick="requestsServices()" data-whatever="@mdo" data-target="#ModalLinkToVisitingAid">Link To Visiting Aid</a>
                                     -->
                                    @endcan
                                </div>
                            </div>


                        </td>
                        @endif
                    </tr>
                <?php $cnt++;
                }
            }
            if (count($document_list) == 0) { ?>
                <tr>
                    <td colspan="8"> Data not found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="pull-right pegination-margin">
    </div>
</div>