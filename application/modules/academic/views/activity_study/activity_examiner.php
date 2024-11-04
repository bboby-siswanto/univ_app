<div class="table-responsive">
    <table id="activity_examiner_table" class="table table-bordered">
        <thead class="bg-dark">
            <tr>
                <th>NIDN/NUPN/NIDK</th>
                <th><?=$activity_lecturer_type;?> Name</th>
                <th><?=$activity_lecturer_type;?> Sequence</th>
                <th>Category</th>
                <th>Feeder Sync</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal" role="dialog" id="activity_examiner_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add <?=$activity_lecturer_type;?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="form_activity_examiner">
                    <input type="hidden" name="activity_lecturer_type" value="<?=$activity_lecturer_type;?>">
                    <input type="hidden" id="examiner_activity_study_id" name="activity_study_id" value="<?=$activity_study->activity_study_id;?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="input_examiner_name"><?=$activity_lecturer_type;?></label>
                                <select name="employee_id" id="input_examiner_name" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="input_examiner_sequence"><?=$activity_lecturer_type;?> Sequence</label>
                                <input type="number" name="activity_lecturer_sequence" id="input_examiner_sequence" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="input_category_examiner">Category</label>
                                <select name="id_kategori_kegiatan" id="input_category_examiner" class="form-control">
                                    <option value="">Please Select</option>
                        <?php
                            foreach ($dikti_kategori_kegiatan as $key => $o_category) {
                        ?>
                                    <option value="<?= $o_category->id_kategori_kegiatan ;?>"><?=$o_category->id_kategori_kegiatan;?> - <?=$o_category->nama_kategori_kegiatan;?></option>
                        <?php
                            }
                        ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit_activity_examiner">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var activity_examiner_table = $('table#activity_examiner_table').DataTable({
        dom: 'Bfrtip',
        searching: false,
        paging: false,
        bInfo: false,
        buttons: [
            {
                text: 'Add <?=$activity_lecturer_type;?>',
                action: function ( e, dt, node, config ) {
                    $('#input_examiner_name').val('').trigger('change');
                    $('#input_examiner_sequence').val('');
                    $('#input_category_examiner').val('').trigger('change');
                    
                    $('#activity_examiner_modal').modal('show');
                }
            }
        ],
        processing: true,
        ajax: {
            type: 'POST',
            url: '<?=base_url()?>academic/activity_study/get_list_activity_lecturer',
            data: function(params) {
                var a_form_data = {
                    activity_study_id: '<?=$activity_study->activity_study_id;?>',
                    lecturer_type: '<?=$activity_lecturer_type;?>'
                }
                
                return a_form_data;
            }
        },
        columns: [
            {
                data: "employee_lecturer_number"
            },
            {data: "personal_data_name"},
            {data: "activity_lecturer_sequence"},
            {
                data: "nama_kategori_kegiatan"
            },
            {
                data: "activity_lecturer_sync",
                render: function(data, type, row) {
                    if (data == 0) {
                        return '<span class="badge badge-success">Success</span>';
                    }
                    else {
                        return '<span class="badge badge-danger">Error Code: ' + data + '</span>';
                    }
                }
            },
            {
                data: "activity_lecturer_id",
                orderable: false,
                render: function(data, type, row) {
                    var html = '<div class="btn-group" role="group">';
                    html += '<button id="btn_delete_examiner_activity" class="btn btn-danger btn-sm" type="button" title="Remove"><i class="fas fa-trash"></i></button>';
                    if (row.activity_lecturer_sync != 0) {
                        html += '<button id="btn_sync_examiner_activity" class="btn btn-warning btn-sm" type="button" title="Sync"><i class="fas fa-sync"></i></button>'
                    }
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    $('select#input_category_examiner').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        dropdownParent: $("#activity_examiner_modal")
    });

    $('select#input_examiner_name').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        minimumInputLength: 2,
        dropdownParent: $("#activity_examiner_modal"),
        ajax: {
            url: '<?=base_url()?>employee/get_lecturer_by_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    keyword: params.term,
                    have_nidn: 'YES'
                }
            },
            processResults: function(result_data) {
                data_result = result_data.data;
                return {
                    results: $.map(data_result, function (items) {
                        return {
                            text: items.employee_lecturer_number + ' - ' + items.personal_data_name,
                            id: items.employee_id
                        }

                    })
                }
            }
        }
    });

    $('table#activity_examiner_table tbody').on('click', 'button#btn_delete_examiner_activity', function(e) {
        e.preventDefault();
        var table_data = activity_examiner_table.row($(this).parents('tr')).data();
        
        if (confirm('Are you sure to remove examiner ' + table_data.personal_data_name + ' ?')) {
            $.blockUI();
            $.post('<?=base_url()?>academic/activity_study/delete_activity_lecturer', {activity_lecturer_id: table_data.activity_lecturer_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    activity_examiner_table.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error!');
            });
        }
    });

    $('table#activity_examiner_table tbody').on('click', 'button#btn_sync_examiner_activity', function(e) {
        e.preventDefault();
        $.blockUI();
        var table_data = activity_examiner_table.row($(this).parents('tr')).data();
        
        $.post('<?=base_url()?>academic/activity_study/force_sync_lecturer_activity', {activity_lecturer_id: table_data.activity_lecturer_id, activity_lecturer_type: 'examiner'}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success', 'Success!');
                activity_examiner_table.ajax.reload(null, false);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error!');
        });
    });

    $('button#btn_submit_activity_examiner').on('click', function(e) {
        e.preventDefault();

        $.blockUI({ baseZ: 2000 });

        var data = $('#form_activity_examiner').serialize();
        $.post('<?=base_url()?>academic/activity_study/save_lecturer_activity', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!', 'Success');
                $('#activity_examiner_modal').modal('hide');
                activity_examiner_table.ajax.reload(null, false);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
        
    });
});
</script>