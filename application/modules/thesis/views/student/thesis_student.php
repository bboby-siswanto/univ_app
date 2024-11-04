<div id="thesis_student" class="">
    <div class="card">
        <div class="card-header">
            <strong><?=$o_student_data->personal_data_name;?> (<?=$o_student_data->study_program_abbreviation;?>/<?=$o_student_data->academic_year_id;?>)</strong>
    <?php
    if ($thesis_data) {
        if ((isset($allow_update)) AND ($allow_update)) {
    ?>
            <div class="card-header-actions">
                <a href="<?=base_url()?>thesis/student_thesis/<?=(isset($thesis_page_type)) ? $thesis_page_type : '' ?>/customize" class="card-header-action btn btn-link">
                    <i class="fa fa-edit"></i><?= (isset($thesis_page_type)) ? ' '.ucwords(strtolower(str_replace('_', ' ', $thesis_page_type))) : '' ?> Registration
                </a>
            </div>
    <?php
        }
    }
    ?>
        </div>
        <div class="card-body">
            <div class="row">
                <table class="table table-bordered">
                    <tr>
                        <th class="w-25">Thesis Title</th>
                        <td>
                            <?= ($thesis_data) ? $thesis_data[0]->thesis_title : '';?>
                        </td>
                    </tr>
                    <tr>
                        <th>Advisor</th>
                        <td>
                        <?php
                        if (($thesis_data) AND (isset($thesis_page_type)) AND ($thesis_page_type == 'proposal_submission')) {
                        ?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Proposed:</strong>
                            <?php
                            if ($advisor_proposed_1) {
                                print($advisor_proposed_1->advisor_name);
                            }
                            if ($advisor_proposed_2) {
                                print(' / '.$advisor_proposed_2->advisor_name);
                            }
                            ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Approved:</strong>
                            <?php
                            if ($advisor_approved_1) {
                                print($advisor_approved_1->advisor_name);
                            }
                            if ($advisor_approved_2) {
                                print(' / '.$advisor_approved_2->advisor_name);
                            }
                            ?>
                                </div>
                            </div>
                        <?php
                        }
                        else if ($thesis_data) {
                            if ($advisor_approved_1) {
                                print($advisor_approved_1->advisor_name);
                            }
                            if ($advisor_approved_2) {
                                print(' & '.$advisor_approved_2->advisor_name);
                            }
                        }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Current Progress</th>
                        <td>
                            <?= ($thesis_data) ? strtoupper($thesis_data[0]->current_progress) : '';?>
                        </td>
                    </tr>
                    <tr>
                        <th>Current Status</th>
                        <td>
                            <?= ($thesis_data) ? strtoupper($thesis_data[0]->current_status) : '';?>
                        </td>
                    </tr>
            <?php
            if ((isset($defense_data)) AND (is_array($defense_data)) AND (count($defense_data) > 0)) {
                $o_defense = $defense_data[0];
            ?>
                    <tr>
                        <th>Defense Schedule</th>
                        <td>
                            <?=$o_defense->thesis_defense_room.' - '.date('d F Y', strtotime($o_defense->thesis_defense_date));?> 
                            <?=date('H:i', strtotime($o_defense->thesis_defense_time_start)).'-'.date('H:i', strtotime($o_defense->thesis_defense_time_end));?>
                        </td>
                    </tr>
            <?php
            }
            ?>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Submission Log
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table id="thesis_log_student" class="table table-bordered">
                        <thead class="bg-dark">
                            <tr>
                                <th>Submission Type</th>
                                <th>Submission Status</th>
                                <th>Date Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_note_list">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remarks / Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="remarks_log_id" id="remarks_log_id">
                <table id="table_note_list" class="table">
                    <thead class="bg-dark">
                        <tr>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var table_note_list = $('#table_note_list').DataTable({
        paging: false,
        info: false,
        searching: false,
        ordering: false,
        ajax: {
            url: '<?= base_url()?>thesis/get_list_remarks',
            type: 'POST',
            data: function(d) {
                d.thesis_log_id = $('#remarks_log_id').val();
            }
        },
        columns: [
            {data: 'remarks'}
        ],
        language: {
            "emptyTable": "No data available"
        }
    });

    var log_table = $('#thesis_log_student').DataTable({
        ordering: false,
        ajax: {
            url: '<?= base_url()?>thesis/thesis_logs',
            type: 'POST',
            data: {
                thesis_student_id: "<?= ($thesis_data) ? strtoupper($thesis_data[0]->thesis_student_id) : 'x';?>"
            }
        },
        columns: [
            {
                data: 'thesis_log_type'
            },
            {data: 'thesis_status'},
            {data: 'date_added'},
            {
                data: 'thesis_student_id',
                render: function(data, type, row) {
                    var log_file = row.log_file;
                    html = '<div class="btn-group btn-group-sm" role="group">';
                    html += '<button id="view_note" class="btn btn-sm btn-success">view note</button>';

                    if (log_file) {
                        html += '<div class="btn-group" role="group">';
                        html += '<button id="btnGroupDrop1" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">View File</button>';
                        html += '<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">';
                        $.each(log_file, function(i, v) {
                            html += '<a target="_blank" class="dropdown-item" href="<?=base_url()?>thesis/view_file/' + v.target + '/' + v.thesis_student_id + '/' + v.thesis_filename + '">' + v.thesis_filetype.replace(/_/g, ' '); + '</a>';
                        });

                        html += '</div></div>';
                        html += '</div>';
                    }
                    
                    // if (row.thesis_proposal_fname != null) {
                        
                    // }
                    // if (row.thesis_work_fname != null) {
                    //     html += '<a target="_blank" class="dropdown-item" href="<?=base_url()?>thesis/view_file/thesis_' + row.thesis_log_type + '/' + row.thesis_student_id + '/' + row.thesis_work_fname + '">Thesis Work</a>';
                    // }
                    // if (row.thesis_plagiate_check_fname != null) {
                    //     html += '<a target="_blank" class="dropdown-item" href="<?=base_url()?>thesis/view_file/thesis_' + row.thesis_log_type + '/' + row.thesis_student_id + '/' + row.thesis_plagiate_check_fname + '">Plagiarism Check</a>';
                    // }
                    // if (row.thesis_log_fname != null) {
                    //     html += '<a target="_blank" class="dropdown-item" href="<?=base_url()?>thesis/view_file/thesis_' + row.thesis_log_type + '/' + row.thesis_student_id + '/' + row.thesis_log_fname + '">Thesis Log</a>';
                    // }
                    // if (row.thesis_other_doc_fname != null) {
                    //     html += '<a target="_blank" class="dropdown-item" href="<?=base_url()?>thesis/view_file/thesis_' + row.thesis_log_type + '/' + row.thesis_student_id + '/' + row.thesis_other_doc_fname + '">Other Required Docs</a>';
                    // }
                    // if (row.thesis_final_fname != null) {
                    //     html += '<a target="_blank" class="dropdown-item" href="<?=base_url()?>thesis/view_file/thesis_' + row.thesis_log_type + '/' + row.thesis_student_id + '/' + row.thesis_final_fname + '">Thesis Final</a>';
                    // }
                    
                    return html;
                }
            }
        ]
    });

    $('#new_registration').on('click', function(e) {
        e.preventDefault();

        $('#thesis_student').addClass('d-none');
        $('#submission_page').removeClass('d-none');
    });

    $('table#thesis_log_student tbody').on('click', 'button[id="view_note"]', function(e) {
        e.preventDefault();

        var data_list = log_table.row($(this).parents('tr')).data();
        
        $('#remarks_log_id').val(data_list.thesis_log_id);
        table_note_list.ajax.reload();
        $('div#modal_note_list').modal('show');
    });
});
</script>
<div id="submission_page" class="d-none">
<?=(isset($submission_page)) ? $submission_page : '';?>
</div>