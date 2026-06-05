
<link href="{{ asset('css/custom.css')}}" rel="stylesheet" >
   <style>
       .recordtabletdwidth th:nth-child(9), .recordtabletdwidth td:nth-child(9){
        min-width: 115px;
    max-width: 115px;
    width: 115px;
       }
       .recordtabletdwidth th:nth-child(10), .recordtabletdwidth td:nth-child(10){
        min-width: 115px;
    max-width: 115px;
    width: 115px;
       }
       .recordtabletdwidth th:nth-child(11), .recordtabletdwidth td:nth-child(11){
        min-width: 120px;
    max-width: 120px;
    width: 120px;
       }
    </style>
<div class="table-responsive">

				  <table id="order-listing1" class="table table-bordered table-head-fix recordtabletdwidth">
                      <thead>
                        <tr>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Record #</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id_new" data-field="id" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id_new"
                                            data-field="id" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Agency Name</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id_new" data-field="agency_name" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id_new"
                                            data-field="agency_name" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            <th style="white-space:nowrap">
                                <div class="sorting-div"><span>Total No of Record</span>
                                    <div class="sorting-btn">
                                        <button type="button" class="record_id_new" data-field="total" data-sort="asc"><i
                                                class="fa fa-sort-up"></i> </button><button type="button" class="record_id_new"
                                            data-field="total" data-sort="desc"><i class="fa fa-sort-down"></i> </button>
                                    </div>
                                </div>
                            </th>
                            
                        </tr>
                        <input type="hidden" id="fields_new" value="id">
                                <input type="hidden" id="sort_new" value="desc">
                            
						
                      </thead>
                      <tbody>
				
                  
                      	
			           
                      <?php 
									
                                    if(count($query) > 0) {
                                        $i = 1 +(($query->currentPage()-1) * $query->perPage());
                                    
                                        foreach ($query as $row) { ?>
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                
                                                <td>{{ $row->agency_name}}</td>
                                                <td>{{ $row->total}}</td>
                                            </tr>
                                    <?php } }else { ?>
                                            <tr><td colspan="3"><center><b>Data not found</b></center></td></tr>
                                    <?php }?>
							</tbody>
                    </table>

                    
                   
                  </div>
                  <div class="pull-right pegination-margin">
                  {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
                

                    </div>
                    