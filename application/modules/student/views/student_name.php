<div class="row">
	<div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <strong class="text-primary">
                    <?= $personal_data->personal_data_name;?> <?= ($student_data) ? '('.$student_data[0]->study_program_abbreviation.'/'.$student_data[0]->academic_year_id.')' : ''; ?>
                </strong>
<?php
if (($this->session->has_userdata('module')) AND (in_array($this->session->userdata('module'), ['academic', 'finance']))) {
?>
                <div class="card-header-actions">
                    <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                        <i class="fas fa-sliders-h"></i> Quick Actions
                    </button>
                    <div class="dropdown-menu" aria-labelledby="settings_dropdown">
            <?php
            if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0','37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
            ?>
                        <a class="dropdown-item" href="<?= base_url()?>academic/student_academic/student_setting/<?=$student_data[0]->student_id;?>" class="card-header-action btn btn-link" data-toggle="tooltip" title="Settings">
                            <i class="fas fa-users-cog"></i> Student Settings
                        </a>
            <?php
            }
            ?>
                        <!-- <button id="btn_generate_ref_letter" class="dropdown-item card-header-action btn btn-link" type="button" data-toggle="modal" data-target="#modal_numbering_letter" title="Generate Ref Letter">
                            <i class="fas fa-sticky-note"></i> Ref Letter
                        </button> -->
                        <a class="dropdown-item" href="<?= base_url()?>academic/score/student_score/<?=$student_data[0]->student_id;?>" class="card-header-action btn btn-link" data-toggle="tooltip" title="Student Academics">
                            <i class="fas fa-graduation-cap"></i> Student Score
                        </a>
            <?php
            if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0','37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
            ?>
                        
            <?php
            }
            if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
                ?>
                            <a class="dropdown-item" href="<?= base_url()?>finance/invoice/lists/<?=$student_data[0]->personal_data_id;?>" class="card-header-action btn btn-link" data-toggle="tooltip" title="Study Activities">
                                <i class="fas fa-file-medical-alt"></i> Student Invoice
                            </a>
                <?php
                }
            ?>
            <?php
            if ($this->session->has_userdata('type') AND ($this->session->userdata('type') == 'staff')) {
            ?>
                        <a class="dropdown-item" href="<?= base_url()?>academic/student_academic/activity_study/<?=$student_data[0]->student_id;?>" class="card-header-action btn btn-link" data-toggle="tooltip" title="Study Activities">
                            <i class="fas fa-file-medical-alt"></i> Student Semester
                        </a>
                        <button class="dropdown-item" class="card-header-action btn btn-link" id="btn_show_modal_halfway" data-toggle="tooltip" title="Generate Transcript">
                            <i class="fas fa-layer-group"></i> Transcript
                        </button>
            <?php
            }
            ?>
                        
                    </div>
                </div>
<?php
}
?>
            </div>
        </div>
    </div>
</div>
<?php
if ($this->session->has_userdata('type') AND ($this->session->userdata('type') == 'staff')) {
?>
<div class="modal" tabindex="-1" role="dialog" id="modal_numbering_letter">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Number and Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="input_ref_letter" onsubmit="return false">
                    <input type="hidden" value="<?=$student_data[0]->student_id?>" name="student_id_letter" id="student_id_letter">
                    <div class="form-group">
                        <div class="row pb-2">
                            <div class="col-md-3">
                                <label>No.</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="number_letter" id="number_letter">
                            </div>
                        </div>
                        <div class="row pt-1">
                            <div class="col-md-3">
                                <label>Date.</label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="date_letter" id="date_letter">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="download_ref_letter" type="button" class="btn btn-primary">Download</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
<script>
    $(function() {
        // $('button#download_ref_letter').on('click', function(e) {
        //     e.preventDefault();

        //     $.blockUI({baseZ: 9999});
        //     var data = $('#input_ref_letter').serialize();
        //     $.post('<?=base_url()?>download/doc_download/generate_ref_letter', data, function(result) {
        //     // $.post('<?=base_url()?>download/pdf_download/generate_template_of_ref_letter', data, function(result) {
        //         $.unblockUI();
        //         if (result.code == 0) {
        //             $('#modal_numbering_letter').modal('hide');
        //             document.location.href = "<?=base_url()?>download/pdf_download/download_academic_file/" + result.file_name;
        //             // console.log(result);
        //         }else{
        //             toastr.warning(result.message, 'Warning!');
        //         }
        //     }, 'json').fail(function(params) {
        //         $.unblockUI();
        //         toastr.error('Error retrieve data!', 'Error');
        //     });
        //     // document.location.href = "<?=base_url()?>download/pdf_download/generate_template_of_ref_letter/" + '' + '/' + date_letter + '/' + number_letter;
        // });

        $('button#btn_show_modal_halfway').on('click', function(e) {
            e.preventDefault();
            var student_name = "<?= $personal_data->personal_data_name;?> <?= ($student_data) ? '('.$student_data[0]->study_program_abbreviation.'/'.$student_data[0]->academic_year_id.')' : ''; ?>";
            let student_academic_year = "<?= $student_data[0]->academic_year_id ?>";
            let student_semester_id = 1;
            
            $('#transcript_send_email').val('');
            $('#student_halfway').html(student_name);
            $('#halfway_student_id').val('<?= $student_data[0]->student_id ?>');
            $('div#filter_halfway_transcript').modal('show');
        });

        $('button#btn_generate_halfway').on('click', function(e) {
            e.preventDefault();
            $('#transcript_send_email').val('');
            var data = $('#form_filter_halfway').serialize();
            generate_halfway_transcript(data)
        });

        $('button#send_transcript_mail').on('click', function(e) {
            e.preventDefault();
            let email_message = CKEDITOR.instances.mail_message.getData();
            $('input#body_email').val(email_message);

            var data = $('#form_filter_halfway').serializeArray();
            var data_message = $('#form_send_mail').serializeArray();

            let param = objectify_form($.merge(data_message, data));
            generate_halfway_transcript(param);
        });

        $('button#btn_send_generate_halfway').on('click', function(e) {
            e.preventDefault();
            $('#mail_student_id').val('');
            $('#transcript_send_email').val('true');
            $('#mail_student').val('<?= $message_to ?>');
            // $('#mail_student').val('budi.siswanto1450@gmail.com; employee@company.ac.id');
            $('#mail_subject').val('Cumulative Transcript of Academic');
            CKEDITOR.instances.mail_message.setData("<?=$transcript_body;?>");
            $('#modal_email_halfway_transcript').modal('show');
        });
    });

    function generate_halfway_transcript(data) {
        $.blockUI({ baseZ: 2000 });
        $.post('<?=base_url()?>academic/score/generate_transcript_halfway', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#modal_email_halfway_transcript').modal('hide');
                $('div#filter_halfway_transcript').modal('hide');
                toastr.success('Success processing data', 'Success');
                url = '<?=base_url()?>academic/score/download_transcript/' + $('#halfway_student_id').val() + '/halfway/' + result.data;
                window.location.href = url;
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data', 'Error!');
        });
    }
</script>