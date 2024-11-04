<div class="card">
    <div class="card-header">
        Job Vacancy
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_job_vacancy">
                <i class="fa fa-plus"></i> Job Vacancy
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="vacancy_table" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Company</th>
                        <th>Job Title</th>
                        <th>Address</th>
                        <th>Job Description</th>
                        <th>Requirements</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_new_job_vacancy">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new job vacancy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_academic_history">
                <?= modules::run('vacancy/form_input_vacancy'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#btn_new_job_vacancy').on('click', function(e) {
            e.preventDefault();
            $('#modal_new_job_vacancy').modal('show');
        });

        var show_datatable_job_vacancy_lists = function(filter_data = false) {
            if($.fn.DataTable.isDataTable('table#tablvacancy_tablee_job_history')){
                job_list_table.destroy();
            }

            job_list_table = $('table#vacancy_table').DataTable({
                ajax: {
                    url: '<?= base_url()?>alumni/vacancy/get_data_filtered',
                    type: 'POST',
                    data: filter_data
                },
                columns: [
                    {data: 'institution_name'},
                    {data: 'ocupation_name'},
                    {data: 'address_street'},
                    {data: 'job_description'},
                    {data: 'requirements'},
                    {data: 'post_status'},
                    {data: 'job_vacancy_id'}
                ],
                columnDefs: [
                    {
                        render: function ( data, type, row ) {
                            return data +' '+ row.address_city+' '+row.address_province+ ' ' +row.country_name+' '+row.address_zipcode;
                        },
                        targets: 2
                    },
                    {
                        render: function ( data, type, row ) {
                            var res = data.toUpperCase();
                            return res;
                        },
                        targets: 5
                    },
                    {
                        targets: -1,
                        orderable: false,
                        render: function ( data, type, row ) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<button name="btn_close_job_vacancy" type="button" data_id="'+data+'" data_status="'+row.post_status+'" id="btn_close_job_vacancy" class="btn btn-success btn-sm" title="Close job vacancy" ><i class="fas fa-power-off"></i></button>';
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                initComplete: function(settings, json) {
                    table_init_complete();
                }
            });
        }

        show_datatable_job_vacancy_lists();

        function table_init_complete(){
            var table = $('#vacancy_table tbody tr');
            $.each(table, function(params) {
                var btn_toggle = $(this).find($('button#btn_close_job_vacancy'));
                var status = btn_toggle.attr('data_status');
                if (status != 'open') {
                    btn_toggle.toggleClass('btn-success btn-danger');
                    btn_toggle.prop('title', 'Open job vacancy');
                }
            });
        }

        $('table#vacancy_table tbody').on('click', 'button[name="btn_edit_academic_history"]', function(e) {
            e.preventDefault();
            
            var job_vacancy_data = job_list_table.row($(this).parents('tr')).data();
            var form = $('#form_vacancy').find('input, select, textarea');

            form.each(function(idx, val) {
                var key_name = val.name;
                $(this).val(job_vacancy_data[key_name]);
            });
            $('#company_found_status').val('1');
            $('.title-modal').text('Edit job vacancy data');
            $('div#modal_new_job_vacancy').modal('show');
        });

        $('table#vacancy_table tbody').on('click', 'button[name="btn_delete_academic_history"]', function(e) {
            e.preventDefault();

            if (confirm('Are you sure?')){
                $.blockUI();
                var s_job_vacancy_id = $(this).attr("data_id");

                $.post('<?= base_url()?>vacancy/remove_job_vacancy', {job_vacancy_id: s_job_vacancy_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('data has been deleted', 'Success!');
                        job_list_table.ajax.reload(table_init_complete);
                    }else{
                        toastr['error'](result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                });
            }
        });

        $('table#vacancy_table tbody').on('click', 'button[name="btn_close_job_vacancy"]', function(e) {
            e.preventDefault();

            if (confirm('Are you sure?')){
                $.blockUI();
                var s_job_vacancy_id = $(this).attr("data_id");
                var s_status = $(this).attr("data_status");

                $.post('<?= base_url()?>alumni/vacancy/prop_status', {job_vacancy_id: s_job_vacancy_id, status: s_status}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('data has been change', 'Success!');
                        job_list_table.ajax.reload(table_init_complete);
                    }else{
                        toastr['error'](result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                });
            }
        });
    });
</script>