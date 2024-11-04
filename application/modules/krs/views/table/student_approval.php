<div class="table-responsive">
    <table id="table_student_approval" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Batch</th>
                <th>Student Status</th>
                <th>KRS Register Status</th>
                <th>Approval</th>
            </tr>
        </thead>
    </table>
</div>
<script>
// var str = "Research Semester (Double Degree)";
// var n = str.search("Research Semester");
// console.log(n);
var uri = '<?= base_url() ?>krs/get_student_krs_lists';
var table_krs_student = $('#table_student_approval').DataTable({
    processing: true,
    order: [[2, 'asc'],[1, 'asc']],
    ajax:{
        url: uri,
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#form_filter_krs_approval').serializeArray();
            var a_filter_data = objectify_form(a_form_data);
            return a_filter_data;
        }
    },
    columns: [
        {data: 'student_number'},
        {data: 'personal_data_name'},
        {data: 'batch'},
        {
            data: 'student_status',
            render: function(data, type, rows) {
                return data.toUpperCase();
            }
        },
        {
            data: 'student_id',
            render: function(data, type, rows) {
                var text = (rows.approval != 'N/A') ? 'KRS Details' : 'Register KRS';
                if ($.inArray(rows['student_status'], ['active', 'graduated']) >= 0) {
                    return '<a href="<?=base_url()?>krs/krs_approval/' + $('#academic_year_id').val() + '/' + $('#semester_type_id').val() + '/' + rows.personal_data_id + '" target="blank">' + text + '</a>';
                }else{
                    return '';
                }
            }
        },
        {data: 'approval'},
    ],
    columnDefs: [
        {
            targets: [0,1],
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>personal_data/profile/' + row.student_id + '/' + row.personal_data_id + '">' + data + '</a>';
            }
        }
    ],
    createdRow: function( row, data, dataIndex){
        if (data.student_status == 'resign') {
            $(row).addClass('bg-secondary');
        }else if (data.approval == 'PENDING') {
            $(row).addClass('smooth-warning');
        }else if(data.approval == 'N/A') {
            $(row).addClass('smooth-danger');
        }
    },
});
$(function() {
    $('#btn_filter_krs_approval').on('click', function(e) {
        e.preventDefault();

        var filter_data = $('#form_filter_krs_approval').serializeArray();
        var filter_data = objectify_form(filter_data);

        var show_data = true;
        $.each(filter_data, function(i, v) {
            if (v == '') {
                console.log(i);
                toastr.warning('Please select form filter field!', 'Warning');
                show_data = false;
                return false;
            }
        });

        if (show_data) {
            table_krs_student.ajax.reload();
        }
    });
});
</script>