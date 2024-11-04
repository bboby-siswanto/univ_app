<?= modules::run('student/show_name'); ?>
<div class="row">
    <div class="col-sm-12">
        <?=modules::run('alumni/job_history/view_list_job_history', $personal_data->personal_data_id)?>
    </div>
</div>