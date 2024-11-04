<style>
    .c-pointer {
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="btn-group pb-2 float-right" role="group" aria-label="Basic example">
            <a class="btn btn-warning" id="btn_view_template_dictionary">
                <i class="fas fa-info"></i> View Dictionary Code
            </a>
            <a href="<?=base_url()?>apps/letter_numbering/list_number_of_letter" class="btn btn-primary">
                <i class="fas fa-list"></i>  Number List
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Letter Type Configuration
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="list_letter_type" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <th>Letter Name</th>
                    <th>Letter Description</th>
                    <th>Letter Code</th>
                    <th>Action</th>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="lt_template_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Template List <span class="abbr_lt"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <!-- <button type="button" id="test">test</button> -->
                <form action="<?=base_url()?>apps/letter_numbering/new_template_file" id="form_template_files" class="mb-3">
                    <input type="hidden" id="template_id_lt_modal" name="letter_type_id" value="999999999999">
                    <div class="input-group">
                        <input type="file" class="form-control" aria-describedby="basic-addon2" id="template_file" name="template_file" accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" id="submit_lt_template_file">Submit File</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            *) <small class="text-danger"><i>File format allowed (doc, docx, odt).</i></small>
                        </div>
                        <div class="col-sm-4">
                            *) <small class="text-danger"><i>You can only add and modify (not delete).</i></small>
                        </div>
                    </div>
                </form>
                <table id="list_template_lt_modal" class="table table-hover">
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="template_dictionary_list_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">General Template Dictionary</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-sm" id="template_key_list">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>${key_number}</code></td>
                            <td>Number of Letter</td>
                        </tr>
                        <tr>
                            <td><code>${key_date}</code></td>
                            <td>Date of Letter</td>
                        </tr>
                        <tr>
                            <td><code>${rector_name}</code></td>
                            <td>Rector Name</td>
                        </tr>
                        <tr>
                            <td><code>${rector_email}</code></td>
                            <td>Rector Email</td>
                        </tr>
                        <tr>
                            <td><code>${vice_academic_rector_name}</code></td>
                            <td>Vice Rector Name</td>
                        </tr>
                        <tr>
                            <td><code>${vice_academic_rector_email}</code></td>
                            <td>Vice Rector Email</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var template_key_list_table = $('table#template_key_list').DataTable({
    ordering: false,
    info: false
});
var table_list_letter_type = $('table#list_letter_type').DataTable({
    ajax: {
        url: '<?= base_url()?>apps/letter_numbering/letter_type',
        type: 'POST'
    },
    columns: [
        {
            data: 'letter_name'
        },
        {data: 'letter_description'},
        {data: 'letter_abbreviation'},
        {
            data: 'letter_type_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
                html += '<button id="lt_template_list" class="btn btn-info btn-sm" type="button" title="Template List"><i class="fas fa-puzzle-piece"></i></button>';
                html += '</div>';
                return html;
            }
        }
    ],
});

var table_template = $('table#list_template_lt_modal').DataTable({
    ordering: false,
    searching: false,
    info: false,
    language: {
      "emptyTable": "No templates available!"
    },
    ajax: {
        url: '<?= base_url()?>apps/letter_numbering/get_list_template',
        type: 'POST',
        data: function(d) {
            d.letter_type_key = $('#template_id_lt_modal').val()
        }
    },
    columns: [
        {
            data: 'filename',
            render: function(data, type, row) {
                var input = '<input type="file" class="form-control d-none input-tmp-file" id="template_file_' + row.template_id + '" name="template_file" accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">';
                var text_ = '<span id="template_' + row.template_id + '">' + row.filename + '</span>';
                return text_ + input;
            }
        },
        {
            data: 'template_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
                html += '<button id="lt_template_download_' + row.template_id + '" name="lt_template_download" class="btn btn-info btn-sm" type="button" title="Download"><i class="fas fa-download"></i></button>';
                html += '<button id="lt_template_edit_' + row.template_id + '" name="lt_template_edit" class="btn btn-info btn-sm" type="button" title="Change Template"><i class="fas fa-edit"></i></button>';
                html += '<button id="lt_template_cancel_' + row.template_id + '" name="lt_template_cancel" class="btn btn-danger btn-sm d-none" type="button" title="Cancel"><i class="fas fa-times"></i></button>';
                html += '<button id="lt_template_save_' + row.template_id + '" name="lt_template_save" class="btn btn-success btn-sm d-none" type="button" title="Save"><i class="fas fa-save"></i></button>';
                html += '</div>';
                return html;
            }
        }
    ],
});

$(function() {
    $('a#btn_view_template_dictionary').on('click', function(e) {
        e.preventDefault();

        $('div#template_dictionary_list_modal').modal('show');
    });

    $('button#submit_lt_template_file').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form_lt_template = $('form#form_template_files');
        var form_data = new FormData(form_lt_template[0]);                       
        var url = form_lt_template.attr('action');
        $.ajax({
            url: url,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(result){
                $.unblockUI();
                console.log(result);
                if (result.code == 0) {
                    $('input#template_file').val('');
                    table_list_letter_type.ajax.reload(null, false);
                    table_template.ajax.reload(null, false);
                    toastr.success('Success', 'Success');
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            },
            error: function (request, status, error) {
                $.unblockUI();
                toastr.error('Error processing data!');
            }
        });
    });

    $('table#list_template_lt_modal tbody').on('click', 'button[name="lt_template_edit"]', function(e) {
        e.preventDefault();
        var row_data = table_template.row($(this).parents('tr')).data();
        $('#template_' + row_data.template_id).addClass('d-none');
        $('input#template_file_' + row_data.template_id).removeClass('d-none');
        
        $('button#lt_template_download_' + row_data.template_id).addClass('d-none');
        $('button#lt_template_edit_' + row_data.template_id).addClass('d-none');
        $('button#lt_template_cancel_' + row_data.template_id).removeClass('d-none');
        $('button#lt_template_save_' + row_data.template_id).removeClass('d-none');
    });

    $('table#list_template_lt_modal tbody').on('click', 'button[name="lt_template_cancel"]', function(e) {
        e.preventDefault();
        var row_data = table_template.row($(this).parents('tr')).data();
        $('#template_' + row_data.template_id).removeClass('d-none');
        $('input#template_file_' + row_data.template_id).addClass('d-none');
        
        $('button#lt_template_download_' + row_data.template_id).removeClass('d-none');
        $('button#lt_template_edit_' + row_data.template_id).removeClass('d-none');
        $('button#lt_template_cancel_' + row_data.template_id).addClass('d-none');
        $('button#lt_template_save_' + row_data.template_id).addClass('d-none');
    });

    $('table#list_template_lt_modal tbody').on('click', 'button[name="lt_template_save"]', function(e) {
        e.preventDefault();
        var row_data = table_template.row($(this).parents('tr')).data();

        if (confirm('Are you sure to update template selected ?')) {
            $.blockUI({ baseZ: 2000 });
            var file_data = $('input#template_file_' + row_data.template_id).prop('files')[0];   
            var form_data = new FormData();                  
            form_data.append('template_file', file_data);
            form_data.append('letter_type_id', row_data.letter_type_id);
            form_data.append('template_key', row_data.template_id);
            var url = '<?=base_url()?>apps/letter_numbering/new_template_file';
            
            $.ajax({
                url: url,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(result){
                    $.unblockUI();
                    console.log(result);
                    if (result.code == 0) {
                        $('#template_' + row_data.template_id).removeClass('d-none');
                        $('input#template_file_' + row_data.template_id).addClass('d-none');
                        $('button#lt_template_download_' + row_data.template_id).removeClass('d-none');
                        $('button#lt_template_edit_' + row_data.template_id).removeClass('d-none');
                        $('button#lt_template_cancel_' + row_data.template_id).addClass('d-none');
                        $('button#lt_template_save_' + row_data.template_id).addClass('d-none');
                        
                        table_list_letter_type.ajax.reload(null, false);
                        table_template.ajax.reload(null, false);
                        toastr.success('Success', 'Success');
                    }
                    else {
                        toastr.warning(result.message, 'Warning!');
                    }
                },
                error: function (request, status, error) {
                    $.unblockUI();
                    toastr.error('Error processing data!');
                }
            });
        }
    });

    $('table#list_letter_type tbody').on('click', 'button#lt_template_list', function(e) {
        e.preventDefault();
        
        var table_lt_data = table_list_letter_type.row($(this).parents('tr')).data();
        $('.abbr_lt').html(table_lt_data.letter_abbreviation);
        $('form#form_template_files')[0].reset();
        $.when($('#template_id_lt_modal').val(table_lt_data.letter_type_id)).done(function() {
            table_template.ajax.reload();
        });
        $('#lt_template_modal').modal('show');
    });

    $('table#list_template_lt_modal tbody').on('click', 'button[name="lt_template_download"]', function(e) {
        e.preventDefault();
        
        var table_lt_template = table_template.row($(this).parents('tr')).data();

        var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + table_lt_template.filename_download;
        var win = window.open(loc, '_blank');
        if (win) {
            win.focus();
        }
        else {
            window.location.href = loc;
        }
    });
})
</script>