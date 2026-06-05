<div class="d-flex justify-content-between mb-3">
    <p class="card-title mb-0">POC Information </span></p> 
    <!-- <p class="mb-0 tx-13">
        <a data-toggle="modal" id="" class="pull-right btn btn-info btn-sm d-none d-md-block" onclick="openCretaeModal()" data-whatever="@mdo"><i class="mdi mdi-plus"></i>
            Cretae POC Information</a>
    </p> -->
    
</div>
<div class="row">
    <div class="col-12">
        <div class="col-12 loader-calender" id="logList8866" style="display:flex;justify-content:center;margin-top:10%">
            <img src="{{ asset('/ajax-loader.gif') }}" class="hide" alt="loader" id="loader_poc">
        </div>
    </div>
   
    <div class="col-12 hideShow hide">
        
        @php 
            $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
        @endphp
        <div class="row">
            <div class="col-md-12 poc-infos">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th>#</th>
                                <th nowrap>Code</th>
                                <th nowrap>Category <br/>Name</th>
                                <th nowrap>Task <br/> Name</th>
                                <th nowrap>As <br/> Needed</th>
                                <th nowrap>Weekly <br/> Min</th>
                                <th nowrap>Weekly<br/> Max</th>
                                <th nowrap>Instruction</th>
                                @foreach($days as $day)
                                <th nowrap>{{ $day}}</th>
                                @endforeach
                            </thead>
                            <tbody id="poc_task_details_id">
                                <tr>
                                    <td colspan="15">No record available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
            
            
        </div>
        
    </div>
    <!-- <div class="col-md-12 mt-3" id="pagin">
        <a class="pull-right btn btn-secondary btn-rounded  btn-sm" href="javascript:void(0)" id="previousId" style="display:none" onClick="previous()">Prev</a></li>
        <a class="pull-right btn btn-primary btn-rounded  btn-sm" href="javascript:void(0)" id="nextId" style="display:none"   onClick="next()">Next</a></li>
    </div> -->
</div>


