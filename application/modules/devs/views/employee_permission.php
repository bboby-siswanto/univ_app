<div class="card">
    <div class="card-header"><?= $employee_data->personal_data_name;?></div>
</div>
<div class="card">
    <div class="card-header">
        Permission Lists
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_permission">
                <i class="fa fa-plus"></i> Add Permission
            </button>
        </div>
    </div>
    <div class="card-body">
        <?= modules::run('devs/devs_employee/employee_pages_table', $employee_id); ?>
    </div>
</div>