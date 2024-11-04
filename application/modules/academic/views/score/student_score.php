<?= modules::run('student/show_name', $student_id, true);?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">Filter data</div>
            <div class="card-body">
                <?= modules::run('academic/score/view_filter_score');?>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                Score
        <?php
            // if ($this->session->userdata('type') == 'staff') {
        ?>
                <div class="card-header-actions">
                    <button class="btn btn-link card-header-action" data-toggle="dropdown" id="option_dropdown" aria-expanded="true">
                        <i class="fas fa-sliders-h"></i> Quick Actions
                    </button>
                    <div class="dropdown-menu" aria-labelledby="option_dropdown">
                        <button id="transcript_semester" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Transcript Semester Student">
                            <i class="fas fa-newspaper"></i> Download Transcript Semester
                        </button>
            <?php
            if ($this->session->userdata('type') == 'staff') {
                if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0','37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
            ?>
                        <button id="transcript_mid_semester" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Mid Term Transcript Student">
                            <i class="fas fa-newspaper"></i> Download Mid Transcript
                        </button>
                        <button id="btn_show_modal_halfway_student" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Halfway Transcript">
                            <i class="fas fa-layer-group"></i> Transcript Halfway
                        </button>
            <?php
                }
            }
            ?>
                    </div>
                </div>
        <?php
            // }
        ?>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="score-student-view">
                    <?= modules::run('academic/score/view_table_score_student', $student_id);?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                Supplement
        <?php
            if ($this->session->userdata('type') == 'staff') {
        ?>
                <div class="card-header-actions">
                    <button class="btn btn-link card-header-action" id="add_supplement">
                        <i class="fas fa-plus"></i> Add Supplement
                    </button>
                </div>
        <?php
            }
        ?>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="score-student-view">
                    <?= modules::run('academic/score/view_table_student_supplement', $student_id);?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="filter_halfway_transcript_student">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Halfway Transcript <span id="student_halfway_student"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_filter_halfway_student" onsubmit="return false;">
                    <input type="hidden" id="halfway_student_id_student" name="student_id">
                    <div class="row">
                        <?=modules::run('academic/score/form_filter_halfway', $student_id)?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_generate_halfway_student">Download Halfway Transcript</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('button#transcript_semester').on('click', function(e) {
            e.preventDefault();
            var table_count = $('table#table_score').DataTable().data();
            if (($('#filter_academic_year_id').val() == '') || ($('#filter_academic_year_id').val() == 'all') || ($('#filter_semester_type_id').val() == '')) {
                toastr.warning('Please select Academic Year and Semester Type!', 'Warning!');
            }else if(table_count.length  == 0){
                toastr.warning('No score data available in semester selected', 'Warning!');
            }else{
                window.location.href = '<?=base_url()?>academic/score/download_semester_transcript/<?=$student_id?>/' + $('#filter_academic_year_id').val() + '/' + $('#filter_semester_type_id').val();
            }
        });

        $('button#add_supplement').on('click', function(e) {
            e.preventDefault();
            var s_academic_year_id = $('#filter_academic_year_id').val();
            var s_semester_type_id = $('#filter_semester_type_id').val();
            var table_count = $('table#table_score').DataTable().data();

            if ((s_academic_year_id == '') || (s_semester_type_id == '')) {
                toastr.warning('Please select filter field!', "Warning!");
            }else if (table_count.length == 0){ 
                toastr.warning('No score available in semester selected!', 'Warning');
            }else{
                $('input#supplement_semester_type_id').val(s_semester_type_id);
                $('input#supplement_academic_year_id').val(s_academic_year_id);
                $('div#modal_supplement').modal('show');
            }
        });

        $('button#btn_show_modal_halfway_student').on('click', function(e) {
            e.preventDefault();

            var student_name = "<?= $student_data->personal_data_name.' ('.$student_data->study_program_abbreviation.'/'.$student_data->academic_year_id.')';?>";
            
            $('#student_halfway_student').html(student_name);
            $('#halfway_student_id_student').val('<?= $student_data->student_id ?>');
            $('div#filter_halfway_transcript_student').modal('show');
        });
        
        $('button#transcript_mid_semester').on('click', function(e) {
            e.preventDefault();
            var table_count = $('table#table_score').DataTable().data();
            if (($('#filter_academic_year_id').val() == '') || ($('#filter_academic_year_id').val() == 'all') || ($('#filter_semester_type_id').val() == '')) {
                toastr.warning('Please select Academic Year and Semester Type!', 'Warning!');
            }else if(table_count.length  == 0){
                toastr.warning('No score data available in semester selected', 'Warning!');
            }else{
                window.location.href = '<?=base_url()?>academic/score/download_mid_transcript/<?=$student_id?>/' + $('#filter_academic_year_id').val() + '/' + $('#filter_semester_type_id').val();
            }
        });

        $('button#btn_generate_halfway_student').on('click', function(e) {
            e.preventDefault();

            var data = $('#form_filter_halfway_student').serialize();

            $.blockUI({ baseZ: 2000 });
            $.post('<?=base_url()?>academic/score/generate_transcript_halfway', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('div#filter_halfway_transcript_student').modal('hide');
                    toastr.success('Success processing data', 'Success');
                    url = '<?=base_url()?>academic/score/download_transcript/' + $('#halfway_student_id_student').val() + '/halfway/' + result.data;
                    window.location.href = url;
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data', 'Error!');
            });
        });
    });
</script>