<style>
    .dropdown-item.disabled, .dropdown-item:disabled {
        color: #73818f !important;
        pointer-events: none;
        background-color: transparent;
    }
</style>
<div class="card">
    <div class="card-header">
        Thesis Final Submission List
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 pb-3">
                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mr-2" role="group" aria-label="Second group">
                        <div class="btn-group" role="group">
                            <button id="btnGrouView" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                View
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGrouView">
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_final_file" href="#" target="_blank">Thesis Final</a>
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_final_journal_publication" href="#" target="_blank">Thesis Journal</a>
                                <a class="dropdown-item thesis_file_button disabled" id="btn_view_final_other_doc" href="#" target="_blank">Thesis Final Other File</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <table class="table table-bordered" id="submission_list_table">
                    <thead class="bg-dark">
                        <tr>
                            <th></th>
                            <th>Student Name</th>
                            <th>Prodi</th>
                            <th>Batch</th>
                            <th>Thesis Title</th>
                            <th>Advisor</th>
                            <th>Examiner</th>
                            <!-- <th>Current Status</th> -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var users = '<?=$access_thesis;?>';
    var counter = 1;
    // console.log(type);
    
    var submission_list_table = $('table#submission_list_table').DataTable({
        order: [[1, "asc"]],
        ajax:{
            url: '<?=base_url()?>thesis/get_thesis_list',
            type: 'POST',
            data: function(d) {
                d.current_progress = 'final';
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
            {data: 'study_program_abbreviation'},
            {data: 'student_batch'},
            {data: 'thesis_title'},
            {data: 'advisor_approved'},
            {data: 'examiner_approved'},
            // {
            //     data: 'current_status',
            //     render: function(data, type, row) {
            //         var html = '<label class="badge badge-pill badge-warning">' + data + '</label>';
            //         switch (data) {
            //             case 'approved_hsp':
            //                 html = '<label class="badge badge-pill badge-info">' + data + '</label>';
            //                 break;

            //             case 'approved':
            //                 html = '<label class="badge badge-pill badge-success">' + data + '</label>';
            //                 break;

            //             case 'rejected':
            //                 html = '<label class="badge badge-pill badge-danger">' + data + '</label>';
            //                 break;

            //             case 'revision':
            //                 var html = '<label class="badge badge-pill badge-secondary">' + data + '</label>';
            //                 break;
                    
            //             default:
            //                 break;
            //         }
            //         html += '<span id="data_id" class="d-none">' + row.thesis_student_id + '</span>';
            //         return html;
            //     }
            // },
        ],
        select: {
            style: 'single'
        }
    });

    submission_list_table.on('select', function(e, dt, type, indexes) {
        var row_data = submission_list_table.row(indexes).data();
        // console.log(row_data.thesis_log_files);
        var link_docs = '<?=base_url()?>thesis/view_file/thesis_final/' + row_data.thesis_student_id + '/';

        // if ((row_data.thesis_final_fname != '') && (row_data.thesis_final_fname != null)) {
        //     $('#btn_view_thesis_final').removeClass('disabled');
        //     $('#btn_view_thesis_final').attr('href', link_docs + row_data.thesis_final_fname);
        // }
        var row_files = row_data.thesis_log_files;
        $('.thesis_file_button').addClass('disabled');
        if (row_files) {
            $.each(row_files, function(i, v) {
                $('#btn_view_' + v.filename_button).removeClass('disabled');
                $('#btn_view_' + v.filename_button).attr('href', link_docs + v.thesis_filename);
            })
        }
    }).on('deselect', function(e, dt, type, indexes) {
        $('#btn_view_thesis_final').addClass('disabled');
    });

});

function show_view_file(action_fname) {
    var checked = submission_list_table.rows({selected: false});
    var count_checked = checked.count();

    if (count_checked <= 0) {
        toastr.warning('Please select one record from the Thesis Work Submission List', 'Warning!');
    }
    else {
        var data_check = checked.data();
        if (data_check.length > 0) {
            switch (action_fname) {
                case 'thesis_work':
                    if (condition) {
                        
                    }
                    break;
            
                default:
                    break;
            }
        }
        else {
            toastr.warning('Please select one record from the Thesis Work Submission Lists', 'Warning!');
        }
    }
}
</script>