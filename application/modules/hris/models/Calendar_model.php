<?php
class Calendar_model extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->hrm = $this->load->database('hr_db', true);
    }

    public function remove_assignor($a_remove_param)
    {
        $this->hrm->delete('dt_job_assign', $a_remove_param);
        return true;
    }

    public function submit_job_assign($a_event_assigndata)
    {
        $this->hrm->insert('dt_job_report', $a_eventdata);
        return ($this->hrm->affected_rows() > 0) ? true : false;
    }

    public function submit_event($a_eventdata, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->hrm->update('dt_job_report', $a_eventdata, $a_update_clause);
            return true;
        }
        else {
            $this->hrm->insert('dt_job_report', $a_eventdata);
            return ($this->hrm->affected_rows() > 0) ? true : false;
        }
    }

    public function get_event($a_clause = false)
    {
        $this->db->select('*, jr.personal_data_id');
        $this->db->from('portal_hr.dt_job_report jr');
        $this->db->join('portal_main.dt_personal_data pd', 'pd.personal_data_id = jr.personal_data_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
