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

require_once(get_config('docroot') . 'artefact/lib.php');
safe_require('artefact', 'assessment');

$data['error'] = true;

$assessment = param_integer('assessment');
$publish = param_boolean('publish',false);
if(!$publish){
	$criteria = param_integer('criteria');
	$grade = param_integer('grade');


	if($assessment && $criteria){
		$assessment = new ArtefactTypeAssessment($assessment);
		//TODO: Set criteria result should be doing our security check
		$assessment->set_criteria_result($criteria,$grade);
		$finallevel = $assessment->get('grade_type')->get_level_id($assessment->get('grade'));
		$data['error'] = false;
		$data['data'] = $finallevel;

	}
}else{
	if($assessment){
		$assessment = new ArtefactTypeAssessment($assessment);
		//TODO: Set criteria result should be doing our security check
		$assessment->change_published_state();
		$published = $assessment->get('published');
		$data['error'] = false;
		$data['data'] = $published;

	}	
}

json_reply(false, $data);
