<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-projectinfo
 * @author     Liip AG
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 * @copyright  (C) 2010 Liip AG, http://www.liip.ch
 *
 */

defined('INTERNAL') || die();

require_once('group.php');
class PluginBlocktypeProjectInfo extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.projectinfo');
    }

    public static function get_instance_title() {
        return '';
    }

    public static function get_description() {
        return get_string('description', 'blocktype.projectinfo');
    }

    public static function single_only() {
        return false;
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('grouphomepage','portfolio');
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
    	$groupid = 0;
		if($instance->get_view()->get('type') == 'portfolio'){
			$groupids = group_get_user_course_groups($instance->get_view()->get('owner'),$instance->get_view()->get('id'));
			if(count($groupids) >0 ){
				$groupid = $groupids[0]->id;
			}

		}else{
	        $groupid = $instance->get_view()->get('group');
		}
        $configdata = $instance->get('configdata');
		$text = isset($configdata['text']) ? $configdata['text'] : '';
		$data = null;
        if ($groupid) {
	        $data = self::get_data($groupid);
        }
        $dwoo = smarty_core();
        $dwoo->assign('text', $text);
        $dwoo->assign('group', $data);
        $dwoo->assign('editwindow', group_format_editwindow($data));
        return $dwoo->fetch('blocktype:projectinfo:projectinfo.tpl');
    }

    public static function has_instance_config() {
        return true;
    }

    public static function default_copy_type() {
        return 'full';
    }

    public static function instance_config_form($instance) {
        $configdata = $instance->get('configdata');
        $text = isset($configdata['text']) ? $configdata['text'] : '';
        if (!$height = get_config('blockeditorheight')) {
            $cfheight = param_integer('cfheight', 0);
            $height = $cfheight ? $cfheight * 0.7 : 150;
        }

		return array(
            'text' => array (
                'type' => 'wysiwyg',
                'title' => get_string('blockcontent', 'blocktype.text'),
                'width' => '100%',
                'height' => $height . 'px',
                'defaultvalue' => $text,
                'rules' => array('maxlength' => 65536),
            ),
			);
    }


    protected static function get_data($groupid) {
        global $USER;

        // get the currently requested group
        $group = group_get_group($groupid);

        $group->ctime = format_date($group->ctime, 'strftimedate');
        // if the user isn't logged in an the group isn't public don't show anything
        if (!is_logged_in() && !$group->public) {
            throw new AccessDeniedException();
        }

        return group_get_projectinfo_data($group);
    }
}
