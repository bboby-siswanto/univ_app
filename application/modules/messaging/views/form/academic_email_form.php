<form id="form_send_mail" onsubmit="return false">
    <input type="hidden" id="mail_student_id" name="student_id">
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label>To</label>
            </div>
            <div class="col-md-9">
                <input type="text" name="mail_student" id="mail_student" class="form-control" readonly="true">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3">
                <label>Subject</label>
            </div>
            <div class="col-md-9">
                <input type="text" name="mail_subject" id="mail_subject" class="form-control">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Message</label>
        <textarea name="mail_message" id="mail_message"></textarea>
        <input type="hidden" name="body_email" id="body_email">
    </div>
</form>
<script>
$(function() {
    CKEDITOR.replace('mail_message');

    $('button#send_mail_student').on('click', function(e) {
        e.preventDefault();
        var email_message = CKEDITOR.instances.mail_message.getData();
        $('input#body_email').val(email_message);

        if ($('#mail_subject').val() == '') {
            if (confirm('Send email without subject?')) {
                send_student_mail();
            }
        }else{
            send_student_mail();
        }
    });
});

function send_student_mail() {
    $.blockUI({ baseZ: 2000 });
    if ($('#mail_student_id').val() == 'blast') {
        var email_data = $('form#form_send_mail').serializeArray();
        var filter_data = $('form#student_filter_form').serializeArray();
        var mail_data = objectify_form($.merge(email_data, filter_data));
        var url = '<?=base_url()?>messaging/send_email_blast_student';
    }else if($('#mail_student_id').val() == ''){
        var mail_data = $('form#form_send_mail').serializeArray();
        var url = '<?=base_url()?>messaging/send_custom_email';
    }else{
        var mail_data = $('form#form_send_mail').serialize();
        var url = '<?=base_url()?>messaging/send_email_personal_student';
    }

    // console.log(url);
    $.post(url, mail_data, function(result) {
        $.unblockUI();
        if (result.code == 0) {
            toastr.success('Email successfully sent', 'Success');
            $('div#modal_send_email').modal('hide');
        }else{
            toastr.warning(result.message, 'Warning!');
        }
    }, 'json').fail(function(params) {
        $.unblockUI();
    })
}
</script>