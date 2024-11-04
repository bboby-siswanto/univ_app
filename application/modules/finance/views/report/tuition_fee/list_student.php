<div class="row">
    <div class="col-12">
        <h4><?=isset($status_filter) ? ucfirst(strtolower($status_filter)) : '';?> Student</h4>
        <div class="table-responsive">
            <table id="table_student_body_<?=isset($status_filter) ? $status_filter : '';?>" class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th rowspan="2" class="bg-white">No</th>    
                        <th rowspan="2" class="bg-white">Student Name</th>    
                        <th rowspan="2">Fac</th>
                        <th rowspan="2">SP</th>
                        <th rowspan="2">Student ID</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Batch</th>
                        <th rowspan="2">Student Status</th>
                        <th colspan="14">Semester</th>
                        <th rowspan="2">Total Tunggakan</th>
                        <th rowspan="2">Tunggakan Semester Lain</th>
                        <th rowspan="2">Tunggakan Semester Dipilih</th>
                        <th rowspan="2">Note</th>
                    </tr>
                    <tr>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                        <th>8</th>
                        <th>9</th>
                        <th>10</th>
                        <th>11</th>
                        <th>12</th>
                        <th>13</th>
                        <th>14</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="22" class="text-left">Total</th>
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
<div class="modal" tabindex="-1" role="dialog" id="modal_note_<?=isset($status_filter) ? $status_filter : '';?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">All Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <ul class="list-group list-note">
                <li class="list-group-item"></li>
            </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
let table_student_body_<?=isset($status_filter) ? $status_filter : '';?> = $('#table_student_body_<?=isset($status_filter) ? $status_filter : '';?>').DataTable({
    processing: true,
    ordering: false,
    // searching: false,
    fixedColumns: {
        left: 2,
        // right: 1
    },
    paging: false,
    info: false,
    ajax: {
        url: '<?= base_url()?>finance/report/get_student_body',
        type: 'POST',
        data: function(d) {
            d.student_status = '<?=isset($status_filter) ? $status_filter : '-';?>';
            d.semester_selected = $('#select_semester').val();
        }
    },
    columns: [
        {
            data: 'student_id',
            className: 'bg-white',
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: 'personal_data_name',
            className: 'bg-white',
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>finance/invoice/lists/' + row.personal_data_id + '" target="_blank">' + data + '</a>';
            }
        },
        {data: 'faculty_abbreviation'},
        {data: 'study_program_abbreviation'},
        {
            data: 'student_number',
            // render: function(data, type, row) {
            //     var classtype = (row.have_onleave) ? 'text-danger' : '';
            //     return '<span class="' + classtype + '">' + data + '</span>';
            // }
        },
        {data: 'student_type'},
        {data: 'academic_year_id'},
        {data: 'student_status'},
        {
            data: 'sem_1',
            className: 'text-right'
        },
        {
            data: 'sem_2',
            className: 'text-right'
        },
        {
            data: 'sem_3',
            className: 'text-right'
        },
        {
            data: 'sem_4',
            className: 'text-right'
        },
        {
            data: 'sem_5',
            className: 'text-right'
        },
        {
            data: 'sem_6',
            className: 'text-right'
        },
        {
            data: 'sem_7',
            className: 'text-right'
        },
        {
            data: 'sem_8',
            className: 'text-right'
        },
        {
            data: 'sem_9',
            className: 'text-right'
        },
        {
            data: 'sem_10',
            className: 'text-right'
        },
        {
            data: 'sem_11',
            className: 'text-right'
        },
        {
            data: 'sem_12',
            className: 'text-right'
        },
        {
            data: 'sem_13',
            className: 'text-right'
        },
        {
            data: 'sem_14',
            className: 'text-right'
        },
        {
            data: 'ori_total_tunggakan',
            className: 'text-right',
            render: $.fn.dataTable.render.number('.', '.', 0, '')
        },
        {
            data: 'ori_semester_lain_tunggakan',
            className: 'text-right',
            render: $.fn.dataTable.render.number('.', '.', 0, '')
        },
        {
            data: 'ori_semester_tunggakan',
            className: 'text-right',
            render: $.fn.dataTable.render.number('.', '.', 0, '')
        },
        {
            data: 'student_id',
            render: function(data, type, row) {
                let note = row.student_note;
                var html = "";
                if (note.length > 0) {
                    html = '<button type="button" id="btn_note_view_<?=isset($status_filter) ? $status_filter : '';?>" class="btn btn-info"><i class="fas fa-eye"></i></button>';
                }
                return html;
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
        
        total = api.column(22).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_lain = api.column(23).data().reduce((a, b) => intVal(a) + intVal(b), 0);
        total_semester = api.column(24).data().reduce((a, b) => intVal(a) + intVal(b), 0);
 
        // Update footer
        api.column(22).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total);
        api.column(23).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_lain);
        api.column(24).footer().innerHTML = $.fn.dataTable.render.number('.', '.', 0, '').display(total_semester);

<?php
if (isset($status_filter)) {
    if ($status_filter == 'active') {
?>
        $('#summ_arrears_prev_semester').html($.fn.dataTable.render.number('.', '.', 0, '').display(total_lain));
        $('#total_owed_active').html($.fn.dataTable.render.number('.', '.', 0, '').display(total));
        var total_owed_graduated = $('#total_owed_graduated').text();
        total_owed_graduated = total_owed_graduated.replace(/[\$.]/g, '');
        var total_owed = parseFloat(total_owed_graduated) + parseFloat(total);

        $('#total_owed').html($.fn.dataTable.render.number('.', '.', 0, '').display(total_owed));
<?php
    }
    else if ($status_filter == 'graduated') {
?>
        $('#total_owed_graduated').html($.fn.dataTable.render.number('.', '.', 0, '').display(total));
        var total_owed_active = $('#total_owed_active').text();
        total_owed_active = total_owed_active.replace(/[\$.]/g, '');
        var total_owed = parseFloat(total_owed_active) + parseFloat(total);

        $('#total_owed').html($.fn.dataTable.render.number('.', '.', 0, '').display(total_owed));
<?php
    }
}
?>
    }
});

$(function() {
    $('table#table_student_body_<?=isset($status_filter) ? $status_filter : '';?> tbody').on('click', 'button#btn_note_view_<?=isset($status_filter) ? $status_filter : '';?>', function(e) {
        e.preventDefault();
        var row_data = table_student_body.row($(this).parents('tr')).data();
        var note = row_data.student_note;
        
        var notelist = $('.list-note');
        notelist.empty();

        note.forEach(value => {
            var list = '<li class="list-group-item">' + value + '</li>';
            notelist.append(list);
        });

        $('#modal_note_<?=isset($status_filter) ? $status_filter : '';?>').modal('show');
    });
})
</script>