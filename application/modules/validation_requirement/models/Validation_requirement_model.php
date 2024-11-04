<?php
class Validation_requirement_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->ldb = $this->load->database('la_db', true);
    }

    function get_lecturer_assessment_result($a_clause = false, $a_employee_id_in = false) {
        $s_selectdata = 'pds.personal_data_name AS "student_name", st.academic_year_id AS "student_batch", st.student_status AS "student_status"';
        $s_selectdata .= ', sp.study_program_abbreviation "prodi", pde.personal_data_name AS "lecturer", em.personal_data_id, sn.subject_name';
        $s_selectdata .= ', qa.question_desc, qa.number, sr.score_name, sr.score_value, sc.academic_year_id, sc.semester_type_id, arq.question_id';

        $this->db->select($s_selectdata);
        $this->db->from('portal_lecturer_assessment.assessment_result ar');
        $this->db->join('portal_lecturer_assessment.assessment_result_question arq', 'arq.result_id = ar.result_id');
        $this->db->join('portal_lecturer_assessment.question_aspect qa', 'qa.question_id = arq.question_id');
        $this->db->join('portal_lecturer_assessment.score_result sr', 'sr.score_result_id = arq.score_result_id');
        $this->db->join('portal_main.dt_score sc', 'sc.score_id = ar.score_id');
        $this->db->join('portal_main.ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('portal_main.ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('portal_main.ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('portal_main.dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('portal_main.ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('portal_main.dt_personal_data pds', 'pds.personal_data_id = st.personal_data_id');
        $this->db->join('portal_main.dt_employee em', 'em.employee_id = ar.employee_id');
        $this->db->join('portal_main.dt_personal_data pde', 'pde.personal_data_id = em.personal_data_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_employee_id_in) {
            $this->db->where_in($a_employee_id_in);
        }

        $this->db->order_by('st.academic_year_id');
        $this->db->order_by('sp.study_program_name');
        $this->db->order_by('pds.personal_data_name');
        $this->db->order_by('pde.personal_data_name');
        $this->db->order_by('sn.subject_name');
        $this->db->order_by('qa.number');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_result_question($a_clause = false)
    {
        $this->ldb->from('assessment_result_question rq');
        $this->ldb->join('score_result rs', 'rs.score_result_id = rq.score_result_id');
        if ($a_clause) {
            $this->ldb->where($a_clause);
        }

        $query = $this->ldb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_lecturer_assessment($a_result_data, $a_result_details_data)
    {
        $this->ldb->trans_begin();
        $s_score_id = $a_result_data['score_id'];
        $s_employee_id = $a_result_data['employee_id'];

        $mba_has_submitted = $this->check_lecturer_assessment([
            'ar.score_id' => $s_score_id,
            'ar.employee_id' => $s_employee_id
        ]);

        if ($mba_has_submitted) {
            $this->ldb->delete('assessment_result', ['result_id' => $mba_has_submitted[0]->result_id]);
        }

        $this->ldb->insert('assessment_result', $a_result_data);
        if ($this->ldb->affected_rows() > 0) {
            if (count($a_result_details_data) > 0) {
                foreach ($a_result_details_data as $a_details) {
                    $this->ldb->insert('assessment_result_question', $a_details);
                }
            }
            else {
                var_dump($a_result_details_data);exit;
                $this->ldb->trans_rollback();
                return false;
            }
        }
        else {
            print('2');exit;
            $this->ldb->trans_rollback();
            return false;
        }

        if ($this->ldb->trans_status() === FALSE){
            $this->ldb->trans_rollback();
            return false;
        }
        else {
            $this->ldb->trans_commit();
            return true;
        }
    }

    public function check_lecturer_assessment($a_clause = false)
    {
        $this->ldb->from('assessment_result ar');
        if ($a_clause) {
            $this->ldb->where($a_clause);
        }
        $query = $this->ldb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_student_question_list($a_clause = false)
    {
        $this->ldb->from('portal_lecturer_assessment.question_aspect qa');
        $this->ldb->join('portal_lecturer_assessment.assessment_result_question arq', 'arq.question_id = qa.question_id');
        $this->ldb->join('portal_lecturer_assessment.assessment_result ar', 'ar.result_id = arq.result_id');
        $this->ldb->join('portal_main.dt_score sc', 'sc.score_id = ar.score_id');
        $this->ldb->group_by('qa.question_id');
        
        if ($a_clause) {
            $this->ldb->where($a_clause);
        }
        $this->ldb->order_by('qa.number');

        $query = $this->ldb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_question_list($a_clause = false)
    {
        $this->ldb->from('question_aspect');
        if ($a_clause) {
            $this->ldb->where($a_clause);
        }
        $this->ldb->order_by('number');
        $query = $this->ldb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_result_option($a_clause = false)
    {
        $this->ldb->from('score_result');
        if ($a_clause) {
            $this->ldb->where($a_clause);
        }
        $query = $this->ldb->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function delete_personal_vaccine($s_personal_data_id)
    {
        $this->db->delete('dt_personal_data_covid_vaccine', ['personal_data_id' => $s_personal_data_id]);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function submit_vaccine($a_data)
    {
        $this->db->insert('dt_personal_data_covid_vaccine', $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function submit_validation($s_personal_data_id, $s_field_update, $b_confirm = false)
    {
        $this->db->from('ref_confirm_validation');
        $this->db->where('personal_data_id', $s_personal_data_id);
        $query = $this->db->get();
        if ($query->num_rows()  == 0) {
            $this->db->insert('ref_confirm_validation', [
                'personal_data_id' => $s_personal_data_id,
                $s_field_update => ($b_confirm) ? 'confirmed' : 'not_confirmed'
            ]);

            return ($this->db->affected_rows() > 0) ? true : false;
        }
        else {
            $o_validation_data = $query->first_row();
            $this->db->update('ref_confirm_validation', [
                $s_field_update => ($b_confirm) ? 'confirmed' : 'not_confirmed'
            ], [
                'confirmation_id' => $o_validation_data->confirmation_id
            ]);

            return true;
        }
    }

    public function get_student_list_assessment($a_clause = false, $mbs_group = false)
    {
        $this->db->select('*, st.academic_year_id AS "student_batch"');
        $this->db->from('portal_lecturer_assessment.assessment_result ar');
        $this->db->join('portal_main.dt_score sc', 'sc.score_id = ar.score_id');
        $this->db->join('portal_main.dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('portal_main.ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($mbs_group) {
            $this->db->group_by($mbs_group);
        }
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_lecturer_list_assessment($a_clause = false, $group_lecturer = true, $a_dept_id = false, $a_employee_id_in = false)
    {
        $this->db->select('*, d1cml.employee_id AS "employee_assessment", d1em.department_id');
        $this->db->from('portal_main.dt_class_master d1cm');
        $this->db->join('portal_main.dt_score d1sc', 'd1sc.class_master_id = d1cm.class_master_id');
        $this->db->join('portal_main.dt_class_master_lecturer d1cml', 'd1cml.class_master_id = d1cm.class_master_id');
        $this->db->join('portal_main.dt_employee d1em', 'd1em.employee_id = d1cml.employee_id');
        $this->db->join('portal_main.dt_personal_data d1pd', 'd1pd.personal_data_id = d1em.personal_data_id');
        $this->db->join('portal_lecturer_assessment.assessment_result d2ar', 'd2ar.score_id = d1sc.score_id');
        $this->db->join('portal_main.ref_curriculum_subject d1cs', 'd1cs.curriculum_subject_id = d1sc.curriculum_subject_id');
        $this->db->join('portal_main.ref_subject d1sb', 'd1sb.subject_id = d1cs.subject_id');
        $this->db->join('portal_main.ref_subject_name d1sn', 'd1sn.subject_name_id = d1sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_dept_id) {
            $this->db->where("(d1em.department_id IN ('".implode("','", $a_dept_id)."') OR d1em.department_id IS NULL)");
            // $this->db->or_where('d1em.department_id', NULL);
        }

        if ($a_employee_id_in) {
            $this->db->where_in('d2ar.employee_id', $a_employee_id_in);
        }
        
        if ($group_lecturer) {
            $this->db->group_by('d1cml.employee_id');
            $this->db->group_by('d1sc.class_master_id');
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
        // return $this->db->last_query();
    }

    public function get_lecturer_list_student($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('dt_class_master cm', 'sc.class_master_id = cm.class_master_id');
        $this->db->join('dt_class_master_lecturer cml', 'cml.class_master_id = cm.class_master_id');
        $this->db->join('dt_employee em', 'em.employee_id = cml.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($this->session->userdata('student_id') != 'd9868ebf-ef1a-4ede-80df-b16ea0df93ee') {
            $this->db->where('sc.score_approval', 'approved');
        }

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $mba_lecturer_list = $query->result();
            foreach ($mba_lecturer_list as $key => $o_subject_lecturer) {
                if(strpos(strtolower($o_subject_lecturer->subject_name), 'internship') !== false){
                    unset($mba_lecturer_list[$key]);
                }
                else if(strpos(strtolower($o_subject_lecturer->subject_name), 'thesis') !== false){
                    unset($mba_lecturer_list[$key]);
                }
                else if(strpos(strtolower($o_subject_lecturer->subject_name), 'research project') !== false){
                    unset($mba_lecturer_list[$key]);
                }
                else if(strpos(strtolower($o_subject_lecturer->subject_name), 'research semester ') !== false){
                    unset($mba_lecturer_list[$key]);
                }
            }

            $mba_lecturer_list = array_values($mba_lecturer_list);
            if ((is_array($mba_lecturer_list)) AND (count($mba_lecturer_list) > 0)) {
                return $mba_lecturer_list;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
        // return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function get_lecturer_score_counter($a_clause = false)
    {
        $this->db->from('portal_lecturer_assessment.assessment_result d2ar');
        $this->db->join('portal_main.dt_score d1sc', 'd1sc.score_id = d2ar.score_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}