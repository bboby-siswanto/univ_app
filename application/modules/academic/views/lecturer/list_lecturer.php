<div class="card">
    <div class="card-header">
        List Lecturer
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="table_lecturer">
                <thead class="bg-dark">
                    <tr>
                        <th>Lecturer Full Name</th>
                        <th>NIP</th>
                        <th>NIDN</th>
                        <th>IULI Email</th>
                        <th>Personal Email</th>
                        <th>Personal Cellular</th>
                        <!-- <th>Dept</th> -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var table_lecturer = $('#table_lecturer').DataTable({
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'Lecturer List Data'
        },
        {
            text: 'Download Pdf',
            extend: 'pdf',
            title: 'Lecturer List Data'
        },
        {
            text: 'Print',
            extend: 'print',
            title: 'Lecturer List Data'
        },
    ],
    ajax: {
        url: '<?= base_url()?>employee/get_filter_data',
        type: 'POST',
        data: function(params) {
            let a_form_data = {
                employee_status: 'active',
                employee_type: 'lecturer'
            };
            return a_form_data;
        }
    },
    columns: [
        {
            data: 'fullname',
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>academic/class_group/lecturer_teaching/' + row.employee_id + '" target="_blank">' + data + '</a>';
            }
        },
        {data: 'employee_id_number'},
        {data: 'employee_lecturer_number'},
        {data: 'employee_email'},
        {data: 'personal_data_email'},
        {data: 'personal_data_cellular'},
        // {data: 'fullname'},
    ]
});
</script>