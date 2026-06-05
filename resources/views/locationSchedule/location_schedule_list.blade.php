@include('include/header')
@include('include/sidebar')
<style type="text/css">
     #order-listing_length,
     #order-listing_paginate,
     #order-listing_info {
         display: none;
     }

     #order-listing_filter {
         text-align: right;
     }

     .select2-design+.select2.select2-container.select2-container--default {
         width: 100% !important;
     }

     td {
         table-layout: fixed;
         width: 20px;
         overflow: hidden;
         word-wrap: break-word;
     }

     .table-width1 tr th:first-child {
         width: 3%;
     }
     .table-width1 tr th:nth-child(3) {
         width: 12%;
     }
     .table-width1 tr th:nth-child(4) {
         width: 12%;
     }

     .table-width1 {
         background-color: #fff;
     }

     .search-inner {
         display: flex;
         justify-content: space-between;
         padding-top: 10px;
         padding-right: 20px;
         padding-left: 20px;
     }

     .page-title-main {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 20px;
     }

     .srch-icon {
         padding: 0 !important;
         width: 40px;
         height: 40px;
     }
 </style>
 <link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
 <div class="main-panel main-page-box">
     @php
         $auth = auth()->user();
         $permissions = auth()->user()->getAllPermissions()->pluck('name');
     @endphp
     <div class="content-wrapper content-wrapper-box">
         <div class="page-title-main">
             <h5 class="mb-1 font-weight-bold">Location Schedule</h5>
             <div class="page-rightbtns cust-page-rightbtns mb-1">
                 <div>
                    @canany(['add-location-schedule', 'Add Location Schedule'])
                        <a href="{{ url('location-schedule/add/')}}/<?php echo $id; ?>" class="btn btn-primary cust-right-btn"><i class="mdi mdi-plus"> </i> Add Schedule </a>
                    @endcanany
                    @canany(['copy-location-schedule', 'Copy Location Schedule'])
                        <a href="javascript:void(0)" class="btn btn-info cust-right-btn" data-toggle="modal" onclick="copyData()" data-whatever="@mdo" title="Add Copy"> <span class="loader"></span> Add Copy </a>
                    @endcanany
                    <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                 </div>
             </div>
         </div>

         <div class="col-12 grid-margin-top">
             @if (Session::has('success'))
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('success') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
             @if (Session::has('error'))
                 <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <strong>{{ Session::get('error') }}</strong>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
             @endif
         </div>
     

         <div class="row">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card mb-3 search-card1 cust-card-box" id="search-div">
                        <div class="card-body">
                            <form action="{{ url('/location-schedule')}}/{{$id}}" method="GET" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="day">Day</label>
                                            <select name="day" id="day" class="form-control">
                                                <option value="">All Days</option>
                                                <option value="monday" @if(request('day') == 'monday') selected @endif>Monday</option>
                                                <option value="tuesday" @if(request('day') == 'tuesday') selected @endif>Tuesday</option>
                                                <option value="wednesday" @if(request('day') == 'wednesday') selected @endif>Wednesday</option>
                                                <option value="thursday" @if(request('day') == 'thursday') selected @endif>Thursday</option>
                                                <option value="friday" @if(request('day') == 'friday') selected @endif>Friday</option>
                                                <option value="saturday" @if(request('day') == 'saturday') selected @endif>Saturday</option>
                                                <option value="sunday" @if(request('day') == 'sunday') selected @endif>Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="start_time">Start Time</label>
                                            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ request('start_time') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="end_time">End Time</label>
                                            <input type="time" name="end_time" id="end_time" class="form-control" value="{{ request('end_time') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">All Status</option>
                                                <option value="N" @if(request('status') == 'N') selected @endif>Enabled</option>
                                                <option value="Y" @if(request('status') == 'Y') selected @endif>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-filter"></i> Filter
                                                </button>
                                                <a href="{{ url('/location-schedule')}}/{{$id}}" class="btn btn-secondary btn-sm">
                                                    <i class="mdi mdi-refresh"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-12">
                 <table id="order-listing1" class="table table-bordered table-width1">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Day</th>
                             <th>Start Time</th>
                             <th>End Time</th>
                             <th>Status</th>
                             <th>Slot</th>
                             <th>Action</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php if ($query->total() != 0) {
                        $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                        foreach ($query as $row) {  ?>
                         <tr>

                             <td><?php echo $i++; ?></td>
                             <td><?php echo ucfirst($row->day); ?></td>
                             <td><?php echo date('h:i A', strtotime($row->start_time)); ?></td>
                             <td><?php echo date('h:i A', strtotime($row->end_time)); ?></td>
                           
                             <td id="row_{{ $row->id}}">
                                @if($row->disable_status =='N')
                                    <span class="badge badge-success">Enabled</span>
                                @else
                                <span class="badge badge-danger">Disabled</span>
                                @endif
                             </td>
                             <td>{{ $row->slot}}</td>
                             <td>
                             @canany(['edit-location-schedule', 'Edit Location Schedule'])
                            
                                 <a
                                     href="{{ url('location-schedule/edit')}}/<?php echo $row->location_id; ?>/<?php echo $row->id; ?>"><i
                                         class="fa fa-edit"></i></a>
                            @endcanany
                            @canany(['delete-location-schedule', 'Delete Location Schedule'])
                           
                                 <a href="{{ url('location-schedule/delete')}}/<?php echo $row->location_id; ?>/<?php echo $row->id; ?>"
                                     onclick="return confirm('Are you sure remove this record?')"><i
                                         class="fa fa-trash"></i></a>
                            @endcanany
                            @canany(['change-status-location-schedule', 'Change Status Location Schedule'])
                           
                                <label class="toggle-switch toggle-switch-success">
                                    <input type="checkbox" data-last-status="{{ $row->disable_status}}" data-id="{{ $row->id}}" id="row_last_status{{ $row->id}}" name="is_disabled" value="1" @if($row->disable_status =='N') checked @endif class="smsEnableDisabled">
                                    <span class="toggle-slider round"></span>
                                </label>
                            @endcanany
                             </td>
                         </tr>
                         <?php }
                      } else { ?>
                         <tr>
                             <td colspan="12">
                                 <center><b>Data not found</b></center>
                             </td>
                         </tr>
                         <?php } ?>
                     </tbody>
                 </table>
                 <div class="pull-right pegination-margin">
                     {{ $query->appends(request()->query())->links('pagination::bootstrap-4') }}
                 </div>
             </div>
         </div>

         <div class="content-wrapper">
             <div class="col-12 "></div>
             <div class="card">
                 <div class="row list-name m-3">
                     <div class="col-sm-6 card-title">
                         <h4 class="card-title">Location Schedule Logs</h4>
                     </div>
                 </div>
                 <div class="card-body">
                     <div class="row">
                         <div class="col-12" id="logList">

                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     @include('auditLogReport/log_modal')
     @include('include/footer')
     
    
     <link rel="stylesheet" href="{{ asset('css/jquery-ui.css')}}">
     <script src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
     <script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
     <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
     
     <script>
        $("#filter-btn").click(function() {
            $("#search-filter-btn").slideToggle(600);
        });
         $(document).on('click', '.log-pegination .pagination a', function(event) {
             $('li').removeClass('active');
             $(this).parent('li').addClass('active');
             event.preventDefault();
             var myurl = $(this).attr('href');
             var page = $(this).attr('href').split('page=')[1];
             getData(page);
             
         });

         $(document).ready(function() {
             $('.nav-item').removeClass('active');
             getData(1);
         });

         function getData(page) {

             var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

             $.ajax({
                 method: 'GET',
                 url: "{{ url('schedule-view-logs') }}" + "?page=" + page,
                 data: {
                     'id': "{{ $id }}",
                     '_token': "{{ csrf_token() }}"
                 },
                 success: function success(response) {

                     $('.order-listing-loader').attr('style', 'display:none');
                     $('#logList').html("");
                     $('#logList').html(response);
                 },
                 error: function error(_error) {
                     console.error(_error);
                     toastr.error('Something happened. Try again');
                 }
             });
         }

         function copyData(){
            $.confirm({
                title: 'Confirm Copy Location Schedule',
                columnClass: "col-md-6",
                content: 'Do you want to Copy the location schedule?<br><br><b>Note:</b><ul class="note-list"><li>Default 15-minute slots will be created.</li><li>Existing slots will be updated based on the schedule.</li><li>Example: 09:00–09:15, 09:15–09:30, etc.</li> </ul>',
                type: 'blue',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-blue',
                        action: function () {
                            $.ajax({
                                method: 'GET',
                                url: "{{ url('copy-schedule') }}",
                                data: {
                                    'id': "{{ $id }}",
                                    
                                },
                                success:function(data){
                                    toastr.success(data.error_msg);
                                    setTimeout(function(){
                                        location.reload();
                                    },1000);
                                }, 
                                error:function(jqr){
                                
                                    toastr.error('Sorry, something went wrong. Please try again.')
                                }

                            })
                        }
                    },
                    cancel:{
                        text: 'Cancel',
                    }
                }
            });
            
         }

         $('.smsEnableDisabled').change(function(e){
            var id = $(this).attr('data-id');
            var row_last_status = $('#row_last_status'+id).attr('data-last-status');
            var enabledStatus ="Enabled";
            if(row_last_status =="N"){
                enabledStatus ="Disabled";
            }
            $.confirm({
                title: 'Are you sure?',
                columnClass: "col-md-6",
                content: 'You want to ' + enabledStatus + ' location schedule?',
                type: 'blue',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-blue',
                        action: function () {
                            $.ajax({
                                method: 'GET',
                                url: "{{ url('schedule-enabled-disabled') }}",
                                data: {
                                    'id': id,
                                    
                                },
                                success:function(data){
                                    toastr.success(data.message);
                                    var status ="";
                                    if(data.data.status =="N"){
                                        status ="<span class='badge badge-success'>Enabled</span>";
                                    }else{
                                        status ="<span class='badge badge-danger'>Disabled</span>"; 
                                    }
                                    $('#row_'+id).html(status)
                                    $('#row_last_status'+id).attr('data-last-status',data.data.status);
                                },
                                error:function(jqr){
                                
                                    toastr.error('Sorry, something went wrong. Please try again.')
                                }

                            })
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                       
                        action: function () {
                            if(row_last_status =="N"){
                                $('#row_last_status'+id).prop("checked",true);
                            }else{
                                $('#row_last_status'+id).prop("checked",false);
                            }
                        }
                    }
                }
            });
         })
         function viewLog(id){
        $.ajax({
            url: "{{ url('get-audit-view-log')}}",
            data: {
                id: id
            },
            success: function(res){
                let old_response = res.data.old_response;
                let new_response = res.data.new_response;
                $('#log-model').modal('show');
                let content = '';
                content += `<div class=\"row\">`;
                content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-primary text-white\" style="padding:10px !important"><b>Old Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
                content += highlightJson(old_response);
                content += `</div></div></div>`;
                content += `<div class=\"col-md-6\"><div class=\"card\"><div class=\"card-header bg-success text-white\"  style="padding:10px !important"><b>New Response</b></div><div class=\"card-body\" style=\"max-height:400px;overflow-y:auto;overflow-x:hidden;\">`;
                content += highlightJson(new_response);
                content += `</div></div></div>`;
                content += `</div>`;
                $('.dataContainer').html(content);
            }
        });
}

function highlightJson(jsonInput) {
    if (!jsonInput) return '<pre style="word-break:break-all;white-space:pre-wrap;">-</pre>';
    let obj;
    if (typeof jsonInput === 'string') {
        try {
            obj = JSON.parse(jsonInput);
        } catch (e) {
            // If not JSON, just show as text
            return '<pre style="word-break:break-all;white-space:pre-wrap;">' + jsonInput + '</pre>';
        }
    } else if (typeof jsonInput === 'object') {
        obj = jsonInput;
    } else {
        return '<pre style="word-break:break-all;white-space:pre-wrap;">' + String(jsonInput) + '</pre>';
    }
    let pretty = JSON.stringify(obj, null, 4);
    // Basic syntax highlighting
    pretty = pretty.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    pretty = pretty.replace(/("[^"]+": )/g, '<span style="color:#007bff;">$1</span>'); // keys
    pretty = pretty.replace(/(:\s?)("[^"]*")/g, '$1<span style="color:#28a745;">$2</span>'); // string values
    pretty = pretty.replace(/(:\s?)(\d+\.?\d*)/g, '$1<span style="color:#d18f00;">$2</span>'); // numbers
    pretty = pretty.replace(/(:\s?)(true|false|null)/g, '$1<span style="color:#aa0d91;">$2</span>'); // booleans/null
    return '<pre style="word-break:break-all;white-space:pre-wrap;">' + pretty + '</pre>';
}
     </script>