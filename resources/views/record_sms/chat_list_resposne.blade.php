<ul class="contact-list new_contact_list pl-0 sr-side-ul">
                                            <?php 
                                            if(count($chatRecords) >0){
                                            foreach ($chatRecords as $obj) {
                    # code...
                    ?>
                                                <li id="user-Alex"><a href="javascript:void(0)"
                                                        onclick="loadAllSMS({{ $obj->phone }})"><img alt=""
                                                            src="img/demo/envelope.png" /> <span> {{ $obj->phone }} <br />
                                                            {{ date('M d , h:i A', strtotime($obj->created_at)) }}</span></a>
                                                    <?php if ($obj->first_name == "") { ?>

                                                    <?php } ?>

                                                </li>
                                        <?php
                                            } } 
                                            ?>
                                        <?php 

                                                if(count($chatRecords) ==0){
                                    ?>
                                    <li>No record available</li>
                                    <?php } ?>
                                        </ul>
                                        <div class="">
                                            {{ $chatRecords->appends(request()->query())->links() }}
                                        </div>