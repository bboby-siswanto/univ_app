<?php
class Vacancy_model extends CI_Model
{
    function __construct()
    {
		parent::__construct();
    }

    public function get_job_vacancy($s_personal_data_id = false, $a_clause = false)
	{
		$this->db->from('dt_job_vacancy dtv');
		$this->db->join('ref_institution ri', 'ri.institution_id = dtv.institution_id');
		$this->db->join('dt_address da', 'da.address_id = ri.address_id');
		$this->db->join('ref_country rc', 'rc.country_id = da.country_id');
		$this->db->join('ref_ocupation ro', 'ro.ocupation_id = dtv.occupation_id');
		if ($s_personal_data_id) {
			$this->db->where('personal_data_id', $s_personal_data_id);
		}
		$this->db->where('post_status !=', 'deleted');
		if ($a_clause) {
			$this->db->where($a_clause);
		}
		$this->db->order_by('dtv.date_added', 'desc');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
	}

	public function save_job_vacancy($a_job_vacancy_data, $s_job_vacancy_id = false)
	{
		if (is_object($a_job_vacancy_data)) {
			$a_job_vacancy_data = (array)$a_job_vacancy_data;
		}

		if ($s_job_vacancy_id) {
			$this->db->update('dt_job_vacancy', $a_job_vacancy_data, array('job_vacancy_id' => $s_job_vacancy_id));
		}else{
			if (!array_key_exists('job_vacancy_id', $a_job_vacancy_data)) {
				$a_job_vacancy_data['job_vacancy_id'] = $this->uuid->v4();
			}
			$this->db->insert('dt_job_vacancy', $a_job_vacancy_data);
		}

		return ($this->db->affected_rows() > 0) ? true : false;
	}
}
