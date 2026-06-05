@include('include/header_lte')
<style>
    a.btn.bg-maroon.margin{
        width: 100%;
        margin-left: 0px;
    }
</style>
@include('include/sidebar')
<div class="content-wrapper" style="min-height: 946px;"> 

    <div class="content-header"> </div>

    <div class="content" style="padding:0">
        <div class="container-fluid"> 
            <div class="form-box-fff card-box">

                <section class="content" style="padding:0">
                    <div class="row">

                        <div class="col-xs-12">

                            <div class="box">


                                <!-- /.box-header -->
                                <div class="box-body ">
                                    <div class="col-md-2" style="background-color: #96999b;">


                                    </div>
                                    <div class="col-md-10">
                                        <div class="box-header with-border">
                                            <h3 class="box-title" style="font-size: 20px; font-weight: bold;">My Templates</h3>
                                        </div>
                                        <div class="box-body no-padding">
                                            <table class="table">
                                                <thead>
                                                <th>Name</th>
                                                <th>Owner</th>
                                                <th>Last Change</th>
                                                <th>Folder</th>
                                                <th></th>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                
                                                        foreach ($templete_list as $val) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo ucfirst($val->template_name); ?></td>
                                                                <td><?php echo ucfirst($val->document_type); ?></td>
                                                                <td><?php echo date('m/d/Y', strtotime($val->created_date)); ?></td>
                                                                <td><a href="<?php echo URL::to('/'); ?>/public/upload/<?php echo $val->upload_document; ?>"><?php echo $val->upload_document; ?></a></td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-info">Use</button>
                                                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                            <span class="caret"></span>
                                                                            <span class="sr-only">Toggle Dropdown</span>
                                                                        </button>
                                                                        <ul class="dropdown-menu" role="menu">
                                                                            <li><a href="#">Edit</a></li>
                                                                            <li><a href="#">Delete</a></li>

                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php 
                                                    }if (count($templete_list) == 0) { ?>
                                                        <tr>
                                                            <td colspan="6">No record available</td></tr>
<?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>

                </section>

            </div>
        </div>
    </div>
</div>
</div>

@include('include/footer_lte')