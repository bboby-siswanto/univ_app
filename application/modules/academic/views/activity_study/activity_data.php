<div class="card activity_data">
    <div class="card-header">Activity Data</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Study Program</strong></div>
            <div class="col-md-9">: <?=($activity_data->program_id == $this->a_programs['NI S1']) ? $activity_data->study_program_ni_name : $activity_data->study_program_name;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>Academic Year / Semester Type</strong></div>
            <div class="col-md-9">: <?=$activity_data->academic_year_id;?> / <?=$activity_data->semester_type_name;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>Type of Activities</strong></div>
            <div class="col-md-9">: <?=$activity_data->nama_jenis_aktivitas_mahasiswa;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>Group Type</strong></div>
            <div class="col-md-9">: <?=($activity_data->activity_member_type == 0) ? 'Personal' : 'Group';?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>Title</strong></div>
            <div class="col-md-9">: <?=$activity_data->activity_title;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>Location</strong></div>
            <div class="col-md-9">: <?=$activity_data->activity_location;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>SK Number</strong></div>
            <div class="col-md-9">: <?=$activity_data->activity_sk_number;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>SK Date</strong></div>
            <div class="col-md-9">: <?=$activity_data->activity_sk_date;?></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3"><strong>Remarks</strong></div>
            <div class="col-md-9">: <?=$activity_data->activity_remarks;?></div>
        </div>
    </div>
</div>
<ul class="nav nav-tabs" id="tab_activity" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="student_activities-tab" data-toggle="tab" href="#student_activities" role="tab" aria-controls="student_activities" aria-selected="true">Student</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="adviser_activities-tab" data-toggle="tab" href="#adviser_activities" role="tab" aria-controls="adviser_activities" aria-selected="false">Adviser</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="examiner_activities-tab" data-toggle="tab" href="#examiner_activities" role="tab" aria-controls="examiner_activities" aria-selected="false">Examiner</a>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="student_activities" role="tabpanel" aria-labelledby="home-tab">
        <?= modules::run('academic/activity_study/tab_activity_student_form', $activity_data);?>
    </div>
    <div class="tab-pane fade" id="adviser_activities" role="tabpanel" aria-labelledby="profile-tab">
        <?= modules::run('academic/activity_study/tab_activity_adviser_form', $activity_data);?>
    </div>
    <div class="tab-pane fade" id="examiner_activities" role="tabpanel" aria-labelledby="contact-tab">
        <?= modules::run('academic/activity_study/tab_activity_examiner_form', $activity_data);?>
    </div>
</div>