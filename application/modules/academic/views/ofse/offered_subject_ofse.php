<div class="card">
    <div class="card-header">
        Offered Subject Filter
    </div>
    <div class="card-body">
        <form id="filter_curriculum_ofse" onsubmit="return false">
            <input type="hidden" name="ofse_period_id" id="ofse_period_id" value="<?=$ofse_data->ofse_period_id;?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Study Program</label>
                        <select name="study_program_id" id="study_program_id_filter" class="form-control">
                            <option value="">Please select...</option>
<?php
if ($study_program_list) {
    foreach ($study_program_list as $o_study_program) {
?>
                            <option value="<?=$o_study_program->study_program_id;?>"><?=$o_study_program->study_program_name;?></option>
<?php
    }
}
?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="button" id="submit_filter_ofse" class="btn btn-info float-right">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Curriculum Subject List (<span id="study_program_curriculum_selected"></span>)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="curriculum_ofse_table" class="table table-bordered">
                        <thead class="bg-dark">
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Offered Subject List (<span id="study_program_ofse_selected"></span>)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="offered_subject_ofse_table" class="table table-bordered">
                        <thead class="bg-dark">
                            <tr>
                                <th>Subject Name</th>
                                <th>Examiner</th>
                                <th>Subject Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal face" tabindex="-1" role="dialog" id="modal_input_ofse_status">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Subject Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_input_status_subject_ofse" onsubmit="return false">
                    <input type="hidden" name="curriculum_subject_id" id="curriculum_subject_id_submit">
                    <div class="form-group">
                        <label>Select subject status:</label>
                        <select name="subject_status" id="subject_status" class="form-control">
                            <option value="">Please select...</option>
<?php
if ($ofse_status_list) {
    foreach ($ofse_status_list as $ofse_status) {
        $s_ofse_status = str_replace('_', ' ', $ofse_status);
?>
                            <option value="<?=$ofse_status;?>"><?= ucwords($s_ofse_status) ?></option>
<?php
    }
}
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-info float-right" id="submit_offered_subject">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
var form_filter = $('form#filter_curriculum_ofse');

var curriculum_ofse_table = $('table#curriculum_ofse_table').DataTable({
    ajax: {
        url: '<?= base_url()?>academic/ofse/get_ofse_curriculum_subject',
        type: 'POST',
        data: function(params) {
            let a_form_data = form_filter.serializeArray();
            var a_filter_data = objectify_form(a_form_data);
            return a_filter_data;
        }
    },
    columns: [
        {data: 'subject_code'},
        {data: 'subject_name'},
        {
            data: 'curriculum_subject_id',
            className: "text-center",
            render: function(data, type, row) {
                return '<button class="btn btn-info btn-sm" type="button" id="btn_curriculum_subject_ofse"><i class="fas fa-chevron-right"></i></button>';
            }
        }
    ]
});

var offered_subject_ofse_table = $('table#offered_subject_ofse_table').DataTable({
    ajax: {
        url: '<?= base_url()?>academic/ofse/get_ofse_offered_subject',
        type: 'POST',
        data: function(params) {
            let a_form_data = form_filter.serializeArray();
            var a_filter_data = objectify_form(a_form_data);
            return a_filter_data;
        }
    },
    columns: [
        {
            data: 'subject_name',
            render: function (data, type, row) {
                let html = '<span class="d-none">' + row['offered_subject_id'] + '</span>' + data;
                return html;
            }
        },
        {data: 'lecturer_1'},
        {data: 'ofse_status'},
        {
            data: 'offered_subject_id',
            render: function(data, type, row) {
                html = '<div class="btn-group-sm">';
                html += '<button type="button" class="btn btn-danger btn-sm" id="btn_remove_offered_subject_ofse"><i class="fas fa-trash"></i></button>';
                html += '</div>';

                return html;
            }
        }
    ]
});

$(function() {
    $('button#submit_filter_ofse').on('click', function(e) {
        e.preventDefault();

        curriculum_ofse_table.ajax.reload(function(json) {
            $('span#study_program_curriculum_selected').text(json.prodi);
        });

        offered_subject_ofse_table.ajax.reload(function(json) {
            $('span#study_program_ofse_selected').text(json.prodi);
        });
    });

    $('table#curriculum_ofse_table tbody').on('click', 'button#btn_curriculum_subject_ofse', function(e) {
        e.preventDefault();

        var data = curriculum_ofse_table.row($(this).parents('tr')).data();
        
        $('input#curriculum_subject_id_submit').val(data.curriculum_subject_id);
        $('div#modal_input_ofse_status').modal('show');
    });
    
    $('table#offered_subject_ofse_table tbody').on('click', 'button#btn_remove_offered_subject_ofse', function(e) {
        e.preventDefault();

        var data = offered_subject_ofse_table.row($(this).parents('tr')).data();
        
        if (confirm('Are you sure ?')) {
            $.post('<?=base_url()?>academic/offered_subject/remove_offered_subject', {offered_subject_id:data.offered_subject_id}, function(result) {
                if (result.code == 0) {
                    toastr.success('Success!');
                    offered_subject_ofse_table.ajax.reload(null, true);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Error processing data!', 'Error System!');
            });
        }
    });

    $('button#submit_offered_subject').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var filter_form = form_filter.serializeArray();
        var submit_form = $('form#form_input_status_subject_ofse').serializeArray();

        var a_form_data = $.merge( filter_form, submit_form );
        var data = objectify_form(a_form_data);
        
        $.post('<?=base_url()?>academic/ofse/submit_offered_subject_ofse', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                $('div#modal_input_ofse_status').modal('hide');
                offered_subject_ofse_table.ajax.reload(null, true);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error procesing data!', 'Error');
        });
    });
});
</script>