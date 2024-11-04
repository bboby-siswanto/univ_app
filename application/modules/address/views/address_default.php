<?= modules::run('student/show_name', $o_personal_data->personal_data_id);?>
<div class="row">
    <?=modules::run('address/view_list_address_list', $personal_data_id)?>
</div>