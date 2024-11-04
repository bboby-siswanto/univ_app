<form id="form_filter_score" onsubmit="return false">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Academic Year</label>
                <select name="academic_year_id" id="filter_academic_year_id" class="form-control">
                    <option value="">Please select...</option>
                    <option value="all">All</option>
            <?php
            if ($academic_year_lists) {
                foreach ($academic_year_lists as $academic_year) {
                    $selected = (($this->session->has_userdata('academic_year_id_active')) AND ($this->session->userdata('academic_year_id_active') == $academic_year->academic_year_id)) ? 'selected="selected"' : '';
            ?>
                    <option value="<?= $academic_year->academic_year_id;?>" <?=$selected;?>><?= $academic_year->academic_year_id;?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Semester Type</label>
                <select name="semester_type_id" id="filter_semester_type_id" class="form-control">
                    <option value="">Please select...</option>
        <?php
            if ($semester_type_lists) {
                foreach ($semester_type_lists as $semester) {
                    $selected = (($this->session->has_userdata('semester_type_id_active')) AND ($this->session->userdata('semester_type_id_active') == $semester->semester_type_id)) ? 'selected="selected"' : '';
                    $semester_name = ($semester->semester_type_id == 4) ? 'OFSE' : $semester->semester_type_name;
        ?>
                    <option value="<?= $semester->semester_type_id;?>" <?=$selected;?>><?= $semester_name;?></option>
        <?php
                }
            }
        ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <button id="filter_semester_score" class="btn btn-info float-right">Filter</button>
            </div>
        </div>
    </div>
</form>
<script>
$(function() {
    $('select#filter_academic_year_id').on('change', function(e) {
        e.preventDefault();
        if ($(this).val() == 'all') {
            $('select#filter_semester_type_id').attr('disabled', true);
        }else{
            $('select#filter_semester_type_id').removeAttr('disabled');
        }
    });

    $('select#filter_semester_type_id').on('change', function(e) {
        e.preventDefault();
        if ($(this).val() == '5') {
            $('select#filter_academic_year_id').attr('disabled', true);
        }else{
            $('select#filter_academic_year_id').removeAttr('disabled');
        }
    });
});
</script>