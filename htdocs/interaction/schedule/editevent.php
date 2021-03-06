<?php
/**
 *
 * @package    mahara
 * @subpackage interaction-forum
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'groups/schedule');
define('SECTION_PLUGINTYPE', 'interaction');
define('SECTION_PLUGINNAME', 'schedule');
define('SECTION_PAGE', 'editevent');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('interaction', 'schedule');
require_once('group.php');
require_once(get_config('docroot') . 'interaction/lib.php');
require_once('pieforms/pieform.php');

$userid = $USER->get('id');
$eventid = param_integer('id', 0);
$view = param_integer('view',0);
$month = param_integer('month',0);
$year = param_integer('year',0);
$day = param_integer('day',0);

$returnto = param_integer('returnto', 0);
if ($returnto) {
	$return = '/interaction/schedule/index.php?group=' . $returnto.'&view='.$view;
	if($month && $year){
		$return .= '&month='.$month.'&year='.$year;
	}
}
else {
	$return = '/interaction/schedule/index.php?group=' . $schedule->groupid.'&view='.$view;
}

if ($eventid == 0) { // new topic
    $scheduleid = param_integer('schedule');
}
else { // edit topic
    $event = get_record_sql(
        'SELECT e.schedule, e.title, e.id AS eventid, e.description,'. db_format_tsfield('e.startdate','startdate'). ', '. db_format_tsfield('e.enddate','enddate').', e.location, e.attendance
        FROM {interaction_schedule_event} e
        INNER JOIN {interaction_instance} s ON (s.id = e.schedule AND s.deleted != 1)
        WHERE e.id = ?',
        array($eventid)
    );
    $scheduleid = $event->schedule;

    if (!$event) {
        throw new NotFoundException(get_string('cantfindevent', 'interaction.schedule', $eventid));
    }
}

$schedule = get_record_sql(
    'SELECT s.id, s.group AS groupid, s.title, g.name AS groupname, g.grouptype
    FROM {interaction_instance} s
    INNER JOIN {group} g ON (g.id = s.group AND g.deleted = 0)
    WHERE s.id = ?
    AND s.deleted != 1',
    array($scheduleid)
);

if (!$schedule) {
    throw new NotFoundException(get_string('cantfindschedule', 'interaction.schedule', $scheduleid));
}

$scheduleconfig = get_records_assoc('interaction_schedule_instance_config', 'schedule', $scheduleid, '', 'field,value');

define('GROUP', $schedule->groupid);
/*$membership = user_can_access_forum((int)$scheduleid);
$moderator = (bool)($membership & INTERACTION_FORUM_MOD);*/
$admintutor = (bool) group_get_user_admintutor_groups();
/* TODO:need to check wether we can add events
if (($scheduleconfig['createeventusers']->value == 'tutor' && !admintutor)) {
    throw new AccessDeniedException(get_string('cantaddevent', 'interaction.schedule'));
}
*/
if (!group_within_edit_window($schedule->groupid)) {
    throw new AccessDeniedException(get_string('cantaddevent', 'interaction.schedule'));
}

if (!$eventid) { // new topic
    define('TITLE', $schedule->title . ' - ' . get_string('addevent','interaction.schedule'));
}

else { // edit topic
    define('TITLE', $schedule->title . ' - ' . get_string('editevent','interaction.schedule'));
/* maybe we need something in here to do a similar thing so if you only just made it and edit it within 30 minutes
then it doesn't notify, otherwise it should notify users of changes being made. this is for later.
    // no record for edits to own posts with 30 minutes
    if (user_can_edit_post($topic->poster, $topic->ctime)) {
        $topic->editrecord = false;
        $timeleft = (int)get_config_plugin('interaction', 'forum', 'postdelay') - round((time() - $topic->ctime) / 60);
    }
    else if ($moderator) {
        $topic->editrecord = true;
    }
    else if (user_can_edit_post($topic->poster, $topic->ctime, $USER->get('id'), false)) {
        $SESSION->add_error_msg(get_string('postaftertimeout', 'interaction.forum', get_config_plugin('interaction', 'forum', 'postdelay')));
        redirect('/interaction/forum/topic.php?id=' . $topicid);
    }
    else {
        throw new AccessDeniedException(get_string('cantedittopic', 'interaction.forum'));
    }
    */
}

//date_default_timezone_set('Europe/London');
//TODO: get these numbers from config somewhere
if($day){
	$defaultstart = new DateTime($day."-".$month."-".$year);
}else{
	$defaultstart = date_create();
}
$defaultduration = date_create();
$defaultduration->setTime(0,30);
$defaultdurationtime = $defaultduration->getTimestamp();
$defaultstart->setTime(10,00);
$defaultstarttime = $defaultstart->getTimestamp();
$defaultend = $defaultstart->add(new DateInterval('PT3H'));
$defaultendtime = $defaultend->getTimestamp();

$numberoptions = array();
for ($i = 1; $i <= 52; $i++) {
	$numberoptions[$i] = $i;
}

$scheduleoptions = schedule_get_subgroup_schedules($schedule->groupid);

$editform = array(
    'name'     => 'editevent',
    'method'   => 'post',
    'autofocus' => isset($event) ? 'desc' : 'title',
    'elements' => array(
        'schedule' => array(
            'type'         => 'select',
            'options'         => $scheduleoptions,
            'title'        => get_string('schedule', 'interaction.schedule'),
            'defaultvalue' => isset($scheduleid) ? $scheduleid : null,
            'rules'        => array(
                'required' => true,
            )
        ),
        'title' => array(
            'type'         => 'text',
            'title'        => get_string('title', 'interaction.schedule'),
            'defaultvalue' => isset($event) ? $event->title : null,
            'rules'        => array(
                'required' => true,
                'maxlength' => 255
            )
        ),
        'description' => array(
            'type'         => 'wysiwyg',
            'title'        => get_string('description', 'interaction.schedule'),
            'rows'         => 8,
            'cols'         => 100,
            'defaultvalue' => isset($event) ? $event->description : null,
            'rules'        => array(
                'required'  => false,
                'maxlength' => 65536,
            ),
        ),
        'startdate' => array(
			'type'         => 'calendar',
                'caloptions' => array(
                    'showsTime'      => true,
                    'ifFormat'       => '%Y/%m/%d %H:%M',
                    'firstDay'		=> 1
                    ),
			'title'        => get_string('startdate', 'interaction.schedule'),
			'changehandler' => 'onDateChange()',
			'defaultvalue' => isset($event->startdate) ? $event->startdate : $defaultstarttime,
                'rules' => array(
                    'required' => true,
                    ),
        ),
        'enddate' => array(
			'type'         => 'calendar',
                'caloptions' => array(
                    'showsTime'      => true,
                    'ifFormat'       => '%Y/%m/%d %H:%M',
                    'firstDay'		=> 1
                    ),
			'title'        => get_string('enddate', 'interaction.schedule'),
			'changehandler' => null,
			'defaultvalue' => isset($event->enddate) ? $event->enddate : $defaultendtime,
                'rules' => array(
                    'required' => true,
                    ),
        ),
        'location' => array(
            'type'         => 'text',
            'title'        => get_string('location', 'interaction.schedule'),
            'defaultvalue' => isset($event) ? $event->location : null,
            'rules'        => array(
                'required' => false,
                'maxlength' => 255
            )
        ),
        'attendance' => array(
            'type'         => 'checkbox',
            'title'        => get_string('attendance', 'interaction.schedule'),
            'description'  => get_string('attendancedescription', 'interaction.schedule'),
            'defaultvalue' => isset($event) ? $event->attendance : !empty($scheduleconfig['attendance']->value),
        ),
        'createtimes' => array(
            'type'         => 'checkbox',
            'title'        => get_string('createtimes', 'interaction.schedule'),
            'description'  => get_string('repeatdescription', 'interaction.schedule'),
            'defaultvalue' => false,
        ),
		'duration' => array(
			'type'         => 'time',
			'title'        => get_string('duration', 'interaction.schedule'),
			'time'			=> true,
			'defaultvalue' => $defaultdurationtime,
			'class'			=> 'hidden',
			),
		'numberofgroups' => array(
			'type'         => 'select',
			'title'        => get_string('noofgroups', 'interaction.schedule'),
			'options'      => array_merge(array(0=>'Individual'),$numberoptions),
			'defaultvalue' => 0,
			'class'			=> 'hidden',
			),
		'numberoftutors' => array(
			'type'         => 'select',
			'title'        => get_string('nooftutors', 'interaction.schedule'),
			'options'      => $numberoptions,
			'defaultvalue' => 1,
			'class'			=> 'hidden',
			),

        'repeat' => array(
            'type'         => 'checkbox',
            'class'			=> isset($event) ? 'hidden' : '',
            'title'        => get_string('repeat', 'interaction.schedule'),
            'description'  => get_string('repeatdescription', 'interaction.schedule'),
            'defaultvalue' => false,
        ),
		'numberoftimes' => array(
			'type'         => 'select',
			'title'        => get_string('numberoftimes', 'interaction.schedule'),
			'options'      => $numberoptions,
			'defaultvalue' => 1,
			'class'			=> 'hidden',
			),
		'howoften' => array(
			'type'         => 'select',
			'title'        => get_string('howoften', 'interaction.schedule'),
			'options'      => array(1=>'Day', 2=>'Two Days',7=>'Week',31=>'Month'),
			'defaultvalue' => 7,
			'class'			=> 'hidden',
			),
        'submit'   => array(
            'type'  => 'submitcancel',
            'value'       => array(
                isset($topic) ? get_string('save') : get_string('post','interaction.schedule'),
                get_string('cancel')
            ),
            'goto'      => get_config('wwwroot') . $return
        ),
        'event' => array(
            'type' => 'hidden',
            'value' => isset($event) ? $event->eventid : false
        ),
        'view' => array(
            'type' => 'hidden',
            'value' => $view
        ),
        'editrecord' => array(
            'type' => 'hidden',
            'value' => isset($topic) ? $topic->editrecord : false
        )
    ),
);


$editform = pieform($editform);

function addevent_validate(Pieform $form, $values) {
}

function editevent_validate(Pieform $form, $values) {
}


function create_times($groupid, $starttime, $duration, $numberoftutors=1, $numberofgroups=null){
	$tempdate = $starttime;
	$starttime = date_create("@$tempdate");
	$endstr = '';
	$tempdate = $duration;
	$durationstart = date_create("@$tempdate");
	$durationend = date_create("@$tempdate");
	$durationstart->setTime(00,00);
	$duration = $durationstart->diff($durationend);
	//Get the members names as an array
	if($members = group_get_member_names($groupid,array('member'))){
		$totalmembers = count($members);
//		error_log('members:' . $totalmembers);
//		error_log('noofgroups:' . $totalmembers);
		if(!isset($numberofgroups)){
			$numberofgroups = $totalmembers;
		}
//		error_log('noofgroups:' . $totalmembers);
		//divide the number of members by number of groups to get the average per group
		$pergroup = floor($totalmembers/$numberofgroups);
		$remainder = $totalmembers%$numberofgroups;
		//round this up
		$numberofslots = round($numberofgroups/$numberoftutors,0);
		$currpos = 0;
		$groupno = 0;
		//for 1 to number of groups / number of tutors
		for($i = 0; $i < $numberofslots; $i++){
			$endstr .= '<p>Time : '.format_date($starttime->getTimestamp(),'strftimetime').'</p>';
			//for 1 to number of tutors
			for($j = 1; $j <= $numberoftutors; $j++){
				$groupno++;
				//add the time to string
				if($numberofgroups > 1){
					$endstr .= '<p>Group '.$groupno.' </p><p>';
				}else{
					$endstr .= '<p>';
				}
				//for currentpos to average per group
				for($k = 1; ($k <= $pergroup + ($remainder > 0 ? 1:0) && $currpos < $totalmembers ); $k++){ 
					//add the members name
					$endstr .= $members[$currpos].'; <br>';
					//increase currentpos
					$currpos++;
				}
				$remainder--;
			}
			//increase time by duration
			$starttime->add($duration);
			$endstr .='</p>';
		}
	}
	return $endstr;
}
/*
function addevent_submit(Pieform $form, $values) {
    global $USER, $SESSION, $schedule;
    $scheduleid = param_integer('schedule');
    $view = param_integer('view',0);
	$month = param_integer('month',0);
	$year = param_integer('year',0);

	$returnto = param_integer('returnto', 0);
	if ($returnto) {
		$return = '/interaction/schedule/index.php?group=' . $returnto.'&view='.$view;
		if($month && $year){
			$return .= '&month='.$month.'&year='.$year;
		}
	}
	else {
		$return = '/interaction/schedule/index.php?group=' . $schedule->groupid.'&view='.$view;
	}
	$tutorialtimes = '';
	if($values['createtimes']){
		$tutorialtimes = create_times($schedule->groupid, $values['startdate'], $values['duration'], $values['numberoftutors'], $values['numberofgroups']);
	}


    db_begin();
    $eventid = insert_record(
        'interaction_schedule_event',
		array(
			'schedule' => $values['schedule'],
			'title' => $values['title'],
			'description' => $values['description'].$tutorialtimes,
			'startdate' => db_format_timestamp($values['startdate']),
			'enddate' => db_format_timestamp($values['enddate']),
			'location' => $values['location'],
			'attendance' => $values['attendance'],
		), 'id', true
    );
    if($values['repeat'] == true){
		$tempdate = $values['startdate'];
		$timez = new DateTimeZone(date_default_timezone_get());
		$newstartdate = date_create("@$tempdate");
		$newstartdate->setTimezone($timez);
		$tempdate = $values['enddate'];
		$newenddate = date_create("@$tempdate");
		$newenddate->setTimezone($timez);
    	$diff = new DateInterval('P1D');
		switch ($values['howoften']) {
			case 1:
				$diff = new DateInterval('P1D');
				break;
			case 2:
				$diff = new DateInterval('P2D');
				break;
			case 7:
				$diff = new DateInterval('P7D');
				break;
			case 31:
				$diff = new DateInterval('P3M');
				break;
		}
    	$numberoftimes = $values['numberoftimes'];
    	$every = $values['howoften'];
    	for($i = 1; $i <= $numberoftimes; $i++){
    		$newstartdate = date_add($newstartdate,$diff);
    		$newenddate = date_add($newenddate,$diff);
    		
			$eventid = insert_record(
				'interaction_schedule_event',
				array(
					'schedule' => $values['schedule'],
					'title' => $values['title'],
					'description' => $values['description'],
					'startdate' => db_format_timestamp($newstartdate),
					'enddate' => db_format_timestamp($newenddate),
					'location' => $values['location'],
					'attendance' => $values['attendance'],
				), 'id', true
			);
    		
    	}
    }
    db_commit();
    $SESSION->add_ok_msg(get_string('addeventsuccess', 'interaction.schedule'));
    redirect($return);
}
*/

function editevent_submit(Pieform $form, $values) {

//TODO: some logic in here to see if the date/time has changed and notify people of the change
// Maybe there should be a check box to say notify when you are editing?   
    global $SESSION, $USER, $event, $schedule;
    $scheduleid = param_integer('schedule');
    $eventid = param_integer('id');
	$view = param_integer('view',0);
	$month = param_integer('month',0);
	$year = param_integer('year',0);
	$adding = $eventid;
	$returnto = param_integer('returnto', 0);
	if ($returnto) {
		$return = '/interaction/schedule/index.php?group=' . $returnto.'&view='.$view;
		if($month && $year){
			$return .= '&month='.$month.'&year='.$year;
		}
	}
	else {
		$return = '/interaction/schedule/index.php?group=' . $schedule->groupid.'&view='.$view;
	}
	$tutorialtimes = '';
	if($values['createtimes']){
		$tutorialtimes = create_times($schedule->groupid, $values['startdate'], $values['duration'], $values['numberoftutors'], $values['numberofgroups']);
	}

    db_begin();
    // check the post content actually changed
    // otherwise topic could have been set as sticky/closed
    if(!$eventid){
		$eventid = insert_record(
			'interaction_schedule_event',
			array(
				'schedule' => $values['schedule'],
				'title' => $values['title'],
				'description' => $values['description'].$tutorialtimes,
				'startdate' => db_format_timestamp($values['startdate']),
				'enddate' => db_format_timestamp($values['enddate']),
				'location' => $values['location'],
				'attendance' => $values['attendance'],
			), 'id', true
		);
	if($values['repeat'] == true){
		$tempdate = $values['startdate'];
		$timez = new DateTimeZone(date_default_timezone_get());
		$newstartdate = date_create("@$tempdate");
		$newstartdate->setTimezone($timez);
		$tempdate = $values['enddate'];
		$newenddate = date_create("@$tempdate");
		$newenddate->setTimezone($timez);
    	$diff = new DateInterval('P1D');
		switch ($values['howoften']) {
			case 1:
				$diff = new DateInterval('P1D');
				break;
			case 2:
				$diff = new DateInterval('P2D');
				break;
			case 7:
				$diff = new DateInterval('P7D');
				break;
			case 31:
				$diff = new DateInterval('P3M');
				break;
		}
    	$numberoftimes = $values['numberoftimes'];
    	$every = $values['howoften'];
    	for($i = 1; $i <= $numberoftimes; $i++){
    		$newstartdate = date_add($newstartdate,$diff);
    		$newenddate = date_add($newenddate,$diff);
    		
			$eventid = insert_record(
				'interaction_schedule_event',
				array(
					'schedule' => $values['schedule'],
					'title' => $values['title'],
					'description' => $values['description'],
					'startdate' => db_format_timestamp($newstartdate),
					'enddate' => db_format_timestamp($newenddate),
					'location' => $values['location'],
					'attendance' => $values['attendance'],
				), 'id', true
			);
    		
    	}
    }


    }else{
		update_record(
			'interaction_schedule_event',
			array(
				'schedule' => $values['schedule'],
				'title' => $values['title'],
				'description' => $values['description'].$tutorialtimes,
				'startdate' => db_format_timestamp($values['startdate']),
				'enddate' => db_format_timestamp($values['enddate']),
				'location' => $values['location'],
				'attendance' => $values['attendance'],
			),
			array('id' => $values['event'])
		);
	}

    db_commit();
    if($adding){
		$SESSION->add_ok_msg(get_string('addeventsuccess', 'interaction.schedule'));
	}else{
		$SESSION->add_ok_msg(get_string('editeventsuccess', 'interaction.schedule'));
	}
    redirect($return);
/*    if ($returnto) {
        redirect('/interaction/schedule/index.php?group=' . $returnto.'&view='.$view);
    }
    else {
        redirect('/interaction/schedule/index.php?group=' . $schedule->groupid.'&view='.$view);
    }*/
}

$javascript = <<<EOF
function addchangeables(){
    var s = $('editevent_repeat');
    var m = $('editevent_numberoftimes_container');
    var t = $('editevent_howoften_container');
    var u = $('editevent_numberoftimes');
    var v = $('editevent_howoften');
    if (!m) {
        return;
    }
    if (s.checked == true) {
        removeElementClass(m, 'hidden');
        removeElementClass(t, 'hidden');
        removeElementClass(u, 'hidden');
        removeElementClass(v, 'hidden');
    }
    else {
        addElementClass(m, 'hidden');
        addElementClass(t, 'hidden');
        addElementClass(u, 'hidden');
        addElementClass(v, 'hidden');
    }
}
function addchangeables2(){
    var a = $('editevent_createtimes');
    var b = $('editevent_duration_container');
    var c = $('editevent_numberofgroups_container');
    var d = $('editevent_numberofgroups');
    var e = $('editevent_numberoftutors_container');
    var f = $('editevent_numberoftutors');
    if (!b) {
        return;
    }
    if (a.checked == true) {
        removeElementClass(b, 'hidden');
        removeElementClass(c, 'hidden');
        removeElementClass(d, 'hidden');
        removeElementClass(e, 'hidden');
        removeElementClass(f, 'hidden');
    }
    else {
        addElementClass(b, 'hidden');
        addElementClass(c, 'hidden');
        addElementClass(d, 'hidden');
        addElementClass(e, 'hidden');
        addElementClass(f, 'hidden');
    }
}
addLoadEvent(function() {
    connect('editevent_repeat', 'onclick', addchangeables);
    connect('editevent_createtimes', 'onclick', addchangeables2);
});


EOF;




$smarty = smarty(array('interaction/schedule/js/edit.js'));
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign('heading', $schedule->groupname);
$smarty->assign('subheading', TITLE);
$smarty->assign('eventid', $eventid);
$smarty->assign('groupid', $schedule->groupid);
$smarty->assign('returnto', $returnto);
$smarty->assign('view', $view);
$smarty->assign('month', $month);
$smarty->assign('year', $year);
$smarty->assign('editform', $editform);
$smarty->display('interaction:schedule:editevent.tpl');
