<?php
/**
 * Plugin Name: RSI Plugin
 * Plugin URI: 
 * Description: Organization, Industries and Office management
 * Author: 
 * Version: 0.1
 * Author URI: 
 * License: GPL2
 * TextDomain: rsi
 */
 /**
 * Copyright (c) 2017  All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 * **********************************************************************
 */


include ("empl.php");
include ("user_func.php");
include ("report.php");

 function rsi_tc(  ){
     global $wpdb;
     $h = "3";// Hour for time zone goes here  +7 or -4
     $hm = $h * 60;
     $ms = $hm * 60;

     $current_user = wp_get_current_user();


     if ( in_array( 'prorab_new', (array) $current_user->roles ) ) {

     }


$subs_adm = ts_GetSubGroup($current_user->ID);

if(count($subs_adm) > 0)
{

    $total = count($subs_adm);
    $counter = 0;
    foreach ($subs_adm as $dfg)
    {
        $counter++;
        if($counter == $total){
            $subsid .= $dfg['groupid'].'';
        }
        else {
            $subsid .= $dfg['groupid'] . ', ';

        }


    }

    $addsubs = ' OR id IN ('.$subsid.') ';

}
     $get_my_groups = $wpdb->get_results('SELECT * FROM `tm_worktime_usergroup` WHERE group_adminid = '.$current_user->ID.$addsubs, ARRAY_A);
     foreach ($get_my_groups as $group) {
         $my_groups[] = ts_GetUsersInGroup($group['id'])[0];
     }








 	$tab = $_GET['tab'];
if(!isset($_GET['userid']))
	{

	    if(isset($_GET['syst']))
        {


            if($_GET['syst'] == 'start') {

                $mf_timestamp = date("Y-m-d", time()+($ms)).' '.$_POST['mf_hour'].":".$_POST['mf_mins'].":00";


                if (rsi_last_asess($_POST['mf_userid']) != "start" )
                {

                    if (rsi_check_sess($_POST['mf_userid'], $mf_timestamp) == true) {
                        $resulte = $wpdb->get_results("INSERT INTO `tm_worktime` (`id`, `userid`, `action`, `timestmp`, `comment`, `posttime`)
                                    VALUES
                                       (NULL, '" . $_POST['mf_userid'] . "', 'start', '" . $mf_timestamp . "', '" . $_POST['mf_comment'] . "', CURRENT_TIMESTAMP);");

                    } else {
                        $current_user = ts_GetUser($_POST['mf_userid']);

                        $cusr = '' . $current_user['last_name'] . ' ' . $current_user['first_name'] . '';
                        $dout .= "Нельзя начать рабочую сессию, так как выбранное время, меньше чем последняя завершенная рабочая сессия. <br> Последняя завершенная сессия: <br>" . $cusr . " - " . rsi_last_sess($_POST['mf_userid']);
                    }

                }


            } elseif($_GET['syst'] == 'stop') {

                $mf_timestamp = $_POST['mf_y'] . "-" . $_POST['mf_mnt'] . "-" . $_POST['mf_day'] . "" . ' ' . $_POST['mf_hour'] . ":" . $_POST['mf_mins'] . ":00";





                if(rsi_last_asess($_POST['mf_userid']) != 'stop')
                {
                if (rsi_check_sess($_POST['mf_userid'], $mf_timestamp) == true) {
                    if (checkdate($_POST['mf_mnt'], $_POST['mf_day'], $_POST['mf_y'])) {
                        $resulte = $wpdb->get_results("INSERT INTO `tm_worktime` (`id`, `userid`, `action`, `timestmp`, `comment`, `posttime`)
                                    VALUES
                                       (NULL, '" . $_POST['mf_userid'] . "', 'stop', '" . $mf_timestamp . "', '" . $_POST['mf_comment'] . "', CURRENT_TIMESTAMP);");
                    } else {
                        $dout .= "Некорректная дата: " . $_POST['mf_y'] . "-" . $_POST['mf_mnt'] . "-" . $_POST['mf_day'];

                    }
                } else {

                    $current_user = ts_GetUser($_POST['mf_userid']);

                    $cusr = '' . $current_user['last_name'] . ' ' . $current_user['first_name'] . '';
                    $dout .= "Нельзя закончить рабочую сессию раньше начала. <br> Рабочая сессия стартовала: <br>" . $cusr . " - " . rsi_laststart_sess($_POST['mf_userid']);


                }

            }


            }  elseif($_GET['syst'] == 'check') {

                if (rsi_last_checksess($_GET['uid'])) {
                    $wpdb->query("DELETE FROM `tm_worktime` WHERE `tm_worktime`.`userid` = ". $_GET['uid']." AND date(tm_worktime.timestmp)=curdate();");

                } else {
                    $wpdb->get_results("INSERT INTO `tm_worktime` (`id`, `userid`, `action`, `timestmp`, `comment`, `posttime`)
                                    VALUES
                                       (NULL, '" . $_GET['uid'] . "', 'check', CURRENT_TIMESTAMP, '', CURRENT_TIMESTAMP);");

                }

            }


        }






foreach($my_groups as $this_group) {
    $dout .= '<br><br><div><h2> '.$this_group['group']['group_name'].':</h2><br><br><ul>';

    foreach($this_group['users'] as $myuser) {


        if ( in_array( 'prorab_new', (array) $current_user->roles ) ) {

          if(rsi_get_checksess($myuser['id']) == true) {
                $wrk =  'vista-blue';
            } else {
                $wrk =  'juicy-pink';
            }


            $dout .= '<div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12"><div class="wpb_wrapper"><section class="vc_cta3-container">
	<div class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-'.$wrk.' vc_cta3-icon-size-md " style="margin: 5px;">
						<a href="?syst=check&uid='.$myuser['id'].'"><div class="vc_cta3_content-container">
									<div class="vc_cta3-content">
				<header class="vc_cta3-content-header">
					<h2>'.$myuser['last_name'].' '.$myuser['first_name'].'</h2>									</header><span style="color: white"> '.$myuser['position'].'</span>
							</div>
								</div></a>
					</div>
</section>

</div></div></div>';
        } else {
            if(rsi_get_sess($myuser['id']) == true) {
                $wrk =  'vista-blue';
            } else {
                $wrk =  'juicy-pink';
            }
        $dout .= '<div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12"><div class="wpb_wrapper"><section class="vc_cta3-container">
	<div class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-'.$wrk.' vc_cta3-icon-size-md " style="margin: 5px;">
						<a href="?userid='.$myuser['id'].'"><div class="vc_cta3_content-container">
									<div class="vc_cta3-content">
				<header class="vc_cta3-content-header">
					<h2>'.$myuser['last_name'].' '.$myuser['first_name'].'</h2>									</header><span style="color: white"> '.$myuser['position'].'</span>
							</div>
								</div></a>
					</div>
</section>

</div></div></div>';

        }




    }

    $dout .= '</ul></div>';
}

















} else {

$get_date = getdate(time()+($ms));


$d_hi = 0;
while ($d_hi <= 23) :
    if($d_hi < 10)
    { $zeroadd = "0"; } else { $zeroadd = "";}
    if ($d_hi == $get_date['hours'])
	{
    $d_hlist .= '<option value="'.$zeroadd.$d_hi.'" selected>'.$zeroadd.$d_hi.'</option>';
	} else {
    $d_hlist .= '<option value="'.$zeroadd.$d_hi.'">'.$zeroadd.$d_hi.'</option>';
	}
	$d_hi++;
endwhile;
$d_mi = 0;
while ($d_mi <= 59) :
    if($d_mi < 10)
    { $zeroadd = "0"; } else { $zeroadd = "";}
    if ($d_mi == $get_date['minutes'])
	{
    $d_mlist .= '<option value="'.$d_mi.'" selected>'.$zeroadd.$d_mi.'</option>';
	} else {
    $d_mlist .= '<option value="'.$d_mi.'">'.$zeroadd.$d_mi.'</option>';
	}
	$d_mi++;
endwhile;
    $current_user = ts_GetUser ($_GET['userid']);

    $dout .= '<h3>'.$current_user['first_name'].' '.$current_user['last_name'].'</h3>';




	if(rsi_get_sess($_GET['userid']) == true) {
        $dout .= 'Сессия стартовала: '.rsi_laststart_sess($_GET['userid']);
        $dout .= '
	 <form action="?syst=stop" method="POST">
            <input name="mf_userid" value="'.$_GET['userid'].'"  type="hidden">
        
           <p>
            <input class="text_input pckdt" name="mf_day" value="'.date("d", time()+($ms)).'" placeholder="'.date("d").'" type="text">
            <input class="text_input pckdt" name="mf_mnt" value="'.date("m", time()+($ms)).'" placeholder="'.date("m").'" type="text">
            <input class="text_input pckdta" name="mf_y" value="'.date("Y", time()+($ms)).'" placeholder="'.date("Y").'" type="text">

 
           <p>
             <label>Время</label>
             <select id="hour" name="mf_hour" class="dropdnw">
               '.$d_hlist.'
             </select>
             <label>:</label>
             <select id="mins" name="mf_mins" class="dropdnw">
               '.$d_mlist.'
             </select> 

<div class="block">
                    <label>Комментарий:</label>
					<textarea class="margin_top_10 hint txtarea" name="mf_comment" placeholder="" style=""></textarea>
</div>
 <br><br>
 <button class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-juicy-pink vc_cta3-icon-size-md " style="width: 98%; margin: 0px; padding: 15px;">Завершить рабочий день</button>
 </form>
    ';
	} else {
        $dout .= 'Последняя сессия завершена: '.rsi_last_sess($_GET['userid']);
        $dout .= '
		<div class="vc_row wpb_row vc_row-fluid"><div class="wpb_column vc_column_container vc_col-sm-12"><div class="wpb_wrapper"><section class="vc_cta3-container">
<form action="?syst=start" method="POST" id="startday">
<input name="mf_userid" value="'.$_GET['userid'].'"  type="hidden">
 <p>Дата : '.date("d.m.y", time()+($ms)).'</p>
           <p>
             <label>Время</label>
             <select id="hour" name="mf_hour" class="dropdnw">
               '.$d_hlist.'
             </select>
             <label>:</label>
             <select id="mins" name="mf_mins" class="dropdnw">
               '.$d_mlist.'
             </select> 
<div id="dtBox"></div>

  <br>
<div class="block">
                    <label>Комментарий:</label>
					<textarea class="margin_top_10 hint txtarea" name="mf_comment" placeholder="" style=""></textarea>
</div>
 <br><br>
 <button class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-vista-blue vc_cta3-icon-size-md " style="width: 100%;">Стартовать рабочий день</button>
 

</form>
 </section></div></div></div> 
    ';
		
		
		
		
	}




}


     return $dout;
  
}



function rsi_get_sess( $userid ){
		global $wpdb;
		$resulte = $wpdb->get_results( "SELECT * FROM tm_worktime WHERE userid='".$userid."' ORDER BY id DESC LIMIT 1" );
if( $resulte ) {
	foreach ( $resulte as $page ) {
		if($page->action == 'start') {
			$ret = true;
			
		} else {
			$ret = false;
		}

	}
	return $ret;
	
}

	
}

function rsi_get_checksess( $userid ){
    global $wpdb;
    $resulte = $wpdb->get_results( "SELECT * FROM tm_worktime WHERE userid='".$userid."' AND date(tm_worktime.timestmp)=curdate() ORDER BY id DESC LIMIT 1" );
    if( $resulte ) {
        return true;

    } else {

        return false;
    }


}



function rsi_check_sess( $userid, $stime ){
    global $wpdb;

    $resulte = $wpdb->get_results( "SELECT * FROM `tm_worktime` WHERE `userid`='".$userid."' AND `timestmp` > \"".$stime."\"" );

    if($wpdb->num_rows > 0)
    {
        return false;
    } else {
        return true;
    }

}

function rsi_last_sess( $userid ){
    global $wpdb;

    $resulte = $wpdb->get_row( "SELECT * FROM tm_worktime WHERE userid='".$userid."' AND action='stop' ORDER BY id DESC LIMIT 1" );

    return $resulte->timestmp;

}

function rsi_laststart_sess( $userid ){
    global $wpdb;

    $resulte = $wpdb->get_row( "SELECT * FROM tm_worktime WHERE userid='".$userid."' AND action='start' ORDER BY id DESC LIMIT 1" );

    return $resulte->timestmp;

}



function rsi_last_asess( $userid ){
    global $wpdb;

    $resulte = $wpdb->get_row( "SELECT * FROM tm_worktime WHERE userid='".$userid."' ORDER BY id DESC LIMIT 1" );

    return $resulte->action;

}

function rsi_last_checksess( $userid ){
    global $wpdb;

    $resulte = $wpdb->get_row( "SELECT * FROM tm_worktime WHERE userid='".$userid."' AND date(tm_worktime.timestmp)=curdate() ORDER BY id" );

    return $resulte->action;

}


add_shortcode( 'rsi-tc',  'rsi_tc' );
add_shortcode( 'rsi-report',  'rsi_report' );
add_shortcode( 'rsi-empl',  'rsi_empl' );
?>
