<style>
    .table-responsive {
        min-height: 600px !important;
    }
</style>
<div class="card">
    <div class="card-header">
        Student List
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                <button id="btn_blast_mail" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Send Email to All Student Selected">
                    <i class="fas fa-mail-bulk"></i> Blast Mail
                </button>
        <?php
        if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        ?>
                <!-- <button id="btn_download_cumulative_gpa" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="modal" data-target="#modal_gpa_recapitulation">
                    <i class="fas fa-mail-bulk"></i> Download Cumulative GPA v2
                </button> -->
        <?php
        }
        ?>
                <button id="btn_dl_cumulative_gpa" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="modal" data-target="#modal_gpa_recapitulation">
                    <i class="fas fa-mail-bulk"></i> Download Cumulative GPA
                </button>
                <button id="send_transcript_semester" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Send Transcript Semester">
                    <i class="fas fa-mail-bulk"></i> Send Transcript Semester
                </button>
                <button id="btn_feeder_sync" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Syncronize student profile to feeder">
                    <i class="fas fa-building"></i> Sync to Feeder
                </button>
        <?php
            if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
        ?>
                <button id="btn_generate_bulk_ijazah" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Bulk Ijazah ND Version 1">
                    <i class="fas fa-graduation-cap"></i> Generate Ijazah ND Selected
                </button>
                <!-- <button id="btn_generate_bulk_ijazah_nd2" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Bulk Ijazah ND Version 2">
                    <i class="fas fa-graduation-cap"></i> Generate Ijazah ND Selected Ver. Vice Rector
                </button> -->
                <button id="btn_generate_bulk_ijazah_ijd" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Bulk Ijazah IJD">
                    <i class="fas fa-graduation-cap"></i> Generate Ijazah IJD Selected
                </button>
                <!-- <button id="btn_generate_bulk_ijazah_ijd1" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Bulk Ijazah IJD NGETES JERMAN VERSI 1">
                    <i class="fas fa-graduation-cap"></i> Generate Ijazah IJD Selected Ver GERMAN 1
                </button> -->
                <!-- <button id="btn_generate_bulk_ijazah_ijd2" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Bulk Ijazah IJD NGETES JERMAN VERSI 2">
                    <i class="fas fa-graduation-cap"></i> Generate Ijazah IJD Selected Ver GERMAN 2
                </button> -->
                <button id="btn_show_graduated_transcript" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Generate Transcript Graduation for Student Filter">
                    <i class="fas fa-mail-bulk"></i> Generate Transcript Graduation
                </button>
        <?php
            }
        ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="student_list_academic" class="table table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th></th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>IULI Email</th>
                        <th>Personal Email</th>
                        <th>Faculty</th>
                        <th>Study Program</th>
                        <th>Student Type</th>
                        <th>Prodi Code</th>
                        <th>Place of Birth</th>
                        <th>Date of Birth</th>
                        <th>Identification Number</th>
                        <th>Gender</th>
                        <th>Personal Cellular</th>
                        <th>Status</th>
                        <th>Graduated Year</th>
                        <th>Graduated Date</th>
                        <th>Covid Vaccine</th>
                        <th>Covid Vaccine Data</th>
                        <th>Internship / Thesis</th>
                        <th>GP Semester</th>
                        <th>Title</th>
                        <th>Title Abbreviation</th>
                        <th>Thesis Title</th>
                        <th>Nationality</th>
                        <th>Mother Maiden Name</th>
                        <th>Parent Name</th>
                        <th>Parent Phone</th>
                        <th>Parent Cellular</th>
                        <th>Parent Email</th>
                        <th>Parent Ocupation</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal_filter_krs" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Academic Year</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_filter_krs" onsubmit="return false;">
                    <input type="hidden" id="krs_personal_data_id" name="krs_personal_data_id">
                    <div class="form-group">
                        <label>Academic Year</label>
                        <select name="academic_year_id" id="krs_academic_year_id" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                    if ($mbo_academic_year) {
                        foreach ($mbo_academic_year as $year) {
                    ?>
                            <option value="<?=$year->academic_year_id?>"><?=$year->academic_year_id?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Semester Type</label>
                        <select name="semester_type_id" id="krs_semester_type_id" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                    if ($mbo_semester_type) {
                        foreach ($mbo_semester_type as $semester) {
                    ?>
                            <option value="<?=$semester->semester_type_id?>"><?=$semester->semester_type_name?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="submit_show_krs" name="submit_show_krs" type="button" class="btn btn-info">Submit</button>
            </div>
        </div>
    </div>
</div>
<div id="modal_send_email" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email to Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= modules::run('messaging/academic_email_form');?>
            </div>
            <div class="modal-footer">
                <button id="send_mail_student" type="button" class="btn btn-primary">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
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
                    <input type="hidden" name="student_id_letter" id="student_id_letter">
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
                                <input type="date" class="form-control" name="date_letter" id="date_letter" value="<?= date('Y-m-d') ?>">
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
<div class="modal" tabindex="-1" role="dialog" id="modal_dikti_sync" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Syncronize student data</h5>
            </div>
            <div class="modal-body">
            <span id="spinner_loader"></span><strong id="stage_sync"></strong>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 1%;" aria-valuemax="100" id="value_progress"></div>
            </div>
                <div id="sync_result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_finish_sync" class="btn btn-primary d-none" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal_graduated_transcript" tab-index="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Graduated Transcript</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <form id="form_generate_graduated_transcript" onsubmit="return false;">
                            <input type="hidden" name="download_student_id" value="" id="download_student_id">
                            <div class="form-group">
                                <label for="transcript_degree" class="required_text">Select Degree:</label>
                                <select name="transcript_degree" id="transcript_degree" class="form-control">
                                    <option value="">Please select..</option>
                                    <option value="IJD" selected="selected">IJD</option>
                                    <option value="ND">ND</option>
                                </select>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="graduation_date">Date of Issuance:</label>
                                <input type="date" name="graduation_date" id="graduation_date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="transcript_date">Rectorate Date:</label>
                                <input type="date" name="transcript_date" id="transcript_date" class="form-control">
                            </div>
                            <div class="form-group" id="input_ijd_date">
                                <label for="ijd_date">IJD Date:</label>
                                <input type="date" name="ijd_date" id="ijd_date" class="form-control">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_generate_graduated_transcript" class="btn btn-info">Generate Transcript</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_gpa_recapitulation">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="filter_gpa_recap">
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox float-right">
                                <input type="checkbox" class="custom-control-input" id="gpa_recap_all_semester" name="gpa_recap_all_semester">
                                <label class="custom-control-label" for="gpa_recap_all_semester">All Semester</label>
                            </div>
                        </div>
                    </div>
                    <div class="row gpa_recap_selected_semester">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Academic Year</label>
                                <select name="gpa_recap_academic_year_id" id="gpa_recap_academic_year_id" class="form-control">
                        <?php
                        if (isset($mbo_academic_year) AND $mbo_academic_year) {
                            foreach ($mbo_academic_year as $year) {
                                $selected = ($this->session->has_userdata('academic_year_id_active') AND ($this->session->userdata('academic_year_id_active') == $year->academic_year_id)) ? 'selected="selected"' : '';
                        ?>
                                    <option value="<?=$year->academic_year_id?>" <?=$selected;?>><?=$year->academic_year_id?></option>
                        <?php
                            }
                        }
                        ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Semester Type</label>
                                <select name="gpa_recap_semester_type_id" id="gpa_recap_semester_type_id" class="form-control">
                        <?php
                        if ((isset($mbo_semester_type)) AND ($mbo_semester_type)) {
                            foreach ($mbo_semester_type as $semester) {
                                $selected = ($this->session->has_userdata('semester_type_id_active') AND ($this->session->userdata('semester_type_id_active') == $semester->semester_type_id)) ? 'selected="selected"' : '';
                                if (in_array($semester->semester_type_id, [1,2,3,7,8])) {
                        ?>
                                    <option value="<?=$semester->semester_type_id?>" <?=$selected;?>><?=$semester->semester_type_name?></option>
                        <?php
                                }
                            }
                        }
                        ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row gpa_recap_selected_semester">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="gpa_recap_include_repeat" name="include_repeat" checked="true">
                                    <label class="custom-control-label" for="gpa_recap_include_repeat">Include Last Repetition (IPS)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="gpa_feeder_check" name="gpa_feeder_check">
                                    <label class="custom-control-label" for="gpa_feeder_check">for Feeder</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_download_gpa_recap" class="btn btn-primary">Download</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_select_semester">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="filter_download_cummulative_gpa">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="option_last_semester" name="option_semester" value="prev_semester">
                        <label class="custom-control-label" for="option_last_semester">Previous Semester</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="option_all_semester" name="option_semester" value="all_semester">
                        <label class="custom-control-label" for="option_all_semester">All Semester</label>
                    </div>
                    <div class="form-group form_last_semester d-none">
                        <hr>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="include_short_semester" name="include_short_semester" checked="true">
                            <label class="custom-control-label" for="include_short_semester">Include Last Short Semester</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="include_repeat" name="include_repeat" checked="true">
                            <label class="custom-control-label" for="include_repeat">Include Last Repetition (IPS)</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_download_cummulative_gpa" class="btn btn-primary">Download</button>
            <?php
                if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            ?>
                <button type="button" id="submit_download_cummulative_gpa_for_validation" class="btn btn-warning">Download for Forlap Validation</button>
            <?php
                }
            ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
    $s_internship_input_form = modules::run('academic/student_academic/form_input_internhip_application_modal');
    $modal_input_internship = modules::run('layout/compose_modal', array(
        'modal_title' => 'Appliation Letter for Internship',
        'modal_body' => $s_internship_input_form
    ));

    $s_transcript_draft_input_form = modules::run('messaging/trancript_draft_input');
    $modal_input_draft_transcript = modules::run('layout/compose_modal', array(
        'modal_title' => 'Draft Transcript',
        'modal_body' => $s_transcript_draft_input_form
    ));

    $s_reff_letter_germany_form = modules::run('academic/student_academic/ref_letter_germany');
    $modal_input_reff_letter_germany_form = modules::run('layout/compose_modal', array(
        'modal_title' => 'Ref Letter to Germany',
        'modal_body' => $s_reff_letter_germany_form
    ));

    $s_form_add_institution = modules::run('institution/form_institution');
    $modal_input_institution = modules::run('layout/compose_modal', array(
        'modal_title' => 'Add Institution',
        'modal_body' => $s_form_add_institution
    ));
    
    $s_form_temporary_graduation = modules::run('academic/student_academic/form_letter_temporary_graduation');
    $modal_input_temporary_graduation = modules::run('layout/compose_modal', array(
        'modal_title' => 'Temporary Graduation Letter',
        'modal_body' => $s_form_temporary_graduation
    ));

    $s_form_english_as_medium_instruction = modules::run('academic/student_academic/form_english_as_medium_instruction');
    $modal_input_form_english_as_medium_instruction = modules::run('layout/compose_modal', array(
        'modal_title' => 'Form Input English as Medium Instuction',
        'modal_body' => $s_form_english_as_medium_instruction
    ));

    $s_form_internship_data_student = modules::run('academic/student_academic/form_view_internship');
    $modal_internship_data_student = modules::run('layout/compose_modal', array(
        'modal_title' => 'Internship Student',
        'modal_body' => $s_form_internship_data_student
    ));

    print($modal_input_internship);
    print($modal_input_draft_transcript);
    print($modal_input_reff_letter_germany_form);
    print($modal_input_institution);
    print($modal_input_temporary_graduation);
    print($modal_input_form_english_as_medium_instruction);
    print($modal_internship_data_student);
?>
<script>
$(function() {
    $('#gpa_recap_all_semester').on('click', function(e) {
        if ($('#gpa_recap_all_semester').is(':checked')) {
            $('.gpa_recap_selected_semester').find('input, select').attr('disabled', true);
        }
        else {
            $('.gpa_recap_selected_semester').find('input, select').removeAttr('disabled');
        }
    });

    $('#option_last_semester').on('click', function(e) {
        // e.preventDefault();

        if ($('#option_last_semester').is(':checked')) {
            $('div.form_last_semester').removeClass('d-none');
        }
    });

    $('#option_all_semester').on('click', function(e) {
        // e.preventDefault();

        if ($('#option_all_semester').is(':checked')) {
            $('div.form_last_semester').addClass('d-none');
        }
    });
    
    $('button#send_transcript_semester').on('click', function(e) {
        e.preventDefault();

        if ($('#academic_year_id').val() == 'all') {
            toastr.warning('Please set academic year and study program is not "all"!');
        }else{
            CKEDITOR.instances.draft_message.setData('<?=$draft_transcript_template;?>');
            $('#draft_transcript_modal').modal('show');
            // console.log('nongol!');
        }
    });

    $('button#submit_download_gpa_recap').on('click', function(e) {
        e.preventDefault();
        let data = $('form#student_filter_form, form#filter_gpa_recap').serialize();

        $.blockUI({ baseZ: 2000 });
        $.post('<?=base_url()?>academic/student_academic/download_cummulative_gpa2', data, function(result) {
            $.unblockUI();

            $('#modal_select_semester').modal('hide');
            if (result.code == 0) {
                window.location.href = '<?=base_url()?>download/excel_download/download_file/' + result.data + '/cummulative_gpa';
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error generate cummulative gpa!', 'Error!');
        });
    });

    $('button#submit_download_cummulative_gpa').on('click', function(e) {
        e.preventDefault();
        var value_selected = $("input[name='option_semester']:checked").val();
        
        let data = $('form#student_filter_form, form#filter_download_cummulative_gpa').serialize();
        // data += "&option_semester=" + value_selected;

        $.blockUI({ baseZ: 2000 });
        $.post('<?=base_url()?>academic/student_academic/download_cummulative_gpa', data, function(result) {
            $.unblockUI();

            $('#modal_select_semester').modal('hide');
            if (result.code == 0) {
                window.location.href = '<?=base_url()?>download/excel_download/download_file/' + result.data + '/cummulative_gpa';
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error generate cummulative gpa!', 'Error!');
        });
    });

    $('button#submit_download_cummulative_gpa_for_validation').on('click', function(e) {
        e.preventDefault();
        var value_selected = $("input[name='option_semester']:checked").val();
        
        let data = $('form#student_filter_form, form#filter_download_cummulative_gpa').serialize();
        // data += "&option_semester=" + value_selected;

        $.blockUI({ baseZ: 2000 });
        $.post('<?=base_url()?>academic/student_academic/download_internal_cummulative_gpa', data, function(result) {
            $.unblockUI();

            $('#modal_select_semester').modal('hide');
            if (result.code == 0) {
                window.location.href = '<?=base_url()?>download/excel_download/download_file/' + result.data + '/cummulative_gpa_validation';
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error generate cummulative gpa!', 'Error!');
        });
    });

	$('select[id="academic_year_id"]').val(<?=($o_year_now) ? $o_year_now->academic_year_id : '';?>);
    // $('select[name="student_status"]').val('active');
    $('select[name="student_status[]"]').val('active');
    $('.selectpicker').selectpicker('refresh');
    let accepted_button = '<?=$accepted_button;?>';

    let uri = '<?=site_url('student/filter_student_academic')?>';
    var table_student_list = $('table#student_list_academic').DataTable({
        processing: true,
        ajax: {
            url: uri,
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#student_filter_form').serialize();
                // var a_filter_data = objectify_form(a_form_data);
                return a_form_data;
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Download Excel',
                extend: 'excel',
                title: 'Student List Data',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                text: 'Download Pdf',
                extend: 'pdf',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
            },
            {
                text: 'Print',
                extend: 'print',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
            },
            // {
            //     text: 'Column Visibility',
            //     action: function () {
            //         // show columns
            //     }
            // },
            'colvis'
        ],
        columns: [
            {
                data: 'student_id',
                orderable: false,
                className: 'select-checkbox',
                render: function(data, type, ui) {
                    var html = '<input type="hidden" value="' + data + '" name="student_id">';
                    return html;
                }
            },
            { data: 'student_number' },
            { data: 'personal_data_name' },
            {
                data: 'academic_year_id',
                visible: false
            },
            { data: 'student_email' },
            { 
                data: 'personal_data_email',
                visible: false
            },
            { 
                data: 'faculty_name',
                visible: false
            },
            { data: 'study_program_name' },
            {
                data: 'student_type',
                visible: false,
                render: function(data, type, row) {
                    return (data === null) ? '' : data.toUpperCase();
                }
            },
            {
                data: 'dikti_code',
                visible: false
            },
            {
                data: 'personal_data_place_of_birth',
                visible: false
            },
            { 
                data: 'personal_data_date_of_birth',
                visible: ('<?=$this->session->userdata('user');?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') ? true : false,
                render: function(data, type, row) {
                    return row.dob;
                }
            },
            {
                data: 'personal_data_id_card_number',
                visible: false
            },
            {
                data: 'personal_data_gender',
                visible: false,
                render: function(data, type, row) {
                    if (data == 'M') {
                        return 'Male'
                    }else{
                        return 'Female'
                    }
                }
            },
            {
                data: 'personal_data_cellular',
                visible: false
            },
            { 
                data: 'status_student',
                render: function(data, type, row) {
                    return (data === null) ? '' : data.toUpperCase();
                }
            },
            {
                data: 'graduated_year_id',
                visible: false,
            },
            {
                data: 'student_date_graduated',
                visible: false,
            },
            {
                data: 'vaccine_data',
                className: 'none',
                render: function(data, type, row) {
                    if (data) {
                        return '<a href="<?=base_url()?>personal_data/covid_certificate/' + row.student_id + '/' + row.personal_data_id + '" target="_blank">' + data.length + ' times</a>';
                    }
                    else {
                        return '0 times';
                    }
                }
            },
            {
                data: 'vaccine_data',
                visible: false,
                render: function(data, type, row) {
                    if (data) {
                        var datastring =  '';
                        var number = 1;
                        $.each(data, function(i, v) {
                            datastring += number +'. vaccine type:' + v.vaccine_type + ', vaccine date: ' + v.vaccine_date + '; <br>';
                            number++;
                        });
                        // var datastring = '{';
                        // var number = 1;
                        // $.each(data, function(i, v) {
                        //     datastring += 'vaccine type:' + v.vaccine_type + ', vaccine date: ' + v.vaccine_date + '; ';
                        //     number++;
                        // });
                        // datastring += '}';
                        return datastring;
                    }
                    else {
                        return '';
                    }
                }
            },
            {
                data: 'subject_current_thesis_internship',
                visible: false,
                render: function(data, type, row) {
                    return (data === null) ? '' : data.toUpperCase();
                }
            },
            {
                data: 'gpa',
                visible: false
            },
            {
                data: 'degree_name',
                visible: false
            },
            {
                data: 'degree_abbreviation',
                visible: false
            },
            {
                data: 'student_thesis_title',
                visible: false
            },
            {
                data: 'personal_data_nationality',
                visible: false
            },
            {
                data: 'personal_data_mother_maiden_name',
                visible: false
            },
            {
                data: 'family_members',
                visible: false,
                render: function(data, type, rows) {
                    // console.log(data);
                    if (data) {
                        var fams = data[0];
                        return fams.personal_data_name + '(' + fams.family_member_status + ')';
                    }else{
                        return '';
                    }
                }
            },
            {
                data: 'family_members',
                visible: false,
                render: function(data, type, rows) {
                    // console.log(data);
                    if (data) {
                        var fams = data[0];
                        return fams.personal_data_phone;
                    }else{
                        return '';
                    }
                }
            },
            {
                data: 'family_members',
                visible: false,
                render: function(data, type, rows) {
                    // console.log(data);
                    if (data) {
                        var fams = data[0];
                        return fams.personal_data_cellular;
                    }else{
                        return '';
                    }
                }
            },
            {
                data: 'family_members',
                visible: false,
                render: function(data, type, rows) {
                    // console.log(data);
                    if (data) {
                        var fams = data[0];
                        return fams.personal_data_email;
                    }else{
                        return '';
                    }
                }
            },
            {
                data: 'family_members',
                visible: false,
                render: function(data, type, rows) {
                    // console.log(data);
                    if (data) {
                        var fams = data[0];
                        return fams.ocupation_name;
                    }else{
                        return '';
                    }
                }
            },
            {
                data: 'personal_data_id',
                visible: false,
                render: function(data, type, row) {
                    if (row['address_data']) {
                        var a_address = row['address_data'];
                        var idx_address = 0;
                        var have_primary = false;
                        $.each(a_address, function(i, v) {
                            if (v.personal_address_type == 'primary') {
                                idx_address = i;
                                have_primary = true;
                            }
                        });

                        if (!have_primary) {
                            var o_address = a_address[0];
                        }else{
                            var o_address = a_address[idx_address];
                        }
                        var zipcode = (o_address.address_zipcode === null) ? '' : (o_address.address_zipcode + ', ');
                        var country = (o_address.country_name === null) ? '' : (o_address.country_name);
                        var city = (o_address.address_city === null) ? '' : (o_address.address_city);
                        var prov = (o_address.address_province === null) ? '' : (o_address.address_province + ' -');
                        var street = (o_address.address_street === null) ? '' : (o_address.address_street + ',');
                        var kec = (o_address.nama_wilayah === null) ? '' : (o_address.nama_wilayah + ',');
                        var kel = (o_address.address_sub_district === null) ? '' : (o_address.address_sub_district + ',');
                        var rt = (o_address.address_rt === null) ? '' : ('RT ' + o_address.address_rt);
                        var rw = (o_address.address_rt === null) ? '' : ('RW ' + o_address.address_rw + ',');

                        zipcode = ucwords(zipcode);
                        country = ucwords(country);
                        city = ucwords(city);
                        prov = ucwords(prov);
                        street = ucwords(street);
                        kec = ucwords(kec);
                        kel = ucwords(kel);
                        rt = ucwords(rt);
                        rw = ucwords(rw);

                        var f_kel = kel.substring(0, 4);
                        if (f_kel != 'Kel.') {
                            kel = 'Kel. ' + kel;
                        }
                        // kel = (f_kel == 'Kel.') ? kel : (f_kel + ' ' + kel);
                        // console.log(kel);

                        return street + ' ' + rt + ' ' + rw + ' ' + kec + ' ' + kel + ' ' + city + ' ' + zipcode + ' ' + prov + ' ' + country;
                        // console.log(o_address);
                        // return '';
                    }else{
                        return '';
                    }
                }
            },
            { data: 'personal_data_id' }
        ],
        select: {
            style:    'multi',
            selector: 'td:first-child'
        },
        columnDefs: [
            {
                targets: 1,
                render: function(data, type, row) {
                    if ('<?=$this->session->userdata("name")?>' == 'BUDI SISWANTO') {
                        if (row.pas_foto !== false) {
                            // data = data + '.';
                        }
                    }
                    return '<a href="<?=base_url()?>personal_data/profile/' + row.student_id + '/' + row.personal_data_id + '">' + data + '</a>';
                }
            },
            {
                targets: -1,
                orderable: false,
                render: function(data, type, row) {
                    var defense_class_marker = (row['student_mark_completed_defense'] == '1') ? 'success' : 'warning';
                    var proposal_class_marker = (row['student_mark_submitted_thesis_proposal'] == '1') ? 'success' : 'warning';

                    let btn_mark_defense = '<button type="button" id="btn_mark_defense" class="btn btn-' + defense_class_marker + ' btn-sm" title="Mark Completed Defense"><i class="fas fa-check"></i></button>';
                    let btn_mark_proposal = '<button type="button" id="btn_mark_proposal" class="btn btn-' + proposal_class_marker + ' btn-sm" title="Mark Thesis Proposal"><i class="fas fa-tasks"></i></button>';
                    let btn_graduation_regist = '<button type="button" id="btn_registration_graduation" class="btn btn-success btn-sm" title="Registration for Graduation"><i class="fas fa-graduation-cap"></i></button>';
					let btn_profile = '<a href="<?=site_url('personal_data/profile/')?>' + row['student_id'] + '/' + row['personal_data_id'] + '" class="btn btn-info btn-sm" title="Show Profile" target=""><i class="fas fa-id-badge"></i></a>';
					let btn_message = '<button type="button" id="btn_display_modal" class="btn btn-info btn-sm" title="Send Mail"><i class="fas fa-envelope"></i></button>';
					let btn_student_setting = '<a href="<?=site_url('academic/student_academic/student_setting/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Student settings" target=""><i class="fas fa-cogs"></i></a>';
					let btn_transfer_credit = '<a href="<?=site_url('academic/transfer_credit/transfer_student/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Transfer credit" target=""><i class="fas fa-exchange-alt"></i></a>';
					let btn_student_score = '<a href="<?=site_url('academic/score/student_score/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Student score" target="_blank"><i class="fas fa-book-open"></i></a>';
					let btn_student_subject_research_semester = '<a href="<?=site_url('academic/ijd/research_semester_subject/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Input Subject Research Semester" target="_blank"><i class="fas fa-compress-arrows-alt"></i></a>';
                    let btn_show_krs = '<button id="show_krs_approval" type="button" class="btn btn-sm btn-info" title="Show KRS"><i class="fas fa-clipboard-check"></i></button>';
                    let btn_student_record = '<a href="<?=base_url()?>student/notes/' + row['student_id'] + '" class="btn btn-sm btn-info" title="Student Notes"><i class="fas fa-quote-right"></i></a>';
                    let btn_internship_view = '<button id="btn_internship_view" type="button" class="btn btn-info btn-sm" title="View internship data"><i class="fas fa-building"></i></button>';

                    let btn_download_transcript_flying_faculty = '<button id="flying_faculty_transcript" class="btn dropdown-item" title="Download Transcript of Flying Faculty"><i class="fas fa-file-download"></i> Download Transcript of Flying Faculty</button>';
                    let btn_download_ref_letter = '<button id="show_input_ref_letter" type="button" class="btn dropdown-item" title="Download Reff Letter"><i class="fas fa-file-download"></i> Download Reff Letter</button>';
                    let btn_download_student_graduation_transcript = '<button id="btn_download_student_graduation_transcript" type="button" class="btn dropdown-item" title="Download Transcript Graduation"><i class="fas fa-file-excel"></i> Download Transcript Graduation</button>';
                    let btn_download_application_internship = '<button id="download_application_internship" type="button" class="btn dropdown-item" title="Download Application Letter (Internship)"><i class="fas fa-file-word"></i> Download Application Letter (Internship)</button>';
                    let btn_download_program_letter = '<button id="show_input_program_letter" type="button" class="btn dropdown-item" title="Download Reff Letter to Germany"><i class="fas fa-file-download"></i> Download Reff Letter to German</button>';
                    let btn_download_temporary_graduation_letter = '<button id="show_temporary_graduation_letter" type="button" class="btn dropdown-item" title="Download Temporary Graduation"><i class="fas fa-file-download"></i> Download Temporary Graduation</button>';
                    let btn_download_form_input_english_as_medium_instuction = '<button id="show_form_input_english_as_medium_instuction" type="button" class="btn dropdown-item" title="Download English as Medium Instuction"><i class="fas fa-file-download"></i> Download English as Medium Instuction</button>';
                    let btn_transcript_halfway = '<button id="btn_halfway_student" type="button" class="btn dropdown-item" title="Transcript Halfway"><i class="fas fa-list"></i> Download Transcript Halfway</button>';
                    let btn_download_ijazah = '<button id="generate_ijazah" type="button" class="btn dropdown-item" title="Generate Ijazah ND"><i class="fas fa-file-download"></i> Generate Ijazah ND</button>';
                    let btn_download_ijazah_nd2 = '<button id="generate_ijazah_2" type="button" class="btn dropdown-item" title="Generate Ijazah ND Version 2"><i class="fas fa-file-download"></i> Generate Ijazah ND Ver. Vice Rector</button>';
                    let btn_download_ijazah_ijd = '<button id="generate_ijazah_ijd" type="button" class="btn dropdown-item" title="Generate Ijazah IJD"><i class="fas fa-file-download"></i> Generate Ijazah IJD</button>';
                    let btn_download_ijazah_ijd1 = '<button id="generate_ijazah_ijd1" type="button" class="btn dropdown-item" title="Generate Ijazah IJD NGETES 1"><i class="fas fa-file-download"></i> Generate Ijazah IJD Ver 1</button>';
                    let btn_download_ijazah_ijd2 = '<button id="generate_ijazah_ijd2" type="button" class="btn dropdown-item" title="Generate Ijazah IJD NGETES 2"><i class="fas fa-file-download"></i> Generate Ijazah IJD Ver 2</button>';


                    var btn_analyze_feeder = '<div class="btn-group" role="group">';
                    btn_analyze_feeder += '<button id="btn_group_feeder" type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Feeder Analyze"><i class="fas fa-diagnoses"></i></button>';
                    btn_analyze_feeder += '<div class="dropdown-menu" aria-labelledby="btn_group_feeder">';
                    btn_analyze_feeder += '<a href="<?=base_url()?>feeder/compare_score_semester/' + row['student_id'] + '" class="btn dropdown-item" target="_blank">Compare score semester</a>';
                    btn_analyze_feeder += '<a href="<?=base_url()?>feeder/student_feeder/sync_student_biodata/' + row['student_id'] + '" class="btn dropdown-item" target="_blank">Sync to feeder</a>';
                    btn_analyze_feeder += '</div></div>';
                    
                    var html = '<div class="btn-group" aria-label="">';

                    if (row['student_status'] == 'active' || row['student_status'] == 'inactive' || row['student_status'] == 'onleave' || row['student_status'] == 'graduated') {
                        // if ('<?=$this->session->userdata("name")?>' == 'BUDI SISWANTO') {
                            // html += btn_mark_defense;
                            // html += btn_mark_proposal;
                        // }
                        // else if ('<?=$this->session->userdata("name")?>' == 'CHANDRA HENDRIANTO') {
                            html += btn_mark_defense;
                            html += btn_graduation_regist;
                        // }
                    }

                    // var btn_download = '';
                    var btn_download = '<div class="btn-group" role="group">';
                    btn_download += '<button id="btn_group_download" type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download File"><i class="fas fa-download"></i></button>';
                    btn_download += '<div class="dropdown-menu" aria-labelledby="btn_group_download">';
                    btn_download += btn_download_application_internship;
                    if (row['student_status'] == 'active') {
                        // 
                    }
                    if ((row['student_status'] == 'active') || (row['student_status'] == 'graduated')) {
                        btn_download += btn_download_ref_letter;
                        btn_download += btn_download_program_letter;
                        
                        btn_download += btn_download_form_input_english_as_medium_instuction;
                    }
                    // if (('<?=$this->session->userdata("name")?>' == 'BUDI SISWANTO') || ('<?=$this->session->userdata("name")?>' == 'CHANDRA HENDRIANTO')) {
                        html += btn_student_setting;
                        // if (row['student_status'] == 'graduated') {
                            btn_download += btn_download_ijazah;
                            // btn_download += btn_download_ijazah_nd2;
                            btn_download += btn_download_ijazah_ijd;
                            // btn_download += btn_download_ijazah_ijd1;
                            // btn_download += btn_download_ijazah_ijd2;
                        // }

                        if ((row['student_status'] == 'active') || (row['student_status'] == 'graduated')) {
                            btn_download += btn_download_student_graduation_transcript;
                        }
                        
                        btn_download += btn_download_temporary_graduation_letter;
                    // }

                    btn_download += btn_download_transcript_flying_faculty;
                    btn_download += btn_transcript_halfway;
                    btn_download += '</div></div>';

                    // html += btn_profile + btn_message;
                    html += btn_student_record;
                    // if (row['internship_data']) {
                    if (row['subject_current_thesis_internship'] != '') {
                        html += btn_internship_view;
                    }
                    // if (Array_in_Array([1,3,9], user_roles)) {
                    //     html += btn_student_setting;
                    // }
                    // html += btn_transfer_credit;
                    // if (row['student_status'] == 'active') {
                        // if (Array_in_Array([1,2,3,4,9], user_roles)) {
                        //     html += btn_transfer_credit;
                        // }
                        // if (Array_in_Array([1,2,3,4,7,8,9], user_roles)) {
                        //     html += btn_show_krs;
                        // }
                        if (row['has_take_research_semester']) {
                            html += btn_student_subject_research_semester;
                        }

                        html += btn_transfer_credit;
                        html += btn_show_krs;
                    // }
                    html += btn_student_score;
                    html += btn_download;
                    if ('<?=$this->session->userdata("name")?>' == 'BUDI SISWANTO') {
                        html += btn_analyze_feeder;
                    }
                    html += '</div>';
                    
                    return html;
                }
            }
        ],
        drawCallback: function(settings) {
            $('button#filter_student').removeAttr('disabled');
        }
    });

    $('select#transcript_degree').on('change', function(e) {
        e.preventDefault();

        if ($('select#transcript_degree').val() != 'IJD') {
            $('div#input_ijd_date').hide(200);
        }else{
            $('div#input_ijd_date').show(200);
        }
    });

    $('button#download_ref_letter').on('click', function(e) {
        e.preventDefault();

        $.blockUI({baseZ: 9999});
        var data = $('#input_ref_letter').serialize();
        $.post('<?=base_url()?>download/doc_download/generate_ref_letter', data, function(result) {
        // $.post('<?=base_url()?>download/pdf_download/generate_template_of_ref_letter', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#modal_numbering_letter').modal('hide');
                document.location.href = "<?=base_url()?>download/pdf_download/download_academic_file/" + result.file_name;
                // console.log(result);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error retrieve data!', 'Error');
        });
        // document.location.href = "<?=base_url()?>download/pdf_download/generate_template_of_ref_letter/" + '' + '/' + date_letter + '/' + number_letter;
    });

    $('button#btn_generate_graduated_transcript').on('click', function(e) {
        e.preventDefault();
        
        // var student_filter = $('form#student_filter_form').serialize();
        // var transcript_filter = $('form#form_generate_graduated_transcript').serialize();

        // download_transcript_graduated($s_student_id, $s_degree, $s_graduation_date = false, $s_rector_date = false, $s_ijd_date = false)
        if ($('#download_student_id').val() != '') {
            let graduation_date = ($('#graduation_date').val() == '') ? 'false' : $('#graduation_date').val();
            let transcript_date = ($('#transcript_date').val() == '') ? 'false' : $('#transcript_date').val();
            let ijd_date = ($('#ijd_date').val() == '') ? 'false' : $('#ijd_date').val();

            url = '<?=base_url()?>download/excel_download/download_transcript_graduated/' + $('#download_student_id').val() + '/' + $('#transcript_degree').val() + '/' + graduation_date + '/' + transcript_date + '/' + ijd_date;
            window.location.href = url;
        }else{
            $.blockUI();

            var data = $('form#student_filter_form, form#form_generate_graduated_transcript').serialize();
            $.post('<?=base_url()?>academic/student_academic/generate_graduated_transcript', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Transcript file has been sent to your email.', 'Success');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            },'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error retrieve data transcript!', 'Error!');
            });
        }

        $('#modal_graduated_transcript').modal('hide');
        
        // console.log(transcript_filter);
    });

    $('button#btn_generate_halfway').on('click', function(e) {
        e.preventDefault();
        $('#transcript_send_email').val('');
        var data = $('#form_filter_halfway').serialize();
        generate_halfway_transcript(data)
    });

    $('button#btn_send_generate_halfway').on('click', function(e) {
            e.preventDefault();
            $('#mail_student_id').val('');
            $('#transcript_send_email').val('true');
            $('#mail_student').val('<?= $message_to ?>');
            // $('#mail_student').val('budi.siswanto1450@gmail.com; employee@company.ac.id');
            $('#mail_subject').val('Cumulative Transcript of Academic');
            var student_id_transcript = $('#halfway_student_id').val();
            $.post('<?=base_url()?>student/get_transcript_text', {student_id: student_id_transcript, mode: 'halfway_text'}, function(result) {
                CKEDITOR.instances.mail_message.setData(result.transcript);
            }, 'json').fail(function(params) {
                // 
            });
            $('#modal_email_halfway_transcript').modal('show');
        });

    $('table#student_list_academic tbody').on('click', 'button#btn_internship_view', function(e) {
        e.preventDefault();

        var tabledata = table_student_list.row($(this).parents('tr')).data();
        var student_name = tabledata.personal_data_name + '(' + tabledata.study_program_abbreviation + '/' + tabledata.academic_year_id + ')';
        let internship_data = tabledata.internship_data;
        // let student_semester_id = 1;
        
        $('#internship_data_student_name').html(student_name);
        if (internship_data != null) {
            $('#internship_data_company_name').html(internship_data.institution_name);
            $('#internship_data_department').html(internship_data.department);
            $('#internship_data_supervisor').html(internship_data.supervisor_name);
        }
        else {
            $('#internship_data_company_name').html('');
            $('#internship_data_department').html('');
            $('#internship_data_supervisor').html('');
        }
        
        $('div#internship_student_modal').modal('show');
    });
    
    $('table#student_list_academic tbody').on('click', 'button#btn_halfway_student', function(e) {
        e.preventDefault();

        var tabledata = table_student_list.row($(this).parents('tr')).data();
        var student_name = tabledata.personal_data_name + '(' + tabledata.study_program_abbreviation + '/' + tabledata.academic_year_id + ')';
        // let student_academic_year = tabledata.academic_year_id;
        // let student_semester_id = 1;
        
        $('#transcript_send_email').val('');
        $('#student_halfway').html(student_name);
        $('select#halfway_academic_year_start').val(tabledata.academic_year_id);
        $('#halfway_student_id').val(tabledata.student_id);
        $('div#filter_halfway_transcript').modal('show');
    });

    $('table#student_list_academic tbody').on('click', 'button#btn_registration_graduation', function(e) {
        e.preventDefault();

        var tabledata = table_student_list.row($(this).parents('tr')).data();
        var student_name = tabledata.personal_data_name + '(' + tabledata.study_program_abbreviation + '/' + tabledata.academic_year_id + ')';
        // let student_academic_year = tabledata.academic_year_id;
        // let student_semester_id = 1;
        var inputValue = (tabledata.student_graduation_registration !== null) ? tabledata.student_graduation_registration : '<?=date('Y');?>';
        Swal.fire({
            title: "Enter year",
            input: "number",
            inputLabel: "Graduation Year",
            inputValue,
            showCancelButton: true,
            // inputValidator: (value) => {
            //     if (!value) {
            //         return "You need to write something!";
            //     }
            // },
            showLoaderOnConfirm: true,
            preConfirm: async (yearValue) => {
                $.post('<?=base_url()?>academic/student_academic/registration_graduation_year', {graduate_year: yearValue, student_id: tabledata.student_id}, function(result) {
                    if (result.code == 0) {
                        toastr.success('Success!');
                        return result;
                    }
                    else {
                        toastr.warning(result.message, 'Warning');
                        // return Swal.showValidationMessage(result.message);
                    }
                }, 'json').fail(function(params) {
                    toastr.error('Error proccessing your request', 'Error');
                    // return Swal.showValidationMessage('Error proccessing your request!');
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            // if (result.isConfirmed) {
            //     console.log('confirm');
            // }
            // console.log('finish');
        });
    });

    $('table#student_list_academic tbody').on('click', 'button#btn_mark_proposal', function(e) {
        e.preventDefault();

        var tabledata = table_student_list.row($(this).parents('tr')).data();
        var marked = false;

        if (tabledata.student_mark_submitted_thesis_proposal == 1) {
            if (confirm('Are you sure to unmarked student submitted thesis proposal ?')) {
                marked = true;
            }
        }else{
            marked = true;
        }

        if (marked) {
            $.blockUI();
            $.post('<?=base_url()?>academic/student_academic/mark_submitted_thesis_proposal', {student_id: tabledata.student_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    // table_student_list.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing system!', 'Error!');
            });
        }
    })

    $('table#student_list_academic tbody').on('click', 'button[id="generate_ijazah"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();
        generate_ijazah('single', student_data.student_id, 'nd');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="generate_ijazah_2"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();
        generate_ijazah('single', student_data.student_id, 'nd');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="generate_ijazah_ijd"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();
        generate_ijazah('single', student_data.student_id, 'ijd');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="generate_ijazah_ijd1"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();
        generate_ijazah('single', student_data.student_id, 'ijd');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="generate_ijazah_ijd2"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();
        generate_ijazah('single', student_data.student_id, 'ijd');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="btn_mark_defense"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();

        var marker = false;
        if (student_data.student_mark_completed_defense == 1) {
            if (confirm('Are you sure to unmarked student completed defense?')) {
                marker = true;
            }
        }else{
            marker = true;
        }
        
        if (marker) {
            $.blockUI();
            $.post('<?=base_url()?>academic/student_academic/mark_completed_defense', {student_id: student_data.student_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    table_student_list.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing system!', 'Error!');
            });
        }
    });

    $('table#student_list_academic tbody').on('click', 'button[id="flying_faculty_transcript"]', function(e) {
        e.preventDefault();

        var table_data = table_student_list.row($(this).parents('tr')).data();
        $.blockUI();
        $.post('<?=base_url()?>academic/student_academic/transcript_flying_faculty', {student_id: table_data.student_id}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                window.location.href = "<?=base_url()?>download/download_transcript_flying_faculty/" + result.batch + '/' + result.filename;
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error retrieve transcript data!', 'Error!');
        });
    });
    
    $('table#student_list_academic tbody').on('click', 'button[id="download_application_internship"]', function(e) {
        e.preventDefault();

        var student_data = table_student_list.row($(this).parents('tr')).data();
        // Appliation Letter for Internship
        $('input#student_id_internship').val(student_data.student_id);
        $('#appliation_letter_for_internship_modal').modal('show');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="show_temporary_graduation_letter"]', function(e) {
        e.preventDefault();
        var student_data = table_student_list.row($(this).parents('tr')).data();
        $('#student_temporary_graduation').val(student_data.student_id);
        $('#temporary_graduation_fac').text(student_data.faculty_abbreviation);
        $('#temporary_graduation_fac_input').val(student_data.faculty_abbreviation);
        
        $('#temporary_graduation_letter_modal').modal('show');
    });
    
    $('table#student_list_academic tbody').on('click', 'button[id="show_form_input_english_as_medium_instuction"]', function(e) {
        e.preventDefault();
        var student_data = table_student_list.row($(this).parents('tr')).data();
        $('#student_english_medium_letter').val(student_data.student_id);
        $('#english_medium_fac').text(student_data.faculty_abbreviation);
        $('#english_medium_fac_input').val(student_data.faculty_abbreviation);
        
        $('#form_input_english_as_medium_instuction_modal').modal('show');
    });
    
    $('table#student_list_academic tbody').on('click', 'button[id="show_input_program_letter"]', function(e) {
        e.preventDefault();
        var student_data = table_student_list.row($(this).parents('tr')).data();
        $('#student_letter_program').val(student_data.student_id);
        $('#fac_abbreviation').text(student_data.faculty_abbreviation);
        $('#letter_number_fac').val(student_data.faculty_abbreviation);
        // console.log(student_data);
        $('#ref_letter_to_germany_modal').modal('show');
    });
    
    $('table#student_list_academic tbody').on('click', 'button[id="show_input_ref_letter"]', function(e) {
        e.preventDefault();
        var student_data = table_student_list.row($(this).parents('tr')).data();
        $('#student_id_letter').val(student_data.student_id);
        $('#modal_numbering_letter').modal('show');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="btn_display_modal"]', function(e) {
        e.preventDefault();

        var table_data = table_student_list.row($(this).parents('tr')).data();
        $('#mail_student_id').val(table_data.student_id);
        $('#mail_student').val(table_data.student_email);
        $('div#modal_send_email').modal('show');
    });

    $('button#btn_show_graduated_transcript').on('click', function(e) {
        e.preventDefault();

        $('#download_student_id').val('');
        $('#modal_graduated_transcript').modal('show');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="btn_download_student_graduation_transcript"]', function(e) {
        e.preventDefault();

        var table_data = table_student_list.row($(this).parents('tr')).data();

        $('#download_student_id').val(table_data.student_id);
        $('#modal_graduated_transcript').modal('show');
    });

    $('table#student_list_academic tbody').on('click', 'button[id="show_krs_approval"]', function(e) {
        e.preventDefault();

        var table_data = table_student_list.row($(this).parents('tr')).data();
        $('input#krs_personal_data_id').val(table_data.personal_data_id);
        $('div#modal_filter_krs').modal('show');
    });

    $('button#submit_show_krs').on('click', function(e) {
        e.preventDefault();
        if (($('select#krs_academic_year_id').val() != '') && ($('select#krs_semester_type_id').val() != '')) {
            if ($('input#krs_personal_data_id').val() != '') {
                $('div#modal_filter_krs').modal('hide');
                let url = "<?=base_url()?>krs/krs_approval/" + $('select#krs_academic_year_id').val() + "/" + $('select#krs_semester_type_id').val() + "/" + $('input#krs_personal_data_id').val();
                window.open(url, '_blank');
            }else{
                toastr.error('Error retrieve student data!', 'Error!');
            }
        }else{
            toastr.warning('Please select filter field!', 'Warning!');
        }
    });

    $('button#btn_blast_mail').on('click', function(e) {
        e.preventDefault();

        let text = [
            'Batch: ' + $('#academic_year_id option:selected').text(),
            'Study Program: ' + (($('select#filter_study_program_id option:selected').data('abbr') != undefined) ? $('select#filter_study_program_id option:selected').data('abbr') : 'All'),
            'Status: ' + (($('#filter_student_status option:selected').text() != undefined) ? $('#filter_student_status option:selected').text() : 'All')
        ].join('; ');
        
        $('#mail_student').val(text);
        $('#mail_student_id').val('blast');
        $('div#modal_send_email').modal('show');
    });

    $('button#btn_download_student').on('click', function(e) {
        e.preventDefault();
        let a_filter_data = $('form#student_filter_form').serialize();
        let url = '<?= base_url()?>academic/student_academic/download_student_filtered';
        $.blockUI();

        $.post(url, a_filter_data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                var data = result.data;
                window.location.href = '<?= base_url()?>file_manager/download_template/' + data.file + '/' + data.semester_active;
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();  
        });
    });

    $('button#filter_student').on('click', function(e) {
        e.preventDefault();

        $('button#filter_student').attr('disabled', 'disabled');
        let columngraduateyear = table_student_list.column(16);
        let columngraduatedate = table_student_list.column(17);
        
        var graduatefilter = false;
        let liststatus = $('#filter_student_status2').val();
        $.each(liststatus, function(i, v) {
            // console.log(v);
            if (v == 'graduated') {
                graduatefilter = true;
            }
        });

        // console.log(graduatefilter);
        if (graduatefilter) {
            columngraduateyear.visible(true);
            columngraduatedate.visible(true);
        }
        else {
            columngraduateyear.visible(false);
            columngraduatedate.visible(false);
        }

        table_student_list.ajax.reload();
    });

    // <div class="modal-body">
    //         <span id="spinner_loader"></span><strong id="stage_sync"></strong>
    //         <div class="progress">
    //             <div class="progress-bar" role="progressbar" style="width: 1%;" aria-valuemax="100" id="value_progress"></div>
    //         </div>
    //             <div id="sync_result"></div>
    //         </div>
    //         <div class="modal-footer">
    //             <button type="button" id="btn_finish_sync" class="btn btn-primary d-none" data-dismiss="modal">Close</button>
    //         </div>

    var start_string = 0;
    $('button#btn_feeder_sync').on('click', function(e) {
        e.preventDefault();
        // $.blockUI();
        $('#modal_dikti_sync').modal('show');
        let a_form_data = $('form#student_filter_form').serialize();
        let uri = '<?=base_url()?>feeder/student/student_sync/false/false/true';

        $.ajax({
            xhr: function()
            {
                start_string = 0;
                $('.progress-bar').css("width", "1%");
                $('#value_progress').html('');
                $('#sync_result').html('');
                var xhr = new window.XMLHttpRequest();
                $('#spinner_loader').html('<i class="fas fa-spinner fa-pulse"></i>');
                $('button#btn_finish_sync').addClass('d-none');
                xhr.addEventListener("progress", function(evt){
                    var response_text = evt.target.response;
                    var a_current_response = JSON.parse(parsing_string(response_text));
                    var message = a_current_response.message;
                    var stage = a_current_response.stage;
                    var total_data = a_current_response.total_data;
                    var current_process = a_current_response.current_process;
                    var percentage = (parseInt(current_process) / parseInt(total_data)) * 100;
                    percentage = parseInt(percentage) + "%";
                    // console.log(message);

                    $('.progress-bar').css("width", percentage);
                    $('#value_progress').html(percentage);
                    $('#stage_sync').html(stage);
                    $('#sync_result').append(message);
                }, false);
                console.log(start_string);
                return xhr;
            },
            type: 'POST',
            url: uri,
            // url: "<?=base_url()?>feeder/student/testing",
            data: a_form_data,
            success: function(data){
                $('.progress-bar').css("width", "100%");
                $('#value_progress').html("100%");
                $('button#btn_finish_sync').removeClass('d-none');
                $('#spinner_loader').html('');
                toastr.success('Sinkronisasi selesai!');
            }
        });
        
        // $.post(url, a_form_data, function(result) {
        //     $.unblockUI();
        //     // console.log(result);
        //     if (result.code == 0) {
        //         toastr.success('Success Syncronize to Feeder', 'Success!');
        //     }else{
        //         toastr.warning(result.message + '<br>message: "' + result.feeder_message + '"', 'Warning!');
        //     }
        // }, 'json').fail(function(params) {
        //     $.unblockUI();
        //     console.log('error');
        // });
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

    $('button#btn_generate_bulk_ijazah').on('click', function(e) {
        e.preventDefault();

        var checked = table_student_list.rows( { selected: true } );
        var count_checked = checked.count();

        if (count_checked > 0) {
            var data_checked = checked.data();
            var a_student_id = [];
            for (let i = 0; i < count_checked; i++) {
                a_student_id.push(data_checked[i].student_id);
            }

            generate_ijazah('bulk', a_student_id, 'nd');
        }else{
            toastr['warning']('No student selected!', 'Warning');
            return false;
        }
    });
    
    $('button#btn_generate_bulk_ijazah_nd2').on('click', function(e) {
        e.preventDefault();

        var checked = table_student_list.rows( { selected: true } );
        var count_checked = checked.count();

        if (count_checked > 0) {
            var data_checked = checked.data();
            var a_student_id = [];
            for (let i = 0; i < count_checked; i++) {
                a_student_id.push(data_checked[i].student_id);
            }

            generate_ijazah('bulk', a_student_id, 'nd');
        }else{
            toastr['warning']('No student selected!', 'Warning');
            return false;
        }
    });

    $('button#btn_generate_bulk_ijazah_ijd').on('click', function(e) {
        e.preventDefault();

        var checked = table_student_list.rows( { selected: true } );
        var count_checked = checked.count();

        if (count_checked > 0) {
            var data_checked = checked.data();
            var a_student_id = [];
            for (let i = 0; i < count_checked; i++) {
                a_student_id.push(data_checked[i].student_id);
            }

            generate_ijazah('bulk', a_student_id, 'ijd');
        }else{
            toastr['warning']('No student selected!', 'Warning');
            return false;
        }
    });

    $('button#btn_generate_bulk_ijazah_ijd1').on('click', function(e) {
        e.preventDefault();

        var checked = table_student_list.rows( { selected: true } );
        var count_checked = checked.count();

        if (count_checked > 0) {
            var data_checked = checked.data();
            var a_student_id = [];
            for (let i = 0; i < count_checked; i++) {
                a_student_id.push(data_checked[i].student_id);
            }

            generate_ijazah('bulk', a_student_id, 'ijd');
        }else{
            toastr['warning']('No student selected!', 'Warning');
            return false;
        }
    });

    $('button#btn_generate_bulk_ijazah_ijd2').on('click', function(e) {
        e.preventDefault();

        var checked = table_student_list.rows( { selected: true } );
        var count_checked = checked.count();

        if (count_checked > 0) {
            var data_checked = checked.data();
            var a_student_id = [];
            for (let i = 0; i < count_checked; i++) {
                a_student_id.push(data_checked[i].student_id);
            }

            generate_ijazah('bulk', a_student_id, 'ijd');
        }else{
            toastr['warning']('No student selected!', 'Warning');
            return false;
        }
    });

    function generate_ijazah(generate_mode, student_id, graduation_type = 'nd') {
        $.blockUI();
        var data = {
            mode: generate_mode,
            student_id: student_id,
            program: graduation_type
        };

        var uri = '<?=base_url()?>academic/student_academic/generate_ijazah';
        $.post(uri, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success, files has been sent to your email!');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
    }

    function parsing_string(string) {
        var new_string = string.substring(start_string, string.length); 
        start_string = string.length;
        return new_string;
    }

    $( "#country_name" ).autocomplete( "option", "appendTo", "#new_institution_form" );

    $(document).keydown(function(event) {
        // console.log(event);
        var charinput = String.fromCharCode(event.which);
        // console.log(charinput);
        if ((event.ctrlKey) && (event.altKey) && (charinput == 'F')){
            $(".dataTables_filter input").focus();
            $(".dataTables_filter input").val('');
        }
        return true;
        event.preventDefault();
    });
});
</script>