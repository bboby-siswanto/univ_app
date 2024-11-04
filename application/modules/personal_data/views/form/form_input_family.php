<?php
$s_user_type = $this->session->userdata('type');
$disable_input = '';
if (in_array($s_user_type, ['student', 'alumni'])) {
// if (in_array($s_user_type, ['student'])) {
	$disable_input = 'readonly';
	// if ((isset($student_data)) AND ($student_data->finance_year_id == 2020)) {
	// 	$disable_input = '';
	// }
}
?>
<div class="card">
    <div class="card-header">
        Parent Data
    </div>
    <div class="card-body">
        <form id="input_family" onsubmit="return false">
        <input type="hidden" name="family_id" id="family_id" value="<?= $family_id ?>">
        <input type="hidden" name="personal_data_id" id="personal_data_id" value="<?= $personal_data_id ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Parent / Guardian Name</label>
                        <input type="text" name="name" id="personal_data_name" <?=$disable_input;?> class="form-control" value="<?= ($family_data) ? $family_data->personal_data_name : '' ?>">
                        <input type="hidden" name="personal_data_id_parent" id="personal_data_id_parent" value="<?= ($family_data) ? $family_data->personal_data_id_family : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Relations</label>
                        <select name="type" id="family_status" class="form-control" <?=$disable_input;?>>
                            <option value="">Please select...</option>
                    <?php
                    if ($relation_lists) {
                        foreach ($relation_lists as $relation) {
                            if ($relation != 'child') {
                    ?>
                            <option value="<?= $relation?>" <?= (($family_data) && ($family_data->family_member_status == $relation)) ? 'selected' : ''; ?>><?= $relation?></option>
                    <?php
                            }
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Parent Email</label>
                        <input type="email" name="email" id="personal_data_email" <?=$disable_input;?> class="form-control" value="<?= ($family_data) ? $family_data->personal_data_email : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Parent Phone</label>
                        <input type="text" name="personal_data_phone" id="personal_data_phone" <?=$disable_input;?> class="form-control" value="<?= ($family_data) ? $family_data->personal_data_phone : '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Parent Cellular</label>
                        <input type="phone" name="phone" id="personal_data_cellular" <?=$disable_input;?> class="form-control" value="<?= ($family_data) ? $family_data->personal_data_cellular : '' ?>">
                    </div>
                </div>
                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <label>Mother's Maiden Name</label>
                        <input type="text" name="mother_maiden_name" id="mother_maiden_name" class="form-control">
                    </div>
                </div> -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Parent Job Title</label>
                        <input type="text" name="occupation_name" id="occupation_name" <?=$disable_input;?> class="form-control" value="<?= ($family_data) ? $family_data->ocupation_name : '' ?>">
                        <input type="hidden" name="occupation_id" id="occupation_id" value="<?= ($family_data) ? $family_data->ocupation_id : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" id="company_name" <?=$disable_input;?> class="form-control" value="<?= ($family_data) ? $family_data->institution_name : '' ?>">
                        <input type="hidden" name="institution_id" id="company_id" value="<?= ($family_data) ? $family_data->institution_id : '' ?>">
                    </div>
                </div>
            </div>
            <button type="button" id="save_family" class="btn btn-success <?= ($disable_input != '') ? 'd-none' : ''?>">Save</button>
        </form>
    </div>
</div>
<script>
$(function() {
    $('input#company_name').autocomplete({
        source: function(request, response){
            var url = "<?=site_url('institution/get_institution')?>";
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                if(rtn.code == 0){
                    var arr = [];
                    arr = $.map(rtn.data, function(m){
                        return {
                            id: m.institution_id,
                            value: m.institution_name
                        };
                    });
                    response(arr);
                }else{
                    $("#institution_name").autocomplete('close');
                }
            }, 'json');
        },
        select: function(event, ui){
            $('input#company_id').val(ui.item.id);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('input#company_id').val('');
            }
        }
    });

    $('input#occupation_name').autocomplete({
        source: function(request, response){
            var url = "<?=site_url('institution/get_occupation_by_name')?>";
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                if(rtn.code == 0){
                    var arr = [];
                    arr = $.map(rtn.data, function(m){
                        return {
                            id: m.ocupation_id,
                            value: m.ocupation_name
                        };
                    });
                    response(arr);
                }else{
                    $("#institution_name").autocomplete('close');
                }
            }, 'json');
        },
        select: function(event, ui){
            $('input#occupation_id').val(ui.item.id);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('input#occupation_id').val('');
            }
        }
    });

    $('button#save_family').on('click', function(e) {
        e.preventDefault();

        $.blockUI();
        var data = $('form#input_family').serialize();
        $.post('<?= base_url()?>personal_data/family/save_family_member', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Save data success', 'Success');
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
        });
    });
})
</script>