<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            Academic History Filter
        </div>
        <div class="card-body">
            <form method="post" id="academic_filter_form">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="graduation_year">Graduation Year</label>
                            <select name="academic_history_graduation_year" id="graduation_year" class="form-control">
                                <option value="all">All</option>
                            <?php
                                for ($i = intval(date('Y')) ; $i >= 2000 ; $i--) { 
                                print("<option value='$i'>$i</option>");
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="major_discipline">Major/Discipline</label>
                            <select name="academic_history_major" id="major_discipline" class="form-control">
                                <option value="all">All</option>
                                <option value="IPA">IPA</option>
                                <option value="IPS">IPS</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" id="filter_student" class="btn btn-primary pull-right">Filter</button>
            </form>
        </div>
    </div>
</div>