<?php
define('PUBLIC', 1);
define('INTERNAL', 1);

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('user.php');
safe_require('artefact', 'assessment');


$assessment = new ArtefactTypeAssessment();
$assessment->set('assessment_scheme',1);
$assessment->set('grade_type',1);
var_dump($assessment->get('assessment_scheme'));
//$assessment->commit();

//tests for assessment objects
//create a new assessment artefact
$assessment = new ArtefactTypeAssessment(1751);
//localhost:8888/mahara/htdocs/artefact/artefact.php?artefact=1751&view=153
/*$assessment->set('assessment_scheme',1);
$assessment->set('grade_type',1);
$assessment->commit();*/

$assessmentscheme = $assessment->get('assessment_scheme');

/*
foreach($assessmentscheme->criteria as $criteria){
	$assessment->set_criteria_result($criteria->id,rand(0,100));
}
*/
echo $assessment->get('grade_type')->title.'<br>';
$levels = $assessment->get('grade_type')->grade_levels;
//var_dump($levels);
echo $assessment->render_self(array())['html'];
var_dump($assessmentscheme->title.'<br/>');
var_dump($assessmentscheme->description.'<br/>');
echo '<table>';
foreach($assessmentscheme->criteria as $criteria){
//	var_dump($criteria->grade_type);
/*	echo '<tr>';
	echo '<td>'.$criteria->title.'</td>';
	echo '<td>'.$criteria->grade.'</td>';
	foreach($criteria->rubric as $level){
		if($criteria->grade && (($criteria->grade >= $level->min_percent) && ($criteria->grade <= $level->max_percent))){
			echo '<td style="background:#aaa">';
		}else{
			echo '<td>';
		}
		echo $level->title.'<br>'.$level->description.'</td>';
	}
	echo '</tr>';*/
}
echo '</table>';
