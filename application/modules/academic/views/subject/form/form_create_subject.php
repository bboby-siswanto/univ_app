<form method="POST" id="form_save_subject" onsubmit="return false">
    <input type="hidden" name="subject_id" id="subject_id" value="<?= ($o_subject_data) ? $o_subject_data->subject_id : ''; ?>">
    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <label>Subject Code</label>
                <input type="text" class="form-control" name="subject_code" id="subject_code" value="<?= ($o_subject_data) ? $o_subject_data->subject_code : ''; ?>">
            </div>
        </div>
        <div class="col-md-8 col-sm-12">
            <div class="form-group">
                <label class="required_text">Subject Name</label>
                <input type="text" class="form-control" name="subject_name" id="subject_name" value="<?= ($o_subject_data) ? $o_subject_data->subject_name : ''; ?>">
                <input type="hidden" name="subject_name_id" id="subject_name_id" value="<?= ($o_subject_data) ? $o_subject_data->subject_name_id : ''; ?>">
            </div>
        </div>
        <div class="col-md-5 col-sm-12">
            <div class="form-group">
                <label class="required_text">Program</label>
                <select name="program_id" id="program_id" class="form-control">
                    <option value="">Please select ...</option>
            <?php
                foreach ($o_program_lists as $program) {
            ?>
                    <option value="<?= $program->program_id;?>"><?= $program->program_name;?></option>
            <?php
                }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-7 col-sm-12">
            <div class="form-group">
                <label id="label_search_study_program_id" class="required_text">
                    Study Program
                    <div class="spinner-border-mini d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </label>
                <select name="study_program_id" id="study_program_id_program" class="form-control">
                    <option value="">Please select ...</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="required_text">Subject Type</label>
                <select name="id_jenis_mata_kuliah" id="id_jenis_mata_kuliah" class="form-control">
                    <option value="">Please select ...</option>
            <?php
                foreach($o_subject_type as $type) {
            ?>
                    <option value="<?= $type->id_jenis_mata_kuliah;?>" <?= (($o_subject_data) AND ($o_subject_data->id_jenis_mata_kuliah == $type->id_jenis_mata_kuliah)) ? 'selected' : ''; ?>><?= $type->nama_jenis_mata_kuliah?></option>
            <?php
                }
            ?> 
                </select>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="required_text">Subject Credit (SKS)</label>
                <input type="text" class="form-control" name="subject_credit" id="subject_credit"  value="<?= ($o_subject_data) ? $o_subject_data->subject_credit : ''; ?>">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right">
                <button class="btn btn-primary" type="submit" id="save_subject">Save</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $('input#subject_name').autocomplete({
            autoFocus: true,
			minLength: 1,
            appendTo: 'form#form_save_subject',
			source: function(request, response){
				var url = '<?=site_url('academic/subject/get_subject_name')?>';
				var data = {
					term: request.term
				};
				$.post(url, data, function(rtn){
					if(rtn.data){
						var arr = [];
						arr = $.map(rtn.data, function(m){
							return {
								id: m.subject_name_id,
								value: m.subject_name
							};
						});
						response(arr);
					}
					else{
						$('input#subject_name_id').val('');
					}
				}, 'json');
			},
			select: function(event, ui){
				var id = ui.item.id;
				$('input#subject_name_id').val(id);
			},
			change: function(event, ui){
				if(ui.item === null){
                    $('input#subject_name_id').val('');
				}
			}
        });

        $('form#form_save_subject').on('submit', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });
            
            var data = $('form#form_save_subject').serialize();
            $.post('<?= base_url()?>academic/subject/save_subject', data, function(result) {
                if(result.code == 0){
                    toastr['success']('subject has been saved', 'Success');
                    $('div#new_subject_modal').modal('hide');
                    if ($.fn.DataTable.isDataTable(subject_list_table)) {
                        subject_list_table.ajax.reload(null, false);
                    }else{
                        window.location.reload();
                    }
                }
                else{
                    toastr['warning'](result.message, 'Warning!');
                }
                $.unblockUI();
            },'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });

            return false;
        });

        $('#program_id').on('change', function(e) {
            e.preventDefault();

            var program_id = this.value;
            if (program_id == '') {
                $('#study_program_id_program').html('<option value="">Please select ...</option>');
            }else{
                show_study_program();
            }
        });
    });

    function show_study_program(setprodi = false) {
        let program_id = $('#program_id').val();
        $('label#label_search_study_program_id .spinner-border-mini').removeClass('d-none');
        
        $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
            $('label#label_search_study_program_id .spinner-border-mini').addClass('d-none');

            var s_html = '<option value="">Please select ...</option>';
            if (result.code == 0) {
                $.each(result.data, function(index, value) {
                    s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
                });
            }
            $('#study_program_id_program').html(s_html);
            
            if (setprodi) {
                $('#study_program_id_program').val(setprodi);
            }
        }, 'json').fail(function(params) {
            $('label#label_search_study_program_id .spinner-border-mini').addClass('d-none');
            
            var s_html = '<option value="">Please select..</option><option value="All">All</option>';
            toastr.error('Error getting data!', 'Error');
        });
    }
</script>