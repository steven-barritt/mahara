<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-mdxevaluation
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

function xmldb_blocktype_mdxevaluation_upgrade($oldversion=0) {

    if ($oldversion < 2014093000) {
        $table = new XMLDBTable('blocktype_mdxevaluation_data');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('instance', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->addFieldInfo('type', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->addFieldInfo('grade', XMLDB_TYPE_INTEGER, 10, XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('instancefk', XMLDB_KEY_FOREIGN, array('instance'), 'block_instance', array('id'));
       create_table($table);
//        set_config_plugin('blocktype', 'wall', 'defaultpostsizelimit', 1500); // 1500 characters
    }
    return true;
}
