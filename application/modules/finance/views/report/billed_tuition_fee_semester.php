<div class="card">
    <div class="card-header">
        Billed Tuition Fee Report Semester 2021-2
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h5>Legend :</h5>
                <div class="row">
                    <div class="col-4">
                        <table>
                            <tr>
                                <td class="bg-secondary pl-4"></td>
                                <td class="pl-2">Inactive Student</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-4">
                        <table>
                            <tr>
                                <td class="bg-warning pl-4"></td>
                                <td class="pl-2">Onleave Student</td>
                            </tr>
                        </table>
                    </div>
                    <!-- <div class="col-4">
                        <table>
                            <tr>
                                <td class="bg-secondary pl-4"></td>
                                <td class="pl-3">Inactive Student</td>
                            </tr>
                        </table>
                    </div> -->
                </div>
                <!-- <div class="float-left">
                    <div class="bg-secondary p-2">Inactive Student</div>
                </div> -->
            </div>
            <div class="col-sm-6">
                <div class="float-right">
                    <h5>Action :</h5>
                    <a href="/" id="link_view_invoice"></a>
                    <div class="btn-group" role="group" aria-label="Action Group">
                        <button type="button" class="btn btn-primary" id="btn_act_view_note" disabled>View Notes</button>
                        <button type="button" class="btn btn-primary" id="btn_act_view_invoice" disabled>View Invoice</button>
                        <button type="button" class="btn btn-primary" id="btn_act_historycal" disabled>View Historycal Billing</button>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="table_tf_list" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th></th>
                        <th>Student Name</th>
                        <th>Current Status</th>
                        <th>Batch</th>
                        <th>Year In</th>
                        <th>Prodi</th>
                        <th>Current Semester</th>
                        <th>Semester Leave</th>
                        <th>Discount / Scholarship</th>
                        <th>SKS Approved</th>
                        <th>Billed Semester</th>
                        <th>Flyer Amount</th>
                        <th>Billed Amount</th>
                        <th>Total Paid</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var table_tf_list = $('#table_tf_list').DataTable({
    processing: true,
    ajax: {
        url: '<?=base_url()?>finance/report/billed_tuition_fee_semester',
        type: 'POST',
        data: function(d) {
            d.academic_year_id = 2021,
            d.semester_type_id = 2
        }
    },
    columns: [
        {
            data: 'invoice_id',
            className: 'select-checkbox',
            render: function(data, type, row) {
                var html = '<input type="hidden" value="' + data + '" name="invoice_id">';
                return html;
            }
        },
        {data: 'personal_data_name'},
        {data: 'student_status'},
        {data: 'student_batch'},
        {data: 'finance_year_id'},
        {data: 'study_program_abbreviation'},
        {data: 'current_semester'},
        {data: 'semester_leave'},
        {data: 'discount_scholarship'},
        {data: 'sks_approved'},
        {data: 'billed_semester'},
        {data: 'flyer_amount'},
        {data: 'billed_amount'},
        {data: 'total_paid'}
    ],
    createdRow: function(row, data, dataIndex){
        if( data.student_status == 'inactive'){
            $(row).addClass('bg-secondary');
        }
        else if( data.student_status == 'onleave'){
            $(row).addClass('bg-warning');
        }
        // else if( data.student_status == 'inactive'){
        //     $(row).addClass('bg-secondary');
        // }
        console.log(data.student_status);
    },
    select: {
        style: 'single'
    }
});

$(function() {
    var btn_act_view_note = $('button#btn_act_view_note');
    var btn_act_view_invoice = $('button#btn_act_view_invoice');
    var btn_act_historycal = $('button#btn_act_historycal');
    var link_view_invoice = $('a#link_view_invoice');

    btn_act_view_note.attr('disabled', 'disabled');
    btn_act_view_invoice.attr('disabled', 'disabled');
    btn_act_historycal.attr('disabled', 'disabled');
    link_view_invoice.attr('href', '/');

    table_tf_list.on('select', function(e, dt, type, indexes) {
        var row_data = table_tf_list.row(indexes).data();

        btn_act_view_note.removeAttr('disabled');
        btn_act_historycal.removeAttr('disabled');
        
        if (row_data.invoice_id == '#' || row_data.invoice_id == '' || row_data.invoice_id == null) {
            btn_act_view_invoice.attr('disabled', 'disabled');
            link_view_invoice.attr('href', '/');
        }
        else {
            btn_act_view_invoice.removeAttr('disabled');
            link_view_invoice.attr('href', '<?=base_url()?>finance/invoice/lists/' + row_data.personal_data_id);
        }

    }).on('deselect', function(e, dt, type, indexes) {
        btn_act_view_note.attr('disabled', 'disabled');
        btn_act_view_invoice.attr('disabled', 'disabled');
        btn_act_historycal.attr('disabled', 'disabled');
        link_view_invoice.attr('href', '/');
    });

    $('button#btn_act_view_invoice').on('click', function(e) {
        e.preventDefault();
        link_view_invoice[0].click();
    });
})
</script>