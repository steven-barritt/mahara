<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-internal
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

function xmldb_grouptype_project_upgrade($oldversion=0) {
    if ($oldversion < 2015091700) {
        $table = new XMLDBTable('group_hierarchy');
        $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, 10, false, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('child', XMLDB_TYPE_INTEGER, 10, false, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, 10, false, XMLDB_NOTNULL, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('parent','child'));
        $table->addKeyInfo('parentfk', XMLDB_KEY_FOREIGN, array('parent'), 'group', array('id'));
        $table->addKeyInfo('childfk', XMLDB_KEY_FOREIGN, array('child'), 'group', array('id'));
        create_table($table);
        
        require_once('group.php');
		$groups = get_records_sql_array("
			SELECT g.id, g.name, g.parent
			FROM {group} g WHERE g.deleted = 0 ORDER BY g.parent
			",array());

		foreach($groups as $group){
			group_add_hierarchy($group->parent,$group->id);
		}
		
		ensure_record_exists('grouptype_roles', (object) array('grouptype' => 'project', 'role' => 'ta'), (object) array('grouptype' => 'project', 'role' => 'ta', 'see_submitted_views' => '0'));


    }

    return true;
}
