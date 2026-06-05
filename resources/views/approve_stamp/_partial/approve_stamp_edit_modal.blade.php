<div class="modal fade" id="approveEditStampModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         style="display: none;" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ModalLabel">Update Stamp</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>

                 <form action="<?php echo URL::to('/approve-stamp/'); ?>" method="post" id="approveEditStamp" name="approveEditStamp" enctype="multipart/form-data">
                     @csrf
                     <div class="modal-body">
                         <input type="hidden" name="id" id="id" value="">
                         <div class="form-group">
                             <label>Title <span style="color:red;">*</span></label>
                             <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                             <span class="error" id="stamp_title_error"></span>
                         </div>
                         <div class="form-group">
                            <label for="message-text" class="col-form-label">Image:<span style="color:red;">*</span></label>
                            <input type="file" class="form-control" id="image" name="stamp_image">
                            <div id="imageDiv"></div>
                            <span class="error mt-2" id="stamp_image_error" for="image"></span>
                        </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-success" id="stampUpdate">Save</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                     </div>
                 </form>
             </div>
             <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
     </div>