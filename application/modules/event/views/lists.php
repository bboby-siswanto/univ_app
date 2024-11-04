<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
				Event Lists
		<?php
		// if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
		?>
				<div class="card-header-actions">
					<button class="card-header-action btn btn-link" id="new_event_button">
						<i class="fa fa-plus"></i> Event
					</button>
				</div>
		<?php
		// }
		?>
			</div>
            <div class="card-body">
	            <div class="table-responsive">
		            <table id="event_list" class="table table-bordered table-hover table-striped">
						<thead class="bg-dark">
				            <tr>
					            <th>Event Name</th>
					            <th>Event Date</th>
					            <th>Registration URL</th>
					            <th>Venue</th>
					            <th>Action</th>
				            </tr>
						</thead>
		            </table>
	            </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="new_event_modal">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Event</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form url="<?=base_url()?>event/new_event" id="new_event_form" onsubmit="return false">
					<input type="hidden" name="event_id" id="event_id">
					<div class="row">
						<div class="col-md-6">
							<div id="first_form" class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="event_name" class="required_text">Event Name</label>
										<input type="text" class="form-control" name="event_name" id="event_name">
									</div>
									<div class="form-group">
										<label for="event_date" class="required_text">Date</label>
										<div class="input-group">
											<input type="date" class="form-control" name="event_date_start" id="event_date_start">
											<div class="input-group-prepend">
												<span class="input-group-text" id="">to.</span>
											</div>
											<input type="date" class="form-control" name="event_date_end" id="event_date_end">
										</div>
									</div>
									<div class="form-group">
										<label for="event_venue">Venue</label>
										<textarea class="form-control" name="event_venue" id="event_venue"></textarea>
									</div>
									<div class="form-group">
										<label for="event_allocation">Event Allocation</label>
										<select name="event_allocation" id="event_allocation" class="form-control">
											<option value="pmb">Candidate</option>
											<option value="general">Public</option>
										</select>
									</div>
									<div class="form-group">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="public_event" name="public_event" checked>
											<label class="custom-control-label" for="public_event">Publish Event</label>
										</div>
									</div>
									<div class="form-group">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="submit_test" name="submit_test">
											<label class="custom-control-label" for="submit_test">automatically add English online test members</label>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="event_slug" class="required_text">Slug</label>
										<input type="text" class="form-control" name="event_slug" id="event_slug" onkeyup="this.value=this.value.replace(/[^a-z0-9\_\-]/g,'');">
										<small id="link_target"></small>
									</div>
									<div class="form-group">
										<label for="event_rundown">Run Down</label>
										<textarea class="form-control" name="event_rundown" id="event_rundown" rows="10"></textarea>
										<!-- <textarea name="" id="" cols="30" rows="10"></textarea> -->
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div id="second_form" class="row">
								<div class="col-12">
									<div class="table-responsive">
										<label for="" class="required_text">Select form field for registration</label>
										<br>
										<small class="text-danger">
											Option Choose must be marked with a single quote and separated by a comma
										</small>
										<table id="event_field_select" class="table table-sm">
											<thead class="bg-dark">
												<tr>
													<th></th>
													<th class="w-25">Field Name</th>
													<th>Field Title</th>
													<th>Option Choose 
														<br><small class="text-danger">ex: 'option 1','option 2','option 3'</small>
													</th>
												</tr>
											</thead>
											<tbody>
							<?php
							if ($field_list) {
								foreach ($field_list as $o_field) {
									if ($o_field->field_id != '2') {
							?>
												<tr class="">
													<td class="select-checkbox">
														<input type="hidden" value="<?=$o_field->field_id;?>" name="field_id[]" id="field_id_<?=$o_field->field_name;?>">
													</td>
													<td><?=$o_field->field_name;?></td>
													<td>
														<input type="text" class="form-control" name="field_title" id="field_title_<?=$o_field->field_name;?>" value="<?=$o_field->field_title_default;?>">
													</td>
													<td>
										<?php
										if ($o_field->field_type == 'option') {
										?>
														<input type="text" class="form-control" name="field_option" id="field_option_<?=$o_field->field_name;?>" value="<?=$o_field->field_option_default;?>">
										<?php
										}
										?>
													</td>
												</tr>
							<?php
									}
								}
							}
							?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="new_event_submit">Submit</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	let event_id = null;
	var event_field = $('table#event_field_select').DataTable({
		paging: false,
		ordering: false,
		searching: false,
		// info: false,
		select: {
			style: 'os',
			style: 'multi'
		},
		drawCallback: function( settings ) {
			var api = this.api();
			if (api.column(0).data().length > 0) {
				api.rows().select();
			}
		} 
	});

	var event_list = $('table#event_list').DataTable({
		ajax: '<?=site_url('event/get_events')?>',
		columns: [
			{
				data: 'event_name',
				render: function(data, type, row) {
					return '<a href="<?=base_url()?>event/booking_list/' + row.event_slug + '">' + data + '</a>';
				}
			},
			{
				data: 'event_start_date',
				render: function(data, type, row){
					var date_start = moment(new Date(data)).format('D MMM YYYY');
					var date_end = moment(new Date(row['event_end_date'])).format('D MMM YYYY');
					return date_start + ' to ' + date_end;
				}
			},
			{
				data: 'event_slug',
				render: function(data, type, row){
					return '<?=base_url()?>event/public/' + data;
				}
			},
			{data: 'event_venue'},
			{
				data: 'event_id',
				render: function(data, type, row){
					var html = '<div class="btn-group btn-group-sm" role="group">';
					html += '<a href="<?=base_url()?>event/booking_list/' + row.event_slug + '" class="btn btn-sm btn-info" title="View Participants"><i class="fas fa-eye"></i></a>';
					html += '<button type="button" id="edit_event" title="Edit Event" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></button>';
					html += '</div>';
					return html;
				}
			}
		]
	});

	$(function(){
		$('button#new_event_submit').on('click', function(e) {
			e.preventDefault();
			// $.blockUI({ baseZ: 2000 });

			var form = $('form#new_event_form');
			var dataform = form.serializeArray();
			var url = form.attr('url');

			var data_checked = event_field.rows('.selected').data();
			var index_checked = event_field.rows('.selected').indexes();
			var table_data = {};
			if (index_checked.length > 0) {
				for (let x = 0; x < index_checked.length; x++) {
					const index_row = index_checked[x];
					// var thesis_student_id = defense_student_list.row(i).data();
					var data = {};
					data['field_id'] = event_field.cell(index_row, 0).nodes().to$().find('input').val();
					data['field_title'] = event_field.cell(index_row, 2).nodes().to$().find('input').val();
					data['field_option_period'] = event_field.cell(index_row, 3).nodes().to$().find('input').val();

					dataform.push({'name': 'fields_id[]', 'value': data['field_id']});
					dataform.push({'name': 'fields_title[]', 'value': data['field_title']});
					dataform.push({'name': 'fields_option[]', 'value': data['field_option_period']});
				}
			}
			dataform.push({'name': 'table_data[]', 'value': table_data});
			
			// console.log(dataform);

			$.post(url, dataform, function(result) {
				$.unblockUI();
				if (result.code == 0) {
					toastr.success('Success!');
					event_list.ajax.reload(null, false);
					$('#new_event_modal').modal('hide');
				}
				else {
					toastr.warning(result.message);
				}
			}, 'json').fail(function(params) {
				$.unblockUI();
				toastr.error('Error processing data!');
			});
		});

		$('input#event_slug').on('change', function(e) {
			e.preventDefault();
			
			var link = '<?=base_url()?>event/public/' + $(this).val();
			$('#link_target').text(link);
		})

		$('input#event_name').on('change', function(e) {
			var slug = $('input#event_name').val();
			slug = slug.replace(' ', '_');
			slug = slug.toLowerCase();
			var link = '<?=base_url()?>event/public/' + slug;

			$('input#event_slug').val(slug);
			$('#link_target').text(link);
		});

		$('button#new_event_button').on('click', function(e) {
			$('input#event_id').val('');
			$('input#event_name').val('');
			$('input#event_date_end').val('');
			$('input#event_date_start').val('');
			$('#event_venue').val('');
			$('input#event_slug').val('');
			$('#event_rundown').val('');
			$('#public_event').prop('checked', true);
			$('#new_event_modal').modal('show');
		});

		// $('table#event_list tbody').on('click', 'button#view_bookings', function(e) {
	    //     e.preventDefault();
	
	    //     var table_data = event_list.row($(this).parents('tr')).data();
	    //     event_id = table_data.event_id;
	    //     event_bookings.ajax.reload();
	    // });

		$('table#event_list tbody').on('click', 'button#edit_event', function(e) {
	        e.preventDefault();
	
	        var table_data = event_list.row($(this).parents('tr')).data();
	        $('input#event_id').val(table_data.event_id);
			$('input#event_name').val(table_data.event_name);
			$('input#event_date_end').val(moment(new Date(table_data.event_end_date)).format('YYYY-MM-DD'));
			$('input#event_date_start').val(moment(new Date(table_data.event_start_date)).format('YYYY-MM-DD'));
			$('#event_venue').val(table_data.event_venue);
			$('input#event_slug').val(table_data.event_slug);
			$('#event_rundown').val(table_data.event_run_down);
			$('#public_event').prop('checked', ((table_data.event_is_public == 1) ? true : false));

	        $('#new_event_modal').modal('show');
	    });
	});
</script>