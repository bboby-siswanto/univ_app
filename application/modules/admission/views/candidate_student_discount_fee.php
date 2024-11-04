<form url="<?=base_url()?>admission/save_discount_fee" id="form_discount_fee" onsubmit="return false">
    <input type="hidden" name="disc_student_id" id="disc_student_id" value="<?=$student_data->student_id;?>">
    <input type="hidden" name="disc_personal_data_id" id="disc_personal_data_id" value="<?= $personal_data_id;?>">
    <input type="hidden" name="disc_scholarship_id" id="disc_scholarship_id" value="<?= $disc_scholarship_id;?>">
    <ul class="list-group">
        <li class="list-group-item active">Discount Tuition Fee</li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Discount Fee Name</label>
                </div>
                <div class="col-md-7">
                    <select name="discount_id" id="discount_id" class="form-control">
                        <option value="">Please select...</option>
                    <?php
                        if ($discount_list) {
                            foreach ($discount_list as $o_discount) {
                        ?>
                                <option value="<?= $o_discount->fee_id;?>" <?= (($student_scholarship) AND ($student_scholarship->scholarship_fee_id == $o_discount->fee_id)) ? 'selected' : '' ?>><?=$o_discount->fee_description;?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Status </label>
                </div>
                <div class="col-md-7">
                    <select name="disc_status" id="disc_status" class="form-control">
                        <option value="">None</option>
                        <?php
                            if ($scholarship_status) {
                                foreach ($scholarship_status as $status) {
                                    if (in_array($status, $status_scholarship)) {
                        ?>
                                <option value="<?= $status?>" <?= (($student_data) AND ($student_data->scholarship_status == $status)) ? 'selected' : '' ?>><?= strtoupper($status) ?></option>
                        <?php
                            }
                        }
                    }
                ?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="float-right">
                <button type="button" id="button_save_discount" class="btn btn-info">Save</button>
            </div>
        </li>
    </ul>
</form>
<script>
$(function() {
    $('#discount_id, #disc_status').select2({
        allowClear: true,
        placeholder: 'Please select...',
        theme: "bootstrap",
    });
    
    $('#button_save_discount').on('click', function(e) {
        e.preventDefault();

        $.blockUI();
        var data = $('#form_discount_fee').serialize();
        $.post('<?= base_url()?>admission/save_candidate_discount', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr['success']('Success saving setting data', 'Success');
            }else{
                toastr['warning'](result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            console.log(params.responseText);
            $.unblockUI();
        });
    });
})
</script>