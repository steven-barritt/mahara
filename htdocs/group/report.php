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
safe_require('artefact', 'comment');
define('TITLE', get_string('report', 'group'));
define('MENUITEM', 'groups/report');
define('GROUP', param_integer('group'));

$wwwroot = get_config('wwwroot');
$needsubdomain = get_config('cleanurlusersubdomains');

$setlimit = true;
$limit = param_integer('limit', 100);
$offset = param_integer('offset', 0);
$sort = param_variable('sort', 'sharedby');
$direction = param_variable('direction', 'asc');
$group = group_current_group();
$role = group_user_access($group->id);
if (!group_role_can_access_report($group, $role)) {
    throw new AccessDeniedException();
}
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
