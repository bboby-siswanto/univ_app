<?php
class Letter_numbering_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_document_template($a_clause = false, $a_template_id = false)
    {
        $this->db->from('dt_letter_number ln');
        $this->db->join('dt_letter_number_target lnt', 'lnt.letter_number_id = ln.letter_number_id');
        $this->db->join('dt_personal_document pdc', 'pdc.letter_number_id = ln.letter_number_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_template_id) {
            $this->db->where_in('lnt.template_id', $a_template_id);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_count_personal_document($a_clause = false)
    {
        $this->db->from('dt_personal_document pdd');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('pdd.document_token');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function remove_letter_target($s_letter_number_id = false, $s_template_id = false, $s_personal_data_id = false)
    {
        $a_clause = [];
        if ($s_letter_number_id) {
            $a_clause['letter_number_id'] = $s_letter_number_id;
        }
        if ($s_template_id) {
            $a_clause['template_id'] = $s_template_id;
        }
        if ($s_personal_data_id) {
            $a_clause['personal_data_id'] = $s_personal_data_id;
        }

        if (count($a_clause) > 0) {
            $this->db->delete('dt_letter_number_target', $a_clause);
        }
        else {
            return false;
        }
    }

    public function save_letter_number_target($a_data)
    {
        $this->db->insert('dt_letter_number_target', $a_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_letter_target($a_clause = false)
    {
        $this->db->select('ln.*, lnt.personal_data_id AS "personal_data_id_target", lnt.template_id, lnt.target_type');
        $this->db->from('dt_letter_number_target lnt');
        $this->db->join('dt_letter_number ln', 'ln.letter_number_id = lnt.letter_number_id');
        // $this->db->where('lnt.letter_number_id', $s_letter_number_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_letter_number($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('dt_personal_document', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('dt_personal_document', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_personal_document($a_clause = false)
    {
        $this->db->select('ln.*, pdc.*, pd.*, pdt.personal_data_name AS "target_name"');
        $this->db->from('dt_personal_document pdc');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = pdc.personal_data_id_generated');
        $this->db->join('dt_personal_data pdt', 'pdt.personal_data_id = pdc.personal_data_id_target', 'left');
        $this->db->join('dt_letter_number ln', 'ln.letter_number_id = pdc.letter_number_id', 'left');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        $this->db->order_by('pdc.date_added', 'desc');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function submit_template($a_data, $s_template_id = false)
    {
        if ($s_template_id) {
            $this->db->update('ref_letter_type_template', $a_data, ['template_id' => $s_template_id]);
            return true;
        }
        else {
            $this->db->insert('ref_letter_type_template', $a_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_template($s_letter_type_id = false, $a_clause = false)
    {
        $this->db->from('ref_letter_type_template ltt');
        $this->db->join('ref_letter_type lt', 'lt.letter_type_id = ltt.letter_type_id');

        if ($s_letter_type_id) {
            $this->db->where('ltt.letter_type_id', $s_letter_type_id);
        }

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('lt.letter_abbreviation');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_count_number($number, $s_month, $s_year, $s_order = 'desc')
    {
        $this->db->from('dt_letter_number');
        $this->db->where('letter_number', $number);
        $this->db->where('letter_month', $s_month);
        $this->db->where('letter_year', $s_year);
        $this->db->order_by('timestamp', $s_order);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function save_new_number($a_data, $a_update_clause = false)
    {
        if ($a_update_clause) {
            $this->db->update('dt_letter_number', $a_data, $a_update_clause);
            return true;
        }
        else {
            $this->db->insert('dt_letter_number', $a_data);
            return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : false;
        }
    }

    public function get_last_number($s_month, $s_year)
    {
        $this->db->select('max(letter_number) AS "max_number"');
        $this->db->where('letter_month <= ', $s_month);
        $this->db->where('letter_year <= ', $s_year);
        $query = $this->db->get('dt_letter_number');
        $o_result = $query->first_row();
        if (is_null($o_result->max_number)) {
            return 520;
        }
        else {
            return $o_result->max_number;
        }
    }

    // function generate_letter_number($backdate = false) {
    //     $s_new_number = $this->get_new_number();
    //     if ($backdate) {
    //         $a_dateselected = explode(' ', set_value('backdate'));
    //         $s_month = date('m', strtotime($a_dateselected[0]));
    //         $s_year = $a_dateselected[1];
    //         $s_letter_date = $s_year.'-'.$s_month.'-01';

    //         $s_new_char = $this->Lnm->get_new_number('char');
    //         $mba_count_number = $this->General->get_where('dt_letter_number');
    //     }
    // }

    public function get_new_number($b_format = 'number', $s_month = false, $s_year = false)
    {
        if ($b_format == 'char') {
            $this->db->select('letter_char AS "max_number"');
            $this->db->where('letter_char != ', NULL);
        }
        else {
            $this->db->select('max(letter_number) AS "max_number"');
            $this->db->where('letter_number != ', NULL);
        }

        if ($s_month OR $s_year) {
            if ($s_month) {
                $this->db->where('letter_month', $s_month);
            }
            if ($s_year) {
                $this->db->where('letter_year', $s_year);
            }
            // $this->db->where('letter_month', $s_month);
            // $this->db->where('letter_year', $s_year);
        }
        else {
            $this->db->where('letter_year', date('Y'));
        }

        $this->db->order_by('date_added', 'DESC');
        $query = $this->db->get('dt_letter_number');
        
        if ($query->num_rows() > 0) {
            $o_result = $query->first_row();
            // if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            //     return $this->db->last_query();
            // }

            if (is_null($o_result->max_number)) {
                return ($b_format == 'char') ? 'A' : 1;
            }
            else {
                $s_number = $o_result->max_number;
                // return ($b_format == 'char') ? chr(ord($s_number) + 1) : ($s_number + 1);
                return ($b_format == 'char') ? ++$s_number : ($s_number + 1);
            }
        }
        else {
            return ($b_format == 'char') ? 'A' : 1;
        }
    }

    public function get_new_number_old($b_format = 'number', $s_month = false, $s_year = false)
    {
        if ($b_format == 'char') {
            // $this->db->query("SELECT letter_char AS 'max_number' FROM dt_letter_number WHERE letter_char IS NOT NULL ORDER BY date_added DESC ");
            $this->db->select('max(letter_char) AS "max_number"');
        }
        else {
            $this->db->select('max(letter_number) AS "max_number"');
        }
        
        if ($s_month AND $s_year) {
            $query = $this->db->get_where('dt_letter_number', ['letter_month' => $s_month, 'letter_year' => $s_year]);
        }
        else {
            $query = $this->db->get('dt_letter_number');
        }

        if ($query->num_rows() > 0) {
            $o_result = $query->first_row();
            if (is_null($o_result->max_number)) {
                return ($b_format == 'char') ? 'A' : 520;
            }
            else {
                $s_number = $o_result->max_number;
                // return ($b_format == 'char') ? chr(ord($s_number) + 1) : ($s_number + 1);
                return ($b_format == 'char') ? ++$s_number : ($s_number + 1);
            }
        }
        else {
            return ($b_format == 'char') ? 'A' : 520;
        }
    }

    public function get_list($a_clause = false, $b_order_number = 'desc')
    {
        $this->db->select('*, ln.date_added AS "last_generate", ln.letter_description');
        $this->db->from('dt_letter_number ln');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = ln.personal_data_id');
        $this->db->join('ref_letter_type lt', 'lt.letter_type_id = ln.letter_type_id');
        $this->db->join('ref_department rd', 'rd.department_id = ln.department_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('ln.letter_number', $b_order_number);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
