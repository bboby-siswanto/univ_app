<form id="compose_email_form" method="post" action="<?=site_url('messaging/send_email')?>">
	<div class="form-group">
		<label>Message Title</label>
		<input type="text" name="message_title" id="message_title">
	</div>
	<div class="form-group">
		<label>Message Content</label>
		<input type="text" name="message_content" id="message_content">
	</div>
</form>

<script>
	$('form#compose_email_form').on('submit', function(e){
		e.preventDefault();
	});
</script>