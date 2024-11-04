<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            Employee Lists
            <div class="card-header-actions">
                <button class="card-header-action btn btn-link" id="btn_new_employee">
                    <i class="fa fa-plus"></i> New Employee
                </button>
            </div>
        </div>
        <div class="card-body">
<?php
    // print('<pre>');
    // $mba_permissions = modules::run('devs/devs_employee/employee_permission');
    // var_dump($this->session->userdata());
?>
            <?=modules::run('devs/devs_employee/employee_table');?>
        </div>
    </div>
</div>

<div id="employee_input" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('devs/devs_employee/form_input');?>
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
        $('button#btn_new_employee').on('click', function(e) {
            e.preventDefault();
            
            var input = $('form#form_input_employee').find('input, select');
            $('#employee_lecturer_number_type').attr('disabled', true);
            $('#employee_lecturer_number').attr('disabled', true);
            $.each(input, function(i, v) {
                v.value = '';
            });
            $('div#employee_input').modal('show');
        });
    });
</script>