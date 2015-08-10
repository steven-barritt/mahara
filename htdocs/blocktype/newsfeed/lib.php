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
    
    public static function get_instance_javascript() {
        return array(
            array(
                'file'   => 'js/newsfeed.js',
                'initjs' => "add_click_events();",
            )
        );

    }
    
        /**
     * This function renders a list of items as html
     *
     * @param array items
     * @param string template
     * @param array options
     * @param array pagination
     */
    public function render_items(&$items, $template) {
        $smarty = smarty_core();
        $smarty->assign('posts', $items);
        return $smarty->fetch($template);
    }


	public static function get_recent($limit=10,$offset=0){
		require_once('view.php');
		$views = View::view_search(null, null, null, null, null, 0, true, '', array('portfolio','grouphomepage'));
		
		$viewarray = array();
		foreach ($views->ids as $view){
			$viewarray[] = $view;
		}
		$mostrecent = NULL;
		$viewsstr = implode(",",$viewarray);
		if($viewsstr != ''){ /*we have views available to us*/
            if (!$mostrecent = get_records_sql_array(
            'SELECT a.title, ' . db_format_tsfield('a.ctime', 'ctime') . ', p.title AS parenttitle, a.id, a.parent, a.description, a.owner,a.author, va.view, a.allowcomments
                FROM {artefact} a
                JOIN {artefact} p ON a.parent = p.id
                JOIN {artefact_blog_blogpost} ab ON (ab.blogpost = a.id AND ab.published = 1)
				JOIN {view_artefact} va ON (p.id = va.artefact)
                WHERE a.artefacttype = \'blogpost\'
                AND va.view IN (' . $viewsstr . ')
				
	            ORDER BY a.ctime DESC, a.id DESC
                LIMIT ' . $limit.' OFFSET '.$offset,array())) {
                $mostrecent = array();
				/*AND a.owner != ?  ,array($usrid) excludes your own posts but this doesn;t seem right somehow*/
            }
            foreach ($mostrecent as &$data) {
                $data->displaydate = format_date($data->ctime);
				if($data->owner){
					$data->user = get_user($data->owner);
				}else{
					$data->user = get_user($data->author);
				}
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
		        $data->artefacturl = get_config('wwwroot') . 'artefact/artefact.php?artefact=' . $data->id.'&view='.$data->view;

            }
		}
		return $mostrecent;

	}


    public static function render_instance(BlockInstance $instance, $editing=false) {
        global $USER;
		$usrid = $USER->get('id');
		require_once('view.php');
		$configdata = $instance->get('configdata');

        $result = '';
        $limit = isset($configdata['count']) ? (int) $configdata['count'] : 10;
//		$mostrecent = self::get_recent($limit);
//		$posthtml = self::render_items($mostrecent,'blocktype:newsfeed:newsfeeditems.tpl');
		$posthtml = '';
		$smarty = smarty_core();
		$smarty->assign('posthtml', $posthtml);
//		$smarty->assign('INLINEJAVASCRIPT', $javascript);
		return $smarty->fetch('blocktype:newsfeed:newsfeed.tpl');

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
