<form id="form_filter_employee" url="<?=base_url()?>hris/employee_list" onsubmit="return false">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="employee_status">Status</label>
                <select name="employee_status" id="employee_status" class="form-control">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="resign">Resign</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="employee_type">Type</label>
                <select name="employee_type" id="employee_type" class="form-control">
                    <option value="all">All</option>
                    <option value="lecturer">Lecturer</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="button" class="btn btn-info float-right" id="filter_employee_button">Filter</button>
        </div>
    </div>
</form>