<?= (isset($student_data)) ? modules::run('student/show_name', $student_data->student_id, true) : '';?>
<div class="card">
    <div class="card-header">
        Unpaid Invoice List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_list" class="table">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Description</th>
                        <th>Amount Billed (IDR)</th>
                        <th>Total Fined (IDR)</th>
                        <th>Total Paid (IDR)</th>
                        <th>Total Amount Billed (IDR)</th>
                <?php
                // if (($this->session->has_userdata('develepment_mode')) AND ($this->session->userdata('develepment_mode')) ) {
                ?>
                        <th>#</th>
                        <!-- View Payment Schema (full payment/installment) -->
                <?php
                // }
                ?>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_payment_method">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body-method">
                <p>Modal body text goes here.</p>
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
        url: '<?=site_url('student/finance/get_unpaid_invoice')?>',
        // data: function(d){
        //     d.payment_type = 'all';
        //     d.personal_data_id = '<?=$this->session->userdata('user');?>';
        //     // d.student_invoice_type = $('select#student_invoice_type').val();
        //     // d.invoice_academic_year_id = $('select#invoice_academic_year_id').val();
        //     // d.invoice_semester_type_id = $('select#invoice_semester_type_id').val();
        // },
        method: 'POST'
    },
    columns: [
        {data: 'invoice_number'},
        {data: 'invoice_description'},
        {data: 'invoice_amount_billed', render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'},
        {data: 'invoice_amount_fined', render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'},
        {data: 'invoice_amount_paid', render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'},
        {data: 'invoice_amount_total', render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'},
<?php
// if (($this->session->has_userdata('develepment_mode')) AND ($this->session->userdata('develepment_mode')) ) {
?>
        {
            data: 'invoice_id',
            render: function(data, type, row) {
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
                html +='<button type="button" class="btn btn-primary" id="view_payment_method">payment method</button>';
                html += '</div>';
                return html;
            }
        },
<?php
// }
?>
    ]
});
$(function() {
    $('#table_list tbody').on('click', 'button#view_payment_method', function(e) {
        e.preventDefault();

        var table_data = table_list.row($(this).parents('tr')).data();
        $('#body-method').html('');
        $.post('<?=base_url()?>callback/api/get_payment_method/' + table_data.invoice_id + '/ajax', false, function(result) {
            var page = result.body;
            $('#body-method').html(page);
        }, 'json').fail(function(params) {
            toastr.error('Error get data', 'Error');
        });

        $('#modal_payment_method').modal('show');
    })
})
</script>