<style>
    .tab-content {
        background: none !important;
        border: none !important;
    }
    .tab-content .tab-pane {
        padding: 0px !important;
    }
    .table-transfer td {
        vertical-align: top;
        padding-top: 5px;
    }
</style>
<?= (isset($student_data)) ? modules::run('student/show_name', $student_data->student_id) : '' ?>
<div class="row">
    <div class="col-md-3">
        <div class="nav flex-column nav-pills" id="nav-tab" role="tablist">
            <button class="btn nav-link btn-outline-primary text-left active" id="nav-home-tab" data-toggle="pill" data-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                <i class="fas fa-user"></i> Student Profile
            </button>
            <button class="btn nav-link btn-outline-primary text-left" id="nav-profile-tab" data-toggle="pill" data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                <i class="fas fa-cog"></i> General Setting
            </button>
        <?php
        if ($this->session->userdata('user') != '150ba76c-4dd2-46e2-bf67-60123b0e2ce6') {
        ?>
            <button class="btn nav-link btn-outline-primary text-left" id="nav-contact-tab" data-toggle="pill" data-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">
                <i class="fa fa-user-cog"></i> Account Setting
            </button>
        <?php
        }
        ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <?= modules::run('student/student_profile', $student_data->student_id);?>
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <?= modules::run('student/form_student_setting', $student_data->student_id)?>
            </div>
            <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                <?= modules::run('student/form_account_setting', $student_data->student_id)?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" id="modal_change_prodi">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Study Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <u><h5>From</h5></u><br>
                        <table style="width: 100%" class="table-transfer">
                            <tr>
                                <td width="200px">Study Program</td>
                                <td>:</td>
                                <td>
                                    <select name="study_program_id_from" id="study_program_id_from" class="form-control" disabled>
                                        <option value=""><?=$student_data->study_program_name;?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Faculty</td>
                                <td>:</td>
                                <td>
                                    <select name="faculty_from" id="faculty_from" class="form-control" disabled>
                                        <option value=""><?=$student_data->faculty_name;?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Batch</td>
                                <td>:</td>
                                <td>
                                    <select name="academic_year_id_from" id="academic_year_id_from" class="form-control" disabled>
                                        <option value=""><?=$student_data->academic_year_id;?></td></option>
                                    </select>
                            </tr>
                            <tr>
                                <td>Student Number</td>
                                <td>:</td>
                                <td><?=$student_data->student_number;?></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>Resign Student</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <u><h5>To</h5></u><br>
                        <form url="<?=base_url()?>academic/student_academic/save_change_prodi" id="form_change_prodi" onsubmit="return false">
                            <input type="hidden" name="student_id" value="<?=$student_data->student_id;?>">
                            <table class="w-100 table-transfer">
                                <tr>
                                    <td style="width: 200px !important;" class="required_text">Study Program</td>
                                    <td>:</td>
                                    <td>
                                        <select name="study_program_id_target" id="study_program_id_target" class="form-control">
                                            <option value="" data-kode=""></option>
                                    <?php
                                    if ($study_program_lists) {
                                        foreach ($study_program_lists as $o_prodi) {
                                    ?>
                                            <option value="<?=$o_prodi->study_program_id;?>" data-kode="<?=$o_prodi->study_program_code;?>" data-fac="<?=$o_prodi->faculty_name;?>"><?=$o_prodi->study_program_name;?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Faculty</td>
                                    <td>:</td>
                                    <td><input type="text" name="faculty_name" id="faculty_name_target" class="form-control" disabled="disabled"></td>
                                </tr>
                                <tr>
                                    <td class="required_text">Batch</td>
                                    <td>:</td>
                                    <td>
                                        <select name="academic_year_id_target" id="academic_year_id_target" class="form-control">
                                            <option value=""></option>
                                    <?php
                                    if ($academic_year_list) {
                                        foreach ($academic_year_list as $o_year) {
                                            $selected = ($o_year->academic_year_id == $student_data->academic_year_id) ? 'selected="selected"' : '';
                                    ?>
                                            <option value="<?=$o_year->academic_year_id;?>" <?=$selected;?>><?=$o_year->academic_year_id;?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                        </select>
                                        <i><small class="text-danger">The Tuition Fee nominal will follow the batch</small></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>New Student Number</td>
                                    <td>:</td>
                                    <td><span id="new_student_number">XXXXXXXXXXX</span></td>
                                </tr>
                                <tr>
                                    <td>New Status</td>
                                    <td>:</td>
                                    <td>Active Student</td>
                                </tr>
                                <tr>
                                    <td class="required_text">Start Academic Year</td>
                                    <td>:</td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <select name="academic_year_semester" id="academic_year_semester" class="form-control">
                                                <option value=""></option>
                                    <?php
                                    if ($academic_year_list) {
                                        foreach ($academic_year_list as $o_year) {
                                            $selected = ($o_year->academic_year_id == $this->session->userdata('academic_year_id_active')) ? 'selected="selected"' : '';
                                    ?>
                                                <option value="<?=$o_year->academic_year_id;?>" <?=$selected;?>><?=$o_year->academic_year_id;?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                            </select>
                                            <select name="semester_type_semester" id="semester_type_semester" class="form-control">
                                                <option value=""></option>
                                    <?php
                                    if ((isset($semester_type_list)) AND ($semester_type_list)) {
                                        foreach ($semester_type_list as $o_semester_type) {
                                            $selected = ($o_semester_type->semester_type_id == $this->session->userdata('semester_type_id_active')) ? 'selected="selected"' : '';
                                    ?>
                                                <option value="<?=$o_semester_type->semester_type_id;?>" <?=$selected;?>><?=$o_semester_type->semester_type_name;?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="required_text">Semester</td>
                                    <td>:</td>
                                    <td>
                                        <select name="semester_id_accepted" id="semester_id_accepted" class="form-control">
                                            <option value=""></option>
                                    <?php
                                    if ((isset($semester_list)) AND ($semester_list)) {
                                        foreach ($semester_list as $o_semester) {
                                    ?>
                                            <option value="<?=$o_semester->semester_id;?>"><?=$o_semester->semester_number.'. '.$o_semester->semester_name;?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_submit_change_prodi">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('#study_program_id_target, #academic_year_id_target, #academic_year_semester, #semester_type_semester, #semester_id_accepted').select2({
        allowClear: true,
        placeholder: "Please select",
        theme: "bootstrap",
    });

    $('#study_program_id_target, #academic_year_id_target').on('change', function(e) {
        set_student_number();
        var prodi_el = document.getElementById('study_program_id_target');
        var faculty = prodi_el.options[prodi_el.selectedIndex].dataset.fac;

        $('#faculty_name_target').val(faculty);
    });

    $('#btn_submit_change_prodi').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_change_prodi');
        var data = form.serialize();
        var url = form.attr('url');

        Swal.fire({
            title: "Do you want to save the changes?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Submit",
        }).then((resultclicked) => {
            /* Read more about isConfirmed, isDenied below */
            // console.log(resultclicked);
            if (resultclicked.value) {
                $.blockUI({ baseZ: 2000 });
                $.post(url, data, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success');
                        window.location.reload();
                    }
                    else {
                        toastr.warning(result.message, 'Warning');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error');
                })
            }
        });
    });

    function set_student_number() {
        var prodi = document.getElementById("study_program_id_target");
        var batch = document.getElementById("academic_year_id_target");

        var prodi_kode = prodi.options[prodi.selectedIndex].dataset.kode;
        var batch_value = batch.value;

        prodi_kode = (prodi_kode == '') ? 'XX' : prodi_kode;
        batch_value = (batch_value == '') ? 'XXXX' : batch_value;

        var nim = '11' + batch_value + prodi_kode + 'XXX';
        // console.log(nim);
        // var prodi_kode = prodi.target.options[prodi.target.selectedIndex].dataset.kode;
        $('#new_student_number').html(nim);

        // console.log(prodi+ '-' +batch.val());
    }
})
</script>