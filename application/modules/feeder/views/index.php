<div class="card">
    <div class="card-header">Periode pelaporan</div>
    <div class="card-body">
        <form method="post" id="periode_pelaporan" action="<?=site_url('feeder/start_sync')?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-control">
                            <option value="">Please select..</option>
                            <?php
							foreach($a_academic_year as $academic_year){
							?>
							<option value="<?=$academic_year->academic_year_id?>"><?=$academic_year->academic_year_id?> / <?=($academic_year->academic_year_id + 1)?></option>
							<?php
							}
							?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Semester Type</label>
                        <select name="semester_type_id" id="semester_type_id" class="form-control">
                            <option value="">Please select..</option>
                            <?php
							foreach($a_semester_type as $o_semester_type){
							?>
							<option value="<?=$o_semester_type->semester_type_id?>"><?=$o_semester_type->semester_type_name?></option>
							<?php
							}
							?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" id="btn_sync" class="btn btn-primary float-right">Sync</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
	<div class="card-header">
        Hasil sinkronisasi
        <div class="card-header-actions"><span id="spinner_loader"></span></div>
    </div>
	<div class="card-body">
		<!-- <iframe id="sync_messages" style="width: 100%; height: 500px;"></iframe> -->
        <div id="sync_result"></div>
	</div>
</div>

<script>
	var previous_string = '';
    var start_string = 0;
    $('button#btn_sync').on('click', function(e){
	    let year = $('select#academic_year_id').val();
	    let semester = $('select#semester_type_id').val();

        $.ajax({
            xhr: function()
            {
                $('#sync_result').html('');
                var xhr = new window.XMLHttpRequest();
                $('#spinner_loader').html('<i class="fas fa-spinner fa-pulse"></i> Sedang melakukan proses sinkronisasi data ke dikti');
                $('button#btn_sync').attr('disabled', 'disabled');
                xhr.addEventListener("progress", function(evt){
                    var response_text = evt.target.response;
                    var a_current_response = JSON.parse(parsing_string(response_text));
                    var message = a_current_response.message;
                    console.log(message);

                    $('#sync_result').append(message);
                }, false);
                return xhr;
            },
            type: 'POST',
            url: "<?=base_url()?>feeder/student/sync_semester",
            // url: "<?=base_url()?>feeder/student/testing",
            data: {
                academic_year_id: year,
                semester_type_id: semester
            },
            success: function(data){
                // console.log('finish');
                $('button#btn_sync').removeAttr('disabled');
                toastr.success('Sinkronisasi selesai!');
                $('#spinner_loader').html('');
            }
        });
    });

    function parsing_string(string) {
        var new_string = string.substring(start_string, string.length); 
        start_string = string.length;
        return new_string;
    }
</script>