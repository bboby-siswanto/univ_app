<form action="POST" id="form_create_curriculum">
    <input type="hidden" name="curriculum_subject_id" id="curriculum_subject_id" value="<?= ($o_subject_curriculum_data) ? $o_subject_curriculum_data->curriculum_subject_id : ''; ?>">
    <input type="hidden" name="curriculum_id" id="curriculum_id" value="<?= $curriculum_id;?>">
    <input type="hidden" name="semester_id_key" id="semester_id_key" value="<?= ($o_subject_curriculum_data) ? $o_subject_curriculum_data->semester_id : '';?>">
    <div class="form-group">
        <label>Semester</label>
        <select name="semester_id" id="semester_id" class="form-control">
            <option value="" selected disabled>---</option>
    <?php
        if ($semester_list) {
            foreach ($semester_list as $semester) {
                $selected = '';
                if (($o_subject_curriculum_data) AND ($o_subject_curriculum_data->semester_id == $semester->semester_id)) {
                    $selected = 'selected';
                }else if(($s_semester_id) AND ($s_semester_id == $semester->semester_id)) {
                    $selected = 'selected';
                }
    ?>
            <option value="<?= $semester->semester_id;?>" <?= $selected;?>><?= $semester->semester_name;?></option>
    <?php
            }
        }
    ?>
        </select>
    </div>
    <div class="form-group">
        <label>Curriculum Subject Category</label>
        <select class="form-control" name="curriculum_subject_category" id="curriculum_subject_category">
            <option value="" disabled>---</option>
<?php
if ((isset($o_curriculum_subject_category)) AND ($o_curriculum_subject_category)) {
    foreach ($o_curriculum_subject_category as $s_subject_category) {
        $select = '';
        if (($o_subject_curriculum_data) AND ($o_subject_curriculum_data->curriculum_subject_category == $s_subject_category)) {
            $select = 'true';
        }else if ($s_subject_category == 'regular semester') {
            $select = 'true';
        }
?>
            <option value="<?=$s_subject_category;?>" <?=$select;?>><?= ucwords($s_subject_category)?></option>
<?php
    }
}
?>
        </select>
    </div>
    <div class="form-group">
        <label>Curriculum Subject Type</label>
        <select class="form-control" name="curriculum_subject_type" id="curriculum_subject_type">
            <option value="" selected disabled>---</option>
            <option value="mandatory" <?= (($o_subject_curriculum_data) AND ($o_subject_curriculum_data->curriculum_subject_type == 'mandatory')) ? 'selected' : ''; ?>>Mandatory</option>
            <!-- <option value="elective" <?= (($o_subject_curriculum_data) AND ($o_subject_curriculum_data->curriculum_subject_type == 'elective')) ? 'selected' : ''; ?>>Elective</option> -->
            <option value="elective" selected="">Elective</option>
            <option value="extracurricular"<?= (($o_subject_curriculum_data) AND ($o_subject_curriculum_data->curriculum_subject_type == 'extracurricular')) ? 'selected' : ''; ?>>Extracurricular</option>
        </select>
    </div>
    <div class="form-group">
        <label>Subject</label>
        <input type="text" class="form-control" name="subject_name" id="subject_name" value="<?= ($o_subject_curriculum_data) ? $o_subject_curriculum_data->subject_name : ''; ?>">
        <input type="hidden" name="subject_id" id="subject_id" value="<?= ($o_subject_curriculum_data) ? $o_subject_curriculum_data->subject_id : ''; ?>">
        <small id="text_subject_not_found" class="d-none">Subject not found? <a href="#" id="activated_subject">Click here</a></small>
    </div>
    <div class="form-group">
        <label>Subject Code</label>
        <input type="text" class="form-control prop_disable" name="subject_code" id="subject_code" disabled="true" value="<?= ($o_subject_curriculum_data) ? $o_subject_curriculum_data->subject_code : ''; ?>">
    </div>
    <div class="form-group">
        <label>Subject Type</label>
        <select name="subject_type" id="subject_type" class="form-control prop_disable" disabled>
                    <option value="" disabled selected>---</option>
            <?php
                foreach($o_subject_type as $type) {
            ?>
                    <option value="<?= $type->id_jenis_mata_kuliah;?>" <?= (($o_subject_curriculum_data) AND ($o_subject_curriculum_data->id_jenis_mata_kuliah == $type->id_jenis_mata_kuliah)) ? 'selected' : ''; ?>><?= $type->nama_jenis_mata_kuliah?></option>
            <?php
                }
            ?> 
        </select>
    </div>
    <div class="form-group">
        <label>Subject Credit (SKS)</label>
        <input type="text" class="form-control prop_disable" name="subject_credit" id="subject_credit" disabled="true" value="<?= ($o_subject_curriculum_data) ? $o_subject_curriculum_data->subject_credit : ''; ?>">
    </div>
    <button type="submit" class="btn btn-info float-right">Save</button>
</form>
<script>
    $(function() {
        var prop_input = $('.prop_disable');
        prop_input.prop('disabled', true);

        $('#subject_name').autocomplete({
            minLength: 1,
            autoFocus: true,
            appendTo: 'form#form_create_curriculum',
            source: function(request, response) {
                var url = '<?=site_url('academic/subject/get_subject_name')?>';
                var data = {
                    term: request.term,
                    from_curriculum: 'true'
                };
                $.post(url, data, function(rtn){
                    if (rtn.data) {
                        var arr = [];
                        arr = $.map(rtn.data, function(m){
                            return {
                                id: m.subject_name,
                                value: m.subject_name + '  ( ' + m.study_program_abbreviation + '/' + m.subject_code + ' )',
                                subject_data: m
                            }
                        });
                        response(arr);
                        $('small#text_subject_not_found').addClass('d-none');
                    }else{
                        $('input#subject_id').val('');
                        $('small#text_subject_not_found').removeClass('d-none');
                    }
                }, 'json');
            },
            select: function(event, ui){
                var id = ui.item.id;
                var subject_data = ui.item.subject_data;
                prop_input.prop('disabled', true);
                $('input#subject_id').val(subject_data.subject_id)
                $('input#subject_code').val(subject_data.subject_code);
                // $('input#school_found_status').val('1');
                $('select#subject_type').val(subject_data.id_jenis_mata_kuliah);
                $('input#subject_credit').val(subject_data.subject_credit);
                
            },
            change: function(event, ui){
                if(ui.item === null){
                    $('input#subject_code').val('');
                    $('select#subject_type').val('');
                    $('input#subject_credit').val('');

                    $('input#subject_id').val('');
                    $('small#text_subject_not_found').removeClass('d-none');
                    // $('input#school_found_status').val('0');
                }else{
                    $('small#text_subject_not_found').addClass('d-none');
                }
            }
        });

        $('#activated_subject').on('click', function(e) {
            e.preventDefault();
            prop_input.prop('disabled', false);
            $('#subject_id').val('');
            $('#subject_code').focus();
        });

        $('#form_create_curriculum').on('submit', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });
            var a_data = $('#form_create_curriculum').serialize();
            $.post('<?= base_url()?>academic/curriculum/save_curriculum_subject', a_data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr['success']('curriculum data has been saved', 'Success');
                    $('div#new_curriculum_modal').modal('hide');
                    if ($.fn.DataTable.isDataTable(curriculum_list_table)) {
                        curriculum_list_table.ajax.reload();
                    }else{
                        window.location.reload();
                    }
                }else{
                    toastr['warning'](result.message, 'Warning!');
                }
            },'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });
        });
    });
</script>