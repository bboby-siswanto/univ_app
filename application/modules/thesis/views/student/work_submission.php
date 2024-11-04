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
        <strong>Thesis Work Submission</strong>
    </div>
    <div class="card-body">
        <form method="post" url="<?=base_url()?>thesis/submit_student_work" onsubmit="return false" id="form_thesis_submission">
            <div class="row">
                <input type="hidden" name="thesis_student_id" id="thesis_student_id" value="<?= ($thesis_data) ? $thesis_data[0]->thesis_student_id : '' ?>">
                <input type="hidden" name="thesis_log_key" id="thesis_log_key" value="<?= ($thesis_log_data) ? $thesis_log_data[0]->thesis_log_id : '' ?>">
                <div class="col-sm-12 ">
                    <div class="border rounded p-2 m-2">
                        <div class="form-group">
                            <label for="thesis_title" class="required_text">Thesis Title</label>
                            <textarea name="thesis_title" id="thesis_title" class="form-control"><?= ($thesis_data) ? $thesis_data[0]->thesis_title : '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="border rounded p-2 m-2">
                        <div class="form-group">
                            <label for="advisor_1_update" class="required_text">Advisor</label>
                            <select name="advisor_1_update" id="advisor_1_update" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="advisor_1_institute_update">Institution</label>
                            <input type="text" name="advisor_1_institute" id="advisor_1_institute_update" class="form-control" value="<?=$advisor_approved_1 ? ($advisor_approved_1->institution_name) : '';?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="border rounded p-2 m-2">
                        <div class="form-group">
                            <label for="thesis_title">Co-Advisor</label>
                            <select name="advisor_2_update" id="advisor_2_update" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="advisor_2_institute_update">Institution</label>
                            <input type="text" name="advisor_2_institute" id="advisor_2_institute_update" class="form-control" value="<?=$advisor_approved_2 ? ($advisor_approved_2->institution_name) : '';?>" disabled>
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
                <form url="<?=base_url()?>thesis/submit_document_thesis" id="form_submit_file" onsubmit="return false">
                    <div class="row">
                        <div class="col-12">
                            <div class="small text-danger">Only file with extension pdf and docx</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label class="required_text">Thesis Work</label>
                                    <input type="file" name="file_tw" id="file_tw" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label class="required_text">Plagiarism Check</label>
                                    <input type="file" name="file_pc" id="file_pc" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label class="required_text">Thesis Log</label>
                                    <input type="file" name="file_tl" id="file_tl" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="border rounded p-2 m-2">
                                <div class="form-group">
                                    <label>Other Required Docs</label>
                                    <input type="file" name="file_other_doc" id="file_other_doc" class="form-control">
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
<div class="modal" tabindex="-1" role="dialog" id="modal_add_advisor">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Advisor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_new_advisor" url="<?=base_url()?>thesis/new_advisor" method="POST" onsubmit="return false">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Advisor Name</label>
                                <input type="text" name="advisor_personal_data_name" id="advisor_personal_data_name" class="form-control v_value">
                                <input type="hidden" name="advisor_personal_data_id" id="advisor_personal_data_id" class="v_value">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Institution</label>
                                <input type="text" name="advisor_institution_name" id="advisor_institution_name" class="form-control v_value">
                                <input type="hidden" name="advisor_institution_id" id="advisor_institution_id" class="v_value">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_new_advisor">Save changes</button>
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
            d.thesis_log_type = 'work';
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
    $('button#submit_new_advisor').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 2000});
        var form = $('#form_new_advisor');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                $('#modal_add_advisor').modal('hide');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        })
    });

    $('button#upload_file').on('click', function(e) {
        e.preventDefault();

        $('#modal_upload_file').modal('show');
    });
    
<?php
if ($advisor_approved_1) {
?>
    var newOption = new Option('<?=$advisor_approved_1->advisor_name;?>', '<?=$advisor_approved_1->advisor_id;?>', false, false);
    $('#advisor_1_update').append(newOption);
    $('#advisor_1_update').val('<?=$advisor_approved_1->advisor_id;?>').trigger('change');
<?php
}
if ($advisor_approved_2) {
?>
    var newOption = new Option('<?=$advisor_approved_2->advisor_name;?>', '<?=$advisor_approved_2->advisor_id;?>', false, false);
    $('#advisor_2_update').append(newOption);
    $('#advisor_2_update').val('<?=$advisor_approved_2->advisor_id;?>').trigger('change');
<?php
}
?>

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
                }
                else {
                    toastr.warning(rtn.message, 'Warning!');
                }
            }
        });
    });

    advisor_select2($('#advisor_1_update'), $('input#advisor_1_institute_update'));
    advisor_select2($('#advisor_2_update'), $('input#advisor_2_institute_update'));

    $('#advisor_personal_data_name').autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('personal_data/get_personal_data_by_name')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                var list = rtn.data;
                var arr = [];
                arr = $.map(list, function(m){
                    return {
                        label: m.personal_data_name,
                        value: m.personal_data_name,
                        id: m.personal_data_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            $('#advisor_personal_data_id').val(id);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('#advisor_personal_data_id').val('');
            }
        }
    });

    $('#advisor_institution_name').autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('institution/get_institutions_ajax')?>';
            var data = {
                term: request.term
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
            $('#advisor_institution_id').val(id);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('#advisor_institution_id').val('');
            }
        }
    });

    $('#advisor_personal_data_name').autocomplete( "option", "appendTo", "#form_new_advisor" );
    $('#advisor_institution_name').autocomplete( "option", "appendTo", "#form_new_advisor" );
});

function advisor_select2(el, elIns) {
    el.select2({
        minimumInputLength: 2,
        allowClear: true,
        placeholder: "Please select",
        theme: "bootstrap",
        ajax: {
            url: '<?=base_url()?>thesis/get_advisor_by_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function(result) {
                // data = result.data;
                // console.log(data);
                return {
                    results: $.map(result, function (item) {
                        return {
                            text: item.advisor_name,
                            id: item.advisor_id,
                            institution_id: item.institution_id,
                            institution_name: item.institution_name
                        }
                    })
                }
            }
        },
        language: {
            noResults: function(term) {
                return "No results found <button onclick='new_advisor()' class='btn btn-link'>+ Add Advisor</button>";
            }
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    el.on("change", function(e) { 
        var data_selected = el.select2('data');
        elIns.val(data_selected[0].institution_name);
    });
}

function new_advisor() {
    $('#advisor_personal_data_name').focus();
    $('.v_value').val('');
    $('#advisor_1_update').select2('close');
    $('#advisor_2_update').select2('close');
    $('#modal_add_advisor').modal('show');
}
</script>