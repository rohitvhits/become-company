$(function() {
  var start = moment().subtract(0, 'days');
  var end = moment();
  $('#range_date').daterangepicker({
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: 'sunday',
      ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
              'month').endOf('month')],
          'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
              .endOf('month')
          ],
          'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
              .endOf('isoWeek')
          ],
          'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
              'weeks').endOf('isoWeek')],
      }
    }, function(chosen_date, end_date) {
       console.log(chosen_date);
       console.log(end_date);
        $('#range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
        loadAgencyCount();    
    })
});

$('#range_date').change(function() {
  loadAgencyCount();
})

$(function() {
  var start = moment().subtract(0, 'days');
  var end = moment();
  $('#announcement_range_date').daterangepicker({
      startDate: start,
      endDate: end,
      autoUpdateInput: false,
      startOfWeek: 'sunday',
      ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
              'month').endOf('month')],
          'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
              .endOf('month')
          ],
          'Next Week': [moment().add(1, 'weeks').startOf('isoWeek'), moment().add(1, 'weeks')
              .endOf('isoWeek')
          ],
          'Last Week': [moment().subtract(1, 'weeks').startOf('isoWeek'), moment().subtract(1,
              'weeks').endOf('isoWeek')],
      }
    }, function(chosen_date, end_date) {
        
        $('#announcement_range_date').val(chosen_date.format('MM/DD/YYYY') + ' - ' + end_date.format(
            'MM/DD/YYYY'));
          loadAnnouncementData();    
  });
});

$('#announcement_range_date').change(function() {
  $('#announcements_section').html('');
  pages = 1;
  loadAnnouncementData();
})

function loadAgencyCount(){
    var range_date = $('#range_date').val();
    var agency_id = $('#agency_id').val();
    var user_id = $('#user_id').val();
    $('.loader-total-count').attr('style','display:flex');
    $.ajax({
        type:"GET",
        url:_TOTAL_AGENCY,
        data:{
          'range_date' : range_date,
          'agency_id' : agency_id,
          'user_id' : user_id,
        },
        success:function(res){
           
            var json = res.data;
            var total = json.totalBooked + json.totalInprogress + json.totalPending + json.totalCompleted;

            $('#total_bokked').html('<a href="'+_PATIENT_LIST_SEARCH+'?status=booked" target="_blank">'+json.totalBooked+'</a>')
            updateProgressBar('total_booked_progress',json.totalBooked,total)
            $('#total_inprogress').html('<a href="'+_PATIENT_LIST_SEARCH+'?status=processing" target="_blank">'+json.totalInprogress+'</a>')
            updateProgressBar('total_processing_progress',json.totalInprogress,total)
            $('#total_pending').html('<a href="'+_PATIENT_LIST_SEARCH+'?status=Pending" target="_blank">'+json.totalPending+'</a>')
            updateProgressBar('total_pending_progress',json.totalPending,total)
            $('#total_completed').html('<a href="'+_PATIENT_LIST_SEARCH+'?status=completed" target="_blank">'+json.totalCompleted+'</a>')
            updateProgressBar('total_completed_progress',json.totalCompleted,total)
            $('.loader-total-count').attr('style','display:none');

        }
    })
}

function loadStatisticData(page =1){
  var type = $('#type_id').val(); 
  var agency_id = $('#statistic_agency_id').val();
  var user_id = $('#user_id').val();
  $('.statistics-count').attr('style','display:flex');
  $.ajax({
      type:"GET",
      url:_URL_STATISTIC,
      data:{
        'type': type,
        'agency_id': agency_id,
        'user_id' : user_id,
        'main_type':"statistical",
        'page':page
      },
      success: function (response) {
        $('#statistic').html("")
        $('#statistic').html(response);
        $('.statistics-count').attr('style','display:none');

    }
  })
}

function loadTodayAppoitmentData(page=1){
  $('.appoitment-count').attr('style','display:flex');
  var agency_id = $('#appoitment_agency_id').val();
  var user_id = $('#user_id').val();
  $.ajax({
      type:"GET",
      url:_TODAY_APPOITMENT,
      data:{
        'agency_id': agency_id,
        'main_type':"today",
        'user_id' : user_id,
        'page':page
      },
      success: function (response) {
        $('#today_appoinment').html("")
        $('#today_appoinment').html(response);
        $('.appoitment-count').attr('style','display:none');
    }
  })
}

$('#user_id').change(function(){
  loadFuction();
  
});


function loadFuction(){
  loadAgencyCount();
  loadStatisticData();
  loadTodayAppoitmentData();
  loadEsignData();
  loadTaskData();
  loadNotesData();
  loadAnnouncementData();
  loadAgencyList();
  loadUpcommingAppoitmentData();
}
loadFuction();

$('#agency_id').change(function(){
  loadAgencyCount();
});
function loadUpcommingAppoitmentData(page=1){
  $('.appoitment-count').attr('style','display:flex');
  var agency_id = $('#appoitment_agency_id').val();
  var user_id = $('#user_id').val();
  $.ajax({
      type:"GET",
      url:_UPCOMMING_APPOITMENT,
      data:{
        'agency_id': agency_id,
        'user_id' : user_id,
        'main_type':"upcomming",
        'page':page
      },
      success: function (response) {
        $('#upcomming_appoinment').html("")
        $('#upcomming_appoinment').html(response);
        $('.appoitment-count').attr('style','display:none');
    }
  })
}

var notesPage = 1;
var notesloading = false;
$('#notes_section').on('scroll', function() {
  let div = $(this);
  // Check if the user has scrolled to the bottom of the div
  if (div.scrollTop() + div.innerHeight() + 100 >= div[0].scrollHeight && !notesloading) {
      notesPage++;
      notesloading = true;
      loadNotesData();
  }
});

$('#notes_agency_id').on('change',function(){
    var htmlResponse = '';
    notesPage = 1;
    $('#notes_section').html(htmlResponse);
});
function loadNotesData(){
  $('.notes-count').attr('style','display:flex');
  notes_agency_id = $('#notes_agency_id').val();
  var user_id = $('#user_id').val();
  $.ajax({
      type:"GET",
      url:_NOTES,
      data:{
        'notes_agency_id' : notes_agency_id,
        'page':notesPage,
        'user_id' : user_id,
      },
      success: function (response) {
    
        var htmlResponse = '';
        if(response.data.length > 0){
          htmlResponse += '<table class="table">';
          for (var i = 0; i < response.data.length; i++) {
            var urls =_URL_NOTES+"/"+response.data[i].patient_id;
                            
            htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">
  
                  <div class="ml-1">
                    <h6 class="mb-1"><a href="${urls}">Record #${response.data[i].patient_id} ${response.data[i].patient.first_name+' '+response.data[i].patient.last_name}</a></h6>
                    <p style="white-space: pre-wrap;">${response.data[i].message}</p>
                    <p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i>${response.data[i].user_details.agency_details.agency_name} ${response.data[i].created_date}</p>
                    <div class="row">
                    <p class="text-muted mb-0 tx-12" style="margin-left:12px;">${response.data[i].user_details.first_name+' '+response.data[i].user_details.last_name}</p>
                    
                    </div>
                    
                  </div>
  
                  </div>`;
          }
        }else{
          htmlResponse +=`<div class="col-md-6"><p class="text-muted mb-1"> No records found. </p></div>`;
        }
        if(notesPage == 1){
          $('#notes_section').html(htmlResponse);
        }else{
          $('#notes_section').append(htmlResponse);
          notesloading = false;
        }
        $('.notes-count').attr('style','display:none');
    }
  })
}

function loadTaskData(page=1){
  status_type = $('#status_type').val();
  $('.task-loader-count').attr('style','display:flex');
  var user_id = $('#user_id').val();
  $.ajax({
      type:"GET",
      url:_TASK,
      data:{
        'status_type': status_type,
        'main_type':"task",
        'page':page,
        'user_id' : user_id,
      },
      success: function (response) {
        $('#task_section').html("");
        $('#task_section').html(response);
        $('.task-loader-count').attr('style','display:none');
    }
  })
}

function loadEsignData(page=1){

  var user_id = $('#user_id').val();
  $('.esign-loader').attr('style','display:flex');
  $.ajax({
      type:"GET",
      url:_ESIGN_DATA,
      data:{
        'page':page,
        'main_type':'esign',
        'user_id' : user_id,
      },
      success: function (response) {
        $('#esign_section').html("")
        $('#esign_section').html(response);
        $('.esign-loader').attr('style','display:none');
    }
  })
}


$('body').on('click', '.pagination a', function(event) {
  console.log($(this).parent('li'));
  // $('li').removeClass('active');
 
  event.preventDefault();
  var page = $(this).attr('href').split('page=')[1];
  var nurl =$(this).attr('href').split('main_type=');
  var redirection = nurl[1].split('&');
  if(redirection[0] =='statistical'){
    loadStatisticData(page);
  }
  if(redirection[0] =='today'){
    loadTodayAppoitmentData(page);
  }
  
  if(redirection[0] =='upcomming'){
    loadUpcommingAppoitmentData(page);
  }
  
  if(redirection[0] =='task'){
    loadTaskData(page);
  }
  if(redirection[0] =='esign'){
    loadEsignData(page);
  }
  $(this).removeClass('.active')
  $(this).parent('li').addClass('active');
});

var pages = 1;
isLoading = false;
$('#announcements_section').on('scroll', function() {
  let div = $(this);
  // Check if the user has scrolled to the bottom of the div
  if (div.scrollTop() + div.innerHeight() <= div[0].scrollHeight && !isLoading) {
      pages++;
      isLoading = true;
      loadAnnouncementData();
  }
});

function loadAnnouncementData(){
  $('.loader-announcement').attr('style','display:flex');
  var announcement_range_date = $('#announcement_range_date').val();
  var user_id = $('#user_id').val();
  $.ajax({
      type:"GET",
      url:_ANNOUNCEMENT_DATA,
      data:{
        'announcement_range_date' : announcement_range_date,
        'page':pages,
        'user_id' : user_id,
      },
      success: function (response) {
        var htmlResponse = '';
        htmlResponse += '<table class="table">';
        var result = response.data.data;
        if(result.length > 0){
          for (var i = 0; i < result.length; i++) {
                            
            htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">
                  <div class="ml-1">
                    <p class="text-muted mb-0 tx-12">${result[i].user_details.first_name+' '+result[i].user_details.last_name}</p>
                    <b><p style="white-space: pre-wrap;">${result[i].announcement_detail.title}</p></b>
                    <div class="row" style="white-space: pre-wrap;"> <div class="col-md-12">${result[i].announcement_detail.description}</div></div>
                  </div>
                  </div>`;
          }
        }else if(pages == 1){
          htmlResponse +=`<tr><td rawspan="3"> No record Found.</td></tr>`;
        }
        if(pages == 1){
          $('#announcements_section').html(htmlResponse);
        }else{
          $('#announcements_section').append(htmlResponse);
        }
        $('.loader-announcement').attr('style','display:none');
    }
  })
}

function updateProgressBar(id,value,total) {
  console.log(id)
  // Get the input number
  const input = value;
  // Validate input (should be between 0 and 100)
  let progressValue = (value / total) * 100;
  // Ensure the percentage is between 0 and 100
  progressValue = Math.min(Math.max(progressValue, 0), 100);

  // Update the progress bar
  if(progressValue > 0){
    const progressBar = document.getElementById(id);
    progressBar.style.width = progressValue + '%';
    progressBar.setAttribute('aria-valuenow', progressValue);
    progressBar.setAttribute('title', Math.round(progressValue) + '%');
  }else{
    $('.'+id).hide();
  }
}

function loadAgencyList(){
  var user_id = $('#user_id').val();
  $.ajax({
      type:"GET",
      url:_LOAD_AGENCY,
      data:{
      
        'user_id' : user_id,
      },
      success: function (response) {
       var json = response.data;
       var htmlOption = "";
       if(json.length !=0){
        $.each(json,function(i,v){
          htmlOption +="<option value='"+v.id+"'>"+v.agency_name+"</option>";
        })
       }

       $('#agency_id').html("")
       $('#agency_id').html(htmlOption);
       $('#notes_agency_id').html("");
       $('#notes_agency_id').html(htmlOption);

       $('#statistic_agency_id').html("");
       $('#statistic_agency_id').html(htmlOption);

       $('#appoitment_agency_id').html("");
       $('#appoitment_agency_id').html(htmlOption);
       
       
      }
  })

}