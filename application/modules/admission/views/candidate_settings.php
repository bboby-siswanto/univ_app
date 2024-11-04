<?= (isset($student_data)) ? modules::run('student/show_name', $student_data->student_id) : '' ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <?= modules::run('student/student_profile', $personal_data_id, true);?>
    </div>
<?php
if (in_array($student_data->student_status, $setting_allowed_status)) {
?>
    <div class="col-md-6 mb-3">
        <?= modules::run('admission/form_candidate_setting', $student_data->student_id)?>
    </div>
<?php
}
?>
    <div class="col-md-6 mb-3">
        <?= modules::run('admission/candidate_scholarship', $student_data->student_id)?>
    </div>
    <div class="col-md-6 mb-3">
        <?= modules::run('admission/candidate_siblings',  $student_data->personal_data_id);?>
    </div>
<?php
if ($student_data->student_status == 'active') {
?>
    <div class="col-md-6 mb-3">
        <?= modules::run('admission/candidate_discount_tuition_fee',  $student_data->personal_data_id);?>
    </div>
<?php
}
?>
</div>