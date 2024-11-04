<form id="send_email" name="send_email" method="post" action="<?=site_url('student/send_email')?>">
	<input type="hidden" name="message_recepient" id="message_recepient">
    <div class="form-group">
        <label>Subject</label>
        <input class="form-control" id="message_subject" name="message_subject">
    </div>
    <div class="form-group">
        <label>Message</label>
        <textarea class="form-control" id="message" name="message"></textarea>
    </div>
    <button class="btn btn-md btn-info pull-right" id="send_message">Send</button>
</form>

<script>
	if($.fn.DataTable.isDataTable('table#student_list_table')){
		$('table#student_list_table tbody').on('click', 'button#btn_display_modal', function(e){
			e.preventDefault();
			$('div#send_email_modal').modal('toggle');
			var data = student_list_table.row($(this).parents('tr')).data();
			let recepient;
			if((data['student_status'] == 'candidate') || (data['student_status'] == 'participant')){
				recepient = data['personal_data_email'];
			}
			$('input#message_recepient').val(recepient);
		});
	}
	
	$('button#send_message').on('click', function(e){
		e.preventDefault();
		var form = $('form#send_email');
		$.post(form.attr('action'), form.serialize(), function(rtn){
			if(rtn.code == 0){
				console.log('terkirim');
			}
		}, 'json');
	});
</script>