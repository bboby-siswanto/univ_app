<div class="card">
    <div class="card-header">
        Absence Class of <?= $class_data->class_group_name ?>
    <?php
    if ($this->session->has_userdata('eid')) {
    ?>
        <div class="card-header-actions">
            <a href="<?= base_url()?>public/classlist/class/<?=$this->session->userdata('eid');?>/<?=$this->session->userdata('pid');?>" class="card-header-action btn btn-link">
                <i class="fas fa-list"></i> List Class
            </a>
        </div>
    <?php
    }
    ?>
       
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="table_unit_subject_lists">
                <thead class="bg-dark">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Lecturer</th>
                        <th>Topics Covered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if ($list_topics) {
                foreach ($list_topics as $o_topics) {
            ?>
                    <tr>
                        <td><?=$o_topics->date_operation;?></td>
                        <td><?=$o_topics->time_range;?></td>
                        <td><?=$o_topics->personal_data_name;?></td>
                        <td><?=$o_topics->subject_delivered_description;?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-sm btn-info" data-time="<?=$o_topics->date_operation.' '.$o_topics->time_range;?>" data-lecturer="<?=$o_topics->personal_data_name;?>" data-topic="<?=$o_topics->subject_delivered_description;?>" data-param="<?=$o_topics->subject_delivered_id;?>" name="btn_view_absence" id="btn_view_absence" title="View Absence">
                                    <i class="fas fa-clipboard-list"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Member Class of <?= $class_data->class_group_name ?>
    </div>
    <div class="card-body">
        <!-- <p>Count Student: <?= $count_student?></p> -->
        <div class="table-responsive">
            <table id="table_class_member" class="table table-sm table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Study Program</th>
                        <th>Score Quiz</th>
                        <th>Score Final Exam</th>
                        <th>Final Score</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if ($list_member) {
                foreach ($list_member as $o_member) {
            ?>
                    <tr>
                        <td><?=$o_member->personal_data_name;?></td>
                        <td><?=$o_member->student_number;?></td>
                        <td><?=$o_member->study_program_abbreviation;?></td>
                        <td><?=$o_member->score_quiz;?></td>
                        <td><?=$o_member->score_final_exam;?></td>
                        <td><?=$o_member->score_sum;?></td>
                        <td><?=$o_member->score_grade;?></td>
                    </tr>
            <?php
                }
            }
            ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_class_absence">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Meeting Attendance of <?=$class_data->class_group_name;?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        Lecturer: <span id="subject_meeting_lecturer"></span>
                    </div>
                    <div class="col-6">
                        Date and Time: <span id="subject_meeting_date"></span> <span id="subject_meeting_time"></span>
                    </div>
                    <div class="col-12">
                        Topics Covered: <span id="subject_meeting_topics"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <input type="hidden" name="paramid" id="paramid">
                            <table id="table-attendance-meeting" class="table table-bordered table-stripped">
                                <thead>
                                    <tr class="bg-dark">
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Study Program</th>
                                        <th>Batch</th>
                                        <th>Attendance</th>
                                        <th>Attendance Note</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var table_attendance_meeting = $('#table-attendance-meeting').DataTable({
    info: false,
    ordering: false,
    ajax: {
        url: '<?= base_url()?>public/classlist/get_attendance_meeting',
        type: 'POST',
        data: function(d){
            d.paramid = $('#paramid').val()
        }
    },
    columns: [
        {data: 'student_number'},
        {data: 'personal_data_name'},
        {data: 'study_program_name'},
        {data: 'academic_year_id'},
        {data: 'absence_status'},
        {data: 'absence_note'},
    ],
});

var table_unit_subject_lists = $('#table_unit_subject_lists').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false
});

$(function() {
    $('#table_unit_subject_lists tbody').on('click', 'button#btn_view_absence', function(e) {
        e.preventDefault();

        $('#paramid').val($(this).attr('data-param'));
        $('#subject_meeting_lecturer').html($(this).attr('data-lecturer'));
        $('#subject_meeting_date').html($(this).attr('data-time'));
        $('#subject_meeting_topics').html($(this).attr('data-topic'));
        
        $('#modal_class_absence').modal('show');
        table_attendance_meeting.ajax.reload();
    })
})
</script>