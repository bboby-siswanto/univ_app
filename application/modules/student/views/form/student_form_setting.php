<form id="student_setting" onsubmit="return false">
    <input type="hidden" name="student_number" value="<?= (($student_data) AND (!is_null($student_data->student_number))) ? '1' : '0' ?>">
    <input type="hidden" name="student_id" value="<?= ($student_data) ? $student_data->student_id : '' ?>">
    <ul class="list-group">
        <li class="list-group-item active">
            <div class="row">
                <div class="col-12">Settings</div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Student status</label>
                </div>
                <div class="col-md-7">
                    <select name="student_status" id="student_status" class="form-control">
                        <option value="">Please select...</option>
                <?php
                    if ($student_status) {
                        foreach ($student_status as $status) {
                ?>
                        <option value="<?= $status?>" <?= (($student_data) AND ($student_data->student_status == $status)) ? 'selected' : '' ?>><?= strtoupper($status) ?></option>
                <?php
                        }
                    }
                ?>
                    </select>
                </div>
            </div>
            <!-- <div id="input_onleave_semester" class="row mt-2 <?= (($student_data) AND ($student_data->student_status == 'onleave')) ? '' : '' ?>">
                <div class="col-md-5">
                    <label>Academic Semester<br><small>(for invoice semester leave)</small></label>
                </div> -->
                <!-- <div class="col-md-7">
                    <div class="row">
                        <div class="col-6">
                            <select name="academic_year_id" id="onleave_academic_year_id" class="form-control">
                                <option value="">---</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="semester_type_id" id="onleave_semester_type_id" class="form-control">
                                <option value="">---</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2 ml-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="extend_semester" name="extend_semester">
                            <label class="custom-control-label" for="extend_semester">2 Semesters</label>
                        </div>
                    </div>
                </div> -->
            <!-- </div> -->
        </li>
        <li class="list-group-item">
            <div id="input_resign" class="row <?= (($student_data) AND ($student_data->student_status == 'resign')) ? '' : '' ?>">
                <div class="col-md-5">
                    <label>Resign Date</label>
                </div>
                <div class="col-md-7">
                    <input type="date" name="date_resign" id="date_resign" class="form-control" value="<?= (($student_data) AND (!is_null($student_data->student_date_resign))) ? date('Y-m-d', strtotime($student_data->student_date_resign)) : '' ?>">
                </div>
            </div>
            <div id="note_resign" class="row mt-2 <?= (($student_data) AND ($student_data->student_status == 'resign')) ? '' : '' ?>">
                <div class="col-md-5">
                    <label>Resign Note</label>
                </div>
                <div class="col-md-7">
                    <input type="text" name="resign_note" id="resign_note" class="form-control" value="<?= (($student_data) AND (!is_null($student_data->student_resign_note))) ? $student_data->student_resign_note : '' ?>">
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Study Program</label>
                </div>
                <div class="col-md-7">
                    <div class="input-group input-group-sm mb-3">
                        <select name="study_program_id" id="filter_study_program_id" class="form-control" <?= (($student_data) AND (!is_null($student_data->student_number))) ? 'disabled' : '' ?> aria-describedby="button-addon2">
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
                        <div class="input-group-append">
                            <button class="btn btn-info btn-sm" type="button" id="btn_change_prodi" title="Transfer Study Program">
                                <i class="fas fa-map-signs "></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Majoring</label>
                </div>
                <div class="col-md-7">
                    <select name="study_program_majoring_id" id="filter_study_program_majoring_id" class="form-control" <?= (($student_data) AND (in_array($student_data->student_status, ['active', 'onleave']))) ? 'disabled' : '' ?>>
                        <option value="">Please select...</option>
                <?php
                if ((isset($study_program_majoring_lists)) AND ($study_program_majoring_lists)) {
                    foreach ($study_program_majoring_lists as $majoring) {
                ?>
                        <option value="<?= $majoring->study_program_majoring_id;?>" <?= (($student_data) AND ($student_data->study_program_majoring_id == $majoring->study_program_majoring_id)) ? 'selected' : '' ?>><?= $majoring->majoring_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div id="input_graduated" class="row <?= (($student_data) AND ($student_data->student_status == 'graduated')) ? '' : '' ?>">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Graduate Date</label>
                        </div>
                        <div class="col-md-7">
                            <input type="date" name="date_graduated" id="date_graduated" class="form-control" value="<?= (($student_data) AND (!is_null($student_data->student_date_graduated))) ? $student_data->student_date_graduated : '' ?>">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-5">
                            <label>PIN Number</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="ijazah_pin" id="ijazah_pin" class="form-control" value="<?= (($student_data) AND (!is_null($student_data->student_pin_number))) ? $student_data->student_pin_number : '' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Thesis Title</label>
                </div>
                <div class="col-md-7">
                    <textarea name="thesis_title" id="thesis_title" class="form-control w-100"><?=($student_data) ? $student_data->student_thesis_title : '';?></textarea>
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

            if (confirm("Save settings!")) {
                $.blockUI();
                var data = $('#student_setting').serialize();
                data += '&personal_data_id=<?= $personal_data_id;?>';
                $.post('<?= base_url()?>academic/student_academic/save_settings', data, function(result) {
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

        $('#btn_change_prodi').on('click', function(e) {
            e.preventDefault();
            
            $('#modal_change_prodi').modal('show');
        })

        $('#filter_program_id').on('change', function(e) {
            e.preventDefault();

            let program_id = $('#filter_program_id').val();
            if (program_id == '') {
                $('#filter_study_program_id').html('<option value="">Please select...</option>');
            }else {
                show_filter_study_program();
            }
        });

        // $('#student_status').on('change', function(e) {
        //     e.preventDefault();
        //     if ($("#student_status").val() == 'resign') {
        //         $('#input_resign').removeClass('d-none');
        //         $('#note_resign').removeClass('d-none');
        //         // $('#input_graduated').addClass('d-none');
        //         $('div#input_onleave_semester').addClass('d-none');
        //     }
        //     else if($('#student_status').val() == 'graduated'){
        //         // $('#input_graduated').removeClass('d-none');
        //         $('#input_resign').addClass('d-none');
        //         $('#note_resign').addClass('d-none');
        //         $('div#input_onleave_semester').addClass('d-none');
        //         $("#resign_note").val('');
        //     }
        //     else if ($('#student_status').val() == 'onleave') {
        //         $('div#input_onleave_semester').removeClass('d-none');
        //         // $('#input_graduated').addClass('d-none');
        //         $('#input_resign').addClass('d-none');
        //         $('#note_resign').addClass('d-none');
        //         $("#resign_note").val('');
        //     }
        //     else{
        //         $('#input_resign').addClass('d-none');
        //         // $('#input_graduated').addClass('d-none');
        //         $('#note_resign').addClass('d-none');
        //         $('div#input_onleave_semester').addClass('d-none');
        //         $("#resign_note").val('');
        //     }
        // });

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