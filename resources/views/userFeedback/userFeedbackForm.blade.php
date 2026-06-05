<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>NY Best Medical Care PC : Appointment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css.map">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css.map">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/fonts/materialdesignicons-webfont.eot">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/fonts/materialdesignicons-webfont.ttf">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/fonts/materialdesignicons-webfont.woff">
    <link href="http://127.0.0.1:8000/assets/css/vertical-layout-light/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/jqvmap/jqvmap.min.css">

    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/css/horizontal-default-light/style.css">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/css/sweetalert2.min.css">
    <link href="http://127.0.0.1:8000/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <script src="http://127.0.0.1:8000/assets/js/jquery.min.js"></script>
    <script src="http://127.0.0.1:8000/assets/js/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/jquery-confirmation/css/jquery-confirm.min.css">
    <link rel="shortcut icon" href="http://127.0.0.1:8000/img/favicon.png" />
    <link href="http://127.0.0.1:8000/assets/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <link href="http://127.0.0.1:8000/assets/css/jquery-confirm.min.css" rel="stylesheet" />
    </script>
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link href="http://127.0.0.1:8000/assetsd/css/vertical-layout-light/jquery.timepicker.css" rel="stylesheet"
        type="text/css">
    <link href="http://127.0.0.1:8000/assets/css/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/vendors/select2/select2.min.css">
    <link rel="stylesheet"
        href="http://127.0.0.1:8000/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link href="http://127.0.0.1:8000/assets/css/tribute.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/modulejs/css/patient.css?time=1726813186">
    <link href="http://127.0.0.1:8000/assets/bootstrap-datetimepicker.min.css" type="text/css" media="all"
        rel="stylesheet" />
    <link href="http://127.0.0.1:8000/assets/modulejs/css/task.css?time=1726813186" rel="stylesheet">
</head>

<body class="sidebar-toggle-display sidebar-hidden">
    <div class="container-scroller">
        <div class="horizontal-menu">
            <nav class="navbar top-navbar col-lg-12 col-12 p-0">
                <div class="container-fluid">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo" href="#"><img
                                src="<?= URL::to('img/logo-ny.png') ?>"></a>
                        <a class="navbar-brand brand-logo-mini" href="#"><img
                                src="<?= URL::to('img/favicon.png') ?>"></a>
                    </div>
            </nav>
        </div>
    </div>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="dashboard-header d-flex flex-column ">
                <div class="d-flex align-items-center justify-content-between flex-wrap border-bottom  mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="mb-0 font-weight-bold">Patient Feedback</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <form class="form-sample" action="{{ URL::to('feedback-form-store') }}" name="adduser"
                                method="post">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $patient_id }}">
                                <div class="row">
                                    @if (isset($organizedRatings['rating']))
                                    @foreach ($organizedRatings['rating'] as $ratingKey => $rating)
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <div>
                                                        <label
                                                            class="rating-title"><b>{{ $rating['title'] }}</b></label>
                                                    </div>
                                                    <div class="rate">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <input type="radio"
                                                                id="star{{ $i }}_{{ $ratingKey }}"
                                                                class="rate" name="rating[{{ $ratingKey }}][]"
                                                                value="{{ $i }}"
                                                                {{ $rating == $i ? 'checked' : '' }} />
                                                            <label for="star{{ $i }}_{{ $ratingKey }}"
                                                                title="text">{{ $i }}
                                                                stars</label>
                                                        @endfor
                                                    </div>
                                                    @if ($rating['is_text'] == 1)
                                                        <textarea class="form-control" name="remark[{{ $rating['type'] }}][]" rows="3"></textarea>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif

                                    @if (isset($organizedRatings['question']))
                                    @foreach ($organizedRatings['question'] as $questionKey => $question)
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <div>
                                                        <label
                                                            class="question-title"><b>{{ $question['title'] }}</b></label>
                                                    </div>
                                                    <textarea class="form-control" name="remark[{{ $question['type'] }}][]" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif
                                    <button type="submit" class="btn btn-primary mr-2"
                                        id="insertButton">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @include('userFeedback.css_userFeedback')
    @include('include/footer')
</body>

</html>
