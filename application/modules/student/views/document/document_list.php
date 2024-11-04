<div class="card">
    <div class="card-header">Document List</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="list_table" class="table table-hover">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if ((isset($doc_list)) AND ($doc_list)) {
                foreach ($doc_list as $doc) {
            ?>
                    <tr>
                        <td><?=$doc;?></td>
                        <td>
                            <div class="btn-group">
                            <a class="btn btn-sm btn-success" href="<?=base_url()?>student/documents/download/<?=(isset($s_category)) ? $s_category : ''; ?>/<?=urldecode($doc)?>" title="Download File"><i class="fas fa-download"></i></a>
                <?php
                if ($this->session->userdata('type') == 'staff') {
                ?>
                            <button id="remove_file" class="btn btn-danger btn-sm" type="button" title="Remove File"><i class="fas fa-trash"></i></button>
                <?php
                }
                ?>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(function() {
    var table_list = $('table#list_table').DataTable({
        paging: false,
        bInfo: false,
        // ordering: false
    });
})
</script>