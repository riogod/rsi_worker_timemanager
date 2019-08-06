<?php


// Добавление сотрудника ts_AddUser
// Удаление сотрудника ts_DelUser
// Редактирование сотрудника ts_UpdUser
// Получение данных сотрудника ts_GetUser
// Получение списка сотрудников ts_GetUsers

// Добавление группы ts_AddGroup
// Удаление группы ts_DelGroup
// Редактирование группы ts_UpdGroup
// Получение данных сотрудника ts_GetGroup
// Получение списка групп ts_GetGroups

// Получение списка сотрудников с активными группами ts_GetUsersFull
// Получение списка сотрудников входящих в группу ts_GetUsersInGroup
// Получение групп куда входит сотрудник ts_GetUserGroups
// Добавления пользователя к группе ts_AddUserInGroup
// Удаление пользователя из группы ts_DelUserInGroup




// Добавление сотрудника ts_AddUser
function ts_AddUser($first_name, $last_name, $position, $phone, $gropid) {
    global $wpdb;

    if(!isset($gropid) or $gropid == '')
    {
        $gropid = ", NULL";
    } else {
        $gropid = ", '".$gropid."'";
    }
    if(!isset($phone) or $phone == '')
    {
        $phone = "NULL";
    } else {
        $phone = "'".$phone."'";
    }

        if($wpdb->query("INSERT INTO `tm_worktime_user` (`first_name`, `last_name`, `position`, `phone`, `gropid`) VALUES ('".$first_name."', '".$last_name."', '".$position."', ".$phone."".$gropid.");"))
        {
            return $wpdb->insert_id;
        } else {
            return false;
        }
}




// Удаление сотрудника ts_DelUser
function ts_DelUser($userid) {
    global $wpdb;

    if($wpdb->query("DELETE FROM `tm_worktime_user` WHERE `tm_worktime_user`.`id` = ".$userid.";"))
    {
        return true;
    } else {
        return false;
    }
}

// Редактирование сотрудника ts_UpdUser
function ts_UpdUser($userid, $dataarr) {
    global $wpdb;

    $i=1;
    foreach ($dataarr as $key => $value) {
        if($value == ""){
            $value = "NULL";
        } else {
            $value = '\''.$value.'\'';

        }

        if (count($dataarr) == $i) {
            $sql_add .= ' `'.$key.'`='.$value.'';
        }
        else {
           $sql_add .= ' `'.$key.'`='.$value.',';
        }
        $i++;
    }



    if($wpdb->query("UPDATE 
                              `tm_worktime_user` 
                            SET 
                              ".$sql_add."
                            WHERE 
                              `tm_worktime_user`.`id` = ".$userid." ;"))
    {
        return true;
    } else {
        return false;
    }

}

// Получение данных сотрудника ts_GetUser
function ts_GetUser($userid)
{
    global $wpdb;
    $get_user = $wpdb->get_row('SELECT * FROM `tm_worktime_user` WHERE `id`='.$userid, ARRAY_A);

return $get_user;
}



// Получение списка сотрудников ts_GetUsers
function ts_GetUsers()
{
    global $wpdb;
    $get_users = $wpdb->get_results('SELECT * FROM `tm_worktime_user` ORDER BY last_name ASC', ARRAY_A);

    return $get_users;
}


// Добавление группы ts_AddGroup
function ts_AddGroup($group_name, $group_admin, $color, $adms) {
    global $wpdb;
    if(!isset($group_admin))
    {  $group_admin = "NULL";  } else { $group_admin = "'".$group_admin."'";}
    if(!isset($color))
    {  $color = "NULL";  } else { $color = "'".$color."'";}

       if($wpdb->query("INSERT INTO `tm_worktime_usergroup` (`group_name`, `group_adminid`, `color`) VALUES ('".$group_name."', ".$group_admin.", ".$color.");"))
    {
        $ins_id = $wpdb->insert_id;

        foreach ($adms as $adm)
        {
            $wpdb->query("INSERT INTO `tm_worktime_admingroup` (`userid`, `groupid`) VALUES ('".$adm."', ".$ins_id.");");
        }

        return true;
    } else {
        return false;
    }
}

// Удаление группы ts_DelGroup
function ts_DelGroup($groupid) {
    global $wpdb;

    if($wpdb->query("DELETE FROM `tm_worktime_usergroup` WHERE `tm_worktime_usergroup`.`id` = ".$groupid.";"))
    {
        $wpdb->query("DELETE FROM `tm_worktime_admingroup` WHERE `tm_worktime_admingroup`.`groupid` = ".$groupid.";");
        return true;
    } else {
        return false;
    }
}
// Редактирование группы ts_UpdGroup
function ts_UpdGroup($groupid, $dataarr) {
    global $wpdb;



    $i=1;
    foreach ($dataarr as $key => $value) {
      if($key != 'groupadm')
      {
        if($value == ""){
            $value = "NULL";
        } else {
            $value = '\''.$value.'\'';
        }

        if (count($dataarr) == $i) {
            $sql_add .= ' `'.$key.'`='.$value.'';
        }
        else {
            $sql_add .= ' `'.$key.'`='.$value.',';
        }

      } else {
          $wpdb->query("DELETE FROM `tm_worktime_admingroup` WHERE `tm_worktime_admingroup`.`groupid` = ".$groupid.";");
          foreach ($value as $adm)
          {
              $wpdb->query("INSERT INTO `tm_worktime_admingroup` (`userid`, `groupid`) VALUES ('".$adm."', ".$groupid.");");
          }
      }


        $i++;

    }


    if($wpdb->query("UPDATE 
                              `tm_worktime_usergroup` 
                            SET 
                              ".$sql_add."
                            WHERE 
                              `tm_worktime_usergroup`.`id` = ".$groupid." ;"))
    {


        return true;
    } else {
        return false;
    }

}
function ts_CheckAdmin($adminid, $groupid)
{

    global $wpdb;
    $get_groups = $wpdb->get_row('SELECT * FROM `tm_worktime_admingroup` WHERE `userid`=' . $adminid . ' AND `groupid`=' . $groupid, ARRAY_A);

    if (count($get_groups) > 0)
    {

      return true;
    } else {

        return false;
    }

}

function ts_GetSubGroup($adminid)
{

    global $wpdb;
    $get_groups = $wpdb->get_results('SELECT * FROM `tm_worktime_admingroup` WHERE `userid`=' . $adminid, ARRAY_A);


    return $get_groups;

}


// Получение данных группы ts_GetGroup
function ts_GetGroup($groupid) {

    global $wpdb;
    $get_groups = $wpdb->get_row('SELECT * FROM `tm_worktime_usergroup` WHERE `id`='.$groupid, ARRAY_A);

    return $get_groups;
}

// Получение списка групп ts_GetGroups
function ts_GetGroups()
{
    global $wpdb;
    $get_groups = $wpdb->get_results('SELECT * FROM `tm_worktime_usergroup` ORDER BY group_name ASC', ARRAY_A);

    return $get_groups;
}

// Добавления пользователя к группе ts_AddUserInGroup
function ts_AddUserInGroup($groupid, $userid) {
    global $wpdb;
    $get_rows = $wpdb->get_row('SELECT * FROM `tm_worktime_usergrop_p` WHERE `userid`='.$userid.' AND `primid`='.$groupid, ARRAY_A);
    if(count($get_rows) == 0)
    {
    if($wpdb->query("INSERT INTO `tm_worktime_usergrop_p` (`userid`, `primid`) VALUES ('".$userid."', '".$groupid."');"))
    {
        return true;
    } else {
        return false;
    }
    } else {
        return false;
    }
}

// Удаление пользователя из группы ts_DelUserInGroup
function ts_DelUserInGroup($groupid, $userid) {
    global $wpdb;

    if($wpdb->query("DELETE FROM `tm_worktime_usergrop_p` WHERE `tm_worktime_usergrop_p`.`userid` = ".$userid." AND `tm_worktime_usergrop_p`.`primid` = ".$groupid.";"))
    {
        return true;
    } else {
        return false;
    }
}



// Получение списка сотрудников с активными группами ts_GetUsersFull
function ts_GetUsersFull($uid="none")
{
    global $wpdb;

    if($uid != "none")
    {
        $where = "WHERE `id`=".$uid." ";
    }
    $get_users = $wpdb->get_results('SELECT * FROM `tm_worktime_user` '.$where.'ORDER BY last_name ASC', ARRAY_A);

    foreach ($get_users as $user) {


        $get_allowgroups = $wpdb->get_results('SELECT `tm_worktime_usergrop_p`.*, `tm_worktime_usergroup`.*  FROM `tm_worktime_usergrop_p` INNER JOIN `tm_worktime_usergroup` WHERE `tm_worktime_usergrop_p`.userid='.$user['id'].' AND `tm_worktime_usergroup`.id = `tm_worktime_usergrop_p`.`primid` ORDER BY `tm_worktime_usergroup`.group_name ASC', ARRAY_A);
        $userlist[] = array(
            'user' => $user,
            'groups' => $get_allowgroups
        );

    }

    return $userlist;
}



// Получение списка сотрудников входящих в группу ts_GetUsersInGroup
function ts_GetUsersInGroup($gid="none")
{
    global $wpdb;

    if($gid != "none")
    {
        $where = "WHERE `id`=".$gid." ";
    }
    $get_groups = $wpdb->get_results('SELECT * FROM `tm_worktime_usergroup` '.$where.'ORDER BY group_name ASC', ARRAY_A);




    foreach ($get_groups as $group) {


        $get_allowgroups = $wpdb->get_results('SELECT `tm_worktime_user`.*  FROM `tm_worktime_user` JOIN `tm_worktime_usergrop_p`  WHERE `tm_worktime_usergrop_p`.userid=`tm_worktime_user`.id AND `tm_worktime_usergrop_p`.primid='.$group['id'].' ORDER BY `tm_worktime_user`.last_name ASC', ARRAY_A);
        $grouplist[] = array(
            'group' => $group,
            'users' => $get_allowgroups
        );

    }


    return $grouplist;
}



function my_has_role($user, $role) {
    $roles = $user->roles;
    return in_array($role, (array) $user->roles);
}







?>
