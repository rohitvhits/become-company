@include('include/header')

@include('include/sidebar')

<!-- Breadcrumb -->

<!-- Page Title -->

<div class="row no-margin-padding">

    <div class="col-md-6">

        <h3 class="block-title">{{ trans('sentence.promotional management')}}</h3>

    </div>

    <div class="col-md-6">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a href="<?php echo URL::to('/dashboard'); ?>">

                    <span class="ti-home"></span>

                </a>

            </li>

            <li class="breadcrumb-item active">{{ trans('sentence.promotional management')}}</li>

        </ol>

    </div>

</div>

<!-- /Page Title -->

<!-- /Breadcrumb -->

<!-- Main Content -->

<div class="container-fluid">

    <div class="row">

        <!-- Widget Item -->

        <div class="col-md-12">

            <div class="widget-area-2 proclinic-box-shadow">

                <h3 class="widget-title">{{ trans('sentence.promotional management')}}</h3>

                <form method="post" action='<?php echo URL::to('/insert_promotion') ?>' name="addpromotion" enctype="multipart/form-data" onsubmit="return validation();">

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                    <div class="form-row">

                        <?php if ($user['type'] == 'superadmin') { ?>

                            <div class="form-group col-md-12" id="outletId" style="display:block;">

                                <label for="Outlet">{{ trans('sentence.Outlet')}} <span style="color:red">*</span></label>

                                <select class="form-control" id="outlet" name="outlet" onchange="getdata();">

                                    <option value="">{{ trans('sentence.Select')}} {{ trans('sentence.Outlet')}}</option>

                                    <?php foreach ($outlet as $row) { ?>

                                        <option value="<?= $row->id ?>" <?php if (isset($promotion) || $promotion != NULL) {
                                                                            if ($row->id == $promotion->outlet_fk) {
                                                                                echo 'selected';
                                                                            }
                                                                        } ?>><?= $row->name ?></option>

                                    <?php } ?>

                                </select>

                                <span style="color:red;" id="outlet_error"><?php echo $errors->add_user->first('outlet'); ?></span>

                            </div>

                        <?php } else { ?>

                            <input type="hidden" id="outlet" name="outlet" value="<?php echo $promotion->outlet_fk; ?>">

                        <?php } ?>

                        <div class="form-group col-md-12">

                            <label for="email">{{ trans('sentence.Promotion image')}} <span style="color:red">*</span></label>

                            <input type="file" name="promotionImage" id="promotionImage" onchange="ValidateSingleInput(this);">

                            <input type="hidden" name="promotionImage_old" id="promotionImage_old">

                            <div id="oldimg">

                            </div>

                            <?php if (isset($promotion) || $promotion != NULL) { ?>

                                <img src="<?php echo URL::asset('/') ?>upload/<?= $promotion->promtionImage ?>" height="50" width="50">

                            <?php } ?>

                            <span style="color:red;" id="promotionImage_error"><?php echo $errors->add_user->first('PromotionImage'); ?></span>

                        </div>

                        <div class="form-group col-md-12">

                            <label for="email">{{ trans('sentence.Description English')}} <span style="color:red">*</span></label>

                            <textarea class="form-control" id="englishDescription" placeholder="{{ trans('sentence.Description English')}}" name="englishDescription"><?php if (isset($promotion) || $promotion != NULL) {

                                                                                                                                                                            echo $promotion->englishDescription;
                                                                                                                                                                        } ?></textarea>

                            <span style="color:red;" id="eng_error"><?php echo $errors->add_user->first('englishDescription'); ?></span>

                        </div>

                        <div class="form-group col-md-12">

                            <label for="email">{{ trans('sentence.Description Chinese')}} <span style="color:red">*</span></label>

                            <textarea class="form-control" id="chineseDescription" placeholder="{{ trans('sentence.Description Chinese')}}" name="chineseDescription"><?php

                                                                                                                                                                        if (isset($promotion) || $promotion != NULL) {

                                                                                                                                                                            echo $promotion->chineseDescription;
                                                                                                                                                                        } ?></textarea>

                            <span style="color:red;" id="chinese_error"><?php echo $errors->add_user->first('chineseDescription'); ?></span>

                        </div>

                        <div class="form-group col-md-12 mb-3">

                            <button type="submit" class="btn btn-primary btn-lg">{{ trans('sentence.Submit')}}</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <!-- /Widget Item -->

    </div>

</div>

<!-- /Main Content -->

<!-- /Page Content -->

<script>
    function getdata() {

        var outlet = $('#outlet').val();

        $.ajax({

            url: "<?php echo URL::to('/'); ?>/getPromotionalData",

            type: "POST",

            data: {
                '_token': "<?php echo CSRF_TOKEN(); ?>",
                outlet: outlet
            },

            success: function(response) {

                if (response.promtionImage != "" && response.promtionImage != null) {

                    $('#oldimg').html('<img src="<?php echo URL::asset("/") ?>upload/' + response.promtionImage + '" height="50" width="50">');

                    $('#promotionImage_old').val(response.promtionImage);

                }

                $('#englishDescription').val(response.englishDescription);

                $('#chineseDescription').val(response.chineseDescription);

            }

        });

    }
</script>

<script>
    function validation() {



        var temp = 0;

        var outletId = $('#outlet').val();

        var englishDescription = $('#englishDescription').val();

        var chineseDescription = $('#chineseDescription').val();

        var promotionImage = $('#promotionImage').val();

        var promotionImage = $('#promotionImage').val();

        var promotionImage_old = $('#promotionImage_old').val();

        if (promotionImage == "" && promotionImage_old == "") {
            $('#promotionImage_error').html("Required");
            temp++;
        } else {
            $('#promotionImage_error').html("");
        }

        <?php if ($user['type'] == 'superadmin') { ?>

            if (outletId == "") {
                $('#outlet_error').html("Required");
                temp++;
            } else {
                $('#outlet_error').html("");
            }

        <?php } ?>

        if (englishDescription == "") {
            $('#eng_error').html("Required");
            temp++;
        } else {
            $('#eng_error').html("");
        }



        if (chineseDescription == "") {
            $('#chinese_error').html("Required");
            temp++;
        } else {
            $('#chinese_error').html("");
        }

        if (temp == 0) {

            return true;

        } else {

            return false;

        }

    }





    var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];

    function ValidateSingleInput(oInput) {

        if (oInput.type == "file") {

            var sFileName = oInput.value;

            if (sFileName.length > 0) {

                var blnValid = false;

                for (var j = 0; j < _validFileExtensions.length; j++) {

                    var sCurExtension = _validFileExtensions[j];

                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {

                        blnValid = true;

                        break;

                    }

                }



                if (!blnValid) {

                    $('#promotionImage_error').html("Sorry,file is invalid, allowed extensions are: " + _validFileExtensions.join(", "));

                    // alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));

                    oInput.value = "";

                    return false;

                } else {

                    $('#promotionImage_error').html("");

                }

            }

        }

        return true;

    }
</script>

@include('include/footer')