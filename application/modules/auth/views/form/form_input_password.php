<form action="<?=base_url()?>auth/submit_new_password" id="form_newpassword" type="POST">
    <div class="form-group">
        <label for="form_new_password" class="required_text">New Password</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-lock"></i>
                </span>
            </div>
            <input class="form-control" type="password" name="form_new_password" id="form_new_password" required="true">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" id="view_password" type="button">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="form_repeat_password">Repeat Password</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-lock"></i>
                </span>
            </div>
            <input class="form-control" type="password" name="form_repeat_password" id="form_repeat_password" required="true">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" id="view_repeat_password" type="button">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>
        </div>
    </div>
</form>
<small class="text-danger">- Minimum 8 characters</small>
<button type="button" class="btn btn-block btn-facebook" id="btn_submit_reset_password">Submit</button>

<script>
$(function() {
    $('#view_password').on('click', function(e) {
        e.preventDefault();

        var current = $('#form_new_password').attr('type');
        var type = 'password'; var icon = 'fa fa-eye-slash';
        if (current == 'password') {
            var type = 'text'; var icon = 'fa fa-eye text-primary';
        }

        $("#form_new_password").attr('type', type);
        $("#view_password").html('<i class="' + icon + '"></i>');
    });
    $('#view_repeat_password').on('click', function(e) {
        e.preventDefault();

        var current = $('#form_repeat_password').attr('type');
        var type = 'password'; var icon = 'fa fa-eye-slash';
        if (current == 'password') {
            var type = 'text'; var icon = 'fa fa-eye text-primary';
        }

        $("#form_repeat_password").attr('type', type);
        $("#view_repeat_password").html('<i class="' + icon + '"></i>');
    });
    $("#btn_submit_reset_password").on('click', function(e) {
        e.preventDefault();

        $("#btn_submit_reset_password").attr('disable', 'true');
        var form = $('#form_newpassword');
        var url = form.attr('action');
        $.post(url, {iulimail: '<?=$iulimail;?>', password1: $('#form_new_password').val(), password2: $('#form_repeat_password').val()}, function(result) {
            $("#btn_submit_reset_password").attr('disable', 'false');
            if (result.code == 0) {
                swal.fire('Sukses', 'Password berhasil diganti, silahkan melakukan login kembali', 'success');
                setTimeout(function(){
			        window.location.href = '<?=base_url()?>';
		        }, 5000);
            }
            else {
                swal.fire('Warning', result.message, 'warning');
            }
        }, 'json').fail(function(params) {
            $("#btn_submit_reset_password").attr('disable', 'false');
            swal.fire('Error', 'Error proccessing your request, please try again later!', 'error');
        });
    });
})
</script>