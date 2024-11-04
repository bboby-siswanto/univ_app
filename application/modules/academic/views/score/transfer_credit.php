<?= modules::run('student/show_name', $student_id);?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Subject Curriculum</div>
            <div class="card-body">
                <?= modules::run('academic/transfer_credit/transfer_curriculum_table', $student_id);?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Student Subject Transfer</div>
            <div class="card-body">
                <?= modules::run('academic/transfer_credit/transfer_subject_table', $student_id);?>
            </div>
        </div>
    </div>
</div>