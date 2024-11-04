<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                Job Vacancy Lists
                <div class="card-header-actions">
                    <div class="card-header-actions">
                        <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                            <i class="fas fa-sliders-h"></i> Quick Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                            <button id="btn_add_job_vacancy" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Add New Job Vacancy">
                                <i class="fas fa-plus-square"></i> Add Job Vacancy
                            </button>
                            <a href="<?=base_url()?>alumni/vacancy/my_vacancies" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="View My Job Vacancy">
                                <i class="fas fa-chalkboard-teacher"></i> View My Job Vacancy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= modules::run('alumni/vacancy/vacancy_lists'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_new_job_vacancy">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add new job vacancy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_input_academic_history">
                <?= modules::run('alumni/vacancy/form_input_vacancy'); ?>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('button#btn_add_job_vacancy').on('click', function(e) {
        e.preventDefault();
        $('div#modal_new_job_vacancy').modal('show');
    });
})
</script>