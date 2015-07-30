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
define('MENUITEM', 'groups/schedule');
define('SECTION_PLUGINTYPE', 'interaction');
define('SECTION_PLUGINNAME', 'schedule');
define('SECTION_PAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('group.php');
safe_require('interaction', 'schedule');
require_once('pieforms/pieform.php');
require_once(get_config('docroot') . 'interaction/lib.php');

$groupid = param_integer('group');


$this_month = date('n',time());
$this_year = date('Y',time());


//different views 
//0 = schedule view - the default
//1 = year planner view
//2 = calendar view
$view = param_integer('view',0);

$month = param_integer('month',$this_month);
$year = param_integer('year',$this_year);
if($month < 1){
	$month = 12;
	$year--;
}
if($month >12){
	$month = 1;
	$year++;
}


define('GROUP', $groupid);
$group = group_current_group();
$membership = group_user_access($groupid);

if (!$membership && !$group->public) {
    throw new GroupAccessDeniedException(get_string('cantviewforums', 'interaction.schedul'));
}


global $USER;
$role = group_user_access($group->id);

define('TITLE', $group->name . ' - ' . get_string('nameplural', 'interaction.schedule'));


//if there is only one schedule which there should be for now then go straight to it.

$schedules = get_schedule_list($group->id, $USER->get('id'));


$events = array();
$startdate = null;
$enddate = null;
//calendar works slightly differently
if($view == 2){
	list($startdate,$enddate) = schedule_get_start_and_enddates($month,$year,6);
}else{
	$startdate = schedule_get_groupstartdate($groupid);
	$enddate = schedule_get_groupenddate($groupid);
	if($startdate){
		$startdate = $startdate[0]->startdate;
	}else{
		$startdate = null;
	}
	if($enddate){
		$enddate = $enddate[0]->enddate;
	}else{
		$enddate = null;
	}
}
//var_dump($startdate);
//var_dump($enddate);
$events = schedule_get_all_groupevents($group->id,$startdate,$enddate);

$javascript = <<<EOF

addLoadEvent(function () {
    forEach(getElementsByTagAndClassName('a', 'event_title'), function(link) {
		connect(link, 'onclick', function(e) {
    			e.preventDefault();
    			var details = getFirstElementByTagAndClassName('div', 'detail', this.parentNode.parentNode);
    			toggleElementClass("hidden",details);
        });
    });
});

EOF;
$table = '';
$smarty = smarty(array(), array(), array(),array());
$smarty->assign('admin', group_user_can_assess_submitted_views($groupid,null));
if($schedules){
	$smarty->assign('schedule',$schedules[0]);
}
$smarty->assign('events', $events);
$smarty->assign('view',$view);
if($view == 2){
	$weeksanddays = schedule_events_per_cal_day($events, $groupid,$month,$year);
	$smarty->assign('weeksanddays',$weeksanddays);
	$smarty->assign('groupid', $groupid);
	$smarty->assign('month',$month);
	$smarty->assign('year',$year);
	$table = $smarty->fetch('interaction:schedule:calendarview.tpl');
}elseif($view == 1){
	$weeksanddays = schedule_events_per_day($events,$groupid);
	$smarty->assign('weeksanddays',$weeksanddays);
	$table = $smarty->fetch('interaction:schedule:yearplannerview.tpl');
}else{
	$table = $smarty->fetch('interaction:schedule:scheduleview.tpl');
}

$headers = array();
$sidebars = array();
//we want the whole screen for the yearplanner view
if($view == 1){
	$sidebars['sidebars'] =  false;
}
$smarty = smarty(array(), $headers, array(),$sidebars);

$smarty->assign('groupid', $groupid);
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading', $group->name);
$smarty->assign('view',$view);
$smarty->assign('admin', group_user_can_assess_submitted_views($groupid,null));
$smarty->assign('groupadmins', group_get_admins(array($groupid)));
if($schedules){
$smarty->assign('schedule',$schedules[0]);
}
$smarty->assign('events', $events);
$smarty->assign('table', $table);
$smarty->display('interaction:schedule:index.tpl');
