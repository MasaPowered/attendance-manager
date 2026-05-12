<?php

if (!function_exists('pulldown_month')) {
    function pulldown_month()
    {
        echo '<select name=month>';
        echo '<option value="01">01</option>';
        echo '<option value="02">02</option>';
        echo '<option value="03">03</option>';
        echo '<option value="04">04</option>';
        echo '<option value="05">05</option>';
        echo '<option value="06">06</option>';
        echo '<option value="07">07</option>';
        echo '<option value="08">08</option>';
        echo '<option value="09">09</option>';
        echo '<option value="10">10</option>';
        echo '<option value="11">11</option>';
        echo '<option value="12">12</option>';
        echo '</select>';
    }
}

if (!function_exists('pulldown_shift')) {
    function pulldown_shift($day, $shift = null)
    {
        $value = '<select name="shift' . $day . '">';
        $value .= '<option value=""></option>';
        $value .= '<option value="出勤" ' . ((!strcmp($shift, "出勤")) ? 'selected' : '') . '>出勤</option>';
        $value .= '<option value="休" ' . ((!strcmp($shift, "休")) ? 'selected' : '') . '>休</option>';
        $value .= '<option value="確休" ' . ((!strcmp($shift, "確休")) ? 'selected' : '') . '>確休</option>';
        $value .= '<option value="在宅" ' . ((!strcmp($shift, "在宅")) ? 'selected' : '') . '>在宅</option>';
        $value .= '</select>';
        return $value;
    }
}

if (!function_exists('pulldown_monthshift')) {
    function pulldown_monthshift($shift = null)
    {
        echo '<select name="month_shift">';
        echo '<option value=""></option>';
        echo '<option value="出勤" ' . ((!strcmp($shift, "出勤")) ? 'selected' : '') . '>出勤</option>';
        echo '<option value="休" ' . ((!strcmp($shift, "休")) ? 'selected' : '') . '>休</option>';
        echo '<option value="確休" ' . ((!strcmp($shift, "確休")) ? 'selected' : '') . '>確休</option>';
        echo '<option value="在宅" ' . ((!strcmp($shift, "在宅")) ? 'selected' : '') . ' >在宅</option>';
        echo '</select>';
    }
}
