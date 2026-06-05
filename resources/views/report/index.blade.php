@include('include/header')
@include('include/sidebar')
<link href="<?php echo URL::to('/'); ?>/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo URL::to('/'); ?>/assets/css/token-input.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/modulejs/css/hub_record.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/css/daterangepicker.css')}}" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/modulejs/css/hub_record/hub_record.css')}}">
<link href="<?php echo URL::to('/'); ?>/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
    rel="stylesheet" />

<div class="main-panel main-page-box">

    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Reports</h5>
        </div>
        <hr />
        <div class="row">
            <div class="col-12 ">
                <div class="location-wise-data-loader" >
                    <div class="col-md-12 pl-0">
                        <table id="" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            @foreach($query as $key => $rep)
                            <tbody class="">
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$rep['name']}}</td>
                                    <td><a href="{{$rep['link']}}" title="{{$rep['name']}}">{{$rep['link']}}</a></td>
                                </tr>
                            </tbody>
                            @endforeach
                        </table>
                    </div>
                </div>
                <span id="hub_list_res"></span>
            </div>
        </div>
    </div>
    <div class="row" id="blank_div" style='margin-top: 100px;'>
    </div>
</div>

@include('include/footer')
<script src="<?php echo URL::to('/'); ?>/assets/js/jquery.tokeninput.js"></script>