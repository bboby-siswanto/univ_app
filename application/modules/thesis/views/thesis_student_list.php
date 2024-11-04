<style>
    .dropdown-item.disabled, .dropdown-item:disabled {
        color: #73818f !important;
        pointer-events: none;
        background-color: transparent;
    }
</style>
<div class="card">
    <div class="card-header">
        Student Filter
    </div>
    <div class="card-body">
        <form id="form_filter_student_thesis" onsubmit="return false">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="thesis_filter_prodi">Study Program</label>
                        <select name="thesis_filter_prodi" id="thesis_filter_prodi" class="form-control">
                            <option value=""></option>
                <?php
                if ($study_program_list) {
                    foreach ($study_program_list as $o_study_program) {
                ?>
                            <option value="<?=$o_study_program->study_program_id;?>"><?=$o_study_program->study_program_name;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="thesis_filter_batch">Batch</label>
                        <select name="thesis_filter_batch" id="thesis_filter_batch" class="form-control">
                            <option value=""></option>
                <?php
                if ($academic_year_list) {
                    foreach ($academic_year_list as $o_year) {
                ?>
                            <option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?>/<?=intval($o_year->academic_year_id) + 1;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-info float-right" id="btn_thesis_filter">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Student List
    </div>
    <div class="card-body">
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
            <!-- <div class="btn-group btn-group-sm mr-2" role="group" aria-label="First group">
                <button type="button" class="btn btn-sm btn-success">1</button>
                <button type="button" class="btn btn-secondary">2</button>
                <button type="button" class="btn btn-secondary">3</button>
                <button type="button" class="btn btn-secondary">4</button>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Second group">
                <button type="button" class="btn btn-secondary">5</button>
                <button type="button" class="btn btn-secondary">6</button>
                <button type="button" class="btn btn-secondary">7</button>
            </div> -->
            <div class="btn-group" role="group">
                <button id="btn_group_view_doc" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    View
                </button>
                <div class="dropdown-menu" aria-labelledby="btn_group_view_doc">
            <?php
            if ((isset($list_filetype)) AND (is_array($list_filetype))) {
                foreach ($list_filetype as $s_filetype) {
            ?>
                    <a class="dropdown-item btn_view disabled" target="_blank" id="btn_dl_<?=$s_filetype;?>" href="#"><?=ucwords(strtolower(str_replace('_', ' ', $s_filetype)));?></a>
            <?php
                }
            }
            ?>
                </div>
                <button type="button" class="btn btn-info" id="detail_thesis_student">Detail</button>
            </div>
            <div class="btn-group ml-2" role="group" aria-label="Second group">
                <a href="" class="btn btn-info disabled" id="btn_defense_score">Defense Result</a>
            </div>
        <?php
        if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        ?>
            <div class="btn-group ml-2" role="group" aria-label="Second group">
                <a href="" class="btn btn-primary disabled" id="btn_set_approval">Set Approval System</a>
            </div>
        <?php
        }
        ?>
        </div>
        <div class="table-responsive mt-2">
            <table class="table table-bordered" id="table_list_thesis">
                <thead class="bg-dark">
                    <tr>
                        <th></th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Student Email</th>
                        <th>Prodi</th>
                        <th>Batch</th>
                        <th>Thesis Title</th>
                        <th>Advisor</th>
                        <th>Examiner</th>
                        <th>Current Progress</th>
                        <th>Academic Year Final Thesis</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_action_approval">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set ByPass Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false" id="form_bypass_approval_thesis">
                    <input type="hidden" id="bypass_thesis_student_id" name="bypass_thesis_student_id">
                    <div class="form-group">
                        <label for="bypass_status">Status Thesis Work</label>
                        <select name="bypass_status" id="bypass_status" class="form-control">
                            <option value=""></option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_bypass_thesis">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var student_thesis_list = $('table#table_list_thesis').DataTable({
    select: {
        style: 'single'
    },
    // dom: 'Bfrtip',
    // buttons: [
    //     {
    //         text: 'Download Excel',
    //         extend: 'excel',
    //         title: 'Student List Data',
    //         exportOptions: {
    //             columns: ':visible'
    //         }
    //     },
    //     {
    //         text: 'Download Pdf',
    //         extend: 'pdf',
    //         title: 'Student List Data',
    //         exportOptions: {columns: ':visible'}
    //     },
    //     {
    //         text: 'Print',
    //         extend: 'print',
    //         title: 'Student List Data',
    //         exportOptions: {columns: ':visible'}
    //     },
    //     // {
    //     //     text: 'Column Visibility',
    //     //     action: function () {
    //     //         // show columns
    //     //     }
    //     // },
    //     'colvis'
    // ],
    ajax: {
        url: '<?= base_url()?>thesis/get_list_student',
        type: 'POST',
        data: function() {
            return $('#form_filter_student_thesis').serialize();
        }
    },
    columns: [
        {
            data: 'thesis_student_id',
            orderable: false,
            className: 'select-checkbox',
            render: function(data, type, row) {
                var html = '<input type="hidden" value="' + data + '" name="thesis_student_id">';
                return html;
            }
        },
        {data: 'personal_data_name'},
        {data: 'student_number'},
        {data: 'student_email'},
        {data: 'study_program_abbreviation'},
        {data: 'student_batch'},
        {data: 'student_thesis_title'},
        {data: 'list_advisor_name'},
        {data: 'list_examiner_name'},
        {
            data: 'current_progress',
            render: function(data, type, row) {
                return '<span class="badge badge-pill badge-success">' + data + '</span>';
            }
        },
        {
            data: 'thesis_student_id',
            render: function(data, type, row) {
                html = 'N/A';
                if (row.last_final_log) {
                    var final_log_data = row.last_final_log;
                    html = final_log_data.academic_year_id;
                }

                return html;
            }
        }
    ],
});

$(function() {
    $('#thesis_filter_prodi, #thesis_filter_batch').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });

    $('#btn_thesis_filter').on('click', function(e) {
        e.preventDefault();

        student_thesis_list.ajax.reload();
    });

    $('#detail_thesis_student').on('click', function(e) {
        e.preventDefault();

        var checked = student_thesis_list.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Student List', 'Warning!');
        }
        else {
            window.location.href="<?=base_url()?>thesis/thesis_detail/" + checked.data()[0].student_id;
        }
    });

    $('#btn_set_approval').on('click', function(e) {
        e.preventDefault();

        var checked = student_thesis_list.rows({selected: true});
        var count_checked = checked.count();

        if (count_checked <= 0) {
            toastr.warning('Please select one record from the Student List', 'Warning!');
        }
        else {
            let datachecked = checked.data()[0];
            $('#bypass_thesis_student_id').val(datachecked.thesis_student_id);
            $('#bypass_status').val(datachecked.current_status);
            $('#modal_action_approval').modal('show');
        }
    });

    $('#submit_bypass_thesis').on('click', function(e) {
        e.preventDefault();

        let form = $('#form_bypass_approval_thesis');
        let data = form.serialize();
        $.post('<?=base_url()?>thesis/update_status_thesis_work', data, function(result) {
            if (result.code == 0) {
                student_thesis_list.ajax.reload(null, false);
                $('#modal_action_approval').modal('hide');
                $('#bypass_thesis_student_id').val('');
                $('#bypass_status').val('');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            toastr.error('error processing data');
        })
    })

    student_thesis_list.on('select', function(e, dt, type, indexes) {
        var row_data = student_thesis_list.row(indexes).data();
        var tooltips_default = 'Please select one record from the Thesis Student List';
        // var tolltips_approved = 'Thesis with ' + row_data.current_status + ' status can not processed with this action';
        var link_docs = '<?=base_url()?>thesis/view_file/';
        var link_defense = '<?=base_url()?>thesis/form_score/';
        // console.log(row_data);
        
        // var button_update_tw = $('button#update_tw');
        // var button_approve_tw = $('button#approve_tw');
        // var button_reject_tw = $('button#reject_tw');
        // 
        $('#btn_set_approval').removeClass('disabled');
        // $('.btn_view').addClass('disabled');

        if (row_data.defense_id !== false) {
            $('#btn_defense_score').removeClass('disabled').removeAttr('title').attr('href', link_defense + row_data.defense_id);
        }
        else {
            $('#btn_defense_score').addClass('disabled').attr('title', 'Defense data not found in system').removeAttr('href');
        }
        <?php
        if ((isset($list_filetype)) AND (is_array($list_filetype))) {
            foreach ($list_filetype as $s_filetype) {
        ?>
                if (row_data.<?=$s_filetype;?> != '') {
                    $('#btn_dl_<?=$s_filetype;?>').removeClass('disabled');
                    $('#btn_dl_<?=$s_filetype;?>').removeAttr('disabled');
                    $('#btn_dl_<?=$s_filetype;?>').attr('href', link_docs + '<?= explode('_', $s_filetype)[0].'_'.explode('_', $s_filetype)[1]; ?>/' + row_data.thesis_student_id + '/' + row_data.<?=$s_filetype;?>);
                }
                else {
                    $('#btn_dl_<?=$s_filetype;?>').addClass('disabled');
                    $('#btn_dl_<?=$s_filetype;?>').attr('disabled', 'disabled');
                    $('#btn_dl_<?=$s_filetype;?>').removeAttr('href');
                }
        <?php
            }
        }
        ?>
    }).on('deselect', function(e, dt, type, indexes) {
        $('#btn_set_approval').addClass('disabled');
        $('#btn_defense_score').addClass('disabled');
    });
})
</script>