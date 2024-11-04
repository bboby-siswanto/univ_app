<?php
class Subject_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function is_validate_subject($a_clause_validate)
    {
        if ($this->is_validate_subject_all($a_clause_validate)){
            return 'Subject Name, Program Study and Subject Credit has been registered';
        }else {
            return false;
        }
    }

    public function test_subject($s_portal_id)
    {
        $query = $this->db->get_where('ref_subject', array('portal_id' => $s_portal_id));
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function is_validate_subject_all($a_clause_validate)
    {
        $check_all = $this->db->get_where('ref_subject', array('subject_name_id' => $a_clause_validate['subject_name_id'], 'program_id' => $a_clause_validate['program_id'], 'study_program_id' => $a_clause_validate['study_program_id'], 'subject_credit' => $a_clause_validate['subject_credit']));
        if ($check_all->num_rows() > 0){
            if (isset($a_clause_validate['subject_id'])) {
                $a_list = $check_all->result();
                $s_subject_id = $a_clause_validate['subject_id'];
                $oke = false;
                foreach ($a_list as $list) {
                    if ($list->subject_id == $s_subject_id) {
                        $oke = true;
                        break;
                    }
                }

                if ($oke) {
                    return false;
                }else{
                    return 'Subject Name, Program Study and Subject Credit has been registered';
                }
            }else{
                return 'Subject Name, Program Study and Subject Credit has been registered';
            }
        }else {
            return false;
        }
    }

    public function delete_subject($subject_id)
    {
        $this->db->delete('ref_subject', array('subject_id' => $subject_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_subject_filtered($a_clause = false, $like_condition = false, $ordering = false, $sort = 'ASC')
    {
        $this->db->select('*, rs.program_id, rs.study_program_id');
        $this->db->from('ref_subject rs');
        $this->db->join('ref_subject_name rsn', 'rsn.subject_name_id = rs.subject_name_id', 'left');
        $this->db->join('dikti_jenis_mata_kuliah djmk', 'djmk.id_jenis_mata_kuliah = rs.id_jenis_mata_kuliah', 'left');
        $this->db->join('ref_program rp', 'rp.program_id = rs.program_id');
        $this->db->join('ref_study_program rps', 'rps.study_program_id = rs.study_program_id','left');
        if ($a_clause) {
            if ($like_condition) {
                $this->db->like($a_clause);
            }else{
                $this->db->where($a_clause);
            }
        }

        if ($ordering) {
            $this->db->order_by($ordering, $sort);
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function save_subject_data($a_subject_data, $s_subject_id = false)
    {
        if ($s_subject_id) {
            $this->db->update('ref_subject', $a_subject_data, array('subject_id' => $s_subject_id));
            return true;
        }else{
            if (is_object($a_subject_data)) {
                (array)$a_subject_data;
            }

            if (!array_key_exists('subject_id', $a_subject_data)) {
                $a_subject_data['subject_id'] = $this->uuid->v4();
            }

            $this->db->insert('ref_subject', $a_subject_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function save_subject_name($a_subject_name_data, $s_subject_name_id = false)
    {
        if ($s_subject_name_id) {
            $this->db->update('ref_subject_name', $a_subject_name_data, array('subject_name_id' => $s_subject_name_id));
        }else{
            if (is_object($a_subject_name_data)) {
                (array)$a_subject_name_data;
            }
            if (!array_key_exists('subject_name_id', $a_subject_name_data)) {
                $a_subject_name_data['subject_name_id'] = $this->uuid->v4();
            }

            $this->db->insert('ref_subject_name', $a_subject_name_data);
        }

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_subject_name_filtered($a_clause = false, $like_condition = false)
    {
        $this->db->from('ref_subject_name rsn');
        if ($a_clause) {
            if ($like_condition) {
                $this->db->like($a_clause);
            }else{
                $this->db->where($a_clause);
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_subject_name($s_subject_name, $join_subject = false)
    {
        $this->db->from('ref_subject_name');
        if ($join_subject) {
            $this->db->select('*, ref_subject.program_id, ref_subject.study_program_id');
            $this->db->join('ref_subject', 'subject_name_id');
            $this->db->join('ref_study_program', 'study_program_id');
            $this->db->join('dikti_jenis_mata_kuliah', 'id_jenis_mata_kuliah', 'left');
            $this->db->order_by('ref_subject.date_added', 'DESC');
        }else{
            $this->db->select('*');
        }
        $this->db->like('subject_name', $s_subject_name);
        $this->db->limit(50);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_subject_type($s_id_jenis_mata_kuliah = false)
    {
        if ($s_id_jenis_mata_kuliah) {
            $this->db->where('id_jenis_mata_kuliah', $s_id_jenis_mata_kuliah);
        }
        $this->db->select('*');
        $this->db->from('dikti_jenis_mata_kuliah');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_subject_prodi($a_filter_data)
    {
        $this->db->select('*, rsb.study_program_id, rsb.program_id');
        $this->db->from('ref_subject rsb');
        $this->db->join('dikti_jenis_mata_kuliah djmk', 'djmk.id_jenis_mata_kuliah = rsb.id_jenis_mata_kuliah', 'left');
        $this->db->join('ref_program rp', 'rp.program_id = rsb.program_id');
        $this->db->join('ref_study_program rsp', 'rsp.study_program_id = rsb.study_program_id', 'left');
        $this->db->join('ref_faculty rfc', 'rfc.faculty_id = rsp.faculty_id', 'left');
        $this->db->join('ref_subject_name rsn', 'rsn.subject_name_id = rsb.subject_name_id');

        if (count($a_filter_data) > 0) {
            foreach ($a_filter_data as $key => $value) {
                if ($value != 'All') {
                    $this->db->where('rsb.'.$key, $value);
                }
            }
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function uc_words($string)
    {
        $a_cond = array('and', 'of', 'in', 'on', 'to', 'at', 'for');
        $s_string = trim($string);

        $a_expl = explode(' ', strtolower($s_string));
        foreach ($a_expl as $key => $str) {
            if (!in_array($str, $a_cond)) {
                $a_expl[$key] = ucwords($str);
            }
        }
        $s_res = implode(' ', $a_expl);
        return $s_res;
    }
}
