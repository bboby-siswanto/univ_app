<form method="POST" id="form_save_curriculum">
    <input type="hidden" name="term" id="term" value="<?= ($term) ? $term : '';?>">
    <input type="hidden" name="curriculum_id" id="curriculum_id" value="<?= ($o_curriculum_data) ? $o_curriculum_data->curriculum_id : ''; ?>">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label>Curriculum Name</label>
                <input type="text" class="form-control" name="curriculum_name" id="curriculum_name" value="<?= ($o_curriculum_data) ? $o_curriculum_data->curriculum_name : ''; ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Active Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-control">
                    <option value="">Please select...</option>
            <?php
                if ($o_academic_year_lists) {
                    foreach ($o_academic_year_lists as $year) {
            ?>
                    <option value="<?= $year->academic_year_id;?>"><?=$year->academic_year_id?></option>
            <?php
                    }
                }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>Program</label>
                <select name="program_id" id="program_id" class="form-control">
                    <option value="">Please select ...</option>
            <?php
                foreach ($o_program_lists as $program) {
            ?>
                    <option value="<?= $program->program_id;?>"><?= $program->program_name;?></option>
            <?php
                }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group">
                <label id="label_filter_study_program_id">
                    Study Program
                    <div class="spinner-border-mini d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </label>
                <select name="study_program_id" id="study_program_id_program" class="form-control">
                    <option value="">Please select ... </option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div id="form-copy"class="form-group d-none">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="copy_subject" name="copy_subject" checked>
                    <label class="custom-control-label" for="copy_subject">Copy with all semester and subject data
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <button class="btn btn-info float-right" id="btn_save_currculum" type="submit">Save</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $('form#form_save_curriculum').on('submit', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = $('form#form_save_curriculum').serialize();
            
            $.post('<?= base_url()?>academic/curriculum/save_curriculum_data', data, function(result) {
                if(result.code == 0){
                    toastr['success']('curriculum data has been saved', 'Success');
                    $('div#new_curriculum_modal').modal('hide');
                    $('#filter_program_id').val($('#program_id').val());
                    show_study_program();
                    $('#filter_academic_year_id').val($('#academic_year_id').val());
                }
                else{
                    toastr['warning'](result.message, 'Warning!');
                }
                $.unblockUI();
            },'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });
            return false;
        });

        $('#program_id').on('change', function(e) {
            e.preventDefault();

            let program_id = $('#program_id').val();
            if (program_id == '') {
                $('#study_program_id_program').html('<option value="">Please select ...</option>');
            }else {
                show_study_program();
            }
        });
    });

    function show_study_program(setprodi = false) {
        let program_id = $('#program_id').val();
        $('label#label_filter_study_program_id .spinner-border-mini').removeClass('d-none');

        $.post('<?=base_url()?>study_program/get_study_program_by_program/true', {program_id: program_id}, function(result) {
            $('label#label_filter_study_program_id .spinner-border-mini').addClass('d-none');
            var s_html = '<option value="">Please select ...</option>';

            if (result.code == 0) {
                $.each(result.data, function(index, value) {
                    s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
                });
            }
            $('#study_program_id_program').html(s_html);

            if (setprodi) {
                $('#study_program_id_program').val(setprodi);
            }
        }, 'json').fail(function(params) {
            $('label#label_filter_study_program_id .spinner-border-mini').addClass('d-none');
            
            var s_html = '<option value="">Please select..</option><option value="All">All</option>';
            toastr.error('Error getting data!', 'Error');
        });
    }
</script>