<?php

function rsi_empl(){
    global $wpdb;

if(!isset($_GET['tab']) or $_GET['tab'] == 'empl')
{
    $menu = 'Сотрудники | <a href="?tab=groups">Группы</a>';



    if($_POST['mf_syst'] == 'add_user') {


        $as = ts_AddUser($_POST['mf_fname'], $_POST['mf_lname'], $_POST['mf_pos'], $_POST['mf_phone'], $_POST['mf_main_group']);

        foreach ($_POST['mf_sec_group'] as $itm)
        {
            ts_AddUserInGroup($itm, $as);

        }

    }
    if(isset($_GET['delid']) and $_GET['delid'] != '' )
    {

        ts_DelUser($_GET['delid']);
    }

    if($_POST['mf_syst'] == 'ed_user') {

        $dataarr = array(
            'last_name' => $_POST['mf_lname'],
            'first_name' => $_POST['mf_fname'],
            'position' => $_POST['mf_pos'],
            'phone' => $_POST['mf_phone'],
            'gropid' => $_POST['mf_main_group']
        );

        ts_UpdUser($_POST['mf_uid'], $dataarr);

        $wpdb->query("DELETE FROM `tm_worktime_usergrop_p` WHERE `tm_worktime_usergrop_p`.`userid` = ".$_POST['mf_uid'].";");
        foreach ($_POST['mf_sec_group'] as $itm)
        {
            ts_AddUserInGroup($itm, $_POST['mf_uid']);

        }

    }



    if(!isset($_GET['msys'])) {
        $dout .= '<br><br><br><br><a href="?tab=empl&msys=add">Добавить сотрудника</a><br><br><table border="1" cellpadding="1" cellspacing="1" style="width:100%">
	<tbody>
		<tr>
			<td style="width: 20px;">№</td>
			<td>Фамилия Имя</td>
			<td>должность</td>
			<td>телефон</td>
			<td>Основная рабочая группа</td>
			<td>Группы</td>
			<td></td>
		</tr>';

        $usr_arr = ts_GetUsers();
        $i = 1;
        foreach ($usr_arr as $this_user) {
            $groups_usr = '';
            $get_maingroup = ts_GetGroup($this_user['gropid']);

            $get_groups = ts_GetUsersFull($this_user['id']);

            foreach ($get_groups[0]['groups'] as $this_group) {



                    $groups_usr .= $this_group['group_name'] . '<br>';
                if ($this_group['id'] != $this_user['gropid']) {
                }


            }


            $dout .= '
		<tr>
			<td>' . $i . '</td>
			<td><a href="?tab=empl&msys=edit&uid='.$this_user['id'].'">' . $this_user['last_name'] . ' ' . $this_user['first_name'] .'</a></td>
			<td>' . $this_user['position'] . '</td>
			<td>' . $this_user['phone'] . '</td>
			<td style="background-color: ' . $get_maingroup['color'] . ';">' . $get_maingroup['group_name'] . '</td>
			<td>' . $groups_usr . '</td>
			<td><a href="?tab=empl&delid='.$this_user['id'].'">Удалить</a></td>
		</tr>';


            $i++;
        }


        $dout .= '	</tbody>
</table>';

    } elseif ($_GET['msys'] == 'add') {

        $grouplist = ts_GetGroups();
        foreach ($grouplist as $this_gr) {

            $maingr .= '<option value="'.$this_gr['id'].'" >'.$this_gr['group_name'].'</option>';
            $secgr .= '<label><input type="checkbox" name="mf_sec_group[]" value="'.$this_gr['id'].'">'.$this_gr['group_name'].'</label><Br>';
        }



        $dout .= '<br><br><br><div class="wpb_wrapper"><form action="?tab=empl" id="mf_addusr" method="POST" >
<input name="mf_syst" value="add_user"  type="hidden">
		<div class="vc_row wpb_row vc_inner">
			<fieldset class="vc_col-sm-15 wpb_column vc_column_container">
				<div class="block">
				<label>Имя: </label>
					<input class="text_input hint" name="mf_fname" value="" placeholder="" type="text">
									<label>Фамилия: </label>
					<input class="text_input hint" name="mf_lname" value="" placeholder="" type="text">
				</div>
								<br><div class="block">
				<label>должность: </label>
					<input class="text_input hint" name="mf_pos" value="" placeholder="" type="text">
									<label>телефон: </label>
					<input class="text_input hint" name="mf_phone" value="" placeholder="" type="text">
				</div>


				<br>
				<div class="block">
					     <label>Основная группа</label>
             <select id="hour" name="mf_main_group" class="dropdnw">
               <option value="" selected>Нет</option>
               '.$maingr.'
             </select>
				</div><br>
			
				</div>
			</fieldset>
			<fieldset class="vc_col-sm-15 wpb_column vc_column_container">
			<div class="block">
			<label>Дополнительные группы:</label><br><br>
			    '.$secgr.'
			    <br><br>
			    </div>
			</fieldset>
    
		</div><br><br>
		<button class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-vista-blue vc_cta3-icon-size-md " style="width: 100%;">Добавить сотрудника</button>

	</form></div>';



    } elseif ($_GET['msys'] == 'edit') {

        $this_user = ts_GetUser($_GET['uid']);

        $active_groups = ts_GetUsersFull($this_user['id']);
        foreach ($active_groups[0]['groups'] as $actgr) {
            $usrgr[] = $actgr['id'];

        }

        $grouplist = ts_GetGroups();
        foreach ($grouplist as $this_gr) {
            if($this_gr['id'] == $this_user['gropid'])
            {
                $bo = 'selected';
            } else {
                $bo = '';
            }

            if(in_array($this_gr['id'], $usrgr))
            {
                $ba = 'checked';
            } else {
                $ba = '';
            }

            $maingr .= '<option value="'.$this_gr['id'].'" '.$bo.'>'.$this_gr['group_name'].'</option>';
            $secgr .= '<label><input type="checkbox" name="mf_sec_group[]" value="'.$this_gr['id'].'" '.$ba.'>'.$this_gr['group_name'].'</label><Br>';
        }


        $dout .= '<br><br><br><div class="wpb_wrapper"><form action="?tab=empl" id="mf_edusr" method="POST" >
<input name="mf_syst" value="ed_user"  type="hidden">
<input name="mf_uid" value="'.$_GET['uid'].'"  type="hidden">
		<div class="vc_row wpb_row vc_inner">
			<fieldset class="vc_col-sm-15 wpb_column vc_column_container">
				<div class="block">
				<label>Имя: </label>
					<input class="text_input hint" name="mf_fname" value="'.$this_user['first_name'].'" placeholder="" type="text">
									<label>Фамилия: </label>
					<input class="text_input hint" name="mf_lname" value="'.$this_user['last_name'].'" placeholder="" type="text">
				</div>
								<br><div class="block">
				<label>должность: </label>
					<input class="text_input hint" name="mf_pos" value="'.$this_user['position'].'" placeholder="" type="text">
									<label>телефон: </label>
					<input class="text_input hint" name="mf_phone" value="'.$this_user['phone'].'" placeholder="" type="text">
				</div>


				<br>
				<div class="block">
					     <label>Основная группа</label>
             <select id="hour" name="mf_main_group" class="dropdnw">
               <option value="" selected>Нет</option>
               '.$maingr.'
             </select>
				</div><br>
			
				</div>
			</fieldset>
			<fieldset class="vc_col-sm-15 wpb_column vc_column_container">
			<div class="block">
			<label>Группы:</label><br><br>
			    '.$secgr.'
			    <br><br>
			    </div>
			</fieldset>
    
		</div><br><br>
		<button class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-vista-blue vc_cta3-icon-size-md " style="width: 100%;">Изменить данные сотрудника</button>

	</form></div>';

    }

} else {
    $menu = '<a href="?tab=empl">Сотрудники</a> | Группы';



if(!isset($_GET['msys']))
{
    if($_POST['mf_syst'] == 'add_group')
    {

        if($_POST['mf_gname'] != '')
        {
            ts_AddGroup($_POST['mf_gname'], $_POST['mf_admin'], $_POST['mf_color'], $_POST['mf_sec_group']);
        } else {
            $dout = '<br><br>Ошибка: Название группы пустое<br><br>';
        }
    }
    if($_POST['mf_syst'] == 'ed_group')
    {
        if($_POST['mf_gname'] != '')
        {

            $upd_group = array(
                'group_name' => $_POST['mf_gname'],
                'groupadm' => $_POST['mf_sec_group'],
                'group_adminid' => $_POST['mf_admin'],
                'color' => $_POST['mf_color']

            );

            ts_UpdGroup($_POST['mf_gid'], $upd_group);
        } else {
            $dout = '<br><br>Ошибка: Название группы пустое<br><br>';
        }
    }

    if(isset($_GET['delid']) and $_GET['delid'] != '' )
    {

        ts_DelGroup($_GET['delid']);
    }








    $dout .=  '<br><br><br><br><a href="?tab=groups&msys=add">Добавить группу</a><br><br><table border="1" cellpadding="1" cellspacing="1" style="width:100%">
	<tbody>
		<tr>
			<td style="width: 20px;">№</td>
			<td>Название группы</td>
			<td>Ответственный</td>
			<td></td>
		</tr>';
    $group_arr = ts_GetGroups();
    $i=1;
    foreach ($group_arr as $group) {

        $otv = get_user_meta($group['group_adminid']);

    $dout .= '		<tr>
			<td style="background-color: '.$group['color'].'">'.$i.'</td>
			<td><a href="?tab=groups&msys=edit&gid='.$group['id'].'">'.$group['group_name'].'</a></td>
			<td>'.$otv['last_name'][0].' '.$otv['first_name'][0].'</td>
			<td><a href="?tab=groups&delid='.$group['id'].'">Удалить</a></td>
		</tr>';
        $i++;
    }

        $dout .= '	</tbody>
</table>

';
} elseif ($_GET['msys'] == 'add') {

    $users = get_users( array( 'fields' => array( 'ID' ), 'role' => 'prorab' ) );
    foreach($users as $user_id) {

        $this_user = get_user_meta($user_id->ID);
        $empl_list .= '<option value="'.$user_id->ID.'" >'.$this_user['last_name'][0].' '.$this_user['first_name'][0].'</option>';
        $secgr .= '<label><input type="checkbox" name="mf_sec_group[]" value="'.$user_id->ID.'" '.$ba.'>'.$this_user['last_name'][0].' '.$this_user['first_name'][0].'</label><Br>';

    }



    $dout .= '<br><br><br><div class="wpb_wrapper"><form action="?tab=groups" id="mf_addgroup" method="POST" >
<input name="mf_syst" value="add_group"  type="hidden">
		<div class="vc_row wpb_row vc_inner">
			<fieldset class="vc_col-sm-6 wpb_column vc_column_container">
				<div class="block">
				<label>Название группы: </label>
					<input class="text_input hint" name="mf_gname" value="" placeholder="" type="text">
				</div>
				<br>
				<div class="block">
					     <label>Цвет группы</label>
             <select id="hour" name="mf_color" class="dropdnw">
               <option value="" selected>Нет</option>
               <option value="Azure" style="background-color: Azure;" >Azure</option>
               <option value="Beige" style="background-color: Beige;" >Beige</option>
               <option value="LightGreen " style="background-color: LightGreen;" >LightGreen</option>
               <option value="LightPink" style="background-color: LightPink;" >LightPink</option>
               <option value="LightCyan" style="background-color: LightCyan;" >LightCyan</option>
               <option value="LightBlue" style="background-color: LightBlue;" >LightBlue</option>
               <option value="LightSalmon" style="background-color: LightSalmon;" >LightSalmon</option>
               <option value="LightYellow" style="background-color: LightYellow;" >LightYellow</option>
               <option value="MistyRose" style="background-color: MistyRose;" >MistyRose</option>
               <option value="PaleTurquoise" style="background-color: PaleTurquoise;" >PaleTurquoise</option>
               <option value="SeaShell" style="background-color: SeaShell;" >SeaShell</option>
               <option value="Plum" style="background-color: Plum;" >Plum</option>
               <option value="MintCream" style="background-color: MintCream;" >MintCream</option>
               <option value="MediumAquaMarine" style="background-color: MediumAquaMarine;" >MediumAquaMarine</option>

             </select>
				</div><br>
				<div class="block">
					     <label>Ответственный</label>
             <select id="hour" name="mf_admin" class="dropdnw">
             <option value="" selected>Нет</option>
               '.$empl_list.'
             </select>
				</div>
				
			</fieldset>
			<fieldset class="vc_col-sm-15 wpb_column vc_column_container" style="margin-top: 15px;">
			<div class="block">
			<label>Дополнительные ответственные:</label><br><br>
			    '.$secgr.'
			    <br><br>
			    </div>
			</fieldset>
		</div><br><br>
		<button class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-vista-blue vc_cta3-icon-size-md " style="width: 100%;">Создать группу</button>

	</form></div>';

} elseif ($_GET['msys'] == 'edit') {
        $group_this = ts_GetGroup($_GET['gid']);

    $users = get_users( array( 'fields' => array( 'ID' ), 'role' => 'prorab' ) );
    foreach($users as $user_id) {

        $this_user = get_user_meta($user_id->ID);
        if($group_this['group_adminid'] == $user_id->ID)
        {
            $empl_list .= '<option value="' . $user_id->ID . '" selected>' . $this_user['last_name'][0] . ' ' . $this_user['first_name'][0] . '</option>';
            $empl_found = true;
        } else {
            $empl_list .= '<option value="' . $user_id->ID . '" >' . $this_user['last_name'][0] . ' ' . $this_user['first_name'][0] . '</option>';
        }

        if(ts_CheckAdmin($user_id->ID, $group_this['id']) == true)
        {
            $secgr .= '<label><input type="checkbox" name="mf_sec_group[]" value="'.$user_id->ID.'" checked>'.$this_user['last_name'][0].' '.$this_user['first_name'][0].'</label><Br>';
        } else {
            $secgr .= '<label><input type="checkbox" name="mf_sec_group[]" value="'.$user_id->ID.'" >'.$this_user['last_name'][0].' '.$this_user['first_name'][0].'</label><Br>';
        }

    }

    if(!$empl_found)
    {
        $bo = 'selected';
    }

    $color_arr = array(
      'Нет', 'Azure', 'Beige', 'LightGreen', 'LightPink', 'LightCyan', 'LightBlue', 'LightSalmon', 'LightYellow', 'MistyRose', 'PaleTurquoise', 'SeaShell', 'Plum', 'MintCream', 'MediumAquaMarine'
    );

    foreach($color_arr as $this_color) {
        if($this_color == $group_this['color'])
        {
            $bz = 'selected';
            $bz_c = $group_this['color'];
        } else {
            $bz = '';
        }

        if ($this_color != 'Нет') {
            $col_out .= '<option value="'.$this_color.'" style="background-color: '.$this_color.';" '.$bz.'>'.$this_color.'</option>';
        } else {
            $col_out .= '<option value="" style="background-color: white;" '.$bz.'>Нет</option>';
        }
    }

    $dout .= '<br><br><br><div class="wpb_wrapper"><form action="?tab=groups" id="mf_addgroup" method="POST" >
<input name="mf_syst" value="ed_group"  type="hidden">
<input name="mf_gid" value="'.$group_this['id'].'"  type="hidden">
		<div class="vc_row wpb_row vc_inner">
			<fieldset class="vc_col-sm-6 wpb_column vc_column_container">
				<div class="block">
				<label>Название группы: </label>
					<input class="text_input hint" name="mf_gname" value="'.$group_this['group_name'].'" placeholder="" type="text">
				</div>
				<br>
				<div class="block">
					     <label>Цвет группы</label>
             <select id="hour" name="mf_color" class="dropdnw" style="background-color: '.$bz_c.';">
              '.$col_out.'

             </select>
				</div><br>
				<div class="block">
					     <label>Ответственный</label>
             <select id="hour" name="mf_admin" class="dropdnw">
             <option value="" '.$bo.'>Нет</option>
               '.$empl_list.'
             </select>
				</div>
			</fieldset>
			<fieldset class="vc_col-sm-15 wpb_column vc_column_container" style="margin-top: 15px;">
			<div class="block">
			<label>Дополнительные ответственные:</label><br><br>
			    '.$secgr.'
			    <br><br>
			    </div>
			</fieldset>
		</div><br><br>
		</div><br><br>
		<button class="vc_general vc_cta3 vc_cta3-style-flat vc_cta3-shape-rounded vc_cta3-align-left vc_cta3-color-vista-blue vc_cta3-icon-size-md " style="width: 100%;">Изменить группу</button>

	</form></div>';
}
}

    $mout = $menu.''.$dout;
    return $mout;
}


?>
