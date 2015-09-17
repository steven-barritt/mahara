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
define('MENUITEM', 'schedule');
define('SECTION_PLUGINTYPE', 'interaction');
define('SECTION_PLUGINNAME', 'schedule');
define('SECTION_PAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('group.php');
safe_require('interaction', 'schedule');
require_once('pieforms/pieform.php');
require_once(get_config('docroot') . 'interaction/lib.php');

$limit = param_integer('limit',31);
$offset = param_signed_integer('offset',0);
$this_month = date('n',time());
$this_year = date('Y',time());

global $USER;
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

$mindate = new DateTime(Date('Y-m-d'));
if($offset){
	$diff = DateInterval::createFromDateString($offset.' days');
	$mindate->add($diff);
}
$maxdate = new DateTime($mindate->format('Y-m-d'));
if($limit){
	$diff = DateInterval::createFromDateString($limit.' days');
	$maxdate->add($diff);
}

if($view == 2){
	list($startdate,$enddate) = schedule_get_start_and_enddates($month,$year,6);
	$mindate->setTimestamp(intval($startdate));
	$maxdate->setTimestamp(intval($enddate));
}elseif($view == 1){
	$month = 1;
	$startdate = schedule_get_user_startdate($USER->get('id'));
	if($startdate){
		$startdate = $startdate[0]->startdate;
		$month = intval(date('n',$startdate));
		$year = intval(date('Y',$startdate));
	}
	//need some more logic in here to work out the term startdate somehow for a user
	//without knowing what the top level group is.
	list($startdate,$enddate) = schedule_get_start_and_enddates($month,$year,52);	
	$mindate->setTimestamp(intval($startdate));
	$maxdate->setTimestamp(intval($enddate));
}





if (!is_logged_in()) {
    throw new AccessDeniedException();
}


define('TITLE', get_string('myschedule', 'interaction.schedule'));





$events = array();
$events = schedule_get_user_events($mindate,$maxdate);

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



$table = '';
$smarty = smarty(array(), array(), array(),array());
$smarty->assign('events', $events);
$smarty->assign('view',$view);
if($view == 2){
	$weeksanddays = schedule_events_per_cal_day($events, null,$month,$year);
	$smarty->assign('weeksanddays',$weeksanddays);
	$smarty->assign('month',$month);
	$smarty->assign('year',$year);
	$table = $smarty->fetch('interaction:schedule:calendarview.tpl');
}elseif($view == 1){
	$weeksanddays = schedule_events_per_day($events,null, $mindate->getTimestamp());
	$smarty->assign('weeksanddays',$weeksanddays);
	$table = $smarty->fetch('interaction:schedule:yearplannerview.tpl');
}else{
	$table = $smarty->fetch('interaction:schedule:scheduleview.tpl');
}


$smarty = smarty(array(), $headers, array(), array());
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading',get_string('myschedule', 'interaction.schedule') );
$smarty->assign('view',$view);
$smarty->assign('limit', $limit);
$smarty->assign('maxdate', $maxdate->getTimestamp());
$smarty->assign('mindate', $mindate->getTimestamp());
$smarty->assign('offset', $offset);
$smarty->assign('table', $table);
$smarty->display('interaction:schedule:schedule.tpl');
