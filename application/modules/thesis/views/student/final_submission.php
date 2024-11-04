<div class="row">
    <div class="col-12 mb-3">
        <div class="btn-toolbar float-right" role="toolbar" aria-label="Action button groups">
            <div class="btn-group" role="group" aria-label="First group">
                <button type="button" class="btn btn-primary btn_upload" id="upload_file" data-toggle="tooltip" data-placement="bottom" title="Upload Thesis File">
                    <i class="fas fa-exclamation"></i> Upload Thesis File
                </button>
                <button type="button" class="btn btn-success" id="submit_thesis_submission" data-toggle="tooltip" data-placement="bottom" title="Submit to Advisor">
                    <i class="fas fa-save"></i> Submit to Dean
                </button>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <strong>Thesis Final Submission</strong>
    </div>
    <div class="card-body">
        <form method="post" url="<?=base_url()?>thesis/submit_student_final" onsubmit="return false" id="form_thesis_submission">
            <div class="row">
                <input type="hidden" name="thesis_student_id" id="thesis_student_id" value="<?= ($thesis_data) ? $thesis_data[0]->thesis_student_id : '' ?>">
                <input type="hidden" name="thesis_log_key" id="thesis_log_key" value="<?= ($thesis_log_data) ? $thesis_log_data[0]->thesis_log_id : '' ?>">
                <div class="col-sm-12 ">
                    <div class="border rounded p-2 m-2">
                        <div class="form-group">
                            <label for="thesis_title" class="required_text">Thesis Title</label>
                            <textarea name="thesis_title" id="thesis_title" class="form-control" disabled><?= ($thesis_data) ? $thesis_data[0]->thesis_title : '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="border rounded p-2 m-2">
                        <div class="form-group">
                            <label for="advisor_1_update" class="required_text">Advisor</label>
                            <input type="text" name="advisor_1" id="advisor_1_update" class="form-control v_value" value="<?=$advisor_approved_1 ? ($advisor_approved_1->advisor_name) : '';?>" disabled>
                            <input type="hidden" name="advisor_1_id" id="advisor_1_id_update" class="v_value" value="<?=$advisor_approved_1 ? ($advisor_approved_1->student_advisor_id) : '';?>">
                        </div>
                        <div class="form-group">
                            <label for="advisor_1_institute_update">Institution</label>
                            <input type="text" name="advisor_1_institute" id="advisor_1_institute_update" class="form-control v_value" value="<?=$advisor_approved_1 ? ($advisor_approved_1->institution_name) : '';?>" disabled>
                            <input type="hidden" name="advisor_1_id_institute" id="advisor_1_id_institute_update" class="v_value" value="<?=$advisor_approved_1 ? ($advisor_approved_1->institution_id) : '';?>">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="border rounded p-2 m-2">
                        <div class="form-group">
                            <label for="thesis_title">Co-Advisor</label>
                            <input type="text" name="advisor_2" id="advisor_2_update" class="form-control v_value" value="<?=$advisor_approved_2 ? ($advisor_approved_2->advisor_name) : '';?>" disabled>
                            <input type="hidden" name="advisor_2_id" id="advisor_2_id_update" class="v_value" value="<?=$advisor_approved_2 ? ($advisor_approved_2->student_advisor_id) : '';?>">
                        </div>
                        <div class="form-group">
                            <label for="advisor_2_institute_update">Institution</label>
                            <input type="text" name="advisor_2_institute" id="advisor_2_institute_update" class="form-control v_value" value="<?=$advisor_approved_2 ? ($advisor_approved_2->institution_name) : '';?>" disabled>
                            <input type="hidden" name="advisor_2_id_institute" id="advisor_2_id_institute_update" class="form-control v_value" value="<?=$advisor_approved_2 ? ($advisor_approved_2->institution_id) : '';?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table id="file_list_upload" class="table">
                        <thead>
                            <tr>
                                <th>File Uploaded</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_upload_file">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Required Files</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>thesis/submit_document_final_thesis" id="form_submit_file" onsubmit="return false">
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label class="required_text">Final Thesis</label>
                                    <input type="file" name="file_tf" id="file_tf" class="form-control v_value">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label class="required_text">Journal Publication</label>
                                    <input type="file" name="file_jp" id="file_jp" class="form-control v_value">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label>Other Doc Final Thesis</label>
                                    <input type="file" name="file_tf_ot" id="file_tf_ot" class="form-control v_value">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_upload_file">Upload</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var file_list_upload = $('table#file_list_upload').DataTable({
    paging: false,
    info: false,
    searching: false,
    ordering: false,
    ajax: {
        url: '<?= base_url()?>thesis/get_list_file_upload',
        type: 'POST',
        data: function(d) {
            d.thesis_log_id = $('#thesis_log_key').val();
            d.thesis_log_type = 'final';
        }
    },
    columns: [
        {data: 'filename'},
        {
            data: 'filepath',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm">';
                html += '<a href="<?=base_url()?>thesis/view_file/' + data + '" class="btn btn-info btn-sm" title="View Document"><i class="fas fa-eye"></i></a>';
                // html += '<button class="btn btn-info btn-sm" id="input_examiner" type="button" title="Input Examiner"><i class="fas fa-user-plus"></i></button>';
                // html += '<button class="btn btn-info btn-sm" id="examiner_list" type="button" title="Examiner List"><i class="fas fa-users-cog"></i></button>';
                html += '</div>';
                return html;
            }
        }
    ]
});

$(function() {
    $('button#upload_file').on('click', function(e) {
        e.preventDefault();

        $('#modal_upload_file').modal('show');
    });

    $('button#submit_thesis_submission').on('click', function(e) {
        e.preventDefault();

        if (confirm('Please re-check the contents and files that have been uploaded. Submission can only be done once. Are you sure to submit?')) {
            $.blockUI();
            var form = $('#form_thesis_submission');
            var data = form.serialize();
            var url = form.attr('url');
            
            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    setInterval(function () {
                        location.reload();
                    }, 1000); 
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing your data!');
            });
        }
    });

    $('button#submit_upload_file').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 2000});

        var form = $('#form_submit_file');
        var form_data = new FormData(form[0]);
        form_data.append('thesis_student_id',$('#thesis_student_id').val());
        form_data.append('thesis_title',$('#thesis_title').val());

        form_data.append('advisor_1_update',$('#advisor_1_update').val());
        form_data.append('advisor_1_id_update',$('#advisor_1_id_update').val());
        form_data.append('advisor_1_institute_update',$('#advisor_1_institute_update').val());
        form_data.append('advisor_1_id_institute_update',$('#advisor_1_id_institute_update').val());
        form_data.append('advisor_2_update',$('#advisor_2_update').val());
        form_data.append('advisor_2_id_update',$('#advisor_2_id_update').val());
        form_data.append('advisor_2_institute_update',$('#advisor_2_institute_update').val());
        form_data.append('advisor_2_id_institute_update',$('#advisor_2_id_institute_update').val());
        // console.log(form_data);
        $.ajax({
            url: form.attr('url'),
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function (xhr, status, error) {
                $.unblockUI();
                toastr.error('Error processing data!');
            },
            success: function(rtn){
                $.unblockUI();
                if (rtn.code == 0) {
                    $('input#thesis_student_id').val(rtn.thesis_student_id);
                    $('input#thesis_log_key').val(rtn.thesis_log_id);
                    file_list_upload.ajax.reload();
                    $('#modal_upload_file').modal('hide');
                    $('html, body').animate({
                        scrollTop: $("#file_list_upload").offset().top
                    }, 500);
                }
                else {
                    toastr.warning(rtn.message, 'Warning!');
                }
            }
        });
    });

    advisor_autocomplete($('input#advisor_1_update'), $('input#advisor_1_id_update'), $('input#advisor_1_institute_update'), $('input#advisor_1_id_institute_update'));
    advisor_autocomplete($('input#advisor_2_update'), $('input#advisor_2_id_update'), $('input#advisor_2_institute_update'), $('input#advisor_2_id_institute_update'));

    institute_autocomplete($('input#advisor_1_institute_update'), $('input#advisor_1_id_institute_update'));
    institute_autocomplete($('input#advisor_2_institute_update'), $('input#advisor_2_id_institute_update'));
});

function advisor_autocomplete(el, elId, elIns, elInstId){
    el.autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('thesis/get_advisor_by_name')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                var arr = [];
                arr = $.map(rtn, function(m){
                    return {
                        label: m.advisor_name,
                        value: m.advisor_name,
                        id: m.advisor_id,
                        institute: m.institution_name,
                        institute_id: m.insitution_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            var institute = ui.item.institute;
            var institute_id = ui.item.institute_id;
            elId.val(id);
            elIns.val(institute);
            elInstId.val(institute_id);
        },
        change: function(event, ui){
            if(ui.item === null){
                elId.val('');
                // el.val('');
                // elIns.val('');
                elInstId.val('');
                // alert('Please use the selection provided');
            }
        }
    });

    // el.autocomplete( "option", "appendTo", "#form_edit_thesis_work" );
};

function institute_autocomplete(el, elId){
    el.autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('institution/get_institutions_ajax')?>';
            var data = {
                term: request.term,
                university: 'true'
            };
            $.post(url, data, function(rtn){
                var list = rtn.data;
                var arr = [];
                arr = $.map(list, function(m){
                    return {
                        label: m.institution_name,
                        value: m.institution_name,
                        id: m.institution_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            elId.val(id);
        },
        change: function(event, ui){
            if(ui.item === null){
                elId.val('');
                // el.val('');
                // alert('Please use the selection provided');
            }
        }
    });

    // el.autocomplete( "option", "appendTo", "#form_edit_thesis_work" );
};
</script>