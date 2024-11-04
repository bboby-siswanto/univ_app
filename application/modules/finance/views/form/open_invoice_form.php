<div class="card">
	<div class="card-body">
		<form class="form" id="new_invoice_form" action="<?=site_url('finance/invoice/new_invoice')?>">
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="payment_type_code">Payment Type</label>
                        <select class="form-control" id="payment_type_code" name="payment_type_code" placeholder="Please select...">
                            <?php
                            foreach($payment_type as $val){
                            ?>
                            <option value="<?=$val->payment_type_code?>"><?=$val->payment_type_code?> - <?=$val->payment_type_name?></option>
                            <?php
                            }	
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="payment_type_code">Virtual Account</label>
                        <select class="form-control" id="payment_type_code" name="payment_type_code" placeholder="Please select...">
                            <?php
                            foreach($payment_type as $val){
                            ?>
                            <option value="<?=$val->payment_type_code?>"><?=$val->payment_type_code?> - <?=$val->payment_type_name?></option>
                            <?php
                            }	
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-12"><hr></div>
                <div class="col-md-8">
                    <div class="form-group student_input">
                        <label for="student_name">Customer Name</label>
                        <input type="text" name="personal_data_id" id="personal_data_id_new_invoice">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="finance_batch">Student Entry Year</label>
                        <select class="form-control" id="finance_batch" name="finance_batch" placeholder="Please select...">
                            <?php
                            foreach($batch as $val){
                            ?>
                            <option value="<?=$val->academic_year_id?>"><?=$val->academic_year_id?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="row">
                        <div id="semester_invoice" class="col-md-12">
                            <div class="form-group">
                                <label for="semester_id">Semester</label>
                                <select class="form-control" id="semester_id" name="semester_id" placeholder="Please select...">
                                    <option value="">Not Applied</option>
                                    <?php
                                    foreach($semester as $val){
                                    ?>
                                    <option value="<?=$val->semester_id?>"><?=$val->semester_number?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="input_sks">
                            <div class="form-group">
                                <label for="semester_credit">Total SKS Approved</label>
                                <input type="text" class="form-control" id="semester_credit" name="semester_credit">
                            </div>
                        </div>
                        <div class="col-md-6 d-none" id="input_package_ceritificate">
                            <div class="form-group">
                                <label for="total_package_certificate">Total Package Ceriticate</label>
                                <input type="number" class="form-control" id="total_package_certificate" name="total_package_certificate" value="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Academic Semester</label>
                        <div class="input-group">
                            <select name="academic_year_academic" id="academic_year_invoice" class="form-control">
                    <?php
                    foreach($batch as $val){
                        $selected = ($val->academic_year_id == $this->session->userdata('academic_year_id_active')) ? 'selected="selected"' : '';
                    ?>
                    <option value="<?=$val->academic_year_id?>" <?=$selected;?>><?=$val->academic_year_id?></option>
                    <?php
                    }
                    ?>
                            </select>
                            <select name="semester_type_academic" id="semester_type_invoice" class="form-control">
                                <option value="1" <?= ($this->session->userdata('semester_type_id_active') == '1') ? 'selected="selected"' : ''; ?>>ODD</option>
                                <option value="2" <?= ($this->session->userdata('semester_type_id_active') == '2') ? 'selected="selected"' : ''; ?>>EVEN</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="fee_name">Fee Name</label>
                        <select class="form-control" id="fee_id" name="fee_id">
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="installments_input">Installments Program</label>
                        <select class="form-control" id="installments_input" name="installments_input" placeholder="Please select...">
                            <?php
                            for($i = 0; $i <= 6; $i++){
                                if($i != 1){
                            ?>
                            <option value="<?=$i?>"><?=($i == 0) ? 'None' : "$i x installment(s)"?></option>
                            <?php
                                }
                            }	
                            ?>
                        </select>
                        <input type="hidden" name="installments" id="installments" value="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="initial_deadline">Deadline</label>
                        <input type="text" class="form-control" id="initial_deadline" name="initial_deadline">
                    </div>
                </div>
            </div>
            
			<button class="btn btn-info float-right" id="save_invoice">Save <i class="fa fa-save"></i></button>
		</form>
	</div>
</div>

<script>
	$(function(){
		$('input#initial_deadline').mask('99-99-9999');
		$('input#initial_deadline').daterangepicker({
			minDate: new Date(),
			timePicker: false,
			opens: 'center',
			drops: 'up',
			singleDatePicker: true,
			autoApply: true,
			locale: {
				format: 'DD-MM-YYYY',
				separator: '-'
			}
		});

		$('select#student_name').select2({
			allowClear: true,
            placeholder: "Please select",
			theme: "bootstrap",
			ajax: {
                url: '<?=base_url()?>student/get_student_by_name',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    return {
						keyword: params.term,
						finance_year_id: $('#finance_batch').val()
                    }
                },
                processResults: function(result) {
                    data = result.data;
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.personal_data_name + ' - ' + item.study_program_abbreviation + '/' + item.finance_year_id,
                                id: item.personal_data_id
                            }
                        })
                    }
                }
			}
		}).on("change", function(e) {
			var data = $(this).select2('data');
			if (data.length > 0) {
				$('input#personal_data_id_new_invoice').val(data[0].id);
			}else{
				$('input#personal_data_id_new_invoice').val('');
			}
		});
		
		$('select#installments_input').on('change', function(e) {
			e.preventDefault();
			$('input#installments').val($(this).val());
		});

		$('button#save_invoice').on('click', function(e){
			e.preventDefault();
			
			$.blockUI({ baseZ: 2000 });
			let form = $('form#new_invoice_form');
			let data = form.serialize();
			let url = form.attr('action');
			// console.log(data);
			
			$.post(url, data, function(rtn){
				$.unblockUI();
				if (rtn.code == 0) {
					$('#new_invoice_modal').modal('hide');
					invoice_table.ajax.reload(null, true);
					toastr.success('Success ' + rtn.message, 'Success');
				}else{
					toastr.warning(rtn.message, 'Warning!');
				}
				// console.log(rtn);
			}, 'json').fail(function(params) {
				$.unblockUI();
				toastr.error('Error processing data!');
			});
		});
		
        $('#invoice_single').on('change', function(e) {
            e.preventDefault();
            if ($(this).is(':checked')) {
                $('select#payment_type_code').attr('disabled', false);
                $('.student_input').removeClass("d-none");
            }
        });

        $('#invoice_bulk').on('change', function(e) {
            e.preventDefault();
            if ($(this).is(':checked')) {
                $('select#payment_type_code').val('02').attr('disabled', true);
                $('.student_input').addClass("d-none");
                $('select#student_name').val(null).trigger('change');
            }
        });

		$('select#finance_batch').on('change', function() {
			$('select#student_name').val(null).trigger('change');
		});

		$('select#semester_id').on('change', function() {
			$('select#fee_id').val(null).trigger('change');
            set_input_credit();
		});

        $('select#payment_type_code').on('change', function() {
			$('select#fee_id').val(null).trigger('change');

            set_input_credit();
            set_package_certificate();
		});

        function set_package_certificate() {
            if ($('#payment_type_code').val() == '13') {
                $('div#input_package_ceritificate').removeClass('d-none');
                $('#semester_invoice').removeClass('col-md-12');
                $('#semester_invoice').addClass('col-md-6');
            }
            else{
                $('div#input_package_ceritificate').addClass('d-none');
                $('#semester_invoice').removeClass('col-md-6');
                $('#semester_invoice').addClass('col-md-12');
            }
        }

        function set_input_credit() {
            if (parseInt($('select#semester_id option:selected').text()) > 8) {
				if ($('#payment_type_code').val() == '02') {
					$('div#input_sks').removeClass('d-none');
                    $('#semester_invoice').removeClass('col-md-12');
                    $('#semester_invoice').addClass('col-md-6');
				}
				$('select#installments_input').removeAttr('disabled', 'true');
			}else{
				if ($('#payment_type_code').val() == '02') {
					$('div#input_sks').addClass('d-none');
                    $('#semester_invoice').removeClass('col-md-6');
                    $('#semester_invoice').addClass('col-md-12');

					$('select#installments_input').val('6').trigger('change');
					$('select#installments_input').attr('disabled', 'true');
					$('input#installments').val('6');
				}else{
					$('select#installments_input').removeAttr('disabled', 'true');
				}
			}
        }
		
		$('select#fee_id').select2({
			placeholder: 'Please select...',
			theme: 'bootstrap',
			allowClear: true,
			minimumResultsForSearch: -1,
		    ajax: {
		        url: '<?=site_url('finance/fee/get_all_fee')?>',
		        dataType: 'json',
		        type: "POST",
		        data: function(params){
		            return{
		                academic_year_id: $('select#finance_batch').val(),
		                semester_id: $('select#semester_id').val(),
		                payment_type_code: $('select#payment_type_code').val()
		            };
		        },
		        processResults: function(result){
			        var disp = {
				        results: []
			        };
				        
			        if(result.data !== false){
				        $.each(result.data, function(k, v){
					        var text_to_display = [
					        	v.fee_description, 
					        	v.academic_year_id
					        ];
					        (v.study_program_abbreviation !== null) ? text_to_display.push(v.study_program_abbreviation) : '';
					        text_to_display = [text_to_display.join('/'), format_currency(v.fee_amount)].join(' @ ');
					        
					        var item = {
						        id: v.fee_id,
						        text: text_to_display
					        };
					        disp.results.push(item);
				        });
			        }
			        
			        return disp;
		        }
		    }
		});
	});
</script>