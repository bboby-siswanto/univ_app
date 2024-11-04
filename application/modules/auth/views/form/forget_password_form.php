<form method="post" id="form_forget_password" onsubmit="return false">
    <label>Enter your IULI email to reset your password:</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
        <span class="input-group-text">
            <i class="fa fa-at"></i>
        </span>
        </div>
        <input class="form-control" type="email" placeholder="IULI Email" id="email" name="email">
    </div>
</form>
<script>
$(function() {
    $('#submit_form_forget_password').on('click', function(e) {
        e.preventDefault();
        var data = $('#form_forget_password').serialize();
        // console.log('click');
        $('#submit_form_forget_password').attr('disabled', 'disabled');
        $.post('<?=base_url()?>auth/forget_password', data, function(result) {
            $('#submit_form_forget_password').removeAttr('disabled');
            if (result.code == 0) {
                swal.fire({
                    type:'success',
                    title:'Success',
                    text:'The link for reset your password has been sending to your personal email account, please check your email: ' + result.email_result
                }).then(res => {
                    $('#forgot_password_modals').modal('hide');
                });
            } else {
                var counter = result.counter;
                swal.fire('Error', result.message, 'warning');
                if (counter >= 3) {
                    window.location.reload();
                }
            }
            console.log(result);
        }, 'json').fail(function(params) {
            $('#submit_form_forget_password').removeAttr('disabled');
            swal.fire('Error', 'Error System', 'error');
        });
    });
})
</script>