function searchCaregiver() {
    var hha_caregiver_code_id = $('#hha_caregiver_code_id').val();
    $('#hhas_caregiver_id').attr('style', 'display:none');
    if (hha_caregiver_code_id.trim() != '') {
        $.ajax({
            type: "get",
            url: _SEARCH_HHA_CAREGIVER,
            data: {
                'q': hha_caregiver_code_id,
                'agency_id': _AGENCYID,

            },
            success: function(res) {
                var response = res.data;
                var tableResponse = "";
                $('#hhas_caregiver_id').attr('style', '');
                $('#hhaAppendCId').html("")
                
                if (response.length != 0) {
                    var cnt = 1;
                    $.each(response, function(i, v) {
                        if (!v.caregiver_id) {
                            tableResponse += `<tr>
                                <td nowrap>${cnt++}</td>
                                <td nowrap>${v.id}</td>
                                <td nowrap>${v.name+'('+v.caregiver_code+')'}</td>
                                <td nowrap>${(v.status !=null)?v.status:""}</td>
                                <td nowrap><input type="radio" name="cid" id="hha${v.id}" onclick="selectedCaregiver(${v.id})" data-type="local" value="${v.id}"  data-name="${v.name}" data-code="${v.caregiver_code}"></td>
                            </tr>`;
                        } else {
                            tableResponse += `<tr>
                                <td nowrap>${cnt++}</td>
                                <td>${v.caregiver_id}</td>
                                <td>${v.first_name+' '+v.last_name +'('+v.caregiver_code+')'}</td>
                                <td>${v.status}</td>
                                <td><input type="radio" name="cid"  id="hha${v.caregiver_id}" onclick="selectedCaregiver(${v.caregiver_id})" data-type="hha" value="${v.caregiver_id}" data-name="${v.first_name+' '+v.last_name}" data-code="${v.caregiver_code}"></td>
                            </tr>`;
                        }

                    });


                    $('#hhaAppendCId').html(tableResponse)
                } else {

                    $('#hhaAppendCId').html('<tr><td colspan="4">No record available</td></tr>')
                }


            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        })
    }

}

function selectedCaregiver(id) {
    var hhx_caregiver_name = $('#hha' + id).attr('data-name')
    var link_hha_caregiver = id;
    $('.token-input-list').remove();
    var urlToken = _LINK_TO_HHA_CAREGIVER+"?agency_id="+_AGENCYID;
    $("#hha_profile_id").tokenInput(urlToken, {

        prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{
            id: link_hha_caregiver,
            name: hhx_caregiver_name
        }] : [],

        tokenLimit: 1,
        zindex: 9999
    });

    $('#dataTypeId').val($('#hha' + id).attr('data-type'));
}

function linkHHACaregiver() {
    $('#exampleModal-link-hha').modal('show');
}

function getHhxProfile() {
    var hha_profile_id = $('#hha_profile_id').val();
    $('.hha_profile_error').html("");
    var cnt = 0;
    if (hha_profile_id == '') {
        $('.hha_profile_error').html("Caregiver Link is required");
        cnt = 1;
    }

    if (cnt == 1) {
        return false;
    } else {
        $.ajax({
            type: "post",
            url: _PATIENT_LINK_TO_CAREGIVER,
            data: {
                'patient_id': _RECORD_ID,
                'agency_id': _AGENCYID,
                'hha_profile_id': hha_profile_id,
                'dataTypeId': $('#dataTypeId').val(),
                '_token': _CSRF_TOKEN
            },
            success: function(res) {
                toastr.success(res.message);
                var fullName = res.data.first_name + ' ' + res.data.last_name + ' ( ' + res.data.caregiver_code + ')';
                $('#hhx_caregiver_id').html(fullName);
                $('#lnkhhx_pdf_id')[0].reset();
                $('#hha_caregiver_ids').val(res.data.caregiver_id);
                $('#hha_caregiver_names').val(fullName);
                $('#closedsNew').click();
                $('#hhx_caregiver_link_id').removeClass('hide');
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        })
    }
}
$('#exampleModal-link-hha').bind('hide', function() {
    $('#lnkhhx_pdf_id')[0].reset();
    $('.token-input-delete-token').click()
});

function getHHXCaregiverDetails() {
    $('.token-input-list').remove();
    var agencyId = '{{ $record->agency_id}}';
    var urlToken = _LINK_TO_HHA_CAREGIVER+"?agency_id=" + agencyId;
    var urlTokenCaregiverCode = _LINK_TO_HHA_CAREGIVER_CAREGIVER+"?agency_id=" + agencyId;
    var link_hha_caregiver = $('#hha_caregiver_ids').val();
    var hhx_caregiver_name = $('#hha_caregiver_names').val();

    $("#hha_profile_id").tokenInput(urlToken, {
        prePopulate: link_hha_caregiver !== "" && hhx_caregiver_name !== "" ? [{
            id: link_hha_caregiver,
            name: hhx_caregiver_name
        }] : [],
        tokenLimit: 1,
        zindex: 9999
    });
}