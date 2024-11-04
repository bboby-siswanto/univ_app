<form onsubmit="return false;" id="period_input_exam">
    <input type="hidden" name="exam_period_id" id="exam_period_id">
    <div class="form-group">
        <label class="required_text">Exam Name</label>
        <input type="text" name="exam_period_name" id="exam_period_name" class="form-control">
    </div>
    <div class="form-group">
        <label class="required_text">Duration Exam</label>
        <input type="text" name="duration_date" id="duration_date" class="form-control">
    </div>
    <div class="form-group">
        <label>Listening File</label><br>
        <input type="file" name="listening_file" id="listening_file">
    </div>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="random_question" name="random_question">
        <label class="custom-control-label" for="random_question">Random Question</label>
    </div>
</form>
<script>
    $(function() {
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        $('#duration_date').daterangepicker({
            "parentEl": '#period_input_exam',
            "timePicker": true,
            "timePicker24Hour": true,
            "timePickerSeconds": true,
            "autoApply": true
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            console.log('Start ' + start.format('YYYY-MM-DD HH:mm:ss') + ' to ' + end.format('YYYY-MM-DD HH:mm:ss'));
        });

        $('button#submit_exam').on('click', function(e) {
            e.preventDefault();

            var formData = new FormData();
            formData.append('period_id', $('#exam_period_id').val());
            formData.append('exam_period_name', $('#exam_period_name').val());
            formData.append('date_start', $('#duration_date').data('daterangepicker').startDate);
            formData.append('date_end', $('#duration_date').data('daterangepicker').endDate);
			formData.append('listening_file', $('#listening_file')[0].files[0]);
            formData.append('random_question', (($('#random_question').is(':checked')) ? true : false));
            // if ($('input.checkbox_check').is(':checked'))
            $.blockUI({baseZ: 1000});

            var url = '<?=base_url()?>admission/entrance_test/submit_period_exam';

            $.ajax({
				url: url,
				type: 'POST',
				data: formData,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(result, status, jqXHR){
                    $.unblockUI();
					if(result.code == 0){
                        toastr['success']('Success create data', 'Success!');
                        $('#input_entrance_test').modal('hide');
                        period_table.ajax.reload('null', true);
					}else{
                        toastr.warning(result.message, 'Warning!');
                    }
				},
                error : function(xhr, ajaxOptions, thrownError) {
                    $.unblockUI();
                    toastr.error('Error processing data', 'Error!');
                    console.log(xhr.responseText);
                }
			});
        });
    });
</script>