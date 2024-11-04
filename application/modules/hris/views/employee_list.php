<div id="accordion">
    <div class="card">
        <div class="card-header">
            Employee Filter
            <div class="card-header-actions">
				<button class="btn btn-link card-header-action" data-toggle="collapse" data-target="#card_body_employee_filter" aria-expanded="true" aria-expanded="card_body_employee_filter">
					<i class="fas fa-caret-square-down"></i>
				</button>
			</div>
        </div>
        <div class="card-body collapse show" id="card_body_employee_filter" data-parent="#accordion">
            <?=modules::run('employee/form_filter');?>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Employee List
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" type="button" id="hris_new_employee">
                <i class="fas fa-plus"></i> New Employee
            </button>
        </div>
    </div>
    <div class="card-body">
        <?=modules::run('employee/view_employee_lists');?>
    </div>
</div>
<div id="employee_input_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('hris/form_input_employee');?>
            </div>
            <div class="modal-footer">
                <button id="save_employee" type="button" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('#hris_new_employee').on('click', function(e) {
        e.preventDefault();

        $('form#form_input_employee').find('input, select').val('').trigger('change');
        $('#employee_lecturer_number_type').val('NIDN');
        $('#employee_input_modal').modal('show');
    });

    $('button#save_employee').on('click', function(e) {
        e.preventDefault();
        $.blockUI({
            theme: true,
            baseZ: 2000
        });

        let data = $('form#form_input_employee').serialize();
        let url = $('form#form_input_employee').attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success', 'Success!');
                $('#employee_input_modal').modal('hide');
                employee_table.ajax.reload();
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data', 'Error');
        });
    });
});
</script>