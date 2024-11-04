<div class="card">
    <div class="card-header">
        Semester Lists
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_semester" type="button">
                <i class="fa fa-plus"></i> Semester
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="semester_table" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Academic Year</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div id="modal_input_semester_settings" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add new semester</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= modules::run('academic/semester/form_input_semester');?>
            </div>
            <div class="modal-footer">
                <button type="button" id="save_input_semester_setting" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="modal_progress" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Progress</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="progress_container"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" id="save_input_semester_setting" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        var datepicker = function(element) {
            var datepicker_start = $('#' + element + '_start_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: today
            }).on('change', function() {
                datepicker_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
                datepicker_end.datepicker('setDate', '');
            });

            var datepicker_end = $('#' + element + '_end_date').datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true
            });
        }

        var datepicker_deadline = $('#dikti_report_deadline').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            minDate: today
        })

        datepicker('semester');
        datepicker('offer_subject');
        datepicker('study_plan');
        datepicker('study_plan_approval');
        datepicker('repetition');
        datepicker('repetition_registration');

        var semester_table = $('table#semester_table').DataTable({
            ajax: {
                url: '<?= base_url()?>academic/semester/filter_semester_lists',
                type: 'POST'
            },
            ordering: false,
            columns: [
                {
	                data: 'academic_year_id',
	                render: function(data, type, row, meta) {
                        var icon_warning = '';
                        if (row['request_approval'] !== null) {
                            var request_approval = row['request_approval'];
                            if (request_approval['personal_data_id_approve'] == null) {
                                icon_warning = '<i class="fas fa-exclamation-triangle text-danger"></i>';
                            }
                        }
                        var semester_name = data + '/' + row.semester_type_name;
                        return '<a href="<?= base_url()?>academic/semester/semester_lists/' + row.academic_year_id + '/' + row.semester_type_id + '">' + semester_name + ' ' + icon_warning + '</a>';
                    }
				},
                {
                    data: 'semester_start_date',
                    render: function(data, type, row) {
                        var date = new Date(data)
                        var new_date = date.getDate() + ' ' + date.toLocaleString('default', { month: 'long' }) + ' ' + date.getFullYear();
                        return new_date;
                    }
                },
                {
                    data: 'semester_end_date',
                    render: function(data, type, row) {
                        var date = new Date(data)
                        var new_date = date.getDate() + ' ' + date.toLocaleString('default', { month: 'long' }) + ' ' + date.getFullYear();
                        return new_date;
                    }
                },
                {
	                data: 'semester_status',
	                render: function(data, type, row) {
                        return data.toUpperCase();
                    }
	            },
	            // {
		        //     data: 'academic_year_id',
		        //     render: function(data, type, row){
			    //         return '<button class="btn btn-sm btn-info" id="sync_data" title="Dikti Syncronize"><i class="fas fa-check"></i></button>';
		        //     }
	            // }
            ],
            // columnDefs: [
            //     {
            //         targets: [1, 2],
            //         render: function(data, type, row) {
            //             var date = new Date(data)
            //             var new_date = date.getDate() + ' ' + date.toLocaleString('default', { month: 'long' }) + ' ' + date.getFullYear();
            //             return new_date;
            //         }
            //     }
            // ]
        });

        var enforceModalFocusFn = $.fn.modal.Constructor.prototype._enforceFocus;
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};

        $('button#btn_new_semester').on('click', function(e) {
            e.preventDefault();

            $('form#form_input_semester_settings').find('input, select').val('');
            $('div#modal_input_semester_settings').modal('show');
        });

        $('button#save_input_semester_setting').on('click', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });

            var data = $('form#form_input_semester_settings').serialize();
            $.post('<?=base_url()?>academic/semester/semester_setting_master_save', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success save semester settings', 'Success!');
                    $('#modal_input_semester_settings').modal('hide');
                    semester_table.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(e) {
                $.unblockUI();
                toastr.warning('Error proccessing data', 'Warning!');
            });
        });
        
        $('table#semester_table tbody').on('click', 'button#sync_data', function(){
	        let table_data = semester_table.row($(this).parents('tr')).data();
	        $('div#modal_progress').modal('toggle');
/*
	        $.get('<?=site_url('feeder/start_sync_curriculum/')?>' + table_data['academic_year_id'], [], function(rtn){
		        if(rtn.code == 0){
			        $.unblockUI();
		        }
	        }, 'json');
*/

			$.ajax({
				xhr: function(){
					console.log('exec jing');
					var xhr = new window.XMLHttpRequest();
					// xhr.responseType = 'json';
					// Download progress
					xhr.onprogress = function(evt){
						console.log(evt);
					}
/*
					xhr.addEventListener("progress", function(evt){
						// let data = evt.target.response;
						// $('#progress_container').append(data);
						console.log(evt.target);
						// console.log(JSON.parse(data));
						// console.log(JSON.parse(evt.target.response));
					}, true);
*/
					
					return xhr;
			    },
			    dataType: 'json',
			    type: 'POST',
			    url: '<?=site_url('feeder/start_sync_curriculum/')?>',
			    data: {
				    academic_year_id: table_data['academic_year_id']
			    },
			    success: function(data){
				    console.log(data);
				    $('div#modal_progress').modal('toggle');
			        // Do something success-ish
			        $.unblockUI();
			    }
			});
        })

        $('table#semester_table tbody').on('click', 'button[name="btn_semester_edit"]', function(e) {
            e.preventDefault();
            
            var table_data = semester_table.row($(this).parents('tr')).data();
            var form_input = $('form#form_input_semester_settings').serializeArray();
            
            $.each(form_input, function(i, v) {
                var key = v.name;
                if (v.name == 'academic_year_id' || v.name == 'semester_type_id') {
                    $('#' + key).val(table_data[key]);
                }else{
                    $('#' + key).datepicker('setDate', table_data[key]);
                }
            });
            $('div#modal_input_semester_settings').modal('show');
        });
    });
</script>