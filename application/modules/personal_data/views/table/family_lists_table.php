<div class="card">
    <div class="card-header">
        Family Lists
        <!-- <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_family_member">
                <i class="fa fa-plus"></i> Family
            </button>
        </div> -->
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="family_lists" class="table table-hover table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>Name</th>
                        <th>Family Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
$(function() {
    var family_table = $('table#family_lists').DataTable({
        paging: false,
        searching: false,
        info: false,
        order: [[ 1, "desc" ], [ 0, "asc" ]],
        ajax: {
            url: '<?= base_url()?>personal_data/family/get_family_list',
            type: 'POST',
            data: {
                family_id: '<?=$family_id;?>'
            }
        },
        columns: [
            {
                data: 'personal_data_name',
                orderable: false
            },
            {
                data: 'personal_data_id',
                orderable: false,
                render: function(data, type, row) {
                    return row['family_member_status'].toUpperCase();
                }
            }
        ]
    });
});
</script>