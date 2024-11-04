<form method="POST" id="form_create_curriculum_semester">
    <input type="hidden" name="curriculum_id" id="curriculum_id" value="<?= $curriculum_id;?>">
    <input type="hidden" name="term" id="term" value="<?= ($term) ? $term : '';?>">
    <input type="hidden" name="semester_key_id" id="semester_key_id" value="<?= ($o_curriculum_semester) ? $o_curriculum_semester->semester_id : ''; ?>">
    <div class="form-group">
        <label>Semester</label>
        <select name="semester_id" id="semester_id" class="form-control">
            <option value="" selected disabled>---</option>
    <?php
        if ($semester_list) {
            foreach ($semester_list as $semester) {
    ?>
            <option value="<?= $semester->semester_id;?>" <?= (($o_curriculum_semester) AND ($o_curriculum_semester->semester_id == $semester->semester_id)) ? 'selected' : ''; ?>><?= $semester->semester_name;?></option>
    <?php
            }
        }
    ?>
        </select>
    </div>
    <div class="form-group">
        <label>Total Credit Mandatory Fixed</label>
        <input type="text" class="form-control" name="curriculum_semester_total_credit_mandatory_fixed" id="curriculum_semester_total_credit_mandatory_fixed" value="<?= ($o_curriculum_semester) ? $o_curriculum_semester->curriculum_semester_total_credit_mandatory_fixed : '' ?>">
    </div>
    <div class="form-group">
        <label>Total Credit Elective Fixed</label>
        <input type="text" class="form-control" name="curriculum_semester_total_credit_elective_fixed" id="curriculum_semester_total_credit_elective_fixed" value="<?= ($o_curriculum_semester) ? $o_curriculum_semester->curriculum_semester_total_credit_elective_fixed : '' ?>">
    </div>
    <div class="form-group">
        <label>Total Credit Extracurricular Fixed</label>
        <input type="text" class="form-control" name="curriculum_semester_total_credit_extracurricular_fixed" id="curriculum_semester_total_credit_extracurricular_fixed" value="<?= ($o_curriculum_semester) ? $o_curriculum_semester->curriculum_semester_total_credit_extracurricular_fixed : '' ?>">
    </div>
    <div class="form-group <?= (($term) AND ($term != 'copy')) ? 'd-none' : '';?>">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="copy_subject" name="copy_subject" checked>
            <label class="custom-control-label" for="copy_subject">Copy with all subject data
        </div>
    </div>
    <button class="btn btn-info float-right" type="submit">Save</button>
</form>
<script>
    $('#form_create_curriculum_semester').on('submit', function(e) {
        e.preventDefault();
        var a_data = $('#form_create_curriculum_semester').serialize();
        
        $.blockUI({ baseZ: 2000 });

        $.post('<?= base_url()?>academic/curriculum/save_curriculum_semester_credit', a_data, function(result) {
            if(result.code == 0){
                    toastr['success']('curriculum semester has been saved', 'Success');
                    $('div#new_curriculum_semester_modal').modal('hide');
                    if ($.fn.DataTable.isDataTable(curriculum_list_table)) {
                        curriculum_list_table.ajax.reload(null, false);
                    }else{
                        window.location.reload();
                    }
                }
                else{
                    toastr['warning'](result.message, 'Warning!');
                }
            $.unblockUI();
        },'json').fail(function(params) {
            $.unblockUI();
        });
    });
</script>