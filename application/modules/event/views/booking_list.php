<div class="card">
    <div class="card-header">
        <?=$event_data->event_name;?> Event
        <div class="card-header-actions">
            <a href="<?=base_url()?>event/lists" class="card-header-action btn btn-link"><i class="fa fa-list"></i> Event List</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Booking Lists
        <!-- <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_event_registration">
                <i class="fa fa-plus"></i> New Event Registration
            </button>
        </div> -->
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="booking_list" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
            <?php
			if ((isset($event_field)) AND ($event_field)) {
				foreach ($event_field as $o_field) {
			?>
						<th><?=$o_field->field_title;?></th>
			<?php
				}
			}
			?>
                        <th>Participation</th>
                        <th>Registration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_check_in" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Check In</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_check_in" method="POST" action="<?=site_url('event/do_check_in')?>">
				<div class="row">
					<div class="col-md-4">
						<video id='participant_video' style='width: 100%; height: auto;' class="d-none">
						</video>
						<img id="participant_picture" class="img-fluid mb-2" src="<?=site_url('assets/img/silhouette.png');?>">
						<button type="button" class="btn btn-block btn-success" id="start_webcam">Take/Retake Photo</button>
						<input type="hidden" id="image_file" name="image_file">
						<div class="btn-group btn-block d-none" role="group" id='snap_close'>
							<button type="button" class="btn btn-danger" id='stop_webcam'>Cancel</button>
							<button type="button" class="btn btn-success" id='snap_photo'>Snap Photo!</button>
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group">
							<label><strong>Name</strong></label>
							<input type="hidden" id="booking_id" name="booking_id">
							<input type="hidden" id="event_id" name="event_id">
							<input type="text" id="static_name" name="name">
						</div>
						<div class="form-group">
							<label><strong>Email</strong></label>
							<input type="text" class="form-control" id="static_email" name="email">
						</div>
						<div class="form-group">
							<label><strong>Phone</strong></label>
							<input type="text" class="form-control" id="static_phone" name="phone">
						</div>
						<div class="form-group">
							<label><strong>How do you know about this event?</strong></label>
							<input type="text" class="form-control" id="reference" name="reference">
						</div>
						<div class="form-group">
							<label><strong>Seat</strong></label>
							<select class="form-control" id="booking_seat" name="booking_seat">
				                <option value="1">1</option>
				                <option value="2">2</option>
				                <option value="3">3</option>
				                <option value="4">4</option>
			                </select>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="check_in_participant">Check-in</button>
			</div>
		</div>
	</div>
</div>

<script>
    let event_id = '<?=$event_data->event_id;?>';
    var event_bookings = $('table#booking_list').DataTable({
        ajax: {
            url: '<?=site_url('event/get_bookings')?>',
            method: 'POST',
            data: function(d){
                d.event_id = event_id;
            }
        },
        columns: [
<?php
if ((isset($event_field)) AND ($event_field)) {
	foreach ($event_field as $o_field) {
?>
			{data: 'booking_<?=$o_field->field_name;?>'},
<?php
	}
}
?>
			{data: 'booking_participation'},
			{data: 'booking_registration'},
            { 
                data: 'booking_id',
                render: function(data, type, row){
                    var btn_html = '<div class="btn-group btn-group-sm" role="group">';
                    if(row['booking_participation'] == 'pending'){
                        btn_html += '<button id="check_in_bookings" class="btn btn-sm btn-warning"><i class="far fa-clock"></i></button>';
                    }
                    else{
                        btn_html += '<a class="btn btn-sm btn-info"><i class="fas fa-check"></i></a>';
                    }

                    if (row['is_student'] == 'false') {
                        btn_html += '<button class="btn btn-sm btn-success" id="push_student_button" title="Add Candidate"><i class="fas fa-user-plus"></i></button>';
                    }
                    btn_html += '</div>';
                    return btn_html;
                }
            }
        ],
        paging: false,
        dom: 'Bfrtip',
        buttons: [
            'excel',
            'csv'
        ]
    });
    
    $(function() {
        $('table#booking_list tbody').on('click', 'button#push_student_button', function(e) {
            e.preventDefault();

            var table_data = event_bookings.row($(this).parents('tr')).data();
            if (table_data.is_student == 'false') {
                $.blockUI();
                var url = '<?=base_url()?>event/push_candidate';
                $.post(url, {booking_id: table_data.booking_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        event_bookings.ajax.reload(null, false);
                        toastr.success('Success');
                    }
                    else {
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!');
                });
            }
        });
        
        $('button#btn_new_event_registration').on('click', function(e){
			e.preventDefault();
			$('form#form_check_in')[0].reset();
			$('input#static_name').prop('readonly', false).removeClass('form-control-plaintext').addClass('form-control');
			$('input#event_id').val(event_id);
			$('div#modal_check_in').modal('toggle');
			startWebCam();
		});

        $('div#modal_check_in').on('hidden.bs.modal', function(){
			stopWebCam();
		});
		
		$('button#check_in_participant').on('click', function(e){
			e.preventDefault();
			let form = $('form#form_check_in');
			let url = form.attr('action');
			let data = form.serialize();
			$.post(url, data, function(rtn){
				if(rtn.code == 0){
					$('div#modal_check_in').modal('toggle');
					form[0].reset();
					toastr.success('Your data has been saved!', 'Success!');
					event_bookings.ajax.reload();
				}
				else{
					toastr.warning(rtn.message, 'Warning!');
				}
			}, 'json');
		});
		
		$('button#check_in_participant').on('click', function(e){
			e.preventDefault();
		});
		
		var cameraStream;

		$('#btnCrop').on('click', (e) => {
			e.preventDefault();
			let response = cropper.getCroppedCanvas({
				width: 800,
				height: 800,
				minWidth: 800,
				maxWidth: 800
			}).toDataURL();
			$('#participant_picture').prop('src', response);
			$image.cropper('destroy');
			$('div#modal-crop-picture').modal('toggle');
			stopWebCam();
			uploadPhoto(response);
		});
	
		uploadPhoto = (image) => {
			$.post('<?=site_url('admissions/patients/uploadPatientPhoto');?>', {image: image}, (rtn) => {
				if(rtn.code == 0){
					$('#photo').val(rtn.filename);
				} else {
					alertError(rtn.msg)
				}
			}, 'json');
		}
	
	
		$('#btnRetake').on('click', () => {
			$image.cropper('destroy');
			$('#modal-crop-picture').modal('toggle');
			$('#image_file').prop('src', '');
			startWebCam();
		});
		
		$('#start_webcam').on('click', () => {
			startWebCam();
		})
	
		$('#snap_photo').on('click', () => {
			takeSnapshot();
		});
	
		$('#stop_webcam').on('click', () => {
			stopWebCam();
		});
	
		$('#modal-crop-picture').on('shown.bs.modal', function(){
	
			$image = $('#image_file');
			$image.cropper({
				// aspectRatio: 4/6,
				aspectRatio: 1/1,
				viewMode: 3,
				background: false,
				modal: false,
			});
			cropper = $image.data('cropper');
		});
	
	
		function takeSnapshot() {
			var context;
			var width = video.offsetWidth
			, height = video.offsetHeight;
	
			canvas = document.createElement('canvas');
			canvas.width = width;
			canvas.height = height;
	
			context = canvas.getContext('2d');
			context.drawImage(video, 0, 0, width, height);
	
			$('#participant_picture').prop('src', canvas.toDataURL('image/png'));
			$('#image_file').val(canvas.toDataURL('image/png'));
			$('#modal-crop-picture').modal('toggle');
	
			stopWebCam();
	    }
	
		stopWebCam = () => {
			$('#snap_close, #participant_video').addClass('d-none');
			$('#start_webcam, #participant_picture').removeClass('d-none');
			cameraStream.getTracks().forEach(function(track) {
			  track.stop();
			});
		}
	
		startWebCam = () => {
			var constraints = {
				audio: false,
				video: true
			}
			navigator.mediaDevices.getUserMedia(constraints)
			.then(function(stream) {
				video = document.getElementById('participant_video');
				video.srcObject = cameraStream = stream;
				video.addEventListener('click', takeSnapshot);
				video.onloadedmetadata = function(e) {
					$('#snap_close, #participant_video').removeClass('d-none');
					$('#start_webcam, #participant_picture').addClass('d-none');
				    video.play();
				};
			})
			.catch(function(err) {
				
			});	
		}
		
		$('table#booking_list').on('click', 'button#check_in_bookings', function(e){
			e.preventDefault();
			var table_data = event_bookings.row($(this).parents('tr')).data();
			$('div#modal_check_in').modal('toggle');
			$('input#booking_id').val(table_data.booking_id);
			$('input#static_name').val(table_data.booking_name)
			.prop('readonly', true).removeClass('form-control').addClass('form-control-plaintext');
			$('input#static_email').val(table_data.booking_email);
			$('input#static_phone').val(table_data.booking_phone);
			$('select#booking_seat').val(table_data.booking_seat);	
			startWebCam();
		});
    });
</script>