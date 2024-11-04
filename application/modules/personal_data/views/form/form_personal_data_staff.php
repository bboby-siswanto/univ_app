<?php
if(!is_null($o_personal_data->personal_data_date_of_birth)){
	$s_dob_date = date('d', strtotime($o_personal_data->personal_data_date_of_birth));
	$s_dob_month = date('m', strtotime($o_personal_data->personal_data_date_of_birth));
	$s_dob_year = date('Y', strtotime($o_personal_data->personal_data_date_of_birth));
}
else{
	$s_dob_date = $s_dob_month = $s_dob_year = null;
}

$s_user_type = $this->session->userdata('type');
$disable_input = '';
$class_hide = 'd-none';
?>

<div class="card">
    <div class="card-header">Your Personal Data</div>
    <div class="card-body">
        <form method="post" id="form_personal_data" action="<?=site_url('personal_data/save_personal_data')?>">
            <div class="row justify-content-start">
                <input id="personal_data_id" name="personal_data_id" type="hidden" value="<?=$o_personal_data->personal_data_id?>">
                <div class="col-lg-4">
                    <label for="profile_picture">Profile Picture</label><br>
	                <?php
		            if($a_avatar){
			        ?>
			        <img src="<?=site_url('file_manager/view/'.$a_avatar[0]->document_id.'/'.$o_personal_data->personal_data_id)?>" class="img-fluid img-thumbnail" style="max-height: 300px;">
			        <?php
		            }  
		            ?>
                    <br>
		            <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                </div>
                <div class="col-lg-8">
                    <div class="row justify-content-around">
                        <div class="col-lg-12">
                            <div class="form-group pt-3">
                                <label for="email" class="required_text">Name</label>
                                <input class="form-control" id="name" name="name" type="text" value="<?=$o_personal_data->personal_data_name?>" <?=$disable_input;?>>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="email" class="required_text">Email</label>
                                <input class="form-control" id="email" name="email" type="email" value="<?=$o_personal_data->personal_data_email?>">
                            </div>
                        </div>
                        <div class="col-lg-7">
							<div class="form-group">
								<label for="identification_number" class="required_text">Your Identification Number</label>
								
                                <div class="input-group">
                                    <input type="text" class="form-control" id="identification_number" name="identification_number" value="<?=$o_personal_data->personal_data_id_card_number?>">
                                    <select class="form-control" id="identification_type" name="identification_type">
                                        <option value="national_id" <?= ($o_personal_data->personal_data_id_card_type == 'national_id') ? 'selected="selected"' : '' ?>>NIK</option>
                                        <option value="passport" <?= ($o_personal_data->personal_data_id_card_type == 'passport') ? 'selected="selected"' : '' ?>>Passport</option>
                                    </select>
                                </div>
							</div>
						</div>
                        <div class="col-lg-4">
                            <label for="phone_number">Phone Number</label>
                            <input class="form-control" id="phone_number" name="phone_number" type="text" value="<?=$o_personal_data->personal_data_phone?>">
                        </div>
                        <div class="col-lg-4">
                            <label for="cellular_number" class="required_text">Cellular Number</label>
                            <input class="form-control" id="cellular_number" name="cellular_number" type="text" value="<?=$o_personal_data->personal_data_cellular?>">
                        </div>
                        <div class="col-lg-4">
							<div class="form-group">
								<label class="required_text">Mother Maiden Name</label>
								<input type="text" class="form-control" id="personal_data_mother_maiden_name" name="personal_data_mother_maiden_name" value="<?= $o_personal_data->personal_data_mother_maiden_name ?>" <?=$disable_input;?>>
							</div>
						</div>
                        <div class="col-lg-6">
							<div class="form-group">
								<label for="birth_country" class="required_text">Country of Birth</label>
								<select name="birth_country_id" id="birth_country_id" class="form-control">
									<option value=""></option>
<?php
	if ($mba_country) {
		foreach ($mba_country as $o_country) {
			$selected = (($o_personal_data) AND ($o_personal_data->country_of_birth == $o_country->country_id)) ? "selected='selected'" : '';
?>
									<option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
<?php
		}
	}
?>
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="birth_place" class="required_text">Place of Birth</label>
								<input type="text" class="form-control" id="placeofbirth" name="placeofbirth" value="<?=$o_personal_data->personal_data_place_of_birth?>" <?=$disable_input;?>>
							</div>
						</div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="required_text">Birthday</label>
                                <div class="row">
                                    <div class="col">
                                        <select class="form-control" id="bdate" name="bdate" <?=$disable_input;?>>
                                            <option>---</option>
                                            <?php
                                            for($di = 1; $di <= 31; $di++){
                                                if($di == $s_dob_date){
                                            ?>
                                            <option value="<?=$di?>" selected><?=$di?></option>
                                            <?php	
                                                }
                                                else{
                                            ?>
                                            <option value="<?=$di?>"><?=$di?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control" id="bmonth" name="bmonth" <?=$disable_input;?>>
                                            <option>---</option>
                                            <?php
                                            $a_months = array(
                                                '1' => 'January',
                                                '2' => 'February',
                                                '3' => 'March',
                                                '4' => 'April',
                                                '5' => 'May',
                                                '6' => 'June',
                                                '7' => 'July',
                                                '8' => 'August',
                                                '9' => 'September',
                                                '10' => 'October',
                                                '11' => 'November',
                                                '12' => 'December'
                                            );
                                            foreach($a_months as $s_key => $s_month){
                                                if($s_key == $s_dob_month){
                                            ?>
                                            <option value="<?=$s_key?>" selected><?=$s_month?></option>
                                            <?php
                                                }
                                                else{
                                            ?>
                                            <option value="<?=$s_key?>"><?=$s_month?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control" id="byear" name="byear" <?=$disable_input;?>>
                                            <option>---</option>
                                            <?php
                                            $s_current_year = date('Y', time());
                                            $s_end_year = date('Y', strtotime($s_current_year." -15 year"));
                                            for($s_start_year = date('Y', strtotime($s_current_year." -30 year")); $s_start_year <= $s_end_year; $s_start_year++){
                                                if($s_start_year == $s_dob_year){
                                            ?>
                                            <option value="<?=$s_start_year?>" selected><?=$s_start_year?></option>
                                            <?php
                                                }
                                                else{
                                            ?>
                                            <option value="<?=$s_start_year?>"><?=$s_start_year?></option>
                                            <?php	
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 text-danger">
                                        <small>Please be extra careful with your birthday. Candidates often place current year as their birthday</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="gender" class="required_text">Gender</label>
                                <select type="text" class="form-control" id="gender" name="gender">
                                    <option value="">---</option>
                                    <option value="M" <?= ($o_personal_data->personal_data_gender == 'M') ? 'selected' : ''; ?>>Male</option>
                                    <option value="F" <?= ($o_personal_data->personal_data_gender == 'F') ? 'selected':'' ; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label for="religion" class="required_text">Religion</label>
                            <select class="form-control" id="religion" name="religion">
                                <option value="">---</option>
                                <?php
                                    if($a_religions){
                                        foreach ($a_religions as $religion) {
                                ?>
                                <option value="<?=$religion->religion_id?>" <?=($o_personal_data->religion_id == $religion->religion_id) ? 'selected' : '' ?>><?=$religion->religion_name?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="gender" class="required_text">Nationality</label>
                            <select type="text" class="form-control" id="nationality" name="nationality">
                                <option value="">---</option>
                                <option value="WNI" <?= ($o_personal_data->personal_data_nationality == 'WNI') ? 'selected' : '' ?>>WNI</option>
                                <option value="WNA" <?= ($o_personal_data->personal_data_nationality == 'WNA') ? 'selected' : '' ?>>WNA</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="citizenship" class="required_text">Citizenship</label>
                            <select name="citizenship_id" id="citizenship_id" class="form-control">
                                <option value=""></option>
    <?php
    if ($mba_country) {
    foreach ($mba_country as $o_country) {
        $selected = (($o_personal_data) AND ($o_personal_data->citizenship_id == $o_country->country_id)) ? "selected='selected'" : '';
    ?>
                                <option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
    <?php
    }
    }
    ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    
                    
                    <div class="form-group">
                        
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                
                            </div>
                            <div class="col">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button type="button" id="save_personal_data" class="btn btn-primary pull-right">Save</button>
    </div>
</div>



<script>
	function countryAutocomplete(el, idcontainer){
		el.autocomplete({
			minLength: 1,
			source: function(request, response){
				var url = '<?=site_url('json/country')?>';
				var data = {
					term: request.term
				};
				$.post(url, data, function(rtn){
					var arr = [];
					arr = $.map(rtn, function(m){
						return {
							id: m.country_id,
							value: m.country_name
						}
					});
					response(arr);
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				idcontainer.val(id);
			},
			change: function(event, ui){
				if(ui.item === null){
					idcontainer.val('');
					el.val('');
					alert('Please use the selection provided');
				}
			}
		});
	};
	
	// countryAutocomplete($('input#birth_country'), $('input#birth_country_id'));
	// countryAutocomplete($('input#citizenship_name'), $('input#citizenship_id'));
	$('select#citizenship_id, select#birth_country_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        minimumInputLength: 2
        // dropdownParent: $("#activity_adviser_modal"),
    });
	
	function save_personal_data(){
		if (validate_nik()) {
			return new Promise((resolve, reject) => {
				var personal_data_form = $('form#form_personal_data');
				var profile_form_data = new FormData(personal_data_form[0]);
				
				$.ajax({
					url: personal_data_form.attr('action'),
					data: profile_form_data,
					cache: false,
					contentType: false,
					processData: false,
					method: 'POST',
					dataType: 'json',
					success: function(rtn){
						resolve(rtn);
					}
				});
			}, (err) => {
				reject(err);
			});
		}
	}
	
	$('button#save_personal_data').click(function(e){
		e.preventDefault();
		save_personal_data().then((res) => {
			if(res.code != 0){
				toastr['warning'](res.message, 'Warning!');
			}
			else{
				toastr['success']('Data saved', 'Success!');
				setTimeout( function(){ 
					location.reload();
				}  , 3000 );
			}
		}).catch((err) => {
			toastr['error']('Error processing data', 'Error!');
			console.log(err);
		});
	});

	function validate_nik() {
		if ($('#nationality').val() == 'WNI') {
			var nik = $('#identification_number').val();
			if (nik.length != 16) {
				toastr.warning('You must fill identification number correctly!', 'Warning!');
				return false;
			}
		}

		return true;
	}
</script>