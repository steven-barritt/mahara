<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2009 Catalyst IT Ltd and others; see:
 *                         http://wiki.mahara.org/Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage blocktype-newviews
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

defined('INTERNAL') || die();

class PluginBlocktypeNewsFeed extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.newsfeed');
    }

    public static function get_description() {
        return get_string('description1', 'blocktype.newsfeed');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('dashboard');
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
        global $USER;
		$usrid = $USER->get('id');
		require_once('view.php');
		$configdata = $instance->get('configdata');

        $result = '';
        $limit = isset($configdata['count']) ? (int) $configdata['count'] : 10;

		$views = View::view_search(null, null, null, null, null, 0, true, '', array('portfolio'));
		
		$viewarray = array();
//		var_dump($views->ids);
		foreach ($views->ids as $view){
	//		var_dump($view);
			$viewarray[] = $view;
		}
//		var_dump($viewarray);
		$mostrecent = NULL;
		$viewsstr = implode(",",$viewarray);
		if($viewsstr == ''){ /*we have no views available to us*/
			$smarty = smarty_core();
		}else{
			$smarty = smarty_core();
		//echo $viewsstr;
        /*if (!empty($configdata['artefactids'])) {
            $before = 'TRUE';
            if ($instance->get_view()->is_submitted()) {
                if ($submittedtime = $instance->get_view()->get('submittedtime')) {
                    // Don't display posts added after the submitted date.
                    $before = "a.ctime < '$submittedtime'";
                }
            }*/
            //$artefactids = implode(', ', array_map('db_quote', $configdata['artefactids']));
            if (!$mostrecent = get_records_sql_array(
            'SELECT a.title, ' . db_format_tsfield('a.ctime', 'ctime') . ', p.title AS parenttitle, a.id, a.parent, a.description, a.owner, va.view, a.allowcomments
                FROM {artefact} a
                JOIN {artefact} p ON a.parent = p.id
                JOIN {artefact_blog_blogpost} ab ON (ab.blogpost = a.id AND ab.published = 1)
				JOIN {view_artefact} va ON (p.id = va.artefact)
                WHERE a.artefacttype = \'blogpost\'
                AND va.view IN (' . $viewsstr . ')
				
	            ORDER BY a.ctime DESC, a.id DESC
                LIMIT ' . $limit,array())) {
                $mostrecent = array();
				/*AND a.owner != ?  ,array($usrid) excludes your own posts but this doesn;t seem right somehow*/
            }
            foreach ($mostrecent as &$data) {
                $data->displaydate = format_date($data->ctime);
				$data->user = get_user($data->owner);
				$postcontent = $data->description;
				safe_require('artefact', 'file');
				$postcontent = ArtefactTypeFolder::append_view_url($postcontent, $data->view);
				$data->description = $postcontent;
				if ($data->allowcomments) {
					safe_require('artefact', 'comment');
					$empty = array();
					$ids = array($data->id);
					$commentcount = ArtefactTypeComment::count_comments($empty, $ids);
	                $data->commentcount = $commentcount ? $commentcount[(int)$data->id]->comments : 0;
	            }
		        $data->artefacturl = get_config('wwwroot') . 'view/artefact.php?artefact=' . $data->id.'&view='.$data->view;

            }
		}
			//var_dump($mostrecent);
            // format the dates

			$smarty->assign('loggedin', $USER->is_logged_in());
			$smarty->assign('view', $instance->get('view'));
			$smarty->assign('posts', $mostrecent);
			return $smarty->fetch('blocktype:newsfeed:newsfeed.tpl');


/*
            $smarty = smarty_core();



            $smarty->assign('mostrecent', $mostrecent);
            $smarty->assign('view', $instance->get('view'));
            $smarty->assign('blockid', $instance->get('id'));
            $smarty->assign('editing', $editing);
            if ($editing) {
                // Get id and title of configued blogs
                $recentpostconfigdata = $instance->get('configdata');
                $wherestm = ' WHERE id IN (' . join(',', array_fill(0, count($recentpostconfigdata['artefactids']), '?')) . ')';
                if (!$selectedblogs = get_records_sql_array('SELECT id, title FROM {artefact}'. $wherestm, $recentpostconfigdata['artefactids'])) {
                    $selectedblogs = array();
                }
                $smarty->assign('blogs', $selectedblogs);
            }
            $result = $smarty->fetch('blocktype:recentposts:recentposts.tpl');
       // }

        return $result;
		
		
		
        require_once('view.php');
        $configdata = $instance->get('configdata');
        $nviews = isset($configdata['limit']) ? intval($configdata['limit']) : 5;

        $sort = array(array('column' => 'mtime', 'desc' => true));
        $views = View::view_search(null, null, null, null, $nviews, 0, true, $sort, array('portfolio'));
        $smarty = smarty_core();
        $smarty->assign('loggedin', $USER->is_logged_in());
        $smarty->assign('posts', $mostrecent);
        return $smarty->fetch('blocktype:newsfeed:newsfeed.tpl');*/
    }

    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form($instance) {
        $configdata = $instance->get('configdata');
        return array('limit' => array(
            'type' => 'text',
            'title' => get_string('viewstoshow', 'blocktype.newsfeed'),
            'description' => get_string('viewstoshowdescription', 'blocktype.newsfeed'),
            'defaultvalue' => (isset($configdata['limit'])) ? intval($configdata['limit']) : 5,
            'size' => 3,
            'minvalue' => 1,
            'maxvalue' => 100,
        ));
    }

    public static function default_copy_type() {
        return 'shallow';
    }

    public static function get_instance_title(BlockInstance $instance) {
        return get_string('title', 'blocktype.newsfeed');
    }
}
