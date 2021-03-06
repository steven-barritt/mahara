{include file="header.tpl"}
<div>
<a href="#" class="export">Export Table data into Excel</a>
</div>

	<h2>{$schedule->title}</h2>
	<div id="viewschedule">
	<table id="attendancelist" class="fullwidth nohead">

		<tr>
			<th class="attendanceColumnHead"></th>
			<th class="attendanceColumnHead">First Name</th>
			<th class="attendanceColumnHead">Last Name</th>
			<th class="attendanceColumnHead">Student Number</th>
			<th class="attendanceColumnHeadVertical" style="height:{$columnheight}"><div class="verticalText"><span>Present</span>
				</div></th>
			<th class="attendanceColumnHeadVertical" style="height:{$columnheight}"><div class="verticalText"><span>Late</span>
				</div></th>
			<th class="attendanceColumnHeadVertical" style="height:{$columnheight}"><div class="verticalText"><span>Absent</span>
				</div></th>
			<th class="attendanceColumnHeadVertical" style="height:{$columnheight}"><div class="verticalText"><span>Excused</span>
				</div></th>
			{cycle values='r0,r1' assign=class}
			{foreach from=$attendnaceevents item=attendanceevent}
			<th class="attendanceColumnHeadVertical {$attendanceevent->startdate|format_date:'strftimeday'}" style="height:{$columnheight}"><div class="verticalText"><span>{$attendanceevent->startdate|format_date:'strfdaymonthyearshortdot'}</span></div></th>
			{/foreach}
			
		</tr>

  {if $userdata}
    	{foreach from=$userdata item=user}
	    <tr class="{cycle values='r0,r1'}">
	    	<td class="{if (($user.percentages[0]->percentage + $user.percentages[1]->percentage) <= 70) && ($user.percentages.total > 0)}absent{/if} {if ($user.percentages[1]->percentage > 25) && ($user.percentages.total > 0)}late{/if}">
            <a href="{profile_url($user.id)}">
               <img src="{profile_icon_url user=$user.id maxwidth=40 maxheight=40}" style="max-width:40px;max-height:40px;" alt="{str tag=profileimagetext arg1=$user.id|display_default_name}" title="{$user.id|display_default_name|escape}">
            </a>
            </td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.firstname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.lastname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.studentnumber}</a></h3></td>
	    	<td class="percent">{$user.percentages[0]->percentage}%</td>
	    	<td class="percent {if ($user.percentages[1]->percentage > 25) && ($user.percentages.total > 0)}late{/if}">{$user.percentages[1]->percentage}%</td>
	    	<td class="percent {if ($user.percentages[2]->percentage > 30) && ($user.percentages.total > 0)}absent{/if}">{$user.percentages[2]->percentage}%</td>
	    	<td class="percent {if ($user.percentages[3]->percentage > 25) && ($user.percentages.total > 0)}excused{/if}">{$user.percentages[3]->percentage}%</td>
			{assign '' olddate}
	    	{foreach from=$user.attendances item=attendance name=att}{if $olddate != $attendance->startdate|format_date:'strfdaymonthyearshort'}{if !$dwoo.foreach.att.first}</tr></table></td>{/if}<td class="attend {$attendance->startdate|format_date:'strftimeday'}"><table class="innerattendnace"><tr>{/if}{if $attendance->attendance != null}<td  class="attendancecol {if $attendance->attendance == 1}attendPresent{/if}{if $attendance->attendance == 2}attendLate{/if}{if $attendance->attendance == 3}attendAbsent{/if}{if $attendance->attendance == 4}attendExcused{/if}"><a class="eventlink" href="{$WWWROOT}interaction/schedule/view.php?event={$attendance->id}" title="{$attendance->title} - {$attendance->scheduletitle}&#13;{str tag=when section=interaction.schedule}{$attendance->startdate|format_date:'strftimetime'}&#13;{str tag=where section=interaction.schedule}{$atendance->location}">{if $attendance->attendance != null}{if $attendance->attendance == 1}Y{/if}{if $attendance->attendance == 2}L{/if}{if $attendance->attendance == 3}N{/if}{if $attendance->attendance == 4}A{/if}{/if}</td>{/if}{assign $attendance->startdate|format_date:'strfdaymonthyearshort' olddate}{/foreach}
	    	{if $user.attendances}</tr></table></td>{/if}
	    </tr>    	
    	{/foreach}
  {else}
    <tr class="{cycle values='r0,r1'}"><td colspan="{$totalcolcount}" class="message">{str tag=noviewssharedwithgroupyet section=group}</td></tr>
  {/if}
	</table></div>
<div class="schedulemods">
	<strong>{str tag="groupadminlist" section="interaction.schedule"}</strong>
	{foreach from=$groupadmins item=groupadmin}
    <span class="inlinelist">
        <a href="{profile_url($groupadmin)}" class="groupadmin"><img src="{profile_icon_url user=$groupadmin maxheight=20 maxwidth=20}" style="max-width:20px;max-height:20px;" alt="{str tag=profileimagetext arg1=$groupadmin|display_default_name}"> {$groupadmin|display_name}</a>
    </span>
    {/foreach}
</div>
{include file="footer.tpl"}
