@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css')}}" />
<style>
    .actions {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.page-title-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>
<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Template List</h5>
            <div class="page-rightbtns cust-page-rightbtns">
                <div>
                @can('template-add')
                         <a href="<?php echo URL::to('/template-add'); ?>" class="btn btn-primary cust-right-btn"><i
                                 class="mdi mdi-plus"></i>Add Template</a>
                     @endcan

                     @can('template-document-type')
                         <a href="javascript::void(0)" class="btn btn-success cust-right-btn"
                             onclick="$('#document_type').modal('show')"><i class="mdi mdi-plus"></i>Document Type</a>
                     @endcan

                     

                         <a href="javascript:void(0)" id="filter-btn" class="btn cust-right-btn" style="background-color: #00879E;color:#fff;"><i class="mdi mdi-filter-outline"></i>Filter <span class="active-filter"></span></a>
                         @can('template-import')
                         <a href="{{ url('esign/esign-import')}}" class="btn btn-secondary  cust-right-btn"><i class="mdi mdi-file-export"></i>Import</a>
                         @endcan
                </div>
            </div>
        </div>
        <hr />
        <div class="row ">
            <div class="col-sm-12">
                <div id="search-filter-btn" style="display: none;">
                    <div class="card search-card1 cust-card-box" id="search-div">
                        <div class="card-body p-0 border-0 form-patient-list-box">
                            <form id="search-form">
                                <div class="row form-row-gap">
                                    
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Template Name</label>
                                                    <input type="text" name="template_name" class="form-control" id="template_name" placeholder="Enter Template Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Agency Name</label>
                                                    <select name="agency_fk[]" id="agency_fk" class="form-control select2-design cal-padding-0 js-example-basic-multiple w-100" multiple="multiple">
                                                 <?php foreach ($agencyList as $rwAgency) { ?>
                                                     <option value="<?php echo $rwAgency->id; ?>" >
                                                         <?php echo $rwAgency->agency_name; ?></option>
                                                 <?php } ?>
                                             </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Lookup Field</label>
                                                    <select name="lookup_fields" id="lookup_fields" class="form-control">
                                                        <option value="">All</option>
                                                        <option value="caregiver">Caregiver</option>
                                                        <option value="patient">Patient</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Created Date</label>
                                                    <input type="text" name="created_date" class="form-control" id="created_date" placeholder="Select Date Range" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group cust-select-box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Last Updated Date</label>
                                                    <input type="text" name="updated_date" class="form-control" id="updated_date" placeholder="Select Date Range" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="row form-row-gap mt-3">
                                <div class="col-md-9">
                                    <div class="appointment-btn-box" style="justify-content:left !important">
                                        <input type="button" name="search"
                                            class="btn search-btn1 searchAppoinment" id="search-data"
                                            value="Search" onclick="loadAjaxList()">
<a href="javascript:void(0)" class="btn btn-light cust-right-btn" onclick="refresh()"><i
                             class="mdi mdi-reload"></i>
                         Reset</a>
                                       
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader shimmer_id" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="checkbox-toggle">
                                    </th>
                                    <th>Template Name</th>
                                    <th>Agency Name</th>
                                    <th>Document Type</th>
                                    <th>Lookup Field</th>
                                    <th>Created Date / Created By</th>
                                    <th>Last Updated Date / Updated By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="shimmer-loader">
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <span id="response_requested_id">
                    
                </span>
            </div>
        </div>

    </div>
    <div class="row" id="blank_div" style='margin-top: 25px;'>
       
    </div>

</div>
<div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div id="messages_id"></div>
         </div>
     </div>
<!-- Signer Notification Modal -->
<div class="modal fade" id="signerNotificationModal" tabindex="-1" role="dialog" aria-labelledby="signerNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signerNotificationModalLabel">Sent Signer Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="signer_notification_template_id" value="">
                <div class="form-group">
                    <label class="font-weight-bold mb-3">Select Signer Types:</label>
                    <div class="row" id="signer_checkbox_container">
                    </div>
                    <div id="no_signer_msg" style="display:none;" class="text-center text-muted py-3">
                        No signers allocated to this template.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveSignerNotification()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('template._partial.document_type_modal')
@include('include/footer')

<script>
    var _LOAD_DATA_URL = "{{ url('template-ajax-list')}}";
    var _STATUS_ACTIVE_DEACTIVE ="{{ url('template-status') }}";
    var _SIGNER_NOTIFICATION_GET = "{{ url('esign/template-signer-notification') }}";
    var _SIGNER_NOTIFICATION_SAVE = "{{ url('esign/template-signer-notification-save') }}";
   
</script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.tokeninput.js')}}"></script>

<script src="{{ asset('assets/modulejs/esign_template/esign_template.js')}}?time={{ time()}}"></script>

<script src="{{ asset('assets/vendors/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/js/select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js')}}"></script>
<script>
$(document).ready(function(){
    var datePickerConfig = {
        autoUpdateInput: false,
        ranges: {
            'Today':        [moment(), moment()],
            'Yesterday':    [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month':   [moment().startOf('month'), moment().endOf('month')],
            'Last Month':   [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        }
    };

    $('#created_date').daterangepicker(datePickerConfig, function(start, end){
        $('#created_date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    });

    $('#updated_date').daterangepicker(datePickerConfig, function(start, end){
        $('#updated_date').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    });
});
</script>
<script>
    function viewAllAgency(id){
        $.confirm({
            columnClass: 'col-md-12',
            type: 'blue',
            buttons: {
                
                cancel:{
                    btnClass: 'btn-blue',
                    action: function () {
                    
                    }
                }
            },
            content: function () {
                var self = this;
                return $.ajax({
                    url: "{{ url('template-agencies-list')}}?id="+id,
                    dataType: 'json',
                    method: 'get'
                }).done(function (response) {
                    var json  = response.data;
                    var html = "<div class='row'>"
                    if(response.data.length){
                        var cnt =1;
                        $.each(json,function(i,v){
                            html +='<div class="col-md-3"><strong>'+cnt+'.</strong> '+v.name+'</div><hr>'
                            cnt++;
                        })
                    }else{
                         html +='<div >No record available</div>'
                    }
                    html +='</div>';
                    self.setContent(html);
                    self.setTitle(response.name);
                }).fail(function(xhr){
                    showErrorAndLoginRedirection(xhr);
                });
            }
        });
    }
    </script>