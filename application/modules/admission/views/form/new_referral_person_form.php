<form id="form_new_referral" method="POST" action="<?=site_url('admission/referral/new_referrer')?>">
	<div class="form-group">
		<label>Name</label>
		<input type="text" id="ref_name" name="name" class="form-control">
	</div>
	<div class="form-group">
		<label>Street</label>
		<textarea id="ref_street" name="street" class="form-control"></textarea>
	</div>
	<div class="form-group">
		<label>Kecamatan</label>
		<input type="text" id="ref_kecamatan" class="form-control">
		<input type="hidden" id="ref_kecamatan_id" name="district_id" class="form-control">
	</div>
	<div class="form-group row">
		<div class="col-md-4">
			<label>RT</label>
			<input type="text" id="ref_rt" name="rt" class="form-control">
		</div>
		<div class="col-md-4">
			<label>RW</label>
			<input type="text" id="ref_rt" name="rw" class="form-control">
		</div>
		<div class="col-md-4">
			<label>Zip Code</label>
			<input type="text" id="ref_rt" name="zip_code" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label>Phone</label>
		<input type="tel" id="ref_phone" name="phone" class="form-control">
	</div>
	<div class="form-group">
		<label>Email</label>
		<input type="email" id="ref_email" name="email" class="form-control">
	</div>
	<div class="form-group">
		<label>KTP</label>
		<input type="text" id="ref_ktp" name="id_card_number" class="form-control">
	</div>
	<div class="float-right">
		<button id="btn_add_referrer" type="submit" class="btn btn-info">Save</button>
	</div>
</form>

<script>
	$('button#btn_add_referrer').on('click', function(e){
		e.preventDefault();
		let form = $('form#form_new_referral');
		let url = form.attr('action');
		let data = form.serialize();
		
		$.post(url, data, function(rtn){
			if(rtn.code == 0){
				toastr['success'](rtn.referral_code, 'Success!');
				if($.fn.DataTable.isDataTable($('table#table_referral_list'))){
					table_referral_list.ajax.reload();
				}
			}
			else{
				toastr['warning'](rtn.message, 'Warning!');
			}
		}, 'json');
	});
	
	$('input#ref_kecamatan').autocomplete({
		minLength: 3,
		appendTo: $('form#form_new_referral'),
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
			$('input#ref_kecamatan_id').val(id);
		},
		change: function(event, ui){
			if(ui.item === null){
				$('input#ref_kecamatan_id').val('');
				toastr['warning']('Please use the selection provided', 'Warning!');
			}
		}
	});
</script>
