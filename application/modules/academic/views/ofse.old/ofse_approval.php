<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">OFSE Filter</div>
            <div class="card-body">
                <?= modules::run('academic/ofse/form_filter_ofse') ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                Subjects
                <div class="card-header-actions">
                    <a class="card-header-action" href="#" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                        <i class="fa fa-cog"></i> Quick Actions
                    </a>
                    <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                        <button type="button" id="download_subject_student_ofse" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download Student Data
                        </button>
                        <button type="button" id="download_subject_examiner" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download Subject and Examiner
                        </button>
                        <button type="button" id="download_ofse_registration_structure" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download Structure of OFSE Registration
                        </button>
                        <button type="button" id="download_ofse_result" class="btn btn-link dropdown-item">
                            <i class="fa fa-download"></i> Download OFSE Result
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= modules::run('ofse/ofse_participant_table') ?>
            </div>
        </div>
    </div>
</div>

<script>
	
</script>