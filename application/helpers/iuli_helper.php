<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('numberToRomanRepresentation')) {
    function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}

if (!function_exists('number_to_words'))
{
    function number_to_words($s_number = 0) {
        $ones = array(
            0 => "",
            1 => "one", 
            2 => "two", 
            3 => "three", 
            4 => "four", 
            5 => "five", 
            6 => "six", 
            7 => "seven",
            8 => "eight",
            9 => "nine",
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen", 
            18 => "eighteen", 
            19 => "nineteen" 
        );
        $tens = array( 
            0 => "",
            1 => "ten",
            2 => "twenty", 
            3 => "thirty", 
            4 => "forty", 
            5 => "fifty", 
            6 => "sixty", 
            7 => "seventy", 
            8 => "eighty", 
            9 => "ninety" 
        ); 
        $hundreds = array( 
            "hundred", 
            "thousand", 
            "million", 
            "billion", 
            "trillion", 
            "quadrillion" 
        );
        $a_number_decimal = explode('.', $s_number);
        $decimal = (count($a_number_decimal) > 1) ? strlen($a_number_decimal[1]) : 0;

        $number = number_format($s_number, $decimal, ".", ","); 
        $a_number = explode(".",$number); 
        $whole_num = $a_number[0]; 
        $decnum = ($decimal > 0) ? $a_number[1] : 0;
        $whole_arr = array_reverse(explode(",",$whole_num)); 
        krsort($whole_arr); 
        $rettxt = "";

        foreach($whole_arr as $key => $i){
            $i = intval($i);
            if($i < 20){ 
                $rettxt .= $ones[$i]; 
            }elseif($i < 100){ 
                $rettxt .= $tens[substr($i,0,1)]; 
                @$rettxt .= " ".$ones[substr($i,1,1)]; 
            }else{ 
                $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
                $rettxt .= " ".$tens[substr($i,1,1)]; 
                $rettxt .= " ".$ones[substr($i,2,1)]; 
            } 
            if($i > 0){
                $rettxt .= " ".$hundreds[$key]." "; 
            }
        }

        if($decnum > 0){ 
            $rettxt .= " and "; 
            if($decnum < 20){ 
                $rettxt .= $ones[intval( $decnum) ]; 
            }elseif( $decnum < 100){ 
                $rettxt .= $tens[substr($decnum,0,1)]; 
                $rettxt .= " ".$ones[substr($decnum,1,1)]; 
            } 
        } 
        return $rettxt; 
    }
}