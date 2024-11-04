<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_list_student_billing" class="table table-hover table-bordered bg-white">
                <thead class="bg-dark">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Student Name</th>
                        <th rowspan="2">Fac</th>
                        <th rowspan="2">SP</th>
                        <th rowspan="2">Student ID</th>
                        <th rowspan="2">Batch</th>
                        <th rowspan="2">Student Status</th>
                        <th colspan="2">Billing Detail</th>
                        <th rowspan="2">Total Tunggakan</th>
                    </tr>
                    <tr>
                        <th>Fee Details</th>
                        <th>Total Amount Billed</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="9" class="text-left">Total</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
let table_list_student_billing = $('#table_list_student_billing').DataTable({
    ordering: false,
    paging: false,
    info: false,
    processing: true,
    // fixedColumns: {
    //     left: 2,
    // },
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'Student Billing',
            exportOptions: {
                columns: ':visible'
            }
        },
    ],
    ajax: {
        url: '<?= base_url()?>finance/report/get_student_all_billing',
        type: 'POST',
        data: function(d) {
            d.student_status = 'active';
        }
    },
    columns: [
        {
            data: 'student_id',
            // className: 'bg-white',
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: 'personal_data_name',
            // className: 'bg-white',
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>finance/invoice/lists/' + row.personal_data_id + '" target="_blank">' + data + '</a>';
            }
        },
        {data: 'faculty_abbreviation'},
        {data: 'study_program_abbreviation'},
        {data: 'student_number'},
        {data: 'academic_year_id'},
        {data: 'student_status'},
        {
            data: 'list_billing',
            render: function(data, type, row) {
                var html = '';
                if (data) {
                    $.each(data, function(i, v) {
                        // var billing = '<li>'+ v.fee_description + '(semester ' + v.semester_number + ')' +'</li>';
                        var billing = v.fee_description + '(semester ' + v.semester_number + ')';
                        html += billing;
                    })
                }
                return html;
            }
        },
        {
            data: 'list_billing',
            render: function(data, type, row) {
                var html = '';
                if (data) {
                    $.each(data, function(i, v) {
                        // var billing = '<li>'+ v.invoice_details_amount +'</li>';
                        var billing = v.invoice_details_amount;
                        html += billing;
                    })
                }
                return html;
            }
        },
        {
            data: 'list_billing',
            render: function(data, type, row) {
                var html = '';
                var total = 0;
                if (data) {
                    $.each(data, function(i, v) {
                        total += parseInt(v.invoice_details_amount);
                        // var billing = '<li>'+ v.invoice_details_amount +'</li>';
                        // html += billing;
                    })
                }
                return total;
            }
        },
    ],
    footerCallback: function (row, data, start, end, display) {
        let api = this.api();
        let intVal = function (i) {
            return typeof i === 'string'
                ? i.replace(/[\$,]/g, '') * 1
                : typeof i === 'number'
                ? i
                : 0;
        };
        
        total = api.column(9).data().reduce((a, b) => intVal(a) + intVal(b), 0);
 
        // Update footer
        api.column(9).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total);
    }
});
</script>