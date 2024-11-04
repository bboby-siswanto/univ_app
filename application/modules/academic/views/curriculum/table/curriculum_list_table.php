<div class="card">
    <div class="card-header">
        Curriculum Lists
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_curriculum">
                <i class="fa fa-plus"></i> Curriculum
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_curriculum" class="table table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Curriculum Name</th>
                        <th>Study Program</th>
                        <th>Active Year</th>
                        <th>Subject Count</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_curriculum_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new curriculum</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_curriculum">
                <?=modules::run('academic/curriculum/form_create_curriculum');?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var show_datatable_curriculum = function(filter_data) {
            if ($.fn.DataTable.isDataTable('table#table_curriculum')) {
                curriculum_list_table.destroy();
            }
            var term = {term: filter_data};

            curriculum_list_table = $('table#table_curriculum').DataTable({
                ajax: {
                    url: '<?= base_url()?>academic/curriculum/filter_curriculum_lists',
                    type: 'POST',
                    data: term
                },
                columns: [
                    {data: 'curriculum_name'},
                    {data: 'study_program_name'},
                    {data: 'academic_year_id'},
                    {data: 'subject_count'},
                    {data: 'curriculum_id'}
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function(data, type, row) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<button name="btn_edit_curriculum" type="button" data_id="'+data+'" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit Curriculum" ><i class="fas fa-edit"></i></button>';
                            html += '<button name="btn_copy_curriculum" type="button" data_id="'+data+'" class="btn btn-success btn-sm" data-toggle="tooltip" title="Create with copy data" ><i class="fas fa-copy"></i></button>';
                            html += '<a href="https://portal.iuli.ac.id/academic/curriculum/curriculum_lists/'+row.curriculum_id+'" class="btn btn-info btn-sm" title="View curriculum semester" target=""><i class="fas fa-bookmark"></i></a><a href="<?=base_url()?>academic/curriculum/curriculum_lists/'+row.curriculum_id+'/'+data+'" class="btn btn-info btn-sm" title="View curriculum subject" target=""><i class="fas fa-book"></i></a>';
                            if ('<?=$this->session->userdata("name")?>' == 'BUDI SISWANTO') {
                                html += '<button name="btn_remove_curriculum" type="button" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        targets: 0,
                        render: function(data, type, row) {
                            return '<a href="<?= base_url()?>academic/curriculum/curriculum_lists/' + row.curriculum_id + '">'+ data + '</a>';
                        }
                    }
                ]
            });
        }
        show_datatable_curriculum({
            program_id: 'All',
            study_program_id: 'All',
            academic_year_id: $('#filter_academic_year_id').val()
        });

        $('table#table_curriculum tbody').on('click', 'button[name="btn_remove_curriculum"]', function(e) {
            e.preventDefault();
            var curriculum_data = curriculum_list_table.row($(this).parents('tr')).data();
            
            var s_curriculum_id = curriculum_data.curriculum_id;
            console.log(s_curriculum_id);
            if (confirm('Are you sure to remove this curriculum data and everything related?')) {
                $.blockUI();
                $.post('<?=base_url()?>academic/curriculum/delete_curriculum', {curriculum_id: s_curriculum_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success remove data!', 'Success');
                        curriculum_list_table.ajax.reload(null, false);
                    }else{
                        toastr.warning(result.message, 'Waning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error proccessing data', 'Error!');
                });
            }
        });

        $('table#table_curriculum tbody').on('click', 'button[name="btn_edit_curriculum"]', function(e) {
            e.preventDefault();

            var curriculum_data = curriculum_list_table.row($(this).parents('tr')).data();
            var form = $('#form_save_curriculum').find('input, select');

            form.each(function(idx, val) {
                var key_name = val.name;
                $(this).val(curriculum_data[key_name]);
            });
            show_study_program(curriculum_data['study_program_id']);

            $('#form-copy').addClass('d-none');
            $('#term').val('edit');
            $('.title-modal').text('Edit curriculum data');
            $('div#new_curriculum_modal').modal('show');
        });
        
        $('table#table_curriculum tbody').on('click', 'button[name="btn_copy_curriculum"]', function(e) {
            e.preventDefault();
            
            var curriculum_data = curriculum_list_table.row($(this).parents('tr')).data();
            var form = $('#form_save_curriculum').find('input, select');
            $.each(form, function(idx, val) {
                var key_name = $(this).attr('name');
                $(this).val(curriculum_data[key_name]);
            });
            show_study_program(curriculum_data['study_program_id']);
            
            $('#term').val('copy');
            $('#form-copy').removeClass('d-none');
            $('.title-modal').text('Copy new curriculum');
            $('div#new_curriculum_modal').modal('show');
        });

        $('button#btn_new_curriculum').on('click', function(e) {
            e.preventDefault();
            
            $('.title-modal').text('Add new curriculum');
            $('#form_save_curriculum').find('input, select').val('');
            $('#form-copy').addClass('d-none');
            $('#term').val('create');
            $('#new_curriculum_modal').modal('show');

            $('#program_id').val('1').trigger('change');
            show_study_program();
        });

        $('button#btn_filter_curriculum').on('click', function(e){
            e.preventDefault();

            var a_data = $('#curriculum_filter_form').serializeArray();
            var search = {};
            $.each(a_data, function(index, value) {
                search[value.name] = value.value;
            });
            
            show_datatable_curriculum(search);
        });
    });
</script>