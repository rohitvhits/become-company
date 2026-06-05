$.ajax({
    url: _GET_AGENCIES,
    type: "GET",
    data: {
        id: _RECORD_ID,
    },
    success: function (response) {
      
        var json = response.data;
        var htmlResponse = "<option value=''>Select Agency</option>";
        $.each(json,function(i,v){
            if(v.agency_detail){
                if(v.id !=_RECORD_ID){
                    htmlResponse +="<option value='"+v.id+"'>"+v.agency_detail.agency_name.trim()+"</option>";
                }
            }
        })
        $("#patient_wise_agency_id").html(""); 
        $("#patient_wise_agency_id").html(htmlResponse); 
    },
    error: function (xhr, status, error) {
        console.log("error");
        console.error("AJAX Error: ", error);
    },
});

$.ajax({
    url: _GET_AGENCIES,
    type: "GET",
    data: {
        id: _RECORD_ID,
    },
    success: function (response) {
      
        var json = response.data;
        var htmlResponse = '';
        $.each(json,function(i,v){
            if(v.agency_detail){
                if(v.id !=_RECORD_ID){
                    htmlResponse += `<div class="row">  <div class="col-md-5"><dt>Agency</dt>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <dd>
                                                                                    ${v.agency_detail.agency_name}
                                                                                </dd>
                                                                            </div>
                                                                        </div>`;
                }
                $('.patient_wise_agency').addClass('d-none');
            }
        })
        if(htmlResponse == ''){
            $('.patient_wise_agency').addClass('d-none');
        }
        console.log(htmlResponse);
        $("#patient_wise_agency").html(""); 
        $("#patient_wise_agency").html(htmlResponse); 
    },
    error: function (xhr, status, error) {
        console.log("error");
        console.error("AJAX Error: ", error);
    },
});

$("#patient_wise_agency_id").on("change", function (event) {
    event.preventDefault();
    var selectedValue = $(this).val();
   $('#redirection_page').attr('href','')
   var url = _PATIENT_VIEW+'/'+selectedValue;
   window.open(
    url,
    '_blank' // <- This is what makes it open in a new window.
  );
   
});