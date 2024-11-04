<form id="form_input_employee" onsubmit="return false;" url="<?=base_url()?>employee/submit_employee">
    <input type="hidden" name="employee_id" id="employee_id">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="required_text">Name</label>
                <div class="input-group">
                    <input type="text" class="form-control  w-25" name="personal_data_title_prefix" id="personal_data_title_prefix" placeholder="Title prefix">
                    <input type="text" class="form-control  w-50" name="personal_data_name" id="personal_data_name" placeholder="Fullname">
                    <input type="text" class="form-control  w-25" name="personal_data_title_suffix" id="personal_data_title_suffix" placeholder="Title suffix">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control " name="personal_data_id_card_number" id="personal_data_id_card_number">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="required_text">Cellular Number</label>
                <input type="text" class="form-control " name="personal_data_cellular" id="personal_data_cellular">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label class="required_text">Personal Email</label>
                <input type="text" class="form-control " name="personal_data_email" id="personal_data_email">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="required_text">Gender</label>
                <select name="personal_data_gender" id="personal_data_gender" class="form-control ">
                    <option value="">Please select...</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" class="form-control " name="personal_data_date_of_birth" id="personal_data_date_of_birth">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="required_text">Employee Number</label>
                <input type="text" class="form-control " name="employee_id_number" id="employee_id_number">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>IULI Email</label>
                <input type="text" class="form-control " name="employee_email" id="employee_email">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Employee Status</label>
                <select name="employment_status" id="employment_status" class="form-control">
                    <option value="">Please Select</option>
                    <option value="PERMANENT">PERMANENT</option>
                    <option value="NON-PERMANENT">NON-PERMANENT</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="required_text">Is Lecturer?</label>
                <select name="employee_is_lecturer" id="employee_is_lecturer" class="form-control">
                    <option value="">Please Select</option>
                    <option value="YES">YES</option>
                    <option value="NO">NO</option>
                </select>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label>Lecturer Number</label>
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
    </div>
</form>
<script>
$(function() {
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
</script>