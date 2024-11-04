<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label>Program</label>
            <select name="program_id" id="program_id_filter_curriculum" class="form-control">
    <?php
        if ($o_program_lists) {
            foreach ($o_program_lists as $program) {
    ?>
                <option value="<?= $program->program_id;?>"><?= $program->program_name;?></option>
    <?php
            }
        }
    ?>
            </select>
        </div>
        <div class="form-group">
            <label>Active Year</label>
            <select name="active_year_id" id="active_year_id" class="form-control">
                <option value="">Please select ...</option>
        <?php
            if ($o_active_year) {
                foreach ($o_active_year as $year) {
        ?>
                <option value="<?= $year->academic_year_id; ?>" <?= ($year->academic_year_intake_status == 'active') ? 'selected' : '' ?>><?= $year->academic_year_id; ?></option>
        <?php
                }
            }
        ?> 
            </select>
        </div>
        <div class="form-group">
            <label>Curirculum</label>
            <select name="curriculum_id" id="curriculum_id" class="form-control" aria-describedby="basic-addon2">
                <option value="">Please Select ...</option>
            </select>
        </div>
        <div class="form-group">
            <label>Semester</label>
            <select name="semester_id" id="semester_id" class="form-control">
                <option value="">Please select...</option>
            </select>
        </div>
        <div class="form-group">
            <button class="btn btn-info float-right" type="button" id="btn_filter_curriculum_offered_subject">Filter</button>
        </div>
    </div>
</div>
<script>
    var ofse_curriculum_data;
    $(function() {
        $('#program_id_filter_curriculum').val('1').trigger('change');
        show_curriculum_data();

        $('#active_year_id').on('change', function(e) {
            e.preventDefault();

            var s_active_year_id = $('#active_year_id').val();
            if (s_active_year_id == '') {
                $('#curriculum_id').html('<option value="">Please select...</option>');
                $('#semester_id').html('<option value="">Please select...</option>');
            }else{
                show_curriculum_data();
            }
        });

        $('#program_id_filter_curriculum').on('change', function(e) {
            e.preventDefault();

            var s_active_year_id = $('#active_year_id').val();
            if (s_active_year_id == '') {
                $('#curriculum_id').html('<option value="">Please select...</option>');
                $('#semester_id').html('<option value="">Please select...</option>');
            }else{
                show_curriculum_data();
            }
        });

        $('#curriculum_id').on('change', function(e) {
            e.preventDefault();
			let sel_cur_id = $(this).val();
			
			$.each(ofse_curriculum_data, function(k,v){
				if(v.curriculum_id === sel_cur_id){
					let opt_semester = '<option value="">Please select ...</option>';
					$.each(v.semester_lists, function(key, val){
						opt_semester += '<option value="' + val.semester_id + '">' + val.semester_name + '</option>';
					});
					$('#semester_id').html(opt_semester);
				}
			});
        });
    });

    function show_curriculum_data(active_year_id = false) {
        let academic_year_id = $('#active_year_id').val();
        let program_id = $('#program_id_filter_curriculum').val();

        var filter_data = {
            valid_academic_year: academic_year_id,
            program_id: program_id
        };

        $.blockUI();
        $.post('<?= base_url()?>academic/curriculum/filter_curriculum_lists', {term: filter_data, this_os: 'true'}, function(result) {
            $.unblockUI();
            ofse_curriculum_data = result.data;
            var opt = '<option value="">Please select ...</option>';

            $.each(ofse_curriculum_data, function(index, value) {
                opt += '<option value="' + value.curriculum_id + '">' + value.curriculum_name + '</option>';
            });

            $('#curriculum_id').html(opt);
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!, Please try again later!', 'Error');
        });
    }
</script>