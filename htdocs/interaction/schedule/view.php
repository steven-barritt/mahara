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
define('SECTION_PAGE', 'editevent');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('interaction', 'schedule');
require_once('group.php');
require_once(get_config('docroot') . 'interaction/lib.php');
require_once('pieforms/pieform.php');

$userid = $USER->get('id');
$eventid = param_integer('event', null);
$returnto = param_integer('returnto', 0);

if (isset($eventid)) {
    $event = get_record_sql(
        'SELECT e.schedule, e.title, e.id AS eventid, e.description,'. db_format_tsfield('e.startdate','startdate'). ', '. db_format_tsfield('e.enddate','enddate').', e.location, e.attendance
        FROM {interaction_schedule_event} e
        INNER JOIN {interaction_instance} s ON (s.id = e.schedule AND s.deleted != 1)
        WHERE e.id = ?',
        array($eventid)
    );
    $scheduleid = $event->schedule;

    if (!$event) {
        throw new NotFoundException(get_string('cantfindevent', 'interaction.schedule', $eventid));
    }
}else{
        throw new NotFoundException(get_string('cantfindevent', 'interaction.schedule', $eventid));
}

$schedule = get_record_sql(
    'SELECT s.id, s.group AS groupid, s.title, g.name AS groupname, g.grouptype
    FROM {interaction_instance} s
    INNER JOIN {group} g ON (g.id = s.group AND g.deleted = 0)
    WHERE s.id = ?
    AND s.deleted != 1',
    array($scheduleid)
);

if (!$schedule) {
    throw new NotFoundException(get_string('cantfindschedule', 'interaction.schedule', $scheduleid));
}

$scheduleconfig = get_records_assoc('interaction_schedule_instance_config', 'schedule', $scheduleid, '', 'field,value');

define('GROUP', $schedule->groupid);

define('TITLE', $schedule->title);


$smarty = smarty(array('interaction/schedule/js/edit.js'));
//$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading', $schedule->groupname);
$smarty->assign('subheading', TITLE);
$smarty->assign('event', $event);
$smarty->assign('returnto', $returnto);
$smarty->display('interaction:schedule:view.tpl');
