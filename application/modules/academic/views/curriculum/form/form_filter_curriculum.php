<div class="card">
    <div class="card-header">Filter Data</div>
    <div class="card-body">
        <form method="post" id="curriculum_filter_form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Program</label>
                        <select name="program_id" id="filter_program_id" class="form-control">
                            <option value="All">All</option>
                    <?php
                    if ($o_program_lists) {
                        foreach ($o_program_lists as $program) {
                    ?>
                            <option value="<?= $program->program_id?>"><?= $program->program_name?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label id="label_filter_study_program">
                            Study Program
                            <div class="spinner-border-mini d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </label>
                        <select name="study_program_id" id="filter_study_program_id" class="form-control">
                            <option value="All">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Active Year</label>
                        <select name="academic_year_id" id="filter_academic_year_id" class="form-control">
                            <option value="All">All</option>
<?php
    foreach ($o_academic_year as $year) {
?>
                            <option value="<?= $year->academic_year_id;?>" <?= ($year->academic_year_intake_status == 'active') ? 'selected' : '' ?>><?= $year->academic_year_id;?></option>
<?php
    }
?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" id="btn_filter_curriculum" class="btn btn-primary float-right">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function() {
        $('#filter_program_id').val('1').trigger('change');
        show_filter_study_program();

        $('#filter_program_id').on('change', function(e) {
            e.preventDefault();

            let program_id = $('#filter_program_id').val();
            if (program_id == '') {
                $('#filter_study_program_id').html('<option value="All">All</option>');
            }else {
                show_filter_study_program();
            }
        });

        function show_filter_study_program(setprodi = false) {
            let program_id = $('#filter_program_id').val();
            $('label#label_filter_study_program .spinner-border-mini').removeClass('d-none');

            $.post('<?=base_url()?>study_program/get_study_program_by_program/true', {program_id: program_id}, function(result) {
                $('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
                var s_html = '<option value="All">All</option>';
                if (result.code == 0) {
                    $.each(result.data, function(index, value) {
                        s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
                    });
                }
                $('#filter_study_program_id').html(s_html);
            }, 'json').fail(function(params) {
                $('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
                
                var s_html = '<option value="">Please select..</option><option value="All">All</option>';
                toastr.error('Error getting data!', 'Error');
            });
        }
    });
</script>