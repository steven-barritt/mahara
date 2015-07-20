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

$limit = param_integer('limit',31);
$offset = param_signed_integer('offset',0);


global $USER;

define('TITLE', get_string('myschedule', 'interaction.schedule'));


//if there is only one schedule which there should be for now then go straight to it.



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


$smarty = smarty(array(), $headers, array(), array());
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading',get_string('myschedule', 'interaction.schedule') );
$smarty->assign('limit', $limit);
$smarty->assign('offset', $offset);
$smarty->assign('events', $events);
$smarty->display('interaction:schedule:schedule.tpl');
