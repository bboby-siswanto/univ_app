<form method="post" id="new_institution_form" action="<?=site_url('institution/save_institution')?>" class="form form-horizontal">
	<input type="hidden" name="institution_id" id="institution_id">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label>Name</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-building"></i>
				        </span>
			        </div>
			        <input value="<?=(isset($institution_data)) ? $institution_data->institution_name : ''?>" class="form-control" type="text" id="institution_name" name="institution_name" placeholder="Institution name" required="true" autofocus="true">
			    </div>
			</div>
			<div class="form-group">
				<label>Type</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-university"></i>
				        </span>
			        </div>
			        <select class="form-control" id="institution_type" name="institution_type" required="true">
					<?php
					foreach($institution_type as $institution){
					?>
					<option value="<?=$institution?>" <?=(isset($institution_data) AND $institution_data->institution_type == $institution) ? 'selected' : ''?>>
						<?=strtoupper($institution)?>
					</option>
					<?php
					}
					?>
			        </select>
			    </div>
			</div>
			<div class="form-group">
				<label>International Institution</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-globe"></i>
				        </span>
			        </div>
			        <select class="form-control" id="institution_is_international" name="institution_is_international" required="true">
					<?php
					foreach($institution_is_international as $institution){
					?>
					<option value="<?=$institution?>" <?=(isset($institution_data) AND $institution_data->institution_is_international == $institution) ? 'selected' : ''?>>
						<?=strtoupper($institution)?>
					</option>
					<?php
					}
					?>
			        </select>
			    </div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Email</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-at"></i>
				        </span>
			        </div>
			        <input value="<?=(isset($institution_data)) ? $institution_data->institution_email : ''?>" class="form-control" type="email" id="institution_email" name="institution_email" placeholder="Institution email">
			    </div>
			</div>
			<div class="form-group">
				<label>Phone</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-phone"></i>
				        </span>
			        </div>
			        <input value="<?=(isset($institution_data)) ? $institution_data->institution_phone_number : ''?>" class="form-control" type="text" id="institution_phone_number" name="institution_phone_number" placeholder="Institution phone" required="true" oninput="this.value=this.value.replace(/[^\d\+]/,'')">
			    </div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<label>Street</label>
				<input type="hidden" id="address_id" name="address_id" value="<?=(isset($institution_data)) ? $institution_data->address_id : ''?>">
				<textarea class="form-control" id="address_street" name="address_street">
					<?=(isset($institution_data)) ? $institution_data->address_street : ''?>
				</textarea>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Country</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-flag"></i>
				        </span>
			        </div>
			        <select class="form-control select2-single" id="country_id" name="country_id" required></select>
			    </div>
			</div>
			<div class="form-group">
				<label>Province</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-road"></i>
				        </span>
			        </div>
			        <input value="<?=(isset($institution_data)) ? $institution_data->address_province : ''?>" class="form-control" type="text" id="address_province" name="address_province" placeholder="Province" required="true">
			    </div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label>City</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-building-o"></i>
				        </span>
			        </div>
			        <input value="<?=(isset($institution_data)) ? $institution_data->address_city : ''?>" class="form-control" type="text" id="address_city" name="address_city" placeholder="City" required="true">
			    </div>
			</div>
			<div class="form-group">
				<label>Zip Code</label>
				<div class="input-group mb-3">
			        <div class="input-group-prepend">
				        <span class="input-group-text">
				            <i class="fa fa-archive"></i>
				        </span>
			        </div>
			        <input value="<?=(isset($institution_data)) ? $institution_data->address_zipcode : ''?>" class="form-control" type="number" id="address_zipcode" name="address_zipcode" placeholder="Zip Code" required="true">
			    </div>
			</div>
		</div>
	</div>
    <button type="button" class="btn btn-primary float-right" id="save_institution">Save</button>
</form>

<script>
	if($.fn.DataTable.isDataTable('table#institution_list_table')){
		$('table#institution_list_table tbody').on('click', 'button#btn_institution_edit_modal', function(e){
			e.preventDefault();
			var data = institution_list_table.row($(this).parents('tr')).data();
			console.log(data);
			$.each(data, function(k, v){
				$('#'+k).val(v);
			});
			
			$('div#save_or_edit_institution_modal').modal('toggle');
		});
	}
	
    var institution_form = $('form#new_institution_form');
    
    $('button#save_institution').on('click', function(e){
       e.preventDefault();
       
       $.post(institution_form.attr('action'), institution_form.serialize(), function(rtn){
           if(rtn.code == 0){
	           institution_form[0].reset();
	           $('input').val('');
	           
               toastr['success']('Data has been saved', 'Success!');
               if($.isFunction(window.show_institution_table)){
                   show_institution_table({
                       institution_type: 'all'
                   });
                   $('div#save_or_edit_institution_modal').modal('toggle');
                }
                else{
                    window.location.reload();
                }
           }
           else{
               toastr['warning'](rtn.message, 'Warning!');
           }
       }, 'json');
    });
    
    $('input#country_name').autocomplete({
		minLength: 1,
		appendTo: '.modal-body',
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
			}
		}
	});
</script>