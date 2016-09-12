<?php
/**
 *
 * @package    mahara
 * @subpackage admin
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('ADMIN', 1);
define('MENUITEM', 'managegroups/groups');
require(dirname(dirname(dirname(__FILE__))) . '/init.php');

require_once('pieforms/pieform.php');
require_once('group.php');
safe_require('interaction', 'schedule');

$group = get_record_select('group', 'id = ? AND deleted = 0', array(param_integer('id')));

define('TITLE', get_string('administergroups', 'admin'));

$quotasform = pieform(array(
    'name'       => 'groupquotasform',
    'elements'   => array(
        'groupid' => array(
            'type' => 'hidden',
            'value' => $group->id,
        ),
        'quota'  => array(
            'type' => 'bytes',
            'title' => get_string('filequota1', 'admin'),
            'description' => get_string('groupfilequotadescription', 'admin'),
            'defaultvalue' => $group->quota,
        ),
        'submit' => array(
            'type' => 'submit',
            'value' => get_string('save'),
        ),
    ),
));

function groupquotasform_submit(Pieform $form, $values) {
    global $SESSION;

    $group = new StdClass;
    $group->id = $values['groupid'];
    $group->quota = $values['quota'];
    update_record('group', $group);

    $SESSION->add_ok_msg(get_string('groupquotaupdated', 'admin'));
    redirect(get_config('wwwroot').'admin/groups/groups.php');
}


$admins = get_column_sql(
    "SELECT gm.member FROM {group_member} gm WHERE gm.role = 'admin' AND gm.group = ?", array($group->id)
);
$tutors = get_column_sql(
    "SELECT gm.member FROM {group_member} gm WHERE gm.role = 'tutor' AND gm.role != 'tutor' AND gm.group = ?", array($group->id)
);

$groupadminsform = pieform(array(
    'name'       => 'groupadminsform',
    'renderer'   => 'table',
    'plugintype' => 'core',
    'pluginname' => 'admin',
    'elements'   => array(
        'admins' => array(
            'type' => 'userlist',
            'title' => get_string('groupadmins', 'group'),
            'defaultvalue' => $admins,
            'lefttitle' => get_string('potentialadmins', 'admin'),
            'righttitle' => get_string('currentadmins', 'admin'),
        ),
        'tutors' => array(
            'type' => 'userlist',
            'title' => get_string('grouptutors', 'group'),
            'defaultvalue' => $tutors,
            'lefttitle' => get_string('potentialtutors', 'admin'),
            'righttitle' => get_string('currenttutors', 'admin'),
        ),
        'archive' => array(
            'type' => 'checkbox',
            'title' => get_string('archivegroup', 'admin'),
            'defaultvalue' => false,
        ),
        'archiveprefix' => array(
            'type' => 'text',
            'title' => get_string('archivegroupprefix', 'admin'),
            'defaultvalue' => '',
        ),
        'resetsubgroupcolours' => array(
            'type' => 'checkbox',
            'title' => get_string('resetsubgroupcolours', 'admin'),
            'defaultvalue' => false,
        ),
        'submit' => array(
            'type' => 'submit',
            'value' => get_string('save'),
        ),
    ),
));

function create_a_new_group($old,$newparent,$prefix){
	global $USER;
/* GROUP DATA
            'name'           => $data['name'],
            'description'    => isset($data['description']) ? $data['description'] : null,
            'urlid'          => isset($data['urlid']) ? $data['urlid'] : null,
            'grouptype'      => $data['grouptype'],
            'category'       => isset($data['category']) ? intval($data['category']) : null,
            'jointype'       => $jointype,
            'ctime'          => $data['ctime'],
            'mtime'          => $data['ctime'],
            'public'         => $data['public'],
            'usersautoadded' => $data['usersautoadded'],
            'quota'          => $data['quota'],
            'institution'    => !empty($data['institution']) ? $data['institution'] : null,
            'shortname'      => $data['shortname'],
            'request'        => isset($data['request']) ? intval($data['request']) : 0,
            'submittableto'  => intval($data['submittableto']),
            'allowarchives'  => !empty($data['submittableto']) ? intval($data['allowarchives']) : 0,
            'editroles'      => $data['editroles'],
            'hidden'         => $data['hidden'],
            'hidemembers'    => $data['hidemembers'],
            'hidemembersfrommembers' => $data['hidemembersfrommembers'],
            'groupparticipationreports' => $data['groupparticipationreports'],
            'invitefriends'  => $data['invitefriends'],
            'suggestfriends' => $data['suggestfriends'],
            'editwindowstart' => $data['editwindowstart'],
            'editwindowend'  => $data['editwindowend'],
            'sendnow'        => isset($data['sendnow']) ? $data['sendnow'] : null,
            'viewnotify'     => isset($data['viewnotify']) ? $data['viewnotify'] : null,
            'feedbacknotify' => isset($data['feedbacknotify']) ? $data['feedbacknotify'] : null,
*/    	
	$newdata = array();
	$old_data = group_get_groups_for_editing(array($old));
	if (count($old_data) != 1) {
        throw new GroupNotFoundException(get_string('groupnotfound', 'group', $id));
    }

    $old_data = (array)$old_data[0];

	$newdata = $old_data;
	$newdata['parent'] = $newparent;
	unset($newdata['id']);
	
	//rename the old one first
	$old_data['name'] = $prefix . $old_data['name'];
	group_update((object)$old_data);

	//copy only the admins and tutors over
	$oldadmins = group_get_member_ids($old,array('admin'));
	$oldtutors = group_get_member_ids($old,array('tutor'));
	$oldGAA = group_get_member_ids($old,array('ta'));
	//need to add admin user to members so it can copy stuff over
	$newdata['members'] = array($USER->get('id') => 'admin');
	foreach($oldadmins as $oldadmin){
		if($USER->get('id') != $oldadmin){
			$newdata['members'][$oldadmin] = 'admin';
		}
	}
	foreach($oldtutors as $oldtutor){
		$newdata['members'][$oldtutor] = 'tutor';
	}
	foreach($oldGAA as $oldGAA){
		$newdata['members'][$oldGAA] = 'ta';
	}
	/*reset some of the defaults - this is a hack for mdx*/
	$newdata['allowarchives'] = false;
	$newdata['viewnotify'] = 2;
	$newdata['feedbacknotify'] = 2;
	//resetdates
	//todo: this would be good but for now we will leave it...
	//create the new group
	$newdata['id'] = group_create($newdata);
	
	//Need to copy scheduled events over to the new year
	//start by getting all events for the group schedule
	//Then for each we need to convert it to the appropriate day
	//so find the day and add a year to it
	//echo date('Y-m-d (l, W)').<br/>;
	//echo date('Y-m-d (l, W)', strtotime("+52 week"));
	copy_schedules_for_group($old_data['id'],$newdata['id'],52);
	//then copy any of the old groups subgroups
	if($subgroups = get_group_subgroups_array($old_data['id'],null,1)){
		foreach($subgroups as $subgroup){
			create_a_new_group($subgroup->id,$newdata['id'],$prefix);
		}
	}


}

function groupadminsform_submit(Pieform $form, $values) {
    global $SESSION, $group, $admins, $tutors;

    $newadmins = array_diff($values['admins'], $admins);
    $demoted = array_diff($admins, $values['admins']);
    if(isset($values['admins'])){
		$newtutors = array_diff($values['tutors'], $tutors);
		$demotetutors = array_diff($tutors, $values['tutors']);
	}
    db_begin();
    
    if(isset($values['archive']) && $values['archive']){
    	//this will copy a group - rename it and then go through and copy all subgroups
    	create_a_new_group($group->id,group_get_parent($group->id),$values['archiveprefix']);//TODO:only go this far
    }
    if(isset($values['resetsubgroupcolours']) && $values['resetsubgroupcolours']){
		$schedules = get_schedule_list($group->id);
		$config = array();
		if($schedules){
			$config = get_records_assoc('interaction_schedule_instance_config', 'schedule', $schedules[0]->id, '', 'field,value');
		}
    	if($subgroups = get_group_subgroups_array($group->id)){
    		$subs = array();
    		foreach($subgroups as $subgroup){
    			$subs[] = $subgroup->id;
    		}
    		if($schedules = get_records_sql_array("SELECT i.id
					from {interaction_instance} i
					join {interaction_schedule_instance_config} cg on cg.schedule = i.id
					where i.group in (" . join(',', $subs) . ")
					AND cg.field = 'color'"
    			,array())){
					$subs = array();    			
					foreach($schedules as $schedule){
						$subs[] = $schedule->id;
					}
					execute_sql("update {interaction_schedule_instance_config} cg 
						set cg.value = ? where cg.schedule in (" . join(',', $subs) . ")",array($config['color']->value));
    			}
    		}
	}

    if ($demoted) {
        $demoted = join(',', array_map('intval', $demoted));
        execute_sql("
            UPDATE {group_member}
            SET role = 'member'
            WHERE role = 'admin' AND \"group\" = ?
                AND member IN ($demoted)",
            array($group->id)
        );
    }
    $dbnow = db_format_timestamp(time());
    foreach ($newadmins as $id) {
        if (group_user_access($group->id, $id)) {
            group_change_role($group->id, $id, 'admin');
        }
        else {
            group_add_user($group->id, $id, 'admin');
        }
    }
    if (isset($demotedtutors) && $demotedtutors) {
        $demotedtutors = join(',', array_map('intval', $demotedtutors));
        execute_sql("
            UPDATE {group_member}
            SET role = 'member'
            WHERE role = 'tutor' AND \"group\" = ?
                AND member IN ($demotedtutors)",
            array($group->id)
        );
    }
    $dbnow = db_format_timestamp(time());
    foreach ($newtutors as $id) {
        if (group_user_access($group->id, $id)) {
            group_change_role($group->id, $id, 'tutor');
        }
        else {
            group_add_user($group->id, $id, 'tutor');
        }
    }
    db_commit();

    $SESSION->add_ok_msg(get_string('groupadminsupdated', 'admin'));
    redirect(get_config('wwwroot').'admin/groups/groups.php');
}

$smarty = smarty();
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('quotasform', $quotasform);
$smarty->assign('groupname', $group->name);
$smarty->assign('managegroupform', $groupadminsform);
$smarty->display('admin/groups/manage.tpl');
