<form id="create_invoice" name="create_invoice" method="post" action="<?=site_url('finance/invoice/create_initial_tuition_fee')?>">
	<input type="hidden" name="personal_data_id" id="personal_data_id">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
		        <label>Name</label>
		        <input class="form-control" id="personal_data_name" name="personal_data_name" readonly>
		    </div>
		    <div class="form-group">
		        <label>Study Program</label>
		        <input class="form-control" id="study_program_name" readonly>
		        <input type="hidden" name="study_program_id" id="study_program_id">
		    </div>
		    <div class="form-group">
		        <label>Study Fee</label>
		        <input class="form-control" id="fee_amount_display" readonly>
		        <input type="hidden" name="fee_id" id="fee_id">
		        <input type="hidden" id="fee_amount">
		    </div>
		    <div class="form-group">
			    <label>Payment Deadline</label>
			    <input class="form-control deadline" id="deadline" name="deadline[]">
		    </div>
		    <div class="form-group">
			    <label>Discount</label>
			    <select class="form-control selectpicker" id="discount" name="discount[]" multiple data-live-search="true" data-actions-box="true">
				    <option value="">Not Applied</option>
				    <?php
					    if($discounts){
						    foreach($discounts as $discount){
					?>
					<option data-value="<?=$discount->fee_amount?>" data-type="<?=$discount->fee_amount_number_type?>" value="<?=$discount->fee_id?>">
						<?=$discount->fee_description?> @ <?=($discount->fee_amount_number_type == 'number') ? "Rp. ".number_format($discount->fee_amount, 2, ",", ".") : $discount->fee_amount."%";?>
					</option>
					<?php
							}
					    }
					?>
			    </select>
		    </div>
		    <div class="form-group">
			    <?php
				    if($additional_fees){
					    foreach($additional_fees as $additional_fee){
						    $disabled = '';
						    if($additional_fee->fee_id == 'a1f3f9d3-a9ef-11e9-9ee5-5254005d90f6'){
							    $disabled = 'checked';
						    }
				?>
				<div class="form-check checkbox">
					<input class="form-check-input" id="additional_fees" type="checkbox" name="additional_fees[]" data-value="<?=$additional_fee->fee_amount?>" value="<?=$additional_fee->fee_id?>" <?=$disabled?>>
					<label class="form-check-label"><?=$additional_fee->fee_description?> @ Rp. <?=number_format($additional_fee->fee_amount, 2, ",", ".")?></label>
				</div>
			    <?php
				    	}
				    }
				?>
			</div>
			<div class="form-group">
				<label>Total <span id="total_display"></span></label>
				<input type="hidden" id="total" name="total">
			</div>
		</div>
		<div class="d-none">
			<?php
			for($i = 1; $i <= 6; $i++){
			?>
			<div class="form-group">
		        <label>Installment/Payment Deadline <?=$i?></label>
		        <div class="row">
			        <div class="col-md-6">
				        <input class="form-control" id="installments" name="installments[]">
			        </div>
			        <div class="col-md-6">
				        <input class="form-control deadline" id="deadline_<?=$i?>" name="deadline[]">
			        </div>
		        </div>
		    </div>
			<?php	
			}	
			?>
		</div>
	</div>
    <button class="btn btn-md btn-info float-right" id="create_invoice">Create & Send Tuition Fee</button>
</form>

<!-- <link href="<?=base_url()?>assets/vendors/bootstrap-daterangepicker/css/daterangepicker.min.css" rel="stylesheet"> -->
<script src="<?=base_url()?>assets/vendors/moment/js/moment.min.js"></script>
<!-- <script src="<?=base_url()?>assets/vendors/bootstrap-daterangepicker/js/daterangepicker.js"></script> -->

<script>
	let base_study_fee = 0;
	let date_options = {
		singleDatePicker: true,
		showDropDowns: true,
		minDate: new Date(),
		locale: {
			format: 'YYYY-MM-DD',
			firstDay: 1
		},
		autoclose: true,
		disableTouchKeyboard: true
	};

	var selectmulti = $('#discount').selectpicker();
	$('.selectpicker').selectpicker('refresh');
	
	function reinstantiateDate(inputObj, dateObj){
		inputObj.daterangepicker(date_options);
	};
	
	$('.deadline').daterangepicker(date_options)
	.on('hide.daterangepicker', function(obj){
		if($(this).attr('id') == 'deadline_1'){
			var objDate = new Date($(this).val());
			for(i = 2; i <= 6; i++){	
				objDate.setMonth(objDate.getMonth()+1);
				objDate.setDate(10);
				var tfx = $('input#deadline_'+i);
				tfx.data('daterangepicker').setStartDate(objDate);
			}
		}
	});
	
	$('button#create_invoice').on('click', function(e){
		e.preventDefault();
		var r = confirm('Are you sure? Please check before again before you submit');
		if(r === true){
			var rr = confirm('Please note that this can not be undone, go back while you can');
			if(rr === true){
				$.blockUI({ baseZ: 2000, message: 'Please wait...' });
				var form = $('form#create_invoice');
				$.post(form.attr('action'), form.serialize(), function(rtn){
					if(rtn.code == 0){
						$('#initial_tuition_fee_invoice_modal').modal('hide');
						toastr['success']('Email has been sent', 'Success!');
						$.unblockUI();
					}
					else{
						toastr['warning'](rtn.message, 'Warning!');
					}
				}, 'json');
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	});
	
	function get_tuition_fee(data){
		return new Promise((resolve, reject) => {
			$.post('<?=site_url('finance/invoice/get_initial_tuition_fee')?>', data, function(rtn){
				if(rtn.code == 0){
					resolve(rtn.data);
				}
				else{
					reject(rtn.message);
				}
			}, 'json').fail(function(params) {
				reject('Something wrong...');
			});
		}, (error) => {
			reject('error');
		});
	}
	
	$('select#discount, input#additional_fees').change(function(e){
		e.preventDefault();
		calculate_tuition_fee();
	});
	
	function calculate_tuition_fee(){
		let total_study_fee = parseInt(base_study_fee);
		// let discount = $('select#discount').find('option:selected').data();
		// console.log(discount);
		// console.log($('select#discount').val());
		let additional_fees = $('input#additional_fees');
		let installments = $('input#installments');
		let number_of_installments = installments.length;
		let additional_fee_amount = 0;

		let discount_selected = $('select#discount').val();
		var discount_total = 0;
		if (discount_selected.length > 0) {
			$.each(discount_selected, function(i, v) {
				if (v == '') {
					discount_total = 0;
					$('select#discount').val('');
					$('.selectpicker').selectpicker('refresh');
					return false;
				}else{
					var data_type = $("option[value=" + v + "]", $('select#discount')).attr('data-type');
					if (data_type !== undefined) {
						var data_number = $("option[value=" + v + "]", $('select#discount')).attr('data-value');
						data_number = parseInt(data_number);
						switch (data_type) {
							case 'number':
								discount_total += data_number;
								break;

							case 'percentage':
								var percentage_number = total_study_fee * (data_number/100);
								discount_total += percentage_number;
								break;
						
							default:
								break;
						}
						console.log(discount_total);
					}
				}
			});
		}

		total_study_fee -= discount_total;

		let installment_study_fee = Math.round(total_study_fee/number_of_installments);
		
		additional_fees.each(function(k, v){
			// console.log($(this).data('value'));
			if($(this).is(':checked')){
				total_study_fee += $(this).data('value');
				additional_fee_amount += $(this).data('value');
			}
		});
		
		let i = 0;
		installments.each(function(k, v){
			if(i == 0){
				first_installment = installment_study_fee + additional_fee_amount;
				$(this).val(first_installment);
			}
			else{
				$(this).val(installment_study_fee);
			}
			i++;
		});

		$('span#total_display').html(formatter.format(total_study_fee));
		$('input#total').val(total_study_fee);
	}
	
	if($.fn.DataTable.isDataTable('table#student_list_table')){
		console.log('true');
		$('table#student_list_table tbody').on('click', 'button#btn_initial_tuition_fee_modal', function(e){
			e.preventDefault();
			var data = student_list_table.row($(this).parents('tr')).data();
			
			var form = $('form#create_invoice');
			if(
				(data['student_status'] === 'candidate') 
				&& (data['study_program_id'] !== null)
				&& (data['personal_data_date_of_birth'])
			){
				$('#initial_tuition_fee_invoice_modal').modal('show');
				var tuition_fee_clause = {
					semester_id: '1',
					study_program_id: data['study_program_id'],
					finance_year: data['finance_year_id'],
					student_class_type: data['student_class_type']
				};
				get_tuition_fee(tuition_fee_clause)
				.then((res) => {
					data['fee_id'] = res[0].fee_id;
					data['fee_amount_display'] = formatter.format(res[0].fee_amount);
					data['fee_amount'] = base_study_fee = res[0].fee_amount;

					$('select#discount').val('');
					$('.selectpicker').selectpicker('refresh');
					// console.log(form[0]);
					
					for(let i = 0; i < form[0].elements.length; i++){
						var element_id = form[0].elements[i].id;
						if (element_id !== '') {
							var element_value = data[element_id];
							if($('#'+element_id).length){
								if(data[element_id] !== undefined){
									$('#'+element_id).val(element_value);
									$('#'+element_id).data('value', element_value);
								}
							}
						}
						// console.log(element_id);
						
					}
					calculate_tuition_fee();
				}).catch((err) => {
					toastr['error'](err, 'Warning!');
					console.log(err);
				});
			}
			else{
				toastr['warning'](
					'Student must be participant, has chosen study program and has filled date of birth to generate the initial tuition fee',
					'Warning!'
				);
			}
		});
	}
	
/*
	$('button#create_invoice').on('click', function(e){
		e.preventDefault();
		var form = $('form#send_email');
		$.post(form.attr('action'), form.serialize(), function(rtn){
			if(rtn.code == 0){
				console.log('terkirim');
			}
		}, 'json');
	});
*/
</script>