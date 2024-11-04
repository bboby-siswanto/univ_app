<div class="card">
    <div class="card-header">
        Class Group List
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="settings_dropdown">
<?php
if (in_array(strtolower($this->session->userdata('user')), ['47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'])) {
?>
                <button id="send_all_score_template" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Send All Score Template">
                    <i class="fas fa-paper-plane"></i> Send All Score Template
                </button>
                <button id="download_class_lists" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download class group lists">
                    <i class="fas fa-layer-group"></i> Download Class Lists
                </button>
<?php
}
?>
                <button id="download_class_absence" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Student Class">
                    <i class="fas fa-clipboard-list"></i> Download All Class Absence
                </button>
                <button id="download_registration_repetition" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Registration Repetition">
                    <i class="fas fa-clipboard-list"></i> Download Registration Repetition
                </button>
                <button id="download_ks_registration" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download KRS Registration">
                    <i class="fas fa-clipboard-list"></i> Download KRS Registration Approved
                </button>
                <button id="download_mimosa_format" type="button" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Mimosa Format Import">
                    <i class="fas fa-clipboard-list"></i> Download for Mimosa (import method)
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive view-table-class">
            <?= modules::run('academic/class_group/view_class_group_lists_class');?>
        </div>
        <?php
        if ($valid_approval) {
        // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        ?>
            <button class="btn btn-info btn-block" id="btn_join_class_group">
                <i class="fa fa-object-group"></i> Merging / Commit Class Group
            </button>
        <?php
        }
        ?>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_group_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new class group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_class_group"></div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_template_teks_score">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Text template to send</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_class_group"></div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('button#download_class_lists').on('click', function(e) {
        e.preventDefault();
        
        $.post('<?=base_url()?>academic/class_group/download_class_lists', $('form#form_filter_class_group').serialize(), function(result) {
            if (result.code == 0) {
                window.location.href = '<?= base_url()?>file_manager/download_template/' + result.file + '/' + result.semester;
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error retrieve data class', 'Warning');
        });
    });

    $('button#download_class_absence').on('click', function(e) {
        e.preventDefault();

        let s_academic_year = $('#academic_year_id').val();
        let s_semester_type_id = $('#semester_type_id_search').val();

        if ((s_academic_year == '') || (s_semester_type_id == '')) {
            toastr['warning']('Please select filter field!', 'Warning');
        }else{
            $.blockUI();

            var data = $('form#form_filter_class_group').serialize();
            $.post('<?=base_url()?>download/excel_download/generate_all_class_student', data, function(result) {
                $.unblockUI();
                // console.log(result);
                if (result.code == 0) {
                    window.location.href = '<?=base_url()?>download/excel_download/download_file/' + result.filename +'/student_all_class/' + s_academic_year + s_semester_type_id;
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error retrieving data!', 'Error!');
            });
        }
    });

    $('#download_mimosa_format').on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Info',
            html: 'Tidak termasuk Subjects:<br><i><b>Research Semester, Project Research, Research Project, Internship, Thesis, NFU</b></i>!',
            // showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Continue',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            console.log(result);
            if (result.value) {
                console.log('continuu');
                window.location.href = "<?=base_url()?>devs/get_for_mimosa/" + $('#academic_year_id').val() + "/" + $('#semester_type_id_search').val();
            }
        })
    });

    $('button#send_all_score_template').on('click', function(e) {
        e.preventDefault();

        if (!lect_list) {
            toastr.warning('Lecturer not available!', 'Warning');
        }else if (class_group_lists_table.rows().count() > 0) {
            // toastr.warning('On progress development!', 'Warning');
            var data = class_group_lists_table.rows().data();
            var a_class_master_id = [];
            $.each(data, function(i, v) {
                a_class_master_id.push(v.class_master_id);
            });

            if (a_class_master_id.length > 0) {
                if (confirm('Are you sure send all score template ?')) {
                    $.blockUI();
                    $.post('<?=base_url()?>academic/class_group/send_all_score_template', {class_master_list: a_class_master_id}, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            toastr.success('Success send all score template!', 'Success');
                        }else{
                            toastr.warning(result.message, 'Warning');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                        toastr.error('Error processing data!', 'Error');
                    });
                }
            }else{
                toastr.warning('Error retrieve class data on this table!', 'Warning');
            }
            // console.log(a_class_master_id);
        }else{
            toastr.warning('No data available!', 'Warning');
        }
    });

    $('button#download_ks_registration').on('click', function(e) {
        e.preventDefault();

        if (($('#academic_year_id').val() == '') || ($('#semester_type_id_search').val() == '')) {
            toastr.warning('Please select filter data!', 'Warning');
        }else{
            
            $.blockUI();
            var data = $('#form_filter_class_group').serialize();
            $.post('<?=base_url()?>download/excel_download/generate_krs_registration', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    var url = '<?=base_url()?>file_manager/academic_download/' + result.file + '/krs_registration/' + result.semester;
                    window.location.href = url;
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error generating data!', 'Erorr');
            });
        }
    });

    $('button#download_registration_repetition').on('click', function(e) {
        e.preventDefault();
        
        if (($('#academic_year_id').val() == '') || ($('#semester_type_id_search').val() == '')) {
            toastr.warning('Please select filter data!', 'Warning');
        }else{
            
            $.blockUI();
            var data = $('#form_filter_class_group').serialize();
            $.post('<?=base_url()?>download/excel_download/generated_repat_registration', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    var url = '<?=base_url()?>file_manager/academic_download/' + result.file + '/krs_registration/' + result.semester;
                    window.location.href = url;
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error generating data!', 'Erorr');
            });
        }
    });
});
</script>