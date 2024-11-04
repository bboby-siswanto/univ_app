<form method="post" id="student_registration_form" action="<?=site_url('admission/create_new_student')?>">
	<div class="form-group">
		<div class="input-group mb-3">
	        <div class="input-group-prepend">
		        <span class="input-group-text">
		            <i class="fa fa-user"></i>
		        </span>
	        </div>
	        <input class="form-control" type="text" id="fullname" name="fullname" placeholder="Fullname" required="true" autofocus="true">
	    </div>
	</div>
	<div class="form-group">
		<div class="input-group mb-3">
	        <div class="input-group-prepend">
		        <span class="input-group-text">
		            <i class="fa fa-at"></i>
		        </span>
	        </div>
	        <input class="form-control" type="email" id="email" name="email" placeholder="Email" required="true">
	    </div>
	</div>
    <div class="form-group">
	    <div class="input-group mb-3">
	        <div class="input-group-prepend">
		        <span class="input-group-text">
		            <i class="fa fa-phone"></i>
		        </span>
	        </div>
	        <input class="form-control" type="text" id="mobile_phone" name="mobile_phone" placeholder="Mobile Phone" required="true" oninput="this.value=this.value.replace(/[^\d\+]/,'')">
	    </div>
    </div>
    <div class="form-group">
		<div class="input-group mb-3">
	        <div class="input-group-prepend">
		        <span class="input-group-text">
		            <i class="fa fa-at"></i>
		        </span>
	        </div>
	        <select class="form-control" name="study_program_id" id="study_program_new_student" required="true">
                <option value="">Please Select...</option>
        <?php
        if ((isset($study_program_list)) AND ($study_program_list)) {
            foreach ($study_program_list as $o_prodi) {
        ?>
                <option value="<?=$o_prodi->study_program_id;?>"><?=$o_prodi->study_program_name;?></option>
        <?php
            }
        }
        ?>
            </select>
	    </div>
	</div>
    <div class="form-group form-check">
	    <input class="form-check-input" type="checkbox" id="send_email" name="send_email" checked="">
        <label for="send_email" class="form-check-label">Send email</label>
    </div>
    <button type="button" class="btn btn-primary" id="create_new_student">Create</button>
</form>

<script>
    var new_student_form = $('form#student_registration_form');
    $('button#create_new_student').on('click', function(e){
       e.preventDefault();
       $.blockUI({ baseZ: 2000 });
       $.post(new_student_form.attr('action'), new_student_form.serialize(), function(rtn){
           $.unblockUI();
           if(rtn.code == 0){
               toastr['success']('Candidate student has been created', 'Success!');
               if($.isFunction(window.show_student_table)){
                   show_student_table({
                       academic_year_id: '<?=$active_batch[0]->academic_year_id?>'
                   });
                   new_student_form[0].reset();
                   $('div#new_candidate_modal').modal('toggle');
                }
                else{
                    window.location.reload();
                }
           }
           else{
               toastr['warning'](rtn.message, 'Warning!');
           }
        }, 'json').fail(function(params) {
            $.unblockUI();
        });
    });
</script>