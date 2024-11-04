<form method="post" id="form_edit_academic_history" onsubmit="return false;">
    <input type="hidden" name="personal_data_id" id="personal_data_id" value="<?= $personal_data_id;?>" placeholder="personal data id">
    <div class="col-sm-12">
        <div class="row">
            <input type="hidden" name="academic_history_id" id="academic_history_id" placeholder="academic history id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->academic_history_id : ''; ?>">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="institution_name">School Name</label>
                    <input type="text" name="institution_name" id="institution_name" placeholder="School Name" class="form-control" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_name : ''; ?>">
                    <input type="hidden" name="institution_id" id="institution_id" placeholder="institution id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_id : ''; ?>">
                    <input type="hidden" name="school_found_status" id="school_found_status" placeholder="school found status" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? '1' : '0'; ?>">
                    <small id="text_school_not_found" class="d-none">School not found? <a href="#" id="activated_school">Click here</a></small>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="institution_address">School Address</label>
                    <textarea name="institution_address" id="institution_address" class="form-control locked" cols="30" rows="3" disabled="true"><?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_street : ''; ?></textarea>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_phone_number">School Phone Number</label>
                    <input type="text" class="form-control locked" name="institution_phone_number" id="institution_phone_number" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_phone_number : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_email">School Email</label>
                    <input type="text" class="form-control locked" name="institution_email" id="institution_email" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_email : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_country">Country</label>
                    <input type="text" class="form-control locked" name="institution_country" id="institution_country" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->country_name : ''; ?>">
                    <input type="hidden" name="institution_country_id" id="institution_country_id" placeholder="country id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->country_id : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_province">Province</label>
                    <input type="text" class="form-control locked" name="institution_province" id="institution_province" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_province : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_city">City</label>
                    <input type="text" class="form-control locked" name="institution_city" id="institution_city" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_city : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_zipcode">Zip Code</label>
                    <input type="text" class="form-control locked" name="institution_zipcode" id="institution_zipcode" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_zipcode : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="school_graduation_year">Graduation Year</label>
                    <input type="text" class="form-control" name="school_graduation_year" id="school_graduation_year" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->academic_history_graduation_year : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="major">Major/Discipline</label>
                    <select class="form-control" id="major" name="major">
                        <option value="">---</option>
                        <option value="IPA" <?= ((isset($o_academic_history_data)) AND ($o_academic_history_data) AND ($o_academic_history_data[0]->academic_history_major == 'IPA')) ? 'selected' : ''; ?>>IPA</option>
                        <option value="IPS" <?= ((isset($o_academic_history_data)) AND ($o_academic_history_data) AND ($o_academic_history_data[0]->academic_history_major == 'IPS')) ? 'selected' : ''; ?>>IPS</option>
                    </select>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" id="btn_create_academic_history">Save</button>
    </div>
</form>
<script type="text/javascript">
    $(function() {
        var locked_input = $('.locked');
        locked_input.prop('disabled', true);

        $('a#activated_school').on('click', function(e) {
            e.preventDefault();
            locked_input.prop('disabled', false);
            $('#school_found_status').val('0');
            $('#institution_id').val('');
        });

        $('input#institution_name').autocomplete({
            autoFocus: true,
			minLength: 1,
            appendTo: 'div#new_academic_history_modal',
			source: function(request, response){
				var url = '<?=site_url('institution/get_institution')?>';
				var data = {
					term: request.term
				};
				$.post(url, data, function(rtn){
					if(rtn.data){
						var arr = [];
						arr = $.map(rtn.data, function(m){
							return {
								id: m.institution_id,
								value: m.institution_name,
								edu_data: m
							};
						});
						response(arr);
                        $('small#text_school_not_found').addClass('d-none');
					}
					else{
                        $("#institution_name").autocomplete('close');
						$('input#institution_id').val('');
                        $('small#text_school_not_found').removeClass('d-none');
					}
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				var edu_data = ui.item.edu_data;
				locked_input.prop('disabled', true);
				$('input#institution_id').val(id);
				$('input#school_found_status').val('1');
				$('textarea#institution_address').val(edu_data.address_street);
				$('input#institution_phone_number').val(edu_data.institution_phone_number);
				$('input#institution_email').val(edu_data.institution_email);
				$('input#institution_zipcode').val(edu_data.address_zipcode);
				$('input#institution_country').val(edu_data.country_name);
				$('input#institution_country_id').val(edu_data.country_id);
				$('input#institution_province').val(edu_data.address_province);
				$('input#institution_city').val(edu_data.address_city);
                
			},
			change: function(event, ui){
				if(ui.item === null){
                    $('textarea#institution_address').val('');
                    $('input#institution_phone_number').val('');
                    $('input#institution_email').val('');
                    $('input#institution_zipcode').val('');
                    $('input#institution_country').val('');
                    $('input#institution_country_id').val('');
                    $('input#institution_province').val('');
                    $('input#institution_city').val('');

					$('input#institution_id').val('');
					$('input#school_found_status').val('0');
				}
			}
        });

        $('input#institution_country').autocomplete({
			minLength: 1,
			appendTo: 'div#new_academic_history_modal',
			source: function(request, response){
				var url = '<?=site_url('institution/get_country_by_name')?>';
				var data = {
					term: request.term
				};
				$.post(url, data, function(rtn){
					if(rtn.code == 0){
						var arr = [];
						arr = $.map(rtn.data, function(m){
							return {
								id: m.country_id,
								value: m.country_name
							}
						});
						response(arr);
					}else{
                        $("#institution_name").autocomplete('close');
                    }
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				$('input#institution_country_id').val(id);
			},
			change: function(event, ui){
				if(ui.item === null){
                    $('input#institution_country').val('');
					$('input#institution_country_id').val('');
					swal.fire('','Please use the selection provided','warning');
				}
			}
		});

        $('button#btn_create_academic_history').on('click', function(e) {
            e.preventDefault();
            $.blockUI({
                baseZ: 2000
            });
            
            var form = $('form#form_edit_academic_history');
			var url = "<?=site_url('personal_data/academic/save_academic_history')?>";
            var data = form.serialize();
            $('div#new_academic_history_modal').modal('hide');

            $.post(url, data, function(rtn){
				if(rtn.code == 0){
                    $.unblockUI();
					toastr['success']('Success saved data', 'Success!');
					window.location.reload();
				}
				else{
                    $.unblockUI();
                    toastr['error'](rtn.message, 'Warning!');
				}
            }, 'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });
        });
    });
</script>