<form method="post" id="form_edit_address" action="<?=site_url('address/save_address_data')?>">
    <input id="personal_data_id" name="personal_data_id" type="hidden" value="<?= $personal_data_id; ?>">
    <div class="col-sm-12">
        <input type="hidden" id="address_id" name="address_id" value="<?=($o_address) ? $o_address->address_id : ''?>">
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Address Name</label>
                    <input type="text" class="form-control" id="address_name" name="address_name" value="<?=($o_address) ? $o_address->personal_address_name : ''?>">
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="form-group">
                    <label for="address_street">Street</label>
                    <input type="text" class="form-control" id="address_street" name="address_street" value="<?=($o_address) ? $o_address->address_street : ''?>">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" id="address_country_name" name="address_country_name" value="<?=($o_address) ? $o_address->country_name : ''?>">
                    <input type="hidden" name="address_country_id" id="address_country_id" value="<?=($o_address) ? $o_address->country_id : ''?>">
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
                    <label for="city">City</label>
                    <input type="text" class="form-control" id="address_city" name="address_city" value="<?=($o_address) ? $o_address->address_city : ''?>">
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="address_district">District / Kecamatan</label>
                    <input type="text" class="form-control" id="address_district" name="address_district" value="<?=($o_address) ? $o_address->nama_wilayah : ''?>">
                    <input type="hidden" name="address_district_id" id="address_district_id" value="<?=($o_address) ? $o_address->dikti_wilayah_id : ''?>">
                    <span class="text-danger"><small>You will see the suggestions after typing at least 3 letters. Select ONLY from the suggestion provided</small></span>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="sub_district">Sub-District / Kelurahan</label>
                    <input type="text" class="form-control" id="address_sub_district" name="address_sub_district" value="<?=($o_address) ? $o_address->address_sub_district : ''?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="rt">RT</label>
                    <input type="text" class="form-control" id="rt" name="rt" maxlength="2" value="<?=($o_address) ? $o_address->address_rt : ''?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="rw">RW</label>
                    <input type="text" class="form-control" id="rw" name="rw" maxlength="2" value="<?=($o_address) ? $o_address->address_rw : ''?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="zip_code">ZIP Code</label>
                    <input type="text" class="form-control" id="zip_code" name="zip_code" value="<?=($o_address) ? $o_address->address_zipcode : ''?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?=($o_address) ? $o_address->address_phonenumber : ''?>">
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary float-right" type="button" id="save_address_data">Save</button>
            </div>
        </div>
    </div>
</form>
<script>
	$('#rt, #rw').mask('99');
	
    function countryAutocomplete(el, idcontainer){
		el.autocomplete({
            appendTo: $('#form_edit_address'),
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
	countryAutocomplete($('input#address_country_name'), $('input#address_country_id'));
	
	$('input#address_district').autocomplete({
		minLength: 3,
        appendTo: $('#form_edit_address'),
		source: function(request, response){
			var url = '<?=site_url('json/dikti_wilayah')?>';
			var data = {
				term: request.term
			};
			$.post(url, data, function(rtn){
				var arr = [];
				arr = $.map(rtn, function(m){
					return {
						id: m.id_wilayah,
						value: m.nama_wilayah
					}
				});
				response(arr);
			}, 'json');
		},
		select: function(event, ui){
			var id = ui.item.id;
			$('input#address_district_id').val(id);
		},
		change: function(event, ui){
			if(ui.item === null){
				$('input#address_district').val('');
                toastr['warning']('Please use the selection provided', 'Warning');
			}
		}
	});
	
	$('button#save_address_data').on('click', function(e){
		e.preventDefault();

        $.blockUI({
            baseZ: 2000
        });
        $('div#new_address_history_modal').modal('hide');
		var form = $('form#form_edit_address');
        
		$.post(form.attr('action'), form.serialize(), function(rtn){
			if(rtn.code == 0){
				toastr['success']('address data has been saved', 'Success');
                // window.location.reload();
			}
			else{
				toastr['warning'](rtn.message, 'Warning!');
			}
            $.unblockUI();
		}, 'json').fail(function(xhr, txtStatus, errThrown) {
            $.unblockUI();
        });
	});
</script>