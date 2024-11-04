<?php //(isset($student_data)) ? modules::run('student/show_name', $student_data->student_id) : '' ?>
<?php
print modules::run('student/show_name');
if(($mba_student_data) AND (in_array($mba_student_data[0]->student_status, array('candidate', 'participant')))){
?>
<div class="card">
	<div class="card-header">
		Study Program
	</div>
	<div class="card-body">
		<div class="row">
			<form method="post" id="change_study_program" action="<?=site_url('student/change_study_program')?>">
				<input type="hidden" name="student_id" value="<?=$mba_student_data[0]->student_id?>">
				<div class="col-sm-12">
					<label>Study Program</label>
					<select name="study_program_id" id="study_program_id" class="form-control">
						<option>Please select...</option>
						<?php
						foreach($a_study_program as $study_program){
							$s_selected = '';
							if($study_program->study_program_id == $mba_student_data[0]->study_program_id){
								$s_selected = 'selected';
							}
						?>
						<option value="<?=$study_program->study_program_id?>" <?=$s_selected?>><?=$study_program->faculty_name?> - <?=$study_program->study_program_name?></option>
						<?php
						}	
						?>
					</select>
				</div>
				<br>
				<div class="col-sm-12">
					<button class="btn btn-primary" id="btn_save_study_program">Save</button>
				</div>
			</form>
			<script>
				$('button#btn_save_study_program').on('click', function(e){
					e.preventDefault();
					var form = $('form#change_study_program');
					$.post(form.attr('action'), form.serialize(), function(rtn){
						if(rtn.code == 0){
							toastr['success']('Your data has been saved', 'Success!');
						}
						else{
							toastr['warning'](rtn.message, 'Warning!');
						}
					}, 'json');
				});
			</script>
		</div>
	</div>
</div>
<?php
}
?>

<div class="row">
    <?=modules::run('personal_data/academic/view_list_academic_history', $personal_data_id)?>
</div>