<form id="form_filter_krs_approval" onsubmit="return false">
    <div class="row">
        <div class="col-md-6 d-none">
            <div class="form-group">
                <label>Program</label>
                <select name="program_id" id="program_id" class="form-control">
                    <option value="">Please select...</option>
        <?php
            if ($program_lists) {
                foreach ($program_lists as $program) {
        ?>
                    <option value="<?= $program->program_id?>"><?= $program->program_name?></option>
        <?php
                }
            }
        ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-control">
                    <option value="">Please select...</option>
        <?php
            if ($academic_year_lists) {
                $s_academic_year_id_active = ($this->session->userdata('academic_year_id_active') !== null) ? $this->session->userdata('academic_year_id_active') : '';
                foreach ($academic_year_lists as $year) {
                    $selected = ($s_academic_year_id_active == $year->academic_year_id) ? 'selected' : '';
        ?>
                    <option value="<?= $year->academic_year_id?>" <?=$selected;?>><?= $year->academic_year_id?></option>
        <?php
                }
            }
        ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Study Program</label>
                <select name="study_program_id" id="study_program_id" class="form-control">
                    <option value="">Please select...</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Semester Type</label>
                <select name="semester_type_id" id="semester_type_id" class="form-control">
                    <option value="">Please select...</option>
        <?php
            if ($semester_type_lists) {
                $s_semester_type_id_active = ($this->session->userdata('semester_type_id_active') !== null) ? $this->session->userdata('semester_type_id_active') : '';
                foreach ($semester_type_lists as $semester) {
                    $selected = ($s_semester_type_id_active == $semester->semester_type_id) ? 'selected' : '';
        ?>
                    <option value="<?= $semester->semester_type_id?>" <?=$selected;?>><?= $semester->semester_type_name?></option>
        <?php
                }
            }
        ?>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="pull-right">
                <button id="btn_filter_krs_approval" class="btn btn-info float-right" type="button">Filter</button>
            </div>
        </div>
    </div>
</form>
<script>
    $('#program_id').val('1').trigger('change');
    show_study_program();
    $(function() {
        $('#program_id').on('change', function(e) {
            e.preventDefault();

            let program_id = $('#program_id').val();
            if (program_id == '') {
                $('#study_program_id').html('<option value="">Please select ...</option>');
            }else {
                show_study_program();
            }
        });
    });

    function show_study_program(setprodi = false) {
        let program_id = $('#program_id').val();
        $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
            var s_html = '<option value="">Please select ...</option>';
            if (result.code == 0) {
                $.each(result.data, function(index, value) {
                    s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
                });
            }
            $('#study_program_id').html(s_html);

            if (setprodi) {
                $('#study_program_id_program').val(setprodi);
            }
        }, 'json');
    }
</script>