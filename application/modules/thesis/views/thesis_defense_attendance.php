<div class="row mb-2">
    <div class="col-md-12">
        <div class="button-group float-right">
            <a href="<?=base_url()?>thesis/thesis_defense" class="btn btn-info"><i class="fas fa-list"></i> Defense lists</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Form Attendance
    </div>
    <div class="card-body">
        <form url="<?=base_url()?>thesis/submit_attendance" id="form_absence" onsubmit="return false">
        <input type="hidden" name="thesis_defense_id" value="<?=($thesis_defense) ? $thesis_defense[0]->thesis_defense_id : ''?>">
        <div class="row">
            <div class="col-sm-5">
                <div class="row">
                    <div class="col-4 mb-2">Student Name</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->personal_data_name : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Student ID</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->student_number : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Study program</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->study_program_abbreviation : ''?></div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="row">
                    <div class="col-4 mb-2">Thesis Title</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? $thesis_defense[0]->thesis_title : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Defense Date</div>
                    <div class="col-8 font-weight-bold">: <?=($thesis_defense) ? date('d F Y', strtotime($thesis_defense[0]->thesis_defense_date)) : ''?></div>
                </div>
                <div class="row">
                    <div class="col-4 mb-2">Defense Time</div>
                    <div class="col-8 font-weight-bold">: 
                        <?=($thesis_defense) ? date('H:i', strtotime($thesis_defense[0]->thesis_defense_time_start)).' - '.date('H:i', strtotime($thesis_defense[0]->thesis_defense_time_end)) : ''?>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-2">
                        Student Attendance
                    </div>
                    <div class="col-4">
                        <select name="attendance_student" id="attendance_student" class="form-control mb-2">
                            <option value="PRESENT" <?= (($thesis_defense) AND ($thesis_defense[0]->attendance == 'PRESENT')) ? 'selected="selected"' : '' ?>>PRESENT</option>
                            <option value="ABSENT" <?= (($thesis_defense) AND ($thesis_defense[0]->attendance == 'ABSENT')) ? 'selected="selected"' : '' ?>>ABSENT</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-2">
                        Notes
                    </div>
                    <div class="col-10">
                        <textarea name="attendance_student_remarks" id="attendance_student_remarks" class="form-control"><?= (($thesis_defense) AND (!empty($thesis_defense[0]->attendance_remarks))) ? $thesis_defense[0]->attendance_remarks : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
if (($thesis_defense) AND (count($thesis_defense[0]->advisor_list) > 0)) {
    foreach ($thesis_defense[0]->advisor_list as $o_advisor) {
        $absence_present_selected = (($o_advisor['attendance']) AND ($o_advisor['attendance'] == 'PRESENT')) ? 'selected="selected"' : '';
        $absence_absent_selected = (($o_advisor['attendance']) AND ($o_advisor['attendance'] == 'ABSENT')) ? 'selected="selected"' : '';
?>
                        <tr>
                            <td><?=($o_advisor['number'] == 1) ? 'Advisor' : 'Co-Advisor';?></td>
                            <td><?=$o_advisor['personal_data_name'];?></td>
                            <td>
                                <input type="hidden" name="student_advisor_id[]" value="<?=$o_advisor['student_advisor_id'];?>">
                                <select name="attendance_advisor[]" class="form-control">
                                    <option value="PRESENT" <?=$absence_present_selected;?>>PRESENT</option>
                                    <option value="ABSENT" <?=$absence_absent_selected;?>>ABSENT</option>
                                </select>
                            </td>
                        </tr>
<?php
    }
}
if (($thesis_defense) AND (count($thesis_defense[0]->examiner_list) > 0)) {
    foreach ($thesis_defense[0]->examiner_list as $o_examiner) {
        $absence_present_selected = (($o_examiner['attendance']) AND ($o_examiner['attendance'] == 'PRESENT')) ? 'selected="selected"' : '';
        $absence_absent_selected = (($o_examiner['attendance']) AND ($o_examiner['attendance'] == 'ABSENT')) ? 'selected="selected"' : '';
?>
                        <tr>
                            <td>Examiner <?=$o_examiner['number'];?></td>
                            <td><?=$o_examiner['personal_data_name'];?></td>
                            <td>
                                <input type="hidden" name="student_examiner_id[]" value="<?=$o_examiner['student_examiner_id'];?>">
                                <select name="attendance_examiner[]" class="form-control">
                                    <option value="PRESENT" <?=$absence_present_selected;?>>PRESENT</option>
                                    <option value="ABSENT" <?=$absence_absent_selected;?>>ABSENT</option>
                                </select>
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
        </form>
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-block btn-primary" id="btn_submit_attendance">Submit Attendance</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('button#btn_submit_attendance').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_absence');
        var url = form.attr('url');
        var data = form.serialize();

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                setInterval(function () {
                    window.location.href = '<?=base_url()?>thesis/thesis_defense'
                }, 2000);
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing your data!');
        });
    });
})
</script>