<form url="<?=base_url()?>hris/save_employee" id="form_input_employee" onsubmit="return false">
    <input type="hidden" name="employee_id" id="employee_id">
    <!-- <div class="accordion" id="personal_form">
        <div class="card">
            <div class="card-header">
                Personal Form
                <div class="card-header-actions">
					<button class="card-header-action btn btn-link" id="btn_collapse_personal_form" type="button" data-toggle="collapse" data-target="#personal_form_collapse" aria-expanded="true" aria-controls="collapseOne">
                        Other Fields <i class="fa fa-plus"></i>
					</button>
				</div>
            </div>
            <div class="card-body">
                
                <div id="personal_form_collapse" class="collapse" aria-labelledby="headingOne" data-parent="#personal_form">
                    <hr>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>NIK / No Paspor</label>
                                <input type="text" class="form-control " name="personal_data_id_card_number" id="personal_data_id_card_number">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>NPWP Number</label>
                                <input type="text" class="form-control " name="personal_data_npwp_number" id="personal_data_npwp_number">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Place of Birth</label>
                                <input type="text" class="form-control " name="personal_data_place_of_birth" id="personal_data_place_of_birth">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="text" class="form-control " name="personal_data_date_of_birth" id="personal_data_date_of_birth">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Personal Email</label>
                                <input type="text" class="form-control " name="personal_data_email" id="personal_data_email">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Mother Maiden Name</label>
                                <input type="text" class="form-control " name="personal_data_mother_maiden_name" id="personal_data_mother_maiden_name">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Citizenship</label>
                                <select name="citizenship_id" id="citizenship_id" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Cellular Number</label>
                                <input type="text" class="form-control " name="personal_data_cellular" id="personal_data_cellular">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Religion</label>
                                <select name="religion_id" id="religion_id" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Blood</label>
                                <input type="text" class="form-control " name="personal_data_blood_group" id="personal_data_blood_group">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Marital Status</label>
                                <select name="personal_data_marital_status" id="personal_data_marital_status" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="accordion" id="employee_form">
        <div class="card">
            <div class="card-header">
                Employee Form
                <div class="card-header-actions">
					<button class="card-header-action btn btn-link" id="btn_collapse_employee_form" type="button" data-toggle="collapse" data-target="#employee_form_collapse" aria-expanded="true" aria-controls="Employee Form">
                        Other Fields <i class="fa fa-plus"></i>
					</button>
				</div>
            </div>
            <div class="card-body">
                
                <div id="employee_form_collapse" class="collapse" aria-labelledby="Employee Form" data-parent="#employee_form">
                    <hr>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Homebase</label>
                                <input type="text" class="form-control " name="employee_homebase" id="employee_homebase">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Employee Status</label>
                                <select name="employment_status" id="employment_status" class="form-control">
                                    <option value="">Please Select</option>
                                    <option value="PERMANENT">PERMANENT</option>
                                    <option value="NON-PERMANENT">NON-PERMANENT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-lg-9">
            <div class="form-group">
                <label class="required_text">Name</label>
                <div class="input-group">
                    <input type="text" class="form-control  w-25" name="personal_data_title_prefix" id="personal_data_title_prefix" placeholder="Title prefix">
                    <input type="text" class="form-control  w-50" name="personal_data_name" id="personal_data_name" placeholder="Fullname">
                    <input type="text" class="form-control  w-25" name="personal_data_title_suffix" id="personal_data_title_suffix" placeholder="Title suffix">
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="required_text">Gender</label>
                <select name="personal_data_gender" id="personal_data_gender" class="form-control">
                    <option value=""></option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row justify-content-around">
        <div class="col-lg-3">
            <div class="form-group">
                <label class="required_text">Status of the employee</label>
                <select name="employment_status" id="employment_status" class="form-control">
                    <option value=""></option>
                    <option value="PERMANENT">Permanent</option>
                    <option value="NON-PERMANENT">Non Permanent</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="required_text">General Occupation</label>
                <select name="employment_group" id="employment_group" class="form-control">
                    <option value=""></option>
                    <option value="ACADEMIC">Academic</option>
                    <option value="NONACADEMIC">Non Academic</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="required_text">Join Date</label>
                <input type="text" class="form-control" name="employee_join_date" id="employee_join_date">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="required_text">NIP</label>
                <input type="text" class="form-control " name="employee_id_number" id="employee_id_number">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="required_text">IULI Email</label>
                <input type="text" class="form-control " name="employee_email" id="employee_email">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="required_text">Job Title</label>
                <input type="text" class="form-control " name="employee_job_title" id="employee_job_title">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="required_text">Department</label>
                <select name="employee_department" id="employee_department" class="form-control">
                    <option value=""></option>
        <?php
        if ((isset($department_list)) AND ($department_list)) {
            foreach ($department_list as $o_department) {
        ?>
                    <option value="<?=$o_department->department_id;?>"><?=$o_department->department_name.' ('.$o_department->department_abbreviation.')';?></option>
        <?php
            }
        }
        ?>
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label class="required_text">Is Lecturer?</label>
                <select name="employee_is_lecturer" id="employee_is_lecturer" class="form-control">
                    <option value=""></option>
                    <option value="YES">YES</option>
                    <option value="NO">NO</option>
                </select>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="required_text">Lecturer Number</label>
                <!-- <input type="text" class="form-control " name="employee_lecturer_number" id="employee_lecturer_number"> -->
                <div class="input-group">
                    <div class="input-group-prepend">
                    <select class="custom-select" id="employee_lecturer_number_type" name="employee_lecturer_number_type" disabled>
                        <option value="NIDN">NIDN</option>
                        <option value="NUPN">NUPN</option>
                        <option value="NIDK">NIDK</option>
                        <option value="OTHERS">OTHERS</option>
                    </select>
                    </div>
                    <input type="text" class="form-control " name="employee_lecturer_number" id="employee_lecturer_number" disabled>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Academic Rank</label>
                <input type="text" class="form-control" name="employee_academic_rank" id="employee_academic_rank">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Homebase</label>
                <select name="employee_homebase_status" id="employee_homebase_status" class="form-control">
                    <option value=""></option>
                    <option value="homebase">Homebase</option>
                    <option value="non-homebase">Non Homebase</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>PKPT</label>
                <input type="text" class="form-control" name="employee_pkpt" id="employee_pkpt">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label>Working Hours</label>
                <select name="employee_working_hour_status" id="employee_working_hour_status" class="form-control">
                    <option value=""></option>
                    <option value="Semi Full Time">Semi Full Time</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Full Time">Full Time</option>
                </select>
            </div>
        </div>
    </div>
</form>
<script>
$(function() {
    // $('#personal_form_collapse').on('hidden.bs.collapse', function () {
    //     $('#btn_collapse_personal_form').html('Other Fields <i class="fa fa-plus"></i>');
    // });

    // $('#personal_form_collapse').on('shown.bs.collapse', function () {
    //     $('#btn_collapse_personal_form').html('Other Fields <i class="fa fa-minus"></i>');
    // });

    // $('#employee_form_collapse').on('hidden.bs.collapse', function () {
    //     $('#btn_collapse_employee_form').html('Other Fields <i class="fa fa-plus"></i>');
    // });

    // $('#employee_form_collapse').on('shown.bs.collapse', function () {
    //     $('#btn_collapse_employee_form').html('Other Fields <i class="fa fa-minus"></i>');
    // });

    $('#personal_data_gender, #employee_is_lecturer, #employee_department, #employment_status, #employment_group').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });

    $('#employee_join_date, #employment_status, #employment_group').on('change', function(e) {
        e.preventDefault();

        var joindate = $('#employee_join_date').val();
        var status = $('#employment_status').val();
        var group = $('#employment_group').val();
        
        setnip(status, group, joindate);
    });

    var datepicker_start = $('input#employee_join_date').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });

    $('#employee_is_lecturer').on('change', function(e) {
        e.preventDefault();
        if ($('#employee_is_lecturer').val() == 'YES') {
            $('#employee_lecturer_number_type').removeAttr('disabled');
            $('#employee_lecturer_number').removeAttr('disabled');
        }else{
            $('#employee_lecturer_number_type').attr('disabled', 'true');
            $('#employee_lecturer_number').attr('disabled', 'true');
        }
    });
});

function setnip(status, group, joindate) {
    var nip = '';
    if ((status != '') && (group != '') && ( joindate != '')) {
        var data_send = {
            status_employee: status, group_employee: group, joindate_employee: joindate
        }

        // console.log(data_send);
        $.post('<?=base_url()?>hris/get_nip_employee', data_send, function(result) {
            if (result.code != 0) {
                toastr.warning(result.message, 'Warning');
            }
            else {
                $('#employee_id_number').val(result.nip);
            }
        }, 'json').fail(function(params) {
            toastr.error('error generate nip', 'Error');
        });
    }
}
</script>