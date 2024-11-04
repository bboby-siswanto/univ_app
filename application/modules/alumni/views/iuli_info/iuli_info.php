<div class="head-info">
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                IULI Info
        <?php
            if ($this->session->userdata('type') == 'staff') {
        ?>
                <div class="card-header-actions">
                    <a href="<?= base_url()?>alumni/iuli_info/my_info" class="card-header-action btn btn-link" id="btn_setting_info">
                        <i class="fa fa-cog"></i> Setting info
                    </a>
                </div>
        <?php
            }
        ?>
            </div>
        </div>
    </div>
</div>
</div>

<?= modules::run('iuli_info/form_list_info');?>