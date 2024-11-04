<style>
    /* .table-condensed thead tr:nth-child(2),
    .table-condensed tbody {
    display: none
    } */
</style>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="pr_jd_institution_name" class="required_text">Company Name</label>
            <input type="text" class="form-control" name="pr_jd_institution_name" id="pr_jd_institution_name"  value="<?=((isset($employment_data)) AND ($employment_data)) ? $employment_data->institution_name : '';?>">
            <input type="hidden" name="pr_jd_institution_id" id="pr_jd_institution_id" value="<?=((isset($employment_data)) AND ($employment_data)) ? $employment_data->institution_id : '';?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_jd_institution_country" class="required_text">Company Country</label>
            <select  class="form-control" name="pr_jd_institution_country" id="pr_jd_institution_country">
                <option value=""></option>
        <?php
        if ($country_list) {
            $country_id = ((isset($employment_data)) AND ($employment_data)) ? $employment_data->country_id : '';
            foreach ($country_list as $o_country) {
                $selected = ($country_id == $o_country->country_id) ? 'selected="selected"' : '';
        ?>
                <option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
        <?php
            }
        }
        ?>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_jd_institution_city" class="required_text">Company City</label>
            <input type="text" class="form-control" name="pr_jd_institution_city" id="pr_jd_institution_city" value="<?=((isset($employment_data)) AND ($employment_data)) ? $employment_data->address_city : '';?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_jd_job_title" class="required_text">Job Title</label>
            <input type="text" class="form-control" name="pr_jd_job_title" id="pr_jd_job_title" value="<?=((isset($employment_data)) AND ($employment_data)) ? $employment_data->ocupation_name : '';?>">
        </div>
    </div>
    <div class="col-md-8">
        <div class="form-group">
            <label for="pr_jd_working_year" class="required_text">Working Date</label>
            <div class="row">
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="pr_jd_working_start" id="pr_jd_working_start" value="<?=((isset($employment_data)) AND (!is_null($employment_data->academic_year_start_date))) ? date('Y-m', strtotime($employment_data->academic_year_start_date)) : '';?>">
                </div>
                <div class="col-sm-2 text-center">
                    <span>until</span>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="pr_jd_working_end" id="pr_jd_working_end" value="<?=((isset($employment_data)) AND (!is_null($employment_data->academic_year_end_date))) ? date('Y-m', strtotime($employment_data->academic_year_end_date)) : '';?>" <?=((isset($employment_data)) AND (is_null($employment_data->academic_year_end_date))) ? 'disabled="disabled"' : '';?>>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="pr_jd_working_still" name="pr_jd_working_still" <?=((isset($employment_data)) AND (is_null($employment_data->academic_year_end_date))) ? 'checked' : '';?>>
                                    <label class="custom-control-label" for="pr_jd_working_still">Still Working</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="input-group input-group-sm">
                
                <div class="input-group-prepend input-sm">
                    <span class="input-group-text">until</span>
                </div>
                
                <div class="input-group-append">
                    <div class="input-group-text">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="pr_jd_working_still" name="pr_jd_working_still">
                            <label class="custom-control-label" for="pr_jd_working_still">Still Working</label>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
<script>
let stillworking_check = document.getElementById('pr_jd_working_still');
$(function() {
    $('#pr_jd_institution_country').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
    });
    
    $("#pr_jd_working_start, #pr_jd_working_end").datepicker( {
        format: "yyyy-mm",
        viewMode: "months", 
        minViewMode: "months",
        autoclose: true,
        endDate: new Date()
    });

    $('#pr_jd_working_still').change(function(e) {
        if(this.checked) {
            $('#pr_jd_working_end').attr('disabled', 'disabled');
        }
        else {
            $('#pr_jd_working_end').removeAttr('disabled');
        }
    });

    $('#pr_jd_institution_name').autocomplete({
        max:10,
		minLength: 3,
		source: function(request, response){
			var url = '<?=site_url('institution/get_institutions')?>';
			var data = {
				term: request.term
			};
            
			$.post(url, data, function(rtn){
				if(rtn.code == 0){
					var arr = [];
					arr = $.map(rtn.data, function(m){
						return {
							id: m.institution_id,
							value: m.institution_name,
                            univ_mail: m.institution_email,
                            univ_phone: m.institution_phone_number,
                            countryid: m.country_id,
                            countryname: m.country_name,
                            cityname: m.address_city
						}
					});
					// response(arr);
					response(arr.slice(0, 10));
				}
			}, 'json');
		},
		select: function(event, ui){
			var id = ui.item.id;
            var newOption = new Option(ui.item.countryname, ui.item.countryid, false, false);

            $('#pr_jd_institution_country').append(newOption).trigger('change');
			$('#pr_jd_institution_id').val(id);
            $('#pr_jd_institution_city').val(ui.item.cityname);
            $('select#pr_jd_institution_country').val(ui.item.countryid).trigger('change');
		},
		change: function(event, ui){
			if(ui.item === null){
				$('#pr_jd_institution_id').val('');
				$('#pr_jd_institution_city').val('');
				$('select#pr_jd_institution_country').val('').trigger('change');
			}
		}
	});

    $('#pr_jd_job_title').autocomplete({
        max:10,
		minLength: 3,
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
							value: m.ocupation_name,
						}
					});
					// response(arr);
					response(arr.slice(0, 10));
				}
			}, 'json');
		},
	});
})
</script>