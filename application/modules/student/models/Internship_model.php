<?php
class Internship_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function save_internship($a_internship_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('dt_student_internship', $a_internship_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('dt_student_internship', $a_internship_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_internship_document($a_clause = false)
    {
        $this->db->select('*, st.academic_year_id');
        $this->db->from('dt_student_internship_doc sid');
        $this->db->join('dt_student_internship si', 'si.internship_id = sid.internship_id');
        $this->db->join('dt_student st', 'st.student_id = si.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_institution ri', 'ri.institution_id = si.institution_id', 'left');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('sid.document_type');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_internship_student($a_clause = false)
    {
        $this->db->select('*, st.academic_year_id');
        $this->db->from('dt_student_internship si');
        $this->db->join('ref_institution ri', 'ri.institution_id = si.institution_id', 'left');
        $this->db->join('dt_student st', 'st.student_id = si.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_internship_data($post_data, $o_student_data)
    {
        $this->db->trans_begin();
        $mba_institution_data = $this->General->get_where('ref_institution', ['institution_name' => $post_data['internship_company']]);
        if ($mba_institution_data) {
            $s_institution_id = $mba_institution_data[0]->institution_id;
        }
        else {
            $s_institution_id = $this->uuid->v4();
            $a_institution_data = [
                'institution_id' => $s_institution_id,
                'institution_name' => $post_data['internship_company'],
                'institution_type' => 'office',
                'date_added' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('ref_institution', $a_institution_data);
        }
        
        $s_internship_id = $this->uuid->v4();
        
        $mba_internship_data = false;
        if (!empty($post_data['internship_id'])) {
            $mba_internship_data = $this->General->get_where('dt_student_internship', ['internship_id' => $post_data['internship_id']]);
            if ($mba_internship_data) {
                $s_internship_id = $mba_internship_data[0]->internship_id;
            }
        }

        $a_student_internship_data = [
            'internship_id' => $s_internship_id,
            'student_id' => $post_data['student_id'],
            'institution_id' => $s_institution_id,
            'supervisor_name' => $post_data['internship_supervisor'],
            'department' => $post_data['internship_department']
        ];

        if ($mba_internship_data) {
            $this->db->update('dt_student_internship', $a_student_internship_data, ['internship_id' => $s_internship_id]);
        }
        else {
            $a_student_internship_data['date_added'] = date('Y-m-d H:i:s');
            $this->db->insert('dt_student_internship', $a_student_internship_data);
        }
        $s_file_path = STUDENTPATH.$o_student_data->personal_data_path.'internship/';
        $upload_success = false;
        $a_error_upload = [];
        if(!file_exists($s_file_path)){
            mkdir($s_file_path, 0755, true);
        }

        $s_academic_year_id_active = $this->session->userdata('academic_year_id_active');
        $s_semester_type_id_active = $this->session->userdata('semester_type_id_active');
        
        $config['allowed_types'] = 'pdf|jpg|doc|docx|xls|xlsx|bmp|jpeg|png';
        $config['max_size'] = 204800;
        $config['file_ext_tolower'] = true;
        $config['overwrite'] = true;
        $config['upload_path'] = $s_file_path;
        $this->load->library('upload', $config);

        $s_student_name = str_replace(' ', '-', str_replace("'" ,"", strtolower($o_student_data->personal_data_name)));
        $s_fname = $s_student_name.'_'.$s_academic_year_id_active.'-'.$s_semester_type_id_active.'_';

        $s_assessment_name = $s_fname.'_internship_assessment';
        $s_logsheet_name = $s_fname.'_internship_logsheet';
        $s_report_name = $s_fname.'_internship_report';
        $s_otherdoc1_name = $s_fname.'_internship_otherdoc1';
        $s_otherdoc2_name = $s_fname.'_internship_otherdoc2';

        if (!empty($_FILES['file_assessment']['name'])) {
            $config['file_name'] = $s_assessment_name;
            $this->upload->initialize($config);
            if($this->upload->do_upload('file_assessment')) {
                $a_internship_doc = [
                    'internship_id' => $s_internship_id,
                    'document_type' => 'assessment',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_assessment_name
                ];
                $file_exists = $this->General->get_where('dt_student_internship_doc', ['internship_id' => $s_internship_id, 'document_type' => 'assessment']);
                if ($file_exists) {
                    $this->db->update('dt_student_internship_doc', $a_internship_doc, ['internship_file_id' => $file_exists[0]->internship_file_id]);
                }
                else {
                    $a_internship_doc['internship_file_id'] = $this->uuid->v4();
                    $a_internship_doc['date_added'] = date('Y-m-d H:i:s');
                    $this->db->insert('dt_student_internship_doc', $a_internship_doc);
                }
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>1'));
            }
        }

        if (!empty($_FILES['file_logsheet']['name'])) {
            $config['file_name'] = $s_logsheet_name;
            $this->upload->initialize($config);
            if($this->upload->do_upload('file_logsheet')) {
                $a_internship_doc = [
                    'internship_id' => $s_internship_id,
                    'document_type' => 'logsheet',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_logsheet_name
                ];
                $file_exists = $this->General->get_where('dt_student_internship_doc', ['internship_id' => $s_internship_id, 'document_type' => 'logsheet']);
                if ($file_exists) {
                    $this->db->update('dt_student_internship_doc', $a_internship_doc, ['internship_file_id' => $file_exists[0]->internship_file_id]);
                }
                else {
                    $a_internship_doc['internship_file_id'] = $this->uuid->v4();
                    $a_internship_doc['date_added'] = date('Y-m-d H:i:s');
                    $this->db->insert('dt_student_internship_doc', $a_internship_doc);
                }
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>2'));
            }
        }

        if (!empty($_FILES['file_report']['name'])) {
            $config['file_name'] = $s_report_name;
            $this->upload->initialize($config);
            if($this->upload->do_upload('file_report')) {
                $a_internship_doc = [
                    'internship_id' => $s_internship_id,
                    'document_type' => 'report',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_report_name
                ];
                $file_exists = $this->General->get_where('dt_student_internship_doc', ['internship_id' => $s_internship_id, 'document_type' => 'report']);
                if ($file_exists) {
                    $this->db->update('dt_student_internship_doc', $a_internship_doc, ['internship_file_id' => $file_exists[0]->internship_file_id]);
                }
                else {
                    $a_internship_doc['internship_file_id'] = $this->uuid->v4();
                    $a_internship_doc['date_added'] = date('Y-m-d H:i:s');
                    $this->db->insert('dt_student_internship_doc', $a_internship_doc);
                }
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>3'));
            }
        }

        if (!empty($_FILES['file_other_1']['name'])) {
            $config['file_name'] = $s_otherdoc1_name;
            $this->upload->initialize($config);
            if($this->upload->do_upload('file_other_1')) {
                $a_internship_doc = [
                    'internship_id' => $s_internship_id,
                    'document_type' => 'other_doc_1',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_otherdoc1_name
                ];
                $file_exists = $this->General->get_where('dt_student_internship_doc', ['internship_id' => $s_internship_id, 'document_type' => 'other_doc_1']);
                if ($file_exists) {
                    $this->db->update('dt_student_internship_doc', $a_internship_doc, ['internship_file_id' => $file_exists[0]->internship_file_id]);
                }
                else {
                    $a_internship_doc['internship_file_id'] = $this->uuid->v4();
                    $a_internship_doc['date_added'] = date('Y-m-d H:i:s');
                    $this->db->insert('dt_student_internship_doc', $a_internship_doc);
                }
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>4'));
            }
        }

        if (!empty($_FILES['file_other_2']['name'])) {
            $config['file_name'] = $s_otherdoc2_name;
            $this->upload->initialize($config);
            if($this->upload->do_upload('file_other_2')) {
                $a_internship_doc = [
                    'internship_id' => $s_internship_id,
                    'document_type' => 'other_doc_2',
                    'document_link' => $this->upload->data('file_name'),
                    'document_name' => $s_otherdoc2_name
                ];
                $file_exists = $this->General->get_where('dt_student_internship_doc', ['internship_id' => $s_internship_id, 'document_type' => 'other_doc_2']);
                if ($file_exists) {
                    $this->db->update('dt_student_internship_doc', $a_internship_doc, ['internship_file_id' => $file_exists[0]->internship_file_id]);
                }
                else {
                    $a_internship_doc['internship_file_id'] = $this->uuid->v4();
                    $a_internship_doc['date_added'] = date('Y-m-d H:i:s');
                    $this->db->insert('dt_student_internship_doc', $a_internship_doc);
                }
            }
            else {
                array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>5'));
            }
        }

        if (count($a_error_upload) > 0) {
            $this->db->trans_rollback();
            $a_return = ['code' => 1, 'message' => implode(';', $a_error_upload)];
        }
        else if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->db->trans_commit();
            $this->send_notification($s_internship_id, $o_student_data->student_id);
            $a_return = ['code' => 0, 'message' => 'Success'];
        }

        return $a_return;
    }

    public function send_notification($s_internship_id, $s_student_id)
    {
        $this->db->from('dt_score sc');
        $this->db->join('dt_class_master cm', 'cm.class_master_id = sc.class_master_id');
        $this->db->join('dt_class_master_lecturer cml', 'cml.class_master_id = cm.class_master_id');
        $this->db->join('dt_employee em', 'em.employee_id = cml.employee_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->like('sn.subject_name', 'internship');
        $this->db->where('sc.student_id', $s_student_id);
        $this->db->where('sc.score_approval', 'approved');
        $this->db->order_by('sc.date_added', 'DESC');

        $a_email_to = [];
        $a_email_cc = [];
        $lecturer_query = $this->db->get();
        if ($lecturer_query->num_rows() > 0) {
            $a_lecturer_data = $lecturer_query->result();
            foreach ($a_lecturer_data as $o_class_lecturer) {
                if (!in_array($o_class_lecturer->employee_email, $a_email_to)) {
                    array_push($a_email_to, $o_class_lecturer->employee_email);
                }
            }
        }
        else {
            $a_email_to = ['employee@company.ac.id'];
        }

        $this->db->select('st.*, sp.*, fc.*, emhod.employee_email AS "hod_email", emde.employee_email AS "dean_email", st.academic_year_id "student_batch", st.personal_data_id "student_personal_data"');
        $this->db->from('dt_student st');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        $this->db->join('dt_personal_data pdhod', 'pdhod.personal_data_id = sp.head_of_study_program_id', 'LEFT');
        $this->db->join('dt_personal_data pdde', 'pdde.personal_data_id = fc.deans_id', 'LEFT');
        $this->db->join('dt_employee emhod', 'emhod.personal_data_id = pdhod.personal_data_id', 'LEFT');
        $this->db->join('dt_employee emde', 'emde.personal_data_id = pdde.personal_data_id', 'LEFT');
        $this->db->where('st.student_id', $s_student_id);
        $query_hod = $this->db->get();
        if ($query_hod->num_rows() > 0) {
            $hoddean = $query_hod->first_row();
            if ((!is_null($hoddean->hod_email)) AND (!in_array($hoddean->hod_email, $a_email_cc))) {
                array_push($a_email_cc, $hoddean->hod_email);
            }

            if ((!is_null($hoddean->dean_email)) AND (!in_array($hoddean->dean_email, $a_email_cc))) {
                array_push($a_email_cc, $hoddean->dean_email);
            }

            $mba_document_internship = $this->get_internship_document(['sid.internship_id' => $s_internship_id]);
            $s_file_path = APPPATH.'uploads/student/'.$hoddean->student_batch.'/'.$hoddean->study_program_abbreviation.'/'.$hoddean->student_id.'/internship/';
            if ($mba_document_internship) {
                foreach ($mba_document_internship as $o_document) {
                    $s_path_file = $s_file_path.$o_document->document_link;
                    if (file_exists($s_path_file)) {
                        $this->email->attach($s_path_file);
                    }
                }
            }

            $a_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $hoddean->student_personal_data]);
            $s_student_name = $a_personal_data[0]->personal_data_name.' ('.$hoddean->study_program_abbreviation.'/'.$hoddean->student_batch.')';
            $s_company_name = $this->input->post('internship_company');
            $s_company_department = $this->input->post('internship_department');
            $s_company_supervisor = $this->input->post('internship_supervisor');
            $s_message = <<<TEXT
Dear All,

Student has submited internship on the portal. All submitted document have been attached.

Student Name : {$s_student_name}
Company Name : {$s_company_name}
Department   : {$s_company_department}
Supervisor   : {$s_company_supervisor}

Note.
- This message generated by sistem.
TEXT;
            $this->email->from('employee@company.ac.id', 'IT Portal System');
            $this->email->to($a_email_to);
            $this->email->cc($a_email_cc);
            $this->email->bcc('employee@company.ac.id');
            $this->email->subject('Internship File Uploaded');
            $this->email->message($s_message);
            $this->email->send();
        }
        else {
            $this->email->from('employee@company.ac.id', 'IT Portal System');
            $this->email->to('employee@company.ac.id');
            $this->email->subject('Error send notification internship');
            $this->email->message('file: portal2/modules/student/models/Internship_model.php ->send_notification function');
            $this->email->send();
        }
    }
}

?>