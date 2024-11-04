<div class="card">
    <div class="card-header">Filter Data</div>
    <div class="card-body">
        <form method="post" id="form_filter_class_group">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Running Year</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-control">
                            <option value="">Please Select...</option>
<?php
    $s_academic_year_id_active = ($this->session->userdata('academic_year_id_active') !== null) ? $this->session->userdata('academic_year_id_active') : '';
    foreach ($mbo_academic_year as $running_year) {
        $selected = ($s_academic_year_id_active == $running_year->academic_year_id) ? 'selected' : '';
?>
                            <option value="<?= $running_year->academic_year_id;?>" <?= $selected; ?>><?= $running_year->academic_year_id.'-'.(intval($running_year->academic_year_id) + 1);?></option>
<?php
    }
?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Semester Type</label>
                        <select name="semester_type_id" id="semester_type_id_search" class="form-control">
                            <option value="">Please Select...</option>
<?php
    $s_semester_type_id_active = ($this->session->userdata('semester_type_id_active') !== null) ? $this->session->userdata('semester_type_id_active') : '';
    foreach ($mbo_semester_type as $semester_type) {
        if ($semester_type->semester_type_id != '3') {
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
                <div class="col-sm-12">
                    <button class="btn btn-info float-right" type="submit">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>