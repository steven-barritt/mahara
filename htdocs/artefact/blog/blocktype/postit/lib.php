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
 * @subpackage blocktype-textbox
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2009 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

defined('INTERNAL') || die();

class PluginBlocktypePostit extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.blog/postit');
    }

    public static function get_description() {
        return get_string('description', 'blocktype.blog/postit');
    }

    public static function get_categories() {
        return array('blog');
    }
	
	public static function get_viewtypes() {
        return array('dashboard');
    }



    public static function render_instance(BlockInstance $instance, $editing=false) {
        $configdata = $instance->get('configdata');
        $smarty = smarty_core();
        $smarty->assign('blogs','');
        return $smarty->fetch('blocktype:postit:postit.tpl');
    }

    // Called by $instance->get_data('collection', ...).
    public static function get_instance_collection($id) {
        require_once('collection.php');
        return new Collection($id);
    }

    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form($instance) {
        global $USER;

        $configdata = $instance->get('configdata');
        return array('default' => array(
            'type' => 'checkbox',
            'title' => get_string('blocktitle', 'blocktype.blog/postit'),
            'description' => get_string('blockdescription', 'blocktype.blog/postit'),
            'defaultvalue' => (isset($configdata['default'])) ? intval($configdata['default']) : 0,
        ));

    }

    public static function default_copy_type() {
        return 'shallow';
    }

}
