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

require_once('activity.php');
require_once('group.php');



function get_schedule_list($groupid){
	$schedule = array();
    if (is_numeric($groupid) && $groupid > 0)
    {
        $schedule = get_records_sql_array(
            "SELECT s.id, s.title, s.description, c.value as color
            FROM {interaction_instance} s
            LEFT JOIN {interaction_schedule_instance_config} c ON (c.schedule = s.id) AND c.field = 'color'
            WHERE s.group = ? AND s.plugin = 'schedule' 
            AND s.deleted != 1
            ",
            array($groupid)
        );
    }

    return $schedule;

}

function schedule_get_schedule($scheduleid){
	$schedule = array();
        $schedule = get_records_sql_array(
            'SELECT s.id, s.title, s.description
            FROM {interaction_instance} s
            INNER JOIN {interaction_schedule_instance_config} c ON (c.schedule = s.id)
            WHERE s.id = ?
            AND s.deleted != 1
            ORDER BY CHAR_LENGTH(c.value), c.value',
            array($scheduleid)
        );

    return $schedule;

}


function schedule_get_event($eventid){
	$events = array();
	
        $events = get_records_sql_array(
            'SELECT e.schedule, e.title, e.id, e.description,e.startdate as bob, '. db_format_tsfield('e.startdate','startdate'). ', '. db_format_tsfield('e.enddate','enddate').', e.location, e.attendance
	FROM {interaction_schedule_event} e
	WHERE e.id = ? AND e.deleted = 0
	ORDER BY e.startdate, e.enddate',
            array($eventid)
        );
 	 
    return $events;

}

function schedule_get_user_events($mindate,$maxdate){
	global $USER;
	$events = array();
	
        $events = get_records_sql_array(
            "SELECT se.schedule, 
            		se.title, 
            		se.id, 
            		se.description, 
            		". db_format_tsfield("se.startdate","startdate"). ", 
            		". db_format_tsfield("se.enddate","enddate").", 
            		se.location, 
            		se.attendance,
            		i.title as scheduletitle,
            		g.name as groupname,
            		g.id as groupid,
					sc.value as color,
					DATEDIFF(se.enddate,se.startdate) AS longerthanaday, 
					i.title as scheduletitle,
					i.group as eventgroup            		
             FROM {group_member} gm
             JOIN {group} g on gm.group = g.id
			JOIN {interaction_instance} i on i.group = gm.group
			JOIN {interaction_schedule_event} se on i.id = se.schedule
			LEFT JOIN {interaction_schedule_instance_config} sc on se.schedule = sc.schedule AND sc.field = 'color' 
			WHERE gm.member = ? AND se.deleted = 0 AND se.startdate > ? AND se.startdate <= ? 
			ORDER BY se.startdate, se.enddate",
            array($USER->get('id'),$mindate->format('Y-m-d'),$maxdate->format('Y-m-d'))
        );
 	//AND se.startdate > ?  , Date('Y-m-d')   and i.plugin="schedule" AND se.deleted = 0 $USER->get('id')
 	//se.title, se.id, se.description, '. db_format_tsfield('se.startdate','startdate'). ', '. db_format_tsfield('se.enddate','enddate').', se.location, se.attendance
 
    return $events;

}

function schedule_get_all_groupevents($groupid,$mindate=null,$maxdate=null){
	global $USER;
	$events = array();
	$options = array();
	$sql = "SELECT e.schedule, 
            	e.title, 
            	e.id, 
            	e.description, 
            	". db_format_tsfield("e.startdate","startdate"). ", 
            	". db_format_tsfield("e.enddate","enddate").", 
            	e.location, 
            	e.attendance, 
            	sc.value as color,
            	DATEDIFF(e.enddate,e.startdate) AS longerthanaday, 
            	s.title as scheduletitle,
            	s.group as eventgroup,
            	(SELECT gh.depth as horder from `group_hierarchy` gh
					Where gh.child = g.id ORDER by gh.depth desc LIMIT 1) as horder
			FROM {interaction_schedule_event} e
			LEFT JOIN {interaction_schedule_instance_config} sc on e.schedule = sc.schedule AND sc.field = 'color' 
			JOIN {interaction_instance} s on e.schedule = s.id
			JOIN {group} g on s.group = g.id
			WHERE s.plugin = 'schedule' AND e.deleted = 0
			AND s.group in ";
		//if the user is a member of staff then show all events in all groups and sub groups
		if($USER->is_institutional_staff()){
			$sql .="	
				(
					SELECT DISTINCT gh.child as group_id from {group_hierarchy} gh where gh.parent = ?
						UNION
					SELECT DISTINCT gh.parent as group_id from {group_hierarchy} gh where gh.child = ?
				) ";
				$options = array($groupid, $groupid);
		}else{
		//otherwise only show sub grop events for groups the user is in. but still show all events for groups going up the hierarchy
			$sql .="	
				(
					SELECT DISTINCT gh.child as group_id from {group_hierarchy} gh 
					JOIN {group_member} gm on gh.child = gm.group where gh.parent = ? AND gm.member = ?
						UNION
					SELECT DISTINCT gh.parent as group_id from {group_hierarchy} gh where gh.child = ?
				) ";
				$options = array($groupid,$USER->get('id'),$groupid);
		}
		if($mindate){
			$sql .=" AND e.startdate >= '".db_format_timestamp($mindate)."'";
		}
		if($maxdate){
			$sql .=" AND e.enddate <= '".db_format_timestamp($maxdate)."'";
		}
			
		$sql .=	" ORDER BY e.startdate, horder, e.enddate";
        $events = get_records_sql_array($sql,$options);
/*
SELECT DISTINCT gh.child as group_id from `group_hierarchy` gh join `group_member` gm on gh.child = gm.group where gh.parent = 3 and gm.member = 14
					UNION
				SELECT DISTINCT gh.parent as group_id from `group_hierarchy` gh join `group_member` gm on gh.child = gm.group where gh.child = 3 and gm.member = 14

			JOIN {group_hierarchy} gh on s.group = gh.child
			WHERE s.plugin = 'schedule' AND e.deleted = 0
			AND gh.parent = ? 
			ORDER BY e.startdate, e.enddate",
*/ 	
	return $events;	
}

//looks for the editwindowstartdate of group falling back on the hierarchy 
//so that it will get its parent or grandparents etc as default
//if finds the first one in the hierarchy
function schedule_get_groupstartdate($groupid){
	$sql="SELECT g.id, ". db_format_tsfield("g.editwindowstart","startdate"). ", gh.depth 
			FROM {group} g join {group_hierarchy} gh on g.id = gh.parent 
			WHERE gh.child = ? 
			AND g.editwindowstart is not null order by gh.depth 
			LIMIT 1";
	return get_records_sql_array($sql,array($groupid));
}

function schedule_get_groupenddate($groupid){
	$sql="SELECT g.id, ". db_format_tsfield("g.editwindowend","enddate"). ", gh.depth 
			FROM {group} g join {group_hierarchy} gh on g.id = gh.parent 
			WHERE gh.child = ? 
			AND g.editwindowend is not null order by gh.depth 
			LIMIT 1";
	return get_records_sql_array($sql,array($groupid));
}

function schedule_get_user_startdate($userid){
	$sql="SELECT g.id, ". db_format_tsfield("g.editwindowstart","startdate"). ", gh.depth 
			FROM {group} g join {group_hierarchy} gh on g.id = gh.parent 
			WHERE gh.child IN (SELECT gm.group from `group_member` gm where gm.member = ?)
			AND g.editwindowstart is not null order by gh.depth desc
			LIMIT 1";
	return get_records_sql_array($sql,array($userid));
}



function schedule_events_per_day($events,$groupid,$startdate=null){
	//based on hiearchichal search for the starteditwindow time
	//we can work out which week that lies in and use that as the start of the year
	if(!$startdate){
		$startdate = schedule_get_groupstartdate($groupid);
		if($startdate){
			$startdate = $startdate[0]->startdate;
		}
	}
	//we need to know how many weeks there are in this year
	$weeksinyear = intval(date('W',mktime(0,0,0,12,31,date('o',time()))));
	if($weeksinyear == 1){
		$weeksinyear = 52;
	}
	//current year
	$currentyear = date('o',time());
	$startweek = 1;
	if($startdate){	
		//if the startdate is set then override the defaults
		$weeksinyear = intval(date('W',mktime(0,0,0,12,31,date('o',$startdate))));
		$currentyear = date('o',$startdate);
		if($weeksinyear == 1){
			$weeksinyear = 52;
		}
		$startweek = intval(date('W',$startdate));
	}
	//work out the monday of the starting week
	$startdate = strtotime($currentyear.'W'.str_pad($startweek, 2, "0", STR_PAD_LEFT));
	$startdate = $startdate + (60*60*2);
	$weeksanddays = array();
	$days = 0;
	//there is possibly a 53 week year so wee need to know that...
	for($i = 1; $i <= 52; $i++){
		$weeksanddays[$i] = array();
		for($j = 1; $j <= 7; $j++){
			$date = strtotime("+".$days." days", $startdate);
			$days++;
			$weeksanddays[$i][$j] = array('date'=>$date,'events'=>array());
		}
	}
	foreach($events as $event){
		$week = intval(date('W',intval($event->startdate)));
		if($week >= $startweek){
			$week = $week - $startweek + 1;
		}else{
			$week = $week + $weeksinyear - $startweek + 1;
		}
		$day = intval(date('N',intval($event->startdate)));
		$weeksanddays[$week][$day]['events'][] = $event;
		if($event->longerthanaday){
			$startdate = new DateTime(date('Y-m-d',intval($event->startdate)));
			$enddate = new DateTime(date('Y-m-d',intval($event->enddate)));
			$dDiff = $startdate->diff($enddate);
  			$noofdays = $dDiff->days;
  			$j = 0;
  			for($i = 1; $i <= $noofdays; $i++){
  				$day++;
  				if($day > 7){
  					$day = 1;
  					$week++;
  				}
				$weeksanddays[$week][$day]['events'][] = $event;
  			}
  			
		}
	}
	return $weeksanddays;
}

function schedule_get_start_and_enddates($month,$year,$noweeks){
	//this could be one line of code but it is pretty unreadable
	$startdate = strtotime(intval(date('o',mktime(0,0,0,$month,1,$year))).'W'.str_pad(intval(date('W',mktime(0,0,0,$month,1,$year))), 2, "0", STR_PAD_LEFT))+(60*60*2);
	//first of the month specified
/*	$startmonthweek  = mktime(0,0,0,$month,1,$year);
	//get the iso week number of the 1st
	$startweek = intval(date('W',$startmonthweek));
	// work out the monday
	$startdate = strtotime(intval(date('o',$startmonthweek)).'W'.str_pad($startweek, 2, "0", STR_PAD_LEFT));
	//this makes sure the date is in the morning in case of summer time
	$startdate = $startdate + (60*60*2);*/
	$enddate = $date = strtotime("+".$noweeks." weeks", $startdate);
	return array($startdate,$enddate);
}

function schedule_events_per_cal_day($events,$groupid, $month,$year){
	//this function gets all events for a calendar month
	//we need to figure out how many weeks are in the month this should be either 4 or 5 not sure if it can be 6
	//there will always be 6 weeks shown and the days fit in based on that
	//we need to work out the first day of the month
	//then we need to calculate the monday of that week
	//then we need to populate 6 weeks worth of data.
	list($startdate,$enddate) = schedule_get_start_and_enddates($month,$year,6);
	$startweek = intval(date('W',$startdate));

	$weeksanddays = array();
	$days = 0;
	//build the array with the dates in it
	for($i = 1; $i <= 6; $i++){
		$weeksanddays[$i] = array();
		for($j = 1; $j <= 7; $j++){
			$date = strtotime("+".$days." days", $startdate);
			$days++;
			$weeksanddays[$i][$j] = array('date'=>$date,'events'=>array());
		}
	}
	foreach($events as $event){
		$week = intval(date('W',intval($event->startdate)));
//		var_dump('<br>'.$week);
		//calculate relative week we populate weeks 1 - 6 January
		if($startweek >=52){
			//so the year starts in week 52 or 53 so all events in week one need to be in the second slot onwards
			if($week < 52){
				$week = $week - 1 + 2;
			}else{
				//if it is in the last week the put it in the first week
				$week = $week - $startweek + 1;
			}
		}else{
				$week = $week - $startweek + 1;
/*			if($week >= 52){
				$week = 1;
			}else{
				$week = $week - $startweek + 1;
			}*/
			if($week < 0){
				$week = 6;
			}
		}
		$day = intval(date('N',intval($event->startdate)));
		$weeksanddays[$week][$day]['events'][] = $event;
		if($event->longerthanaday){
			$startdate = new DateTime(date('Y-m-d',intval($event->startdate)));
			$enddate = new DateTime(date('Y-m-d',intval($event->enddate)));
			$dDiff = $startdate->diff($enddate);
  			$noofdays = $dDiff->days;
  			$j = 0;
  			for($i = 1; $i <= $noofdays; $i++){
  				$day++;
  				if($day > 7){
  					$day = 1;
  					$week++;
  				}
				$weeksanddays[$week][$day]['events'][] = $event;
  			}
  			
		}
	}
	return $weeksanddays;
}


function get_schedule_events($schedule){
	$events = array();
	
        $events = get_records_sql_array(
            "SELECT e.schedule, 
            	e.title, 
            	e.id, 
            	e.description, 
            	". db_format_tsfield("e.startdate","startdate"). ", 
            	". db_format_tsfield("e.enddate","enddate").", 
            	e.location, 
            	e.attendance, 
            	sc.value as color, 
            	DATEDIFF(e.enddate,e.startdate) AS longerthanaday, 
            	s.title as scheduletitle

	FROM {interaction_schedule_event} e
	LEFT JOIN {interaction_schedule_instance_config} sc on e.schedule = sc.schedule AND sc.field = 'color' 
	JOIN {interaction_instance} s on e.schedule = s.id
	WHERE e.schedule = ? AND e.deleted = 0
	ORDER BY e.startdate, e.enddate",
            array($schedule->id)
        );
 	 
    return $events;

}


function schedule_get_attendance_events($schedule){
	$events = array();
	
        $events = get_records_sql_array(
            'SELECT e.schedule, e.title, e.id, e.description,e.startdate as bob, '. db_format_tsfield('e.startdate','startdate'). ', '. db_format_tsfield('e.enddate','enddate').', e.location, e.attendance
	FROM {interaction_schedule_event} e
	WHERE e.schedule = ? AND e.attendance AND e.deleted = 0
	ORDER BY e.startdate, e.enddate',
            array($schedule)
        );
 	 
    return $events;

}

function schedule_get_group_attendance_dates($groupid){
	$events = array();
	
        $events = get_records_sql_array(
            'SELECT date(e.startdate) as title, '. db_format_tsfield('e.startdate','startdate'). ', count( e.attendance) as attendnacecount
			FROM {interaction_schedule_event} e
			JOIN {interaction_instance} i on e.schedule = i.id
			WHERE e.attendance AND e.deleted = 0 AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
			GROUP BY date(e.startdate)',
            array($groupid)
        );
 	 
    return $events;

}

function schedule_get_group_attendance_events($groupid){
	$events = array();
	
        $events = get_records_sql_array(
            'SELECT e.schedule, e.title, e.id, e.description,'. db_format_tsfield('e.startdate','startdate'). ', '. db_format_tsfield('e.enddate','enddate').', e.location, e.attendance
	FROM {interaction_schedule_event} e
	JOIN {interaction_instance} i on e.schedule = i.id
	WHERE e.attendance AND e.deleted = 0 AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
	ORDER BY e.startdate, e.enddate',
            array($groupid)
        );
 	 
    return $events;

}


function schedule_get_group_attendance($groupid, $userid){
	$attendance = array();
	
        $attendance = get_records_sql_array(
            'SELECT e.id, att.attendance, att.excuse, att.attachment
			FROM 	{interaction_schedule_event} e 
			JOIN	{interaction_instance} i on e.schedule = i.id
			LEFT JOIN (
			SELECT a.event, a.attendance, a.excuse, a.attachment 
			FROM	{interaction_schedule_attendance} a
			WHERE
			a.user = ?) as att

			on att.event = e.id 
			WHERE e.deleted = 0 AND e.attendance
			AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
			ORDER BY e.startdate, e.enddate',
            array($userid,$groupid)
        );
 	 
    return $attendance;

}


function schedule_get_schedule_attendance($scheduleid, $userid){
	$attendance = array();
	
        $attendance = get_records_sql_array(
            'SELECT i.id, att.attendance, att.excuse, att.attachment
			FROM 	{interaction_schedule_event} i LEFT JOIN (
			SELECT a.event, a.attendance, a.excuse, a.attachment 
			FROM	{interaction_schedule_attendance} a
			WHERE
			a.user = ?) as att

			on att.event = i.id WHERE i.schedule = ? AND i.deleted = 0 AND i.attendance
			ORDER BY i.startdate, i.enddate',
            array($userid,$scheduleid)
        );
 	 
    return $attendance;

}

function schedule_get_user_group_attendance($userid,$groupid){
	$attendance = array();
//	var_dump($userid);
//	var_dump($groupid);
	$percentages = array();
        $attendance = get_records_sql_array(
            'SELECT i.id, i.title, ii.group, ii.title as scheduletitle, i.location, ii.description, ii.id as schedule, att.attendance, att.excuse, att.attachment ,'. db_format_tsfield('i.startdate','startdate'). '
			FROM 	{interaction_schedule_event} i LEFT JOIN (
			SELECT a.event, a.attendance, a.excuse, a.attachment 
			FROM	{interaction_schedule_attendance} a
			WHERE
			a.user = ?) as att

			on att.event = i.id
                        JOIN {interaction_instance} ii on i.schedule = ii.id                        
                        WHERE  i.deleted = 0 AND i.attendance  AND ii.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
			ORDER BY i.startdate, i.enddate',
            array($userid,$groupid)
        );
        if(!$attendance){
        	$attendance = array();
        }
        
        $percentages = get_records_sql_array("SELECT 'present' as attendance, count(a.id) as total 
			FROM 	{interaction_schedule_attendance} a
                        JOIN	{interaction_schedule_event} e on a.event = e.id
                        JOIN	{interaction_instance} i on i.id = e.schedule
			WHERE
			a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 1 AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
		UNION
		SELECT 'late' as attendance, count(a.id) as total 
					FROM 	{interaction_schedule_attendance} a
								JOIN	{interaction_schedule_event} e on a.event = e.id
                        JOIN	{interaction_instance} i on i.id = e.schedule
					WHERE
					a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 2 AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
		UNION
		SELECT 'absent' as attendance, count(a.id) as total 
					FROM 	{interaction_schedule_attendance} a
								JOIN	{interaction_schedule_event} e on a.event = e.id
                        JOIN	{interaction_instance} i on i.id = e.schedule
					WHERE
					a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 3 AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)
		UNION
		SELECT 'excused' as attendance, count(a.id) as total 
			FROM 	{interaction_schedule_attendance} a
                        JOIN	{interaction_schedule_event} e on a.event = e.id
                        JOIN	{interaction_instance} i on i.id = e.schedule
			WHERE
			a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 4 AND i.group IN (SELECT child FROM {group_hierarchy} WHERE parent = ?)",
			array($userid,$groupid,$userid,$groupid,$userid,$groupid,$userid,$groupid)
		);
//		var_dump($percentages);
 //	 	bob::bob();

	$total = 0;
	foreach($percentages as $percent){
		$total += $percent->total;
	}
	foreach($percentages as $percent){
		if($total > 0){
			$percent->percentage = intval(round(($percent->total / $total)*100));
		}else{
			$percent->percentage = 0;
		}
	}
	$percentages['total'] = $total;


    return array($attendance,$percentages);}

function schedule_get_user_attendance($userid){
	$attendance = array();
	$percentages = array();
        $attendance = get_records_sql_array(
            'SELECT i.id, i.title, ii.group, ii.title as scheduletitle, i.location, ii.description, ii.id as schedule, att.attendance, att.excuse, att.attachment ,'. db_format_tsfield('i.startdate','startdate'). '
			FROM 	{interaction_schedule_event} i LEFT JOIN (
			SELECT a.event, a.attendance, a.excuse, a.attachment 
			FROM	{interaction_schedule_attendance} a
			WHERE
			a.user = ?) as att

			on att.event = i.id
                        JOIN {interaction_instance} ii on i.schedule = ii.id
                        JOIN {group_member} gm on ii.group = gm.group AND gm.member = ? 
                        
                        WHERE  i.deleted = 0 AND i.attendance
			ORDER BY i.startdate, i.enddate',
            array($userid,$userid)
        );
        
        $percentages = get_records_sql_array("SELECT 'present' as attendance, count(a.id) as total 
			FROM 	{interaction_schedule_attendance} a
                        JOIN	{interaction_schedule_event} e on a.event = e.id
			WHERE
			a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 1
		UNION
		SELECT 'late' as attendance, count(a.id) as total 
					FROM 	{interaction_schedule_attendance} a
								JOIN	{interaction_schedule_event} e on a.event = e.id
					WHERE
					a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 2
		UNION
		SELECT 'absent' as attendance, count(a.id) as total 
					FROM 	{interaction_schedule_attendance} a
								JOIN	{interaction_schedule_event} e on a.event = e.id
					WHERE
					a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 3
		UNION
		SELECT 'excused' as attendance, count(a.id) as total 
			FROM 	{interaction_schedule_attendance} a
                        JOIN	{interaction_schedule_event} e on a.event = e.id
			WHERE
			a.user = ? AND e.deleted = 0 AND e.attendance AND a.attendance = 4",
			array($userid,$userid,$userid,$userid)
		);
 	 	$total = 0;
	foreach($percentages as $percent){
		$total += $percent->total;
	}
	foreach($percentages as $percent){
		if($total > 0){
			$percent->percentage = intval(round(($percent->total / $total)*100));
		}else{
			$percent->percentage = 0;
		}
	}
	$percentages['total'] = $total;

    return array($attendance,$percentages);

}



function schedule_get_event_attendance($eventid, $userid){
	$attendance = array();
	
        $attendance = get_records_sql_array(
            'SELECT *
	FROM {interaction_schedule_attendance} a
	WHERE a.event = ? AND a.user = ?',
            array($eventid,$userid)
        );
 	 
    return $attendance;

}

function schedule_update_attendance($eventid, $userid, $attendance, $excuse=null, $attachment=null){
	//TODO: Some sort of security check in here. Anyone might be able to hack the json script and change their attendance.
/*	global $USER, $group;
	$role = group_user_access($group->id);
	if(!group_role_can_moderate_views($group->id, $role)){
		throw new GroupAccessDeniedException(get_string('cantviewattendance', 'interaction.schedule'));
	}*/
	
	if ($data = get_record('interaction_schedule_event','id',$eventid)) {
		if($schedule = get_record('interaction_instance','id',$data->schedule)){
			global $USER, $group;
			$role = group_user_access($schedule->group);
			if(!group_role_can_moderate_views($schedule->group, $role)){
				throw new GroupAccessDeniedException(get_string('cantviewattendance', 'interaction.schedule'));
			}
			db_begin();
				delete_records_sql(
					"DELETE FROM {interaction_schedule_attendance}
					WHERE event = ? AND user = ?",
					array($eventid,$userid)
				);
				insert_record('interaction_schedule_attendance', (object)array(
					'event' => $eventid,
					'user' => $userid,
					'attendance' => $attendance,
					'excuse' => $excuse,
					'attachment' => $attachment,
				));
			db_commit();
		}
	}
	
}


// Contstants for objectionable content reporting events.
/*define('REPORT_OBJECTIONABLE', 1);
define('MAKE_NOT_OBJECTIONABLE', 2);
define('DELETE_OBJECTIONABLE_POST', 3);
define('DELETE_OBJECTIONABLE_TOPIC', 4);
*/
class PluginInteractionSchedule extends PluginInteraction {

	private static function get_nearestparent_color($group){
		$parent = group_get_parent($group);
		if($parent){
			$schedules = get_schedule_list($parent);
			if($schedules){
				$parentconfig = get_records_assoc('interaction_schedule_instance_config', 'schedule', $schedules[0]->id, '', 'field,value');
				if(isset($parentconfig['color'])){
					return $parentconfig['color'];
				}
			}
			else{
				return self::get_nearestparent_color($parent);
			}
		}	
	}


    public static function instance_config_form($group, $instance=null) {
        if (isset($instance)) {
            $instanceconfig = get_records_assoc('interaction_schedule_instance_config', 'schedule', $instance->get('id'), '', 'field,value');
        }
//        var_dump($instanceconfig['attendance']->value);
		//find group parent colour
		if(!isset($instanceconfig['color'])){
			$instanceconfig['color'] = self::get_nearestparent_color($group->id);
/*			$parent = group_get_parent($group->id);
			if($parent){
				$schedules = get_schedule_list($parent);
				if($schedules){
					$parentconfig = get_records_assoc('interaction_schedule_instance_config', 'schedule', $schedules[0]->id, '', 'field,value');
					$instanceconfig['color'] = $parentconfig['color'];
				}
			}*/
		}
		
        return array(
            'fieldset' => array(
                'type' => 'fieldset',
                'collapsible' => true,
                'collapsed' => false,
                'legend' => get_string('settings'),
                'elements' => array(
                    'attendance' => array(
                        'type'         => 'checkbox',
                        'title'        => get_string('scheduleattendance', 'interaction.schedule'),
                        'description'  => get_string('scheduleattendancedescription', 'interaction.schedule'),
                        'defaultvalue' => isset($instanceconfig['attendance']) ? $instanceconfig['attendance']->value : true,
                        'help'         => true,
                    ),
                    'color' => array(
                        'type'         => 'color',
                        'title'        => get_string('scheduleattendance', 'interaction.schedule'),
                        'description'  => get_string('scheduleattendancedescription', 'interaction.schedule'),
                        'defaultvalue' => isset($instanceconfig['color']) ? $instanceconfig['color']->value : "#000000",
                        'help'         => true,
                    ),
                )
            )
        );
        
    }

    public static function instance_config_js() {
        return <<<EOF
function update_maxindent() {
    var s = $('edit_interaction_indentmode');
    var m = $('edit_interaction_maxindent_container');
    var t = $('edit_interaction_maxindent');
    if (!m) {
        return;
    }
    if (s.options[s.selectedIndex].value == 'max_indent') {
        removeElementClass(m, 'hidden');
        removeElementClass(t, 'hidden');
    }
    else {
        addElementClass(m, 'hidden');
        addElementClass(t, 'hidden');
    }
}
addLoadEvent(function() {
    connect('edit_interaction_indentmode', 'onchange', update_maxindent);
});
EOF;
    }

    public static function instance_config_save($instance, $values){
        
        db_begin();
//		var_dump($values);
		unset($values['id']);
        unset($values['plugin']);
        unset($values['group']);
        unset($values['justcreated']);
        unset($values['creator']);
        unset($values['title']);
        unset($values['description']);
        unset($values['submit']);
        unset($values['sesskey']);

		foreach($values as $key => $value){
			delete_records_sql(
				"DELETE FROM {interaction_schedule_instance_config}
				WHERE field = ? AND schedule = ?",
				array($key,$instance->get('id'))
			);
			insert_record('interaction_schedule_instance_config', (object)array(
				'schedule' => $instance->get('id'),
				'field' => $key,
				'value' => $value,
			));
		}
        db_commit();

    }


    public static function get_activity_types() {
    /*
        return array(
            (object)array(
                'name' => 'newpost',
                'admin' => 0,
                'delay' => 1,
                'allownonemethod' => 1,
                'defaultmethod' => 'email',
            ),
            (object)array(
                'name' => 'reportpost',
                'admin' => 1,
                'delay' => 0,
                'allownonemethod' => 1,
                'defaultmethod' => 'email',
            ),
        );
        */
    }

    public static function get_cron() {
   
        
    }


    /**
     * Subscribes the forum plugin to events
     *
     * @return array
     */
    public static function get_event_subscriptions() {
    
        return array(
            (object)array(
                'plugin'       => 'schedule',
                'event'        => 'creategroup',
                'callfunction' => 'create_default_schedule',
            ),
        );
        
    }

    public static function menu_items() {
        return array(
            'schedule' => array(
                'path' => 'schedule',
                'url' => 'interaction/schedule/schedule.php',
                'title' => get_string('name', 'interaction.schedule'),
                'weight' => 45,
            ),
        );
    }

    public static function group_menu_items($group) {
        global $USER;
        $role = group_user_access($group->id);

        $menu = array();
        if ($group->public || $role ) {
            $menu['schedule'] = array(// @todo: make forums an artefact plugin
                'path' => 'groups/schedule',
                'url' => 'interaction/schedule/index.php?group=' . $group->id,
                'title' => get_string('name', 'interaction.schedule'),
                'weight' => 25,
            );
            if(group_role_can_moderate_views($group->id, $role)){
            $menu['attendance'] = array(// @todo: make forums an artefact plugin
                'path' => 'groups/attendance',
                'url' => 'interaction/schedule/viewattendance.php?group=' . $group->id,
                'title' => get_string('attendance', 'interaction.schedule'),
                'weight' => 80,
            );
            }
        }
        return $menu;
    }

    /**
     * When a user joins a group, subscribe them automatically to all forums
     * that should be subscribable
     *
     * @param array $eventdata
     */
    public static function user_joined_group($event, $gm) {
    /*
        if ($forumids = get_column_sql("
            SELECT ii.id
            FROM {group} g
            LEFT JOIN {interaction_instance} ii ON g.id = ii.group
            LEFT JOIN {interaction_forum_instance_config} ific ON ific.forum = ii.id
            WHERE \"group\" = ? AND ific.field = 'autosubscribe' and ific.value = '1'",
            array($gm['group']))) {
            db_begin();
            foreach ($forumids as $forumid) {
                insert_record(
                    'interaction_forum_subscription_forum',
                    (object)array(
                        'forum' => $forumid,
                        'user'  => $gm['member'],
                        'key'   => PluginInteractionForum::generate_unsubscribe_key(),
                    )
                );
            }
            db_commit();
        }
        */
    }

    /**
     * When a group is created, create one forum automatically.
     *
     * @param array $eventdata
     */
    public static function create_default_schedule($event, $eventdata) {
        global $USER;
        $creator = 0;
        if (isset($eventdata['members'][$USER->get('id')])) {
            $creator = $USER->get('id');
        }
        else {
            foreach($eventdata['members'] as $userid => $role) {
                if ($role == 'admin') {
                    $creator = $userid;
                    break;
                }
            }
        }
        db_begin();
        $schedule = new InteractionScheduleInstance(0, (object) array(
            'group'       => $eventdata['id'],
            'creator'     => $creator,
            'title'       => get_string('defaultscheduletitle', 'interaction.schedule', $eventdata['name']),
            'description' => get_string('defaultscheduledescription', 'interaction.schedule', $eventdata['name']),
        ));
        $schedule->commit();
        $defaultcolor = new stdClass();
        $defaultcolor->value = "#000000";
        $defaultcolor = self::get_nearestparent_color($eventdata['id']);

        self::instance_config_save($schedule, array(
            'attendance' => 1,
            'color' => $defaultcolor->value,
        ));
        db_commit();
    }

    /**
     * Optional method. Takes a list of forums and sorts them according to
     * their weights for the sideblock
     *
     * @param array $forums An array of hashes of forum data
     * @return array        The array, sorted appropriately
     */
    public static function sideblock_sort($forums) {
    /*
        if (!$forums) {
            return $forums;
        }

        $weights = get_records_assoc('interaction_forum_instance_config', 'field', 'weight', 'forum', 'forum, value');
        foreach ($forums as &$forum) {
            // Note: forums expect every forum to have a 'weight' record in the
            // forum instance config table, so we don't need to check that
            // there is a weight for the forum here - there should be,
            // otherwise someone has futz'd with the database or there's a bug
            // elsewhere that allowed this to happen
            $forum->weight = $weights[$forum->id]->value;
        }
        usort($forums, create_function('$a, $b', 'return $a->weight > $b->weight;'));
        return $forums;
        */
    }


    /**
     * Process new forum posts.
     *
     * @param array $postnow An array of post ids to be sent immediately.  If null, send all posts older than postdelay.
     */
    /*public static function interaction_forum_new_post($postnow=null) {
        if (is_array($postnow) && !empty($postnow)) {
            $values = array();
            $postswhere = 'id IN (' . join(',', array_map('intval', $postnow)) . ')';
            $delay = false;
        }
        else {
            $currenttime = time();
            $minpostdelay = $currenttime - get_config_plugin('interaction', 'forum', 'postdelay') * 60;
            $values = array(db_format_timestamp($minpostdelay));
            $postswhere = 'ctime < ?';
            $delay = null;
        }
        $posts = get_column_sql('SELECT id FROM {interaction_forum_post} WHERE sent = 0 AND deleted = 0 AND ' . $postswhere, $values);
        if ($posts) {
            set_field_select('interaction_forum_post', 'sent', 1, 'deleted = 0 AND sent = 0 AND ' . $postswhere, $values);
            foreach ($posts as $postid) {
                activity_occurred('newpost', array('postid' => $postid), 'interaction', 'forum', $delay);
            }
        }
    }*/

    public static function can_be_disabled() {
        return false; //TODO until it either becomes an artefact plugin or stops being hardcoded everywhere
    }

    /**
     * Generates a random key to use for unsubscription requests.
     *
     * See the interaction_forum_subscription_* tables and related operations
     * on them for more information.
     *
     * @return string A random key
     */
    public static function generate_unsubscribe_key() {
        return dechex(mt_rand());
    }


    public static function has_config() {
        return true;
    }

    public static function get_config_options() {
        $weekstartday = (int) get_config_plugin('interaction', 'schedule', 'weekstartday');

        return array(
            'elements' => array(
                'weekstartday' => array(
                    'title'        => get_string('weekstartday', 'interaction.forum'),
                    'description'  => get_string('weekstartdaydescription', 'interaction.schedule'),
                    'type'         => 'text',
                    'rules'        => array('integer' => true, 'minvalue' => 0, 'maxvalue' => 10000000),
                    'defaultvalue' => $weekstartday,
                ),
            ),
            'renderer' => 'table'
        );
    }

    public static function save_config_options($form, $values) {
        set_config_plugin('interaction', 'schedule', 'weekstartday', $values['weekstartday']);
    }
/*
    public static function get_active_topics($limit, $offset, $category, $forumids = array()) {
        global $USER;

        if (is_postgres()) {
            $lastposts = '
                    SELECT DISTINCT ON (topic) topic, id, poster, subject, body, ctime
                    FROM {interaction_forum_post} p
                    WHERE p.deleted = 0
                    ORDER BY topic, ctime DESC';
        }
        else if (is_mysql()) {
            $lastposts = '
                    SELECT topic, id, poster, subject, body, ctime
                    FROM (
                        SELECT topic, id, poster, subject, body, ctime
                        FROM {interaction_forum_post}
                        WHERE deleted = 0
                        ORDER BY ctime DESC
                    ) temp1
                    GROUP BY topic';
        }

        $values = array();
        $from = '
            FROM
                {interaction_forum_topic} t
                JOIN {interaction_instance} f ON t.forum = f.id
                JOIN {group} g ON f.group = g.id';

        // user is not anonymous
        if ($USER->get('id') > 0) {
            $from .= '
                JOIN {group_member} gm ON (gm.group = g.id AND gm.member = ?)
            ';

            $values[] = $USER->get('id');
        }

        $from .= '
                JOIN {interaction_forum_post} first ON (first.topic = t.id AND first.parent IS NULL)
                JOIN (' . $lastposts . '
                ) last ON last.topic = t.id';

        $where = '
            WHERE g.deleted = 0 AND f.deleted = 0 AND t.deleted = 0';

        if (!empty($category)) {
            $where .= ' AND g.category = ?';
            $values[] = (int) $category;
        }

        if (!empty($forumids)) {
            $where .= ' AND f.id IN (' . join(',', array_fill(0, count($forumids), '?')) . ')';
            $values = array_merge($values, $forumids);
        }

        $result = array(
            'count'  => count_records_sql('SELECT COUNT(*) ' . $from . $where, $values),
            'limit'  => $limit,
            'offset' => $offset,
            'data'   => array(),
        );

        if (!$result['count']) {
            return $result;
        }

        $select = '
            SELECT
                t.id, t.forum AS forumid, f.title AS forumname, g.id AS groupid, g.name AS groupname, g.urlid,
                first.subject AS topicname, first.poster AS firstpostby,
                last.id AS postid, last.poster, last.subject, last.body, last.ctime, edits.ctime as mtime,
                COUNT(posts.id) AS postcount';

        $from .= '
                LEFT JOIN {interaction_forum_post} posts ON posts.topic = t.id
                LEFT JOIN {interaction_forum_edit} edits ON edits.post = last.id';

        $sort = '
            GROUP BY
                t.id, t.forum, f.title, g.id, g.name, g.urlid,
                first.subject, first.poster,
                last.id, last.poster, last.subject, last.body, last.ctime, edits.ctime
            ORDER BY last.ctime DESC';

        $result['data'] = get_records_sql_array($select . $from . $where . $sort, $values, $offset, $limit);

        foreach($result['data'] as &$r) {
            $r->groupurl = group_homepage_url((object) array('id' => $r->groupid, 'urlid' => $r->urlid));
        }

        return $result;
    }
*/
    // Rewrite download links in the post body to add a post id parameter.
    // Used in download.php to determine permission to view the file.
    static $replacement_postid;

/*    public static function replace_download_link($matches) {
        parse_str(html_entity_decode($matches[1]), $params);
        if (empty($params['file'])) {
            return $matches[0];
        }
        $url = get_config('wwwroot') . 'artefact/file/download.php?file=' . (int) $params['file'];
        unset($params['post']);
        unset($params['file']);
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        return $url . '&post=' . (int) self::$replacement_postid;
    }
*/
/*
    public static function prepare_post_body($body, $postid) {
        self::$replacement_postid = $postid;
        return preg_replace_callback(
            '#(?<=[\'"])' . get_config('wwwroot') . 'artefact/file/download\.php\?(file=\d+(?:(?:&amp;|&)(?:[a-z]+=[x0-9]+)+)*)#',
            array('self', 'replace_download_link'),
            $body
        );
    }
*/
    /**
     * Given a post id & the id of an image artefact, check that the logged-in user
     * has permission to see the image in the context of the post.
     */
/*
    public static function can_see_attached_file($file, $postid) {
        global $USER;
        require_once('group.php');

        if (!$file instanceof ArtefactTypeImage) {
            return false;
        }

        $post = get_record_sql('
            SELECT
                p.body, p.poster, g.id AS groupid, g.public
            FROM {interaction_forum_post} p
            INNER JOIN {interaction_forum_topic} t ON (t.id = p.topic AND t.deleted = 0)
            INNER JOIN {interaction_forum_post} fp ON (fp.parent IS NULL AND fp.topic = t.id)
            INNER JOIN {interaction_instance} f ON (t.forum = f.id AND f.deleted = 0)
            INNER JOIN {group} g ON (f.group = g.id AND g.deleted = 0)
            WHERE p.id = ? AND p.deleted = 0',
            array($postid)
        );

        if (!$post) {
            return false;
        }

        if (!$post->public && !group_user_access($post->groupid, $USER->get('id'))) {
            return false;
        }

        // Check that the author of the post is allowed to publish the file
        $poster = new User();
        $poster->find_by_id($post->poster);
        if (!$poster->can_publish_artefact($file)) {
            return false;
        }

        // Load the post as an html fragment & make sure it has the image in it
        $page = new DOMDocument();
        libxml_use_internal_errors(true);
        $success = $page->loadHTML($post->body);
        libxml_use_internal_errors(false);
        if (!$success) {
            return false;
        }
        $xpath = new DOMXPath($page);
        $srcstart = get_config('wwwroot') . 'artefact/file/download.php?file=' . $file->get('id') . '&';
        $query = '//img[starts-with(@src,"' . $srcstart . '")]';
        $elements = $xpath->query($query);
        if ($elements->length < 1) {
            return false;
        }

        return true;
    }
*/
    /**
     * Return number of forums associated to a group
     *
     * @param  $groupid: the group ID number
     * @return the number of forums
     *     OR null if invalid $groupid
     */
     /*
    public static function count_group_forums($groupid) {
        if ($groupid && $groupid > 0) {
            return count_records_select('interaction_instance', '"group" = ? AND deleted != 1', array($groupid), 'COUNT(id)');
        }
        return null;
    }*/

    /**
     * Return number of topics associated to a group
     *
     * @param  $groupid: the group ID number
     * @return the number of topics
     *     OR null if invalid $groupid
     */
     /*
    public static function count_group_topics($groupid) {
        if ($groupid && $groupid > 0) {
            return count_records_sql('SELECT COUNT(t.id)
                    FROM {interaction_instance} f
                    JOIN {interaction_forum_topic} t ON t.forum = f.id AND t.deleted != 1
                    WHERE f.group = ?
                        AND f.deleted != 1', array($groupid));
        }
        return null;
    }*/

    /**
     * Return number of posts associated to a group
     *
     * @param  $groupid: the group ID number
     * @return the number of posts
     *     OR null if invalid $groupid
     */
     /*
    public static function count_group_posts($groupid) {
        if ($groupid && $groupid > 0) {
            return count_records_sql('SELECT COUNT(p.id)
                    FROM {interaction_instance} f
                    JOIN {interaction_forum_topic} t ON t.forum = f.id AND t.deleted != 1
                    JOIN {interaction_forum_post} p ON p.topic = t.id AND p.deleted != 1
                    WHERE f.group = ?
                    AND f.deleted != 1', array($groupid));
        }
        return null;
    }*/


    /**
     * Return IDs of plugin instances
     *
     * @param  int $groupid optional group ID number
     * @return array list of the instance IDs
     */
    public static function get_instance_ids($groupid = null) {
        if (isset($groupid) && $groupid > 0) {
            return get_column('interaction_instance', 'id', 'plugin', 'schedule', 'group', $groupid, 'deleted', 0);
        }
        return get_column('interaction_instance', 'id', 'plugin', 'schedule', 'deleted', 0);
    }
}

class InteractionScheduleInstance extends InteractionInstance {

    public static function get_plugin() {
        return 'schedule';
    }

    public function interaction_remove_user($userid) {
    	/*
        delete_records('interaction_forum_moderator', 'forum', $this->id, 'user', $userid);
        delete_records('interaction_forum_subscription_forum', 'forum', $this->id, 'user', $userid);
        delete_records_select('interaction_forum_subscription_topic',
            'user = ? AND topic IN (SELECT id FROM {interaction_forum_topic} WHERE forum = ?)',
            array($userid, $this->id)
        );
        */
    }

}


