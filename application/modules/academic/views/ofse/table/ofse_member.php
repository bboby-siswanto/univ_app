<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <!-- <button type="button" class="btn btn-secondary">Left</button> -->
            <a href="<?=base_url()?>academic/ofse/ofse_schedule/<?=$ofse_period_id;?>" target="_blank" class="btn btn-primary">Schedule</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">Student Member <span><?=$ofse_data->ofse_period_name;?></span></div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_ofse_member" class="table table-bordered table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Study Program</th>
                        <th>Student Email</th>
                        <th>Total Subjects</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        var table_lists_ofse_member = $('table#table_ofse_member').DataTable({
            // paging: false,
            // bInfo: false,
            ajax : {
                url : '<?= base_url()?>academic/ofse/get_member_details',
                type : 'POST',
                data : {
                    ofse_period_id: '<?=$ofse_period_id;?>'
                }
            },
            columns: [
                {data: 'student_number'},
                {
                    data: 'personal_data_name',
                    render: function(data, type, row) {
                        return '<a href="<?=base_url()?>academic/ofse/student_krs/<?=$ofse_period_id;?>/' + row['student_id'] + '" target="_blank">' + data + '</a>';
                    }
                },
                {
                    data: 'study_program_abbreviation',
                    render: function(data, type, row) {
                        if (data == 'COS') {
                            data = 'CSE';
                        }

                        return data;
                    }
                },
                {data: 'student_email'},
                {data: 'total_subject'}
            ]
        });

        $('table#lists_ofse_member tbody').on('click', 'button[name="btn_input_score_ofse"]', function(e) {
            e.preventDefault();
        });
    });
</script>