<form id="form_transcript_draft" onsubmit="return false">
    <div class="form-group">
        <label>Message</label>
        <textarea name="draft_message" id="draft_message"></textarea>
        <input type="hidden" name="transcript_body_mail" id="transcript_body_mail">
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary" name="send_all_transcript" id="send_all_transcript">Submit</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>
<script>
    $(function() {
        CKEDITOR.replace('draft_message');

        $('button#send_all_transcript').on('click', function(e) {
            e.preventDefault();
            var email_message = CKEDITOR.instances.draft_message.getData();
            $('input#transcript_body_mail').val(email_message);

            // var email_data = $('form#form_transcript_draft').serializeArray();
            // var filter_data = $('form#student_filter_form').serializeArray();
            var mail_data = $('form#student_filter_form').serializeArray();
            // var mail_data = objectify_form($.merge(email_data, filter_data));
            
            filter_draft = $('#draft_message').val();
            filter_message_body = $('#transcript_body_mail').val();

            mail_data.push({'name' : 'transcript_body_mail', 'value' : filter_message_body});
            mail_data.push({'name' : 'draft_message', 'value' : filter_draft});
            // mail_data.push({'' : });
            console.log(mail_data);
            // return false;

            $.blockUI({ baseZ: 2000 });
            $.post('<?=base_url()?>academic/score/send_transcript_semester', mail_data, function(result) {
                
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Email sent', 'Success');
                    $('#draft_transcript_modal').modal('hide');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI(); 
            });
        });
    });
</script>