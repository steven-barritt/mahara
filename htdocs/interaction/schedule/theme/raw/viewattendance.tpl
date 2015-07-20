{include file="header.tpl"}
{if $schedule}
	<h2>{$schedule->title}</h2>
	<div id="viewschedule"><table id="attendancelist" class="fullwidth nohead">

		<tr>
			<th class="attendanceColumnHead"></th>
			<th class="attendanceColumnHead">First Name</th>
			<th class="attendanceColumnHead">Last Name</th>
			<th class="attendanceColumnHead">Student Number</th>
			{assign '' olddate}
			{cycle values='r0,r1' assign=class}
			{foreach from=$attendnaceevents item=attendanceevent}
			<th class=" {if $olddate == $attendanceevent->startdate|format_date:'strfdaymonthyearshort'} {$class}{else}{cycle assign=class}{$class}{/if} attendanceColumnHeadVertical" style="height:{$columnheight}">
				<div class="verticalText"><span><a href="{$WWWROOT}interaction/schedule/takeattendance.php?event={$attendanceevent->id}&amp;group={$groupid}&amp;returnto=viewattendance">{if $olddate != $attendanceevent->startdate|format_date:'strfdaymonthyearshort'}{$attendanceevent->startdate|format_date:'strftimedayvshortyear'}<br/>{else}&nbsp;&nbsp;{/if}{$attendanceevent->startdate|format_date:'strftimetimezero'} - {$attendanceevent->title}</a></span>
				</div>
			</th>
			{assign $attendanceevent->startdate|format_date:'strfdaymonthyearshort' olddate}
			{/foreach}
			
		</tr>

  {if $userdata}
    	{foreach from=$userdata item=user}
	    <tr class="{cycle values='r0,r1'}">
	    	<td>
            <a href="{profile_url($user.id)}">
               <img src="{profile_icon_url user=$user.id maxwidth=40 maxheight=40}" alt="{str tag=profileimagetext arg1=$user.id|display_default_name}" title="{$user.id|display_default_name|escape}">
            </a>
            </td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.firstname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.lastname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.studentnumber}</a></h3></td>
	    	{for i 1 $colcount}
	    		<td class="attendancecol {if $user[$i]->attendance == 1}attendPresent{/if}{if $user[$i]->attendance == 2}attendLate{/if}{if $user[$i]->attendance == 3}attendAbsent{/if}{if $user[$i]->attendance == 4}attendExcused{/if}">{if $user[$i]->attendance == null}-{/if}</td>
	    	{/for}
	    </tr>    	
    	{/foreach}
  {else}
    <tr class="{cycle values='r0,r1'}"><td colspan="{$totalcolcount}" class="message">{str tag=noviewssharedwithgroupyet section=group}</td></tr>
  {/if}
	</table></div>
{else}
	<h2>{str tag=name section=interaction.schedule}</h2>

	<div class="message">{str tag=noschedules section=interaction.schedule}</div>
{/if}
<div class="schedulemods">
	<strong>{str tag="groupadminlist" section="interaction.schedule"}</strong>
	{foreach from=$groupadmins item=groupadmin}
    <span class="inlinelist">
        <a href="{profile_url($groupadmin)}" class="groupadmin"><img src="{profile_icon_url user=$groupadmin maxheight=20 maxwidth=20}" alt="{str tag=profileimagetext arg1=$groupadmin|display_default_name}"> {$groupadmin|display_name}</a>
    </span>
    {/foreach}
</div>
{include file="footer.tpl"}
