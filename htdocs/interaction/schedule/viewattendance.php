<?php
/**
 *
 * @package    mahara
 * @subpackage interaction-forum
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('PUBLIC', 1);
define('INTERNAL', 1);
define('MENUITEM', 'groups/attendance');
define('SECTION_PLUGINTYPE', 'interaction');
define('SECTION_PLUGINNAME', 'schedule');
define('SECTION_PAGE', 'attendance');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('group.php');
require_once('user.php');
safe_require('interaction', 'schedule');
require_once('pieforms/pieform.php');
require_once(get_config('docroot') . 'interaction/lib.php');

$groupid = param_integer('group');
define('GROUP', $groupid);
$group = group_current_group();

$schedules = get_schedule_list($groupid);
$scheduleid = $schedules[0]->id;

//$scheduleid = param_integer('schedule');
//$schedules = schedule_get_schedule($scheduleid);
$sort = param_variable('sort', 'firstname');
$direction = param_variable('direction', 'asc');

$membership = group_user_access($groupid);

if (!$membership && !$group->public) {
    throw new GroupAccessDeniedException(get_string('cantviewattendance', 'interaction.schedule'));
}
global $USER;
$role = group_user_access($group->id);
if(!group_role_can_moderate_views($group->id, $role)){
    throw new GroupAccessDeniedException(get_string('cantviewattendance', 'interaction.schedule'));
}

define('TITLE', $group->name . ' - ' . get_string('nameplural', 'interaction.schedule'));


//if there is only one schedule which there should be for now then go straight to it.

$groupmembers = group_get_member_ids_inc_subgroups($groupid, array('member'));
//TODO: in here we need to do the same as with the report get all subgroup members if necessary
//TODO: we will also need to implement that in the functions that retrieve the schedules and events

$attendanceevents = array();
if(in_array($group->grouptype, array('project','assessment'))) {
	$attendanceevents = schedule_get_group_attendance_events($groupid);
}else{
	$attendanceevents = schedule_get_group_attendance_dates($groupid);
}

//TODO: work out the longest title for an event and then make the column that high.
$longesttitle = 0;
foreach($attendanceevents as $event){
	if(strlen($event->title) > $longesttitle){
		$longesttitle = strlen($event->title);
	}
}
//var_dump($longesttitle);
$longesttitle += 8; //extra characters added before
$longesttitle = $longesttitle * 7; //don't know why 7 works, font size is 12px
$columnheight = $longesttitle.'px';



//build the userdata with their attendance as an array at the end
$columns = array();
$sortable = array();
$userdata = array();
/*	$assessments = array();
	$name = display_default_name(22);
get_assessments(22,$subgroups,$assessments);
	$userdata[] = array('id'=>22,'name'=>$name,'assessments'=>$assessments);*/
foreach($groupmembers as $member){
	$user = get_user_for_display($member);
	$attendances = array();
//	var_dump($member);
	list($attendances,$percentages) = schedule_get_user_group_attendance($member,$groupid);
//	$attendances = schedule_get_group_attendance($groupid,$member);
	$thisuser = array('id'=>$member,'firstname'=>$user->firstname,'lastname'=>$user->lastname,'profileicon'=>$user->profileicon,'studentnumber'=>$user->studentid);
/*	$i = 1;
	if(!$columns){
		foreach($attendances as $attendance){
			$columns[$i] = $attendance[0];
			$sortable[] = $i;
			$i++;
		}
	}*/
	$thisuser['attendances'] = $attendances;
	$thisuser['percentages'] = $percentages;
	$i = 1;
	foreach($attendances as $attendance){
		$thisuser[$i] = $attendance;
		$i++;
	}
//		$userdata[] = array('id'=>$member,'firstname'=>$user->firstname,'lastname'=>$user->lastname,'profileicon'=>$user->profileicon,'assessments'=>$assessments);
	$userdata[] = $thisuser;
//var_dump($userdata);
//var_dump($userdata[0][1]);
}
//	var_dump($userdata);
//bob::bob();


$nocols = count($attendanceevents);

$sortable[] = 'firstname';
$sortable[] = 'lastname';
if (in_array($sort, $sortable)) {
	sorttablebycolumn($userdata, $sort, $direction);
}


//$attendance = array();

//$attendance = schedule_get_attendance($eventid);



$headers = array();


$smarty = smarty(array(), $headers, array(), array());
$smarty->assign('groupid', $groupid);
$smarty->assign('heading', $group->name);
$smarty->assign('admin', $membership == 'admin');
$smarty->assign('columnheight', $columnheight);
$smarty->assign('colcount', $nocols);
$smarty->assign('userdata', $userdata);
$smarty->assign('groupadmins', group_get_admins(array($groupid)));
if($schedules){
$smarty->assign('schedule',$schedules[0]);
}
$smarty->assign('attendnaceevents', $attendanceevents);
if(in_array($group->grouptype, array('project','assessment'))) {
	$smarty->display('interaction:schedule:viewattendance.tpl');
}else{
	$smarty->display('interaction:schedule:viewgroupattendance.tpl');
}

