<div class="col-md-8 offset-md-2 text-center">
    <h3>Authentication</h3>
    <hr/>
    <form id="form_login" action="<?=site_url('auth/login')?>">
	    <div class="alert d-none" role="alert" id="login_alert"></div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fa fa-at"></i>
            </span>
            </div>
            <input class="form-control" type="text" autocomplete="off" autocapitalize="off" placeholder="IULI Email" name="email" autofocus required="true">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fa fa-lock"></i>
                </span>
            </div>
            <input class="form-control" type="password" placeholder="Password" name="password" id="login_password" required="true">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" id="view_password" type="button">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button id="submit_login" class="btn btn-block btn-facebook" type="submit">Sign In</button>
            </div>
    <?php
    if ((!$this->session->has_userdata('counter_forget_password')) OR ($this->session->userdata('counter_forget_password') < 3)) {
    ?>
            <div class="col-12">
                <button type="button" class="btn btn-block btn-link" data-toggle="modal" data-target="#forgot_password_modals">
                    <span>Forget Password</span>
                </button>
            </div>
    <?php
    }
    ?>
        </div>
    </form>
</div>
<script>
	$('#form_login').on('submit', function(e){
        e.preventDefault();
        // $('div#login_alert').removeClass('d-none').addClass('alert-danger').html('Sorry for the inconvenience but we’re performing some maintenance at the moment. If you need to you can always contact us, otherwise we’ll be back online shortly!');
        // setTimeout(function(){
        //     $('div#login_alert').removeClass('alert-danger').addClass('d-none').html('');
        // }, 5000);

        $('#submit_login').attr('disabled', 'true');

        var login_form = $('form#form_login');
        $.post(login_form.attr('action'), login_form.serialize(), function(rtn){
            if(rtn.code == 0){
		        window.location.replace(rtn.redirect_uri);
	        }
            else if(rtn.code == 2) {
                $('#submit_login').removeAttr('disabled');
                $('div#login_alert').removeClass('d-none').addClass('alert-danger').html(rtn.message);
		        setTimeout(function(){
			        $('div#login_alert').removeClass('alert-danger').addClass('d-none').html('');
		        }, 5000);
                Swal.fire({
                    title: '<strong>Alert..</strong>',
                    icon: 'info',
                    html: rtn.message,
                    showCloseButton: false,
                    showCancelButton: true,
                    showConfirmButton: false,
                    focusConfirm: false,
                    // confirmButtonText:
                    // 	'<i class="fa fa-thumbs-up"></i> Great!',
                    // confirmButtonAriaLabel: 'Thumbs up, great!',
                    cancelButtonText: 'Close',
                    cancelButtonAriaLabel: 'Close'
                });
            }
	        else{
                $('#submit_login').removeAttr('disabled');
		        $('div#login_alert').removeClass('d-none').addClass('alert-danger').html(rtn.message);
		        setTimeout(function(){
			        $('div#login_alert').removeClass('alert-danger').addClass('d-none').html('');
		        }, 5000);
	        }
        }, 'json').fail(function(params) {
            $('#submit_login').removeAttr('disabled');
            $('div#login_alert').removeClass('d-none').addClass('alert-danger').html('Invalid Email / Password');
		        setTimeout(function(){
			        $('div#login_alert').removeClass('alert-danger').addClass('d-none').html('');
		        }, 5000);
        });
    });
    $(function() {
        $('#view_password').on('click', function(e) {
            e.preventDefault();

            var current = $('#login_password').attr('type');
            var type = 'password'; var icon = 'fa fa-eye-slash';
            if (current == 'password') {
                var type = 'text'; var icon = 'fa fa-eye text-primary';
            }

            $("#login_password").attr('type', type);
            $("#view_password").html('<i class="' + icon + '"></i>');
        })
    })
</script>