<?=modules::run('student/show_name', $student_data->student_id, true);?>

<div class="card">
    <div class="card-header">
        Note List
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_record">
				<i class="fa fa-plus"></i> Note
			</button>
        </div>
    </div>
    <div class="card-body">
        <div class="custom-control custom-checkbox float-right mb-3">
            <input type="checkbox" class="custom-control-input" id="check_all_dept" name="check_all_dept" value="false">
            <label class="custom-control-label" for="check_all_dept">Show all note department</label>
        </div>
        <div class="table-responsive">
            <table id="note_list" class="table table-bordered table-striped">
                <thead classs="bg-dark">
                    <tr>
                        <th>Datetime</th>
                        <th>Category</th>
                        <th>Note</th>
                        <th>Noted By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="new_record">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes <?=$student_data->personal_data_name;?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url()?>student/submit_record" onsubmit="return false" id="form_input_record" enctype="multipart/form-data">
                    <input type="hidden" name="record_id" id="record_id">
                    <input type="hidden" name="personal_data_id" value="<?=$student_data->personal_data_id;?>">
                    <div class="form-group">
                        <label for="record_category" class="required_text">Category</label>
                        <select name="record_category" id="record_category" class="form-control">
                        <?php
                        if (count($a_record_category) > 0) {
                            foreach ($a_record_category as $s_category) {
                        ?>
                            <option value="<?=$s_category;?>"><?=strtoupper($s_category);?></option>
                        <?php
                            }
                        }
                        ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="record_comment" class="required_text">Notes</label>
                        <textarea name="record_comment" id="record_comment" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="record_file">Upload File</label>
                        <input type="file" class="form-control" id="file_upload" name="files[]" multiple>
                    </div>
                    <div class="form-group" id="list_files"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_record" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_filelist">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Files</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Download File:</h4>
                <input type="hidden" name="record_key" id="record_key">
                <table class="table table-bordered" id="table_listfile">
                    <thead class="bg-dark">
                        <tr>
                            <th>Filename</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var note_list = $('table#note_list').DataTable({
        processing: true,
        ajax:{
            url: '<?=base_url()?>student/get_record_notes',
            type: 'POST',
            data: function(d){
                d.personal_data_id = '<?=$student_data->personal_data_id;?>',
                d.all_dept = $('#check_all_dept').val()
            }
        },
        columns: [
            {data: 'record_added'},
            {
                data: 'record_category',
                render: function(data, type, rows) {
                    return data.toUpperCase();
                }
            },
            {data: 'record_comment'},
            {data: 'personal_data_name'},
            {
                data: 'record_id',
                orderable: false,
                render: function(data, type, rows) {
                    let btn_update = '<button id="update_record" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                    var html = '<div class="btn-group" role="group" aria-label="">';
                    if ('<?=$employee_login;?>' == rows['employee_id']) {
                        html += btn_update;
                    }
                    html += '<button id="show_list_file" class="btn btn-info btn-sm"><i class="fas fa-file-download"></i></button>';
                    html += '</div>';

                    return html;
                }
            }
        ]
    });

    var table_listfile = $('table#table_listfile').DataTable({
        processing: true,
        ordering: false,
        paging: false,
        info: false,
        searching: false,
        ajax:{
            url: '<?=base_url()?>student/get_record_file',
            type: 'POST',
            data: function(d){
                d.record_id = $('#record_key').val()
            }
        },
        columns: [
            {
                data: 'record_file_name',
                render: function(data, type, row) {
                    return '<a href="<?=base_url()?>file_manager/student_files/record/<?=$student_data->student_id;?>/' + row.record_file_name + '/yes" target="_blank">' + data + '</a>';
                }
            },
            {data: 'timestamp'}
        ]
    });

    $(function() {
        $("#check_all_dept").change(function() {
            if(this.checked) {
                this.value = 'true';
            }else{
                this.value = 'false';
            }
            note_list.ajax.reload(null, false);
        });

        $('button#btn_new_record').on('click', function(e) {
            e.preventDefault();

            $('input#record_id').val('');
            $('#record_category').val(0);
            $('#record_comment').val('');
            $('#new_record').modal('show');
        });
        
        $('table#note_list tbody').on('click', 'button#update_record', function(e) {
            e.preventDefault();

            var table_data = note_list.row($(this).parents('tr')).data();
            $('input#record_id').val(table_data.record_id);
            $('#record_category').val(table_data.record_category);
            $('#record_comment').val(table_data.record_comment);
            $('div#list_files').html('');

            if (table_data.record_files) {
                $.each(table_data.record_files, function(i, v) {
                    var files = '<label>' + v.record_file_name + '';
                    files += ' <span class="badge badge-danger btn" data-id="' + v.record_file_id + '" onclick="remove_file(\'' + v.record_file_id + '\')" title="Remove this file"><i class="fas fa-times"></i></span>';
                    files += '</label><br>';
                    $('div#list_files').append(files);
                });
            }
            $('#new_record').modal('show');
        });

        $('table#note_list tbody').on('click', 'button#show_list_file', function(e) {
            e.preventDefault();

            var table_data = note_list.row($(this).parents('tr')).data();
            $('#record_key').val(table_data.record_id);
            table_listfile.ajax.reload();
            $('#modal_filelist').modal('show');
        });

        $('button#submit_record').on('click', function(e) {
            e.preventDefault();

            let form = $('form#form_input_record');
            let uri = form.attr('action');
            // var data = form.serialize();

            // var formvalue = $("#form_input_record");
            var form_data = new FormData(form[0]);

            $.blockUI({ baseZ: 2000 });
            $.ajax({
                url: '<?=base_url()?>student/submit_record',
                method: 'POST',
                dataType: 'json',
                cache: false,
                data: form_data,
                contentType: false,
                processData: false,
                success: function(response){
                    $.unblockUI();
                    if (response.code == 0) {
                        toastr.success('Success');
                        $('#new_record').modal('hide');
                        note_list.ajax.reload(null, false);
                    }else{
                        toastr.warning(response.message, 'Warning!');
                    }
                } 
            }).fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', "Error");
            });
        });
    });

    function remove_file(data_id) {
        // console.log(data_id);
        if (confirm("Are you sure to remove this file from record?")) {
            $.blockUI({ baseZ: 2000 });
            $.post('<?=base_url()?>student/remove_file_record', {data: data_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    note_list.ajax.reload(null, false);
                    toastr.success('Success!');
                    $('#new_record').modal('hide');
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        }
    }
</script>