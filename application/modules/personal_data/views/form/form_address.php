<div class="card">
    <div class="card-header">Address</div>
    <div class="card-body">
        <div class="row">
            <form method="post" id="form_edit_address" action="<?=site_url('personal_data/save_address_data')?>">
                <input id="personal_data_id" name="personal_data_id" type="hidden" value="<?=$personal_data_id?>">
                <div class="col-sm-12">
                    <input type="hidden" id="address_id" name="address_id" value="<?=($o_address) ? $o_address->address_id : ''?>">
                    <div class="row">
	                    <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="address_street">Street</label>
                                <input type="text" class="form-control" id="address_street" name="address_street" value="<?=($o_address) ? $o_address->address_street : ''?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="country" class="required_text">Country</label>
                                <!-- <input type="text" class="form-control" id="address_country_name" name="address_country_name" value="<?=($o_address) ? $o_address->country_name : ''?>"> -->
                                <!-- <input type="hidden" name="address_country_id" id="address_country_id" value="<?=($o_address) ? $o_address->country_id : ''?>"> -->
								<select name="address_country_id" id="address_country_id" class="form-control">
								<option value=""></option>
					<?php
						if ($mbo_country) {
							foreach ($mbo_country as $o_country) {
								$selected = (($o_address) AND ($o_address->country_id == $o_country->country_id)) ? "selected='selected'" : '';
					?>
									<option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
					<?php
							}
						}
					?>
								</select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="address_province">Province</label>
                                <input type="text" class="form-control" id="address_province" name="address_province" value="<?=($o_address) ? $o_address->address_province : ''?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="city" class="required_text">City</label>
                                <input type="text" class="form-control" id="address_city" name="address_city" value="<?=($o_address) ? $o_address->address_city : ''?>">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label for="address_district" class="required_text">District / Kecamatan</label>
                                <!-- <input type="text" class="form-control" id="address_district" name="address_district" value="<?=($o_address) ? $o_address->nama_wilayah : ''?>"> -->
                                <!-- <input type="hidden" name="address_district_id" id="address_district_id" value="<?=($o_address) ? $o_address->dikti_wilayah_id : ''?>">
                                <span class="text-danger"><small>You will see the suggestions after typing at least 3 letters. Select ONLY from the suggestion provided</small></span> -->
								<select name="address_district_id" id="address_district_id" class="form-control">
									<option value=""></option>
<?php
	if ($mbo_wilayah) {
		foreach ($mbo_wilayah as $o_wilayah) {
			$selected = (($o_address) AND ($o_address->dikti_wilayah_id == $o_wilayah->id_wilayah)) ? "selected='selected'" : '';
?>
									<option value="<?=$o_wilayah->id_wilayah;?>" <?=$selected;?>><?=$o_wilayah->nama_wilayah;?></option>
<?php
		}
	}
?>
								</select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <label for="sub_district" class="required_text">Sub-District / Kelurahan</label>
                                <input type="text" class="form-control" id="address_sub_district" name="address_sub_district" value="<?=($o_address) ? $o_address->address_sub_district : ''?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="rt">RT</label>
                                <input type="text" class="form-control" id="rt" name="rt" maxlength="3" value="<?=($o_address) ? $o_address->address_rt : ''?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="rw">RW</label>
                                <input type="text" class="form-control" id="rw" name="rw" maxlength="3" value="<?=($o_address) ? $o_address->address_rw : ''?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="zip_code">ZIP Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" value="<?=($o_address) ? $o_address->address_zipcode : ''?>">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" id="save_address_data" class="btn btn-primary pull-right">Save</button>
    </div>
</div>

<script>
	// function countryAutocomplete(el, idcontainer){
	// 	el.autocomplete({
	// 		minLength: 1,
	// 		source: function(request, response){
	// 			var url = '<?=site_url('json/country')?>';
	// 			var data = {
	// 				term: request.term
	// 			};
	// 			$.post(url, data, function(rtn){
	// 				var arr = [];
	// 				arr = $.map(rtn, function(m){
	// 					return {
	// 						id: m.country_id,
	// 						value: m.country_name
	// 					}
	// 				});
	// 				response(arr);
	// 			}, 'json');
	// 		},
	// 		select: function(event, ui){
	// 			var id = ui.item.id;
	// 			idcontainer.val(id);
	// 		},
	// 		change: function(event, ui){
	// 			if(ui.item === null){
	// 				idcontainer.val('');
	// 				el.val('');
	// 				alert('Please use the selection provided');
	// 			}
	// 		}
	// 	});
	// };
	// countryAutocomplete($('input#address_country_name'), $('input#address_country_id'));

	$('select#address_country_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        minimumInputLength: 2
        // dropdownParent: $("#activity_adviser_modal"),
    });
	
	$('select#address_district_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        minimumInputLength: 2
        // dropdownParent: $("#activity_adviser_modal"),
    });
	
	// $('input#address_district').autocomplete({
	// 	minLength: 3,
	// 	source: function(request, response){
	// 		var url = '<?=site_url('json/dikti_wilayah')?>';
	// 		var data = {
	// 			term: request.term
	// 		};
	// 		$.post(url, data, function(rtn){
	// 			var arr = [];
	// 			arr = $.map(rtn, function(m){
	// 				return {
	// 					id: m.id_wilayah,
	// 					value: m.nama_wilayah
	// 				}
	// 			});
	// 			response(arr);
	// 		}, 'json');
	// 	},
	// 	select: function(event, ui){
	// 		var id = ui.item.id;
	// 		$('input#address_district_id').val(id);
	// 	},
	// 	change: function(event, ui){
	// 		if(ui.item === null){
	// 			$('input#address_district').val('');
	// 			// alert('Please use the selection provided');
	// 			toastr['warning']('Please use the selection provided', 'Warning!');
	// 		}
	// 	}
	// });
	
	function save_address_data(){
		return new Promise((resolve, reject) => {
			var form = $('form#form_edit_address');
			$.post(form.attr('action'), form.serialize(), function(rtn){
				resolve(rtn);
			}, 'json');
		}, (err) => {
			reject(err);
		});
	}
	
	$('button#save_address_data').click(function(e){
		e.preventDefault();
		save_address_data().then((res) => {
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
</script>