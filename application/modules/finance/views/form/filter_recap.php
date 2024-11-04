<div id="accordion_filter">
    <div class="card">
        <div class="card-header">
            Filter Data
            <div class="card-header-actions">
				<button class="btn btn-link card-header-action" data-toggle="collapse" data-target="#card_filter" aria-expanded="true" aria-expanded="card_body_student_filter">
					<i class="fas fa-caret-square-down"></i>
				</button>
			</div>
        </div>
        <div class="card-body collapse hide" id="card_filter" data-parent="#accordion_filter">
            <form id="filter_recap" onsubmit="return false">
                <input type="hidden" name="payment_type_code" id="payment_type_code" value="02">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="batch">Batch</label>
                            <select name="batch" id="batch" class="form-control form-control-sm">
                                <option value="all">All</option>
                    <?php
                        if ($batch) {
                            foreach ($batch as $o_bacth) {
                    ?>
                                <option value="<?=$o_bacth->academic_year_id;?>"><?=$o_bacth->academic_year_id;?></option>
                    <?php
                            }
                        }
                    ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="academic_semester">Academic Semester</label>
                            <select name="academic_semester" id="academic_semester" class="form-control form-control-sm">
                    <?php
                        if ($academic_semester) {
                            foreach ($academic_semester as $o_academic_semester) {
                                $selected = ($o_academic_semester->semester_status == 'active') ? 'selected="selected"' : '';
                    ?>
                                <option value="<?=$o_academic_semester->academic_year_id.'-'.$o_academic_semester->semester_type_id;?>" <?=$selected;?>><?=$o_academic_semester->academic_year_id.'-'.$o_academic_semester->semester_type_id;?></option>
                    <?php
                            }
                        }
                    ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="student_status">Student Status</label>
                            <select name="student_status[]" id="student_status" class="form-control form-control-sm selectpicker" multiple data-live-search="true" data-actions-box="true">
                    <?php
                        if ($status_lists) {
                            foreach ($status_lists as $s_status) {
                    ?>
                                <option value="<?=$s_status;?>"><?= strtoupper($s_status);?></option>
                    <?php
                            }
                        }
                    ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button id="submit_filter" type="button" class="btn btn-info float-right">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
var selectmulti = $('#student_status').selectpicker();
// $('select[name="student_status[]"]').val(['active', 'inactive', 'onleave']);
$('select[name="student_status[]"]').val('active');
$('.selectpicker').selectpicker('refresh');
</script>