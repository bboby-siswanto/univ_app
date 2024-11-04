<div class="row">
	<div class="col-md-12">
        <div class="card">
            <div class="card-header"><?= $o_curriculum_data->curriculum_name;?></div>
        </div>
    </div>
	<div class="col-md-12">
        <?=modules::run('academic/curriculum/view_table_curriculum_semester', $s_curriculum_id)?>
	</div>
</div>
