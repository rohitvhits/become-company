function diagnosis() {
    var temp = 0;

    var history = $('#history').val();
    var symptoms = $('#symptoms').val();

    $("#symptoms_error").html("");
    $("#history_error").html("");
    const junkInputs = [
        'n/a', 'na', 'none', 'no', 'not available', 'unknown', '-', '.', '...', 'nil'
      ];
    if (symptoms == "") {
        $('#symptoms_error').html("Please enter Symptoms Name");
        temp++;
    }else if (junkInputs.includes(symptoms)){
        $('#symptoms_error').html("Please enter valid Symptoms. 'N/A' or similar inputs are not allowed.");
        temp++;
    }
    if (history == "") {
        $('#history_error').html("Please enter History");
        temp++;
    }else if (junkInputs.includes(history)){
        $('#history_error').html("Please enter valid History. 'N/A' or similar inputs are not allowed.");
        temp++;
    }
    
    if (temp == 0) {
        $('#shimmer-loaders').attr('style','display:block');
        document.getElementById('show-result').style.display = 'none';
        document.getElementById('result').style.display = 'none';
        document.getElementById('result-no-message').style.display = 'none';
        form = $('#predict')[0];
        var formData = new FormData(form);
        console.log(formData);
        $.ajax({
            type: "POST",
            url: DIAGNOSIS,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.diagnosis != "" && data.medications.length > 0) {
                    document.getElementById('diagnosis').innerHTML = `<li>${data.diagnosis}</li>`;
    
                    // Medications
                    let medsList = document.getElementById('medications');
                    medsList.innerHTML = '';
                    data.medications.forEach(med => {
                        medsList.innerHTML += `<li>${med}</li>`;
                    });
    
                    // Red Flags
                    let redFlagList = document.getElementById('red_flags');
                    redFlagList.innerHTML = '';
                    data.red_flags.forEach(flag => {
                        redFlagList.innerHTML += `<li>${flag}</li>`;
                    });
                    document.getElementById('show-result').style.display = 'block';
                    document.getElementById('result').style.display = 'block';
                    $('#shimmer-loaders').attr('style','display:none');
                } else {
                    document.getElementById('show-result').style.display = 'block';
                    document.getElementById('result-no-message').style.display = 'block';
                    $('#shimmer-loaders').attr('style','display:none');
                }
            },
            error: function (jqXHR) {
                const json = jqXHR.responseText;
                const obj = JSON.parse(json);
                toastr.error(obj.message);
                $("#insertButton").prop('disabled', false);
                $('#shimmer-loaders').attr('style','display:none');
            },
        });
    } else {
       
    }
}

function refresh(){
    $('#shimmer-loaders').attr('style','display:none');
    $('#show-result').attr('style','display:none');
    $('#health-result-div').attr('style','display:none');
    $('#test-div').attr('style','display:none');
    $('#clinical-notes-div').attr('style','display:none');
    $('#predict')[0].reset();
    $('#predict-health')[0].reset();
    $('#predict-lab-test')[0].reset();
    $('#clinical-notes-form')[0].reset();
    $('.dropify-clear').click();
    $('.error-html').html("");
}

$('ul.left-section-ul li').click(function() {
    $('ul.left-section-ul li').removeClass('active');
    $(this).addClass('active');
})

$('ul.right-section-ul li').click(function() {
    $('ul.right-section-ul li').removeClass('active');
    $(this).addClass('active');

})


function diagnosisHealth() {
    var temp = 0;
    var lifestyle = $('#health-lifestyle').val();
    var history = $('#health-history').val();
    var risk = $('#health-risk').val();

    $("#health_lifestyle_error").html("");
    $("#health_risk_error").html("");
    $("#health_history_error").html("");

    if (lifestyle == "") {
        $('#health_lifestyle_error').html("Please enter Lifestyle");
        temp++;
    }

    if (history == "") {
        $('#health_history_error').html("Please enter History");
        temp++;
    }

    if (risk == "") {
        $('#health_risk_error').html("Please enter Risk");
        temp++;
    }

    if (temp == 0) {
        $('#healthy-shimmer-loaders').attr('style','display:block');
        form = $('#predict-health')[0];
        var formData = new FormData(form);
        document.getElementById('health-result-div').style.display = 'none';
        document.getElementById('res-health-msg').style.display = 'none';
        document.getElementById('health-result').style.display = 'none';
        $.ajax({
            type: "POST",
            url: DIAGNOSIS_HEALTH,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data.diagnosis);
                if ((data.diagnosis != null)) {
                    document.getElementById('health-diagnosis').innerHTML = `<li>${data.diagnosis}</li>`;
    
                    // Medications
                    let preventive_measures = document.getElementById('health-preventive-measures');
                    preventive_measures.innerHTML = '';
                    data.preventive_measures.forEach(med => {
                        preventive_measures.innerHTML += `<li>${med}</li>`;
                    });
    
                    // Tips
                    let tips = document.getElementById('health-tips');
                    tips.innerHTML = '';
                    data.tips.forEach(flag => {
                        tips.innerHTML += `<li>${flag}</li>`;
                    });
    
                    document.getElementById('health-result-div').style.display = 'block';
                    document.getElementById('health-result').style.display = 'block';
                    $('#healthy-shimmer-loaders').attr('style','display:none');
                } else {
                    document.getElementById('health-result-div').style.display = 'block';
                    document.getElementById('res-health-msg').style.display = 'block';
                    $('#healthy-shimmer-loaders').attr('style','display:none');
                }
            },
            error: function (jqXHR) {
                const json = jqXHR.responseText;
                const obj = JSON.parse(json);
                toastr.error(obj.message);
                $("#healthinsertButton").prop('disabled', false);
                $('#healthy-shimmer-loaders').attr('style','display:none');
            },
        });
    } else {
       
    }
}

function diagnosisLabTest() {
    var temp = 0;

    var history = $('#lab-test-history').val();
    var symptoms = $('#lab-test-symptoms').val();

    $("#lab-test-symptoms_error").html("");
    $("#lab-test-history_error").html("");

    if (symptoms == "") {
        $('#lab-test-symptoms_error').html("Please enter Symptoms Name");
        temp++;
    }

    if (history == "") {
        $('#lab-test-history_error').html("Please enter History");
        temp++;
    }
    if (temp == 0) {
        $('#test-shimmer-loaders').attr('style','display:block');
        form = $('#predict-lab-test')[0];
        var formData = new FormData(form);
        document.getElementById('test-div').style.display = 'none';
        document.getElementById('suggest_result').style.display = 'none';
        document.getElementById('test-no-message').style.display = 'none';
        $.ajax({
            type: "POST",
            url: DIAGNOSIS_LAB_TEST,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.reasoning != '' && data.tests.length > 0) {
                    document.getElementById('reasoning').innerHTML = `<li>${data.reasoning}</li>`;
    
                    // Medications
                    let suggest_tests = document.getElementById('suggest-tests');
                    suggest_tests.innerHTML = '';
                    data.tests.forEach(med => {
                        suggest_tests.innerHTML += `<li>${med}</li>`;
                    });
    
                    document.getElementById('test-div').style.display = 'block';
                    document.getElementById('suggest_result').style.display = 'block';
                    $('#test-shimmer-loaders').attr('style','display:none');
                } else {
                    document.getElementById('test-div').style.display = 'block';
                    document.getElementById('test-no-message').style.display = 'block';
                    $('#test-shimmer-loaders').attr('style','display:none');
                }
            },
            error: function (jqXHR) {
                const json = jqXHR.responseText;
                const obj = JSON.parse(json);
                toastr.error(obj.message);
                $("#insertButton").prop('disabled', true);
                $('#test-shimmer-loaders').attr('style','display:none');
            },
        });
    } else {
       
    }
}

function diagnosisReportTest() {
    var temp = 0;

    var symptoms = $('#report-test-symptoms').val();
    var report = $('#report').val();

    $("#report-test-symptoms-error").html("");
    $("#history_error").html("");
    const junkInputs = [
        'n/a', 'na', 'none', 'no', 'not available', 'unknown', '-', '.', '...', 'nil'
      ];
    if (symptoms == "") {
        $('#report-test-symptoms-error').html("Please enter Symptoms Name");
        temp++;
    }else if (junkInputs.includes(symptoms)){
        $('#report-test-symptoms-error').html("Please enter valid Symptoms. 'N/A' or similar inputs are not allowed.");
        temp++;
    }
    
    if (temp == 0) {
        $('#shimmer-loaders').attr('style','display:block');
        document.getElementById('report-show-result').style.display = 'none';
        document.getElementById('report-result').style.display = 'none';
        document.getElementById('report-result-no-message').style.display = 'none';
        form = $('#report-lab-test')[0];
        var formData = new FormData(form);
        console.log(formData);
        $.ajax({
            type: "POST",
            url: REPORT_DIAGNOSIS,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                if (data.diagnosis != "" && data.medications.length > 0) {
                    document.getElementById('diagnosis').innerHTML = `<li>${data.diagnosis}</li>`;
    
                    // Medications
                    let medsList = document.getElementById('medications');
                    medsList.innerHTML = '';
                    data.medications.forEach(med => {
                        medsList.innerHTML += `<li>${med}</li>`;
                    });
    
                    // Red Flags
                    let redFlagList = document.getElementById('red_flags');
                    redFlagList.innerHTML = '';
                    data.red_flags.forEach(flag => {
                        redFlagList.innerHTML += `<li>${flag}</li>`;
                    });
                    document.getElementById('report-show-result').style.display = 'block';
                    document.getElementById('report-result').style.display = 'block';
                    $('#shimmer-loaders').attr('style','display:none');
                } else {
                    document.getElementById('report-show-result').style.display = 'block';
                    document.getElementById('report-result-no-message').style.display = 'block';
                    $('#shimmer-loaders').attr('style','display:none');
                }
            },
            error: function (jqXHR) {
                const json = jqXHR.responseText;
                const obj = JSON.parse(json);
                toastr.error(obj.message);
                $("#insertButton").prop('disabled', false);
                $('#shimmer-loaders').attr('style','display:none');
            },
        });
    } else {
       
    }
}

function diagnosisClinicalNots() {
    var temp = 0;

    var transcript = $('#transcript').val();

    $("#transcript_error").html("");
    const junkInputs = [
        'n/a', 'na', 'none', 'no', 'not available', 'unknown', '-', '.', '...', 'nil'
      ];
    if (transcript == "") {
        $('#transcript_error').html("Please enter Transcript");
        temp++;
    }else if (junkInputs.includes(symptoms)){
        $('#transcript_error').html("Please enter valid Transcript. 'N/A' or similar inputs are not allowed.");
        temp++;
    }
    
    if (temp == 0) {
        $('#shimmer-loaders').attr('style','display:block');
        document.getElementById('clinical-notes-div').style.display = 'none';
        document.getElementById('clinical-notes-no-message').style.display = 'none';
        document.getElementById('clinical-notes-result').style.display = 'none';
        form = $('#clinical-notes-form')[0];
        var formData = new FormData(form);
        $.ajax({
            type: "POST",
            url: CLINICAL_NOTES,
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data.error);
                if(data.error == undefined){
                    const fields = {
                                    "subjective": {"name":"Subjective",'icon':'fa-file-text'},
                                    "objective": {"name":"Objective",'icon':'fa-eye'},
                                    "assessment": {"name":"Assessment",'icon':'fa-search'},
                                    "plan": {"name":"Plan",'icon':'fa-clipboard'},
                                    "chief_complaint": {"name":"Chief Complaint",'icon':'fa-commenting'},
                                    "vitals": {"name":"Vitals",'icon':'fa-heartbeat'},
                                    "labs_to_order": {"name":"Labs To Order",'icon':'fa-flask'},
                                    "medications": {"name":"Medications",'icon':'fa-medkit'},
                                    "follow_up": {"name":"Follow Up",'icon':'fa-calendar'}
                    };
                    const output = document.getElementById('clinical-notes-result');
                    output.innerHTML = '';
                        for (const key in fields) {
                            console.log(fields);
                            if (data[key]) {
                            const section = document.createElement('div');
                            section.innerHTML = `
                                    <div class="card-design">
                                        <h3><span class="emoji"><i class="fa ${fields[key].icon}"></i></span>${fields[key].name}</h3>
                                        <p>${data[key]}</p>
                                    </div>`;
                            output.appendChild(section);
                            console.log(output);
                            console.log(section);
                            }
                        }
                        document.getElementById('clinical-notes-div').style.display = 'block';
                        document.getElementById('clinical-notes-result').style.display = 'block';
                        $('#shimmer-loaders').attr('style','display:none');
                }else{
                    toastr.error('Please provide accurate and detailed input so I can generate the most relevant and high-quality clinical notes for you');
                    document.getElementById('clinical-notes-div').style.display = 'none';
                    document.getElementById('clinical-notes-result').style.display = 'none';
                    $('#shimmer-loaders').attr('style','display:none');
                }
            },
            error: function (jqXHR) {
                const json = jqXHR.responseText;
                const obj = JSON.parse(json);
                toastr.error(obj.message);
                $("#insertButton").prop('disabled', false);
                $('#shimmer-loaders').attr('style','display:none');
            },
        });
    } else {
       
    }
}
