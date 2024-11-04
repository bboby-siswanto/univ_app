<form method="POST" onsubmit="return false" id="form_input_activity_study">
    <input type="hidden" name="activity_study_id" id="input_activity_study_id">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="required_text" for="input_academic_year_id">Running Year</label>
                    <select name="academic_year_id" id="input_academic_year_id" class="form-control">
                        <option value="" disabled>Please Select!</option>
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
            <div class="col-md-3">
                <div class="form-group">
                    <label class="required_text" for="input_semester_type_id">Semester Type</label>
                    <select name="semester_type_id" id="input_semester_type_id" class="form-control">
                        <option value="" disabled>Please Select!</option>
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
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required_text" for="input_study_program_id">Study Program</label>
                    <select name="study_program_id" id="input_study_program_id" class="form-control">
                        <option value="" selected="true" disabled>Please Select!</option>
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
                    <label class="required_text" for="input_dikti_jenis_aktivitas">Type of Activities</label>
                    <select name="id_jenis_aktivitas_mahasiswa" id="input_dikti_jenis_aktivitas" class="form-control">
                        <option value="" selected="true" disabled>Please Select!</option>
        <?php
            if ($dikti_jenis_aktivitas) {
                foreach ($dikti_jenis_aktivitas as $o_jenis_aktivitas) {
        ?>
                        <option value="<?=$o_jenis_aktivitas->id_jenis_aktivitas_mahasiswa;?>"><?=$o_jenis_aktivitas->nama_jenis_aktivitas_mahasiswa;?></option>
        <?php
                }
            }
        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="required_text" for="input_activity_member_type">Group Type</label>
                    <select name="activity_member_type" id="input_activity_member_type" class="form-control">
                        <option value="0">Personal</option>
                        <option value="1">Group</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="required_text" for="input_activity_title">Title</label>
                    <input type="text" name="activity_title" id="input_activity_title" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="input_sk_number">SK Number</label>
                    <input type="text" name="activity_sk_number" id="input_sk_number" class="form-control">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="input_sk_date">SK Date</label>
                    <input type="date" class="form-control" name="activity_sk_date" id="input_sk_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="input_activity_location">Location</label>
                    <input type="text" name="activity_location" id="input_activity_location" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="input_activity_remarks">Remarks</label>
                    <input type="text" class="form-control" name="activity_remarks" id="input_activity_remarks ">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="float-right">
                    <button id="btn_submit_activity_study" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>