<?php
class Address_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function delete_address_data($s_address_id, $s_personal_data_id)
    {
        $delete_personal_address = $this->db->delete('dt_personal_address', array('address_id' => $s_address_id, 'personal_data_id' => $s_personal_data_id));
        $delete_address = $this->db->delete('dt_address', array('address_id' => $s_address_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_address($a_address_data, $s_address_id = false)
    {
        if ($s_address_id) {
	        $a_address_data['address_id'] = $s_address_id;
            $this->db->update('dt_address', $a_address_data, array('address_id' => $s_address_id));
            return true;
        }else{
            if(is_object($a_address_data)){
                $a_address_data = (array)$a_address_data;
            }

            if(!array_key_exists('address_id', $a_address_data)){
                $a_address_data['address_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_address', $a_address_data);
            return ($this->db->affected_rows() > 0) ? $a_address_data['address_id'] : false;
        }
    }

    public function save_personal_address($a_personal_address, $s_personal_data_id = false, $s_address_id = false)
    {
        if (($s_personal_data_id) AND ($s_address_id)) {
            $this->db->update('dt_personal_address', $a_personal_address, array('personal_data_id' => $s_personal_data_id, 'address_id' => $s_address_id));
            return true;
        }else{
            $this->db->insert('dt_personal_address', $a_personal_address);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_personal_address($s_personal_data_id)
    {
        $this->db->select('*');
        $this->db->from('dt_personal_address dpa');
        $this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'left');
        $this->db->join('dt_personal_data dpd', 'dpd.personal_data_id = dpa.personal_data_id', 'left');
        $this->db->join('dikti_wilayah dw', 'dw.id_wilayah = da.dikti_wilayah_id','left');
        $this->db->join('ref_country rc', 'rc.country_id = da.country_id', 'left');
        $this->db->where('dpa.personal_data_id', $s_personal_data_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_personal_address_filtered($a_clause = false)
    {
        $this->db->select('*');
        $this->db->from('dt_personal_address dpa');
        $this->db->join('dt_address da', 'da.address_id = dpa.address_id', 'left');
        $this->db->join('dikti_wilayah dw', 'dw.id_wilayah = da.dikti_wilayah_id','left');
        $this->db->join('ref_country rc', 'rc.country_id = da.country_id', 'left');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
