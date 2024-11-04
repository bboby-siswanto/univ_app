<?php
if (($approval !== null) AND ($approval) AND ($requested)) {
?>
<div class="alert alert-danger" role="alert">
    New Update Request from: <strong><?= ($requested) ? $requested->request_by : '' ?></strong> at <strong><?= ($requested) ? date('d M Y H:i:s', strtotime($requested->request_datetime)) : '' ?></strong><br>
    Notes: <?=$requested->request_note;?>
</div>
<?php
}
?>
<div class="card">
    <div class="card-header">
        Period <?= $semester_data->academic_year_id.'/'.$semester_data->semester_type_id;?> (<?= date('d M Y', strtotime($semester_data->semester_start_date)); ?> - <?= date('d M Y', strtotime($semester_data->semester_end_date)); ?>)
    </div>
    <div class="card-body">
        <form id="semester_settings_details_input" onsubmit="return false;">
            <input type="hidden" name="semester_start_date" value="<?= $semester_data->semester_start_date?>">
            <input type="hidden" name="semester_end_date" value="<?= $semester_data->semester_end_date?>">
            <input type="hidden" name="academic_year_id" value="<?= $semester_data->academic_year_id;?>">
            <input type="hidden" name="semester_type_id" value="<?= $semester_data->semester_type_id;?>">
            <div class="row">
                <div class="col-md-3 mt-3">
                    <div class="border-line-right">
                        <strong>Offer Subject End Date Period</strong>
                        <div class="form-group">
                            <label>Regular Semester</label>
                            <input type="text" class="form-control" id="regular_offer_subject_end_date" name="regular_offer_subject_end_date" value="<?= ((!is_null($semester_data->offer_subject_end_date)) AND ($semester_data->offer_subject_end_date != '')) ? date('Y-m-d', strtotime($semester_data->offer_subject_end_date)) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Short Semester</label>
                            <input type="text" class="form-control" id="short_semester_offer_subject_end_date" name="short_semester_offer_subject_end_date" value="<?= ((!is_null($semester_data->offer_subject_short_semester_end_date)) AND ($semester_data->offer_subject_short_semester_end_date != '')) ? date('Y-m-d', strtotime($semester_data->offer_subject_short_semester_end_date)) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>OFSE</label>
                            <input type="text" class="form-control" id="ofse_offer_subject_end_date" name="ofse_offer_subject_end_date" value="<?= ((!is_null($semester_data->offer_subject_ofse_end_date)) AND ($semester_data->offer_subject_ofse_end_date != '')) ? date('Y-m-d', strtotime($semester_data->offer_subject_ofse_end_date)) : '' ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3">
                    <div class="border-line-right">
                        <strong>Study Plan Registration Period for Student</strong>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Regular Study Plan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="regular_study_plan_start_date" name="regular_study_plan_start_date" value="<?= ((!is_null($semester_data->study_plan_start_date)) AND ($semester_data->study_plan_start_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_start_date)) : '' ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">to</span>
                                        </div>
                                        <input type="text" class="form-control" id="regular_study_plan_end_date" name="regular_study_plan_end_date" value="<?= ((!is_null($semester_data->study_plan_end_date)) AND ($semester_data->study_plan_end_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_end_date)) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Short Semester Study Plan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="short_semester_study_plan_start_date" name="short_semester_study_plan_start_date" value="<?= ((!is_null($semester_data->study_plan_short_semester_start_date)) AND ($semester_data->study_plan_short_semester_start_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_short_semester_start_date)) : '' ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">to</span>
                                        </div>
                                        <input type="text" class="form-control" id="short_semester_study_plan_end_date" name="short_semester_study_plan_end_date" value="<?= ((!is_null($semester_data->study_plan_short_semester_end_date)) AND ($semester_data->study_plan_short_semester_end_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_short_semester_end_date)) : '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>OFSE Study Plan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="ofse_study_plan_start_date" name="ofse_study_plan_start_date" value="<?= ((!is_null($semester_data->study_plan_ofse_start_date)) AND ($semester_data->study_plan_ofse_start_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_ofse_start_date)) : '' ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text">to</span>
                                        </div>
                                        <input type="text" class="form-control" id="ofse_study_plan_end_date" name="ofse_study_plan_end_date" value="<?= ((!is_null($semester_data->study_plan_ofse_end_date)) AND ($semester_data->study_plan_ofse_end_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_ofse_end_date)) : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <strong>Study Plan Approval End Date Period</strong>
                    <div class="form-group">
                        <label>Regular Semester</label>
                        <input type="text" class="form-control" id="regular_study_plan_approval_end_date" name="regular_study_plan_approval_end_date" value="<?= ((!is_null($semester_data->study_plan_approval_end_date)) AND ($semester_data->study_plan_approval_end_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_approval_end_date)) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Short Semester</label>
                        <input type="text" class="form-control" id="short_semester_study_plan_approval_end_date" name="short_semester_study_plan_approval_end_date" value="<?= ((!is_null($semester_data->study_plan_approval_short_semester_end_date)) AND ($semester_data->study_plan_approval_short_semester_end_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_approval_short_semester_end_date)) : '' ?>">
                    </div>
                    <!-- <div class="form-group">
                        <label>OFSE</label>
                        <input type="text" class="form-control" id="ofse_study_plan_approval_end_date" name="ofse_study_plan_approval_end_date" value="<?= ((!is_null($semester_data->study_plan_approval_ofse_end_date)) AND ($semester_data->study_plan_approval_ofse_end_date != '')) ? date('Y-m-d', strtotime($semester_data->study_plan_approval_ofse_end_date)) : '' ?>">
                    </div> -->
                </div>
                <div class="col-md-6 mt-3">
                    <div class="border-line-right">
                        <strong>Repetition Registration Period for Student</strong>
                        <div class="form-group">
                            <label>Regular Semester</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="regular_repetition_registration_start_date" name="regular_repetition_registration_start_date" value="<?= ((!is_null($semester_data->repetition_registration_start_date)) AND ($semester_data->repetition_registration_start_date != '')) ? date('Y-m-d', strtotime($semester_data->repetition_registration_start_date)) : '' ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text">to</span>
                                </div>
                                <input type="text" class="form-control" id="regular_repetition_registration_end_date" name="regular_repetition_registration_end_date" value="<?= ((!is_null($semester_data->repetition_registration_end_date)) AND ($semester_data->repetition_registration_end_date != '')) ? date('Y-m-d', strtotime($semester_data->repetition_registration_end_date)) : '' ?>">
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label>OFSE</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ofse_repetition_registration_start_date" name="ofse_repetition_registration_start_date" value="<?= ((!is_null($semester_data->repetition_registration_ofse_start_date)) AND ($semester_data->repetition_registration_ofse_start_date != '')) ? date('Y-m-d', strtotime($semester_data->repetition_registration_ofse_start_date)) : '' ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text">to</span>
                                </div>
                                <input type="text" class="form-control" id="ofse_repetition_registration_end_date" name="ofse_repetition_registration_end_date" value="<?= ((!is_null($semester_data->repetition_registration_ofse_end_date)) AND ($semester_data->repetition_registration_ofse_end_date != '')) ? date('Y-m-d', strtotime($semester_data->repetition_registration_ofse_end_date)) : '' ?>">
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <!-- <div class="col-md-6 mt-3">
                            <div class="border-line-right">
                                <strong>&nbsp;</strong>
                                <div class="form-group">
                                    <label class="font-weight-bold">Dikti Report Deadline</label>
                                    <input type="text" id="dikti_report_deadline" name="dikti_report_deadline" class="form-control" placeholder="Select Date" value="<?= ((!is_null($semester_data->dikti_report_deadline)) AND ($semester_data->dikti_report_deadline != '')) ? date('Y-m-d', strtotime($semester_data->dikti_report_deadline)) : '' ?>">
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-6 mt-3">
                            <strong>&nbsp;</strong>
                            <div class="form-group">
                                <label class="font-weight-bold">Semester Status</label>
                                <select name="semester_status" id="semester_status" class="form-control">
                                    <option value="" disabled>Please Select</option>
                                    <option value="active" <?= ($semester_data->semester_status == 'active') ? 'selected' : '';?>>Active</option>
                                    <option value="inactive" <?= ($semester_data->semester_status == 'inactive') ? 'selected' : '';?>>InActive</option>
                                </select>
                            </div>
                        </div>
                    </div>
        <?php
            if (($approval === null) OR (!$approval)) {
        ?>
                    <div class="form-group">
                        <label class="font-weight-bold">Request Note</label>
                        <input type="text" class="form-control" name="request_note" id="request_note">
                    </div>
        <?php
            }
        ?>
                </div>
            </div>
        </form>
        <div class="form-group">
<?php
if (($approval !== null) AND ($approval)) {
?>
            <button type="button" id="approve_semester_details" class="btn btn-success float-right">Approve Request</button>
<?php
}else{
?>
            <button type="button" id="save_semester_details" class="btn btn-primary float-right">Send Request</button>
<?php
}
?>
        </div>
    </div>
</div>
<script>
    $(function() {
        var semester_start_date = new Date('<?= $semester_data->semester_start_date?>');
        var semester_end_date = new Date('<?= $semester_data->semester_end_date?>');
        var start_date = new Date(semester_start_date.getFullYear(), semester_start_date.getMonth(), semester_start_date.getDate());
        var end_date = new Date(semester_end_date.getFullYear(), semester_end_date.getMonth(), semester_end_date.getDate());
        
        var datepicker_end_period = function(element) {
            var datepicker = $('#' + element + '_end_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: start_date,
                // maxDate: end_date
            });
        }
        var datepicker = function(element) {
            var datepicker_start = $('#' + element + '_start_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: start_date,
                // maxDate: end_date
            }).on('change', function() {
                datepicker_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
                datepicker_end.datepicker('setDate', '');
            });

            var element_date = new Date(datepicker_start.val());
            // console.log(element+ ': ' + element_date);
            element_date = new Date(element_date.getFullYear(), element_date.getMonth(), element_date.getDate());

            var datepicker_end = $('#' + element + '_end_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                // maxDate: end_date,
                minDate: element_date
            });
        }

        var datepicker_deadline = $('#dikti_report_deadline').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            minDate: start_date,
            // maxDate: end_date
        });

        datepicker_end_period('regular_offer_subject');
        datepicker_end_period('short_semester_offer_subject');
        datepicker_end_period('ofse_offer_subject');
        datepicker_end_period('ofse_study_plan_approval');
        datepicker_end_period('short_semester_study_plan_approval');
        datepicker_end_period('regular_study_plan_approval');
        datepicker('regular_study_plan');
        datepicker('short_semester_study_plan');
        datepicker('ofse_study_plan');
        datepicker('regular_repetition_registration');
        datepicker('ofse_repetition_registration');
        // datepicker('repetition_registration');

        $('button#save_semester_details').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            var data = $('form#semester_settings_details_input').serialize();
            $.post('<?= base_url()?>academic/semester/semester_setting_detail_save', data, function(result) {
                $.unblockUI();
                if(result.code == 0) {
                    toastr.success('Success saving data', 'Success');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        });
        
        $('button#approve_semester_details').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            var data = $('form#semester_settings_details_input').serialize();
            $.post('<?= base_url()?>academic/semester/semester_setting_detail_approved', data, function(result) {
                $.unblockUI();
                if(result.code == 0) {
                    toastr.success('Success saving data', 'Success');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        });
    })
</script>