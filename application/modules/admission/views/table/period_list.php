<div class="table-responsive">
    <table class="table table-bordered table-striped" id="table_period">
        <thead class="bg-dark">
            <tr>
                <th>No</th>
                <th>Exam Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Random Question</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    var period_table = $('#table_period').DataTable({
        ajax: {
            url: '<?=base_url()?>admission/entrance_test/period_list',
            type: 'POST',
            data: false
        },
        columns: [
            {
                data: 'exam_id',
                render: function ( data, type, row, meta ) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'exam_period_name' ,
                render: function(data, type, row) {
                    var html = '<a href="<?=base_url()?>admission/entrance_test/participant_list/' + row.exam_id + '">' + data + '</a>';
                    return html;
                }
            },
            { data: 'exam_start_time' },
            { data: 'exam_end_time' },
            { 
                data: 'exam_random_question' ,
                visible: false
            },
            {
                data: 'exam_id' ,
                render: function(data, type, row) {
                    var btn_edit = '<button type="button" id="edit_data" title="Edit data" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></button>';
                    html = '<div class="btn-group" role="group" aria-label="">';
                    // html += btn_edit;
                    html += '</div>';
                    return html;
                }
            }
        ]
    });
</script>