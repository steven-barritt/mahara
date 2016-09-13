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
$eventid = param_integer('event');
$sort = param_variable('sort', 'firstname');
$direction = param_variable('direction', 'asc');
$returnto = param_variable('returnto', 'index');
$fillall = param_boolean('fillall', false);

$membership = group_user_access($groupid);

if (!$membership && !$group->public) {
    throw new GroupAccessDeniedException(get_string('cantviewforums', 'interaction.schedul'));
}

global $USER;
$role = group_user_access($group->id);
if(!group_role_can_moderate_views($group->id, $role)){
    throw new GroupAccessDeniedException(get_string('cantviewattendance', 'interaction.schedule'));
}


define('TITLE', $group->name . ' - ' . get_string('nameplural', 'interaction.schedule'));

$attendance = param_integer('attendance',null);
$userid = param_integer('userid',null);


if($attendance && $userid){
	schedule_update_attendance($eventid,$userid,$attendance);
}



//if there is only one event which there should be for now then go straight to it.
$event = schedule_get_event($eventid);
if($event){
	$event = $event[0];
}
$groupmembers = group_get_member_ids($groupid, array('member'));

if($fillall){
	foreach($groupmembers as $member){
		if(!$attendance = schedule_get_event_attendance($eventid,$member)){
			schedule_update_attendance($eventid,$member,1);
		}
	}
}

//TODO: in here we need to do the same as with the report get all subgroup members if necessary
//TODO: we will also need to implement that in the functions that retrieve the schedules and events
//$attendanceevents = schedule_get_attendance_events($scheduleid);

//TODO: work out the longest title for an event and then make the column that high.
//$columnheight = '100px';



//build the userdata with their attendance as an array at the end
$columns = array();
$sortable = array();
$userdata = array();
foreach($groupmembers as $member){
	$user = get_user_for_display($member);
	$attendances = array();
	$attendance = schedule_get_event_attendance($eventid,$member);
	$thisuser = array('id'=>$member,
		'firstname'=>$user->firstname,
		'lastname'=>$user->lastname,
		'profileicon'=>$user->profileicon,
		'studentnumber'=>$user->studentid,
		'attendance'=>$attendance[0]);
	$userdata[] = $thisuser;
}



$sortable[] = 'firstname';
$sortable[] = 'lastname';
if (in_array($sort, $sortable)) {
	sorttablebycolumn($userdata, $sort, $direction);
}


$javascript = <<<EOF

function changeattendance(link) {
	var href = getNodeAttribute(link,'href');
	var userid = href.substr(href.indexOf('&userid=')+8,href.indexOf('&',href.indexOf('&userid=')+1)-(href.indexOf('&userid=')+8));
	var eventid = href.substr(href.indexOf('event=')+6,href.indexOf('&',href.indexOf('event=')+1)-(href.indexOf('event=')+6));
	var attendance = parseInt(href.substr(href.indexOf('attendance=')+11,href.indexOf('&',href.indexOf('attendance=')+1)-(href.indexOf('attendance=')+11)));
//todo: do json callback here

    sendjsonrequest('attendance.json.php',
        {'event': eventid, 'user': userid, 'attendance':attendance},
        'GET',
        function(data) {
        	var userid = data['data'];
			removeElementClass('present_'+userid,'present');
			addElementClass(getFirstElementByTagAndClassName('img',null,'present_'+userid),'greyedout');
			removeElementClass('late_'+userid,'late');
			addElementClass(getFirstElementByTagAndClassName('img',null,'late_'+userid),'greyedout');
			removeElementClass('absent_'+userid,'absent');
			addElementClass(getFirstElementByTagAndClassName('img',null,'absent_'+userid),'greyedout');
			removeElementClass('excused_'+userid,'excused');
			addElementClass(getFirstElementByTagAndClassName('img',null,'excused_'+userid),'greyedout');
			switch(attendance){
				case 1: 
					addElementClass('present_'+userid,'present');
					removeElementClass(getFirstElementByTagAndClassName('img',null,'present_'+userid),'greyedout');
					break;
				case 2: 
					addElementClass('late_'+userid,'late');
					removeElementClass(getFirstElementByTagAndClassName('img',null,'late_'+userid),'greyedout');
					break;
				case 3: 
					addElementClass('absent_'+userid,'absent');
					removeElementClass(getFirstElementByTagAndClassName('img',null,'absent_'+userid),'greyedout');
					break;
				case 4: 
					addElementClass('excused_'+userid,'excused');
					removeElementClass(getFirstElementByTagAndClassName('img',null,'excused_'+userid),'greyedout');
					break;
			}


        },
        function() {
            // @todo error
        }
    );
    return false;


//var bob = getElement('present_'+userid);
//	alert(getNodeAttribute(bob,'class'));
//	alert(getNodeAttribute('present_14','class'));
	//reset all the class attributes

		
}



addLoadEvent(function () {
    forEach(getElementsByTagAndClassName('a', 'attendancelink'), function(link) {
		connect(link, 'onclick', function(e) {
    			e.preventDefault();
    			changeattendance(this)
        });
    });
});

EOF;
//$attendance = array();

//$attendance = schedule_get_attendance($eventid);



$headers = array();


$smarty = smarty(array(), $headers, array(), array());
$smarty->assign('INLINEJAVASCRIPT', $javascript);

$smarty->assign('groupid', $groupid);
$smarty->assign('heading', $group->name);
$smarty->assign('admin', $membership == 'admin');
$smarty->assign('userdata', $userdata);
$smarty->assign('returnto', $returnto);
$smarty->assign('return',$_SERVER['HTTP_REFERER']);
$smarty->assign('groupadmins', group_get_admins(array($groupid)));
$smarty->assign('event',$event);
$smarty->display('interaction:schedule:takeattendance.tpl');
