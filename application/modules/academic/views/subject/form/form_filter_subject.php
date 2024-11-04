<div class="card">
    <div class="card-header">Filter Data</div>
    <div class="card-body">
        <form method="post" id="subject_filter_form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Program</label>
                        <select name="program_id" id="filter_program_id" class="form-control">
                            <option value="">Please select..</option>
                    <?php
                        foreach($o_program_lists as $program) {
                    ?>
                            <option value="<?= $program->program_id;?>"><?= $program->program_name ?></option>
                    <?php
                        }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label id="label_search_study_program">
                            Study Program
                            <div class="spinner-border-mini d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </label>
                        <select name="study_program_id" id="filter_study_program_id" class="form-control">
                            <option value="">Please select..</option>
                            <option value="All">All</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" id="btn_filter_subject" class="btn btn-primary float-right">Filter</button>
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
                $('#filter_study_program_id').html('<option value="">Please select..</option>');
            }else {
                show_filter_study_program();
            }
        });

        function show_filter_study_program(setprodi = false) {
            let program_id = $('#filter_program_id').val();
            $('label#label_search_study_program .spinner-border-mini').removeClass('d-none');

            $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
                $('label#label_search_study_program .spinner-border-mini').addClass('d-none');
                var s_html = '<option value="">Please select..</option><option value="All">All</option>';
                if (result.code == 0) {
                    $.each(result.data, function(index, value) {
                        s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
                    });
                }
                $('#filter_study_program_id').html(s_html);

                if (setprodi) {
                    $('#study_program_id_program').val(setprodi);
                }
            }, 'json').fail(function(params) {
                $('label#label_search_study_program .spinner-border-mini').addClass('d-none');
                var s_html = '<option value="">Please select..</option><option value="All">All</option>';
                toastr.error('Error getting data!', 'Error');
            });
        }
    });
</script>