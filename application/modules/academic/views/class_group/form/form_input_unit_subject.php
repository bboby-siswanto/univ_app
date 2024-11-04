<form id="form_unit_subject" onsubmit="return false">
    <input type="hidden" name="class_master_id" id="class_master_id" value="<?= $class_master_id?>">
    <input type="hidden" name="subject_delivered_id" id="subject_delivered_id" value="<?= ($o_subject_delivered_data) ? $o_subject_delivered_data->subject_delivered_id : '' ?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Lecturer</label>
                <input type="text" class="form-control" name="personal_data_name" id="personal_data_name" value="<?= ($o_class_lecturer) ? $o_class_lecturer->personal_data_name : '' ?>" <?= ($o_class_lecturer) ? 'readonly' : '' ?>>
                <input type="hidden" class="form-control" name="employee_id" id="employee_id"  value="<?= ($o_class_lecturer) ? $o_class_lecturer->employee_id : '' ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Date</label>
                <input type="date" class="form-control" name="unit_date" id="unit_date" value="<?= ($o_subject_delivered_data) ? date('Y-m-d', strtotime($o_subject_delivered_data->subject_delivered_time_start)) : date('Y-m-d'); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Time Start</label>
                <select name="unit_time" id="unit_time" class="form-control">
                    <option value="">Please Select...</option>
            <?php
            if ($a_times) {
                foreach ($a_times as $time) {
            ?>
                    <option value="<?=$time?>" <?= (($o_subject_delivered_data) AND ($time == (date('H:i', strtotime($o_subject_delivered_data->subject_delivered_time_start))))) ? 'selected' : '' ?>><?=$time?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Topics Covered</label>
                <textarea name="unit_description" id="unit_description" cols="30" rows="3" class="form-control"><?= ($o_subject_delivered_data) ? $o_subject_delivered_data->subject_delivered_description : '' ?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $('input#personal_data_name').autocomplete({
            minLength: 1,
            source: function(request, response){
                var url = '<?=site_url('employee/get_lecturer_sugestion')?>';
                var data = {
                    term: request.term
                };
                $.post(url, data, function(rtn){
                    var arr = [];
                    arr = $.map(rtn.data, function(m){
                        return {
                            id: m.employee_id,
                            value: m.personal_data_name
                        }
                    });
                    response(arr);
                }, 'json');
            },
            select: function(event, ui){
                var id = ui.item.id;
                $('input#employee_id').val(id);
            },
            change: function(event, ui){
                if(ui.item === null){
                    $('input#personal_data_name').val('');
                    $('input#employee_id').val('');
                    toastr['warning']('Please use the selection provided', 'Warning!');
                }
            }
        });
    });
</script>