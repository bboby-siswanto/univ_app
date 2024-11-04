<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>IULI PMB</title>
        <meta name="description" content="IULI PORTAL">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

        
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	    <script>
	        WebFont.load({
	            google: {
	                "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
	            },
	            active: function() {
	                sessionStorage.fonts = true;
	            }
	        });
	    </script>

        <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/vendors/fontawesome/css/all.min.css" rel="stylesheet">
        <link href="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/images/ui-icons_444444_256x240.png" rel="stylesheet" type="text/css" />

        <script src="<?=base_url()?>assets/vendors/jquery/js/jquery.min.js"></script>
        <script src="<?=base_url()?>assets/vendors/popper.js/js/popper.min.js"></script>
        <script src="<?=base_url()?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?=base_url()?>assets/vendors/pace-progress/js/pace.min.js"></script>
        <script src="<?=base_url()?>assets/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
        <script src="<?=base_url()?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
        <script src="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.js"></script>
    </head>
    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
        <div class="container pt-5">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-header" style="background-color: #001489 !important;">
                                <div class="row">
                                    <div class="col-md-5 my-auto">
                                        <img src="<?=base_url()?>assets/img/iuli.png" class="img-fluid"/>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-8 offset-md-2 text-center">
        <?php
        // if ((date('H:i:s') > date('H:i:s', strtotime('08:00:00')))
        //          AND (date('H:i:s') <= date('H:i:s', strtotime('15:00:00')))
        //                 AND (!in_array(date('w'), [6,7]))) {
            // if () {
        ?>
                                    <h3>Authentication</h3>
                                    <hr/>
                                    <form id="form_login" action="<?=site_url('exam/auth_entrance_test/check_token')?>">
                                        <div class="alert d-none" role="alert" id="login_alert"></div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-at"></i>
                                            </span>
                                            </div>
                                            <input class="form-control" type="email" autocomplete="off" autocapitalize="off" placeholder="Your Email Registered" name="email" autofocus required="true">
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-at"></i>
                                            </span>
                                            </div>
                                            <input class="form-control" type="text" autocomplete="off" autocapitalize="off" placeholder="Your Token" name="token" autofocus required="true">
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button id="submit_login" class="btn btn-block btn-facebook" type="submit">Sign In</button>
                                            </div>
                                        </div>            
                                    </form>
                                    <hr>
                                    <h4 class="text-left">Rules:</h4>
                                    <ol class="text-left" style="font-size: 12px !important">
                                        <li>You will have 160 minutes to do your test, which consists of a 35 minutes english listening section, 
                                            a 25 minutes english reading section &amp; a 100 minutes mathematics section.</li>
                                        <li>You can’t undo the answers you have already submitted, so please think carefully</li>
                                        <li>After you finish, the answers will be automatically sent to the IULI Admissions Department</li>
                                    </ol>
        <?php
            // }
        // }
        // else {
        ?>
                                    <!-- <h3>Sorry..</h3>
                                    <hr/>
                                    <p>The exam will be start from Monday to Friday from 08:00 to 15:00 server time (WIB)</p> -->
        <?php
        // }
        ?>
                                </div>
                            </div>
                            <div class="card-footer text-white" style="background-color: #001489 !important;">
                                &copy; International University Liaison Indonesia.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" role="dialog" id="show_notif_exam" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Thank you for your interest in IULI. Before you start your entrance test please read the instructions below:
                        </h5>
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button> -->
                    </div>
                    <div class="modal-body">
                        <ol>
                            <li>You will have 160 minutes to do your test, which consists of a 35 minutes english listening section, 
                                a 25 minutes english reading section &amp; a 60 minutes mathematics section.</li>
                            <li>You can’t undo the answers you have already submitted, so please think carefully</li>
                            <li>After you finish, the answers will be automatically sent to the IULI Admissions Department</li>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submit_step">Continue..</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var url = '';
            $('button#submit_step').on('click', function(e) {
                e.preventDefault();
                window.location.replace(url);
            });
            $('#form_login').on('submit', function(e){
                e.preventDefault();
                $('#submit_login').attr('disabled', 'true');

                var login_form = $('form#form_login');
                $.post(login_form.attr('action'), login_form.serialize(), function(rtn){
                    $('#submit_login').removeAttr('disabled');
                    if(rtn.code == 0){
                        url = rtn.redirect_uri;
                        // window.location.replace(url);
                        $('#show_notif_exam').modal('show');
                    }
                    else{
                        $('div#login_alert').removeClass('d-none').addClass('alert-danger').html(rtn.message);
                        setTimeout(function(){
                            $('div#login_alert').removeClass('alert-danger').addClass('d-none').html('');
                        }, 5000);
                    }
                }, 'json').fail(function(params) {
                    $('#submit_login').removeAttr('disabled');
                    $('div#login_alert').removeClass('d-none').addClass('alert-danger').html('Invalid token');
                        setTimeout(function(){
                            $('div#login_alert').removeClass('alert-danger').addClass('d-none').html('');
                        }, 5000);
                });
            });
        </script>
    </body>
</html>