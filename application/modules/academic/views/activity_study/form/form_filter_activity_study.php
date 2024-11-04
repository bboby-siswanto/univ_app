<div class="card">
    <div class="card-header">Filter Data</div>
    <div class="card-body">
        <form id="activity_study_filter_form">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter_academic_year_id">Academic Year</label>
                                <select name="academic_year_id" id="filter_academic_year_id" class="form-control">
                    <?php
                        $s_academic_year_id_active = ($this->session->userdata('academic_year_id_active') !== null) ? $this->session->userdata('academic_year_id_active') : '';
                        if ($academic_year_list) {
                            foreach ($academic_year_list as $running_year) {
                                $selected = ($s_academic_year_id_active == $running_year->academic_year_id) ? 'selected' : '';
                    ?>
                                    <option value="<?= $running_year->academic_year_id;?>" <?= $selected; ?>><?= $running_year->academic_year_id.'-'.(intval($running_year->academic_year_id) + 1);?></option>
                    <?php
                            }
                        }
                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter_study_program_id">Study Program</label>
                                <select name="study_program_id" id="filter_study_program_id" class="form-control">
                                    <option value="all">All</option>
                    <?php
                        if ($study_program_list) {
                            foreach ($study_program_list as $o_study_program) {
                    ?>
                                    <option value="<?=$o_study_program->study_program_id;?>"><?=$o_study_program->faculty_name;?> - <?=$o_study_program->study_program_name;?></option>
                    <?php
                            }
                        }
                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter_semester_type_id">Semester Type</label>
                                <select name="semester_type_id" id="filter_semester_type_id" class="form-control">
                    <?php
                        $s_semester_type_id_active = ($this->session->userdata('semester_type_id_active') !== null) ? $this->session->userdata('semester_type_id_active') : '';
                        if ($semester_type_list) {
                            foreach ($semester_type_list as $semester_type) {
                                $selected = ($s_semester_type_id_active == $semester_type->semester_type_id) ? 'selected' : '';
                    ?>
                                    <option value="<?= $semester_type->semester_type_id;?>" <?= $selected; ?>><?= $semester_type->semester_type_name;?></option>
                    <?php
                            }
                        }
                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-info float-right" type="button" id="btn_filter_activity_study">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>