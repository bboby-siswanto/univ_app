<div class="row">
	<div class="col-12">
		<?=modules::run('auth/login_form')?>
	</div>
</div>
<div class="modal fade" id="forgot_password_modals" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Forget Password</h4>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <?=modules::run('auth/forget_password_form')?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            <button class="btn btn-info" type="button" id="submit_form_forget_password">Request</button>
        </div>
        </div>
    </div>
</div>