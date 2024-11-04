<div class="card">
    <div class="card-header">
        Filter Data
    </div>
    <div class="card-body">
        <form id="filter_alumni_form" onsubmit="return false">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Institute</label>
                        <select name="program_id" id="filter_program_id" class="form-control">
                            <option value="all" selected="selected">All</option>
                            <?php
                            foreach($ref_program as $value){
                            ?>
                            <option value="<?=$value->program_id?>"><?=$value->type_of_admission_name?></option>
                            <?php
                            }  
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label id="label_filter_study_program">
                            Study Program
                            <div class="spinner-border-mini d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </label>
                        <select name="study_program_id" id="filter_study_program_id" class="form-control">
                            <option value="all">All</option>
                            <?php
                            foreach($study_program as $value){
                            ?>
                            <option value="<?=$value->study_program_id?>" data-abbr="<?= $value->study_program_abbreviation; ?>"><?=$value->study_program_name?></option>
                            <?php
                            }  
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Batch</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-control">
                            <option value="all">All</option>
                            <?php
                            foreach($batch as $value){
                            ?>
                            <option value="<?=$value->academic_year_id?>"><?=$value->academic_year_id?></option>
                            <?php
                            }  
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col">
                <button id="btn_filter_alumni" class="btn btn-info float-right" type="button">Filter</button>
            </div>
        </div>
    </div>
</div>
<script>
	var selectmulti = $('#filter_student_status2').selectpicker();

	$('#filter_program_id').on('change', function(e) {
		e.preventDefault();
        show_filter_study_program();
	});

	function show_filter_study_program() {
		let program_id = $('#filter_program_id').val();
		$('label#label_filter_study_program .spinner-border-mini').removeClass('d-none');

		$.post('<?=base_url()?>study_program/get_study_program_instititute', {program_id: program_id}, function(result) {
			$('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
			var s_html = '<option value="all">All</option>';
			if (result.code == 0) {
				$.each(result.data, function(index, value) {
					s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
				});
			}
			$('#filter_study_program_id').html(s_html);
		}, 'json').fail(function(params) {
			$('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
			
			var s_html = '<option value="">Please select..</option><option value="all">All</option>';
			toastr.error('Error getting data!', 'Error');
		});
	}
</script>