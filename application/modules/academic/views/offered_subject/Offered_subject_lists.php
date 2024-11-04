<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Curriculum Filter</div>
            <div class="card-body">
                <?= modules::run('academic/curriculum/form_filter_curriculum_offered_subject');?>
                <hr>
                <ul class="list-group text-danger list-notes">
                    <li class="list-group-item">
                        <div class="media">
                            <span class="align-self-center mr-3"><i class="fa fa-warning"></i></span>
                            <div class="media-body">
                                Mechatronics Engineering (MTE) and Biomedical Engineering (BME) are sub study program of Electrical Engineering (ELE). <br>
                                All subjects offered from ELE will also be offered to MTE and BME.
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="media">
                            <span class="align-self-center mr-3"><i class="fa fa-warning"></i></span>
                            <div class="media-body">
                                Aviation Management (AVM) is a sub study program of Management (MGT). <br>
                                All subjects offered from MGT will also be offered to AVM.
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="media">
                            <span class="align-self-center mr-3"><i class="fa fa-warning"></i></span>
                            <div class="media-body">
                                Automotive Engineering (AUE) is a sub study program of Mechanical Engineering (MEE). <br>
                                All subjects offered from MEE will also be offered to AUE.
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="media">
                            <span class="align-self-center mr-3"><i class="fa fa-warning"></i></span>
                            <div class="media-body">
                                Subjects offered in a specific sub study program will be marked automatically as "ELECTIVE". <br>
                                Please be aware of multiple subjects with different subject type (Elective or Mandatory) and subject credits.
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="media">
                            <span class="align-self-center mr-3"><i class="fa fa-warning"></i></span>
                            <div class="media-body">
                                Sub study program curriculum will not be available and the previous record will remain in archive.
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card" id="target">
            <div class="card-header">Offered Subject Filter</div>
            <div class="card-body">
                <?= modules::run('academic/offered_subject/form_filter_offered_subject');?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Curriculum Subject Lists <span id="curriculum_data_filter"></span></div>
            <div class="card-body">
                <?= modules::run('academic/curriculum/view_table_curriculum_subject');?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Offered Subject Lists <span id="data_filter"></span></div>
            <div class="card-body">
                <?= modules::run('academic/offered_subject/view_table_offered_subject');?>
            </div>
        </div>
    </div>
</div>