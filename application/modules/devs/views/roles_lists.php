<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            Roles Lists
            <div class="card-header-actions">
                <button class="card-header-action btn btn-link" id="btn_new_roles">
                    <i class="fa fa-plus"></i> New Roles
                </button>
            </div>
        </div>
        <div class="card-body">
            <?=modules::run('devs/roles/roles_table');?>
        </div>
    </div>
</div>

<div id="roles_input" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('devs/roles/form_input');?>
            </div>
            <div class="modal-footer">
                <button id="save_roles" type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('button#btn_new_roles').on('click', function(e) {
            e.preventDefault();
            $('#roles_id').val('');
            $('#roles_name').val('');
            $('div#roles_input').modal('show');
        });
    });
</script>