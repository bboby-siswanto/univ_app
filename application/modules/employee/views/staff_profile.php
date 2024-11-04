<?php
print modules::run('hris/show_name');
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
<div class="row personal_data">
	<div class="col-md-6">
		<?=modules::run('personal_data/form_personal_data', $personal_data_id)?>
	</div>
	<div class="col-md-6">
		<?=modules::run('personal_data/form_address', $personal_data_id)?>
	</div>
</div>