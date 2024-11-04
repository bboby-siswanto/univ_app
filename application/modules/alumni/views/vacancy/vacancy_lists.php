<div class="row">
    <div class="col">
        <div class="table-responsive">
            <table id="vacancy_lists" class="table" width="100%">
                <thead class="d-none"></thead>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#vacancy_lists').DataTable({
            info: false,
            lengthChange: false,
            ajax: {
                url: '<?= base_url()?>alumni/vacancy/get_data_filtered/false',
                type: 'POST'
            },
            columns: [
                {data: 'job_vacancy_id'}
            ],
            columnDefs: [
                {
                    render: function ( data, type, row ) {
                        var address = row.address_street +' '+ row.address_city+' '+row.address_province+ ' ' +row.country_name+' '+row.address_zipcode;
                        var html = '<a href="<?= base_url()?>alumni/vacancy/job_detail/' + row.job_vacancy_id + '" class="unstyle-link">';
                        html += '<h5>'+row.institution_name+' - <span>' + row.ocupation_name + '</span></h5>';
                        html += '<p>' + address + '</p></a>';
                        return html;
                    },
                    targets: 0
                }
            ]
        });
    });
</script>