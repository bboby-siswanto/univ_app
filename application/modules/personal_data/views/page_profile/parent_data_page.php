<?php
// print('<pre>');var_dump($parent_data);exit;
?>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_pt_family_relation" class="required_text">Relation</label>
            <select class="form-control" name="pr_pt_family_relation" id="pr_pt_family_relation">
                <option value=""></option>
                <option value="father" <?= ((isset($parent_data)) AND ($parent_data->family_member_status == 'father')) ? 'selected="selected"' : '';?>>Father</option>
                <option value="mother" <?= ((isset($parent_data)) AND ($parent_data->family_member_status == 'mother')) ? 'selected="selected"' : '';?>>Mother</option>
                <option value="guardian" <?= ((isset($parent_data)) AND ($parent_data->family_member_status == 'guardian')) ? 'selected="selected"' : '';?>>Guardian</option>
            </select>
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label for="pr_pt_personal_data_name" class="required_text">Parent / Guardian Name</label>
            <input type="text" class="form-control" name="pr_pt_personal_data_name" id="pr_pt_personal_data_name" value="<?=((isset($parent_data)) AND ($parent_data)) ? $parent_data->personal_data_name : '';?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_pt_personal_data_email" class="required_text">Parent / Guardian Email</label>
            <input type="text" class="form-control" name="pr_pt_personal_data_email" id="pr_pt_personal_data_email" value="<?=((isset($parent_data)) AND ($parent_data)) ? $parent_data->personal_data_email : '';?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_pt_personal_data_phone" class="required_text">Parent / Guardian Cellular</label>
            <input type="text" class="form-control" name="pr_pt_personal_data_phone" id="pr_pt_personal_data_phone" value="<?=((isset($parent_data)) AND ($parent_data)) ? $parent_data->personal_data_cellular : '';?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_pt_personal_data_occupation">Parent / Guardian Job Title</label>
            <input type="text" class="form-control" name="pr_pt_personal_data_occupation" id="pr_pt_personal_data_occupation" value="<?=((isset($parent_data)) AND ($parent_data)) ? $parent_data->ocupation_name : '';?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_pt_personal_data_institution_name">Company Name</label>
            <input type="text" class="form-control" name="pr_pt_personal_data_institution_name" id="pr_pt_personal_data_institution_name" value="<?=((isset($parent_data)) AND ($parent_data)) ? $parent_data->institution_name : '';?>">
        </div>
    </div>
</div>