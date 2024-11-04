<form id="student_init_basic_data" name="student_init_basic_data" method="POST" action="<?=site_url('student/init_student_data')?>">
	<div class="form-group">
		<input type="hidden" id="student_id" name="student_id">
		<input type="hidden" id="personal_data_id" name="personal_data_id">
		<label>Name</label>
		<input type="text" id="personal_data_name" name="personal_data_name" class="form-control" disabled>
	</div>
	<div class="form-group">
		<label>Gender</label>
		<select id="personal_data_gender" name="personal_data_gender" class="form-control" disabled>
			<option value="M">Male</option>
			<option value="F">Female</option>
		</select>
	</div>
	<div class="form-group">
		<label>NIK</label>
		<input type="text" id="personal_data_id_card_number" name="personal_data_id_card_name" class="form-control" disabled>
	</div>
	<div class="form-group">
		<label>Date of birth</label>
		<input type="text" id="personal_data_dob" name="personal_data_dob" class="form-control" disabled>
	</div>
	<div class="pt-2">
		<p>By submitting this form:</p>
		<ol>
			<li>You agreed that the data provided is <storng>VALID</storng> and will be under your responsibility until the student logged in by himself/herself <strong>AS A STUDENT</strong>.</li>
			<li>You have cross-checked the following references:
				<ol>
					<li>The candidate <strong>received IULI's scholarship with a 100% discounts</strong>.</li>
					<li>IULI has <strong>LEGALLY APPROVED</strong> this candidate to study within this running semester.</li>
				</ol>
			</li>
			<li>You have cross-checked the hard copy data is the same as this form.</li>
			<li>You acknowledge that once you submit this data, the following events will occur:
				<ol>
					<li>The system by-passes the candidate's invoice</li>
					<li>The system <strong>WILL GENERATE</strong> the candidate's IULI email address and will use <strong>HIS BIRTH DATE</strong> as the default password</li>
				</ol>
			</li>
		</ol>
	</div>
	<div class="form-group">
		<input type="checkbox" id="acknowledge" name="acknowledge" value="acknowledged">
		<label>I have read the terms above and agreed to proceed</label>
	</div>
	<button class="btn btn-info btn-block disabled" id="btn_proceed_init_data" disabled>
		<i class="fa fa-save"></i> Save
	</button>
</form>

<script>
	$('input#acknowledge').on('change', function(e){
		e.preventDefault();
		let val = $(this).is(':checked');
		if(val){
			$('button#btn_proceed_init_data').attr('disabled', false).removeClass('disabled');
		}
		else{
			$('button#btn_proceed_init_data').attr('disabled', true).addClass('disabled');
		}
	});
	
	$('button#btn_proceed_init_data').on('click', function(e){
		e.preventDefault();
		let form = $('form#student_init_basic_data');
		let data = form.serialize();
		let url = form.attr('action');
		
		$.post(url, data, function(rtn){
			toastr['success']('This student has been marked as "ACTIVE"', 'Success!');
			if($.fn.DataTable.isDataTable('table#student_list_table')){
				student_list_table.ajax.reload();
			}
		}, 'json');
	});
	
	if($.fn.DataTable.isDataTable('table#student_list_table')){
		$('table#student_list_table tbody').on('click', 'button#btn_initial_student_data', function(e){
			e.preventDefault();
			var data = student_list_table.row($(this).parents('tr')).data();
			if(data['student_status'] == 'pending'){
				$('input#student_id').val(data['student_id']);
				$('input#personal_data_id').val(data['personal_data_id']);
				$('input#personal_data_name').val(data['personal_data_name']);
				$('input#personal_data_gender').val(data['personal_data_gender']);
				$('input#personal_data_id_card_number').val(data['personal_data_id_card_number']);
				$('input#personal_data_dob').val(data['personal_data_date_of_birth']);
				
				$('div#set_student_status_to_active_modal').modal('toggle');
			}
			else{
				toastr['warning']('Student status is not a "pending student"', 'Warning!');
			}
		});

		$('table#student_list_table tbody').on('click', 'button#btn_approve_candidate', function(e){
			e.preventDefault();
			var data_table = student_list_table.row($(this).parents('tr')).data();
			if (confirm("Are you sure to set status " + data_table['personal_data_name'] + " to participant?")) {
				$.blockUI();
				var data = {student_id: data_table['student_id'], status: 'participant'};
				$.post('<?= base_url()?>student/set_student_status', data, function(result) {
					$.unblockUI();
					if (result.code == 0) {
						toastr.success('Success change ' + data_table['personal_data_name'] + ' status', 'Success!');
						student_list_table.ajax.reload(null, false);
					}else{
						toastr.warning(result.message, 'Warning!');
					}
				}, 'json').fail(function(params) {
					toastr.warning('error processing data', 'Warning!');
					$.unblockUI();
				});
			}
		});
	}
	
	
</script>