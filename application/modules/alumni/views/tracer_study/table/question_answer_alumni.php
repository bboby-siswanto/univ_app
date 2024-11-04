<div class="card">
	<div class="card-header"><?=$userdata->personal_data_name.' ('.$userdata->study_program_abbreviation.')'?></div>
</div>

<div class="card">
    <div class="card-header">
       Question And Answer List
	</div>
	<div class="card-body">
	    <div class="table-responsive">
			<table id="question_answer" class="table">
				<thead>
					<tr>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
<?php
if ($dikti_question) {
	// $a_input_number = ['f502', 'f503', 'f1301', 'f1302', 'f1303', 'f302', 'f303', 'f6', 'f7', 'f7a'];
	foreach ($dikti_question as $o_question) {
		
?>
				<tr id="question_<?= $o_question->question_id ;?>">
					<td> <?=$o_question->question_number;?></td>
					<td>
						<div class="row">
							<div class="col-md-6 <?=($o_question->is_required == 'TRUE') ? 'required_text' : '';?>">
								<?=trim($o_question->question_name);?>
							</div>
							<div class="col-md-6">
<?php
		if ($o_question->question_choices) {
			foreach ($o_question->question_choices as $o_choices) {
				$checked = '';
				if ($o_choices->answer_data !== NULL) {
					$checked = 'checked="true"';
				}

				$input_value = '';
				// $s_input_type = (in_array($o_choices->dikti_input_code, $a_input_number)) ? 'number' : 'text';

				if (strpos(strtolower($o_choices->question_choice_name), 'rp.') !== false) {
					$input_value = '0';
				}
				
				if ($o_question->is_multiple == 1) {
?>
								<div class="custom-control custom-checkbox pb-2">
									<input type="checkbox" class="custom-control-input" <?=$checked;?> readonly>
									<label class="custom-control-label w-100">
<?php
					$s_choices = trim($o_choices->question_choice_name);
					if ($o_choices->answer_data !== NULL) {
						$o_answer_data = $o_choices->answer_data;
						$s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
					}
					print($s_choices);
?>
									</label>
								</div>
<?php
				}else if($o_choices->has_free_text == 1){
					$s_choices = trim($o_choices->question_choice_name);
					if ($o_choices->answer_data !== NULL) {
						$o_answer_data = $o_choices->answer_data;
						$s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
					}
					print($s_choices);
				}else {
?>
								<div class="custom-control custom-radio pb-2">
									<input type="radio" class="custom-control-input" <?=$checked;?> readonly>
									<label class="custom-control-label w-100">
<?php
					$s_choices = trim($o_choices->question_choice_name);

					if ($o_choices->answer_data !== NULL) {
						$o_answer_data = $o_choices->answer_data;
						$s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
					}
					print($s_choices);
?>
									</label>
								</div>
<?php
				}
			}
		}
?>
							</div>
						</div>
					</td>
				</tr>
<?php
		if ($o_question->question_child) {
			foreach ($o_question->question_child as $o_question_child) {
?>
				<tr id="question_<?= $o_question_child->question_id ;?>">
					<td></td>
					<td>
						<div class="row">
							<div class="col-md-6 <?=($o_question_child->is_required == 'TRUE') ? 'required_text' : '';?>">
								<?=$o_question_child->question_number;?>. <?=trim($o_question_child->question_name);?>
							</div>
							<div class="col-md-6">
<?php
				if ($o_question_child->question_choices) {
					foreach ($o_question_child->question_choices as $o_choices) {
						$checked = '';
						if ($o_choices->answer_data !== NULL) {
							$checked = 'checked="true"';
						}

						if ($o_question_child->is_multiple == 1) {
		?>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" <?=$checked;?> readonly>
											<label class="custom-control-label">
		<?php
							$s_choices = trim($o_choices->question_choice_name);
							if ($o_choices->answer_data !== NULL) {
                                $o_answer_data = $o_choices->answer_data;
                                $s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
                            }
							print($s_choices);
		?>
											</label>
										</div>
		<?php
						}else if($o_choices->has_free_text == 1){
							$s_choices = trim($o_choices->question_choice_name);
							if ($o_choices->answer_data !== NULL) {
                                $o_answer_data = $o_choices->answer_data;
                                $s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
                            }
							print($s_choices);
						}else {
		?>
										<div class="custom-control custom-radio pb-2">
											<input type="radio" class="custom-control-input" <?=$checked;?> readonly>
											<label class="custom-control-label">
		<?php
							$s_choices = trim($o_choices->question_choice_name);
							if ($o_choices->answer_data !== NULL) {
                                $o_answer_data = $o_choices->answer_data;
                                $s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
                            }
							print($s_choices);
		?>
											</label>
										</div>
		<?php
						}
					}
				}
?>
							</div>
						</div>
					</td>
				</tr>
<?php
			}
		}
	}
}
?>
				</tbody>
			</table>
	    </div>
    </div>
</div>