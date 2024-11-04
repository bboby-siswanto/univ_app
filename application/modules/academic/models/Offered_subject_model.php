<?php
class Offered_subject_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function submit_subject_data($a_post)
    {
        $this->db->trans_begin();
        
        // $this->db->from('ref_subject sb');
        // $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        // $this->db->where('sn.subject_name', $a_post['osns_subject_name']);
        // $this->db->where('sb.study_program_id', $a_post['study_program_id']);
        // $this->db->where('sb.subject_credit', $a_post['osns_subject_credit']);
        // $query_subject = $this->db->get();

        $a_message_error = [];
        // if ($query_subject->num_rows() > 0) {
        //     array_push($a_message_error, 'Subject exists');
        // }
        // else {
            $s_dateadded = date('Y-m-d H:i:s');
            $s_subject_id =$this->uuid->v4();
            
            $query_subject_name = $this->db->get_where('ref_subject_name', ['subject_name' => $a_post['osns_subject_name']]);
            if ($query_subject_name->num_rows() > 0) {
                $subject_name_data = $query_subject_name->first_row();
                $s_subject_name_id = $subject_name_data->subject_name_id;
                $s_subject_name_code = $subject_name_data->subject_name_code;
            }
            else {
                $s_subject_name_id = $this->uuid->v4();
                $s_subject_name_code = explode('-', $a_post['osns_subject_code'])[0];
                $a_subject_name_data = [
                    'subject_name_id' => $s_subject_name_id,
                    'subject_name' => $a_post['osns_subject_name'],
                    'subject_name_code' => $s_subject_name_code,
                    'date_added' => $s_dateadded
                ];
                $this->db->insert('ref_subject_name', $a_subject_name_data);
            }

            $a_subject_data = [
                'subject_id' => $s_subject_id,
                'subject_name_id' => $s_subject_name_id,
                'subject_code' => $a_post['osns_subject_code'],
                'study_program_id' => $a_post['study_program_id'],
                'program_id' => $a_post['program_id'],
                'subject_credit' => $a_post['osns_subject_credit'],
                'date_added' => $s_dateadded
            ];
            $this->db->insert('ref_subject', $a_subject_data);

            $this->db->from('ref_curriculum');
            $this->db->where('study_program_id', $a_post['study_program_id']);
            $this->db->where('program_id', $a_post['program_id']);
            $this->db->order_by('valid_academic_year', 'DESC');
            $query_check_curriculum = $this->db->get();
            if ($query_check_curriculum->num_rows() > 0) {
                $o_curriculum_selected = $query_check_curriculum->first_row();
                $s_curriculum_id = $o_curriculum_selected->curriculum_id;

                $query_curriculum_semester = $this->db->get_where('ref_curriculum_semester', ['curriculum_id' => $s_curriculum_id, 'semester_id' => $a_post['osns_semester_id']]);
                if ($query_curriculum_semester->num_rows() == 0) {
                    $a_curriculum_semester = [
                        'curriculum_id' => $s_curriculum_id,
                        'semester_id' => $a_post['osns_semester_id'],
                        'date_added' => $s_dateadded
                    ];
                    $this->db->insert('ref_curriculum_semester', $a_curriculum_semester);
                }
            }
            else {
                $s_curriculum_id = $this->uuid->v4();
                $a_curriculum_data = [
                    'curriculum_id' => $s_curriculum_id,
                    'study_program_id' => $a_post['study_program_id'],
                    'program_id' => $a_post['program_id'],
                    'academic_year_id' => date('Y'),
                    'valid_academic_year' => date('Y'),
                    'curriculum_name' => 'Curriculum '.date('Y'),
                    'date_added' => $s_dateadded
                ];
                $this->db->insert('ref_curriculum', $a_curriculum_data);

                $a_curriculum_semester = [
                    'curriculum_id' => $s_curriculum_id,
                    'semester_id' => $a_post['osns_semester_id'],
                    'date_added' => $s_dateadded
                ];
                $this->db->insert('ref_curriculum_semester', $a_curriculum_semester);
            }

            $s_curriculum_subject_id = $this->uuid->v4();
            $a_curriculum_subject = [
                'curriculum_subject_id' => $s_curriculum_subject_id,
                'curriculum_id' => $s_curriculum_id,
                'semester_id' => $a_post['osns_semester_id'],
                'subject_id' => $s_subject_id,
                'curriculum_subject_code' => '',
                'curriculum_subject_credit' => $a_post['osns_subject_credit'],
                'curriculum_subject_ects' => $this->grades->get_score_ects($a_post['osns_subject_credit'], 2),
                'curriculum_subject_category' => 'regular semester',
                'curriculum_subject_type' => $a_post['osns_cur_subject_type'],
                'date_added' => $s_dateadded
            ];
            $this->db->insert('ref_curriculum_subject', $a_curriculum_subject);
        // }

        if (count($a_message_error) > 0) {
            $this->db->trans_rollback();
            $a_return = ['code' => 1, 'message' => implode('; ', $a_message_error)];
        }
        else if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $a_return = ['code' => 1, 'message' => 'Failed response process!'];
        }
        else {
            $this->db->trans_commit();
            $a_return = ['code' => 0, 'message' => 'Success', 'curriculum_subject_id' => $s_curriculum_subject_id];
        }

        return $a_return;
    }

    public function get_offered_subject_curriculum($a_clause = false, $a_study_program_id = false)
    {
        $this->db->from('ref_subject sb');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        $this->db->join('ref_curriculum_subject cs', 'cs.subject_id = sb.subject_id');
        $this->db->join('ref_curriculum cr', 'cr.curriculum_id = cs.curriculum_id');
        $this->db->join('ref_study_program sp', 'sp.study_program_id = cr.study_program_id');
        $this->db->join('ref_semester sm', 'sm.semester_id = cs.semester_id');

        if ($a_clause) {
            $this->db->where($a_clause);
        }

        if ($a_study_program_id) {
            $this->db->where_in('cr.study_program_id', $a_study_program_id);
        }

        $this->db->group_by('sn.subject_name, cs.curriculum_subject_credit');
        $this->db->order_by('sn.subject_name');
        $this->db->order_by('cs.curriculum_subject_credit');
        $this->db->order_by('cs.date_added', 'DESC');

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_offer_subject_class_group($a_filter_data = false)
    {
        $this->db->from('dt_offered_subject ofs');
        $this->db->join('dt_class_group_subject cgs', 'cgs.offered_subject_id = ofs.offered_subject_id');
        $this->db->join('dt_class_group cg', 'cg.class_group_id = cgs.class_group_id');
        if ($a_filter_data) {
            $this->db->where($a_filter_data);
        }
        $this->db->group_by('cg.class_group_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_offered_subject_filtered($a_clause = false)
    {
        $this->db->from('dt_offered_subject dos');
        if ($a_clause) {
            foreach ($a_clause as $key => $value) {
                if ($value != '') {
                    $this->db->where($key, $value);
                }
            }
        }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_offer_subject_lecturer($s_offered_subject_id)
    {
        $this->db->from('dt_class_group_subject cgs');
        $this->db->join('dt_class_group_lecturer cgl', 'cgl.class_group_id = cgs.class_group_id');
        $this->db->join('dt_employee de', 'de.employee_id = cgl.employee_id');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = de.personal_data_id');
        $this->db->where('cgs.offered_subject_id', $s_offered_subject_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_offered_subject_lists_filtered($a_clause = false)
    {
        $this->db->select("
        	*,
        	rsm.semester_number,
            dos.academic_year_id as running_year,
            dos.semester_type_id,
            dos.program_id,
            dos.study_program_id
        ");
        $this->db->from('dt_offered_subject dos');
        $this->db->join('ref_curriculum_subject rcs', 'rcs.curriculum_subject_id = dos.curriculum_subject_id');
        $this->db->join('ref_study_program rsp', 'rsp.study_program_id = dos.study_program_id');
        $this->db->join('ref_semester_type rst', 'rst.semester_type_id =  dos.semester_type_id');
        $this->db->join('ref_subject rs', 'rcs.subject_id = rs.subject_id');
        $this->db->join('ref_subject_name rsn', 'rs.subject_name_id = rsn.subject_name_id');
        $this->db->join('ref_semester rsm', 'rsm.semester_id = rcs.semester_id');
        if ($a_clause) {
            foreach ($a_clause as $key => $value) {
                if ($value != '') {
                    $this->db->where($key, $value);
                }
            }
        }
        // if ($this->session->userdata('user') != '47013ff8-89df-11ef-8f45-0068eb6957a0') {
            $this->db->group_by('dos.curriculum_subject_id');
        // }
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get_offered_subject_subject($a_clause = false)
    {
        $this->db->from('dt_offered_subject  os');
        $this->db->join('ref_curriculum_subject  cs', 'cs.curriculum_subject_id = os.curriculum_subject_id');
        $this->db->join('ref_subject sb', 'sb.subject_id = cs.subject_id');
        $this->db->join('ref_subject_name sn', 'sn.subject_name_id = sb.subject_name_id');
        if ($a_clause) {
            $this->db->where($a_clause);
        }
        $q = $this->db->get();
        return ($q->num_rows() > 0) ? $q->result() : false;
    }

    public function update_sync_offered_subject($a_offer_subject_data, $s_offered_subject_id)
    {
        $this->db->update('dt_offered_subject', $a_offer_subject_data, array('offered_subject_id' => $s_offered_subject_id));
        return true;
    }

    public function save_offer_subject($a_offer_subject_data)
    {
        if (is_object($a_offer_subject_data)) {
            $a_offer_subject_data = (array) $a_offer_subject_data;
        }

        if (!array_key_exists('offered_subject_id', $a_offer_subject_data)) {
            $a_offer_subject_data['offered_subject_id'] = $this->uuid->v4();
        }

        $this->db->insert('dt_offered_subject', $a_offer_subject_data);
        return ($this->db->affected_rows() > 0) ? true : false;
    }

    public function remove_offered_subject($s_offered_subject_id)
    {
        $this->db->delete('dt_offered_subject', array('offered_subject_id' => $s_offered_subject_id));
        return ($this->db->affected_rows() > 0) ? true : false;
        // return $this->db->error();
    }
}
