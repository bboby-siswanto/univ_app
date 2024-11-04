<div class="card">
    <div class="card-header">
        Subject Lists
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_subject">
                <i class="fa fa-plus"></i> Subject
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_subject" class="table table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Subject Credit</th>
                        <th>Program</th>
                        <th>Study Program</th>
                        <th>Subject Type (Dikti)</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_subject_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new subject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_subject">
                <?=modules::run('academic/subject/form_create_subject');?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var show_datatable_subject = function(filter_data) {
            if ($.fn.DataTable.isDataTable('table#table_subject')) {
                subject_list_table.destroy();
            }

            subject_list_table = $('table#table_subject').DataTable({
                ajax: {
                    url: '<?= base_url()?>academic/subject/filter_subject_lists',
                    type: 'POST',
                    data: filter_data
                },
                columns: [
                    {data: 'subject_name'},
                    {data: 'subject_code'},
                    {data: 'subject_credit'},
                    {data: 'program_name'},
                    {data: 'study_program_name'},
                    {data: 'nama_jenis_mata_kuliah'},
                    {data: 'subject_id'}
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function(data, type, row) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<button name="btn_edit_subject" type="button" data_id="'+data+'" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit Subject" ><i class="fas fa-edit"></i></button>';
                            html += '</div>';
                            return html;
                        }
                    }
                ]
            });
        }
        show_datatable_subject({
            program_id: $('#filter_program_id').val(),
            study_program_id: $('#filter_study_program_id').val()
        });

        $('button#btn_new_subject').on('click', function(e) {
            e.preventDefault();
            $('#form_save_subject').find('input, select').val('');
            $('.title-modal').text('Add new subject');
            $('#new_subject_modal').modal('show');
            $('#study_program_id_program').html('<option value="">Please select ...</option>');
            $('#program_id').val('1').trigger('change');
            show_study_program();
        });

        $('table#table_subject tbody').on('click', 'button[name="btn_edit_subject"]', function(e) {
            e.preventDefault();

            var subject_data = subject_list_table.row($(this).parents('tr')).data();
            var form = $('#form_save_subject').find('input, select');
            $.each(form, function(idx, val) {
                var key_name = $(this).attr('name');
                $(this).val(subject_data[key_name]);
            });
            show_study_program(subject_data['study_program_id']);

            $('.title-modal').text('Update subject data');
            $('div#new_subject_modal').modal('show');
        });

        $('table#table_subject tbody').on('click', 'button[name="btn_delete_subject"]', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure?')) {
                $.blockUI();
                var s_subject_id = $(this).attr("data_id");
                $.post('<?= base_url()?>academic/subject/delete_subject', {subject_id: s_subject_id}, function(result) {
                    if(result.code == 0){
                        toastr['success']('address data has been deleted', 'Success');
                        if ($.fn.DataTable.isDataTable(subject_list_table)) {
                            subject_list_table.ajax.reload();
                        }else{
                            window.location.reload();
                        }
                    }
                    else{
                        toastr['warning'](result.message, 'Warning!');
                    }
                    $.unblockUI();
                }, 'json').fail(function(xhr, txtStatus, errThrown) {
                    $.unblockUI();
                });
            }
        });

        $('button#btn_filter_subject').on('click', function(e){
            e.preventDefault();

            var s_program_id = $('#filter_program_id').val();
            var s_study_program_id = $('#filter_study_program_id').val();
            show_datatable_subject({
                program_id: s_program_id,
                study_program_id: s_study_program_id
            });
        });
    });
</script>