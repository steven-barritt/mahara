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
require_once(get_config('libroot') . 'group.php');
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
		if(group_get_type($eventdata['group']) == 'project' && $eventdata['copy_views']){
			$sharedviews = View::get_sharedviews_data(0,0,$eventdata['group'],true);
			$sharedviews = $sharedviews->data;
			$ids = array();
			foreach ($sharedviews as &$data) {
				$ids[] = $data['id'];
			}
			
			$userobj = new User();
			$userobj->find_by_id($eventdata['member']);
			//var_dump($userobj);
			$userobj->copy_views_from_group($ids,$eventdata['group']);
		}
		
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
        return array('member', 'tutor', 'admin', 'ta');
    }

    public static function get_view_moderating_roles() {
        return array('tutor', 'admin','ta');
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
            'ta'  => (object) array('view' => true, 'edit' => true, 'republish' => true),
        );
    }

}
