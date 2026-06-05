<div class="table-responsive">
    <table id="esignTemplateId" class="table">
        <thead>
            <tr>
            <th>#</th>
            <th>Dcoument Type</th>
            <th>Template Name</th>
            <th>PDF</th>
            <th>Signer</th>
            <th>Status</th>
            <th>Created Date</th>
            <th>Created By</th>
            <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($document_list)) { $i = 1; foreach($document_list as $val){ ?>
														<tr>
															<td><?= $i++ ?></td>
															<td><?= $val->name; ?></td>
															<td><?= $val->template_name; ?></td>
															
															<td>
																<?php if($val->pdf_generate !=''){?>
																
																
															<a target="_blank" href="<?php echo URL::to('/');?>/dpe/<?php echo $val->groupId;?>"><i style="font-size: 22px;" class="mdi mdi-file-pdf"></i></a></td>
															<?php }else{?>
															<?php }?>
															</td>
															<td><?php 
																if($val->totalSigner !=0){
															?>
															<label class='badge badge-warning'>Pending</label>
															<?php } else{ ?>
															<label class='badge badge-success'>Completed</label>
															<?php } ?>
															</td>
															<td><?php echo $val->totalSigner;?></td>
															<td><?php echo date('m/d/Y',strtotime($val->created_date));?></td>
															<td><?php echo $val->first_name.' '.$val->last_name;?></td>
															<td>
															
															<?php if($val->totalSigner !=0){?>
															<a href="#" data-toggle="modal" data-whatever="@mdo" data-target="#sendRequest" onclick="getSigner('<?php echo $val->groupId;?>','<?php echo $val->id;?>','<?php echo $val->main_intakeId;?>')"><i class="fa fa-eye"></i></a>
																<?php } ?>
															</td>
															</td>
														</tr>
													
													<?php } } if(count($document_list) ==0){  ?>
													<tr><td colspan="6">No record available</td></tr>
													<?php } ?>
        </tbody>
    </table>

</div>