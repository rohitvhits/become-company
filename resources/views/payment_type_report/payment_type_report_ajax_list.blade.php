<div class="table-responsive">
  <table id="order-listing1" class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>ID</th>
        <th>Patient Code</th>
        <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
          <th>Agency Name</th>
        <?php } ?>
        <th>Type</th>
        <th>Full Name</th>
        <th>Status</th>
        <th>Services</th>
        <th>Mobile</th>
        <th>Phone</th>
        <th>Payment Type</th>
        <th>DOB</th>
        <th>Gender</th>
        <th>Appointment Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($patients->count() != 0) {
        $i = 1 + (($patients->currentPage() - 1) * $patients->perPage());
        foreach ($patients as $row) { ?>
          <tr>
            <td><?= $i++ ?></td>
            <td>
              <a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>">
                <?= $row->id ?>
              </a>
            </td>
            <td>
              <a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>">
                <?= $row->patient_code ?>
              </a>
            </td>
            <?php if (in_array($user->user_type_fk, array(3, 184))) { ?>
              <td><?= $row->agency_name ?></td>
            <?php } ?>
            <td><?php echo $row->type; ?></td>
            <td><?php echo $row->first_name; ?> <?php echo $row->last_name; ?></td>
            <td>
              <?php
              // Define badge classes
              $badges = ['success', 'primary', 'warning', 'danger', 'info'];
              
              // Pick a random badge
              $randomBadge = "primary"; 
              if(strtolower($row->status) == 'completed'){
                $randomBadge = "success";
              }
              if(strtolower($row->status) == 'processing'){
                $randomBadge = "info";
              }
              if(strtolower($row->status) == 'telehealth completed'){
                $randomBadge = "warning";
              }
              if(strtolower($row->status) == 'form completed'){
                $randomBadge = "primary";
              }
              ?>
            
              <span class="badge badge-<?php echo $randomBadge; ?>">
                <?php echo $row->status; ?>
              </span>  
            </td>
            <td><?php echo $row->service_name; ?></td>
            <td><?php echo $row->mobile; ?></td>
            <td><?php echo $row->phone; ?></td>
            <td>
              <span class="badge badge-info">
                <?php echo $row->payment_type_name ?: 'Not Specified'; ?>
              </span>
            </td>
            <td>
              <?php
                if ($row->dob != '0000-00-00' && $row->dob != '') {
                  echo date('m/d/Y', strtotime($row->dob));
                }
              ?>
            </td>
            <td><?php echo $row->gender; ?></td>
            <td>
             @if(strtolower($row->type) == 'caregiver')
                @if(isset($row->appointment_date))
                    <label class="badge badge-success">Schedule Appointment</label> <br/>
                    <?php if ($row->appointment_date != '') {
                            echo date('m/d/Y', strtotime($row->appointment_date));
                        } ?> <?php if ($row->start_time != '' && $row->end_time) {
                          $start_time = date('h:i A', strtotime($row->start_time));
                          $end_time = date('h:i A', strtotime($row->end_time));
                      ?><br /><?php
                              echo $start_time . ' - ' . $end_time;
                          } ?>
                        <br />
                    <?php echo $row->location_name; ?><br />
                @endif
                @if(isset($row->telehealth_date_time))
                    @if(isset($row->appointment_date))
                        <hr/>
                    @endif
                    <label class="badge badge-primary">Telehealth Appointment</label>
                    <br/>
                    {{date('m/d/Y', strtotime($row->telehealth_date_time))}}<br />
                    {{$row->telehealth_time_frame ?: $row->telehealth_time_slot}} <br/>
                @endif
              @endif
              @if(strtolower($row->type) == 'patient')
                @if ($row->appointment_date != '')
                    <label class="badge badge-success">Schedule Appointment</label> <br/>   
                    {{date('m/d/Y h:i A', strtotime($row->appointment_date))}}
                @endif
                @if(isset($row->telehealth_date_time))
                    @if(isset($row->appointment_date))
                        <hr/>
                    @endif
                    <label class="badge badge-primary">Telehealth Appointment</label>
                    <br/>
                    {{date('m/d/Y', strtotime($row->telehealth_date_time))}}<br />
                    {{$row->telehealth_time_frame ?: $row->telehealth_time_slot}} <br/>
                @endif
              @endif
            </td>
            <td>
              <a href="<?php echo URL::to('/'); ?>/patient/view/<?php echo $row->id; ?>"
                 class="btn btn-sm btn-primary" title="View Details">
                <i class="mdi mdi-eye"></i>
              </a>
            </td>
          </tr>
        <?php }
      } else { ?>
        <tr>
          <td colspan="15" style="text-align: center;">
            <b>No data found</b>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <div class="pull-right pegination-margin">
    {{ $patients->links() }}
  </div>
</div>
