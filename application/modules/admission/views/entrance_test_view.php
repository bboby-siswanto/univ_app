<div class="card">
    <div class="card-header">
        Period Entrance Test Online
        <div class="card-header-actions">
        <a class="card-header-action" href="#" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
				<i class="fa fa-gear"></i> Quick Actions
			</a>
			<div class="dropdown-menu" aria-labelledby="settings_dropdown">
				<a class="dropdown-item" href="#" data-toggle="modal" data-target="#input_entrance_test" aria-expanded="true">
					<i class="fa fa-plus"></i> Entrance Test
				</a>
				<a class="dropdown-item" href="<?=base_url()?>admission/entrance_test/question_list">
					<i class="fa fa-university"></i> Question Bank
				</a>
			</div>
        </div>
    </div>
    <div class="card-body">
        <?= modules::run('admission/entrance_test/period_table') ?>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="input_entrance_test">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Entrance Test</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('admission/entrance_test/input_exam');?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_exam">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>