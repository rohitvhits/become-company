
@include('include/header')

@include('include/sidebar')
 <div class="main-panel">        
        <div class="content-wrapper">
          <div class="row">
                <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Import Excel</h4>
                  <form class="form-sample" action='<?php echo URL::to('/import_excel/'.$id) ?>'  name="adduser" method="post"  enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Import Excel File </label>
                          <div class="col-sm-9">
                            <input type="file" class="form-control" name="import_excel">                          </div>
                        </div>
                      </div>
                     
                    </div>
                
                      <button type="submit" class="btn btn-primary mr-2">Save</button>
                  </form>
                </div>
              </div>
            </div>

<!-- /Main Content -->

<!-- /Page Content -->

  <!-- End Date Picker -->
@include('include/footer')