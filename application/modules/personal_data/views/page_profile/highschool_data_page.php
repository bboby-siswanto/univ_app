<div class="row">
    <div class="col-md-5">
        <div class="form-group">
            <label for="pr_hs_institution_name" class="required_text">Highschool Name</label>
            <input type="text" class="form-control" name="pr_hs_institution_name" id="pr_hs_institution_name" value="<?=((isset($highschool_data)) AND ($highschool_data)) ? $highschool_data->institution_name : '';?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_hs_nisn_number" class="required_text">NISN (Student Number)</label>
            <input type="text" class="form-control" name="pr_hs_nisn_number" id="pr_hs_nisn_number" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->student_nisn : '';?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_hs_graduation_year" class="required_text">Graduation Year</label>
            <input type="text" class="form-control" name="pr_hs_graduation_year" id="pr_hs_graduation_year" value="<?=((isset($highschool_data)) AND ($highschool_data)) ? $highschool_data->academic_history_graduation_year : '';?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_hs_email">Highschool Email</label>
            <input type="text" class="form-control" name="pr_hs_email" id="pr_hs_email" value="<?=((isset($highschool_data)) AND ($highschool_data)) ? $highschool_data->institution_email : '';?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_hs_phone_number">Highschool Phone Number</label>
            <input type="text" class="form-control" name="pr_hs_phone_number" id="pr_hs_phone_number" value="<?=((isset($highschool_data)) AND ($highschool_data)) ? $highschool_data->institution_phone_number : '';?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="pr_hs_country" class="required_text">Highschool Country</label>
            <select  class="form-control" name="pr_hs_country" id="pr_hs_country">
                <option value=""></option>
        <?php
        if ($country_list) {
            $hs_country_id = ((isset($highschool_data)) AND ($highschool_data)) ? $highschool_data->country_id : '';
            foreach ($country_list as $o_country) {
                $selected = ($hs_country_id == $o_country->country_id) ? 'selected="selected"' : '';
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
            <label for="pr_hs_city" class="required_text">Highschool City</label>
            <input type="text" class="form-control" name="pr_hs_city" id="pr_hs_city" value="<?=((isset($highschool_data)) AND ($highschool_data)) ? $highschool_data->address_city : '';?>">
        </div>
    </div>
</div>
<hr>
<?php
// $transfer_checked = ((isset($student_data)) AND ($student_data->student_type == 'transfer')) ? 'checked' : '';
?>
<div class="row">
    <div class="col-12 mb-2">
        <label for="pr_hs_transfer">Are you transfer student?</label>
        <div class="custom-control custom-switch switch-transfer-student">
            <input class="custom-control-input" type="checkbox" id="pr_hs_transfer_student" name="pr_hs_transfer_student">
            <label class="custom-control-label" for="pr_hs_transfer_student">No</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-7 univ-transfer">
        <div class="form-group">
            <label for="pr_hs_university_name" class="required_text">University Name</label>
            <input type="text" class="form-control" name="pr_hs_university_name" id="pr_hs_university_name">
        </div>
    </div>
    <div class="col-md-5 univ-transfer">
        <div class="form-group">
            <label for="pr_hs_university_prodi" class="required_text">Study Program</label>
            <input type="text" class="form-control" name="pr_hs_university_prodi" id="pr_hs_university_prodi">
        </div>
    </div>
    <div class="col-md-4 univ-transfer">
        <div class="form-group">
            <label for="pr_hs_university_country" class="required_text">Country</label>
            <select  class="form-control" name="pr_hs_university_country" id="pr_hs_university_country">
                <option value=""></option>
        <?php
        if ($country_list) {
            foreach ($country_list as $o_country) {
        ?>
                <option value="<?=$o_country->country_id;?>"><?=$o_country->country_name;?></option>
        <?php
            }
        }
        ?>
            </select>
        </div>
    </div>
    <div class="col-md-4 univ-transfer">
        <div class="form-group">
            <label for="pr_hs_university_city" class="required_text">City</label>
            <input type="text" class="form-control" name="pr_hs_university_city" id="pr_hs_university_city">
        </div>
    </div>
    <div class="col-md-4 univ-transfer">
        <div class="form-group">
            <label for="pr_hs_university_ipk">IPK</label>
            <input type="text" class="form-control" name="pr_hs_university_ipk" id="pr_hs_university_ipk">
        </div>
    </div>
</div>
<script>
$(function() {
    let student_type = '<?= ((isset($student_data)) AND ($student_data)) ? $student_data->student_type : '' ?>';
    
    if (student_type == 'transfer') {
        $('#pr_hs_transfer_student').prop('checked', true);
        $('.univ-transfer').addClass('univ-transfer-show');
        $('.switch-transfer-student label').text('Yes');
    }
    
    $("#pr_hs_transfer_student").change(function() {
        if(this.checked) {
            $('.switch-transfer-student label').text('Yes');
            $('.univ-transfer').addClass('univ-transfer-show');
        }
        else {
            $('.switch-transfer-student label').text('No');
            $('.univ-transfer').removeClass('univ-transfer-show');
        }
    });
    
    $('#pr_hs_university_country').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
    });

    $('#pr_hs_university_name').autocomplete({
        max:10,
		minLength: 3,
		source: function(request, response){
			var url = '<?=site_url('institution/get_institutions')?>';
			var data = {
				term: request.term,
                type: 'university'
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
})
</script>