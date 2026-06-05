@include('include/header')
@include('include/sidebar')

<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.1.0/ckeditor5.css">
<style>
    #editor {
        visibility: hidden;
        /* Keeps the editor hidden */
    }

    #editor:focus {
        visibility: visible;
        /* Ensures the textarea is visible when focused */
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">CMS</h5>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form id="submitForm" method="POST" action="{{ url('cms/update') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $details->id}}">

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-form-label">Description<span
                                            class="error">*</span></label>
                                    <textarea id="editor" name="description"> {{ $details->message}}</textarea>


                                    <span class="editor_error text-danger"></span>
                                </div>


                            </div>
                            
                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
<!-- /Main Content -->
<!-- /Page Content -->

@include('include/footer')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('/assets/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace("editor");


    $('#submitForm').submit(function(e) {
        var content = CKEDITOR.instances['editor'].getData()
        $('.editor_error').html("");
        var cnt = 0;
        if (content == "") {
            $('.editor_error').html("Description is required");
            cnt = 1;
        }

        if (cnt == 1) {
            return false;
        } else {
            return true;
        }


    })
</script>
<script>


</script>