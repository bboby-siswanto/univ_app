<?= modules::run('academic/activity_study/form_filter_activity_study'); ?>
<div class="card">
    <div class="card-header">
        Activity Study List
        <div class="card-header-actions">
            <button id="btn_new_activity" class="btn btn-link card-header-action" class="card-header-action btn btn-link" data-toggle="modal" title="Add New" data-target="#form_input_activity_study_modal">
                <i class="fas fa-plus-circle"></i> Add New
            </button>
            <!-- <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                <button id="btn_blast_mail" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Send Email to All Student Selected">
                    <i class="fas fa-mail-bulk"></i> Add New
                </button>
            </div> -->
        </div>
    </div>
    <div class="card-body">
        <?= modules::run('academic/activity_study/activity_study_table_list'); ?>
    </div>
</div>
<?php
$s_form_input_activity_study = modules::run('academic/activity_study/form_input_activity_study');
$modal_input_activity_study = modules::run('layout/compose_modal', array(
    'modal_title' => 'Form Input Activity Study',
    'modal_body' => $s_form_input_activity_study
));
print($modal_input_activity_study);
?>
<script>
    $(function() {
        $('#btn_new_activity').on('click', function(e) {
            e.preventDefault();

            $('form#form_input_activity_study').find('input').val('');
        });
    });
</script>