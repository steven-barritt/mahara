<?php
/**
 *
 * @package    mahara
 * @subpackage core
 * @author     Melissa Draper <melissa@catalyst.net.nz>, Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 */

define('INTERNAL', 1);
require(dirname(dirname(__FILE__)) . '/init.php');
require_once('view.php');
require_once('group.php');
require_once('user.php');
safe_require('artefact', 'comment');
define('TITLE', get_string('report', 'group'));
define('MENUITEM', 'groups/report');
define('GROUP', param_integer('group'));

$wwwroot = get_config('wwwroot');
$needsubdomain = get_config('cleanurlusersubdomains');

$setlimit = true;
$limit = param_integer('limit', 100);
$offset = param_integer('offset', 0);
$direction = param_variable('direction', 'asc');
$group = group_current_group();
if(in_array($group->grouptype,array('year','module','assessment'))){
	$sort = param_variable('sort', 'firstname');
}else{
	$sort = param_variable('sort', 'sharedby');
}
$role = group_user_access($group->id);
if (!group_role_can_access_report($group, $role)) {
    throw new AccessDeniedException();
}


//SB - this is where we need to diverge between a project group and a year group to show a different report
//what we should be doing is for each user we need to look at all the shared pages- this is tricky as we only want to see the ones from this year
//how do we determine if the page is for this year or not?
//how do we sort the grades from each project into years/modules
//need some sort of hierarchy in the groups? then get sharedviews from there?
//
//get users
//$group, $roles=null, $includedeleted=false
/*
function countassessmentcols($subgroups1,&$colcount){
	foreach($subgroups1 as $subgroup){
		if($subgroup->grouptype == 'assessment'){
			$colcount++;		
		}
		if(count($subgroup->subgroups) > 0){
			countassessmentcols($subgroup->subgroups,$colcount);
		}
	}
}
*/
function countassessmentcols($subgroups){
	$colcount = 0;
	foreach($subgroups as $subgroup){
		if($subgroup->grouptype == 'assessment'){
			$colcount++;		
		}
	}
}




function buildrows($subgroups){
	
//	$hassubgroups = false;
//	$cols = array();
	foreach($subgroups1 as $subgroup){
		if(in_array($subgroup->grouptype,array('year','module','assessment'))){ 
			$cols_count = 0;
			countassessmentcols($subgroup->subgroups,$cols_count);
	//		var_dump($cols_count);
	//		$cols[] = array('name'=>$subgroup->name,'colspan'=>$cols_count);
			$rows[$rowno][] = array('name'=>$subgroup->name,'colspan'=>$cols_count);
			if(count($subgroup->subgroups)>0){
				buildrows($subgroup->subgroups,$rows,$rowno+1);
			}
		}
	}
}


//var_dump($userdata);
//for each user

//get subgroups
// for each supgroup
// if grouptype = Mudule then start an average

//for each subgroup

//if group is assessment type then

function averagegrades($grades){
	$avgrade = 0;
	if($grades){
		$avgrade = round(array_sum($grades)/count($grades));
	}
	return Intval($avgrade);
}


function get_assessment($user,$assessment){
	$grades = array();
	$projectgroups = get_group_subgroups_array($assessment->id,'project');
	if(count($projectgroups) > 0){
		//find the actual Views shared by the user
		foreach($projectgroups as $project){
			//$limit=10, $offset=0, $groupid, $copynewuser=false, $getbloginfo=false, $submittedgroup = null,$excludetemplates=false,$user=null
			$sharedviews = View::get_sharedviews_data2(null,0,$project->id,false,false,null,false,$user);
//					var_dump($user);
			$sharedviews = $sharedviews->data;
//					var_dump($sharedviews);
			//find the assessment
			foreach ($sharedviews as &$data) {
//						bob::bob();
				require_once(get_config('docroot') . 'blocktype/lib.php');

				$sql = "SELECT bi.*
						FROM {block_instance} bi
						WHERE bi.view = ?
						AND bi.blocktype = 'mdxevaluation'
						";
				if (!$evaldata = get_records_sql_array($sql, array($data['id']))) {
					$evaldata = array();
				}

				foreach ($evaldata as $eval){
					$bi = new BlockInstance($eval->id, (array)$eval);
					$configdata = $bi->get('configdata');
					if(isset($configdata['evaltype'])){
						if($configdata['evaltype'] == 3){
							$published = isset($configdata['published']) ? $configdata['published'] : false;
							if($published){
								//TODO: Add individual grade elements - research etc.
								$grades[] = Intval($configdata['selfmark']);
							}
						}
					}		
				}
			}

			
		}
	}
	//if there is more than one then average the result and round up
	$grade = averagegrades($grades);
	return array($assessment->name, $grade);

}

function get_assessments($user,$assessmentgroup){
	$assessments = array();
	foreach($assessmentgroup as $assessment){
		$grades = array();
		$projectgroups = get_group_subgroups_array($assessment->id,'project');
		if(count($projectgroups) > 0){
			//find the actual Views shared by the user
			foreach($projectgroups as $project){
				//$limit=10, $offset=0, $groupid, $copynewuser=false, $getbloginfo=false, $submittedgroup = null,$excludetemplates=false,$user=null
				$sharedviews = View::get_sharedviews_data2(null,0,$project->id,false,false,null,false,$user);
//					var_dump($user);
				$sharedviews = $sharedviews->data;
//					var_dump($sharedviews);
				//find the assessment
				foreach ($sharedviews as &$data) {
//						bob::bob();
					require_once(get_config('docroot') . 'blocktype/lib.php');

					$sql = "SELECT bi.*
							FROM {block_instance} bi
							WHERE bi.view = ?
							AND bi.blocktype = 'mdxevaluation'
							";
					if (!$evaldata = get_records_sql_array($sql, array($data['id']))) {
						$evaldata = array();
					}

					foreach ($evaldata as $eval){
						$bi = new BlockInstance($eval->id, (array)$eval);
						$configdata = $bi->get('configdata');
						if(isset($configdata['evaltype'])){
							if($configdata['evaltype'] == 3){
								$published = isset($configdata['published']) ? $configdata['published'] : false;
								if($published){
									//TODO: Add individual grade elements - research etc.
									$grades[] = Intval($configdata['selfmark']);
								}
							}
						}		
					}
				}

				
			}
		}
		//if there is more than one then average the result and round up
		$grade = averagegrades($grades);
		$assessments[] = array($assessment->name, $grade);
		//add it to the list of assessments

	}
	return $assessments;
	
}

//Get all members and subgroup members
function getallmembers($subgroups){
	$members = array();
	foreach($subgroups as $subgroup){
		$newmembers = group_get_member_ids($subgroup->id, array('member'));
		$newmembers = array_combine($newmembers,$newmembers);
		if($newmembers){
			$members = array_merge($members,$newmembers);
		}
		if(count($subgroup->subgroups) > 0){
			$members = array_merge($members,getallmembers($subgroup->subgroups));
		}
		
	}
	return $members;
}

//for each supgroup
//get the shared_views_by_user_data
//this should have all the info we need
//then for each view get the tutor mark - if published
//add this to module average

if(in_array($group->grouptype, array('year','module','assessment')) ){

	$groupmembers = group_get_member_ids_inc_subgroups($group->id, array('member'));
	$groupmembers = array_combine($groupmembers,$groupmembers);
/*	$groupmembers = group_get_member_ids($group->id, array('member'));
	$groupmembers = array_combine($groupmembers,$groupmembers);
	$groupmembers = array_merge($groupmembers,getallmembers($subgroups));
	$groupmembers = array_unique($groupmembers);*/
//	var_dump(array_unique($groupmembers));
//var_dump($subgroups);
	$rows = array();
	$colcount = 0;
//	$rows[0][] = 'bob';
//	var_dump($rows);


	$assessmentgroups = array();
	$modulegroups = null;
	$modulegroups = get_group_subgroups_array($group->id,'module');
	if($modulegroups){
		foreach($modulegroups as $subgroup){
			$assessments = get_group_subgroups_array($subgroup->id,'assessment');
			$subgroup->colspan =count($assessments);
			$assessmentgroups = array_merge($assessmentgroups,$assessments);
		}
	}else{
		$assessmentgroups = get_group_subgroups_array($group->id,'assessment');
	}
//	var_dump($modulegroups);
	//var_dump($subgroups);
	$colcount = count($assessmentgroups);
	
	
//	$rows = buildrows($subgroups);
//	var_dump($rows);

	$columns = array();
	$sortable = array();
	$userdata = array();
	/*	$assessments = array();
		$name = display_default_name(22);
	get_assessments(22,$subgroups,$assessments);
		$userdata[] = array('id'=>22,'name'=>$name,'assessments'=>$assessments);*/
	foreach($groupmembers as $member){
		$user = get_user_for_display($member);
	//	var_dump($member);
		if($group->grouptype == 'assessment'){
			$assessments = get_assessments($member,array($group));
		}else{
			$assessments = get_assessments($member,$assessmentgroups);
		}
		$thisuser = array('id'=>$member,'firstname'=>$user->firstname,'lastname'=>$user->lastname,'profileicon'=>$user->profileicon,'studentnumber'=>$user->studentid);
		$i = 1;
		if(!$columns){
			foreach($assessments as $assessment){
				$columns[$i] = $assessment[0];
				$sortable[] = $i;
				$i++;
			}
		}
		$i = 1;
		foreach($assessments as $assessment){
			$thisuser[$i] = $assessment[1];
			$i++;
		}
//		$userdata[] = array('id'=>$member,'firstname'=>$user->firstname,'lastname'=>$user->lastname,'profileicon'=>$user->profileicon,'assessments'=>$assessments);
		$userdata[] = $thisuser;
	
	}
//	var_dump($userdata);
	//bob::bob();


	//$nocols = count($userdata[0]['assessments']);
	//var_dump($nocols);
/*	$i = 1;
	$columns = array();
	foreach($userdata[0]['assessments'] as $assessment){
		$columns[] = array('column'.$i=>$assessment[0]);
		$i++;
	}*/
//	var_dump($columns);
	$sortable[] = 'firstname';
	$sortable[] = 'lastname';
	if (in_array($sort, $sortable)) {
    	sorttablebycolumn($userdata, $sort, $direction);
	}

	

	$pagination = build_pagination(array(
		'url'    => get_config('wwwroot') . 'group/report.php?group=' . $group->id,
		'count'  => count($userdata),
		'limit'  => $limit,
		'setlimit' => $setlimit,
		'offset' => $offset,
		'jumplinks' => 6,
		'numbersincludeprevnext' => 2,
	));

$js = <<< EOF
addLoadEvent(function () {
	p = {$pagination['javascript']}
});
EOF;

	$smarty = smarty(array('paginator'));
	$smarty->assign('baseurl', get_config('wwwroot') . 'group/report.php?group=' . $group->id);
	$smarty->assign('heading', $group->name);
	$smarty->assign('userdata', $userdata);
	$smarty->assign('columns', $columns);
	$smarty->assign('modulegroups', $modulegroups);
	$smarty->assign('assessmentgroups', $assessmentgroups);
	$smarty->assign('colcount', $colcount);
	$smarty->assign('totalcolcount', $colcount+4);

	$smarty->assign('pagination', $pagination['html']);
	$smarty->assign('INLINEJAVASCRIPT', $js);

	$smarty->assign('usercount', count($userdata));
	$smarty->assign('sort', $sort);
	$smarty->assign('direction', $direction);
	$smarty->display('group/yearreport.tpl');
}else{





$sharedviews = View::get_sharedviews_data(0, null, $group->id,false,true,null,true);
$sharedviewscount = $sharedviews->count;
$sharedviews = $sharedviews->data;
foreach ($sharedviews as &$data) {

    if (isset($data['group'])) {
        $data['groupname'] = get_field('group', 'name', 'id', $data['group']);
    }

    $view = new View($data['id']);
    $comments = ArtefactTypeComment::get_comments(0, 0, null, $view);

	$selfgrade = 20;
	$peergrade = 20;
	$tutorgrade = 20;
	$published = true;
    require_once(get_config('docroot') . 'blocktype/lib.php');
	
	$sql = "SELECT bi.*
            FROM {block_instance} bi
            WHERE bi.view = ?
            AND bi.blocktype = 'mdxevaluation'
            ";
    if (!$evaldata = get_records_sql_array($sql, array($data['id']))) {
        $evaldata = array();
    }
	
	foreach ($evaldata as $eval){
		$bi = new BlockInstance($eval->id, (array)$eval);
		$configdata = $bi->get('configdata');
		if(isset($configdata['evaltype'])){
			if($configdata['evaltype'] == 1){
				$selfgrade = $configdata['selfmark'];
			}elseif($configdata['evaltype'] == 2){
				$peergrade = $configdata['selfmark'];
			}elseif($configdata['evaltype'] == 3){
				$published = isset($configdata['published']) ? $configdata['published'] : true;
				$tutorgrade = $configdata['selfmark'];
			}
		}		
	}
	
    $extcommenters = 0;
    $membercommenters = 0;
    $extcomments = 0;
    $membercomments = 0;
    $commenters = array();
    foreach ($comments->data as $c) {
        if (empty($c->author)) {
            if (!isset($commenters[$c->authorname])) {
                $commenters[$c->authorname] = array();
            }
            $commenters[$c->authorname]['commenter'] = $c->authorname;
            $commenters[$c->authorname]['count'] = (isset($commenters[$c->authorname]['count']) ? $commenters[$c->authorname]['count'] + 1 : 1);
            if ($commenters[$c->authorname]['count'] == 1) {
                $extcommenters++;
            }
            $extcomments++;
        }
        else {
            if (!isset($commenters[$c->author->id])) {
                $commenters[$c->author->id] = array();
            }
            $commenters[$c->author->id]['commenter'] = (int) $c->author->id;
            $commenters[$c->author->id]['member'] = group_user_access($group->id, $c->author->id);
            $commenters[$c->author->id]['count'] = (isset($commenters[$c->author->id]['count']) ? $commenters[$c->author->id]['count'] + 1 : 1);
            if (empty($commenters[$c->author->id]['member'])) {
                if ($commenters[$c->author->id]['count'] == 1) {
                    $extcommenters++;
                }
                $extcomments++;
            }
            else {
                if ($commenters[$c->author->id]['count'] == 1) {
                    $membercommenters++;
                }
                $membercomments++;
            }
        }
    }


    sorttablebycolumn($commenters, 'count', 'desc');
    $data['mcommenters'] = $membercommenters;
    $data['selfgrade'] = intval($selfgrade);
    $data['peergrade'] = intval($peergrade);
    $data['tutorgrade'] = intval($tutorgrade);
    $data['publishedgrade'] = $published;
    $data['postcount'] = isset($data['postcount']) ? $data['postcount'] : 0;
    $data['ecommenters'] = $extcommenters;
    $data['mcomments'] = $membercomments;
    $data['ecomments'] = $extcomments;
    $data['comments'] = $commenters;
    $data['baseurl'] = $needsubdomain ? $view->get_url(true) : ($wwwroot . $view->get_url(false));
}

$publishform = pieform(array(
			'name'                => 'publishgrades',
			'successcallback'     => 'publishgrades_submit',
			'renderer'            => 'div',
			'autofocus'           => false,
			'elements'            => array(
				'group' => array(
					'type'  => 'hidden',
					'value' => $group->id,
				),
				'submit' => array(
					'type'  => 'submit',
					'value' => get_string('publishbtn', 'group'),
				),
			),
		));


if (in_array($sort, array('title', 'sharedby', 'mcomments', 'ecomments','submittedtime','postcount', 'selfgrade','peergrade','tutorgrade'))) {
    sorttablebycolumn($sharedviews, $sort, $direction);
}
$sharedviews = array_slice($sharedviews, $offset, $limit);

list($searchform, $groupviews, $unusedpagination) = View::views_by_owner($group->id);
$groupviews = $groupviews->data;
$groupviewscount = count($groupviews);

foreach ($groupviews as &$data) {
    $view = new View($data['id']);
    $comments = ArtefactTypeComment::get_comments(0, 0, null, $view);

    $extcommenters = 0;
    $membercommenters = 0;
    $extcomments = 0;
    $membercomments = 0;
    $commenters = array();
    foreach ($comments->data as $c) {
        if (empty($c->author)) {
            if (!isset($commenters[$c->authorname])) {
                $commenters[$c->authorname] = array();
            }
            $commenters[$c->authorname]['commenter'] = $c->authorname;
            $commenters[$c->authorname]['count'] = (isset($commenters[$c->authorname]['count']) ? $commenters[$c->authorname]['count'] + 1 : 1);
            if ($commenters[$c->authorname]['count'] == 1) {
                $extcommenters++;
            }
            $extcomments++;
        }
        else {
            if (!isset($commenters[$c->author->id])) {
                $commenters[$c->author->id] = array();
            }
            $commenters[$c->author->id]['commenter'] = (int) $c->author->id;
            $commenters[$c->author->id]['member'] = group_user_access($group->id, $c->author->id);
            $commenters[$c->author->id]['count'] = (isset($commenters[$c->author->id]['count']) ? $commenters[$c->author->id]['count'] + 1 : 1);
            if (empty($commenters[$c->author->id]['member'])) {
                if ($commenters[$c->author->id]['count'] == 1) {
                    $extcommenters++;
                }
                $extcomments++;
            }
            else {
                if ($commenters[$c->author->id]['count'] == 1) {
                    $membercommenters++;
                }
                $membercomments++;
            }
        }
    }

    $data['id'] = (int) $data['id'];
    $data['mcommenters'] = $membercommenters;
    $data['ecommenters'] = $extcommenters;
    $data['mcomments'] = $membercomments;
    $data['ecomments'] = $extcomments;
    $data['comments'] = $commenters;
    $data['title'] = $data['displaytitle'];
}

if (in_array($sort, array('title', 'mcomments', 'ecomments'))) {
    sorttablebycolumn($groupviews, $sort, $direction);
}
$groupviews = array_slice($groupviews, $offset, $limit);

$pagination = build_pagination(array(
    'url'    => get_config('wwwroot') . 'group/report.php?group=' . $group->id,
    'count'  => max($sharedviewscount, $groupviewscount),
    'limit'  => $limit,
    'setlimit' => $setlimit,
    'offset' => $offset,
    'jumplinks' => 6,
    'numbersincludeprevnext' => 2,
));

$js = <<< EOF
addLoadEvent(function () {
    p = {$pagination['javascript']}
});
EOF;

$smarty = smarty(array('paginator'));
$smarty->assign('baseurl', get_config('wwwroot') . 'group/report.php?group=' . $group->id);
$smarty->assign('heading', $group->name);
$smarty->assign('sharedviews', $sharedviews);
$smarty->assign('groupviews', $groupviews);
$smarty->assign('pagination', $pagination['html']);
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('publishform', $publishform);
$smarty->assign('gvcount', $groupviewscount);
$smarty->assign('svcount', $sharedviewscount);
$smarty->assign('sort', $sort);
$smarty->assign('direction', $direction);
$smarty->display('group/report.tpl');

}

function publishgrades_submit(Pieform $form, $values){
	require_once(get_config('docroot') . 'blocktype/lib.php');
	global $SESSION;
    try {
    //$limit=10, $offset=0, $groupid, $copynewuser=false, $getbloginfo=false, $submittedgroup = null
    	$sharedviews = View::get_sharedviews_data(0, null, $values['group'],false,false,$values['group']);
		$sharedviews = $sharedviews->data;
		foreach ($sharedviews as &$data) {

	
			$sql = "SELECT bi.*
					FROM {block_instance} bi
					WHERE bi.view = ?
					AND bi.blocktype = 'mdxevaluation'
					";
			if (!$evaldata = get_records_sql_array($sql, array($data['id']))) {
				$evaldata = array();
			}
			
			foreach ($evaldata as $eval){
				$bi = new BlockInstance($eval->id, (array)$eval);
				$configdata = $bi->get('configdata');
				if(isset($configdata['evaltype'])){
					if($configdata['evaltype'] == 3){
						$configdata['published']= true;
						$bi->set('configdata',$configdata);
						$bi->set('dirty',true);
						$bi->commit();
					}
				}		
			}
			
			//now get any unpublished comments and publish them to the user.
			safe_require('artefact', 'comment');
			ArtefactTypeComment::publish_comments($data['id']);
/*			if(ArtefactTypeComment::count_comments(array($data['id']),null, true)){
				$feedback = ArtefactTypeComment::get_comments(0, 0, null, $data);
				foreach($feedback as $comment){
					$comment->set('published',true);
					$comment->set('dirty',true);
					$comment->commit();
					activity_occurred('feedback', $comment, 'artefact', 'comment');
				}			
			}*/
		}

    	

    }
    catch (SQLException $e) {
        $SESSION->add_error_msg(get_string('couldnotpublish', 'group').$e->getMessage());
    }
    $SESSION->add_ok_msg(get_string('published', 'group'));
    $goto = get_config('wwwroot').'group/report.php?group='.$values['group'];
    redirect($goto);

}
