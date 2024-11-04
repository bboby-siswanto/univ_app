<form id="form_filter_offer_subject">
    <input type="hidden" name="curriculum_subject_id" id="curriculum_subject_id_cursub">
    <div class="form-group">
        <label>Program</label>
        <select name="program_id" id="program_id" class="form-control">
            <option value="">Please select...</option>
<?php
    if ($o_program_lists) {
        foreach ($o_program_lists as $program) {
?>
            <option value="<?= $program->program_id;?>"><?= $program->program_name;?></option>
<?php
        }
    }
?>
        </select>
    </div>
    <div class="form-group">
        <label id="filter_study_program_offered_subject">
            Study Program
            <div class="spinner-border-mini d-none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </label>
        <select name="study_program_id" id="study_program_id" class="form-control">
            <option value="">Please select...</option>
        </select>
    </div>
    <div class="form-group">
        <label>Academic Year</label>
        <select name="academic_year_id" id="academic_year_id" class="form-control">
            <option value="">Please select...</option>
<?php
    if ($o_academic_year_lists) {
        $s_academic_year_id_active = ($this->session->userdata('academic_year_id_active') !== null) ? $this->session->userdata('academic_year_id_active') : '';
        foreach ($o_academic_year_lists as $year) {
?>
            <option value="<?= $year->academic_year_id;?>" <?= ($s_academic_year_id_active == $year->academic_year_id) ? 'selected' : '' ?>><?= $year->academic_year_id.'-'.(intval($year->academic_year_id) + 1);?></option>
<?php
        }
    }
?>
        </select>
    </div>
    <div class="form-group">
        <label>Semester Type</label>
        <select name="semester_type_id" id="semester_type_id" class="form-control">
            <option value="">Please select...</option>
<?php
    if ($o_semester_type_lists) {
        $s_semester_type_id_active = ($this->session->userdata('semester_type_id_active') !== null) ? $this->session->userdata('semester_type_id_active') : '';
        foreach ($o_semester_type_lists as $semester) {
            // if ($semester->semester_type_id != 5) {
?>
            <option value="<?= $semester->semester_type_id;?>" <?= ($s_semester_type_id_active == $semester->semester_type_id) ? 'selected' : '' ?>><?= $semester->semester_type_name;?></option>
<?php
            // }
        }
    }
?>
        </select>
    </div>
    <div class="form-grorp">
        <button type="button" id="btn_filter_offered_subject" class="btn btn-info float-right">Fiter</button>
    </div>
</form>
<script>
    $('#program_id').val('1').trigger('change');
    show_study_program();
    $(function() {
        $('#program_id').on('change', function(e) {
            e.preventDefault();

            let program_id = $('#program_id').val();
            console.log(program_id);
            if (program_id == '') {
                $('#study_program_id').html('<option value="">Please select...</option>');
            }else {
                show_study_program();
            }
        });
    });

    function show_study_program(setprodi = false) {
        $('label#filter_study_program_offered_subject .spinner-border-mini').removeClass('d-none');
        let program_id = $('#program_id').val();

        $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
            $('label#filter_study_program_offered_subject .spinner-border-mini').addClass('d-none');
            var s_html = '<option value="" selected>Please select...</option>';

            if (result.code == 0) {
                $.each(result.data, function(index, value) {
                    var prodi_name = (program_id == '2') ? value.study_program_exp_name : value.study_program_name;
                    s_html += '<option value="' + value.study_program_id + '" data-abbr="'+value.study_program_abbreviation+'">' + value.faculty_name + ' - ' + prodi_name + '</option>';
                });
            }
            $('#study_program_id').html(s_html);

            if (setprodi) {
                $('#study_program_id').val(setprodi);
            }
        }, 'json').fail(function(params) {
            $('label#filter_study_program_offered_subject .spinner-border-mini').addClass('d-none');
            
            var s_html = '<option value="">Please select..</option><option value="All">All</option>';
            toastr.error('Error getting data!', 'Error');
        });
    }
</script>