<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-socialicons
 * @author     Liip AG
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 * @copyright  (C) 2010 Liip AG, http://www.liip.ch
 *
 */

defined('INTERNAL') || die();

require_once('group.php');
class PluginBlocktypeSocialIcons extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.socialicons');
    }

    public static function get_instance_title() {
        return '';
    }

    public static function get_description() {
        return get_string('description', 'blocktype.socialicons');
    }

    public static function single_only() {
        return false;
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('grouphomepage','profile');
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
    	$data = null;
		$dwoo = smarty_core();

        $dwoo->assign('data', $data);
        return $dwoo->fetch('blocktype:socialicons:socialicons.tpl');
    }

    public static function has_instance_config() {
        return true;
    }

    public static function default_copy_type() {
        return 'shallow';
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

        return null;
    }
}
