@include('include/header')

@include('include/sidebar')



<div class="row no-margin-padding">

    <div class="col-md-6">

        <h3 class="block-title">{{ trans('sentence.edit profile')}}</h3>

    </div>

    <div class="col-md-6">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">

                <a href="<?php echo URL::to('/dashboard'); ?>">

                    <span class="ti-home"></span>

                </a>

            </li>

            <li class="breadcrumb-item active">{{ trans('sentence.edit profile')}}</li>

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

                <h3 class="widget-title">{{ trans('sentence.edit profile')}}</h3>

                <form method="post" action='<?php echo URL::to('/update_profile') ?>?i=<?php echo $id; ?>' name="editprofile" onsubmit="return validation();">

                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                    <div class="form-row">

                        <div class="form-group col-md-6">

                            <label for="patient-name">{{ trans('sentence.name')}} <span style="color:red;">*</span></label>

                            <input type="text" class="form-control" placeholder="{{ trans('sentence.name')}}" id="Name" name="name" value="<?php echo $userDetail->name; ?>">

                            <span style="color:red;" id="name_error"><?php echo $errors->edit_profile->first('name'); ?></span>

                        </div>

                        <div class="form-group col-md-6">

                            <label for="email">{{ trans('sentence.email')}} <span style="color:red;">*</span></label>

                            <input type="email" placeholder="{{ trans('sentence.email')}}" class="form-control" id="Email" name="email" value="<?php echo $userDetail->email; ?>">

                            <span style="color:red;" id="email_error"><?php echo $errors->edit_profile->first('email'); ?></span>

                        </div>

                        <?php /* ?><div class="form-group col-md-6">

                                <label for="email">password</label>

                                <input type="password" class="form-control" id="Password" name="password" value="<?php echo $userDetail->password ?>">

                                <span style="color:red;"><?php echo $errors->add_user->first('password'); ?></span>

                            </div> <?php */ ?>

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

</div>

<!-- /Page Content -->

<script>
    $('#UsertypeId').on('change', function() {

        if (this.value == "individual") {

            $('#outletId').show();

        } else {

            $('#outletId').hide();

        }

    });



    function validation() {

        var temp = 0;

        var name = $('#Name').val();

        var email = $('#Email').val();

        if (name == "") {
            $('#name_error').html('Required');
            temp++;
        } else {
            $('#name_error').html('');
        }

        if (email == "") {
            $("#email_error").html("Required.");
            temp++;
        } else {

            var filter1 = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

            if (filter1.test(email)) {

                $("#email_error").html("");

            } else {

                $("#email_error").html("Please Enter Valid Email.");

                temp++;

            }

        }

        if (temp == 0) {

            return true;

        } else {

            return false;

        }

    }
</script>

@include('include/footer')