<div class="modal" tabindex="-1" role="dialog" id="fee_details_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Payment Type</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="form" id="payment_code_form" action="<?=site_url('finance/fee/save_fee_data')?>">
					<input type="hidden" name="fee_id" id="fee_id">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>Fee Type</label>
								<select class="form-control" id="fee_amount_type" name="fee_amount_type">
									<option value="main">Main</option>
									<option value="additional">Additional</option>
								</select>
							</div>
						</div>
						<div class="col-md-10">
							<div class="form-group">
								<label>Fee Description</label>
								<input class="form-control" id="fee_description" name="fee_description" type="text">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-10">
							<div class="form-group">
								<label>Alt Fee Description</label>
								<input class="form-control" id="fee_alt_description" name="fee_alt_description" type="text">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>Operator</label>
								<select id="fee_amount_sign_type" name="fee_amount_sign_type" class="form-control">
									<option value="positive">+</option>
									<option value="negative">-</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Entry Year</label>
								<select class="form-control" id="academic_year_id" name="academic_year_id">
									<option value="">Not applicable</option>
									<?php
									foreach($academic_year as $ay){
									?>
									<option value="<?=$ay->academic_year_id?>" <?=($ay->academic_year_intake_status == 'active') ? 'selected' : ''?>><?=$ay->academic_year_id?></option>
									<?php
									}	
									?>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Number/Percentage</label>
								<select id="fee_amount_number_type" name="fee_amount_number_type" class="form-control">
									<option value="number">Rp.</option>
									<option value="percentage">%</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Amount</label>
								<input class="form-control" id="fee_amount" name="fee_amount" type="text">
							</div>
						</div>
					</div>
					<div class="row main-fee-container">
						<div class="col-md-6">
							<div class="form-group">
								<label>Payment Type Code</label>
								<select id="payment_type_code" name="payment_type_code" class="form-control">
									<option value="">Not applicable</option>
									<?php
									foreach($payment_type as $type){
									?>
									<option value="<?=$type->payment_type_code?>"><?=$type->payment_type_name?> - <?=$type->payment_type_code?></option>
									<?php
									}	
									?>
								</select>
							</div>
						</div>
						<div class="col">
							<div class="form-group">
								<label>Institute</label>
								<select class="form-control" id="program_id" name="program_id">
									<option value="">Not applicable</option>
									<?php
									foreach($programs as $program){
									?>
									<option value="<?=$program->program_id?>"><?=(!is_null($program->type_of_admission_code) ? $program->type_of_admission_code : $program->program_code)?></option>
									<?php
									}	
									?>
								</select>
							</div>
						</div>
						
					</div>
					<div class="row main-fee-container">
						<div class="col">
							<div class="form-group">
								<label>Scholarship</label>
								<select class="form-control" id="scholarship_id" name="scholarship_id">
									<option value="">Not applicable</option>
									<?php
									foreach($scholarship as $sch){
									?>
									<option value="<?=$sch->scholarship_id?>"><?=$sch->scholarship_name?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Study Program</label>
								<select class="form-control" id="study_program_id" name="study_program_id">
									<option value="">Not applicable</option>
									<?php
									foreach($study_programs as $sp){
									?>
									<option value="<?=$sp->study_program_id?>"><?=$sp->study_program_abbreviation?></option>
									<?php
									}	
									?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Semester</label>
								<select class="form-control" id="semester_id" name="semester_id">
									<option value="">Not applicable</option>
									<?php
									foreach($semester as $sem){
									?>
									<option value="<?=$sem->semester_id?>"><?=$sem->semester_number?></option>
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
				<button class="btn btn-md btn-info float-right" data-dismiss="modal" aria-label="Close">Close</button>
				<button class="btn btn-md btn-info float-right" id="btn_save_payment_type">Create/Save changes</button>
			</div>
		</div>
	</div>
</div>



<div class="card">
    <div class="card-header">
        Fee List
        <div class="card-header-actions">
			<button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
				<i class="fa fa-plus"></i> Fee
            </button>
			<div class="dropdown-menu" aria-labelledby="settings_dropdown">
				<a class="dropdown-item card-header-action btn btn-link" href="#" data-toggle="modal" data-target="#fee_details_modal" aria-expanded="true">
					<i class="fa fa-file"></i> Single Fee
				</a>
				<a class="dropdown-item card-header-action btn btn-link" href="#" data-toggle="modal" data-target="#modal_bulk_fee" aria-expanded="true">
					<i class="fa fa-file-import"></i> Upload Bulk Fee
				</a>
			</div>
		</div>
    </div>
    <div class="card-body">
	    <form class="form mb-5">
		    <div class="form-group">
			    <label>Select Entry Year</label>
			    <select class="form-control" id="fee_academic_year" name="fee_academic_year">
				    <option>Please select...</option>
				    <?php
					foreach($academic_year as $ay){
					?>
					<option value="<?=$ay->academic_year_id?>" <?=($ay->academic_year_intake_status == 'active') ? 'selected' : ''?>><?=$ay->academic_year_id?></option>
					<?php
					}	
					?>
			    </select>
		    </div>
	    </form>
	    <div class="table-responsive">
			<table id="fee_list_table" class="table table-striped table-bordered">
				<thead class="bg-dark">
					<tr>
						<th>Date Added</th>
						<th>Fee Description</th>
						<th>Study Program</th>
						
						<th>Semester</th>
						<th>Scholarship</th>
						<th>Amount</th>
						<th>Actions</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_bulk_fee">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Upload Bulk Fee</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card">
					<div class="card-body">
						<h5>1. Download Template</h5>
						<hr>
						<div class="row">
							<div class="col-4">
								<div class="form-group">
									<label for="fee_bulk_student_batch" class="required_text">Student Batch</label>
									<select name="fee_bulk_student_batch" id="fee_bulk_student_batch" class="form-control">
							<?php
							if ((isset($academic_year)) AND ($academic_year)) {
								foreach ($academic_year as $o_year) {
							?>
										<option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?>/<?=intval($o_year->academic_year_id) + 1;?></option>
							<?php
								}
							}
							?>
									</select>
								</div>
							</div>
							<div class="col-4">
								<div class="form-group">
									<label for="fee_bulk_class_program" class="required_text">Class Program</label>
									<select name="fee_bulk_class_program" id="fee_bulk_class_program" class="form-control">
										<option value="1">Regular Class / GII</option>
										<option value="2">Employee Class / EXP</option>
									</select>
								</div>
							</div>
							<div class="col-4">
								<div class="form-group">
									<label for="fee_bulk_payment_type" class="required_text">Payment Type</label>
									<select name="fee_bulk_payment_type" id="fee_bulk_payment_type" class="form-control">
										<option value="02">Tuition Fee</option>
										<option value="04">Short Semester Fee</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<button id="btn_download_template_bulk_fee" class="btn btn-info float-right" type="button">Download Template</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
						<h5>2. Upload Template</h5>
						<hr>
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>File</label>
									<div class="input-group">
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="fee_bulk_file_template" accept=".xlsx">
											<label class="custom-file-label" for="fee_bulk_file_template">Choose file</label>
										</div>
										<div class="input-group-append">
											<button class="btn btn-info" type="button" id="btn_upload_bulk_file">Execute</button>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<span id="result_log"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-primary">Save changes</button> -->
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	var payment_code_form = $('form#payment_code_form');

	$('#fee_amount').number( true, 0 );
	
	let academic_year_id = <?=$active_year->academic_year_id?>;
	fee_list_table = $('table#fee_list_table').DataTable({
		processing: true,
		ajax: {
			url: '<?=site_url('finance/fee/get_all_fee')?>',
			method: 'POST',
			data: function(d){
				d.academic_year_id = $('select#fee_academic_year').val()
			}
		},
		columns: [
			{ data: 'date_added', visible: false },
			{ 
				data: 'fee_description',
				render: function(data, type, row) {
					if (row['fee_alt_description'] !== null) {
						data += '<br><small>(' + row['fee_alt_description'] + ')</small>';
					}
					
					return data + '<input type="hidden" data-alt="fee_id" value="' + row['fee_id'] + '">';
				}
			},
			{ data: 'study_program_name', defaultContent: '-' },
			{
				data: 'semester_number',
				defaultContent: '-',
				type: 'html-num-fmt',
				render: function(data, type, row) {
					console.log(data);
					if (data !== null) {
						if (data.indexOf('.') == -1) {
							return $.number(data);
						}
						else {
							return $.number(data, 1);
						}
					}
					else {
						return '-';
					}
				}
			},
			{ data: 'scholarship_name', defaultContent: '-' },
			{ 
				data: 'fee_amount',
				defaultContent: '-',
				render: function(data, type, row){
					let operator_sign = '';
					if(row['fee_amount_sign_type'] == 'negative'){
						operator_sign = '- ';
					}
					
					let display_amount = '';
					if(row['fee_amount_number_type'] == 'number'){
						display_amount = formatter.format(data);
					}
					else{
						display_amount = data + '%';
					}
					
					return operator_sign + display_amount;
				}
			},
			{
				data: 'fee_id',
				render: function(data, type, row){
					var html = '<div class="btn-group btn-group-sm">';
					html += '<button id="edit_item" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>';

					if ('<?=$this->session->userdata("user")?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
						html += '<button id="copy_item" class="btn btn-sm btn-info"><i class="fa fa-copy"></i></button>';
					}
					html += '</div>';
					return html;
				}
			}
		],
		order: [
			[0, 'desc']
		]
	});
	
	$('select#fee_academic_year').on('change', function(e){
		e.preventDefault();
		academic_year_id = $(this).val();
		fee_list_table.ajax.reload();
	});
/*
	function load_fee_list_table(academic_year_id = null){
		if($.fn.dataTable.isDataTable('table#fee_list_table')){
			fee_list_table.destroy();
		}
		
		
	}
*/
	// load_fee_list_table(<?=$active_year->academic_year_id?>);
	
	$('table#fee_list_table tbody').on('click', 'button#edit_item', function(e){
		e.preventDefault();
		var data = fee_list_table.row($(this).parents('tr')).data();
		// console.log(data);
		$.each(payment_code_form[0], function(k, v){
			var el_id = $(this).attr('id');
			$('#' + el_id).val(data[el_id]);
		});
		$('div#fee_details_modal').modal('show');
		change_view(data['fee_amount_type']);
	});
	
	$('table#fee_list_table tbody').on('click', 'button#copy_item', function(e){
		e.preventDefault();
		var data = fee_list_table.row($(this).parents('tr')).data();
		// console.log(data);
		$.each(payment_code_form[0], function(k, v){
			var el_id = $(this).attr('id');
			$('#' + el_id).val(data[el_id]);
		});
		$('input#fee_id').val('');
		$('div#fee_details_modal').modal('show');
		change_view(data['fee_amount_type']);
	});
	
	$('button#btn_save_payment_type').on('click', function(e){
		e.preventDefault();
		let url = payment_code_form.attr('action');
		let data = payment_code_form.serialize();
		
		$.post(url, data, function(rtn){
			// console.log(rtn);
			if(rtn.code == 0){
				$('div#fee_details_modal').modal('hide');
				academic_year_id = rtn.academic_year_id;
				// load_fee_list_table(<?=$active_year->academic_year_id?>);
				fee_list_table.ajax.reload(null, false);
			}
			else{
				toastr.warning(rtn.message, 'Warning!');
			}
		}, 'json').fail(function(params) {
			toastr.error('Error processing data!', 'Error');
		});
		// console.log(payment_code_form.serialize());
		
	});

	// $('input#fee_description').on('keypress', function() {
	// 	$('button#btn_save_payment_type').trigger('click');
	// });
	
	$('div#fee_details_modal').on('hidden.bs.modal', function(e){
		e.preventDefault();
		payment_code_form[0].reset();
		$('input#fee_id').val('');
	});

	// $('#modal_bulk_fee').modal('show');
	
	function change_view(value){
		if(value === 'main'){
			$('div.main-fee-container').show(800);
		}
		else{
			$('div.main-fee-container').hide(500);
		}
	}
	
	$('select#fee_amount_type').on('change', function(e){
		academic_year_id = $(this).val();
		fee_list_table.ajax.reload();
	});

	$('button#btn_download_template_bulk_fee').on('click',  function(e) {
		e.preventDefault();

		var batch = $('#fee_bulk_student_batch').val();
		var classtype = $('#fee_bulk_class_program').val();
		var payment_type = $('#fee_bulk_payment_type').val();

		if ((batch != '') && (classtype != '') && (payment_type != '')) {
			window.location.href = '<?=base_url()?>finance/fee/download_excel_template/' + batch + '/' + classtype + '/' + payment_type;	
		}
	});

	$('button#btn_upload_bulk_file').on('click', function(e) {
		e.preventDefault();

		$.blockUI({baseZ: 9000});
		var formData = new FormData();
		formData.append('filefee', $('input#fee_bulk_file_template')[0].files[0]);

		var url = '<?= base_url()?>finance/fee/upload_excel_fee';
		$.ajax({
			url: url,
			type: 'POST',
			data: formData,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			success: function(rtn, status, jqXHR){
				if(rtn.code == 0){
					$.unblockUI();
					fee_list_table.ajax.reload();
					$('span#result_log').html(rtn.log_result);
					// $('#modal_bulk_fee').modal('hide');
				}else{
					$.unblockUI();
					toastr.warning(rtn.message);
				}
			},
		}).fail(function(xhr, textStatus, errorThrown) {
			$.unblockUI();
			toastr.error("Sorry, there was an interruption in our system, and the document was not sent to system");
		})
		// .failfunction(xhr, textStatus, errorThrown){
		// 		$.unblockUI();
		// 		toastr.error("Sorry, there was an interruption in our system, and the document was not sent to system");
		// 	};
	})
</script>