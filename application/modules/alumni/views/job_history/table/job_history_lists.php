<div class="card">
    <div class="card-header">
        Job History Lists
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_job">
                <i class="fa fa-plus"></i> Company
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_job_history" class="table table-bordered table-striped table-hovered">
                <thead class="bg-dark">
                    <tr>
                        <th>Company</th>
                        <th>Address</th>
                        <th>Start Year</th>
                        <th>End Year</th>
                        <th>Job Title</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_job_history_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new job history</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_academic_history">
                <?=modules::run('alumni/job_history/form_create_job_history');?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('button#btn_new_job').on('click', function(e) {
            e.preventDefault();

            $('form#form_input_job_history').find('input, select, textarea').val('');
            $('#is_available').prop('checked', false);
            $('#company_end_date').removeAttr('readonly');
            $('div#new_job_history_modal').modal('show');
        });

        var show_datatable_job_history_lists = function(filter_data = false) {
            if($.fn.DataTable.isDataTable('table#table_job_history')){
                job_list_table.destroy();
            }

            job_list_table = $('table#table_job_history').DataTable({
                ajax: {
                    url: '<?= base_url()?>alumni/job_history/get_job_filtered<?= (isset($personal_data_id)) ? "/".$personal_data_id : "";?>',
                    type: 'POST',
                    data: filter_data
                },
                columns: [
                    {data: 'institution_name'},
                    {data: 'address_street'},
                    {data: 'academic_year_start_date'},
                    {data: 'academic_year_end_date'},
                    {data: 'ocupation_name'},
                    {data: 'status'},
                    {data: 'academic_history_id'}
                ],
                columnDefs: [
                    {
                        render: function ( data, type, row ) {
                            return data +' '+ row.address_city+' '+row.address_province+ ' ' +row.country_name+' '+row.address_zipcode;
                        },
                        targets: 1
                    },
                    {
                        render: function ( data, type, row ) {
                            return data.toUpperCase();
                        },
                        targets: 5
                    },
                    {
                        targets: -1,
                        orderable: false,
                        render: function ( data, type, row ) {
                            if (row['status'] == 'active') {
                                var btn_set = '<button name="btn_set_active" id="btn_set_active" type="button" class="btn btn-sm btn-danger" data_id="' + row.academic_history_id + '" data_status="' + row.status + '" title="This job is active"><i class="fas fa-power-off"></i></button>';
                            }else{
                                var btn_set = '<button name="btn_set_active" id="btn_set_active" type="button" class="btn btn-sm btn-success" data_id="' + row.academic_history_id + '" data_status="' + row.status + '" title="This job is inactive"><i class="fas fa-power-off"></i></button>';
                            }
                            var btn_request_assesment = '<button name="btn_req_assesment" id="btn_req_assesment" type="button" class="btn btn-info btn-sm" data_id="' + row.institution_id + '" title="Send request assesment to your company/institution"><i class="fas fa-paper-plane"></i></button>';

                            var html = '<div class="btn-group" role="group" aria-label="">';
                            // html += btn_set;
                            html += btn_request_assesment;
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                initComplete: function(settings, json) {
                    // var btn_delete = $('#table_job_history tbody tr').find($('button[data_main="yes"]'));
                    // btn_delete.remove();
                    table_init_complete()
                }
            });
        }

        show_datatable_job_history_lists();

        $('table#table_job_history tbody').on('click', 'button[name="btn_edit_academic_history"]', function(e) {
            e.preventDefault();
            $.blockUI();

            var s_academic_history_id = $(this).attr("data_id");
            
            $.post('<?= base_url()?>personal_data/job_history/form_create_job_history', {academic_history_id: s_academic_history_id}, function(result) {
                $('div#modal_input_academic_history').html(result.data);
                $('.title-modal').text('Update company data');
                $('div#new_job_history_modal').modal('toggle');
                $.unblockUI();
            }, 'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });
        });

        $('table#table_job_history tbody').on('click', 'button[name="btn_delete_academic_history"]', function(e) {
            e.preventDefault();
            
            if(confirm("Are you sure deleted this item ?")) {
                var s_academic_history_id = $(this).attr("data_id");
                $.blockUI();
                $.post('<?= base_url()?>personal_data/job_history/delete_job_history', {academic_history_id: s_academic_history_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('job data has been removed', 'Success');
                        if ($.fn.DataTable.isDataTable(job_list_table)) {
                            job_list_table.ajax.reload(table_init_complete);
                        }else{
                            window.location.reload();
                        }
                    }else{
                        toastr['warning'](result.message, 'Warning!');
                    }
                }, 'json').fail(function(xhr, txtStatus, errThrown) {
                    $.unblockUI();
                });
            }
        });

        $('table#table_job_history tbody').on('click', 'button#btn_req_assesment', function(e) {
            e.preventDefault();

            if (confirm('Send email to your company/institution ?')) {
                $.blockUI();
                var s_id = $(this).attr("data_id");
                $.post('<?=base_url()?>alumni/job_history/send_request_assesment', {s_institution_id: s_id}, function(result) {
                    $.unblockUI();
                    
                    if (result.code == 0) {
                        toastr.success('Success!');
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing your request!', 'Error!');
                });
            }
        })

        $('table#table_job_history tbody').on('click', 'button[name="btn_set_active"]', function(e) {
            e.preventDefault();

            if (confirm('Are you sure?')){
                $.blockUI();
                var s_id = $(this).attr("data_id");
                var s_status = $(this).attr("data_status");

                $.post('<?= base_url()?>personal_data/academic/prop_status', {academic_history_id: s_id, status: s_status}, function(result) {
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

        $('button#btn_create_job_history').on('click', function(e) {
            e.preventDefault();
            $.blockUI({
                baseZ: 2000
            });
            
            var form = $('form#form_input_job_history');
			var url = "<?=site_url('alumni/job_history/save_job_history')?>";
            var data = form.serialize();

            $.post(url, data, function(rtn){
				if(rtn.code == 0){
                    $.unblockUI();
					toastr['success']('Success saved data', 'Success!');
                    $('div#new_job_history_modal').modal('hide');
					show_datatable_job_history_lists();
				}
				else{
                    $.unblockUI();
                    toastr['error'](rtn.message, 'Warning!');
				}
            }, 'json').fail(function(xhr, txtStatus, errThrown) {
                toastr['error']('Error proccessing data', 'Warning!');
                $.unblockUI();
            });
        });
    });

    function table_init_complete() {
        var table = $('#table_job_history tbody tr');

        $.each(table, function(params) {
            var btn_toggle = $(this).find($('button#btn_set_active'));
            var status = btn_toggle.attr('data_status');
            if (status != 'active') {
                btn_toggle.toggleClass('btn-danger btn-success');
                btn_toggle.prop('title', 'Set active');
            }
        });
    }
</script>