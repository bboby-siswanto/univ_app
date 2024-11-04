<?= (isset($student_data)) ? modules::run('student/show_name', $student_data->student_id, true) : '';?>
<div class="card">
    <div class="card-header">
        Payment History
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_list" class="table">
                <thead>
                    <tr>
                        <th>Virtual Account</th>
                        <th>Payment Date</th>
                        <th>Payment Amount</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_payment_invoice">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body-detail">
                <div class="row">
                    <div class="col-sm-6">
                        Beneficiary Name: <span id="payment_beneficiary_name"></span>
                    </div>
                    <div class="col-sm-6">
                        Payment Date: <span id="payment_payment_date"></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <input type="hidden" id="bni_id" name="bni_id">
                    <table id="table_details" class="table table-hover">
                        <thead>
                            <tr>
                                <th>VA. No</th>
                                <th>Invoice Number</th>
                                <th>Payment Description</th>
                                <th>Payment Amount</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
let table_list = $('#table_list').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false,
    ajax: {
        url: '<?=site_url('student/finance/get_payment_student')?>',
        data: function(d){
            d.payment_type = 'all';
            d.personal_data_id = '<?=$student_data->personal_data_id?>';
        },
        method: 'POST'
    },
    columns: [
        {data: 'sub_invoice_details_va_number'},
        {data: 'datetime_payment'},
        {
            data: 'total_payment_amount',
            render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'
        },
        {
            data: 'payment_id',
            render: function(data, type, row) {
                var receipt = row.receipt_no;
                receipt = receipt.replaceAll('/', '-');
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
                html +='<button type="button" class="btn btn-primary" id="view_payment_details">View Payment</button>';
                html +='<a href="<?=base_url()?>finance/invoice/get_receipt/' + row.bni_transactions_id + '/Receipt-' + receipt + '" class="btn btn-success" id="download_receipt" target="_blank">Download Receipt</a>';
                html += '</div>';
                return html;
            }
        },
    ]
});

let table_detail = $('#table_details').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false,
    ajax: {
        url: '<?=site_url('student/finance/get_payment_detail')?>',
        data: function(d){
            d.bni_id = $('#bni_id').val();
        },
        method: 'POST'
    },
    columns: [
        {data: 'sub_invoice_details_va_number'},
        {data: 'invoice_number'},
        {data: 'sub_invoice_details_description'},
        {
            data: 'payment_amount',
            render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'
        },
    ]
});
$(function() {
    $('#table_list tbody').on('click', 'button#view_payment_details', function(e) {
        e.preventDefault();

        var table_data = table_list.row($(this).parents('tr')).data();
        $('#bni_id').val(table_data.bni_transactions_id);
        
        table_detail.ajax.reload();
        $('#payment_beneficiary_name').text(table_data.customer_name);
        $('#payment_payment_date').text(table_data.datetime_payment);
        $('#modal_payment_invoice').modal('show');
    })
})
</script>