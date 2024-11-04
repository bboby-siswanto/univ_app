<?php
	$s_student_id = false;
	if (isset($student_data)) {
		$s_student_id = $student_data->student_id;
	}
	print modules::run('student/show_name', $s_student_id);

	if (in_array($this->session->userdata('type'), array('student', 'alumni'))) {
	?>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">
					Your SGS Code: <strong><?=(is_null($personal_data->personal_data_reference_code) ? 'N/A': $personal_data->personal_data_reference_code)?></strong>
				</div>
			</div>
		</div>
	</div>
	<?php
	}
?>
<div class="row personal_data">
	<div class="col-md-6">
		<?=modules::run('personal_data/form_personal_data', $personal_data_id)?>
	</div>
	<div class="col-md-6">
		<?=modules::run('personal_data/form_address', $personal_data_id)?>
<!-- 		<button class="btn btn-block btn-primary btn-lg" type="button" id="btn_save_all_personal_data">Save</button> -->
	</div>
</div>
<!--
<script>
	$(function() {
		$('#btn_save_all_personal_data').on('click', function(e) {
			e.preventDefault();
			$.blockUI({
				message: 'Saving data...'
			});
			
			save_personal_data().then((spd_res) => {
				if(spd_res.code == 0){
					save_address_data().then((sad_res) => {
						$.unblockUI();
						if(sad_res.code != 0){
							toastr['warning'](sad_res.message, 'Warning!');
						}
					}).catch((err) => {
						console.log(err);
					});
				}
				else{
					$.unblockUI();
					toastr['warning'](spd_res.message, 'Warning!');
				}
			}).catch((err) => {
				console.log(res);
			});
			// $('button#save_personal_data').click();
			// $('button#save_address_data').click();
			return false;
		});
	});
</script>
-->