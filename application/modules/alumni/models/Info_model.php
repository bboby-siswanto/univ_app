<?php
class Info_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_testimonial_data($a_param_data = false)
    {
        $this->db->from('dt_testimonial dt');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = dt.personal_data_id');
        $this->db->join('dt_student st', 'st.personal_data_id = dt.personal_data_id');
        if ($a_param_data) {
            $this->db->where($a_param_data);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function save_info($a_info_data, $s_info_id = false)
    {
        if (is_object($a_info_data)) {
            $a_info_data = (array)$a_info_data;
        }

        if ($s_info_id) {
            $this->db->update('dt_iuli_info', $a_info_data, array('info_id' => $s_info_id));
        }else{
            if (!array_key_exists('info_id', $a_info_data)) {
                $a_info_data['info_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_iuli_info', $a_info_data);
        }

        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_info_lists($a_clause = false)
    {
        $this->db->from('dt_iuli_info dii');
        $this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = dii.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->where('dii.info_status !=', 'deleted');
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
