<form method="post" id="form_input_job_history" onsubmit="return false;">
    <input type="hidden" name="academic_history_id" id="academic_history_id" placeholder="academic history id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->academic_history_id : ''; ?>">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="institution_name">Company Name</label>
                    <input type="text" name="institution_name" id="institution_name" placeholder="Company Name" class="form-control" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_name : ''; ?>">
                    <input type="hidden" name="institution_id" id="institution_id" placeholder="institution id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_id : ''; ?>">
                    <input type="hidden" name="company_found_status" id="company_found_status" placeholder="company found status" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? '1' : '0'; ?>">
                    <!-- <small id="text_company_not_found" class="d-none">Company not found? <a href="#" id="activated_school">Click here</a></small> -->
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="institution_address">Company Address</label>
                    <textarea name="institution_address" id="institution_address" class="form-control locked" cols="30" rows="3"><?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_street : ''; ?></textarea>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_phone_number">Company Phone Number</label>
                    <input type="text" class="form-control locked" name="institution_phone_number" id="institution_phone_number" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_phone_number : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_email">Company Email</label>
                    <input type="text" class="form-control locked" name="institution_email" id="institution_email" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->institution_email : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_country">Country</label>
                    <!-- <input type="text" class="form-control locked" name="institution_country" id="institution_country" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->country_name : ''; ?>">
                    <input type="hidden" name="institution_country_id" id="institution_country_id" placeholder="country id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->country_id : ''; ?>"> -->
                    <select name="institution_country_id" id="institution_country_id" class="form-control">
                        <option value=""></option>
                <?php
                if ($country_list) {
                    foreach ($country_list as $key => $value) {
                        $selected = ((isset($o_academic_history_data)) AND ($o_academic_history_data) AND ($o_academic_history_data[0]->country_id == $value->country_id)) ? 'selected="selected"' : '';
                ?>
                        <option value="<?=$value->country_id;?>" <?=$selected;?>><?=$value->country_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
            <!-- <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_province">Province</label>
                    <input type="text" class="form-control locked" name="institution_province" id="institution_province" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_province : ''; ?>">
                </div>
            </div> -->
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_city">City</label>
                    <input type="text" class="form-control locked" name="institution_city" id="institution_city" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_city : ''; ?>">
                </div>
            </div>
            <!-- <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_zipcode">Zip Code</label>
                    <input type="text" class="form-control locked" name="institution_zipcode" id="institution_zipcode" disabled="true" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->address_zipcode : ''; ?>">
                </div>
            </div> -->
<?php
    if ((isset($my_job)) AND ($my_job)) {
?>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-control" name="company_start_date" id="company_start_date" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->academic_year_start_date : ''; ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label>End Date</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="company_end_date" id="company_end_date" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->academic_year_end_date : '' ?>" <?= ((isset($o_academic_history_data)) AND ($o_academic_history_data) AND (is_null($o_academic_history_data[0]->academic_year_end_date))) ? 'readonly=""' : '' ?>>
                        <div class="input-group-append">
                            <span class="input-group-text">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" <?= ((isset($o_academic_history_data)) AND ($o_academic_history_data) AND (is_null($o_academic_history_data[0]->academic_year_end_date))) ? 'checked' : 'false' ?>>
                                <input type="hidden" name="string_still_working" id="string_still_working" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data) AND (is_null($o_academic_history_data[0]->academic_year_end_date))) ? 'yes' : 'no' ?>">
                                <label class="custom-control-label" for="is_available">Still working</label>
                            </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="institution_occupation">Job Title</label>
                    <input type="text" class="form-control" name="institution_occupation" id="institution_occupation" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->ocupation_name : '' ?>">
                    <input type="hidden" name="institution_occupation_id" id="institution_occupation_id" placeholder="occupation id" value="<?= ((isset($o_academic_history_data)) AND ($o_academic_history_data)) ? $o_academic_history_data[0]->ocupation_id : '' ?>">
                </div>
            </div>
<?php
    }
?>
            <div class="col-sm-12">
                <button class="btn btn-primary" id="btn_create_job_history">Save</button>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(function() {
        var locked_input = $('.locked');
        // locked_input.prop('disabled', true);

        // $('a#activated_school').on('click', function(e) {
        //     e.preventDefault();
        //     locked_input.prop('disabled', false);
        //     $('#company_found_status').val('0');
        //     $('#institution_id').val('');
        // });

        $('#is_available').on('change', function(e) {
            e.preventDefault();

            if (this.checked) {
                $('input#company_end_date').attr('readonly','true');
                $('input#string_still_working').val('yes');
            }else{
                $('input#company_end_date').removeAttr('readonly');
                $('input#string_still_working').val('no');
            }
        });

        $('input#institution_name').autocomplete({
            autoFocus: true,
			minLength: 1,
            appendTo: 'div#new_job_history_modal, div#modal_new_job_vacancy',
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
                        // $('small#text_company_not_found').addClass('d-none');
					}
					else{
                        $("#institution_name").autocomplete('close');
						$('input#institution_id').val('');
                        // $('small#text_company_not_found').removeClass('d-none');
					}
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				var edu_data = ui.item.edu_data;
				// locked_input.prop('disabled', true);
				// $('input#institution_id').val(id);
				// $('input#company_found_status').val('1');
				$('textarea#institution_address').val(edu_data.address_street);
				$('input#institution_phone_number').val(edu_data.institution_phone_number);
				$('input#institution_email').val(edu_data.institution_email);
				// $('input#institution_zipcode').val(edu_data.address_zipcode);
				// $('input#institution_country').val(edu_data.country_name);
				// $('input#institution_country_id').val(edu_data.country_id);
                $('#institution_country_id').val(edu_data.country_id).trigger('change');
				// $('input#institution_province').val(edu_data.address_province);
				$('input#institution_city').val(edu_data.address_city);
                
			},
			change: function(event, ui){
				if(ui.item === null){
                    $('textarea#institution_address').val('');
                    $('input#institution_phone_number').val('');
                    $('input#institution_email').val('');
                    // $('input#institution_zipcode').val('');
                    // $('input#institution_country').val('');
                    // $('input#institution_country_id').val('');
                    $('#institution_country_id').val('').trigger('change');
                    // $('input#institution_province').val('');
                    $('input#institution_city').val('');

					$('input#institution_id').val('');
					// $('input#company_found_status').val('0');
				}
			}
        });

        $('select#institution_country_id').select2({
            appendTo: 'div#new_job_history_modal, div#modal_new_job_vacancy',
            minimumInputLength: 3,
            allowClear: true,
            placeholder: 'Please select...',
            theme: "bootstrap"
        });

        // $('input#institution_country').autocomplete({
		// 	minLength: 1,
		// 	appendTo: 'div#new_job_history_modal, div#modal_new_job_vacancy',
		// 	source: function(request, response){
		// 		var url = '<?=site_url('institution/get_country_by_name')?>';
		// 		var data = {
		// 			term: request.term
		// 		};
		// 		$.post(url, data, function(rtn){
		// 			if(rtn.code == 0){
		// 				var arr = [];
		// 				arr = $.map(rtn.data, function(m){
		// 					return {
		// 						id: m.country_id,
		// 						value: m.country_name
		// 					}
		// 				});
		// 				response(arr);
		// 			}
		// 		}, 'json');
		// 	},
		// 	select: function(event, ui){
		// 		var id = ui.item.id;
		// 		$('input#institution_country_id').val(id);
		// 	},
		// 	change: function(event, ui){
		// 		if(ui.item === null){
        //             $('input#institution_country').val('');
		// 			$('input#institution_country_id').val('');
        //             toastr['warning']('Please use the selection provided!', 'warning');
		// 		}
		// 	}
		// });

        $('input#institution_occupation').autocomplete({
            minLength:1,
            appendTo: 'div#new_job_history_modal',
			source: function(request, response){
				var url = '<?=site_url('institution/get_occupation')?>';
				var data = {
					term: request.term
				};
				$.post(url, data, function(rtn){
					if(rtn.code == 0){
						var arr = [];
						arr = $.map(rtn.data, function(m){
							return {
								id: m.ocupation_id,
								value: m.ocupation_name
							}
						});
						response(arr);
					}
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				$('input#institution_occupation_id').val(id);
			},
			change: function(event, ui){
				if(ui.item === null){
					$('input#institution_occupation_id').val('');
				}
			}
        });
    });
</script>