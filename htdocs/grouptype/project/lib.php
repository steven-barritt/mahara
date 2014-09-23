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
 * @subpackage grouptype-course
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2009 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

defined('INTERNAL') || die();
require_once('view.php');
class PluginGrouptypeProject extends PluginGrouptype {

    public static function postinst($prevversion) {
        if ($prevversion == 0) {
            parent::installgrouptype('GroupTypeProject');
        }
    }
    public static function can_be_disabled() {
        return false;
    }


	public static function get_event_subscriptions() {
        $sub = new stdClass();
        $sub->plugin = 'project';
        $sub->event = 'userjoinsgroup';
        $sub->callfunction = 'copy_template_page';
        return array($sub);
    }
 
    public static function copy_template_page($event, $eventdata) {
		//SB this is where we have to fnd the pages shared with group and with copynewuser
		//then we need to call the user function copy template page with the page
		//rray(5) { ["member"]=> int(25) ["group"]=> int(16) ["ctime"]=> string(19) "2014-09-21 13:52:47" ["role"]=> string(6) "member" ["method"]=> string(8) "internal" }
		/*$limit   = param_integer('limit', 5);
    $offset  = param_integer('offset', 0);

    $data = View::view_search(null, null, (object) array('group' => $group->id), null, $limit, $offset);
    // Add a copy view form for all templates in the list
    foreach ($data->data as &$v) {
        if ($v['template']) {
            $v['copyform'] = pieform(create_view_form(null, null, $v['id']));
        }
    }
$query=null, $ownerquery=null, $ownedby=null, $copyableby=null, $limit=null, $offset=0,
                                       $extra=true, $sort=null, $types=null, $collection=false, $accesstypes=null, $tag=null, $copynewuser=false	*/
	    //var_dump($user['group']);
		//We need to check wether the calling group is actually of the type Project as the event handler doesn;t seem to do this
//		$type = group_get_type($eventdata['group']);
		if(group_get_type($eventdata['group']) == 'project'){
			$sharedviews = View::get_sharedviews_data(0,0,$eventdata['group'],true);
			$sharedviews = $sharedviews->data;
			$ids = array();
			foreach ($sharedviews as &$data) {
				$ids[] = $data['id'];
			}
			
			$userobj = new User();
			$userobj->find_by_id($eventdata['member']);
	//		var_dump($userobj);
	//		bob::bob();
			$userobj->copy_views_from_group($ids,$eventdata['group']);
		}
		
//		var_dump($ids);
/*		
		var_dump($data);
		Bob::bob();
        $templateid = $values['usetemplate'];
        unset($values['usetemplate']);
        list($view, $template, $copystatus) = View::create_from_template($values, $templateid);
        if (isset($copystatus['quotaexceeded'])) {
            $SESSION->add_error_msg(get_string('viewcopywouldexceedquota', 'view'));
            redirect(get_config('wwwroot') . 'view/choosetemplate.php');
        }
        $SESSION->add_ok_msg(get_string('copiedblocksandartefactsfromtemplate', 'view',
            $copystatus['blocks'],
            $copystatus['artefacts'],
            $template->get('title'))
        );
 
		var_dump($USER->name);
		//var_dump($user);
		*/
/*        $name = display_name($user, null, true);
        $blog = new ArtefactTypeBlog(0, (object) array(
            'title'       => get_string('defaultblogtitle', 'artefact.blog', $name),
            'owner'       => $user['id'],
        ));
        $blog->commit();*/
    }
	

}

class GroupTypeProject extends GroupType {

    public static function allowed_join_types($all=false) {
        global $USER;
        return self::user_allowed_join_types($USER, $all);
    }

    public static function user_allowed_join_types($user, $all=false) {
        $jointypes = array();
        if (defined('INSTALLER') || defined('CRON') || $all || $user->get('admin') || $user->get('staff') || $user->is_institutional_admin() || $user->is_institutional_staff()) {
            $jointypes = array_merge($jointypes, array('controlled', 'request'));
        }
        return $jointypes;
    }
    public static function can_be_created_by_user() {
        global $USER;
        return $USER->get('admin') || $USER->get('staff') || $USER->is_institutional_admin()
            || $USER->is_institutional_staff();
    }

    public static function get_roles() {
        return array('member', 'tutor', 'admin');
    }

    public static function get_view_moderating_roles() {
        return array('tutor', 'admin');
    }

    public static function get_view_assessing_roles() {
        return array('tutor', 'admin');
    }

    public static function default_role() {
        return 'member';
    }
	public static function default_artefact_rolepermissions() {
        return array(
            'member' => (object) array('view' => true, 'edit' => false, 'republish' => false),
            'tutor'  => (object) array('view' => true, 'edit' => true, 'republish' => true),
            'admin'  => (object) array('view' => true, 'edit' => true, 'republish' => true),
        );
    }

}
