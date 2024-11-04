<?php
class Score_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_studentscore_like_subject_name($a_clause = false, $s_subject_name = false, $s_ordering = 'ASC')
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($s_subject_name) {
            $this->db->like('sn.subject_name', $s_subject_name);
        }

        $this->db->order_by('sc.academic_year_id', $s_ordering);
        $this->db->order_by('sc.semester_type_id', $s_ordering);
        $this->db->order_by('sn.subject_name', $s_ordering);
        
        $this->db->group_by('sc.student_id');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_student_absence($a_clause = false)
    {
        $this->db->from('dt_absence_student as');
        $this->db->join('dt_score sc', 'sc.score_id = as.score_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }
    
	public function get_sum_merit_credit($a_clause = false, $a_semester_type_id = false)
    {
        $this->db->select("
        	SUM(sc.score_merit) AS 'sum_merit',
        	SUM(curs.curriculum_subject_credit) AS 'sum_credit'
        ");
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_semester rs', 'rs.semester_id = sc.semester_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        
        if($a_clause){
	        $this->db->where($a_clause);
        }
		
        if ($a_semester_type_id) {
            $this->db->where_in('sc.semester_type_id', $a_semester_type_id);
        }else{
            $this->db->where_not_in('sc.semester_type_id', [4,5]);
        }
		
        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $this->db->order_by('sn.subject_name', 'ASC');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->first_row() : false;
    }
    
    public function delete_data($s_score_id)
    {
        $this->db->delete('dt_score', array('score_id' => $s_score_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function get_historycal_score($s_student_id, $a_semester_in = false)
    {
        $this->db->select('*, sc.academic_year_id AS "semester_academic_year_id"');
        $this->db->from('dt_score sc');
        $this->db->join('ref_semester_type rst', 'rst.semester_type_id = sc.semester_type_id');
        $this->db->where('sc.student_id', $s_student_id);

        if ($a_semester_in) {
            $this->db->where_in('sc.semester_type_id', $a_semester_in);
        }

        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $q = $this->db->get();

        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_score_semester($a_clause = false)
    {
        $this->db->from('dt_score sc');
        $this->db->join('ref_semester sm', 'sm.semester_id = sc.semester_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        // $this->db->join()
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function save_data($a_score_data, $a_update_clause = false)
    {
        if (is_object($a_score_data)) {
            $a_score_data = (array)$a_score_data;
        }

        if ($a_update_clause) {
            $this->db->update('dt_score', $a_score_data, $a_update_clause);
            return true;
        }else {
            if (!array_key_exists('score_id', $a_score_data)) {
                $a_score_data['score_id'] = $this->uuid->v4();
            }

            $this->db->insert('dt_score', $a_score_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_score_ofse($a_clause = false, $a_ofse_semester = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id", sc.semester_id, sb.study_program_id AS "subject_study_program_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('dt_ofse os', 'os.ofse_period_id = sc.ofse_period_id', 'left');

        if ($a_ofse_semester) {
            $this->db->where_in('sc.semester_type_id', $a_ofse_semester);
        }
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $this->db->order_by('sn.subject_name', 'ASC');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_student_krs_class($a_clause = false, $a_param_in = false, $s_where_in = false) {
        $this->db->select('*');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if (($s_where_in) AND ($a_param_in)) {
            $this->db->where_in($s_where_in, $a_param_in);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_score_data($a_clause = false, $a_ofse_semester = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id", sc.semester_id, sb.study_program_id AS "subject_study_program_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');

        if ($a_ofse_semester) {
            $this->db->where_in('sc.semester_type_id', $a_ofse_semester);
        }
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $this->db->order_by('sn.subject_name', 'ASC');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function tes_get_score_data($a_clause = false, $a_ofse_semester = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id", sc.semester_id, sb.study_program_id AS "subject_study_program_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');

        if ($a_ofse_semester) {
            $this->db->where_in('sc.semester_type_id', $a_ofse_semester);
        }
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $this->db->order_by('sn.subject_name', 'ASC');
        
        $query = $this->db->get();
        return $this->db->last_query();
    }

    public function get_score_data_test($a_clause = false, $a_ofse_semester = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id", sc.semester_id, sb.study_program_id AS "subject_study_program_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');

        if ($a_ofse_semester) {
            $this->db->where_in('sc.semester_type_id', $a_ofse_semester);
        }
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $this->db->order_by('sn.subject_name', 'ASC');
        
        $query = $this->db->get();
        // return ($query->num_rows() > 0) ? $query->result() : false;
        return $this->db->last_query();
    }
    
    public function get_score_data_transcript($a_clause = false, $a_semester_id = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id", sc.semester_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_semester_id) {
            $this->db->where_in('sc.semester_id', $a_semester_id);
        }
        $this->db->order_by('sc.academic_year_id', 'ASC');
        $this->db->order_by('sc.semester_type_id', 'ASC');
        $this->db->order_by('sn.subject_name', 'ASC');
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_score_like_subject_name($a_clause = false, $s_subject_name = false, $s_ordering = 'ASC')
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, st.study_program_id AS "student_study_program_id", st.program_id AS "student_program_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($s_subject_name) {
            $this->db->like('sn.subject_name', $s_subject_name);
        }

        $this->db->order_by('sc.academic_year_id', $s_ordering);
        $this->db->order_by('sc.semester_type_id', $s_ordering);
        $this->db->order_by('sn.subject_name', $s_ordering);
        
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    // public function get_good_grades_regular_semester($s_subject_name, $s_student_id, $d_score_sum = null)
    // {
    //     $mba_score_data = $this->get_score_data([
    //         'sc.student_id' => $s_student_id,
    //         'sc.score_approval' => 'approved',
    //         'sn.subject_name' => $s_subject_name,
	// 		'sc.score_display' => 'TRUE'
    //     ], [1,2,3]);

    //     if ($mba_score_data) {
    //         if (count($mba_score_data) == 1) {
    //             return true;
    //         }else{
    //             $b_this_high = false;
    //             foreach ($mba_score_data as $o_score) {
    //                 $d_score_sum = (is_null($d_score_sum)) ? 0 : $d_score_sum;
    //                 $d_score_sum_compare = (is_null($o_score->score_sum)) ? 0 : $o_score->score_sum;
    
    //                 if ($d_score_sum_compare > $d_score_sum) {
    //                     return false;
    //                 }
    //             }
    
    //             return true;
    //         }
    //     }
    //     else {
    //         return false;
    //     }
    // }

    public function get_good_grades($s_subject_name, $s_student_id, $d_score_sum = null)
    {
        $mba_score_data = $this->get_score_data_transcript([
            'sc.student_id' => $s_student_id,
            'sc.score_approval' => 'approved',
            'sn.subject_name' => $s_subject_name,
            'sc.semester_id != ' => '17',
			'sc.score_display' => 'TRUE'
        ]);

        if ($mba_score_data) {
            if (count($mba_score_data) == 1) {
                return true;
            }else{
                $b_this_high = false;
                foreach ($mba_score_data as $o_score) {
                    $d_score_sum = (is_null($d_score_sum)) ? 0 : $d_score_sum;
                    $d_score_sum_compare = (is_null($o_score->score_sum)) ? 0 : $o_score->score_sum;
    
                    if ($d_score_sum_compare > $d_score_sum) {
                        return false;
                    }
                }
    
                return true;
            }
        }
        else {
            return false;
        }
    }
    
    public function get_last_score($a_clause = false, $s_order = 'DESC')
    {
        $this->db->select('sc.*');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'sc.student_id = st.student_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->where_in('sc.semester_type_id', array(1,2));
        $this->db->order_by('sc.academic_year_id', $s_order);
        $this->db->order_by('sc.semester_type_id', $s_order);
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->first_row() : false;
    }

    public function get_score_by_id($s_score_id = false, $a_score_id_in = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student stu', 'stu.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($s_score_id) {
            $this->db->where('sc.score_id', $s_score_id);
        }

        if ($a_score_id_in) {
            $this->db->where_in('score_id',  $a_score_id_in);
        }

        $this->db->order_by('subject_name', 'asc');
        $query = $this->db->get();
		
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_repetition_payment($s_personal_data_id, $s_student_finance_year, $s_datetime_semester_start, $s_datetime_semester_end)
    {
        $this->db->from('dt_invoice inv');
        $this->db->join('dt_invoice_details ind', 'ind.invoice_id = inv.invoice_id');
        $this->db->join('dt_fee fee', 'fee.fee_id = ind.fee_id');
        $this->db->join('dt_sub_invoice s_inv', 's_inv.invoice_id = inv.invoice_id');
        $this->db->join('dt_sub_invoice_details sind', 'sind.sub_invoice_id = s_inv.sub_invoice_id');
        // $this->db->join('bni_billing bill', 'bill.trx_id = sind.trx_id');
        $this->db->where('fee.payment_type_code', '03');
        $this->db->where('inv.personal_data_id', $s_personal_data_id);
        $this->db->where('fee.academic_year_id', $s_student_finance_year);
        $this->db->where('inv.date_added >= ', $s_datetime_semester_start);
        $this->db->where('inv.date_added <=', $s_datetime_semester_end);

        $this->db->order_by('inv.date_added', 'asc');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;

    }

    public function get_repeat_registration($a_clause = false)
    {
        $this->db->select('*, st.academic_year_id AS "batch", st.program_id, st.study_program_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'fc.faculty_id = sp.faculty_id');
        $this->db->where('sc.score_mark_for_repetition != ', null);

        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->group_by('sc.student_id');
        $this->db->order_by('st.academic_year_id');
        $this->db->order_by('fc.faculty_name');
        $this->db->order_by('sp.study_program_name');
        $this->db->order_by('pd.personal_data_name');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_score_student($s_student_id, $a_clause = false)
    {
        $this->db->select('*, sc.academic_year_id, sc.semester_type_id, sc.curriculum_subject_id, sc.semester_id AS "student_semester_id"');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student stu', 'stu.student_id = sc.student_id');
        $this->db->join('ref_curriculum_subject curs', 'curs.curriculum_subject_id = sc.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = curs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('ref_semester sm', 'sm.semester_id = sc.semester_id', 'LEFT');
        // $this->db->join('ref_semester_type smt', 'smt.semester_type_id = sc.semester_type_id');
        $this->db->where('sc.student_id', $s_student_id);
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $this->db->order_by('subject_name', 'asc');
        $query = $this->db->get();
		
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_last_score_semester($s_student_id)
    {
        $this->db->select('*, sc.academic_year_id AS "academic_year_semester", sc.semester_type_id AS "semester_type_semester"');
        $this->db->from('dt_score sc');
        $this->db->join('ref_semester rs','rs.semester_id = sc.semester_id');
        $this->db->where_in('rs.semester_type_id', [1,2]);
        $this->db->where('sc.student_id', $s_student_id);
        
        $this->db->order_by('sc.semester_id', 'DESC');
        $this->db->group_by('sc.semester_id');
        $q = $this->db->get();

        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function get_supplement($a_param_data = false)
    {
        if ($a_param_data) {
            $this->db->where($a_param_data);
        }
        $this->db->from('dt_student_supplement');
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function remove_student_supplement($student_supplement_id = false)
    {
        $this->db->delete('dt_student_supplement', array('supplement_id' => $student_supplement_id));
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function save_student_suppement($a_supplement_data, $a_param_data = false)
    {
        if ($a_param_data) {
            $this->db->update('dt_student_supplement', $a_supplement_data, $a_param_data);
            return true;
        }else{
            if (is_object($a_param_data)) {
                $a_param_data = (array)$a_param_data;
            }

            if (!array_key_exists('supplement_id', $a_supplement_data)) {
                $a_supplement_data['supplement_id'] = $this->uuid->v4();
            }
            $this->db->insert('dt_student_supplement', $a_supplement_data);
            return ($this->db->affected_rows() > 0) ? true : false;
        }
    }

    public function get_student_invoice($a_clause = false)
    {
	    $this->db->join('dt_invoice_details did', 'did.invoice_id = di.invoice_id');
        $this->db->join('dt_fee df', 'df.fee_id = did.fee_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
	    $query = $this->db->get('dt_invoice di');
	    return ($query->num_rows() >= 1) ? $query->result() : false;
    }

    public function get_student_by_score($a_clause = false, $a_semester_type_in = false)
    {
        $this->db->select('*, sc.academic_year_id AS "academic_year_id", sc.semester_type_id AS "semester_type_id", st.academic_year_id AS "student_batch", st.program_id, st.study_program_id');
        $this->db->from('dt_score sc');
        $this->db->join('dt_student st', 'st.student_id = sc.student_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = st.personal_data_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = st.study_program_id');
        $this->db->join('ref_faculty fc', 'sp.faculty_id = fc.faculty_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_semester_type_in) {
            $this->db->where_in('sc.semester_type_id', $a_semester_type_in);
        }

        $this->db->order_by('st.finance_year_id');
        $this->db->order_by('fc.faculty_name');
        $this->db->order_by('sp.study_program_name');
        $this->db->group_by('sc.student_id');
        $q = $this->db->get();

        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function update_score_semester($s_semester_id, $s_student_id, $s_academic_year_id, $s_semester_type_id)
    {
        $a_update_data = [
            'semester_id' => $s_semester_id
        ];
        $this->db->update('dt_score', $a_update_data, [
            'student_id' => $s_student_id,
            'academic_year_id' => $s_academic_year_id,
            'semester_type_id' => $s_semester_type_id
        ]);
        return true;
    }
}
