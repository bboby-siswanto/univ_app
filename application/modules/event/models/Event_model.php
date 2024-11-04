<?php
class Event_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	function submit_event_field($s_event_id, $a_data) {
		$this->db->delete('dt_event_field', ['event_id' => $s_event_id]);
		foreach ($a_data as $a_eventfield_data) {
			$this->db->insert('dt_event_field', $a_eventfield_data);
		}
	}

	public function get_event_field($a_clause = false)
	{
		$this->db->from('dt_event_field ef');
		$this->db->join('dt_event ev', 'ev.event_id = ef.event_id');
		$this->db->join('ref_event_field ref', 'ref.field_id = ef.field_id');
		if ($a_clause) {
			$this->db->where($a_clause);
		}

		$this->db->order_by('ef.field_id');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result() : false;
		// return $this->db->last_query();
	}

    public function update_event($a_data, $s_event_id)
    {
        $this->db->update('dt_event', $a_data, ['event_id' => $s_event_id]);
        return true;
    }

	public function create_event($a_data)
	{
		if(!array_key_exists('event_id', $a_data)){
			$a_data['event_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('dt_event', $a_data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	public function update_booking_data($a_booking_data, $a_clause)
	{
		$this->db->update('dt_event_bookings', $a_booking_data, $a_clause);
        return true;
	}
	
	public function get_event_bookings($s_event_id = '')
	{
		$this->db->select('*, deb.date_added AS "booking_registration"');
		$this->db->join('dt_event de', 'de.event_id = deb.event_id');
		// $query = (!empty($s_event_id)) ? $this->db->get_where('dt_event_bookings deb', ['deb.event_id' => $s_event_id]) : $this->db->get('dt_event_bookings deb');
		// $query = (!empty($s_event_id)) ? $this->db->get_where('dt_event_bookings deb', ['deb.event_id' => $s_event_id]) : 0;
		if(!empty($s_event_id)){
			$query = $this->db->get_where('dt_event_bookings deb', ['deb.event_id' => $s_event_id]);
			return ($query->num_rows() >= 1) ? $query->result() : false;
		}
		else{
			return false;
		}
	}
	
	public function register_event($a_event_data)
	{
		if(!array_key_exists('booking_id', $a_event_data)){
			$a_event_data['booking_id'] = $this->uuid->v4();
		}
		
		$this->db->insert('dt_event_bookings', $a_event_data);
        return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	public function check_phone_bookings($s_phone, $s_event_id)
	{
		$query = $this->db->get_where('dt_event_bookings', [
			'event_id' => $s_event_id,
			'booking_phone' => $s_phone
		]);
		return ($query->num_rows() >= 1) ? true : false;
	}
	
	public function check_email_bookings($s_email, $s_event_id, $valid_student = false)
	{
        $query = $this->db->get_where('dt_event_bookings', [
			'event_id' => $s_event_id,
			'booking_email' => $s_email
		]);

        // if ($valid_student) {
        //     if ($query->num_rows() == 0) {
        //         $this->db->join('dt_student st', 'st.personal_data_id = pd.personal_data_id');
        //         $query = $this->db->get_where('dt_personal_data', [
        //             'personal_data_email' => $s_email
        //         ]);
        //     }
        // }
		return ($query->num_rows() >= 1) ? true : false;
	}
	
	public function get_event($a_clause = false)
	{
		$query = ($a_clause) ? $this->db->get_where('dt_event', $a_clause) : $this->db->get('dt_event');
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
}