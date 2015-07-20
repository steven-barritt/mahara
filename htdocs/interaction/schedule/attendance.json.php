<?php
/**
 *
 * @package    mahara
 * @subpackage core
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('JSON', 1);
require(dirname(dirname(dirname(__FILE__))) . '/init.php');

require_once(get_config('docroot') . 'interaction/lib.php');
safe_require('interaction', 'schedule');


$eventid = param_integer('event');
$userid = param_integer('user');
$attendance = param_integer('attendance');

if($attendance && $userid){
	schedule_update_attendance($eventid,$userid,$attendance);
}


$data['error'] = false;
$data['data'] = $userid;
json_reply(false, $data);
