<?php
$month_installment = [
    '1' => ['July', 'August', 'September', 'October', 'November', 'December'],
    '2' => ['January', 'February', 'March', 'April', 'May', 'June']
];
?>
<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table id="table_semester_body" class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2" class="bg-white">No</th>    
                        <th rowspan="2" class="bg-white">Student Name</th>   
                        <th rowspan="2">Fac</th>
                        <th rowspan="2">SP</th>
                        <th rowspan="2">Student ID</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Batch</th>
                        <th colspan="6">Installment Unpaid</th>
                        <th rowspan="2">Total Unpaid</th>
                    </tr>
                    <tr>
            <?php
            if ((isset($list_installment)) AND (is_array($list_installment))) {
                foreach ($list_installment as $key => $value) {
                    print('<th>'.date('M', strtotime('2023-'.$value.'-1')).'</th>');
                }
            }
            else {
            ?>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
            <?php
            }
            ?>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="7">Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
let table_semester_body = $('#table_semester_body').DataTable({
    processing: true,
    // searching: false,
    fixedColumns: {
        left: 2,
    },
    paging: false,
    info: false,
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'Unpaid Installment'
        }
    ],
    ajax: {
        url: '<?= base_url()?>finance/report/get_current_semester_new',
        type: 'POST',
        data: function(d) {
            d.student_status = 'active';
            d.semester_selected = $('#select_semester').val();
        }
    },
    columns: [
        {
            data: 'student_id',
            className: 'bg-white',
            orderable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: 'personal_data_name',
            className: 'bg-white',
            orderable: false,
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>finance/invoice/lists/' + row.personal_data_id + '" target="_blank">' + data + '</a>';
            }
        },
        {data: 'faculty_abbreviation',orderable: false},
        {data: 'study_program_abbreviation',orderable: false},
        {data: 'student_number',orderable: false},
        {data: 'student_type',orderable: false},
        {data: 'student_batch',orderable: false},
        {
            data: 'installment_1',
            orderable: false,
            className: 'text-right',
            render: function(data, type, row, display) {
                // let installment = row.installment;
                return $.fn.dataTable.render.number('.', '.', 0, '').display(data);
            }
        },
        {
            data: 'installment_2',
            orderable: false,
            className: 'text-right',
            render: function(data, type, row, display) {
                let installment = row.installment;
                return $.fn.dataTable.render.number('.', '.', 0, '').display(data);
            }
        },
        {
            data: 'installment_3',
            orderable: false,
            className: 'text-right',
            render: function(data, type, row, display) {
                let installment = row.installment;
                return $.fn.dataTable.render.number('.', '.', 0, '').display(data);
            }
        },
        {
            data: 'installment_4',
            orderable: false,
            className: 'text-right',
            render: function(data, type, row, display) {
                let installment = row.installment;
                return $.fn.dataTable.render.number('.', '.', 0, '').display(data);
            }
        },
        {
            data: 'installment_5',
            orderable: false,
            className: 'text-right',
            render: function(data, type, row, display) {
                let installment = row.installment;
                return $.fn.dataTable.render.number('.', '.', 0, '').display(data);
            }
            // render: $.fn.dataTable.render.number('.', '.', 0, '')
        },
        {
            data: 'installment_6',
            orderable: false,
            className: 'text-right',
            render: function(data, type, row, display) {
                let installment = row.installment;
                return $.fn.dataTable.render.number('.', '.', 0, '').display(data);
            }
            // render: $.fn.dataTable.render.number('.', '.', 0, '')
        },
        {
            data: 'total_unpaid_installment',
            orderable: false,
            className: 'text-right',
            render: $.fn.dataTable.render.number('.', '.', 0, '')
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

        // var col_installment = 7;
        // for (let installment = parseInt(start_month); installment <= parseInt(last_month); installment++) {
        //     total_installment += api.column(col_installment).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        //     col_installment++;
        // }
        
        // if (col_installment <= 12) {
        //     for (let x = col_installment; x <= col_installment; x++) {
        //         total_waiting_installment += api.column(col_installment).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        //         col_installment++;
        //     }
        // }
        // var test_error = api.column(0).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        
        
        total_1 = api.column(7).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_2 = api.column(8).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_3 = api.column(9).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_4 = api.column(10).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_5 = api.column(11).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_6 = api.column(12).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_all = api.column(13).data().reduce((a, b) => intVal(a) + intVal(b), 0);
 
        // Update footer
        api.column(7).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_1);
        api.column(8).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_2);
        api.column(9).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_3);
        api.column(10).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_4);
        api.column(11).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_5);
        api.column(12).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_6);
        api.column(13).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_all);

        // for summary tab
        var start_month = parseInt('<?=$start_month;?>');
        start_month = (start_month > 6) ? start_month - 6 : start_month;
        var last_month = parseInt('<?=$prev_month;?>');
        last_month = (last_month > 6) ? last_month - 6 : last_month;
        var current_month = parseInt('<?=$current_month;?>');
        current_month = (current_month > 6) ? current_month - 6 : current_month;
        
        var total_prev_installment = 0;
        var total_waiting_installment = 0;
        var current_month_installment = 0;

        var apicolumn = 1;

        for (let x = 1; x <= 6; x++) {
            if (x <= last_month) {
                total_prev_installment += window['total_' + x];
            }
            else if (x == current_month) {
                current_month_installment += window['total_' + x];
            }
            else {
                total_waiting_installment += window['total_' + x];
            }
        }

        $('#arrears_current_semester_previous').html($.fn.dataTable.render.number('.', '.', 0, '').display(total_prev_installment));
        $('#arrears_current_semester_current_month').html($.fn.dataTable.render.number('.', '.', 0, '').display(current_month_installment));
        $('#arrears_current_semester_until_last').html($.fn.dataTable.render.number('.', '.', 0, '').display(total_waiting_installment));
        // end summary tab
    }
});

$(function() {
    $('#semester_select_semester').on('change', function(e) {
        table_semester_body.ajax.reload();
    })
})
</script>