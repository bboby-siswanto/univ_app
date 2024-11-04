<div class="table-responsive">
    <table id="activity_list_table" class="table table-bordered table-hover">
        <thead class="bg-dark">
            <tr>
                <th width="20">No.</th>
                <th>Study Program</th>
                <th>Activity Type</th>
                <th>Title</th>
                <th>SK Date</th>
                <th>Sync Feeder</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<script>
$(function() {
    var activity_list_table = $('table#activity_list_table').DataTable({
        processing: true,
        ajax: {
            type: 'POST',
            url: '<?=base_url()?>academic/activity_study/get_list_activity',
            data: function(params) {
                let a_form_data = $('#activity_study_filter_form').serialize();
                // var a_filter_data = objectify_form(a_form_data);
                return a_form_data;
            }
        },
        columns: [
            {
                data: "activity_study_id",
                orderable: false
            },
            {
                data: "study_program_name",
                render: function(data, type, rows) {
                    if (rows['program_id'] == '<?=$this->a_programs['NI S1'];?>') {
                        return rows['study_program_ni_name'];
                    }else{
                        return rows['study_program_name'];
                    }
                }
            },
            {data: "nama_jenis_aktivitas_mahasiswa"},
            {data: "activity_title"},
            {data: "activity_sk_date"},
            {
                data: "feeder_sync",
                render: function(data, type, row) {
                    var html_success = '<span class="badge badge-success">Success</span>';
                    var html_failed = '<span class="badge badge-danger">Failed</span>'
                    return (data == 0) ? html_success : html_failed;
                }
            },
            {
                data: "activity_study_id",
                orderable: false,
                render: function(data, type, row) {
                    var html = '<div class="btn-group" role="group">';
                    html += '<a href="<?=base_url()?>academic/activity_study/activity_study_list/' + data + '" target="_blank" title="View Data" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                    html += '<button id="btn_update_activity" class="btn btn-info btn-sm" type="button" title="Update Data"><i class="fas fa-edit"></i></button>';
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    $('table#activity_list_table').DataTable().on( 'order.dt search.dt', function () {
        $('table#activity_list_table').DataTable().column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    $('table#activity_list_table tbody').on('click', 'button#btn_update_activity', function(e) {
        e.preventDefault();
        var activity_data = activity_list_table.row($(this).parents('tr')).data();
        
        var input_form = $('form#form_input_activity_study').find('input, select');
        $.each(input_form, function(i, v) {
            var field_name = v.name;
            var field_id = v.id;
            
            $('#' + field_id).val(activity_data[field_name]);
        });

        $('#form_input_activity_study_modal').modal('show');
    });

    $('button#btn_submit_activity_study').on('click', function(e) {
        e.preventDefault();

        $.blockUI({baseZ: 9999});
        var data = $('#form_input_activity_study').serialize();
        
        $.post('<?=base_url()?>academic/activity_study/save_activity', data,  function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Succes', 'Success');
                activity_list_table.ajax.reload(null, false);
                $('#form_input_activity_study_modal').modal('hide');
                window.location.href = "<?=base_url()?>academic/activity_study/activity_study_list/" + result.activity_id;
            }else{
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
    });

    $('#btn_filter_activity_study').on('click', function(e) {
        e.preventDefault();

        activity_list_table.ajax.reload();
    });
});
</script>