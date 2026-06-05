loadAgencyDashboardCount();
loadStatisticData();
loadLocationsData();
loadTodayAppoitmentData(1);

$('#agency_id').change(function(){
  loadAgencyCount();
});

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
  announcementPage = 1;
  loadAnnouncementData();
})


function loadAgencyDashboardCount(){
    $('.total-listing-loader1').attr('style','display:flex');
    $.ajax({
        type:"GET",
        url:_TOTAL_AGENCY,
        success:function(res){
            var json = res.data;
            console.log(json.totalPatients);
            $('#total_patients').html(json.totalPatients)
            $('#total_caregiver').html(json.totalCaregiver)
            $('.total-listing-loader1').attr('style','display:none');

        }
    })
}
var notesPage = 1;
isNotesLoading = false;
$('#notes_section').on('scroll', function() {
  let div = $(this);
  // Check if the user has scrolled to the bottom of the div
  if (div.scrollTop() + div.innerHeight() <= div[0].scrollHeight && !isNotesLoading) {
      notesPage++;
      isNotesLoading = true;
      loadNotesData();
  }
});

function loadNotesData(){
  $('.all-notes-loader').attr('style','display:flex');
  notes_agency_id = $('#notes_agency_id').val();
  $.ajax({
      type:"GET",
      url:_NOTES,
      data:{
        'notes_agency_id' : notes_agency_id,
        'page': notesPage
      },
      success: function (response) {
        var htmlResponse = '';
        var result = response.data.data;
        if(result.length > 0){
          htmlResponse += '<table class="table">';
          for (var i = 0; i < result.length; i++) {
            var urls =_URL_NOTES+"/"+result[i].patient_id;
                            
            htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">  
                  <div class="ml-1">
                    <h6 class="mb-1"><a href="${urls}">Record #${result[i].patient_id} ${result[i].patient.first_name+' '+result[i].patient.last_name}</a></h6>
                    <p style="white-space: pre-wrap;">${result[i].message}</p>
                    <p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i>${result[i].created_date}</p>
                    <div class="row">
                    <p class="text-muted mb-0 tx-12" style="margin-left:12px;">${result[i].user_details.first_name+' '+result[i].user_details.last_name}</p>
                    </div>
                  </div>
                  </div>`;
          }
        }else if(notesPage == 1){
          htmlResponse +=`<div class="col-md-6"><p class="text-muted mb-1"> No records found. </p></div>`;
        }
        if(notesPage == 1){
          $('#notes_section').html(htmlResponse);
        }else{
          $('#notes_section').append(htmlResponse);
          isNotesLoading = false;
        }
        $('.all-notes-loader').attr('style','display:none');

    }
  })
}

var notesUserPage = 1;
isNotesUserLoading = false;
$('#notes_ny_best_section').on('scroll', function() {
  let div = $(this);
  console.log('Hello')
  // Check if the user has scrolled to the bottom of the div
  if (div.scrollTop() + div.innerHeight() <= div[0].scrollHeight && !isNotesUserLoading) {
      notesUserPage++;
      isNotesUserLoading = true;
      loadNotesNybestUserData();
  }
});
function loadNotesNybestUserData(){
  $('.nybest-notes-loader').attr('style','display:flex');
  $.ajax({
      type:"GET",
      url:_NOTES_NYBEST + '?page=' + notesUserPage,
      success: function (response) {
        var htmlResponse = '';
        htmlResponse += '<table class="table">';
        var result = response.data.data;
        if(result.length > 0){
          for (var i = 0; i < result.length; i++) {
            var urls =_URL_NOTES+"/"+result[i].patient_id;
                            
            htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">  
                  <div class="ml-1">
                    <h6 class="mb-1"><a href="${urls}">Record #${result[i].patient_id} ${result[i].patient.first_name+' '+result[i].patient.last_name}</a></h6>
                    <p style="white-space: pre-wrap;">${result[i].message}</p>
                    <p class="text-muted mb-0 tx-12"><i class="mdi mdi-map-marker mr-1"></i> ${result[i].created_date}</p>
                    <div class="row">
                    <p class="text-muted mb-0 tx-12" style="margin-left:12px;">${result[i].user_details.first_name+' '+result[i].user_details.last_name}</p>
                    </div>
                  </div>
                  </div>`;
          }
        }else if(notesUserPage == 1){
          htmlResponse +=`<tr><td rawspan="3"> No record Found.</td></tr>`;
        }
        if(notesUserPage == 1){
          $('#notes_ny_best_section').html(htmlResponse);
        }else{
          $('#notes_ny_best_section').append(htmlResponse);
          isNotesUserLoading = false;
        }
        $('.nybest-notes-loader').attr('style','display:none');
    }
  })
}

function loadStatisticData(page=1){
  type = $('#type_id').val();
  $('.statistics-loader').attr('style','display:flex');
  $.ajax({
      type:"GET",
      url:_URL_STATISTIC,
      data:{
        'type': type,
        'main_type':"statistical",
        'page':page
      },
      success: function (response) {
        $('.loader-sec').hide();
        $('#statistic').html("")
        $('#statistic').html(response);
        $('.statistics-loader').attr('style','display:none');
    }
  })
}

function loadLocationsData(page=1){
  $('.location-loader').attr('style','display:flex');
  var location = $('#location_id').val();
  $.ajax({
      type:"GET",
      url:_LOCATIONS,
      data:{
        'location': location,
        'page': page
      },
      success: function (response) {
        $('.loader-sec').hide();
        $('#location').html("")
        $('#location').html(response);
        $('.location-loader').attr('style','display:none');

    }
  })
}

function loadTodayAppoitmentData(page){
  var agency_id = $('#appoitment_agency_id').val();
  $('.appoitment-loader').attr('style','display:flex');
  $.ajax({
      type:"GET",
      url:_TODAY_APPOITMENT,
      data:{
        'agency_id': agency_id,
        'page':page,
        'main_type':"today",
      },
      success: function (response) {
        $('#today_appoinment').html("")
        $('#today_appoinment').html(response);
        $('.appoitment-loader').attr('style','display:none');
    }
  })
}

function loadUpcommingAppoitmentData(page){
  $('.appoitment-loader').attr('style','display:flex');
  var agency_id = $('#appoitment_agency_id').val();
  $.ajax({
      type:"GET",
      url:_UPCOMMING_APPOITMENT,
      data:{
        'agency_id': agency_id,
        'main_type':"upcomming",
        'page':page
      },
      success: function (response) {
        $('#upcomming_appoinment').html("")
        $('#upcomming_appoinment').html(response);
        $('.appoitment-loader').attr('style','display:none');
    }
  })
}

var announcementPage = 1;
isAnnouncementLoading = false;

$('#announcements_section').on('scroll', function() {
  let div = $(this);
  // Check if the user has scrolled to the bottom of the div
  if (div.scrollTop() + div.innerHeight() <= div[0].scrollHeight && !isAnnouncementLoading) {
      announcementPage++;
      isAnnouncementLoading = true;
      loadAnnouncementData();
  }
});

function loadAnnouncementData(){
  $('.announcement-loader').attr('style','display:flex');
  var announcement_range_date = $('#announcement_range_date').val();
  $.ajax({
      type:"GET",
      url:_ANNOUNCEMENT_DATA,
      data:{
        'announcement_range_date' : announcement_range_date,
        'page': announcementPage
      },
      success: function (response) {
        var htmlResponse = '';
        result = response.data.data;
        htmlResponse += '<table class="table">';
        if(result.length > 0){
          for (var i = 0; i < result.length; i++) {     
            htmlResponse +=`<div class="d-flex align-items-center py-2 border-bottom">
                  <div class="ml-1">
                    <p class="text-muted mb-0 tx-12">${result[i].user_details.first_name+' '+result[i].user_details.last_name}</p>
                    <b><p style="white-space: pre-wrap;">${result[i].announcement_detail.title}</p></b>
                    <div class="row" style="white-space: pre-wrap;"> <div class="col-md-7">${result[i].announcement_detail.description}</div></div>
                  </div>
                  </div>`;
          }
        }else if(announcementPage == 1){
          htmlResponse +=`<tr><td rawspan="3"> No record Found.</td></tr>`;
        }
        if(announcementPage == 1){
          $('#announcements_section').html(htmlResponse);
        }else{
          $('#announcements_section').append(htmlResponse);
          isAnnouncementLoading = false;
        }
        $('.announcement-loader').attr('style','display:none');
    }
  })
}

$('body').on('click', '.pagination a', function(event) {
  $('li').removeClass('active');
  $(this).parent('li').addClass('active');
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
  
 
});

loadAnnouncementData();
loadNotesNybestUserData();
loadNotesData();

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