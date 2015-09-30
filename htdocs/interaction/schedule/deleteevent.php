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

define('INTERNAL', 1);
define('MENUITEM', 'groups/schedule');
define('SECTION_PLUGINTYPE', 'interaction');
define('SECTION_PLUGINNAME', 'schedule');
define('SECTION_PAGE', 'deleteevent');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('interaction' ,'schedule');
require_once('group.php');
require_once('pieforms/pieform.php');
require_once(get_config('docroot') . 'interaction/lib.php');

$eventid = param_integer('id');
$view = param_integer('view',0);
$month = param_integer('month',0);
$year = param_integer('year',0);

$returnto = param_integer('returnto', 0);
if ($returnto) {
	$return = '/interaction/schedule/index.php?group=' . $returnto.'&view='.$view;
	if($month && $year){
		$return .= '&month='.$month.'&year='.$year;
	}
}
else {
	$return = '/interaction/schedule/index.php?group=' . $schedule->groupid.'&view='.$view;
}

$event = get_record_sql(
	'SELECT e.schedule, e.title, e.id AS eventid, e.description,'. db_format_tsfield('e.startdate','startdate'). ', '. db_format_tsfield('e.enddate','enddate').', e.location, e.attendance
	FROM {interaction_schedule_event} e
	INNER JOIN {interaction_instance} s ON (s.id = e.schedule AND s.deleted != 1)
	WHERE e.id = ?',
	array($eventid)
);
$scheduleid = $event->schedule;

$schedule = get_record_sql(
    'SELECT s.id, s.group AS groupid, s.title, g.name AS groupname, g.grouptype
    FROM {interaction_instance} s
    INNER JOIN {group} g ON (g.id = s.group AND g.deleted = 0)
    WHERE s.id = ?
    AND s.deleted != 1',
    array($scheduleid)
);

if (!$event) {
	throw new NotFoundException(get_string('cantfindevent', 'interaction.schedule', $eventid));
}

if (!$schedule) {
    throw new NotFoundException(get_string('cantfindschedule', 'interaction.schedule', $scheduleid));
}

define('GROUP', $schedule->groupid);


define('TITLE', $event->title . ' - ' . get_string('deleteeventspecific', 'interaction.schedule', $event->title));

$form = pieform(array(
    'name'     => 'deleteevent',
    'renderer' => 'div',
    'autofocus' => false,
    'elements' => array(
        'title' => array(
            'value' => get_string('deleteeventsure', 'interaction.schedule'),
        ),
        'notify' => array(
        	'type' => 'checkbox',
            'title' => get_string('deletenotify', 'interaction.schedule'),
            'defaultvalue' => true,
        ),
        'submit' => array(
            'type'  => 'submitcancel',
            'value' => array(get_string('yes'), get_string('no')),
            'goto'  => get_config('wwwroot') . $return,
        ),
        'schedule' => array(
            'type' => 'hidden',
            'value' => $scheduleid
        ),
        'view' => array(
            'type' => 'hidden',
            'value' => $view
        )
    )
));

function deleteevent_submit(Pieform $form, $values) {
    global $SESSION, $USER, $eventid, $schedule;
    // mark event as deleted
	$view = param_integer('view',0);
	$month = param_integer('month',0);
	$year = param_integer('year',0);

	$returnto = param_integer('returnto', 0);
	if ($returnto) {
		$return = '/interaction/schedule/index.php?group=' . $returnto.'&view='.$view;
		if($month && $year){
			$return .= '&month='.$month.'&year='.$year;
		}
	}
	else {
		$return = '/interaction/schedule/index.php?group=' . $schedule->groupid.'&view='.$view;
	}
    update_record(
        'interaction_schedule_event',
        array('deleted' => 1),
        array('id' => $eventid)
    );
    $SESSION->add_ok_msg(get_string('deletetopicsuccess', 'interaction.forum'));
	redirect($return);
}

$smarty = smarty();
$smarty->assign('schedule', $event->title);
$smarty->assign('subheading', TITLE);
$smarty->assign('event', $event);
$smarty->assign('groupadmins', group_get_admin_ids($schedule->groupid));
$smarty->assign('deleteform', $form);
$smarty->display('interaction:schedule:deleteevent.tpl');
