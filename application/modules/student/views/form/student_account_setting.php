<form id="form_account_setting" url="<?=base_url()?>academic/student_academic/save_account_settings" onsubmit="return false">
    <input type="hidden" name="student_number" value="<?= (($student_data) AND (!is_null($student_data->student_number))) ? '1' : '0' ?>">
    <input type="hidden" name="student_id" value="<?= ($student_data) ? $student_data->student_id : '' ?>">
    <input type="hidden" name="personal_data_id" value="<?= ($student_data) ? $student_data->personal_data_id : '' ?>">
    <input type="hidden" name="student_email" value="<?= ($student_data) ? $student_data->student_email : '' ?>">
    <ul class="list-group">
        <li class="list-group-item active">Account Settings</li>
        <li class="list-group-item">
            <div class="row">
                <div class="col">
                    <label>Portal Blocked</label>
                </div>
                <div class="col">
                    <div class="pull-right">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="student_block" name="student_block" <?= (($student_data) AND ($student_data->student_portal_blocked == 'TRUE')) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="student_block"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div id="input_blocked_message" class="row mt-2 <?= (($student_data) AND ($student_data->student_portal_blocked == 'TRUE')) ? '' : 'd-none' ?>">
                <div class="col-md-5">
                    <label>Blocked Message</label>
                </div>
                <div class="col-md-7">
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" name="blocked_message" id="blocked_message" class="form-control" aria-describedby="blocked_message" value="<?= (($student_data) AND (!is_null($student_data->student_portal_blocked))) ? $student_data->student_portal_blocked_message : '' ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-warning" type="button" id="view_blocked_message" title="Show Block Message"><i class="fa fa-exclamation-triangle"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col">
                    <label>Score Blocked</label><br>
                    <small class="text-danger">(Transcript &amp; Access view score)</small>
                </div>
                <div class="col">
                    <div class="pull-right">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="student_send_transcript" name="student_send_transcript" <?= (($student_data) AND ($student_data->student_send_transcript == 'FALSE')) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="student_send_transcript"></label>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col">
                    <label>Email Blocked</label>
                </div>
                <div class="col">
                    <div class="pull-right">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="student_email_enable" name="student_email_enable" <?= ($mailenabled == 'FALSE') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="student_email_enable"></label>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col">
                    <label>Wifi Blocked</label>
                </div>
                <div class="col">
                    <div class="pull-right">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="student_inet_enable" name="student_inet_enable" <?= ($internetenabled == 'FALSE') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="student_inet_enable"></label>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="float-right">
                <button type="button" id="btn_reset_password" class="btn btn-danger">Reset Account Password</button>
                <button type="button" id="button_save_account_setting" class="btn btn-info">Submit</button>
            </div>
        </li>
    </ul>
</form>
<script>
$(function() {
    $('#button_save_account_setting').on('click', function(e) {
        e.preventDefault();

        if (confirm("Submit Account settings!")) {
            $.blockUI();
            var form = $('#form_account_setting');
            var data = form.serialize();
            var url = form.attr("url");
            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    window.location.reload();
                    toastr['success']('Success submit account setting data', 'Success');
                }else{
                    toastr['warning'](result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        }
    });

    $('#btn_reset_password').on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: "Do you want to reset password account this student?",
            text: "The password will change to the default format of the student's date of birth",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Continue",
        }).then((resultclicked) => {
            /* Read more about isConfirmed, isDenied below */
            // console.log(resultclicked);
            if (resultclicked.value) {
                $.blockUI({ baseZ: 2000 });
                $.post('<?=base_url()?>student/reset_password_student', {student_id: '<?= ($student_data) ? $student_data->student_id : '' ?>'}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success');
                        // window.location.reload();
                    }
                    else {
                        toastr.warning(result.message, 'Warning');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error');
                })
            }
        });
    });

    $('#student_block').on('change', function(e) {
        if ($('#student_block').is(':checked')) {
            $('#input_blocked_message').removeClass('d-none');
        }
        else {
            $("#input_blocked_message").addClass('d-none');
        }
    })

    $('button#view_blocked_message').on('click', function(e) {
        e.preventDefault();

        var message = $('input#blocked_message').val();
        Swal.fire({
            title: '<strong>Alert..</strong>',
            icon: 'info',
            html: message,
            showCloseButton: false,
            showCancelButton: true,
            showConfirmButton: false,
            focusConfirm: false,
            cancelButtonText: 'Close',
            cancelButtonAriaLabel: 'Close'
        });
    });
})
</script>