<div class="row">
	<div class="col-md-12">
        <div class="card">
            <div class="card-header"><?= $o_curriculum_data->curriculum_name;?></div>
        </div>
    </div>
	<div class="col-md-12">
		<?=modules::run('academic/curriculum/form_curriculum_subject_filter', $s_semester_id, $s_curriculum_id)?>
	</div>
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				Curriculum Subject Lists
				<div class="card-header-actions">
					<button class="card-header-action btn btn-link" id="btn_new_curriculum">
						<i class="fa fa-plus"></i> Subject
					</button>
				</div>
			</div>
			<div class="card-body">
				<?=modules::run('academic/curriculum/view_table_curriculum_subject', $s_curriculum_id, $s_semester_id)?>
			</div>
		</div>
		<div class="modal fade" tabindex="-1" role="dialog" id="new_curriculum_modal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title title-modal">Add new curriculum subject</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="modal_input_curriculum">
						<?=modules::run('academic/curriculum/form_create_curriculum_subject', $curriculum_id);?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
