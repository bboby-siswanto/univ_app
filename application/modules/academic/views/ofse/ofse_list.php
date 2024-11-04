<div class="card">
    <div class="card-header">
        OFSE List
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" type="button" id="btn_new_ofse">
                <i class="fas fa-plus"></i> New OFSE
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="ofse_table" class="table table-bordered">
                <thead class="bg-dark">
                    <tr>
                        <th>No</th>
                        <th>OFSE Period</th>
                        <th>Registration Period</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_new_ofse">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Ofse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('academic/ofse/form_new_ofse')?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_form">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_upload_score">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url()?>academic/ofse/upload_score" id="submit_ofse_score" onsubmit="return false">
                    <input type="hidden" id="ofse_period_id_upload" name="ofse_period_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_form">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var ofse_table = $('table#ofse_table').DataTable({
    ajax: {
        url: '<?= base_url()?>academic/ofse/ofse_list',
        type: 'POST'
    },
    columns: [
        {
            data: 'ofse_period_id',
        },
        {
            data: 'ofse_period_name',
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>academic/ofse/ofse_student_member/' + row['ofse_period_id'] + '">' + data + '</a>';
            }
        },
        {
            data: 'study_plan_ofse_start_date',
            render: function(data, type, row) {
                var date_start = new Date(row['study_plan_ofse_start_date']);
                var date_end = new Date(row['study_plan_ofse_end_date']);

                var date_start = moment(date_start).format('DD MMM YYYY');
                var date_end = moment(date_end).format('DD MMM YYYY');

                return  date_start + ' - ' + date_end;
            }
        },
        {
            data: 'ofse_period_id',
            render: function(data, type, row) {
                var btn_download_ofse_subject = '<button type="button" id="download_subject" class="btn btn-link dropdown-item"><i class="fa fa-download"></i> Download Subject and Examiner</button>';
                var btn_download_ofse_student = '<button type="button" id="download_student_ofse" class="btn btn-link dropdown-item"><i class="fa fa-download"></i> Download Student Data</button>';
                var btn_download_ofse_structure = '<button type="button" id="download_ofse_registration_structure" class="btn btn-link dropdown-item"><i class="fa fa-download"></i> Download Structure of OFSE Registration</button>';
                var btn_download_ofse_result = '<button type="button" id="download_ofse_result" class="btn btn-link dropdown-item"><i class="fa fa-download"></i> Download OFSE Result</button>';

                var btn_download = '<div class="btn-group" role="group">';
                btn_download += '<button id="btn_group_download" type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download File"><i class="fas fa-download"></i></button>';
                btn_download += '<div class="dropdown-menu" aria-labelledby="btn_group_download">';
                btn_download += btn_download_ofse_subject;
                btn_download += btn_download_ofse_student;
                btn_download += btn_download_ofse_structure;
                btn_download += btn_download_ofse_result;
                btn_download += '</div></div>';


                html = '<div class="btn-group" aria-label="">';
                html += '<a class="btn btn-info btn-sm" href="<?=base_url()?>academic/ofse/offered_subject/' + data + '" target="_blank" title="Offered Subject"><i class="fas fa-list-ul"></i></a>';
                html += btn_download;
                // html += '<button type="button" class="btn btn-info btn-sm" id="upload_score_ofse" title="Upload OFSE Score"><i class="fas fa-file-upload"></i></button>';
                html += '<a class="btn btn-info btn-sm" href="<?=base_url()?>academic/ofse/ofse_subject/' + data + '" target="_blank" title="List Subject and Question"><i class="fas fa-book"></i></a>';
                html += '<a class="btn btn-info btn-sm" href="<?=base_url()?>academic/ofse/publish_all_ofse_score/' + data + '" target="_blank" title="Publish All Score"><i class="fas fa-upload"></i></a>';
                // html += '<button type="button" class="btn btn-info btn-sm" id="btn_publish_all_score" title="Publish All Score"><i class="fas fa-upload"></i></button>';
                html += '</div>';

                return html;
            }
        }
    ]
});

$(function() {
    $('button#btn_new_ofse').on('click', function(e) {
        e.preventDefault();

        $('div#modal_new_ofse').modal('show');
        $('input').val('');
    });

    $('button#submit_form').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form_new_ofse = $('form#form_new_ofse');
        var data = form_new_ofse.serialize();
        let url = form_new_ofse.attr('action');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success("Success!");
                $('div#modal_new_ofse').modal('hide');
                if (result.uri == '') {
                    ofse_table.ajax.reload(null, true);
                }else{
                    window.location.href = result.uri;
                }
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error!');
        })
    });

    $('table#ofse_table tbody').on('click', 'button#download_subject', function(e) {
        e.preventDefault();

        var table_data = ofse_table.row($(this).parents('tr')).data();
        let url = '<?= base_url()?>academic/ofse/generate_ofse_subject';
        var data = {
            ofse_period_id: table_data.ofse_period_id
        };
        download_ofse_template(url, data, table_data.ofse_period_id);
    });

    $('table#ofse_table tbody').on('click', 'button#download_student_ofse', function(e) {
        e.preventDefault();

        var table_data = ofse_table.row($(this).parents('tr')).data();
        let url = '<?= base_url()?>academic/ofse/generate_ofse_student';
        var data = {
            ofse_period_id: table_data.ofse_period_id
        };
        download_ofse_template(url, data, table_data.ofse_period_id);
    });

    $('table#ofse_table tbody').on('click', 'button#download_ofse_registration_structure', function(e) {
        e.preventDefault();

        var table_data = ofse_table.row($(this).parents('tr')).data();
        let url = '<?= base_url()?>academic/ofse/generate_ofse_structure';
        var data = {
            ofse_period_id: table_data.ofse_period_id
        };
        download_ofse_template(url, data, table_data.ofse_period_id);
    });

    $('table#ofse_table tbody').on('click', 'button#download_ofse_result', function(e) {
        e.preventDefault();

        var table_data = ofse_table.row($(this).parents('tr')).data();
        let url = '<?= base_url()?>academic/ofse/generate_ofse_result';
        var data = {
            ofse_period_id: table_data.ofse_period_id
        };
        download_ofse_template(url, data, table_data.ofse_period_id);
    });

    // $('table#ofse_table tbody').on('click', 'button#btn_publish_all_score', function(e) {
    //     e.preventDefault();
    //     $.blockUI();

    //     var table_data = ofse_table.row($(this).parents('tr')).data();
    //     let url = '<?= base_url()?>academic/ofse/publish_all_ofse_score';
    //     var data = {
    //         ofse_period_id: table_data.ofse_period_id
    //     };

    //     $.post(url, data, function(result) {
    //         $.unblockUI();
    //         if (result.code == 0) {
    //             toastr.success('Success!');
    //         }
    //         else {
    //             toastr.warning(result.message);
    //         }
    //     }, 'json').fail(function(params) {
    //         $.unblockUI();
    //         toastr.error('Error processing data!');
    //     })
    // });

    function download_ofse_template(url, data, data_id) {
        // var data = $('form#form_ofse_filter').serialize();
        $.blockUI();
        
        $.post(url , data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                window.location.href = '<?= base_url()?>academic/ofse/download_file/' + result.file + '/' + data_id;
            }else{
                toastr['error'](result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
    }
});
</script>