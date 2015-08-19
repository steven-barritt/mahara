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
safe_require('blocktype', 'newsfeed');
require_once(get_config('libroot') . 'view.php');
require_once(get_config('libroot') . 'group.php');
$limit = param_integer('limit', 10);
$offset = param_integer('offset', 0);

$mostrecent = PluginBlocktypeNewsFeed::get_recent($limit,$offset);
$posthtml = PluginBlocktypeNewsFeed::render_items($mostrecent,'blocktype:newsfeed:newsfeeditems.tpl');

$data['error'] = false;
$data['data'] = $posthtml;
json_reply(false, $data);
