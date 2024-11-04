<style>
    .table-responsive {
        min-height: 600px !important;
    }
</style>
<div class="card">
    <div class="card-header">
        Student List <?= (isset($class_type)) ? '.' : ''; ?>
        <div class="card-header-actions">
<!--
			<a class="card-header-action" href="#" data-toggle="modal" data-target="#new_candidate_modal" aria-expanded="true">
				<i class="fa fa-plus"></i> Candidate
			</a>
-->
			<!-- <a class="card-header-action" href="#" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
				<i class="fa fa-gear"></i> Quick Actions
			</a> -->
			<!-- <div class="dropdown-menu" aria-labelledby="settings_dropdown"> -->
				<!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#new_candidate_modal" aria-expanded="true">
					<i class="fa fa-plus"></i> Candidate
				</a> -->
				<!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#compose_email_modal" aria-expanded="true">
					<i class="fa fa-envelope"></i> Compose Bulk Email
				</a> -->
			<!-- </div> -->
		</div>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
		    <table id="student_list_table" class="table table-striped table-sm table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Active Date</th>
						<th>Enrollment Paid</th>
						<th>Student Number</th>
						<th>Batch</th>
						<th>Student Email</th>
						<th>Date of Birth</th>
						<th>Place of Birth</th>
						<th>Phone</th>
						<th>School</th>
						<th>SGS Code</th>
						<th>Referrer</th>
						<th>Scholarship Registration</th>
						<th>Institute</th>
						<th>Study Program</th>
						<th>Study Program Alternative</th>
						<th>Candidate Status</th>
						<th>Register Date</th>
						<th>Email Confirmed</th>
						<th>Finish Online Test</th>
						<th>Parent Name</th>
						<th>Parent Phone</th>
						<th>Parent Email</th>
						<th>Parent Occupation</th>
						<th>City</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="set_refferal_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Refferenced Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_refferal_input" onsubmit="return false">
					<input type="hidden" name="personal_data_id" id="personal_data_id_refferal">
					<div class="form-group">
						<label for="reffference_code_refferal">Refference Code</label>
						<input type="text" class="form-control" name="reffference_code_refferal" id="reffference_code_refferal">
					</div>
				</form>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="submit_refference_code_refferal">Submit</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>
<script>
	var class_selected = JSON.parse('<?= (isset($class_type)) ? json_encode($class_type) : [];?>');
	$('select[id="academic_year_id"]').val('<?=$active_batch[0]->academic_year_id?>');

	$('button#filter_student').on('click', function(e){
		e.preventDefault();
		// var form = $('form#student_filter_form');
		// var filter_data = objectify_form(form.serializeArray());
		show_student_table();
	});
	
	function show_student_table(){
		var form = $('form#student_filter_form');
		var filter_data = objectify_form(form.serializeArray());
		console.log(filter_data);

		if($.fn.DataTable.isDataTable('table#student_list_table')){
			student_list_table.destroy();
		}
		
		student_list_table = $('table#student_list_table').DataTable({
			responsive: true,
			ajax: {
				url: '<?=site_url('student/filter_result')?>',
				type: 'POST',
				data: filter_data
			},
			dom: 'Bfrltip',
			buttons: [
				// 'csvHtml5', 'excel', 'pdf', 'print', 'colvis',
				'csvHtml5',
				{
					text: "Excel",
					action: function ( e, dt, node, config ) {
						$.blockUI();
						var col_src = dt.columns().dataSrc();
						var keys = [];
						var title = [];
						var list_data = [];
						var row_data = dt.rows().data();
						
						for (let i_col = 0; i_col < (col_src.length - 1); i_col++) {
							keys.push(col_src[i_col]);
							title.push(dt.columns().header()[i_col].innerText);
						}

						var param_data = dt.ajax.params();
						param_data['list_key'] = keys;
						param_data['list_title'] = title;
						// console.log(param_data);return false;
						
						$.post('<?=base_url()?>student/download_list', param_data, function(result) {
							if (result.code == 0) {
								$.unblockUI();
								window.location.href = '<?=base_url()?>file_manager/download_temp/' + result.filename;
							}
							else{
								toastr.warning(result.message, 'Warning!');
							}
						}, 'json').fail(function(params) {
							$.unblockUI();
							toastr.error('Error processing data!', 'Error');
						});
					}
				},
				'colvis',
			],
			columns: [
				{ 
					data: 'personal_data_name',
					responsivePriority: 1,
					render: function(data, type, row){
						return '<a href="<?=site_url('personal_data/profile/')?>'+row['student_id']+'/'+row['personal_data_id']+'">'+data+'</a>';
					}
				},
				{
					data: 'personal_data_email',
					responsivePriority: 2,
					// render: function(data, type, row){
					// 	return '<a href="<?=site_url('personal_data/profile/')?>'+row['student_id']+'/'+row['personal_data_id']+'">'+data+'</a>';
					// }
				},
				{ data: 'student_date_active', defaultContent: 'N/A' },
				{
					data: 'has_paid_enrollment_fee', defaultContent: 'N/A', responsivePriority: 6,
					render: function(data, type, row) {
						return (data) ? 'TRUE' : 'FALSE';
					}
				},
				{
					data: 'student_number', defaultContent: 'N/A',
				},
				{ data: 'academic_year_id' },
				{ data: 'student_email', defaultContent: 'N/A' },
				{ data: 'personal_data_date_of_birth', defaultContent: 'N/A' },
				{ data: 'personal_data_place_of_birth', defaultContent: 'N/A' },
				{
					data: 'personal_data_cellular', responsivePriority: 3,
					render: function(data, type, row) {
						return '<a href="https://wa.me/' + data + '" target="_blank">' + data + '</a>';
					}
				},
				{
					data: 'institution_name',
					defaultContent: 'N/A',
					responsivePriority: '<?= ($this->session->userdata('employee_id') == '24284490-2802-4094-9675-059de314a723') ? '5' : '20' ?>'
				},
				{ data: 'personal_data_reference_code', defaultContent: 'N/A' },
				{ data: 'referal_name', defaultContent: 'N/A' },
				{ data: 'scholarship_name' },
				{ data: 'type_of_admission_name'},
				{ data: 'study_program_name', responsivePriority: 9 },
				{ data: 'study_program_alternative', responsivePriority: 10 },
				{
					data: 'student_status',
					defaultContent: 'N/A',
					responsivePriority: 4,
					render: function(data, type, row){
						return (data === null) ? '' : data.toUpperCase();
					}
				},
				{
					data: 'register_date',
					responsivePriority: '<?= ($this->session->userdata('employee_id') == '24284490-2802-4094-9675-059de314a723') ? '20' : '5' ?>'
					// render: function(data, type, row){
					// 	return moment(data).format('YYYY-MM-DD');
					// }
				},
				{
					data: 'personal_data_email_confirmation',
					responsivePriority: 7
				},
				{
					data: 'has_finished_online_test',
					responsivePriority: 8,
					render: function(data, type, row) {
						var html = data;
						if (row.exam_candidate_id != '') {
							var html = '<a href="<?=base_url()?>admission/entrance_test/participant_result/' + row.exam_candidate_id + '" target="blank">' + data + '</a>';
						}
						return html;
					}
				},
				{
					data: 'family_name'
				},
				{
					data: 'family_contact'
				},
				{
					data: 'family_email'
				},
				{
					data: 'family_ocupation'
				},
				{
					data: 'address_city'
				},
				{
					data: 'personal_data_id', responsivePriority: 6,
					render: function(data, type, row){
						let btnProfile = '<a href="<?=site_url('personal_data/profile/')?>' + row['student_id'] + '/' + row['personal_data_id'] + '" class="btn btn-info btn-sm" title="Show Profile" target=""><i class="fas fa-id-badge"></i></a>';
						let btnMessage = '<button type="button" id="btn_display_modal" class="btn btn-info btn-sm" title="Send Mail"><i class="fas fa-envelope"></i></button>';
						let btnTuitionFee = '<button type="button" id="btn_initial_tuition_fee_modal" class="btn btn-info btn-sm" title="Initial Tuition Fee Invoice"><i class="fas fa-file"></i></button>';
						let btnInitStudent = '<button name="btn_initial_student_data" type="button" id="btn_initial_student_data" class="btn btn-info btn-sm" title="Set student status to active"><i class="fas fa-user-graduate"></i></button>';
						let btnInitSetting = '<a href="<?=site_url('admission/candidate_setting/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Candidate settings" target="_blank"><i class="fas fa-cogs"></i></a>';
						let btn_student_record = '<a href="<?=base_url()?>student/notes/' + row['student_id'] + '" class="btn btn-sm btn-info" title="Student Notes"><i class="fas fa-quote-right"></i></a>';
						let btn_input_refferal = '<button name="btn_set_refferal" type="button" id="btn_set_refferal" class="btn btn-info btn-sm" title="Set Refferal"><i class="fas fa-user-tag"></i></button>';
						let btn_invoice = '<a href="<?=site_url('finance/invoice/lists/')?>' + row['personal_data_id'] + '" class="btn btn-info btn-sm" title="Invoice Data" target="_blank"><i class="fas fa-file-invoice-dollar"></i></a>';
						let btn_student_score = '<a href="<?=site_url('academic/score/student_score/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Student score" target="_blank"><i class="fas fa-book-open"></i></a>';
						
						var html = '<div class="btn-group" role="group" aria-label="">';
						if (('<?=$this->session->userdata('employee_id');?>' == 'dd1c9811-2f67-41b5-9cf5-b8db5f09d35e') || ('<?=$this->session->userdata('employee_id');?>' == '4e2b8186-8e7b-4726-a1f5-e280d4ac0825') || ('<?=$this->session->userdata('employee_id');?>' == '24284490-2802-4094-9675-059de314a723')) {
							html += btnProfile;
							if (row.student_status == 'candidate') {
								html += btnTuitionFee;
							}
							html += btnInitStudent;
							html += btnInitSetting;
							html += btn_invoice;
							if ('<?=$this->session->userdata('employee_id');?>' == '24284490-2802-4094-9675-059de314a723') {
								
								html += btnMessage;
								html += btn_student_record;
								// 
							}
							// if (row.student_status == 'candidate') {
							// 	html += '<button class="btn btn-sm btn-info" id="btn_approve_candidate" name="btn_approve_candidate"' +
							// 		' title="Set candidate to participant"><i class="fas fa-handshake"></i></button>';
							// }
							html += btn_student_score;
						}
						
						html += '</div>';
						return html;
					}
				}
			],
			order: [
				[7, 'desc'],
				[0, 'asc' ]
			]
		});
	};
	
	show_student_table();
	
	$('table#student_list_table tbody').on('click', 'button#btn_approve_candidate', function(e){
		e.preventDefault();
		
		let table_data = student_list_table.row($(this).parents('tr')).data();

		if (confirm("Are you sure ?")) {
			
			$.post('<?=site_url('student/set_student_status')?>', {
				student_id: table_data.student_id,
				status: 'participant'
			}, function(rtn){
				if(rtn.code == 0){
					show_student_table();
				}
			}, 'json');
		}
	});
	
	if($.fn.DataTable.isDataTable('table#student_list_table')) {
		$('table#student_list_table tbody').on('click', 'button#btn_set_refferal', function(e){
			e.preventDefault();
			
			let table_data = student_list_table.row($(this).parents('tr')).data();
			$('input#personal_data_id_refferal').val(table_data.personal_data_id);
			$('input#reffference_code_refferal').val('');

			$('#set_refferal_modal').modal('show');
		});
		
		$('table#student_list_table tbody').on('click', 'button[id="btn_initial_student_data"]', function(e){
			e.preventDefault();
			
			var table_data = student_list_table.row($(this).parents('tr')).data();
			if (table_data.student_status == 'pending') {
				if (confirm('Set student from pending to active ?')) {
					$.blockUI();
					$.post('<?=base_url()?>admission/accepted_student_active', {student_id: table_data.student_id, personal_data_id: table_data.personal_data_id}, function(result) {
						$.unblockUI();
						if (result.code == 0) {
							toastr.success('Success!');
							show_student_table();
						}
						else {
							toastr.warning(result.message, 'Warning!');
						}
					}, 'json').fail(function(params) {
						$.unblockUI();
						toastr.error('Error processing data!');
					});
				}
			}
			else {
				toastr.warning('Candidate status is must pending!');
			}
			// console.log(student_list_table.row($(this).parents('tr')).data());
		});
		
		$('table#student_list_table tbody').on('click', 'button[id="btn_display_modal"]', function(e) {
			e.preventDefault();

			var table_data = student_list_table.row($(this).parents('tr')).data();
			$('#mail_student_id').val(table_data.student_id);
			$('#mail_student').val(table_data.student_email);
			$('div#modal_send_email').modal('show');
		});
	}

	$('#submit_refference_code_refferal').on('click', function(e) {
		e.preventDefault();
		var data = $('#form_refferal_input').serialize();
		$.post('<?=base_url()?>student/set_refferenced_code', data, function(result) {
			if (result.code == 0) {
				toastr.success('Success input reffence code', 'Success');
				$('#set_refferal_modal').modal('hide');

				show_student_table();
			}else{
				toastr.warning(result.message, 'Warning!');
			}
		}, 'json').fail(function(params) {
			toastr.error('Error processing data!');
		});
	});
</script>

<div class="modal fade" tabindex="-1" role="dialog" id="new_candidate_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add new candidate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('admission/form_create_new_student')?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="compose_email_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compose Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('messaging/compose_email_form')?>
            </div>
        </div>
    </div>
</div>

<div id="modal_send_email" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email to Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_send_mail" onsubmit="return false">
                    <input type="hidden" id="mail_student_id" name="student_id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label>To</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="mail_student" id="mail_student" class="form-control" readonly="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Subject</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="mail_subject" id="mail_subject" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="mail_message" id="mail_message"></textarea>
                        <input type="hidden" name="body_email" id="body_email">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="send_mail_student" type="button" class="btn btn-primary">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="initial_tuition_fee_invoice_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Initial Tuition Fee Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?=modules::run('finance/invoice/initial_tuition_fee')?>
			</div>
		</div>
	</div>
</div>
<?=$modal_html?>
<script>
	CKEDITOR.replace('mail_message');
</script>