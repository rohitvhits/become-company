var page;
var readingPage;
var lastReadingPage;

let currentActivityLogPage = 1;

function getPatientORUTRN(paging=1){
    $('#shimmerLoader').show();
    $.ajax({
       
        type:"GET",
        url:_PATIENTORNTRNLIST,
        data:{
            'robort_id':_ROBORTID,
            'page':paging,
            'agency_id':_AGENCYID
        },
        success:function(res){
           
            $('#shimmerLoader').hide();
            var jsos    =res.data[0].items;
           
            var htmlResponse = '';
            if(jsos.length !=0){
                $.each(jsos,function(i,vs){
                    htmlResponse +=`<div class="oru-message-card">
                <div class="oru-message-header">
                    <span class="oru-message-type oru-type-oru"></span>
                    <div class="oru-message-time">
                        <i class="mdi mdi-clock-outline"></i>
                        <span>${moment(vs.createdAt).format('MM/DD/YYYY hh:mm A')}</span>
                    </div>
                </div>
                <div class="oru-message-body">
                 <span id="details_id${vs.uuid}" style="display:none">${vs.text}</span>
                    <div class="oru-message-content">
                       
                        <span >
                        ${truncateWithEllipsis(vs.text, 500)}
                        </span>
                    
                    </div>
                </div>
                <div class="oru-message-footer">
                    <span class="oru-status-badge"></span>
                    <div class="oru-action-buttons">
                        <button class="oru-btn oru-btn-view" onclick="viewOruTRNMessageDetails('${vs.uuid}')">View Details</button>
                       
                    </div>
                </div>
            </div>`
                  
                })
            }else{
                $('#emptyState').attr('style','display:block');
            }

            if(res.data[0].meta.page ==1){
                $('#sms-logss').html(htmlResponse)
            }else{
                $('#sms-logss').append(htmlResponse)
            }
            page = res.data[0].meta.page+1
            if(res.data[0].meta.page ==  res.data[0].meta.totalPages){
                $('#hideLoadMoreId').attr('style','display:none')
            }else{
                if(res.data[0].items.length !=0){
                    $('#hideLoadMoreId').attr('style','')
                }else{
                    $('#hideLoadMoreId').attr('style','display:none');
                }

            }

            $('#total_oru_record').html(res.data[0].meta['totalItems']);
        }
    })
}

function loadMore(){
    getPatientORUTRN(page)
}

function getPatientReading(paging=1){
    $('#readingShimmerLoader').fadeIn(300);
    $('#readingTableWrapper').hide();
  
    $.ajax({
       
        type:"GET",
        url:_PATIENTREADINGLIST,
        data:{
            'robort_id':_ROBORTID,
            'page':paging,
            'agency_id':_AGENCYID
        },
        success:function(res){
            $('#total_reading_record').html(" ( " + (res?.data?.[0]?.meta?.totalItems ?? 0) + " ) ");
            $('#loadertag886612').attr('style','display:none');
            $('#previousId').attr('style','');
            $('#nextId').attr('style','');
            var json = res.data[0].items;
            var htmlResponse = '';
            if(res?.data?.[0]?.meta?.['page'] ==1){
                var cnt =1;
                $('#previousId').attr('style','display:none');
            }else{
                var cnt= (res?.data?.[0]?.meta?.['page'] *30)-29;
            }
          
            readingPage = res?.data?.[0]?.meta?.['page'];
            if(json?.length !=0){
                $.each(json,function(i,v){
                    htmlResponse +='<tr><td>'+cnt+++'</td><td>'+v.title+'</td><td>'+v.units+'</td><td>'+v.value+'</td><td>'+moment(v.answerDate).format('MM/DD/YYYY hh:mm A')+'</td></tr>'
                })
            }else{
                htmlResponse = '<tr><td colspan="5">No record available</td></tr>'
                $('#previousId').attr('style','display:none');
                $('#nextId').attr('style','display:none');
            }
            

            $('#reading_id').html("")
            $('#reading_id').html(htmlResponse);
           
            if(res?.data?.[0]?.meta?.['totalPages'] ==1){
                $('#previousId').attr('style','display:none');
                $('#nextId').attr('style','display:none');
               
            }else{
                if(res?.data?.[0]?.meta?.['totalPages'] == res?.data?.[0]?.meta?.['page']){
                    $('#nextId').attr('style','display:none');
                }
            }
            lastReadingPage = res?.data?.[0]?.meta?.['totalPages']??0;
            $('#readingShimmerLoader').fadeOut(300, function() {
                $('#readingTableWrapper').fadeIn(300);
            });
        }
    })
}

function previous(){
    var nextPage = readingPage-1;
    getPatientReading(nextPage);
   
}

function next(){
    var nextPage = readingPage+1;
    getPatientReading(nextPage);
}

function getPatientMedicineList(paging=1){
    $('#medicationShimmerLoader').fadeIn(300);
        $('#medicationTableWrapper').hide();
  
    $.ajax({
       
        type:"GET",
        url:_PATIENTMEDICATIONLIST,
        data:{
            'robort_id':_ROBORTID,
            'page':paging,
            'agency_id':_AGENCYID
        },
        success:function(res){
            $('#loadertagmedication').attr('style','display:none');
            var json = res.data.items;
            var htmlResponse = '';
            var paginationHtml = '';
           if(typeof json != "undefined"){
               
                if(json?.length !=0){
                    var cnt = 1;
                    $.each(json,function(i,v){
                        htmlResponse +='<tr><td>'+cnt+++'</td><td>'+v.medicationName+'</td><td>'+moment(v.startDate).format('MM/DD/YYYY hh:mm A')+'</td><td>'+v.dosage+'</td><td>'+v.quantity+'</td><td>'+v.frequency+'</td></tr>'
                    })
                }else{
                    htmlResponse = '<tr><td colspan="6">No record available</td></tr>'
                }
            
                if (res.data.meta.totalPages > 1) {
                    paginationHtml += '<ul class="pagination justify-content-center">';

                    // Previous button
                    if (res.data.meta.page > 1) {
                        paginationHtml += '<li class="page-item">' +
                            '<a class="page-link" href="#" onclick="getPatientMedicineList(' + (res.data.meta.page - 1) + ')">&laquo;</a>' +
                            '</li>';
                    } else {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
                    }

                    // Page numbers
                    for (var i = 1; i <= res.data.meta.totalPages; i++) {
                        if (i === res.data.meta.page) {
                            paginationHtml += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
                        } else {
                            paginationHtml += '<li class="page-item">' +
                                '<a class="page-link" href="#" onclick="getPatientMedicineList(' + i + ')">' + i + '</a>' +
                                '</li>';
                        }
                    }

                    // Next button
                    if (res.data.meta.page < res.data.meta.totalPages) {
                        paginationHtml += '<li class="page-item">' +
                            '<a class="page-link" href="#" onclick="getPatientMedicineList(' + (res.data.meta.page + 1) + ')">&raquo;</a>' +
                            '</li>';
                    } else {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
                    }

                    paginationHtml += '</ul>';
                }
           }else{
            htmlResponse = '<tr><td colspan="6">No record available</td></tr>'
           }

           $('#medication_id').html("")
           $('#medication_id').html(htmlResponse);
           $('#medication_pagination').html(paginationHtml);
            $('#medicationShimmerLoader').fadeOut(300, function() {
                $('#medicationTableWrapper').fadeIn(300);
            });
        }
    })
}

$('#update-remote-id').click(function() {

    var remoteId = $('#hha_remote_id').val();
    var name = $('#hha_remote_name').val();
    var uuid = $('#hha_remote_uuid').val();
    var searchResponse = {};
    if(uuid !=""){
        var details = emmacareEmployeeData.find(o=>o.uuid ==uuid);
        searchResponse = details;
    }

    
    $('.hha_remote_id_error').html("");
    var cnt = 0;
    if (remoteId == '') {
        $('.hha_remote_id_error').html("Please Select Employee");
        cnt = 1;
    }
    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            type: "post",
            url: _REMOTE_PATIENT_UPDATE,
            data: {
                'patient_id': _RECORD_ID,
                'remote_id': remoteId,
                'name': name,
                'agency_id':_RECORD_AGENCY_ID,
                '_token': _CSRF_TOKEN,
                'response':searchResponse
            },
            success: function(res) {
                toastr.success(res.error_msg);
                $('#lnkhhx_remote_id')[0].reset();
                $('#exampleModal-link-remote-id').modal('hide');
                $('.token-input-delete-token').click()
                $('#hhx_remote_id').html('');
                $('.token-input-list').remove();
                remoteID = res.data[0].robort_id;
                remoteName = name;
                extenalId = '';
                extenalId = (res.data[0].externalId != null) ? res.data[0].externalId : "";

                $('#hhx_robort_id').html(name);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        })
    }

});

function CloseRemoteEmployeePopup() {
    $('.hha_remote_id_error').html("");
    $('#lnkhhx_remote_id')[0].reset();
    $('.token-input-list').remove();
    $('.token-input-delete-token').click()
}

function remoteFunction(type="") {
    var urlToken = _REMOTE_EMP_DATA+'?agency_id='+_AGENCYID;
    $("#hha_remote_id").tokenInput(urlToken, {
        tokenLimit: 1,
        zindex: 9999,

        prePopulate: remoteID !== "" ? [{
            id: remoteID,
            name: remoteName
        }] : [],
        onAdd: function(item) {

            var selectedRemoteId = item.remote_id;
            var name = item.name;
            $('#hha_remote_id').val(selectedRemoteId);
            $('#hha_remote_name').val(name);
        },
        onResult: function(results) {
            // Add scroll to dropdown
            setTimeout(function () {
                $(".token-input-dropdown").css({
                    "max-height": "200px",
                    "overflow-y": "auto",
                    "overflow-x": "hidden"
                });
            }, 10);

            return results;
        }
    });

    if(type !=1){
        $('#hha_remote_uuid').val('');
    }
}

$('#remote-popup').click(function() {
    $('#lnkhhx_remote_id')[0].reset();
    $('.token-input-list').remove()
    $('#hha_remote_id').html("");
    $('#hha_remote_name').html("");
    $('.token-input-delete-token').click()
    remoteFunction();

});

function getPatientCarePlan(paging =1){
    $('#carePlanShimmerLoader').fadeIn(300);
        $('#carePlanContent').hide();
        $('#statsBar').hide();
    $.ajax({
       
        type:"GET",
        url:_REMOTE_PATIENT_CARE_PLAN,
        data:{
            'id':_ROBORTID,
            'page':paging,
            'agency_id':_AGENCYID
        },
        success:function(res){
            var json = res.data;
            let count = json.length;
            $('#carePlanContent').removeClass('hide');
            $('#emptyCPPlan').addClass('hide');
            if(count ==0){
                $('#carePlanContent').addClass('hide');
                $('#emptyCPPlan').removeClass('hide');
            }else{
                renderAllCarePlans(json);
            }
            renderReviewInfo(json);
            renderStatsBar(json);
            
            $('#carePlanShimmerLoader').fadeOut(300, function() {
                $('#carePlanContent').fadeIn(300);
                $('#statsBar').fadeIn(300);
            });
        }
    })   
}

function getPatientActivityLog(paging=1){
    $('#shimmerActivityLoader').fadeIn(300);
        $('#activityTimeline').hide();
    $.ajax({
       
        type:"GET",
        url:_REMOTE_PATIENT_ACTIVITY_LOG,
        data:{
            'id':_ROBORTID,
            'page':paging,
            'agency_id':_AGENCYID
        },
        success:function(res){
            currentActivityLogPage = paging;
            loadPatientActivitiesLogs(res);
        },
        error:function(xhr){
            console.error('Error fetching activities:', error);
            $('#shimmerActivityLoader').fadeOut(300, function() {
                $('#activityTimeline').fadeIn(300);
            });
        }
    })   
}


function renderCarePlans(carePlanData) {
    let html = '';
  
    carePlanData.forEach((plan, planIndex) => {
      let planId = `plan-${planIndex}`;
      let goals = plan.goals || [];
  
      // Group goals by disease
      let goalsByDisease = {};
      goals.forEach(goal => {
        let disease = goal.disease || 'Unknown';
        if (!goalsByDisease[disease]) goalsByDisease[disease] = [];
        goalsByDisease[disease].push(goal);
      });
  
      html += `
        <div class="cp-review">
          <div class="cp-review-header" onclick="toggleCP('review','${planId}')">
            <div class="cp-review-left">
              <span>📋 Review #${planIndex + 1}</span>
            </div>
            <div class="cp-review-right">
              <span>👨‍⚕️ ${plan.clinician?.name || 'N/A'}</span>
              <span>📅 ${plan.reviewedAt ? formatDate(plan.reviewedAt) : 'N/A'}</span>
              <span class="cp-toggle rotated" id="review-icon-${planId}">▼</span>
            </div>
          </div>
  
          <div class="cp-review-content expanded" id="review-content-${planId}">
            <div class="cp-review-body">
      `;
  
      // Disease grouping
      Object.entries(goalsByDisease).forEach(([disease, diseaseGoals], diseaseIndex) => {
        let diseaseId = `${planId}-disease-${diseaseIndex}`;
        let diseaseClass = disease.toLowerCase().replace(/\s+/g, '-');
  
        html += `
          <div class="cp-disease">
            <div class="cp-disease-header ${diseaseClass}" onclick="toggleCP('disease','${diseaseId}')">
              <div class="cp-disease-left">
                <span>💊</span>
                <span>${disease}</span>
              </div>
              <div class="cp-disease-right">
                <span>🎯 ${diseaseGoals.length}</span>
                <span class="cp-toggle rotated" id="disease-icon-${diseaseId}">▼</span>
              </div>
            </div>
  
            <div class="cp-disease-content expanded" id="disease-content-${diseaseId}">
              <div class="cp-disease-body">
        `;
  
        // Goals loop
        diseaseGoals.forEach((goal, goalIndex) => {
          let goalId = `${diseaseId}-goal-${goalIndex}`;
          let statusText = goal.status === 1 ? 'Pending' : (goal.status === 2 ? 'Active' : 'Done');
          let interventions = goal.patientInterventions || [];
  
          html += `
            <div class="cp-goal">
              <div class="cp-goal-header" onclick="toggleCP('goal','${goalId}')">
                <div class="cp-goal-left">
                  <span class="cp-goal-num">${goalIndex + 1}</span>
                  <div class="cp-goal-txt">${goal.content || ''}</div>
                </div>
                <div class="cp-goal-right">
                  <span class="cp-badge s${goal.status}">${statusText}</span>
                  <span class="cp-toggle" id="goal-icon-${goalId}">▼</span>
                </div>
              </div>
  
              <div class="cp-goal-details" id="goal-content-${goalId}">
                <div class="cp-goal-meta">
                  ${goal.goalCategory?.title ? `<div class="cp-goal-meta-item"><i class="fas fa-tag"></i> ${goal.goalCategory?.title}</div>` : ''}
                  ${goal.createdAt ? `<div class="cp-goal-meta-item"><i class="fas fa-calendar"></i> ${formatDate(goal.createdAt)}</div>` : ''}
                  ${goal.targetDate ? `<div class="cp-goal-meta-item"><i class="fas fa-flag"></i> ${formatDate(goal.targetDate)}</div>` : ''}
                </div>
  
                ${interventions.length > 0 ? `
                  <div class="cp-interventions">
                    <div class="cp-interventions-header">
                      <div class="cp-interventions-left">
                        <i class="fas fa-tasks"></i>
                        <span>Interventions</span>
                      </div>
                      <span class="cp-count">${interventions.length}</span>
                    </div>
                    ${interventions.map((intervention, intIndex) => `
                      <div class="cp-intervention">
                        <div class="cp-intervention-left">
                          <span class="cp-intervention-num">${intIndex + 1}</span>
                          <div class="cp-intervention-txt">${intervention.content}</div>
                        </div>
                        <div class="cp-intervention-meta">
                          <span class="cp-badge s${intervention.status}">
                            ${intervention.status === 1 ? 'Pending' : (intervention.status === 2 ? 'Active' : 'Done')}
                          </span>
                          ${intervention.interventionCategory?.title ? `<span class="cp-category">${intervention.interventionCategory.title}</span>` : ''}
                        </div>
                      </div>
                    `).join('')}
                  </div>
                ` : ''}
              </div>
            </div>
          `;
        });
  
        html += `
              </div>
            </div>
          </div>
        `;
      });
  
      html += `
            </div>
          </div>
        </div>
      `;
    });
  
    return html;
  }
  
  // Utility to format date
  function formatDate(dateString) {
    const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
  }

  function getStatusText(status) {
    const statusMap = {
        1: 'Pending',
        2: 'Active',
        3: 'Completed'
    };
    return statusMap[status] || 'Unknown';
}

function renderAllCarePlans(carePlans) {
    const contentEl = document.getElementById('carePlanContent');
    let html = '';
    // Render each care plan review
    carePlans.forEach((carePlan, planIndex) => {
        const planId = `careplan-${planIndex}`;
        const stats = calculateStats(carePlan.goals || []);

        html += `
            <div class="disease-section" style="margin-bottom: 10px;">
                <div class="disease-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);" onclick="toggleDisease('${planId}')">
                    <div class="disease-header-left">
                        <span>📋</span>
                        <span>Care Plan Review #${planIndex + 1}</span>
                    </div>
                    <div class="disease-header-right">
                        <span>👨‍⚕️ ${carePlan?.clinician?.name??""}</span>
                        <span>📅 ${formatDate(carePlan?.reviewedAt??"")}</span>
                        <span class="toggle-icon" id="${planId}-icon">▼</span>
                    </div>
                </div>
                <div class="disease-content expanded" id="${planId}">
                    <div class="disease-goals">
        `;

        // Group goals by disease
        const goalsByDisease = {};
        (carePlan.goals || []).forEach(goal => {
            if (!goalsByDisease[goal.disease]) {
                goalsByDisease[goal.disease] = [];
            }
            goalsByDisease[goal.disease].push(goal);
        });

        // Render each disease section
        let diseaseIndex = 0;
        for (const [disease, goals] of Object.entries(goalsByDisease)) {
            const diseaseClass = disease.toLowerCase().replace(/\s+/g, '-');
            const diseaseId = `disease-${planIndex}-${diseaseIndex}`;

            html += `
                <div class="disease-section">
                    <div class="disease-header ${diseaseClass}" onclick="toggleDisease('${diseaseId}')">
                        <div class="disease-header-left">
                            <span>💊</span>
                            <span>${disease}</span>
                        </div>
                        <div class="disease-header-right">
                            <span>🎯 ${goals.length} Goals</span>
                            <span class="toggle-icon" id="${diseaseId}-icon">▼</span>
                        </div>
                    </div>
                    <div class="disease-content expanded" id="${diseaseId}">
                        <div class="disease-goals">`;

                            goals.forEach((goal, goalIndex) => {
                                const goalId = `goal-${planIndex}-${diseaseIndex}-${goalIndex}`;

                                html += `
                                    <div class="goal-card">
                                        <div class="goal-header" onclick="toggleGoal('${goalId}')">
                                            <div class="goal-left">
                                                <span class="goal-number">${goalIndex + 1}</span>
                                                <div class="goal-content">${goal.content}</div>
                                            </div>
                                            <div class="goal-right">
                                                <span class="status-badge status-${goal.status}">${getStatusText(goal.status)}</span>
                                                <span class="goal-toggle-icon" id="${goalId}-icon">▼</span>
                                            </div>
                                        </div>
                                        <div class="goal-details" id="${goalId}">
                                            <div class="goal-meta">
                                                <div class="goal-meta-item">
                                                    🏷️ ${goal.goalCategory?.title}
                                                </div>
                                                <div class="goal-meta-item">
                                                    📅 Created: ${formatDate(goal.createdAt)}
                                                </div>
                                                <div class="goal-meta-item">
                                                    🎯 Target: ${formatDate(goal.targetDate)}
                                                </div>
                                            </div>

                                            <div class="interventions-section">
                                                <div class="interventions-header">
                                                    <div class="interventions-header-left">
                                                        <span>📋</span>
                                                        <span>Patient Interventions</span>
                                                    </div>
                                                    <span class="intervention-count">${goal.patientInterventions?.length}</span>
                                                </div>
                                `;

                                goal.patientInterventions.forEach((intervention, intIndex) => {
                                    html += `
                                        <div class="intervention-item">
                                            <div class="intervention-left">
                                                <span class="intervention-number">${intIndex + 1}</span>
                                                <div class="intervention-content">${intervention.content}</div>
                                            </div>
                                            <div class="intervention-meta">
                                                <span class="status-badge status-${intervention.status}">${getStatusText(intervention.status)}</span>
                                                <span class="category-badge">${intervention.interventionCategory?.title}</span>
                                            </div>
                                        </div>
                                    `;
                                });

                                html += `</div></div></div>`;
                            });

                html += `</div></div></div>`;
            diseaseIndex++;
        }

        html += `</div></div></div>`;
    });

    contentEl.innerHTML = html;
}

// Toggle functions
function toggleDisease(diseaseId) {
    const content = document.getElementById(diseaseId);
    const icon = document.getElementById(diseaseId + '-icon');

    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('rotated');
    } else {
        content.classList.add('expanded');
        icon.classList.add('rotated');
    }
}

function toggleGoal(goalId) {
    const details = document.getElementById(goalId);
    const icon = document.getElementById(goalId + '-icon');

    if (details.classList.contains('expanded')) {
        details.classList.remove('expanded');
        icon.classList.remove('rotated');
    } else {
        details.classList.add('expanded');
        icon.classList.add('rotated');
    }
}

function renderStatsBar(carePlans) {
    const statsBarEl = document.getElementById('statsBar');

    // Calculate total stats across all care plans
    let totalGoals = 0;
    let activeGoals = 0;
    let pendingGoals = 0;
    let totalInterventions = 0;

    carePlans.forEach(carePlan => {
        const goals = carePlan.goals || [];
        totalGoals += goals.length;
        activeGoals += goals.filter(g => g.status === 2).length;
        pendingGoals += goals.filter(g => g.status === 1).length;

        goals.forEach(goal => {
            totalInterventions += (goal.patientInterventions || []).length;
        });
    });

    statsBarEl.innerHTML = `
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-value">${totalGoals}</div>
                <div class="stat-label">Total Goals</div>
            </div>
        </div>
        <div class="stat-card active">
            <div class="stat-content">
                <div class="stat-value">${activeGoals}</div>
                <div class="stat-label">Active Goals</div>
            </div>
        </div>
        <div class="stat-card pending">
            <div class="stat-content">
                <div class="stat-value">${pendingGoals}</div>
                <div class="stat-label">Pending Goals</div>
            </div>
        </div>
        <div class="stat-card completed">
            <div class="stat-content">
                <div class="stat-value">${totalInterventions}</div>
                <div class="stat-label">Interventions</div>
            </div>
        </div>
    `;
}

function renderReviewInfo(carePlans) {
    const reviewInfoEl = document.getElementById('reviewInfo');
    reviewInfoEl.innerHTML = `
        <div class="review-info-item">
            📋 ${carePlans.length} Care Plan Review${carePlans.length > 1 ? 's' : ''}
        </div>
    `;
}

function calculateStats(goals) {
    let totalGoals = goals.length;
    let activeGoals = goals.filter(g => g.status === 2).length;
    let pendingGoals = goals.filter(g => g.status === 1).length;
    let totalInterventions = goals.reduce((sum, g) => sum + (g.patientInterventions?.length || 0), 0);

    return { totalGoals, activeGoals, pendingGoals, totalInterventions };
}

function truncateWithEllipsis(text, maxLen = 100) {
    if (typeof text !== 'string') return '';
    if (text.length <= maxLen) return text;
    return text.slice(0, maxLen) + '...';
}

function viewOruTRNMessageDetails(uuid){
    var details = $('#details_id'+uuid).text();
    $.confirm({
        title: 'ORU/TRN Message Details',
        content: '<p style="white-space:pre-wrap">'+details+'</p>',
        type: 'blue',
        columnClass: 'col-md-9',
                buttons: {
            cancel: function () {
               
            }
        }
    })
}

// Function to determine activity type class
function getActivityTypeClass(reason) {
    if (reason.includes('Creation')) return 'creation';
    if (reason.includes('Revision')) return 'revision';
    return 'creation';
}

// Function to determine activity icon
function getActivityIcon(reason) {
    if (reason.includes('Creation')) return 'mdi-plus';
    if (reason.includes('Revision')) return 'mdi-pencil';
    return 'mdi-file-document';
}

// Function to render interventions
function renderInterventions(interventions) {
    if (!interventions || interventions.length === 0) return '';

    let html = `
        <div class="interventions-section">
            <div class="interventions-title">
                <i class="mdi mdi-clipboard-check-outline"></i>
                Assigned Interventions (${interventions.length})
            </div>
    `;

    interventions.forEach(intervention => {
        html += `
            <div class="intervention-item">
                <div class="intervention-content">
                    <i class="mdi mdi-chevron-right"></i>
                    <span>${intervention.content}</span>
                </div>
                <div class="intervention-goal">
                    <i class="mdi mdi-target"></i>
                    <span>${intervention.goal.content}</span>
                </div>
            </div>
        `;
    });

    html += `</div>`;
    return html;
}

function loadPatientActivitiesLogs(data){
    $('#shimmerActivityLoader').fadeOut(300, function() {
        $('#activityTimeline').fadeIn(300);
    });
    
    if (!data || !data.data || !data.data[0] || !data.data[0].items || data.data[0].items.length === 0) {
        $('#emptyActivityState').show();
        $('#activityTimeline').hide();
        $('#total_activity_record').text('0');
        return;
    }

    const items = data.data[0].items;
    const meta = data.data[0].meta;

    // Update record count
    $('#total_activity_record').text(meta.totalItems);

    // Render activities
    let activitiesHTML = '';
    items.forEach(item => {
        activitiesHTML += renderActivityLogsItem(item);
    });

    $('#activityTimeline').html(activitiesHTML).show();
    $('#emptyActivityState').hide();

    updatePagination(meta.page, meta.totalPages);
}

function renderActivityLogsItem(item) {
    const typeClass = getActivityTypeClass(item.reason);
    const iconClass = getActivityIcon(item.reason);
    const formattedDate = formatDate(item.createdAt);

    return `
        <div class="activity-item">
            <div class="activity-icon ${typeClass}">
                <i class="mdi ${iconClass}"></i>
            </div>
            <div class="activity-card">
                <div class="activity-card-header">
                    <span class="activity-reason ${typeClass}">${item.reason}</span>
                    <div class="activity-meta">
                        <div class="activity-date">
                            <i class="mdi mdi-clock-outline"></i>
                            <span>${formattedDate}</span>
                        </div>
                        <div class="activity-reporter">
                            <i class="mdi mdi-account"></i>
                            <span>${item.reportedBy}</span>
                        </div>
                    </div>
                </div>
                <div class="activity-card-body">
                    <span style="display:none" id="details_activity_log_id${item.uuid}">${item.description}</span>
                    <div class="activity-description">
                        ${truncateWithEllipsis(item.description,500)}
                    </div>
                    ${renderInterventions(item.assignedInterventions)}
                </div>
                <div class="activity-card-footer">
                    <span class="activity-uuid"></span>
                    <div class="activity-actions">
                        <button class="activity-btn activity-btn-view" onclick="viewActivityLogDetails('${item.uuid}')">
                            View Details
                        </button>
                     
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Function to view activity details
function viewActivityLogDetails(uuid) {
    var details = $('#details_activity_log_id'+uuid).text();
    $.confirm({
        title: 'Activity Log Details',
        content: '<p style="white-space:pre-wrap">'+details+'</p>',
        type: 'blue',
        columnClass: 'col-md-9',
        buttons: {
            cancel: function () {
               
            }
        }
    })
}

function nextActivityPage() {
    currentActivityLogPage++;
    getPatientActivityLog(currentActivityLogPage);

    // Scroll to top of timeline
    $('#activityTimeline').animate({ scrollTop: 0 }, 300);
}

function previousActivityPage() {
    if (currentActivityLogPage > 1) {
        currentActivityLogPage--;
        getPatientActivityLog(currentActivityLogPage);

        // Scroll to top of timeline
        $('#activityTimeline').animate({ scrollTop: 0 }, 300);
    }
}

function updatePagination(currentPage, totalPages) {
    if (totalPages <= 1) {
        $('#activityPagination').hide();
        return;
    }

    $('#activityPagination').show();
    $('#currentPage').text(currentPage);
    $('#totalPages').text(totalPages);

    // Update previous button
    if (currentPage <= 1) {
        $('#prevPageBtn').prop('disabled', true);
    } else {
        $('#prevPageBtn').prop('disabled', false);
    }

    // Update next button
    if (currentPage >= totalPages) {
        $('#nextPageBtn').prop('disabled', true);
    } else {
        $('#nextPageBtn').prop('disabled', false);
    }
}

function getRemoteBasicDetails(){
    $('#demographicLoaderOverlay').fadeIn(300);
    $.ajax({
        url: _GET_REMOTE_BASIC_DETAILS,
        method: 'GET',
        data: { 'robort_id':_ROBORTID,
            'agency_id':_AGENCYID
        },
        success: function(response) {
            var res = [];
            if(response.data.length !=0){
                res = response.data[0];  
            }
            loadDemographicDetails(res);
            $('#demographicLoaderImg').hide();
            $('#demographicLoaderOverlay').fadeOut(300);
        },
        error: function(xhr) {
            console.error('Error loading demographic details:', xhr);
            $('#demographicLoaderOverlay').fadeOut(300);
            
            $('#no_data_message').removeClass('hide');
        }
    });
}

function loadDemographicDetails(patientData) {
    
    if (!patientData || !patientData.items || patientData.items.length === 0) {
     
        $('#no_data_message').removeClass('hide');
        $('#demographicDetailsContainer').hide();
        return false;
    }

    const data = patientData.items[0];

    // Patient Basic Information
    $('#patient_uuid').text(data.uuid || '-');
    $('#patient_id').text(data.patientId || '-');
    $('#legacy_id').text(data.legacyId || '-');
    $('#external_id').text(data.externalId || '-');
    $('#first_name').text(data.firstName || '-');
    $('#last_name').text(data.lastName || '-');
    $('#dob').text(moment(data.dob).format('MM/DD/YYYYY') || '-');
    $('#gender').text(data.gender || '-');

    // Enrolled Program Status with badge color
    const statusBadge = $('#enrolled_program_status_badge');
    statusBadge.text(data.enrolledProgramStatus || '-');

    // Set badge color based on status
    statusBadge.removeClass('badge-success badge-warning badge-danger badge-secondary');
    switch(data.enrolledProgramStatus) {
        case 1:
            statusBadge.addClass('info-value text-muted small');
            break;
        case 2:
            statusBadge.addClass('info-value text-muted small');
            break;
        case 3:
            statusBadge.addClass('info-value text-muted small');
            break;
        default:
            statusBadge.addClass('info-value text-muted small');
    }

    // Referral Source
    if (data.referralSource) {
        $('#referral_source_uuid').text(data.referralSource.uuid || '-');
        $('#referral_source_name').text(data.referralSource.name || '-');
    }

    // Provider
    if (data.provider) {
        $('#provider_uuid').text(data.provider.uuid || '-');
        $('#provider_name').text(data.provider.name || '-');
    }

    // Clinician
    if (data.clinician) {
        $('#clinician_uuid').text(data.clinician.uuid || '-');
        $('#clinician_name').text(data.clinician.name || '-');
    }

    // Insurance Information
    if (data.insurance && data.insurance.length > 0) {
        let insuranceHtml = '';
        data.insurance.forEach((ins, index) => {
            insuranceHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        ${ins.isPrimary ?
                            '<span class="badge badge-success">Yes</span>' :
                            '<span class="badge badge-secondary">No</span>'}
                    </td>
                    <td>${ins.policyNumber || '-'}</td>
                    <td>${ins.type || '-'}</td>
                    <td>${ins.planName || '-'}</td>
                </tr>
            `;
        });
        $('#insurance_list').html(insuranceHtml);
    }
}

var emmacareEmployeeData = [];
var emmacareCurrentPage = 1;
var emmacareItemsPerPage = 10;

function searchEmmacareEmployee(){
    var emmacare_first_name = $('#emmacare_first_name').val();
    var emmacare_last_name = $('#emmacare_last_name').val();
    var emmacare_externalId = $('#emmacare_externalId').val();
    var emmacare_dob = $('#emmacare_dob').val();

    if (emmacare_first_name.trim() != '' || emmacare_last_name.trim() != '' || emmacare_externalId.trim() != ''  || emmacare_dob.trim() != '') {
        $('#emmacare_remote_div_id').attr('style', '');
        $('#searchLoader').removeClass('d-none');
        $('#searchIcon').addClass('d-none');
        $('#emmacareAPLoader').show();
        $('#emmacareCId').html('');

        $.ajax({
            type: "get",
            url: _SEARCH_EMMACARE_EMPLOYEE,
            data: {
                'first_name': emmacare_first_name,
                'last_name': emmacare_last_name,
                'externalId': emmacare_externalId,
                'dob': emmacare_dob,
                'agency_id':_AGENCYID,
            },
            success: function(res) {
                $('#searchLoader').addClass('d-none');
                $('#searchIcon').removeClass('d-none');
                $('#emmacareAPLoader').hide();

                if(res.data && res.data.items && res.data.items.length > 0){
                    emmacareEmployeeData = res.data.items;
                    emmacareCurrentPage = 1;
                    displayEmmacareEmployees(emmacareCurrentPage);
                    renderEmmacarePagination();
                } else {
                    $('#emmacareCId').html('<tr><td colspan="8" class="text-center text-muted py-3">No patients found</td></tr>');
                    $('#emmacareResultCount').html('');
                    $('#emmacarePaginationContainer').html('');
                }
            },
            error: function(xhr) {
                $('#searchLoader').addClass('d-none');
                $('#searchIcon').removeClass('d-none');
                $('#emmacareAPLoader').hide();
                toastr.error(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error occurred while searching');
            }
        })
    } else {
        toastr.warning('Please enter at least one search criteria');
    }

}

function displayEmmacareEmployees(page) {
    var startIndex = (page - 1) * emmacareItemsPerPage;
    var endIndex = startIndex + emmacareItemsPerPage;
    var pageData = emmacareEmployeeData.slice(startIndex, endIndex);

    var html = '';

    if (pageData.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted py-3">No patients found</td></tr>';
    } else {
        pageData.forEach(function(patient, index) {
            var globalIndex = startIndex + index + 1;
            var fullName = (patient.firstName || '') + ' ' + (patient.lastName || '');
            var gender = patient.gender || 'N/A';
            var dob = patient.dob || 'N/A';
            var externalId = patient.externalId || 'N/A';
            var patientId = patient.patientId || 'N/A';
            var uuid = patient.uuid || '';

            var referralSource = (patient.referralSource && patient.referralSource.name) ? patient.referralSource.name : 'N/A';

            var primaryInsurance = 'N/A';
            if(patient.insurance && patient.insurance.length > 0){
                var primaryIns = patient.insurance.find(function(ins){ return ins.isPrimary === true; });
                if(primaryIns){
                    primaryInsurance = primaryIns.type || 'N/A';
                }
            }

            var enrolledProgramStatus = patient.enrolledProgramStatus || 0;
            var statusText = '';
            var statusBadge = '';

            switch(enrolledProgramStatus) {
                case 1:
                    statusText = 'Enrolled';
                    statusBadge = '<span class="badge badge-success badge-sm">Enrolled</span>';
                    break;
                case 2:
                    statusText = 'Pending';
                    statusBadge = '<span class="badge badge-warning badge-sm">Pending</span>';
                    break;
                case 3:
                    statusText = 'Discharged';
                    statusBadge = '<span class="badge badge-secondary badge-sm">Discharged</span>';
                    break;
                case 4:
                    statusText = 'Declined';
                    statusBadge = '<span class="badge badge-danger badge-sm">Declined</span>';
                    break;
                case 5:
                    statusText = 'Active';
                    statusBadge = '<span class="badge badge-info badge-sm">Active</span>';
                    break;
                default:
                    statusText = 'Unknown';
                    statusBadge = '<span class="badge badge-secondary badge-sm">Unknown</span>';
            }

            html += '<tr>';
            html += '<td class="small">' + globalIndex + '</td>';
            html += '<td class="small">' + patientId + '</td>';
            html += '<td class="small">' + externalId + '</td>';
            html += '<td class="small"><strong>' + fullName + '</strong></td>';
            html += '<td class="small">' + moment(dob).format('MM/DD/YYYY') + '</td>';
            html += '<td class="small">' + gender + '</td>';
            html += '<td class="small">' + statusBadge + '</td>';
            html += '<td class="small text-center">';
            html += '<input type="radio" name="remote_radio" onclick="selectEmmacarePatient(\'' + uuid + '\')">';
         
            html += '</td>';
            html += '</tr>';
        });
    }

    $('#emmacareCId').html(html);
}

function renderEmmacarePagination() {
    var totalPages = Math.ceil(emmacareEmployeeData.length / emmacareItemsPerPage);

    if (totalPages <= 1) {
        $('#emmacarePaginationContainer').html('');
        return;
    }

    var paginationHtml = '<nav aria-label="Emmacare Employee Pagination" class="mt-2">';
    paginationHtml += '<ul class="pagination pagination-sm justify-content-center mb-0">';

    paginationHtml += '<li class="page-item ' + (emmacareCurrentPage === 1 ? 'disabled' : '') + '">';
    paginationHtml += '<a class="page-link" href="javascript:void(0)" onclick="changeEmmacarePage(' + (emmacareCurrentPage - 1) + ')">Previous</a>';
    paginationHtml += '</li>';

    var startPage = Math.max(1, emmacareCurrentPage - 2);
    var endPage = Math.min(totalPages, emmacareCurrentPage + 2);

    if (startPage > 1) {
        paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="changeEmmacarePage(1)">1</a></li>';
        if (startPage > 2) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<li class="page-item ' + (i === emmacareCurrentPage ? 'active' : '') + '">';
        paginationHtml += '<a class="page-link" href="javascript:void(0)" onclick="changeEmmacarePage(' + i + ')">' + i + '</a>';
        paginationHtml += '</li>';
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        paginationHtml += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="changeEmmacarePage(' + totalPages + ')">' + totalPages + '</a></li>';
    }

    paginationHtml += '<li class="page-item ' + (emmacareCurrentPage === totalPages ? 'disabled' : '') + '">';
    paginationHtml += '<a class="page-link" href="javascript:void(0)" onclick="changeEmmacarePage(' + (emmacareCurrentPage + 1) + ')">Next</a>';
    paginationHtml += '</li>';

    paginationHtml += '</ul>';
    paginationHtml += '</nav>';

    $('#emmacarePaginationContainer').html(paginationHtml);
}

function changeEmmacarePage(page) {
    var totalPages = Math.ceil(emmacareEmployeeData.length / emmacareItemsPerPage);

    if (page < 1 || page > totalPages) {
        return;
    }

    emmacareCurrentPage = page;
    displayEmmacareEmployees(page);
    renderEmmacarePagination();
}

function selectEmmacarePatient(uuid) {
    var details = emmacareEmployeeData.find(o=>o.uuid ==uuid);
    var externalId = "";
    if(details.externalId !="" && details.externalId !=null){
        externalId = ' ('+details.externalId+')';
    }
    $('#hha_remote_name').val(details.firstName+' '+details.lastName + externalId);
    $('#hha_remote_patient_id').val(details.patientId);
    $('#hha_remote_uuid').val(details.uuid);
    $('#hha_remote_dob').val(moment(details.dob).format('MM/DD/YYYY'));
    
    $('.token-input-list').remove();
    remoteID = details.patientId;
    remoteName = details.firstName+' '+details.lastName + externalId;
    remoteFunction(1)
   
}

function clearEmmacareSearch() {
    $('#emmacare_first_name').val('');
    $('#emmacare_last_name').val('');
    $('#emmacare_externalId').val('');
    $('#emmacare_dob').val('');
    $('#emmacareCId').html('');
    $('#emmacarePaginationContainer').html('');
    $('#emmacareResultCount').html('');
    $('#emmacare_remote_div_id').hide();
    emmacareEmployeeData = [];
    emmacareCurrentPage = 1;
}