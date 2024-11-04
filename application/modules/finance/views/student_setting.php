<?php echo modules::run('student/show_name', $o_student->student_id); ?>
<form id="form_finance_setting" url="<?=base_url()?>finance/invoice/save_setting" onsubmit="return false">
    <input type="hidden" id="personal_data_id" name="personal_data_id" value="<?=$o_student->personal_data_id;?>">
    <input type="hidden" id="student_id" name="student_id" value="<?=$o_student->student_id;?>">
    
    <div class="row">
        <div class="col-md-6">
            <ul class="list-group">
                <li class="list-group-item bg-info">Settings</li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Minimum Payment</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" id="finance_min_payment" name="finance_min_payment" class="form-control" value="<?=$o_student->finance_min_payment;?>">
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <button type="button" id="btn_save_setting" class="btn float-right float-end btn-info">Save</button>
                </li>
            </ul>
        </div>
    </div>
</form>
<script>
$(function() {
    $('#btn_save_setting').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_finance_setting');
        var url = form.attr('url');
        var data = form.serialize();

        $.post(url, data, function(result) {
            if (result.code == 0) {
                toastr.success('Success');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            toastr.error('Error processing data!');
        });
    });
})
</script>