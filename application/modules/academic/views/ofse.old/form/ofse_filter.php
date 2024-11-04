<form id="form_ofse_filter" onsubmit="return false">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-control">
                    <option value="">Please select...</option>
            <?php
            if ($mbo_academic_year) {
                foreach ($mbo_academic_year as $academic_year) {
            ?>
                    <option value="<?=$academic_year->academic_year_id?>"><?= $academic_year->academic_year_id.'-'.(intval($academic_year->academic_year_id)+1); ?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Semester Type</label>
                <select name="semester_type_id" id="semester_type_id" class="form-control">
                    <option value="">Please select...</option>
            <?php
            if ($mbo_semester_type) {
                foreach ($mbo_semester_type as $semester_type) {
            ?>
                    <option value="<?=$semester_type->semester_type_id?>"><?= $semester_type->semester_type_name?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Study Program</label>
                <select name="study_program_id" id="study_program_id" class="form-control">
                    <option value="">Please select...</option>
                    <option value="All">All</option>
            <?php
            if ($mba_study_program) {
                foreach ($mba_study_program as $study_program) {
            ?>
                    <option value="<?=$study_program->study_program_id?>"><?=$study_program->faculty_name?> - <?=$study_program->study_program_name?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="button" id="filter_ofse" class="btn btn-primary float-right">Filter</button>
        </div>
    </div>
</form>