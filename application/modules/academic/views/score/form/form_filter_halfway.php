<div class="col-md-6">
    <label>Semester Starts:</label>
    <div class="input-group">
        <select name="academic_year_start" id="halfway_academic_year_start" class="custom-select w-75">
            <option value="">Please select...</option>
    <?php
    if ($academic_year_list) {
        foreach ($academic_year_list as $year) {
    ?>
            <option value="<?=$year->academic_year_id?>"><?=$year->academic_year_id?></option>
    <?php
        }
    }
    ?>
        </select>
        <select name="semester_type_start" id="halfway_semester_type_start" class="custom-select w-25">
            <option value="">Please select...</option>
    <?php
    if ($semester_type_list) {
        foreach ($semester_type_list as $semester) {
    ?>
            <option value="<?=$semester->semester_type_id?>" <?= ($semester->semester_type_id == 1) ? 'selected' : ''; ?>><?=$semester->semester_type_name?></option>
    <?php
        }
    }
    ?>
        </select>
    </div>
</div>
<div class="col-md-6">
    <label>Semester End: </label>
    <div class="input-group">
        <select name="academic_year_end" id="halfway_academic_year_end" class="custom-select w-75">
            <option value="">Please select...</option>
    <?php
    if ($academic_year_list) {
        // $s_academic_year_last_score = ($student_last_score) ? $student_last_score->academic_year_id : '';
        $s_academic_year_last_score = $this->session->userdata('academic_year_id_active');
        foreach ($academic_year_list as $year) {
    ?>
            <option value="<?=$year->academic_year_id?>" <?= ($year->academic_year_id == $s_academic_year_last_score) ? 'selected' : ''; ?>><?=$year->academic_year_id?></option>
    <?php
        }
    }
    ?>
        </select>
        <select name="semester_type_end" id="halfway_semester_type_end" class="custom-select w-25">
            <option value="">Please select...</option>
    <?php
    if ($semester_type_list) {
        // $s_semester_type_last_score = ($student_last_score) ? $student_last_score->semester_type_id : '';
        $s_semester_type_last_score = $this->session->userdata('semester_type_id_active');
        foreach ($semester_type_list as $semester) {
    ?>
            <option value="<?=$semester->semester_type_id?>" <?= ($semester->semester_type_id == $s_semester_type_last_score) ? 'selected' : ''; ?>><?=$semester->semester_type_name?></option>
    <?php
        }
    }
    ?>
        </select>
    </div>
</div>