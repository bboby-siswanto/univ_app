<?php
class Devs_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('production', true);
    }

    public function get_roles($a_clause = false)
    {
        if ($a_clause) {
            $q = $this->db->get_where('ref_roles', $a_clause);
        }else{
            $q = $this->db->get('ref_roles');
        }
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_student_parent($s_family_id)
    {
        $this->db->from('dt_personal_data pd');
        $this->db->join('dt_family_member fm', 'fm.personal_data_id = pd.personal_data_id');
        $this->db->where('fm.family_id', $s_family_id);
        $this->db->where('family_member_status != ', 'child');
        $this->db->order_by('pd.date_added', 'DESC');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->first_row() : false;
    }

    public function get_candidate_academic_history($s_personal_data_id)
    {
        $this->db->from('dt_academic_history ah');
        // $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ah.personal_data');
        $this->db->join('dt_student st', 'st.personal_data_id = ah.personal_data_id');
        $this->db->where('st.personal_data_id', $s_personal_data_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->first_row() : false;
    }

    public function get_employee_roles($a_clause = false)
    {
        $this->db->join('dt_employee em', 'em.employee_id = emp.employee_id');
        $this->db->join('dt_roles_pages rp', 'rp.roles_pages_id = emp.roles_pages_id');
        $this->db->join('ref_roles rr', 'rr.roles_id = rp.roles_id');
        if ($a_clause) {
            $q = $this->db->where($a_clause);
        }
        $this->db->group_by('rp.roles_id');
        $q = $this->db->get('dt_employee_pages emp');
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_employee_list_roles($a_clause = false)
    {
        $this->db->join('dt_employee em', 'em.employee_id = emp.employee_id');
        $this->db->join('dt_roles_pages rp', 'rp.roles_pages_id = emp.roles_pages_id');
        if ($a_clause) {
            $q = $this->db->where($a_clause);
        }
        $this->db->group_by('rp.roles_id');
        $q = $this->db->get('dt_employee_pages emp');

        $a_roles = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $roles) {
                array_push($a_roles, $roles->roles_id);
            }
        }
        return $a_roles;
    }

    public function get_employee_pages($a_clause = false)
    {
        $this->db->from('dt_employee_pages ep');
        $this->db->join('dt_employee em', 'em.employee_id = ep.employee_id');
        $this->db->join('dt_roles_pages rp', 'rp.roles_pages_id = ep.roles_pages_id');
        $this->db->join('ref_roles rr', 'rr.roles_id = rp.roles_id');
        $this->db->join('ref_pages pg', 'pg.pages_id = rp.pages_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_employee_lists($a_clause = false)
    {
        $this->db->from('dt_employee em');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('pd.personal_data_name');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function save_roles_pages($a_roles_data)
    {
        $this->db->insert('dt_roles_pages', $a_roles_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    // public function save_roles_pages($roles_id, $a_data = false)
    // {
    //     $this->db->delete('dt_roles_pages', array('roles_id' => $roles_id));
    //     if ($a_data) {
    //         $this->db->insert_batch('dt_roles_pages', $a_data);
    //     }

    //     return ($this->db->affected_rows() > 0) ? true : false;
    // }

    public function get_roles_pages($a_clause = false)
    {
        $this->db->from('dt_roles_pages drp');
        $this->db->join('ref_pages rp', 'rp.pages_id = drp.pages_id');
        $this->db->join('ref_roles rr', 'rr.roles_id = drp.roles_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('drp.pages_id');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function remove_roles_pages($s_roles_pages_id)
    {
        $this->db->delete('dt_roles_pages', array('roles_pages_id' => $s_roles_pages_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_employee_permission_page($a_employee_permission_pages)
    {
        $this->db->insert('dt_employee_pages', $a_employee_permission_pages);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    // public function save_employee_permission_page($a_data)
    // {
    //     $this->db->insert_batch('dt_employee_pages', $a_data);
    //     return ($this->db->affected_rows() > 0) ? true : false;
    // }

    public function remove_employee_pages_permission($a_clause)
    {
        $this->db->delete('dt_employee_pages', $a_clause);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_roles($a_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('ref_roles', $a_data, $a_clause_update);
            return true;
        }else{
            $this->db->insert('ref_roles', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function remove_roles($s_roles_id)
    {
        $this->db->delete('ref_roles', array('roles_id' => $s_roles_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_pages($a_data, $a_clause_update = false)
    {
        if ($a_clause_update) {
            $this->db->update('ref_pages', $a_data, $a_clause_update);
            return true;
        }else{
            $this->db->insert('ref_pages', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function remove_pages($s_pages_id)
    {
        $this->db->delete('ref_pages', array('pages_id' => $s_pages_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function del_data($s_table, $a_clause)
    {
        $this->db->delete($s_table, $a_clause);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_student_score($a_clause = false)
    {
        $this->db->select('*, st.academic_year_id AS "batch", sc.academic_year_id AS "academic_year_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        // $this->db->order_by('sc.academic_year_id', 'ASC');
        // $this->db->order_by('sc.semester_type_id', 'ASC');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_subject_delivered($a_clause = false)
    {
        $this->db->from('dt_class_subject_delivered');
        $this->db->order_by('subject_delivered_time_start');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_score_data($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'st.personal_data_id = pd.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_student_absence($a_clause = false)
    {
        $this->db->from('dt_absence_student sa');
        $this->db->join('dt_score sc', 'sc.score_id = sa.score_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_subject_details($a_clause = false)
    {
        $this->db->from('dt_class_group_subject cgs');
        $this->db->join('dt_class_master_class cmc', 'cmc.class_group_id = cgs.class_group_id');
        $this->db->join('dt_offered_subject ofs', 'ofs.offered_subject_id = cgs.offered_subject_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = ofs.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_lecturer_absence($a_clause = false)
    {
        $this->db->select('*, cm.academic_year_id AS "academic_year_id", cm.semester_type_id AS "semester_type_id"');
        $this->db->from('dt_class_subject_delivered csd');
        $this->db->join('dt_employee em', 'em.employee_id = csd.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = em.personal_data_id');
        $this->db->join('dt_class_master cm', 'cm.class_master_id = csd.class_master_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    function retrieve_all_student_billing($a_clause = false, $s_grouping = false) {
        $this->db->from('dt_invoice di');
        $this->db->join('dt_personal_data pd','pd.personal_data_id = di.personal_data_id');
        $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
        $this->db->join('dt_fee fee', 'fee.fee_id = did.fee_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->where_in('di.invoice_status', ['created', 'pending']);
        $this->db->where_not_in('fee.payment_type_code', ['01', '06', '08', '10']);

        if ($s_grouping) {
            $this->db->group_by($s_grouping);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
