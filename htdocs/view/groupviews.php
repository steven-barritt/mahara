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
define('PUBLIC', 1);
define('MENUITEM', 'groups/views');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'view');
define('SECTION_PAGE', 'groupviews');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once(get_config('libroot') . 'view.php');
require_once(get_config('libroot') . 'group.php');
require_once('pieforms/pieform.php');
require_once(get_config('libroot') . 'collection.php');
require_once(get_config('libroot') . 'objectionable.php');
require_once('institution.php');
safe_require('artefact', 'comment');

$offset = param_integer('offset', 0);

define('GROUP', param_integer('group'));
$group = group_current_group();
if (!is_logged_in() && !$group->public) {
    throw new AccessDeniedException();
}


$role = group_user_access($group->id);
$can_edit = $role && group_role_can_edit_views($group, $role);

// If the user can edit group views, show a page similar to the my views
// page, otherwise just show a list of the views owned by this group that
// are visible to the user.

$currentviewid = param_integer('currentview',null);





	list($collections,$views) = View::get_views_and_collections(null, $group->id,null);
//	var_dump($collections);
//	var_dump("bob");
//	var_dump($views);
//    list($searchform, $data, $pagination) = View::get_views_and_collections(null, $group->id,null);
//    list($searchform, $data, $pagination) = View::views_by_owner($group->id);
	
    $createviewform = pieform(create_view_form($group->id));

$js = '';
/*
$js = <<< EOF
addLoadEvent(function () {
    p = {$pagination['javascript']}
EOF;
if ($offset > 0) {
    $js .= <<< EOF
    if ($('groupviews')) {
        getFirstElementByTagAndClassName('a', null, 'groupviews').focus();
    }
    if ($('myviews')) {
        getFirstElementByTagAndClassName('a', null, 'myviews').focus();
    }
EOF;
}
else {
    $js .= <<< EOF
    if ($('searchresultsheading')) {
        addElementClass('searchresultsheading', 'hidefocus');
        setNodeAttribute('searchresultsheading', 'tabIndex', -1);
        $('searchresultsheading').focus();
    }
EOF;
}
$js .= '});';
*/


if (isset($currentviewid)) {
    $currentview = new View($currentviewid);
}else{
	if(count($views) > 0){
		$currentviewid = reset($views)['id'];
	}else if(count($collections) > 0){
		$first = reset($collections);
		$first = reset($first['views']);
		$currentviewid = $first['id'];
	}else{
		$currentviewid = -1;
	}
	if($currentviewid != -1){
		$currentview = new View($currentviewid);
	}else{
		$currentview = null;
	}
}


$headers = '';
$javascript = '';
$extrastylesheets = '';
$skin='';
if(isset($currentview)){
	if (!can_view_view($currentview)) {
		throw new AccessDeniedException(get_string('accessdenied', 'error'));
	}

	// Feedback list pagination requires limit/offset params
	$limit       = param_integer('limit', 10);
	$offset      = param_integer('offset', 0);
	$showcomment = param_integer('showcomment', null);
	$owner    = $currentview->get('owner');
	$viewtype = $currentview->get('type');
	define('TITLE', $group->name . ' - '.$currentview->get('title'));
	$javascript = array('paginator', 'viewmenu', 'expandable', 'author', 'views-inline','js/jquery/modernizr.custom.js');
	$blocktype_js = $currentview->get_all_blocktype_javascript();
	$javascript = array_merge($javascript, $blocktype_js['jsfiles']);
	$inlinejs = "addLoadEvent( function() {\n" . join("\n", $blocktype_js['initjs']) . "\n});";

	$extrastylesheets = array('style/views.css');
	$viewtheme = $currentview->get('theme');
	if ($viewtheme && $THEME->basename != $viewtheme) {
		$THEME = new Theme($viewtheme);
	}
	$headers = array('<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'theme/views.css?v=' . get_config('release'). '">');
	$headers = array_merge($headers, $currentview->get_all_blocktype_css());
	// Set up skin, if the page has one
	$viewskin = $currentview->get('skin');
	$issiteview = $currentview->get('institution') == 'mahara';
	if ($viewskin && get_config('skins') && can_use_skins($owner, false, $issiteview) && (!isset($THEME->skins) || $THEME->skins !== false)) {
		$skin = array('skinid' => $viewskin, 'viewid' => $currentview->get('id'));
		$skindata = unserialize(get_field('skin', 'viewskin', 'id', $viewskin));
	}
	else {
		$skin = false;
	}

	if (!$currentview->is_public()) {
		$headers[] = '<meta name="robots" content="noindex">';  // Tell search engines not to index non-public views
	}

	// include slimbox2 js and css files, if it is enabled...
	if (get_config_plugin('blocktype', 'gallery', 'useslimbox2')) {
		$langdir = (get_string('thisdirection', 'langconfig') == 'rtl' ? '-rtl' : '');
		$headers = array_merge($headers, array(
			'<script type="text/javascript" src="' . get_config('wwwroot') . 'lib/slimbox2/js/slimbox2.js?v=' . get_config('release'). '"></script>',
			'<link rel="stylesheet" type="text/css" href="' . get_config('wwwroot') . 'lib/slimbox2/css/slimbox2' . $langdir . '.css?v=' . get_config('release'). '">'
		));
	}

	$can_edit = $USER->can_edit_view($currentview) && !$submittedgroup && !$currentview->is_submitted();

	$viewgroupform = false;
	if ($owner && $owner == $USER->get('id')) {
		if ($tutorgroupdata = group_get_user_course_groups($owner, $currentview->get('id'))) {
			if (!$currentview->is_submitted()) {
				$viewgroupform = view_group_submission_form($currentview, $tutorgroupdata, 'view');
			}
		}
	}

	$viewcontent = $currentview->build_rows(); // Build content before initialising smarty in case pieform elements define headers.
//var_dump($viewcontent);
	$smarty = smarty(
		$javascript,
		$headers,
		array(),
		array(
			'stylesheets' => $extrastylesheets,
			'sidebars' => false,
			'skin' => $skin
		)
	);

/*	var viewid = {$viewid};
	var showmore = {$showmore};
		paginator = {$feedback->pagination_js}
*/
	$javascript = <<<EOF
	function add_event_click_events() {
		forEach(getElementsByTagAndClassName('a', 'collection'), function(link) {
			connect(link, 'onclick', function(e) {
					e.preventDefault();
					var details = getFirstElementByTagAndClassName('ul', 'collectionviews', this.parentNode.parentNode);
					toggleElementClass("hidden",details);
			});
		});
	};
	addLoadEvent(function () {
	add_event_click_events();
	});
EOF;

	// collection top navigation
/*	if ($collection) {
		$shownav = $collection->get('navigation');
		if ($shownav) {
			if ($views = $collection->get('views')) {
				if (count($views['views']) > 1) {
					$smarty->assign_by_ref('collection', array_chunk($views['views'], 5));
				}
			}
		}
	}
*/
	$smarty->assign('INLINEJAVASCRIPT', $javascript . $inlinejs);
	$smarty->assign('new', $new);
	$smarty->assign('viewid', $viewid);
	$smarty->assign('viewtype', $viewtype);
	$smarty->assign('feedback', $feedback);
	$smarty->assign('owner', $owner);
//	$smarty->assign('tags', $currentview->get('tags'));

	if ($currentview->is_anonymous()) {
	  $smarty->assign('PAGEAUTHOR', get_string('anonymoususer'));
	  $smarty->assign('author', get_string('anonymoususer'));
	  if ($currentview->is_staff_or_admin_for_page()) {
		$smarty->assign('realauthor', $currentview->display_author());
	  }
	  $smarty->assign('anonymous', TRUE);
	} else {
	  $smarty->assign('PAGEAUTHOR', $currentview->formatted_owner());
	  $smarty->assign('author', $currentview->display_author());
	  $smarty->assign('anonymous', FALSE);
	}


	$titletext = ($collection && $shownav) ? hsc($collection->get('name')) : $currentview->display_title(true, false, false);

	if ($can_edit) {
		$smarty->assign('visitstring', $currentview->visit_message());
		$smarty->assign('editurl', get_config('wwwroot') . 'view/blocks.php?id=' . $viewid . ($new ? '&new=1' : ''));
	}

	$title = hsc(TITLE);


	$smarty->assign('viewdescription', $currentview->get('description'));
	$smarty->assign('viewcontent', $viewcontent);
	$smarty->assign('releaseform', $releaseform);
	if (isset($addfeedbackform)) {
		$smarty->assign('enablecomments', 1);
		$smarty->assign('addfeedbackform', $addfeedbackform);
	}
	if (isset($objectionform)) {
		$smarty->assign('objectionform', $objectionform);
		$smarty->assign('notrudeform', $notrudeform);
	}
	$smarty->assign('viewbeingwatched', $viewbeingwatched);

	if ($viewgroupform) {
		$smarty->assign('view_group_submission_form', $viewgroupform);
	}

	$smarty->assign('limitedediting', get_account_preference($USER->id, 'limitedediting'));
	$viewcontent = $smarty->fetch('view/groupview.tpl');
	$currentcollectionid = $currentview->collection_id();
}
else{
define('TITLE', $group->name . ' - ' . get_string('groupviews', 'view'));
}


$smarty = smarty(
	array('paginator'),
	$headers,
	array(),
	array(
		'stylesheets' => $extrastylesheets,
		'sidebars' => false,
		'skin' => $skin
	)
);


$can_edit = group_role_can_edit_views($group->id,$role);

//$smarty = smarty(array('paginator'));
$smarty->assign('INLINEJAVASCRIPT', $js.$javascript);
$smarty->assign('currentviewid', $currentviewid);
$smarty->assign('currentcollectionid',$currentcollectionid);
$smarty->assign('canedit', $can_edit);
$smarty->assign('views', $views);
$smarty->assign('group', $group);
$smarty->assign('collections', $collections);
$smarty->assign('pagination', $pagination['html']);
	$smarty->assign('viewcontent',$viewcontent);
    $smarty->assign('query', param_variable('query', null));
    $smarty->assign('querystring', get_querystring());
    $smarty->assign('editlocked', $role == 'admin');
    $smarty->assign('searchform', $searchform);
    $smarty->assign('createviewform', $createviewform);
    $smarty->display('view/groupviews.tpl');
