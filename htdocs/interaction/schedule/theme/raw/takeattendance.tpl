{include file="header.tpl"}

{if $event}
	<h1>{str tag=takeattendance section=interaction.schedule}</h1>
<div id="schedulebtns">
	<a href="{$WWWROOT}interaction/schedule/{$returnto}.php?schedule={$event->schedule}&amp;group={$groupid}&amp;returnto=index" class="btn">{str tag="done" section=interaction.schedule}</a>
	</div>
	<h2>{$event->title} - {$event->startdate|format_date:'strftimetimedayyearshort'}</h2>
	<div id="viewschedule"><table id="attendancelist" class="fullwidth nohead">

		<tr>
			<th class="attendanceColumnHead">Mugshot</th>
			<th class="attendanceColumnHead">First Name</th>
			<th class="attendanceColumnHead">Last Name</th>
			<th class="attendanceColumnHead">Student Number</th>
			<th class="attendanceColumnHead"></th>
			
		</tr>

  {if $userdata}
    	{foreach from=$userdata item=user}
	    <tr class="{cycle values='r0,r1'}" id="row{$user.id}">
	    	<td>
            <a href="{profile_url($user.id)}">
               <img src="{profile_icon_url user=$user.id maxwidth=40 maxheight=40}" style="max-width:40px;max-height:40px;" alt="{str tag=profileimagetext arg1=$user.id|display_default_name}" title="{$user.id|display_default_name|escape}">
            </a>
            </td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.firstname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.lastname}</a></h3></td>
	    	<td class="sv"><h3 class="title"><a href="{$WWWROOT}user/view.php?id={$user.id}">{$user.studentnumber}</a></h3></td>
	    	<td class="attendancecol {if $user.attendance->attendance == 1}present{/if}" id="present_{$user.id}">
				<a href="{$WWWROOT}interaction/schedule/takeattendance.php?event={$event->id}&amp;group={$groupid}&amp;userid={$user.id}&amp;attendance=1&amp;returnto={$returnto}#row{$user.id}" class="attendancelink"><img class="{if $user.attendance->attendance != 1}greyedout{/if}" src="{theme_url filename='images/present.png' plugin='interaction/schedule'}"/></a>
	    	</td>
	    	<td class="attendancecol {if $user.attendance->attendance == 2}late{/if}" id="late_{$user.id}">
	    		<a href="{$WWWROOT}interaction/schedule/takeattendance.php?event={$event->id}&amp;group={$groupid}&amp;userid={$user.id}&amp;attendance=2&amp;returnto={$returnto}#row{$user.id}" class="attendancelink"><img class="{if $user.attendance->attendance != 2}greyedout{/if}" src="{theme_url filename='images/late.png' plugin='interaction/schedule'}"/></a>
	    	</td>
	    	<td class="attendancecol {if $user.attendance->attendance == 3}absent{/if}" id="absent_{$user.id}">
	    		<a href="{$WWWROOT}interaction/schedule/takeattendance.php?event={$event->id}&amp;group={$groupid}&amp;userid={$user.id}&amp;attendance=3&amp;returnto={$returnto}#row{$user.id}" class="attendancelink"><img class="{if $user.attendance->attendance != 3}greyedout{/if}" src="{theme_url filename='images/absent.png' plugin='interaction/schedule'}"/></a>
	    	</td>
			<td class="attendancecol {if $user.attendance->attendance == 4}excused{/if}" id="excused_{$user.id}">
	    		<a href="{$WWWROOT}interaction/schedule/takeattendance.php?event={$event->id}&amp;group={$groupid}&amp;userid={$user.id}&amp;attendance=4&amp;returnto={$returnto}#row{$user.id}" class="attendancelink"><img class="{if $user.attendance->attendance != 4}greyedout{/if}" src="{theme_url filename='images/excused.png' plugin='interaction/schedule'}"/></a>
			</td>
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
        <a href="{profile_url($groupadmin)}" class="groupadmin"><img src="{profile_icon_url user=$groupadmin maxheight=20 maxwidth=20}" alt="{str tag=profileimagetext arg1=$groupadmin|display_default_name}" style="max-width:20px"> {$groupadmin|display_name}</a>
    </span>
    {/foreach}
</div>
{include file="footer.tpl"}
