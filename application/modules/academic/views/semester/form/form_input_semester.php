<form id="form_input_semester_settings" onsubmit="return false" id="form_semester_table">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="required_text">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-control">
                    <option value="">Please select...</option>
            <?php
            if ($academic_year_lists) {
                foreach ($academic_year_lists as $year) {
            ?>
                    <option value="<?= $year->academic_year_id;?>"><?= $year->academic_year_id;?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="required_text">Semester Type</label>
                <select name="semester_type_id" id="semester_type_id" class="form-control">
                    <option value="">Please select...</option>
            <?php
            if ($semester_type_lists) {
                foreach ($semester_type_lists as $semester_type) {
            ?>
                    <option value="<?=$semester_type->semester_type_id;?>"><?= $semester_type->semester_type_name;?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label class="required_text">Semester Period</label>
                <div class="input-group">
                    <input type="text" id="semester_start_date" name="semester_start_date" class="form-control" placeholder="Start Date">
                    <div class="input-group-append">
                        <span class="input-group-text">to</span>
                    </div>
                    <input type="text" id="semester_end_date" name="semester_end_date" class="form-control" placeholder="End Date">
                </div>
            </div>
        </div>
    </div>
</form>