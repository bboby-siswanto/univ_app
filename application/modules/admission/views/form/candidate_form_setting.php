<form id="candidate_setting" onsubmit="return false">
    <input type="hidden" name="student_id" value="<?=$student_data->student_id;?>">
    <input type="hidden" name="student_number" value="<?= (($student_data) AND (!is_null($student_data->student_number))) ? '1' : '0' ?>">
    <ul class="list-group">
        <li class="list-group-item active">Settings</li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Candidate status</label>
                </div>
                <div class="col-md-7">
                    <select name="student_status" id="student_status" class="form-control">
                        <option value="">Please select...</option>
                <?php
                    if ($student_status) {
                        foreach ($student_status as $status) {
                            if (in_array($status, $status_admission)) {
                ?>
                        <option value="<?= $status?>" <?= (($student_data) AND ($student_data->student_status == $status)) ? 'selected' : '' ?>><?= strtoupper($status) ?></option>
                <?php
                            }
                        }
                    }
                ?>
                    </select>
                </div>
            </div>
            <div id="input_cancel" class="row mt-2 <?= (($student_data) AND ($student_data->student_status == 'cancel')) ? '' : 'd-none' ?>">
                <div class="col-md-5">
                    <label>Cancel Note</label>
                </div>
                <div class="col-md-7">
                    <input type="text" name="student_candidate_cancel_note" id="student_candidate_cancel_note" class="form-control" value="<?= (($student_data) AND (!is_null($student_data->student_candidate_cancel_note))) ? $student_data->student_candidate_cancel_note : '' ?>">
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Academic Year</label>
                </div>
                <div class="col-md-7">
                    <select name="academic_year_id" id="academic_year_candidate" class="form-control">
<?php
if ($academic_year) {
    foreach ($academic_year as $o_academic_year) {
?>
                        <option value="<?=$o_academic_year->academic_year_id;?>" <?= (($student_data) AND (!is_null($student_data->academic_year_id)) AND ($student_data->academic_year_id == $o_academic_year->academic_year_id)) ? 'selected="selected"' : '' ?>><?=$o_academic_year->academic_year_id;?></option>
<?php
    }
}
?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Candidate Student Type</label>
                </div>
                <div class="col-md-7">
                    <select name="student_type" id="candidate_student_type" class="form-control">
                        <option value="">Please select...</option>
<?php
if ($student_type) {
    foreach ($student_type as $s_student_type) {
?>
                        <option value="<?=$s_student_type;?>" <?= (($student_data) AND ($student_data->student_type == $s_student_type)) ? 'selected="selected"' : '' ?>><?= strtoupper($s_student_type);?></option>
<?php
    }
}
?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Program</label>
                </div>
                <div class="col-md-7">
                    <select name="program_id" id="filter_program_id" class="form-control" <?= (($student_data) AND (!is_null($student_data->student_number))) ? 'disabled' : '' ?>>
                        <option value="">Please select...</option>
                <?php
                if ($program_lists) {
                    foreach ($program_lists as $program) {
                ?>
                        <option value="<?= $program->program_id;?>" <?= (($student_data) AND ($student_data->program_id == $program->program_id)) ? 'selected' : '' ?>><?=$program->program_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Study Program</label>
                </div>
                <div class="col-md-7">
                    <select name="study_program_id" id="filter_study_program_id" class="form-control" <?= (($student_data) AND (!is_null($student_data->student_number))) ? 'disabled' : '' ?>>
                        <option value="">Please select...</option>
                <?php
                if ($study_program_lists) {
                    foreach ($study_program_lists as $prodi) {
                ?>
                        <option value="<?= $prodi->study_program_id;?>" <?= (($student_data) AND ($student_data->study_program_id == $prodi->study_program_id)) ? 'selected' : '' ?>><?= $prodi->study_program_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="float-right">
                <button type="button" id="button_save_setting" class="btn btn-info">Save</button>
            </div>
        </li>
    </ul>
</form>
<script>
    $(function() {
        $('#button_save_setting').on('click', function(e) {
            e.preventDefault();

            if (confirm("Are you sure!")) {
                $.blockUI();
                var data = $('#candidate_setting').serialize();
                data += '&personal_data_id=<?= $personal_data_id;?>';
                $.post('<?= base_url()?>admission/save_candidate_settings', data, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('Success saving setting data', 'Success');
                    }else{
                        toastr['warning'](result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                });
            }
        });

        $('#student_block').on('change', function(e) {
            if ($('#student_block').is(':checked')) {
                $('#input_blocked_message').removeClass('d-none');
            }else{
                $('#input_blocked_message').addClass('d-none');
            }
        });

        $('#filter_program_id').on('change', function(e) {
            e.preventDefault();

            let program_id = $('#filter_program_id').val();
            // if (program_id == '') {
            //     $('#filter_study_program_id').html('<option value="">Please select...</option>');
            // }else {
            //     show_filter_study_program();
            // }
        });

        $('#student_status').on('change', function(e) {
            e.preventDefault();
            if ($("#student_status").val() == 'cancel') {
                $('#input_cancel').removeClass('d-none');
            }else{
                $('#input_cancel').addClass('d-none');
                $("#student_candidate_cancel_note").val('');
            }
        });
        
        $('#candidate_student_type').on('change', function(e) {
            e.preventDefault();
            if ($("#candidate_student_type").val() == 'transfer') {
                $('#input_semester_accepted').removeClass('d-none');
                $('#input_semester_type_accepted').removeClass('d-none');
            }else{
                $('#input_cancel').addClass('d-none');
                $('#input_semester_type_accepted').addClass('d-none');
                $("#accepted_semester").val('');
                $("#semester_type_id").val('');
            }
        });

        function show_filter_study_program(setprodi = false) {
            let program_id = $('#filter_program_id').val();
            $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
                var s_html = '<option value="">Please select...</option>';
                if (result.code == 0) {
                    $.each(result.data, function(index, value) {
                        s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
                    });
                }
                $('#filter_study_program_id').html(s_html);

                if (setprodi) {
                    $('#study_program_id_program').val(setprodi);
                }
            }, 'json');
        }
    })
</script>