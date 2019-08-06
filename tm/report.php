<?php

function rsi_report(){
    global $wpdb;

    $montsarray = array(
       'Январь',
       'Февраль',
       'Март',
       'Апрель',
       'Май',
       'Июнь',
       'Июль',
       'Август',
       'Сентябрь',
       'Октябрь',
       'Ноябрь',
       'Декабрь'

    );
    $h = "3";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -
    $hm = $h * 60;
    $ms = $hm * 60;





if(!isset($_GET['sys_mont']) && !isset($_GET['sys_year']))
{
    $report_month = date("m", time()+($ms));
    $report_month = $report_month*1;
    $report_year = date("Y", time()+($ms));
} else {
    $report_month = $_GET['sys_mont'];
    $report_year = $_GET['sys_year'];

}



    $mn = 1;
    foreach ($montsarray as $msmonth)
    {
        if($mn == $report_month)
        { $sel = 'selected';} else {$sel='';}
        $mnt_out .= '<option value="'.$mn.'" '.$sel.'>'.$msmonth.'</option>';
        $mn++;
    }


$mout .= '<form action="" id="mf_date" method="GET" >
<select id="sys_mont" name="sys_mont" class="dropdnw">
               '.$mnt_out.'
             </select>

<select id="sys_year" name="sys_year" class="dropdnw">';


    for ($iyear = 2017; $iyear <= 2027; $iyear++) {
        if ($iyear == date("Y")) {
        $mout .= '<option value="'.$iyear.'" selected>'.$iyear.'</option>';
        } else {
        $mout .= '<option value="'.$iyear.'" >'.$iyear.'</option>';
        }
    }
/*               <option value="2017" >2017</option>
               <option value="2018" selected>2018</option>
               <option value="2019" >2019</option>
               <option value="2020" >2020</option>
               <option value="2021" >2021</option>
*/
$mout .='             </select>
<button class="" style="height: 47px;"> Выбрать</button>

</form><br><br>';


$num_days = date("t", strtotime($report_year."-".$report_month));

    for ($d = 1; $d <= $num_days; $d++) {

        $header_tbl .= '<td>'.$d.'</td>';
        if($d == 15)
        {
            $header_tbl .= '<td>Итого за 15 дней</td>';
        }

    }


    $mout .=  '<table border="1" cellpadding="1" cellspacing="1" style="width:auto">
	<tbody>
		<tr>
			<td>№</td>
			<td>Фамилия, инициалы, должность</td>
			<td>Объект</td>
			<td>Ответственный</td>
'.$header_tbl.'
			<td>Итого за месяц</td>
		</tr>';

    $users = ts_GetUsers();
    $i = 1;
    foreach($users as $this_user) {

        $getGroup = ts_GetGroup($this_user['gropid']);
        $this_admin = get_user_meta ( $getGroup['group_adminid']);


        $totaltime = 0;


        $mout .= '<tr>
			<td style="background-color: '.$getGroup['color'].';">'.$i.'</td>
			<td>'.$this_user['last_name'].' '.$this_user['first_name'].'<br> '.$this_user['position'].'</a></td>
			<td>'.$getGroup['group_name'].'</td>
			<td>'.$this_admin['last_name'][0].' '.$this_admin['first_name'][0].'</td>';
        $gt_tmusr = GetUserDayTime($this_user['id'], $report_month, $report_year);

        for ($d = 1; $d <= $num_days; $d++) {



    $mz = strtotime($report_year . "-" . $report_month . "-" . $d);
    $gz = strtotime(date("Y-m-d", time() + ($ms)));


    if (($mz - $gz) < 0) {
        $t_hr = RevInt($gt_tmusr[$report_year][$report_month][$d]['total_hours']);
        $t_min = RevInt($gt_tmusr[$report_year][$report_month][$d]['total_mins']);


        if ($t_hr > 0 or $t_min > 0) {
            $datta = GetTimeS(($t_hr * 60) + $t_min);
            $hr = $datta['hr'] . 'ч ' . $datta['mn'] . 'м';
            $hr_style = 'LightCyan';
            $totaltime += ($t_hr * 60) + $t_min;
        } else {
            $hr = 'Н';
            $hr_style = 'SeaShell';
        }
    }

if(!isset($gt_tmusr[$report_year][$report_month][$d]['checker'])) {
    $mout .= '<td style="background-color: ' . $hr_style . '">' . $hr . '</td>';
} else {
    $mout .= '<td style="background-color: PaleGreen">П</td>';

}
    if ($d == 15) {
        $datti = GetTimeS($totaltime);
        $mout .= '<td>' . $datti['hr'] . 'ч ' . $datti['mn'] . 'м' . '</td>';
    }
    unset($hr);
    unset($hr_style);


        }

        $datti = GetTimeS($totaltime);
        $mout .= '<td>'.$datti['hr'] . 'ч ' . $datti['mn'] . 'м'.'</td>
		</tr>';
        $i++;
    }



    $mout .= '	</tbody>
</table>

';
    return $mout;
}


function GetUserDayTime($userid, $month, $year)
{
    global $wpdb;
    $get_data = $wpdb->get_results('SELECT * FROM `tm_worktime` WHERE userid = '.$userid.' AND `timestmp` >= \''.$year.'-'.$month.'-01 00:00:00\' AND `timestmp` <= \''.$year.'-'.$month.'-01 00:00:00\' + INTERVAL 1 MONTH ORDER by id', ARRAY_A);

    foreach ($get_data as $data) {
        list($ddate, $dtime) = explode(" ", $data['timestmp']);
        $get_date_arr = date_parse_from_format("Y-m-d H:i:s", $data['timestmp']);


        if($data['action'] == 'start')
        {
            $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_hours'] = $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_hours'] + $get_date_arr['hour'];
            $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_mins'] = $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_mins'] + $get_date_arr['minute'];

        } elseif($data['action'] == 'stop') {
            $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_hours'] = $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_hours'] - $get_date_arr['hour'];
            $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_mins'] = $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['total_mins'] - $get_date_arr['minute'];

        } else {
            $date_arr[$get_date_arr['year']][$get_date_arr['month']][$get_date_arr['day']]['checker'] = 'Y';
        }


    }

return $date_arr;
}

function RevInt($my_int)
{
    return $my_int - ($my_int*2);
}

function GetTimeS($my_int)
{

    $h= round($my_int/60,2); // Переводм в часы с дробной частью 
    $hours = floor($my_int / 60); // Получаем челое число часов 
    $m=$h-$hours; // Получаем дробную часть от часов 
    $minutes= floor($m*60);


    $retarr = array(
    'hr' => $hours,
    'mn' => $minutes
);


    return $retarr;
}

?>
