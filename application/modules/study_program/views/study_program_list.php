<div class="card">
    <div class="table-responsive">
        <table class="table table-bordered" id="table_prodi">
            <thead class="bg-dark">
                <tr>
                    <th>Study Program Name</th>
                    <th>Abbreviation</th>
                    <th>Faculty</th>
                    <th>SK Akreditasi</th>
                    <th>Prodi Code</th>
                    <th>Head of Department</th>
                    <th>Dean Name</th>
                    <th>Prodi Key</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ((isset($prodi_data)) AND ($prodi_data)) {
        foreach ($prodi_data as $o_prodi) {
    ?>
                <tr>
                    <td><?=$o_prodi->study_program_name;?></td>
                    <td><?=$o_prodi->study_program_abbreviation;?></td>
                    <td><?=$o_prodi->faculty_abbreviation;?></td>
                    <td><?=$o_prodi->study_program_sk_accreditation;?></td>
                    <td><?=$o_prodi->study_program_code;?></td>
                    <td><?=$o_prodi->head_of_study_program_name;?></td>
                    <td><?=$o_prodi->deans_name;?></td>
                    <td><?=$o_prodi->study_program_id;?></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-success btn-sm" href="<?=base_url()?>feeder/student/sync_student_semester/all/<?=$o_prodi->study_program_id;?>/<?=$this->session->userdata('academic_year_id_active').$this->session->userdata('semester_type_id_active');?>" target="blank" title="sync student semester"><i class="fas fa-sync"></i></a>
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