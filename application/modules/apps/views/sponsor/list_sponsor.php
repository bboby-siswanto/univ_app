<div class="containter pr-4 pl-4">
    <div class="card">
        <div class="card-header">
            IULIFest Sponsor Invoice
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="sponsor_list">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Sponsor Name</th>
                                    <th>Invoice Number</th>
                                    <th>Amount Billed IDR</th>
                                    <th>Paid Date</th>
                                    <th>Amount Paid IDR</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var sponsor_list = $('#sponsor_list').DataTable({
    ordering: false,
    ajax: {
        url: "<?=base_url()?>apps/sponsor/get_invoice_sponsor",
        method: 'POST',
    },
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'IULIFest Sponsor Invoice',
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            text: 'Print',
            extend: 'print',
            title: 'IULIFest Sponsor Invoice',
            exportOptions: {columns: ':visible'}
        },
        // {
        //     text: 'Column Visibility',
        //     action: function () {
        //         // show columns
        //     }
        // },
        'colvis'
    ],
    columns: [
        {
            data: 'personal_data_name',
            render: function(data, type, row) {
                var link = '<a href="<?=base_url()?>apps/sponsor/generate_invoice/' + row.invoice_number.replace('-', '_') + '" target="blank">' + data + '</a>';
                return '<span id="' + row.invoice_id + '"></span>' + link;
            }
        },
        {data: 'invoice_number'},
        {
            data: 'payment_amount',
            // className: 'dt-body-right'
        },
        {
            data: 'sub_invoice_details_datetime_paid_off',
            render: function(data, type, row) {
                return (data === null) ? '-' : data;
            }
        },
        {data: 'paid_amount'},
        {
            data: 'invoice_status',
            render: function(data, type, row) {
                return data.toUpperCase();
            }
        },
    ],
    // columnDefs: [
    //     {
    //         targets: 2,
    //         className: 'dt-body-right'
    //     }
    // ]
});

function reload_data() {
    sponsor_list.ajax.reload(null, true);
}

$(document).ready(function() {
    setInterval(reload_data ,60000);
});
</script>