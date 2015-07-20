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
$events = schedule_get_all_groupevents($group->id);
/*if(group_has_children($group->id)){
	$events = schedule_get_all_groupevents($group->id);
}else{
	if($schedules){
		$events = get_schedule_events($schedules[0]);
	}
}*/

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


$headers = array();


$smarty = smarty(array(), $headers, array(), array());
$smarty->assign('groupid', $groupid);
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading', $group->name);
$smarty->assign('admin', group_user_can_assess_submitted_views($groupid,null));
$smarty->assign('groupadmins', group_get_admins(array($groupid)));
if($schedules){
$smarty->assign('schedule',$schedules[0]);
}
$smarty->assign('events', $events);
$smarty->display('interaction:schedule:index.tpl');
