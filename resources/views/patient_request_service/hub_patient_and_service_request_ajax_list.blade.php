    @php
        $auth = auth()->user();
    @endphp
    <div class="row">
        <div class="col-12 ">
            <div class="table-responsive tableData">
                <table id="order-listing1" class="table table-bordered table-width1">
                    <thead>
                        <tr>

                            <th class="no_warp">Portal ID</th>
                            <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                            <th class="no_warp">Agency Name</th>
                            <?php } ?>
                            <th class="no_warp">Company Name</th>
                            <th>Type</th>
                            <th class="no_warp">Name/Mobile/DOB/Services </th>
                            <th class="no_warp">Booking Date</th>
                            <th>Created Date</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        if (count($query) > 0) {
                            $i = 1 + (($query->currentPage() - 1) * $query->perPage());
                            foreach ($query as $row) {  ?>
                        <tr>

                            <td> <a href="{{ URL::to('/') }}/hub-record/view/{{ $row->patient->hub_id }}"
                                    target="_blank" rel="noopener noreferrer">#
                                    {{ $row->patient->hub_id }} </a>
                                @if ($row->patient->record_read == 0)
                                    <div style="position:relative"><span class="add_new_record left_record">New</span>
                                    </div>
                                @endif
                            </td>

                            <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
                            <td><?= $row->patient->agencyDetail->agency_name ?></td>
                            <?php } ?>
                            <td>{{ $row->patient->hubCompanyDetail->agency_name }}</td>
                            <td><?php echo $row->patient->type; ?>
                                <br />
                                <?php echo $row->patient->diciplin; ?>
                                <br />
                                @if ($row->patient->location_branch != '')
                                    <p class="text-muted" style="font-size:10px">
                                        ({{ $row->patient->location_branch }})</p>
                                @endif


                            </td>

                            <td>
                                <?php echo $row->patient->first_name . ' ' . $row->patient->last_name; ?><br />
                                <?php echo $row->patient->mobile; ?><br />
                                <?php if (isset($row->patient->dob) && $row->patient->dob != '0001-01-01' && $row->patient->dob != '1000-01-01' && $row->patient->dob != '0000-00-00') {
                                    echo date('m/d/Y', strtotime($row->patient->dob));
                                } ?> @if (!empty($row->patient->gender))
                                    ({{ $row->patient->gender }})
                                @endif
                                <br />
                                @foreach ($row->patientServiceRequestRelationShip as $data)
                                    {{ $data->requestService->name ?? '' }}
                                @endforeach <br />
                            </td>

                            <td>
                                @if ($row->booking_date != '')
                                    {{ date('m/d/Y', strtotime($row->booking_date)) }}
                                @else
                                    -
                                @endif

                            </td>

                            <td><?php echo date('m/d/Y h:i A', strtotime($row->created_at)); ?><br />
                                @if (isset($row->userDetails->first_name))
                                    {{ $row->userDetails->first_name . '' . $row->userDetails->last_name }}
                                @endif

                            </td>

                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="20">
                                <center><b>Data not found</b></center>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="pull-right pegination-margin">
                    {{ $query->links() }}
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#service_request_count').html("{{ $query->total() }}");
    </script>
