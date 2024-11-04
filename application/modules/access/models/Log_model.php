<?php
class Log_model extends CI_Model
{
    public $dtablelist;
    function __construct() {
        parent::__construct();
        $this->dblog = $this->load->database('dblog', TRUE);
    }

    function findsearch($a_clause = false, $a_likeclause = false) {
        $tables = $this->dblog->list_tables();
        $a_table = [];
        foreach ($tables as $key => $table_name) {
            $foundsearch = $this->find($table_name, $a_clause, $a_likeclause);
            if ($foundsearch) {
                array_push($a_table, $table_name);
            }
        }
        return $a_table;
    }

    function find($table_name, $a_clause = false, $a_likeclause = false) {
        $this->dblog->from($table_name);
        if ($a_clause) {
            $this->dblog->where($a_clause);
        }
        if ($a_likeclause) {
            $this->dblog->like($a_likeclause);
        }
        $query = $this->dblog->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
}
