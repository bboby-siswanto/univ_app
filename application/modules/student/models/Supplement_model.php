<?php
class Supplement_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_supplement_doc($a_clause = false)
    {
        $this->db->select('*, ssp.date_added AS "date_upload", st.academic_year_id AS "student_batch"');
        $this->db->from('dt_student_supplement_doc ssp');
        $this->db->join('dt_student_supplement sp', 'sp.supplement_id = ssp.supplement_id');
        $this->db->join('dt_student st', 'st.student_id = sp.student_id');
        $this->db->join('ref_study_program spr', 'spr.study_program_id = st.study_program_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_supplement($a_clause = false)
    {
        $this->db->select('*, ssp.date_added AS "date_supplement", st.academic_year_id AS "student_batch", ssp.academic_year_id, ssp.semester_type_id');
        $this->db->from('dt_student_supplement ssp');
        $this->db->join('dt_student st', 'st.student_id = ssp.student_id');
        $this->db->join('dt_employee em', 'em.employee_id = ssp.employee_id', 'left');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_supplement($post_data)
    {
        $this->load->model('student/Student_model', 'Stm');
        $s_student_id = $post_data['supplement_student_id'];

        $mba_student_data = $this->Stm->get_student_filtered(['ds.student_id' => $s_student_id]);
        if ($mba_student_data) {
            $o_student = $mba_student_data[0];
            $s_file_path = APPPATH.'uploads/student/'.$o_student->academic_year_id.'/'.$o_student->study_program_abbreviation.'/'.$o_student->student_id.'/achievement/';
            if(!file_exists($s_file_path)){
                mkdir($s_file_path, 0777, TRUE);
            }

            $this->db->trans_begin();
            $s_supplement_id = $this->uuid->v4();
            $a_error_upload = [];

            $a_supplement_data = [
                'supplement_id' => $s_supplement_id,
                'student_id' => $s_student_id,
                'employee_id' => NULL,
                'academic_year_id' => $this->session->userdata('academic_year_id_active'),
                'semester_type_id' => $this->session->userdata('semester_type_id_active'),
                'supplement_comment' => $post_data['supplement_desc'],
                'supplement_category' => 'positive',
                'date_added' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('dt_student_supplement', $a_supplement_data);

            $config['allowed_types'] = 'pdf|jpg|jpeg|png';
            $config['max_size'] = 104800;
            $config['file_ext_tolower'] = true;
            $config['overwrite'] = true;
            $config['upload_path'] = $s_file_path;
            $this->load->library('upload', $config);

            foreach ($_FILES as $s_key => $a_value) {
                if (!empty($a_value['name'])) {
                    $s_filename = $a_value['name'];
                    $s_fname = md5(json_encode($a_value));
                    $config['file_name'] = $s_fname;
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload($s_key)) {
                        $a_attachment_data= [
                            'supplement_doc_id' => $this->uuid->v4(),
                            'supplement_id' => $s_supplement_id,
                            'supplement_doc_link' => $this->upload->data('file_name'),
                            'supplement_doc_fname' => $s_filename,
                            'date_added' => date('Y-m-d H:i:s')
                        ];
                        $this->db->insert('dt_student_supplement_doc', $a_attachment_data);
                    }
                    else {
                        array_push($a_error_upload, $this->upload->display_errors('<span>', '</span>'));
                    }
                }
            }

            if (count($a_error_upload) > 0) {
                $a_return = ['code' => 3, 'message' => implode(', ', $a_error_upload)];
            }
            else if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $a_return = ['code' => 1, 'message' => 'Failed response process!'];
            }
            else {
                $this->db->trans_commit();
                $a_return = ['code' => 0, 'message' => 'Success'];
            }
        }
        else {
            $a_return = ['code' => 2, 'message' => 'Data not found!'];
        }

        return $a_return;
    }
}
