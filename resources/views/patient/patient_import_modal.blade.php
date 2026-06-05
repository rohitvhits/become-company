
<div class="import-preview-container">
    <input type="hidden" name="last_id" value="<?php echo $getlastId;?>">

    <!-- Instructions Banner -->
    <div class="alert alert-info border-0 mb-3">
        <div class="d-flex align-items-start">
            <i class="mdi mdi-lightbulb-on-outline mr-2" style="font-size: 24px;"></i>
            <div>
                <strong>How to map your CSV:</strong>
                <ul class="mb-0 mt-2 pl-3">
                    <li>Select the appropriate field for each column from the dropdown</li>
                    <li>Preview shows the first 10 rows of your data</li>
                    <li>Each field can only be mapped once</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- CSV Preview Table -->
    <div class="table-responsive shadow-sm" style="border-radius: 0.5rem; max-height: 500px; overflow: auto;">
        <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <?php
                    if(isset($import_data[0])){
                        $j = 0;
                        foreach($import_data[0] as $rows) { ?>
                            <th class="text-center align-middle" style="min-width: 200px; vertical-align: top; background-color: #f8f9fa;">
                                <!-- CSV Column Header -->
                                <div class="csv-column-header mb-2">
                                    <span class="badge badge-secondary px-2 py-1">
                                        <i class="mdi mdi-table-column"></i> <?=$rows?>
                                    </span>
                                </div>

                                <!-- Mapping Dropdown -->
                                <div class="mapping-select-wrapper">
                                    <select name="row_order[]" id="row_order<?=$j?>" class="form-control form-control-sm selectvalues">
                                        <option value="">-- Select Field --</option>
                                        <optgroup label="Personal Information">
                                            <option value="type">Type</option>
                                            <option value="first_name">First Name</option>
                                            <option value="last_name">Last Name</option>
                                            <option value="dob">Date of Birth</option>
                                            <option value="gender">Gender</option>
                                            <option value="patient_code">Patient Code</option>
                                            <option value="cin">CIN</option>
                                            <option value="language">Language</option>
                                        </optgroup>
                                        <optgroup label="Contact Information">
                                            <option value="mobile">Mobile</option>
                                            <option value="phone">Phone</option>
                                            <option value="address1">Address Line 1</option>
                                            <option value="address2">Apt/Suite/Floor</option>
                                            <option value="city">City</option>
                                            <option value="state">State</option>
                                            <option value="zip">ZIP Code</option>
                                        </optgroup>
                                        <optgroup label="Service & Insurance">
                                            <option value="service_id">Service</option>
                                            <option value="service_expiry_date">Service Expiry Date</option>
                                            <option value="diciplin">Discipline</option>
                                            <option value="insurance_id">Insurance ID</option>
                                            <option value="insurance_name">Insurance Name</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </th>
                    <?php
                        $j++;
                        }
                    } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach($import_data as $row){
                    if($i != 0){
                        if($i <= 11){ ?>
                            <tr class="data-preview-row">
                                <?php foreach($row as $row_data) { ?>
                                    <td class="text-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;" title="<?php echo htmlspecialchars($row_data); ?>">
                                        <?php echo htmlspecialchars($row_data); ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php
                        }
                    }
                    $i++;
                } ?>
            </tbody>
        </table>
    </div>

    <!-- Stats Footer -->
    
</div>

<style>
    .import-preview-container {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .csv-column-header .badge {
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: none;
    }

    .mapping-select-wrapper .selectvalues {
        border: 2px solid #e0e0e0;
        transition: all 0.2s ease;
    }

    .mapping-select-wrapper .selectvalues:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }

    .mapping-select-wrapper .selectvalues option:disabled {
        color: #ccc;
        font-style: italic;
    }

    .table-bordered thead th {
        border-bottom: 3px solid #dee2e6 !important;
        padding: 1rem 0.75rem;
    }

    .table-bordered td {
        padding: 0.75rem;
        font-size: 0.875rem;
    }

    .data-preview-row:hover {
        background-color: #f5f5f5;
    }

    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    $(function(){
        // Enhanced column mapping logic
        $('.selectvalues').change(function(){
            var $currentSelect = $(this);

            if($currentSelect.attr('id') == 'row_order0' && $currentSelect.val() == 'Default'){
                $('.selectvalues').not(this).prop('disabled', true).val('Disabled');
            } else {
                $('.selectvalues').not(this).removeProp('disabled');

                // Reset all options
                $('.selectvalues option').removeProp('disabled');

                // Disable already selected options in other dropdowns
                $('.selectvalues').each(function(){
                    var val = $(this).val();
                    if(val && val != 'Default' && val != 'Disabled' && val != ''){
                        $('.selectvalues').not(this).find('option[value="'+val+'"]').prop('disabled', true);
                    }
                });
            }

            // Visual feedback for mapped columns
            if($currentSelect.val() && $currentSelect.val() != '') {
                $currentSelect.addClass('border-success').removeClass('border-danger');
            } else {
                $currentSelect.removeClass('border-success border-danger');
            }
        });

        // Highlight required fields on page load
        setTimeout(function(){
            $('.selectvalues').each(function(){
                if($(this).val() && $(this).val() != '') {
                    $(this).addClass('border-success');
                }
            });
        }, 100);
    });
</script>
