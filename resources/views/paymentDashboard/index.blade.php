
@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/modulejs/css/paymentDashboard/payment_dashboard.css') }}" type="text/css" />

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">
        <div class="page-title-main">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0 font-weight-bold">Payment Dashboard</h5>
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                    <select class="form-control js-example-basic-multiple" multiple id="agency_id">
                        <option value="">Select Agency</option>
                        @foreach($agencyList as $agn)
                        <option value="{{ $agn->id }}">{{ $agn->agency_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="card common-card-box" style="height: 100%; border-radius:4px;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="dash-sm-card">
                                                <div class="row">
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-info shimmer">
                                                            <div class="inner">
                                                                <h3 id="total_pay">$0.00</h3>
                                                                <p>Total Payment</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-dollar"></i>
                                                            </div>
                                                            <a target="_blank" id="total_payment_link" href="{{ url('payment-log-report')}}"
                                                                class="small-box-footer">View All<i
                                                                    class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-danger shimmer">
                                                            <div class="inner">
                                                                <h3 id="remaining_pay">$0.00<sup style="font-size: 20px"></sup></h3>
                                                                <p>Remaining Pay</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-money"></i>
                                                            </div>
                                                            <a target="_blank" id="remaining_pay_link" href="{{ url('payment-log-report')}}"
                                                                class="small-box-footer">View All<i
                                                                    class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-6">
                                                        <div class="small-box bg-success shimmer">
                                                            <div class="inner">
                                                                <h3 id="recieved_pay">$0.00<sup style="font-size: 20px"></sup></h3>
                                                                <p>Received Pay</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-credit-card"></i>
                                                            </div>
                                                            <a target="_blank" id="total_recieved_link" href="{{ url('payment-log-report')}}"
                                                                class="small-box-footer">View All<i
                                                                    class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="dash-side-box">
                                                        <div class="box info-box card common-card-box p-0">
                                                            <div class="title">
                                                                <h5 class="mb-0"><i class="fa fa-map-marker mr-2"></i>Location Wise Payment Data</h5>
                                                            </div>
                                                            <div class="location-wise-data-loader" style="display:flex">
                                                                <div class="col-md-6 pl-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Location</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Total Remaining</th>
                                                                                <th>Total Received</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6 pr-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Location</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Total Remaining</th>
                                                                                <th>Total Received</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="row basic-detail-row"
                                                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;padding-right: 0px;" id="location_wise_payment_data">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="dash-side-box">
                                                        <div class="box info-box card common-card-box p-0">
                                                            <div class="title">
                                                                <h5 class="mb-0"><i class="fa fa-building mr-2"></i>Agency Wise Payment Data</h5>
                                                            </div>
                                                            <div class="agency-wise-data-loader" style="display:flex">
                                                                <div class="col-md-6 pl-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Agency</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Total Remaining</th>
                                                                                <th>Total Received</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6 pr-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Agency</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Total Remaining</th>
                                                                                <th>Total Received</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="row basic-detail-row"
                                                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;padding-right: 0px;" id="agency_wise_payment_data">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="dash-side-box">
                                                        <div class="box info-box card common-card-box p-0">
                                                            <div class="title">
                                                                <h5 class="mb-0"><i class="fa fa-cogs mr-2"></i>Service Wise Payment Data</h5>
                                                            </div>
                                                            <div class="service-wise-data-loader" style="display:flex">
                                                                <div class="col-md-6 pl-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Service</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Total Remaining</th>
                                                                                <th>Total Received</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6 pr-0">
                                                                    <table id="" class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 36%;">Service</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Total Remaining</th>
                                                                                <th>Total Received</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="shimmer-loader">
                                                                            <tr>
                                                                                <th scope="row"></th>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                                <td class="text-center">$0.00</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="row basic-detail-row"
                                                                style="max-height: calc(100vh - 100px);overflow-y:auto;margin-right:0px;padding-right: 0px;" id="service_wise_payment_data">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="card common-card-box mb-2" style="border-radius:4px;">
                                <div class="card-body">
                                    <div class="dash-side-box">
                                        <div class="box info-box">
                                            <div class="title">
                                                <h5><i class="fa fa-pie-chart mr-2"></i> Payment Type Analytics</h5>
                                            </div>
                                        </div>
                                        <div class="chart-shimmer">
                                            <div class="chart-placeholder"></div>
                                        </div>
                                        <div class="chart-box-detail" id="piechart">
                                        </div>
                                        <div class="chart-box-detail" id="payment_type_table_list">
                                        </div>
                                        <div class="chart-box-detail" id="payment_no_data" style="display: none; padding: 50px 0;text-align: center;top: 38%;width: 100%;font-size: 14px;">Nothing to display</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card common-card-box mb-2" style="border-radius:4px;">
                                <div class="card-body">
                                    <div class="dash-side-box">
                                        <div class="box info-box">
                                            <div class="title">
                                                <h5><i class="fa fa-clock-o mr-2"></i> Monthly Payment Received Analytics</h5>
                                            </div>
                                        </div>
                                        <div class="chart-shimmer">
                                            <div class="chart-placeholder"></div>
                                        </div>
                                        <div class="chart-box-detail" id="monthlyChart"></div>
                                        <div class="chart-box-detail" id="monthly_table_list"></div>
                                        <div class="chart-box-detail" id="monthly_no_data" style="display: none; padding: 50px 0;text-align: center;top: 38%;width: 100%;font-size: 14px;">Nothing to display</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
    @include('include/footer')
    @include('paymentDashboard/js_dashboard')

