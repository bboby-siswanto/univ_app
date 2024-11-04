<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grades{

	public function thesis_final_score($d_twe, $d_tpe)
	{
		if ($d_twe == 0) {
			$d_twe_result = 0;
		}else {
			$d_twe_result = (double)$d_twe * 60/100;
		}

		if ($d_tpe == 0) {
			$d_tpe_result = 0;
		}else {
			$d_tpe_result = (double)$d_tpe * 40/100;
		}
		
		$score_sum = ($d_twe_result) + ($d_tpe_result);
		return round($score_sum, 2, PHP_ROUND_HALF_UP);
	}

	public function lecturer_assessment_grade($d_assessment_score) {
		$grade = "Fail";
		if ($d_assessment_score == 5) {
			$grade = "Excelent";
		}
		else if ($d_assessment_score >= 4) {
			$grade = "Good";
		}
		else if ($d_assessment_score >= 3) {
			$grade = "Satisfactory";
		}
		else if ($d_assessment_score >= 2) {
			$grade = "Poor";
		}

		return $grade;
	}

	public function conversion_score_to_german($d_score_sum)
	{
		if ($d_score_sum <= 28) {
			$s_score = 5.0;
		}else{
			$x = 3 * ((100 - $d_score_sum) / (100 - 46));
			$s_score = 1 + $x;
		}
		return round($s_score, 1, PHP_ROUND_HALF_UP);
	}

	public function get_graduation_predicate($cumulative_gpa)
	{
		$s_predicate = '-';

		if(($cumulative_gpa >= 3.5) AND ($cumulative_gpa <= 3.7)){
			$s_predicate = 'Cum laude';
		}
		
		if(($cumulative_gpa >= 3.71) AND ($cumulative_gpa <= 3.89)){
			$s_predicate = 'Magna cum laude';
		}
		
		if(($cumulative_gpa >= 3.9) AND ($cumulative_gpa <= 4)){
			$s_predicate = 'Summa cum laude';
		}

		return $s_predicate;
	}

	public function get_ipk($i_total_merit, $i_total_sks)
	{
		if (($i_total_merit == 0) OR ($i_total_sks == 0)) {
			$ipk = 0;
		}else {
			$ipk = (double)$i_total_merit / (double)$i_total_sks;
		}
		return round($ipk, 2, PHP_ROUND_HALF_UP);
	}

	public function get_score_ects($sks, $grade_point)
	{
		if (($sks == 0) OR ($grade_point == 0)) {
			$i_ects_sks = 0;
		}else {
			$i_ects_sks = (double)$sks * 1.4;
		}
		return round($i_ects_sks, 1, PHP_ROUND_HALF_UP);
	}

	public function get_ects_score($sks, $s_subject_name, $s_ects_score = null)
	{
		if ($sks == 0) {
			$i_ects_sks = 0;
		}else {
			$i_ects_sks = (double)$sks * 1.4;
		}

		$s_subject_name = strtolower($s_subject_name);

		if(strpos($s_subject_name, 'internship') !== false){
			$i_ects_sks = 12.0;
		}
		else if(strpos($s_subject_name, 'thesis') !== false){
			$i_ects_sks = 14.0;
		}
		else if(strpos($s_subject_name, 'research semester') !== false){
			$i_ects_sks = 12.0;
		}
		
		if(!is_null($s_ects_score)){
			$i_ects_sks = $s_ects_score;
		}

		return round($i_ects_sks, 1, PHP_ROUND_HALF_UP);
	}

	public function get_merit($sks, $grade_point)
	{
		if (($sks == 0) OR ($grade_point == 0)) {
			$score_merit = 0;
		}else {
			$score_merit = (double)$sks * (double)$grade_point;
		}
		return round($score_merit, 2, PHP_ROUND_HALF_UP);
	}

	public function get_score_sum($s_score_quiz, $s_final_exam)
	{
		if ($s_score_quiz == 0) {
			$d_quiz_result = 0;
		}else {
			$d_quiz_result = (double)$s_score_quiz * 40/100;
		}

		if ($s_final_exam == 0) {
			$d_final_exam_result = 0;
		}else {
			$d_final_exam_result = (double)$s_final_exam * 60/100;
		}
		
		$score_sum = ($d_quiz_result) + ($d_final_exam_result);
		return round($score_sum, 2, PHP_ROUND_HALF_UP);
	}

	public function get_score_absence($i_alpha_count, $i_subject_credit)
	{
		if ($i_subject_credit == 0) {
			$f_temp = 0;
		}else{
			$i_total_meeting = ($i_subject_credit * 14);
			$f_temp = ($i_total_meeting - $i_alpha_count) / $i_total_meeting;
		}
		
		$result = 100-($f_temp) * 100;
		return round($result, 2, PHP_ROUND_HALF_UP);
	}
	
	public function get_grade($sScore) {
		$grade = '';

		// score is above 86
		if ($sScore >= 85.5) {
			$grade = "A";
		} else {
			// score is above 71
			if ($sScore >= 70.5) {
				$grade = "B";
			}

			// score is above 56
			else if ($sScore >= 55.5) {
				$grade = "C";
			}

			// score is above 45.5
			else if ($sScore >= 45.5) {
				$grade = "D";
			}

			// score below 45
			else {
				$grade = "F";
			}
		}
		return $grade;
	}

	public function get_grade_point($sScore) {
		$gp = '';

		// grade A
		if ($sScore > 85) {
			$gp = 4;
		} else {

			// grade B and C
			if (($sScore >= 71) OR ($sScore >= 56)) {
				$gp = ($sScore - 26) / 15;
			}

			// grade D
			else if ($sScore >= 46) {
				$gp = ($sScore - 36) / 10;
			}

			// grade F
			else {
				$gp = 0;
			}
		}

		// round up, 2
		return round($gp, 2, PHP_ROUND_HALF_UP);
		// return $gp;
	}

	public function get_credit_realization($i_sks, $i_count_subject_delivered)
	{
		$i_sks = intval($i_sks);
		$i_count_subject_delivered = intval($i_count_subject_delivered);

		if (($i_sks == 0) OR ($i_count_subject_delivered == 0)) {
			$f_result = 0;
		}else{
			$f_result = $i_sks / (($i_sks * 14) / $i_count_subject_delivered);
		}

		return round($f_result, 2, PHP_ROUND_HALF_UP);
	}

	public function get_ofse_score($s_score_examiner_one, $s_score_examiner_two)
	{
		$ofse_score = (number_format($s_score_examiner_one, 2) + number_format($s_score_examiner_two, 2)) / 2;
		return round($ofse_score, 2, PHP_ROUND_HALF_UP);
	}

	public function conversion_ects_credit($d_credit)
	{
		$d_ects = 0;
		if ($d_credit > 0) {
			$d_ects = round((intval($d_credit) * 1.4), 2);
		}

		return $d_ects;
	}
}