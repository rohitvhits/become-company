<div class="modal fade" id="approveStampModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         style="display: none;" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Add Stamp</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>

                 <form action="<?php echo URL::to('/approve-stamp/store'); ?>" method="post" id="approveStamp" name="approveStamp" enctype="multipart/form-data">
                     @csrf
                     <div class="modal-body">
                         <div class="form-group">
                             <label>Title <span style="color:red;">*</span></label>
                             <input type="text" name="title" class="form-control" id="stamp_title" value="{{ old('title') }}">
                             <span class="error" id="title_error"></span>
                         </div>
                         <div class="form-group">
                            <label for="message-text" class="col-form-label">Image:<span style="color:red;">*</span></label>
                            <input type="file" class="form-control" id="stamp_image" name="image">
                            <span class="error mt-2" id="image_error" for="image"></span>
                        </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-success" id="stampSave">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                     </div>
                 </form>
             </div>
             <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
     </div>