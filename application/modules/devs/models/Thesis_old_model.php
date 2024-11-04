<?php
class Thesis_old_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->tdb = $this->load->database('db_thesis', true);
    }

    public function get_where($s_table_name, $a_clause = false)
	{
		if ($a_clause) {
			$query = $this->tdb->get_where($s_table_name, $a_clause);
		}else{
			$query = $this->tdb->get($s_table_name);
		}

		return ($query->num_rows() > 0) ? $query->result() : false;
	}

    public function get_from_defense($s_student_name, $a_clause = false)
    {
        $this->tdb->from('thesis_defense td');
        $this->tdb->join('thesis_student ts', 'ts.student_number = td.student_number');
        $this->tdb->where('ts.full_name', $s_student_name);
        if ($a_clause) {
            $this->tdb->where($a_clause);
        }

        $query = $this->tdb->get();
        return ($query->num_rows() > 0) ? $query->first_row() : false;
    }
}
