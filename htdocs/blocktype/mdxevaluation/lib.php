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

class PluginBlocktypeMdxEvaluation extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.mdxevaluation');
    }

    public static function get_description() {
        return get_string('description1', 'blocktype.mdxevaluation');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('portfolio');
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
        $configdata = $instance->get('configdata');
        $smarty = smarty_core();
        $smarty->assign('research', $configdata['research']);
        $smarty->assign('concept', $configdata['concept']);
        $smarty->assign('technical', $configdata['technical']);
        $smarty->assign('presentation', $configdata['presentation']);
        $smarty->assign('studentship', $configdata['studentship']);
        $smarty->assign('workbook', $configdata['workbook']);
        $smarty->assign('selfmark', $configdata['selfmark']);
		return $smarty->fetch('blocktype:mdxevaluation:mdxevaluation.tpl');
		
    }

    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form($instance) {
        $configdata = $instance->get('configdata');

		return array(
            'research' => array(
			'type' => 'radio',
            'title' => get_string('research', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            'description' => get_string('researchdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['research'])) ? intval($configdata['research']) : 0,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
        	),
			'concept' => array(
			'type' => 'radio',
            'title' => get_string('concept', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            'description' => get_string('conceptdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['concept'])) ? intval($configdata['concept']) : 0,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'technical' => array(
			'type' => 'radio',
            'title' => get_string('technical', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            'description' => get_string('technicaldescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['technical'])) ? intval($configdata['technical']) : 0,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'presentation' => array(
			'type' => 'radio',
            'title' => get_string('presentation', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            'description' => get_string('presentationdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['presentation'])) ? intval($configdata['presentation']) : 0,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'studentship' => array(
			'type' => 'radio',
            'title' => get_string('studentship', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            'description' => get_string('studentshipdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['studentship'])) ? intval($configdata['studentship']) : 0,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'workbook' => array(
			'type' => 'radio',
            'title' => get_string('workbook', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            'description' => get_string('workbookdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['workbook'])) ? intval($configdata['workbook']) : 0,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
        	),
			'selfmark' => array(
            'type' => 'radio',
            'title' => get_string('selfmark', 'blocktype.mdxevaluation'),
            'description' => get_string('selfmarkdescription', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',6 => '6',7 => '7',8 => '8',9 => '9',10 => '10'
								   ,11 => '11',12 => '12',13 => '13',14 => '14',15 => '15',16 => '16',17 => '17'
                                   ),
            'defaultvalue' => (isset($configdata['selfmark'])) ? intval($configdata['selfmark']) : 1,
			'rules' => array('required'    => true),
                'help' => true,
			)
		);
    }

    public static function default_copy_type() {
        return 'shallow';
    }

    public static function get_instance_title(BlockInstance $instance) {
        return get_string('title', 'blocktype.mdxevaluation');
    }
}
