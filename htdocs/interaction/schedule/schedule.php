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


global $USER;

if (!is_logged_in()) {
    throw new AccessDeniedException();
}


define('TITLE', get_string('myschedule', 'interaction.schedule'));





$events = array();
$events = schedule_get_user_events($limit,$offset);

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




$smarty = smarty(array(), $headers, array(), array());
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading',get_string('myschedule', 'interaction.schedule') );
$smarty->assign('limit', $limit);
$smarty->assign('maxdate', $maxdate->getTimestamp());
$smarty->assign('mindate', $mindate->getTimestamp());
$smarty->assign('offset', $offset);
$smarty->assign('events', $events);
$smarty->display('interaction:schedule:schedule.tpl');
