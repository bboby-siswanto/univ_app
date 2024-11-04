<form onsubmit="return false" id="form_7">
    <input type="hidden" name="template_data" id="template_data" value="generate_lecturer_assignment">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="employee_key">Lecturer</label>
                <select name="employee_key" id="employee_key" class="form-control">
                    <option value=""></option>
            <?php
            if ((isset($employee_list)) AND ($employee_list)) {
                foreach ($employee_list as $o_employee) {
            ?>
                    <option value="<?=$o_employee->employee_id;?>"><?=ucwords(strtolower($o_employee->personal_data_name));?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="study_program_key">Study Program</label>
                <select name="study_program_key" id="study_program_key" class="form-control">
                    <option value=""></option>
            <?php
            if ((isset($study_program_list)) AND ($study_program_list)) {
                foreach ($study_program_list as $o_study_program) {
            ?>
                    <option value="<?=$o_study_program->study_program_id;?>"><?=$o_study_program->study_program_name.' ('.$o_study_program->study_program_abbreviation.')';?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="academic_semester_key">Academic Semester</label>
                <select class="custom-select" id="academic_semester_key" name="academic_semester_key">
                    <option value=""></option>
            <?php
            if ((isset($academic_year_list)) AND ($academic_year_list)) {
                foreach ($academic_year_list as $o_academic_year) {
            ?>
                    <option value="<?=$o_academic_year->academic_year_id;?>"><?=$o_academic_year->academic_year_id.' / '.(intval($o_academic_year->academic_year_id) + 1);?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="semester_type_key">Semester Type</label>
                <select class="custom-select" id="semester_type_key" name="semester_type_key">
                    <option value=""></option>
            <?php
            if ((isset($semester_type_list)) AND ($semester_type_list)) {
                foreach ($semester_type_list as $o_semester_type) {
            ?>
                    <option value="<?=$o_semester_type->semester_type_id;?>"><?=$o_semester_type->semester_type_name;?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-secondary float-right" type="button" data-dismiss="modal">Cancel</button>
            <button class="btn btn-info float-right" type="button" name="submit_form_7" id="submit_form_7">Generate</button>
        </div>
    </div>
</form>
<script>
$(function() {
    $('select#academic_semester_key, select#semester_type_key, select#study_program_key').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        cache: false
    });

    $('select#employee_key').select2({
        allowClear: true,
        placeholder: "Please select..",
        minimumInputLength: 1,
        theme: "bootstrap",
        cache: false
    });

    $('button#submit_form_7').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        var form = $('form#form_7');
        var form_request = $('form#form_letter_number');
        var data = form.serialize();
        var request = form_request.serialize();
        var url = "<?=base_url()?>apps/letter_numbering/generate_number";
        var request_data = request + '&' + data;
        request_data += '&template_key=' + $('select#template_list').val();

        $.post(url, request_data, function(result) {
            $.unblockUI();
            table_list.ajax.reload(null, false);
            if (result.code == 0) {
                $('#modal_select_template').modal('hide');
                var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                // var win = window.open(loc, '_blank');
                // if (win) {
                //     win.focus();
                // }
                // else {
                    window.location.href = loc;
                // }
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            table_list.ajax.reload(null, false);
            send_ajax_error(params.responseText);
            toastr.error('Error Processing request!', 'error');
        });
    });
})
</script>