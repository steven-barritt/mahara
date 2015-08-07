<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-schedule
 * @author     Nigel McNie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 * @copyright  (C) 2009 Nigel McNie http://nigel.mcnie.name/
 *
 */

defined('INTERNAL') || die();
safe_require('interaction', 'schedule');

class PluginBlocktypeSchedule extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.schedule');
    }

    public static function get_description() {
        return get_string('description', 'blocktype.schedule');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_instance_javascript() {
        return array(
            array(
                'file'   => 'js/schedule.js',
                'initjs' => "add_event_click_events();",
            )
        );

    }


    private static function get_group(BlockInstance $instance) {
        static $groups = array();

        $block = $instance->get('id');

        if (!isset($groups[$block])) {

            // When this block is in a group view it should always display the

            $groupid = $instance->get_view()->get('group');
            $configdata = $instance->get('configdata');

            if (!$groupid && !empty($configdata['groupid'])) {
                $groupid = intval($configdata['groupid']);
            }

            if ($groupid) {
                $groups[$block] = get_record_select('group', 'id = ? AND deleted = 0', array($groupid), '*, ' . db_format_tsfield('ctime'));
            }
            else {
                $groups[$block] = false;
            }
        }

        return $groups[$block];
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
        $events = array();
        $role = null;
        if ($group = self::get_group($instance)) {

            require_once('group.php');
            $role = group_user_access($group->id);
        }
		$public = false;
		if($group){
			$public = $group->public;
		}
		if ($role || $public) {
			$schedules = get_schedule_list($group->id);
			$events = get_schedule_events($schedules[0]);
		}
		$limit = 14;
		$configdata = $instance->get('configdata');
		if (!empty($configdata['limit'])) {
			$limit = intval($configdata['limit']);
		}

		$mindate = new DateTime(Date('Y-m-d'));
		$maxdate = new DateTime($mindate->format('Y-m-d'));
		if($limit){
			$diff = DateInterval::createFromDateString($limit.' days');
			$maxdate->add($diff);
		}

		if(!$events & !$role){
			$events = schedule_get_user_events($mindate,$maxdate);
		}
		
		//var_dump($events);
/*                $scheduleinfo = get_records_sql_array(''
			array($group->id), 0, $limit
		);
*/
		$limittext = "2 weeks";
		switch ($limit) {
			case 7:
				$limittext = "1 weeks";
				break;
			case 14:
				$limittext = "2 weeks";
				break;
			case 21:
				$limittext = "3 weeks";
				break;
			case 31:
				$limittext = "1 month";
				break;
			case 62:
				$limittext = "2 months";
				break;
		}


		$smarty = smarty_core();
		$smarty->assign('limittext', $limittext);
		$smarty->assign('group', $group);
		$smarty->assign('events', $events);
		if ($instance->get_view()->get('type') == 'grouphomepage') {
			return $smarty->fetch('blocktype:schedule:groupschedule.tpl');
		}
		return $smarty->fetch('blocktype:schedule:schedule.tpl');

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
                'title' => get_string('poststoshow', 'blocktype.schedule'),
                'options' => array(7=>'1 Week',14=>'2 Weeks',21=>'3 Weeks',31=>'1 Month',62=>'2 Months'),
                'description' => get_string('poststoshowdescription', 'blocktype.schedule'),
                'defaultvalue' => (isset($configdata['limit'])) ? intval($configdata['limit']) : 14,
            );
        }

        return $elements;
    }

    public static function default_copy_type() {
        return 'shallow';
    }

    public static function feed_url(BlockInstance $instance) {
        if ($group = self::get_group($instance)) {
            if ($group->public) {
                return get_config('wwwroot') . 'interaction/schedule/atom.php?type=g&id=' . $group->id;
            }
        }
    }

    public static function get_instance_title(BlockInstance $instance) {
        if ($instance->get_view()->get('type') == 'grouphomepage') {
            return get_string('groupschedule', 'interaction.schedule');
        }
        return get_string('title', 'blocktype.schedule');
    }
}
