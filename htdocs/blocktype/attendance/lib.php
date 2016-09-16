<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-attendance
 * @author     Nigel McNie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 * @copyright  (C) 2009 Nigel McNie http://nigel.mcnie.name/
 *
 */

defined('INTERNAL') || die();
safe_require('interaction', 'schedule');
require_once('group.php');

class PluginBlocktypeAttendance extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.attendance');
    }

    public static function get_description() {
        return get_string('description', 'blocktype.attendance');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_instance_javascript() {
        return array(
            array(
                'file'   => 'js/attendance.js',
                'initjs' => "add_event_click_events();",
            )
        );

    }


    private static function get_groups(BlockInstance $instance) {

        $block = $instance->get('id');

		
		$groupids = group_get_user_course_groups($instance->get_view()->get('owner'),$instance->get_view()->get('id'));
	
        return $groupids;
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
		

	//lets start with simple attendance block on a normal page
	//we start by getting the group the page is shared with
	//this is limited to one at the moment but should be able to cope with multiples
	//this isn;t very nice
	//likewise we then get the schedule for that group but at present there is assumed only one
	//this again should be changed later to allow more than one schedule per group
		global $USER;
		$attendances = array();
		$events = array();
		$cansee = true;
		$canedit = false;
		$groupid = null;
		$percentages = null;
		if($instance->get_view()->get('type') == 'profile'){
			if($instance->get_view()->get('owner') == $USER->get('id') || $USER->is_staff_for_user($instance->get_view()->get('owner'))){
				list($attendances,$percentages) = schedule_get_user_attendance($instance->get_view()->get('owner'));
			}else{
				$cansee = false;
			}
			//check user privaliges to see if they can see this...
			//only admin, staff, institution staff, and the user can see this. if it is a profile version
			
		}else{
			$groups = self::get_groups($instance);
			if(count($groups) > 0){
				$userid = (!empty($USER) ? $USER->get('id') : 0);
				$canedit = group_user_can_assess_submitted_views($groups[0]->id,$userid);
				$groupid = $groups[0]->id;
				$schedules = get_schedule_list($groups[0]->id);
				if($schedules){
					$scheduleid = $schedules[0]->id;
					$events = schedule_get_attendance_events($scheduleid);
					$attendances = schedule_get_schedule_attendance($scheduleid,$instance->get_view()->get('owner'));
				}
			}
		}

		$columnheight = '120px'; //just big enough to show the date
		
		$total = count($attendances);
		$factor = 50;
		if($total % 50 == 0){
			$factor = 50;
		}else{
			$potentialrows = intval($total / 50)+1;
			$factor = intval($total / $potentialrows)+1;
		}
		$smarty = smarty_core();
		$smarty->assign('cansee',$cansee);
		$smarty->assign('factor',$factor);
		$smarty->assign('events',$events);
		$smarty->assign('columnheight',$columnheight);
		$smarty->assign('attendances', $attendances);
		$smarty->assign('groupid', $groupid);
		$smarty->assign('canedit', $canedit);
/*		$smarty->assign('limittext', $limittext);
		$smarty->assign('group', $group);
		$smarty->assign('events', $events);*/
		if ($instance->get_view()->get('type') == 'profile') {
			$smarty->assign('percentages',$percentages);
			return $smarty->fetch('blocktype:attendance:profileattendance.tpl');
		}
		return $smarty->fetch('blocktype:attendance:attendance.tpl');

        return '';
    }

    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form($instance) {
        global $USER;

        $elements   = array();
        $groupid    = $instance->get_view()->get('group');
        $configdata = $instance->get('configdata');

        if ($groupid || $instance->get_view()->get('institution')) {
            $elements['groupid'] = array(
                'type' => 'hidden',
                'value' => $groupid,
            );
        }

        if (!isset($elements['groupid'])) {
            $elements['limit'] = array(
                'type' => 'select',
                'title' => get_string('poststoshow', 'blocktype.attendance'),
                'options' => array(7=>'1 Week',14=>'2 Weeks',21=>'3 Weeks',31=>'1 Month',62=>'2 Months'),
                'description' => get_string('poststoshowdescription', 'blocktype.attendance'),
                'defaultvalue' => (isset($configdata['limit'])) ? intval($configdata['limit']) : 14,
            );
        }

        return $elements;
    }

    public static function default_copy_type() {
        return 'shallow';
    }


    public static function get_instance_title(BlockInstance $instance) {
        if ($instance->get_view()->get('type') == 'grouphomepage') {
            return get_string('groupattendance', 'interaction.attendance');
        }
        return get_string('title', 'blocktype.attendance');
    }
}
