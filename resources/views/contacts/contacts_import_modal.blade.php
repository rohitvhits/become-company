
<div class="table-responsive no-padding">
    <input type="hidden" name="last_id" value="<?php echo $getlastId;?>">
                              <table class="table table-hover">
                                <tbody>
                                    <tr>
                                <?php 
                                
                                if(isset($import_data[0])){
                                    $j = 0;
                                foreach($import_data[0] as $rows) {	?>
                                        <th><?=$rows?> <br/>
                                        <select name="row_order[]" id="row_order<?=$j?>" class="form-control selectvalues " >
                                            <option value="">Select Option</option>
                                            <option value="name">Contacts Name</option>
                                            <option value="mobile">Mobile</option>
                                            <option value="alternative_mobile">Alternative Mobile</option>
                                            <option value="email">Email</option>
                                            
                                            <option value="notes">Notes</option>
                                            <option value="language">Language</option>
                                          
                                          
                                        </select>
                                        </th>
                                <?php  $j++; } } ?>
                                    </tr>
                                    <?php $i = 0 ;
                                    foreach($import_data as $row){ if($i != 0){ 
										if($i <=4){
										?>
                                        <tr>
                                            <?php foreach($row as $row_data) { 
                                            
                                            ?>
                                            <td><?php echo $row_data; ?></td>
                                            <?php } ?>
                                        </tr>
                                    <?php }  }$i++; } ?>
                                    
                                </tbody>
                              </table>
                            </div>
                            
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>	
        $(function(){
        $('.selectvalues').change(function(){
            console.log('here disabled select');
            if($(this).attr('id') == 'row_order0' && $(this).val() == 'Default'){
                $('.selectvalues').not(this).prop('disabled', true).val('Disabled');
            } else {
                $('.selectvalues').not(this).removeProp('disabled');
                
                $('.selectvalues option').removeProp('disabled');
                $('.selectvalues').each(function(){
                    var val = $(this).val();
                    if(val != 'Default' || val != 'Disabled'){
                        $('.selectvalues option[value="'+val+'"]').not(this).prop('disabled', true);
                    }
                });
            }
        });
     });
     </script>