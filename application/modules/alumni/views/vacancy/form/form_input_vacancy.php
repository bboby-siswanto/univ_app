<div class="custom_company">
    <form id="form_vacancy" onsubmit="return false">
        <input type="hidden" name="job_vacancy_id" id="job_vacancy_id">
        <div class="col-sm-12">
            <div class="row"></div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" class="form-control" name="institution_name" id="institution_name">
                        <input type="hidden" name="institution_id" id="institution_id">
                        <input type="hidden" name="company_found_status" id="company_found_status" value="0">
                        <small id="text_company_not_found" class="d-none">Company not found? <a href="#" id="activated_school">Click here</a></small>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Company Address</label>
                        <textarea name="address_street" id="address_street" cols="30" rows="5" class="form-control locked" disabled="true"></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Company Phone Number</label>
                        <input type="text" class="form-control locked" name="institution_phone_number" id="institution_phone_number" disabled="true">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Company Email</label>
                        <input type="email" class="form-control locked" name="institution_email" id="institution_email" disabled="true">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country_name" id="country_name" class="form-control locked" disabled="true">
                        <input type="hidden" name="country_id" id="country_id" class="form-control">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Province</label>
                        <input type="text" class="form-control locked" name="address_province" id="address_province" disabled="true">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control locked" name="address_city" id="address_city" disabled="true">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Zip Code</label>
                        <input type="text" class="form-control locked" name="address_zipcode" id="address_zipcode" disabled="true">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" class="form-control" name="job_vacancy_site" id="job_vacancy_site">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="job_vacancy_email" id="job_vacancy_email">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="ocupation_name" id="occupation_name" class="form-control">
                        <input type="hidden" name="occupation_id" id="occupation_id">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Job Description</label>
                        <textarea name="job_description" id="job_description" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Requirements</label>
                        <textarea name="requirements" id="requirements" cols="30" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="button" id="btn_save_vacancy" class="btn btn-info float-right">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function() {
        var locked_input = $('.locked');
        locked_input.prop('disabled', true);

        $('a#activated_school').on('click', function(e) {
            e.preventDefault();
            locked_input.prop('disabled', false);
            $('#company_found_status').val('0');
            $('#institution_id').val('');
        });

        $('input#institution_name').autocomplete({
            autoFocus: true,
			minLength: 1,
            appendTo: 'div#modal_new_job_vacancy',
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
                        $('small#text_company_not_found').addClass('d-none');
					}
					else{
                        $("#institution_name").autocomplete('close');
						$('input#institution_id').val('');
                        $('small#text_company_not_found').removeClass('d-none');
					}
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				var edu_data = ui.item.edu_data;
				locked_input.prop('disabled', true);
				$('input#institution_id').val(id);
				$('input#company_found_status').val('1');
				$('textarea#address_street').val(edu_data.address_street);
				$('input#institution_phone_number').val(edu_data.institution_phone_number);
				$('input#institution_email').val(edu_data.institution_email);
				$('input#address_zipcode').val(edu_data.address_zipcode);
				$('input#country_name').val(edu_data.country_name);
				$('input#country_id').val(edu_data.country_id);
				$('input#address_province').val(edu_data.address_province);
				$('input#address_city').val(edu_data.address_city);
                
			},
			change: function(event, ui){
				if(ui.item === null){
                    $('textarea#address_street').val('');
                    $('input#institution_phone_number').val('');
                    $('input#institution_email').val('');
                    $('input#address_zipcode').val('');
                    $('input#country_name').val('');
                    $('input#country_id').val('');
                    $('input#address_province').val('');
                    $('input#address_city').val('');

					$('input#institution_id').val('');
					$('input#company_found_status').val('0');
				}
			}
        });

        $('input#country_name').autocomplete({
			minLength: 1,
			appendTo: 'div#modal_new_job_vacancy',
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
                        $("#country_name").autocomplete('close');
                    }
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				$('input#country_id').val(id);
			},
			change: function(event, ui){
				if(ui.item === null){
                    $('input#country_name').val('');
					$('input#country_id').val('');
                    toastr['warning']('Please use the selection provided!', 'warning');
				}
			}
		});

        $('input#occupation_name').autocomplete({
            minLength:1,
            appendTo: 'div#modal_new_job_vacancy',
			source: function(request, response){
				var url = '<?=site_url('institution/get_occupation_by_name')?>';
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
					}else{
                        $("#occupation_name").autocomplete('close');
                    }
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				$('input#occupation_id').val(id);
			},
			change: function(event, ui){
				if(ui.item === null){
					$('input#occupation_id').val('');
				}
			}
        });

        $('button#btn_save_vacancy').on('click', function(e) {
            e.preventDefault();
            $.blockUI({baseZ: 2000});

            var data = $('form#form_vacancy').serialize();
            $.post('<?= base_url()?>alumni/vacancy/save_job_vacancy', data, function(rtn) {
                $.unblockUI();
                if (rtn.code == 0) {
                    toastr['success']('Thanks, data has been saved', 'Success!');
                    $('#modal_new_job_vacancy').modal('hide');

                    window.location.reload();
                }else{
                    toastr['error'](rtn.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        });
    });
</script>