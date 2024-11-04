<div class="card">
    <div class="card-header">Filter Data</div>
    <div class="card-body">
        <form method="post" id="curriculum_subject_filter_form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Semester</label>
                        <select name="semester_id" id="semester_id" class="form-control">
                            <option value="All">All</option>
                    <?php
                        foreach($o_semester_data as $semester) {
                    ?>
                            <option value="<?= $semester->semester_id;?>" <?= (($s_semester_id) AND ($s_semester_id == $semester->semester_id)) ? 'selected' : ''; ?>><?= $semester->semester_name; ?></option>
                    <?php
                        }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" id="btn_filter_curriculum_subject" class="btn btn-primary float-right">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>