<html>
    <style>
        table, th, td {
            border: 1px solid black; border-collapse: collapse;
        }
    </style>
    <body>
        <h2>Per <?=date('d M Y H:i')?></h2>
    <?php
    if ($list_data) {
        foreach ($list_data as $o_faculty_data) {
            if ($o_faculty_data->have_alumni) {
                print('<h3>'.str_replace('Faculty of ', '', $o_faculty_data->faculty_name).'</h3>');
                print('<hr>');
                if ($o_faculty_data->prodi_list) {
                    foreach ($o_faculty_data->prodi_list as $o_prodi) {
                        if ($o_prodi->alumni_list) {
                            print('<h3>'.$o_prodi->study_program_name.'</h3>');
                            ?>
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="40%">Alumni Name</th>
                                            <th width="20%">Data Tracer</th>
                                            <th width="30%">Last Submit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            <?php
                                $i_num = 1;
                                foreach ($o_prodi->alumni_list as $o_alumni) {
                                    print('<tr>');
                                    print('<td>'.$i_num.'</td>');
                                    print('<td>'.$o_alumni->personal_data_name.'</td>');
                                    print('<td>'.(($o_alumni->answer_data) ? 'OK' : '').'</td>');
                                    print('<td>'.$o_alumni->last_submit.'</td>');
                                    print('</tr>');
                                    $i_num++;
                                }
                            ?>
                                    </tbody>
                                </table>
                                <!-- <p><br></p> -->
                                <pagebreak>
                            <?php
                        }
                    }
                }
            }
        }
    }
    ?>
    </body>
</html>