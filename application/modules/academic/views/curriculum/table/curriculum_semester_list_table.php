<div class="card">
    <div class="card-header">
        Curriculum Semester Lists
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_curriculum">
                <i class="fa fa-plus"></i> Semester
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_curriculum_semester" class="table table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Semester Name</th>
                        <th>Total Credit Mandatory Fixed</th>
                        <th>Total Credit Elective Fixed</th>
                        <th>Total Credit Extracurricular Fixed</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_curriculum_semester_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new curriculum subject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_curriculum_semester">
                <?=modules::run('academic/curriculum/form_create_curriculum_semester', $curriculum_id);?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var show_datatable_curriculum_semester = function(filter_data) {
            if ($.fn.DataTable.isDataTable('table#table_curriculum_semester')) {
                curriculum_list_table.destroy();
            }

            curriculum_list_table = $('table#table_curriculum_semester').DataTable({
                ajax: {
                    url: '<?= base_url()?>academic/curriculum/filter_curriculum_semester_lists',
                    type: 'POST',
                    data: filter_data
                },
                order: [],
                columns: [
                    {data: 'semester_name'},
                    {data: 'curriculum_semester_total_credit_mandatory_fixed'},
                    {data: 'curriculum_semester_total_credit_elective_fixed'},
                    {data: 'curriculum_semester_total_credit_extracurricular_fixed'},
                    {data: 'semester_id'}
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function(data, type, row) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<button name="btn_curriculum_semester_edit" type="button" data_id="'+data+'" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit curriculum semester" ><i class="fas fa-edit"></i></button>';
                            html += '<button name="btn_copy_curriculum" type="button" data_id="'+data+'" class="btn btn-success btn-sm" data-toggle="tooltip" title="Create with copy data" ><i class="fas fa-copy"></i></button>';
                            html += '<a href="<?=base_url()?>academic/curriculum/curriculum_lists/'+row.curriculum_id+'/'+data+'" class="btn btn-info btn-sm" title="View curriculum subject" target=""><i class="fas fa-book"></i></a>';
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        targets: 0,
                        render: function(data, type, row) {
                            return '<a href="<?= base_url()?>academic/curriculum/curriculum_lists/' + row.curriculum_id + '/' + row.semester_id + '">'+ data + '</a>';
                        }
                    }
                ]
            });
        }
        show_datatable_curriculum_semester({
            curriculum_id : '<?= $curriculum_id;?>'
        });

        $('button#btn_new_curriculum').on('click', function(e) {
            e.preventDefault();
            var s_curriculum_id = '<?= $curriculum_id; ?>';
            $.post('<?= base_url()?>academic/curriculum/form_create_curriculum_semester/' + s_curriculum_id, {term: 'create'}, function(result) {
                $('div#modal_input_curriculum_semester').html(result.data);
                $('.title-modal').text('New curriculum semester');
                $('div#new_curriculum_semester_modal').modal('show');
            },'json');
        });

        $('table#table_curriculum_semester tbody').on('click', 'button[name="btn_copy_curriculum"]', function(e) {
            e.preventDefault();
            var s_semester_id = $(this).attr("data_id");
            var s_curriculum_id = '<?= $curriculum_id; ?>';
            $.post('<?= base_url()?>academic/curriculum/form_create_curriculum_semester/' + s_curriculum_id, {semester_id: s_semester_id, term: 'copy'}, function(result) {
                $('div#modal_input_curriculum_semester').html(result.data);
                $('.title-modal').text('Copy curriculum semester');
                $('div#new_curriculum_semester_modal').modal('show');
            },'json');
        });

        $('table#table_curriculum_semester tbody').on('click', 'button[name="btn_curriculum_semester_edit"]', function(e) {
            e.preventDefault();
            var s_semester_id = $(this).attr("data_id");
            var s_curriculum_id = '<?= $curriculum_id; ?>';
            $.post('<?= base_url()?>academic/curriculum/form_create_curriculum_semester/' + s_curriculum_id, {semester_id: s_semester_id, term: 'edit'}, function(result) {
                $('div#modal_input_curriculum_semester').html(result.data);
                $('.title-modal').text('Copy curriculum semester');
                $('#semester_id').attr('disabled','true');
                $('div#new_curriculum_semester_modal').modal('show');
            },'json');
        });
    });
</script>