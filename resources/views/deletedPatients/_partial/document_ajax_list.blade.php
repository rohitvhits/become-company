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
                <th>#</th>
                <th nowrap>Document Name</th>
                <th nowrap>Requested Id</th>
               
                <th nowrap>Attachment Service</th>
                <th nowrap>Document Completion Date</th>
             
                <th nowrap>Created Date/ Created By</th>
            

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
                    $docFlagClasss = '';
                    if($va->deleted_flag == 'N'){
                       
                        if($va->flag =='1'){
                            $docFlagClasss = "pale-yellow-color";
                        }if($va->flag =='0'){
                            $docFlagClasss = '';
                        }
                    }
            ?>

                    <tr class="{{ $color }} {{$docFlagClasss}}">
                        <input type="hidden" name="src{{$va->id}}" value="<?php echo json_encode($final);?>"> 
                        <td><?php echo $cnt; ?>
                        @if($record->id !=  $va->patient_id)
                        <span style="margin-left:10px;top: 0;background: #00BBE0;padding: 1px 5px;font-size: 10px;color: #fff;border-radius: 2px 2px 2px 2px;font-size: 10px !important;">Merge</span>
                        @endif
                    </td>
                        <td ><?php echo $va->document_name; ?></td>
                        <td ><?php echo $va->request_service_id; ?></td>
                       
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