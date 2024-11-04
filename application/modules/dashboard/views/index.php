<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="brand-card">
                    <div class="card-header bg-primary text-center">
                        <h1 class="display-3"><?= $student_statistics['candidate']['total']; ?></h1>
                        <strong>Candidate Students<div><?= $student_statistics['active_year']; ?></div></strong>
                    </div>
                    <div class="card-body">
                <?php
                    if ($student_statistics['candidate']['list']) {
                        foreach ($student_statistics['candidate']['list'] as $list) {
                ?>
                        <div class="card" style="width: 100%;">
                            <div class="card-body p-0 d-flex align-items-center">
                                <span class="bg-primary p-2 mr-3 font-weight-bold"><?= $list->total; ?></span>
                                <div class="text-primary"><?= ($list->study_program_name == null) ? 'Have Not Chosen' : $list->study_program_name; ?></div>
                            </div>
                        </div>
                <?php
                        }
                    }
                ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="brand-card">
                    <div class="card-header bg-purple text-center text-white">
                        <h1 class="display-3"><?= $student_statistics['paid']['total']; ?></h1>
                        <strong>Paid Enrolment Fee<div><?= $student_statistics['active_year']; ?></div></strong>
                    </div>
                    <div class="card-body">
                <?php
                    if ($student_statistics['paid']['list']) {
                        foreach ($student_statistics['paid']['list'] as $list) {
                ?>
                        <div class="card" style="width: 100%;">
                            <div class="card-body p-0 d-flex align-items-center">
                                <span class="bg-primary p-2 mr-3 font-weight-bold"><?= $list->total; ?></span>
                                <div class="text-primary"><?= ($list->study_program_name == null) ? 'Have Not Chosen' : $list->study_program_name; ?></div>
                            </div>
                        </div>
                <?php
                        }
                    }
                ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="brand-card">
                    <div class="card-header bg-warning text-center">
                        <h1 class="display-3"><?= $student_statistics['pending']['total']; ?></h1>
                        <strong>Pending Students<div><?= $student_statistics['active_year']; ?></div></strong>
                    </div>
                    <div class="card-body">
                <?php
                    if ($student_statistics['pending']['list']) {
                        foreach ($student_statistics['pending']['list'] as $list) {
                ?>
                        <div class="card" style="width: 100%;">
                            <div class="card-body p-0 d-flex align-items-center">
                                <span class="bg-primary p-2 mr-3 font-weight-bold"><?= $list->total; ?></span>
                                <div class="text-primary"><?= ($list->study_program_name == null) ? 'Have Not Chosen' : $list->study_program_name; ?></div>
                            </div>
                        </div>
                <?php
                        }
                    }
                ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="brand-card">
                    <div class="card-header bg-success text-center">
                        <h1 class="display-3"><?= $student_statistics['active']['total']; ?></h1>
                        <strong>Active Students<div><?= $student_statistics['active_year']; ?></div></strong>
                    </div>
                    <div class="card-body">
                <?php
                    if ($student_statistics['active']['list']) {
                        foreach ($student_statistics['active']['list'] as $list) {
                ?>
                        <div class="card" style="width: 100%;">
                            <div class="card-body p-0 d-flex align-items-center">
                                <span class="bg-primary p-2 mr-3 font-weight-bold"><?= $list->total; ?></span>
                                <div class="text-primary"><?= ($list->study_program_name == null) ? 'Have Not Chosen' : $list->study_program_name; ?></div>
                            </div>
                        </div>
                <?php
                        }
                    }
                ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>