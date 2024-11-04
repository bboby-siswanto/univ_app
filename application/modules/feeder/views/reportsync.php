<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_report_sync" class="table table-bordered table-hovered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Program Studi</th>
                        <th>Student Semster Status</th>
                    </tr>
                </thead>
                <tbody>
<?php
if ($data_list) {
    foreach ($data_list as $o_data) {
?>
                    <tr>
                        <td><?=$o_data->personal_data_name;?></td>
                        <td><?=$o_data->study_program_name_feeder;?></td>
                        <td><?=$o_data->student_semester_status;?></td>
                    </tr>
<?php
    }
}
?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
let table_report_sync = $('#table_report_sync').DataTable({
    paging: false
});
</script>