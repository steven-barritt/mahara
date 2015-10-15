<?php
/**
 *
 * @package    mahara
 * @subpackage admin
 * @author     Richard Mansfield
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('INSTITUTIONALADMIN', 1);
define('MENUITEM', 'configusers');
require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once(get_config('docroot') . 'lib/antispam.php');
require_once(get_config('docroot') . 'lib/view.php');
require_once(get_config('libroot') . 'group.php');


define('TITLE', get_string('bulkactions', 'admin'));

$userids = array_map('intval', param_variable('users'));

$ph = $userids;
$institutionsql = '';

if (!$USER->get('admin')) {
    // Filter the users by the admin's institutions
    $institutions = array_values($USER->get('admininstitutions'));
    $ph = array_merge($ph, $institutions);
    $institutionsql = '
            AND id IN (
                SELECT usr FROM {usr_institution} WHERE institution IN (' . join(',', array_fill(0, count($institutions), '?')) . ')
            )';
}

$users = get_records_sql_assoc('
    SELECT
        u.id, u.username, u.email, u.firstname, u.lastname, u.suspendedcusr, u.authinstance, u.studentid,
        u.preferredname, CHAR_LENGTH(u.password) AS haspassword, aru.remoteusername AS remoteuser, u.lastlogin,
        u.probation
    FROM {usr} u
        LEFT JOIN {auth_remote_user} aru ON u.id = aru.localusr AND u.authinstance = aru.authinstance
    WHERE id IN (' . join(',', array_fill(0, count($userids), '?')) . ')
        AND deleted = 0' . $institutionsql . '
    ORDER BY username',
    $ph
);

// Display the number of users filtered out due to institution permissions.  This is not an
// exception, because the logged in user might be an admin in one institution, and staff in
// another.
if ($uneditableusers = count($userids) - count($users)) {
    $SESSION->add_info_msg(get_string('uneditableusers', 'admin', $uneditableusers));
}

$userids = array_keys($users);

// Hidden drop-down to submit the list of users back to this page.
// Used in all three forms
$userelement = array(
    'type'     => 'select',
    'class'    => 'hidden',
    'multiple' => 'true',
    'options'  => array_combine($userids, $userids),
    'value'    => $userids,
);

// Change authinstance
if ($USER->get('admin')) {
    $authinstances = auth_get_auth_instances();
}
else {
    $admininstitutions = $USER->get('admininstitutions');
    $authinstances = auth_get_auth_instances_for_institutions($admininstitutions);
}

$options = array();
$default = null;
foreach ($authinstances as $authinstance) {
    $options[$authinstance->id] = $authinstance->displayname. ': '.$authinstance->instancename;
    if (!$default && $authinstance->name == 'mahara') {
        $default = $authinstance->id;
    }
}

// Suspend users
$suspendform = pieform(array(
    'name'     => 'suspend',
    'class'    => 'bulkactionform',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'reason' => array(
            'type'        => 'text',
            'title'       => get_string('suspendedreason', 'admin') . ': ',
        ),
        'suspend' => array(
            'type'        => 'submit',
            'value'       => get_string('Suspend', 'admin'),
        ),
    ),
));

// Change authentication method
$changeauthform = null;
if (count($options) > 1) {
    $changeauthform = pieform(array(
        'name'           => 'changeauth',
        'class'          => 'bulkactionform',
        'renderer'       => 'oneline',
        'dieaftersubmit' => false,
        'elements'       => array(
            'users' => $userelement,
            'title'        => array(
                'type'         => 'html',
                'class'        => 'bulkaction-title',
                'value'        => get_string('changeauthmethod', 'admin') . ': ',
            ),
            'authinstance' => array(
                'type'         => 'select',
                'options'      => $options,
                'defaultvalue' => $default,
            ),
            'changeauth' => array(
                'type'         => 'submit',
                'value'        => get_string('submit'),
            ),
        ),
    ));
}

// Set probation points
$probationform = null;
if (is_using_probation()) {
    $probationform = pieform(array(
        'name' => 'probation',
        'class' => 'bulkactionform',
        'renderer' => 'oneline',
        'elements' => array(
            'users' => $userelement,
            'probationpoints' => array(
                'type' => 'select',
                'title' => get_string('probationbulksetspamprobation', 'admin') . ': ',
                'options' => probation_form_options(),
                'defaultvalue' => '0',
            ),
            'setprobation' => array(
                'type' => 'submit',
                'confirm' => get_string('probationbulkconfirm', 'admin'),
                'value' => get_string('probationbulkset', 'admin'),
            )
        ),
    ));
}

// Delete users
$resetprofileform = pieform(array(
    'name'     => 'resetprofile',
    'class'    => 'bulkactionform resetprofile',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('resetprofile', 'admin') . ': ',
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirmresetprofile', 'admin'),
            'value'       => get_string('reset'),
        ),
    ),
));

$resetdashboardform = pieform(array(
    'name'     => 'resetdashboard',
    'class'    => 'bulkactionform resetdashboard',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('resetdashboard', 'admin') . ': ',
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirmresetdashboard', 'admin'),
            'value'       => get_string('reset'),
        ),
    ),
));

$groups = get_group_list('project','Archive Group');
$options = array();
foreach($groups as $group){
	$options[$group->id] = $group->name;
}

$archivepagesform = pieform(array(
    'name'     => 'archivepages',
    'class'    => 'bulkactionform archivepages',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('archivepages', 'admin') . ': ',
        ),
        'group' => array(
            'type'        => 'select',
            'options'	 => $options,
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirmarchivepages', 'admin'),
            'value'       => get_string('archive', 'admin'),
        ),
    ),
));


$allgroups = get_group_list();
$grpoptions = array();
foreach($allgroups as $group){
	$grpoptions[$group->id] = $group->name;
}

$addtogroupform = pieform(array(
    'name'     => 'addtogroup',
    'class'    => 'bulkactionform addtogroup',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('addtogroup', 'admin') . ': ',
        ),
        'group' => array(
            'type'        => 'select',
            'options'	 => $grpoptions,
        ),
        'copy_title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('addtogroupcopyviews', 'admin') . ': ',
        ),
        'copy_views'        => array(
            'type'         => 'checkbox',
            'label'			=> 'Copy Views',
            'class'        => 'bulkaction-title',
            'defaultvalue'        => true,
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirmaddtogroup', 'admin'),
            'value'       => get_string('addtogroup', 'admin'),
        ),
    ),
));

$invitetogroupform = pieform(array(
    'name'     => 'invitetogroup',
    'class'    => 'bulkactionform invitetogroup',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('invitetogroup', 'admin') . ': ',
        ),
        'group' => array(
            'type'        => 'select',
            'options'	 => $grpoptions,
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirminvitetogroup', 'admin'),
            'value'       => get_string('invitetogroup', 'admin'),
        ),
    ),
));



$setlimitededitingform = pieform(array(
    'name'     => 'setlimitedediting',
    'class'    => 'bulkactionform setlimitedediting',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('setlimitedediting', 'admin') . ': ',
        ),
        'limitedediting'        => array(
            'type'         => 'checkbox',
            'class'        => 'bulkaction-title',
            'defaultvalue'        => false,
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirmsetlimitedediting', 'admin'),
            'value'       => get_string('update'),
        ),
    ),
));


// Delete users
$deleteform = pieform(array(
    'name'     => 'delete',
    'class'    => 'bulkactionform delete',
    'renderer' => 'oneline',
    'elements' => array(
        'users' => $userelement,
        'title'        => array(
            'type'         => 'html',
            'class'        => 'bulkaction-title',
            'value'        => get_string('deleteusers', 'admin') . ': ',
        ),
        'delete' => array(
            'type'        => 'submit',
            'confirm'     => get_string('confirmdeleteusers', 'admin'),
            'value'       => get_string('delete'),
        ),
    ),
));

$smarty = smarty();
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('users', $users);
$smarty->assign('changeauthform', $changeauthform);
$smarty->assign('suspendform', $suspendform);
$smarty->assign('resetprofileform', $resetprofileform);
$smarty->assign('archivepagesform', $archivepagesform);
$smarty->assign('setlimitededitingform', $setlimitededitingform);
$smarty->assign('resetdashboardform', $resetdashboardform);
$smarty->assign('addtogroupform', $addtogroupform);
$smarty->assign('invitetogroupform', $invitetogroupform);
$smarty->assign('deleteform', $deleteform);
$smarty->assign('probationform', $probationform);
$smarty->display('admin/users/bulk.tpl');

function changeauth_validate(Pieform $form, $values) {
    global $userids, $SESSION;

    // Make sure all users are members of the institution that
    // this authinstance belongs to.
    $authobj = AuthFactory::create($values['authinstance']);

    if ($authobj->institution != 'mahara') {
        $ph = $userids;
        $ph[] = $authobj->institution;
        $institutionusers = count_records_sql('
            SELECT COUNT(usr)
            FROM {usr_institution}
            WHERE usr IN (' . join(',', array_fill(0, count($userids), '?')) . ') AND institution = ?',
            $ph
        );
        if ($institutionusers != count($userids)) {
            $SESSION->add_error_msg(get_string('someusersnotinauthinstanceinstitution', 'admin'));
            $form->set_error('authinstance', get_string('someusersnotinauthinstanceinstitution', 'admin'));
        }
    }
}

function changeauth_submit(Pieform $form, $values) {
    global $users, $SESSION, $authinstances, $USER;

    $newauth = AuthFactory::create($values['authinstance']);
    $needspassword = method_exists($newauth, 'change_password');

    $updated = 0;
    $needpassword = 0;

    db_begin();

    $newauthinst = get_records_select_assoc('auth_instance', 'id = ?', array($values['authinstance']));
    if ($USER->get('admin') || $USER->is_institutional_admin($newauthinst[$values['authinstance']]->institution)) {
        foreach ($users as $user) {
            if ($user->authinstance != $values['authinstance']) {
                // Authinstance can be changed by institutional admins if both the
                // old and new authinstances belong to the admin's institutions
                $authinst = get_field('auth_instance', 'institution', 'id', $user->authinstance);
                if ($USER->get('admin') || $USER->is_institutional_admin($authinst)) {
                    // determine the current remoteusername
                    $current_remotename = get_field('auth_remote_user', 'remoteusername',
                                                    'authinstance', $user->authinstance, 'localusr', $user->id);
                    if (!$current_remotename) {
                        $current_remotename = $user->username;
                    }
                    // remove row if new authinstance row already exists to avoid doubleups
                    delete_records('auth_remote_user', 'authinstance', $values['authinstance'], 'localusr', $user->id);
                    insert_record('auth_remote_user', (object) array(
                        'authinstance'   => $values['authinstance'],
                        'remoteusername' => $current_remotename,
                        'localusr'       => $user->id,
                    ));
                }

                if ($user->haspassword && !$needspassword) {
                    $user->password = '';
                }
                else if ($needspassword && !$user->haspassword) {
                    $needpassword++;
                }

                $user->authinstance = $values['authinstance'];
                update_record('usr', $user, 'id');
                $updated++;
            }
        }
    }

    db_commit();

    if ($needpassword) {
        // Inform the user that they may need to reset passwords
        $SESSION->add_info_msg(get_string('bulkchangeauthmethodresetpassword', 'admin', $needpassword));
    }
    $message = get_string('bulkchangeauthmethodsuccess', 'admin', $updated);
    $form->reply(PIEFORM_OK, array('message' => $message));
}

function suspend_submit(Pieform $form, $values) {
    global $users, $SESSION;

    $suspended = 0;

    db_begin();

    foreach ($users as $user) {
        if (!$user->suspendedcusr) {
            suspend_user($user->id, $values['reason']);
            $suspended++;
        }
    }

    db_commit();

    $SESSION->add_ok_msg(get_string('bulksuspenduserssuccess', 'admin', $suspended));
    redirect('/admin/users/suspended.php');
}

function delete_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION;

    db_begin();

    foreach ($users as $user) {
        delete_user($user->id);
    }

    db_commit();

    $SESSION->add_ok_msg(get_string('bulkdeleteuserssuccess', 'admin', count($users)));
    redirect('/admin/users/search.php');
}

function resetprofile_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION;
	
    db_begin();

    foreach ($users as $user) {
    	    
    	$tmpuser = new User();
    	$tmpuser->find_by_id($user->id);
    	$tmpuser->delete_profile_view();
    	$tmpuser->install_profile_view();	
//        delete_user($user->id);
    }

    db_commit();

    $SESSION->add_ok_msg(get_string('bulkresetprofileuserssuccess', 'admin', count($users)));
    redirect('/admin/users/search.php');
}
function resetdashboard_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION;
	
    db_begin();

    foreach ($users as $user) {
    	    
    	$tmpuser = new User();
    	$tmpuser->find_by_id($user->id);
    	$tmpuser->delete_dashboard_view();
    	$tmpuser->install_dashboard_view();	
//        delete_user($user->id);
    }

    db_commit();

    $SESSION->add_ok_msg(get_string('bulkresetdashboarduserssuccess', 'admin', count($users)));
    redirect('/admin/users/search.php');
}


function archivepages_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION;
	

    foreach ($users as $user) {
    	    
        db_begin();
        execute_sql("
            UPDATE {view}
            SET submittedgroup = ?,
                submittedtime = current_timestamp,
                submittedhost = NULL,
                submittedstatus = 1
            WHERE type = 'portfolio' AND owner = ?",
            array($values['group'], $user->id)
        );


        ArtefactType::update_locked($user->id);
        db_commit();

//        delete_user($user->id);
    }

    $SESSION->add_ok_msg(get_string('bulkarchivepagesuserssuccess', 'admin', count($users)));
    redirect('/admin/users/search.php');
}

function setlimitedediting_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION;
	
    db_begin();

    foreach ($users as $user) {
    	    
    	$tmpuser = new User();
    	$tmpuser->find_by_id($user->id);
    	$tmpuser->set_account_preference('limitedediting',$values['limitedediting']);
//        delete_user($user->id);
    }

    db_commit();

    $SESSION->add_ok_msg(get_string('bulkarchivepagesuserssuccess', 'admin', count($users)));
    redirect('/admin/users/search.php');
}

function addtogroup_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION;
	
    db_begin();

    foreach ($users as $user) {
  		//need to check that they are not already a member!!
  		if(!group_is_member($user->id,$values['group'])){
  			group_add_user($values['group'],$user->id, null, 'internal',$values['copy_views'] );	
  		}    
    }

    db_commit();

    $SESSION->add_ok_msg(get_string('bulkresetdashboarduserssuccess', 'admin', count($users)));
    redirect('/admin/users/search.php');
}

function invitetogroup_submit(Pieform $form, $values) {
    global $users, $editable, $SESSION, $USER;
	$group = get_record_select('group', 'id = ? AND deleted = 0', array($values['group']), '*, ' . db_format_tsfield('ctime'));
	if (!$group) {
		throw new GroupNotFoundException(get_string('groupnotfound', 'group', GROUP));
	}
	    
	db_begin();
    foreach ($users as $user) {
    	
        group_invite_user($group, $user->id, $USER->get('id'), 'member', true);
    }
    db_commit();

    $SESSION->add_ok_msg(get_string('invitationssent', 'group', count($values['users'])));
    redirect('/admin/users/search.php');
}


function probation_submit(Pieform $form, $values) {
    global $SESSION, $users;

    $newpoints = ensure_valid_probation_points($values['probationpoints']);
    $paramlist = array($newpoints);

    $sql = '';
    foreach ($users as $user) {
        $paramlist[] = $user->id;
        $sql .= '?,';
    }
    // Drop the last comma
    $sql = substr($sql, 0, -1);

    execute_sql('update {usr} set probation = ? where id in (' . $sql . ')', $paramlist);

    $SESSION->add_ok_msg(get_string('bulkprobationpointssuccess', 'admin', count($users), $newpoints));
    redirect('/admin/users/search.php');
}
