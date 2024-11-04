<div class="table-responsive">
    <table id="student_supplement" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th width="20px">No</th>
                <th>Comment</th>
                <th width="10%">Category</th>
                <th width="8%">Action</th>
            </tr>
        </thead>
    </table>
</div>
<div id="modal_supplement" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Supplement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_input_supplement" onsubmit="return false">
                    <input type="hidden" id="supplement_student_id" name="student_id" value="<?=$student_id?>">
                    <input type="hidden" id="supplement_academic_year_id" name="academic_year_id">
                    <input type="hidden" id="supplement_semester_type_id" name="semester_type_id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="required_text">Category</label>
                                <select name="supplement_category" id="supplement_category" class="form-control">
                                    <option value="">Please select..</option>
                            <?php
                                if ($student_category) {
                                    foreach ($student_category as $category) {
                            ?>
                                    <option value="<?=$category?>"><?=strtoupper($category)?></option>
                            <?php
                                    }
                                }
                            ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required_text">Comment</label>
                        <textarea name="supplement_comment" id="supplement_comment" class="form-control" cols="30" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="submit_supplement" type="button" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var view_supplement = function(data_filter, table_target) {
        if($.fn.DataTable.isDataTable('table#student_supplement')){
            $('table#student_supplement').DataTable().destroy();
        }

        table_supplement = $('table#student_supplement').DataTable({
            ordering: false,
            searching: false,
            info: false,
            paging: false,
            processing: true,
            ajax: {
                url: '<?= base_url()?>academic/score/view_table_student_supplement',
                type: 'POST',
                data: data_filter
            },
            columns: [
                {
                    data: 'supplement_id',
                    render: function ( data, type, row, meta ) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'supplement_comment'},
                {data: 'supplement_category'},
                {
                    data: 'supplement_id',
                    render: function (data, type, row) {
                        var html = '<div class="btn-group">';
                        html += '<button type="button" class="btn btn-danger btn-sm" id="remove_supplement">Delete</button>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
        });
    }
    
    $(function() {
        view_supplement({
            student_id: '<?= $student_id;?>', 
            academic_year_id: $('#filter_academic_year_id').val(), 
            semester_type_id: $('#filter_semester_type_id').val(),
        });

        $('button#submit_supplement').on('click', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });
            var data = $('form#form_input_supplement').serialize();
            $.post('<?=base_url()?>academic/score/save_supplement', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('div#modal_supplement').modal('hide');
                    toastr.success('Success save data', 'Success');
                    table_supplement.ajax.reload();
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        });

        $('table#student_supplement tbody').on('click', 'button#remove_supplement', function(e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $.blockUI();
                var table_data = table_supplement.row($(this).parents('tr')).data();
                $.post('<?=base_url()?>academic/score/remove_supplement', {supplement_id: table_data.supplement_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success', 'Success');
                        table_supplement.ajax.reload();
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                });
            }
        });
    });
</script>