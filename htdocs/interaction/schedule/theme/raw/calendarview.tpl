{include file="header.tpl"}
<div id="scheduleoptions">
<a href="{$WWWROOT}interaction/schedule/index.php?group={$groupid}&view=0" class="btn newschedule {if $view == 0}selected{/if}">{str tag="scheduleview" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/schedule/index.php?group={$groupid}&view=1" class="btn newschedule {if $view == 1}selected{/if}">{str tag="yearplannerview" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/schedule/index.php?group={$groupid}&view=2" class="btn newschedule {if $view == 2}selected{/if}">{str tag="calendarview" section=interaction.schedule}</a>
</div>
{if $admin}
<div id="schedulebtns">
{if !$schedule}
<a href="{$WWWROOT}interaction/edit.php?group={$groupid}&amp;plugin=schedule&amp;returnto=index" class="btn newschedule">{str tag="newschedule" section=interaction.schedule}</a>
{else}
<a href="{$WWWROOT}interaction/schedule/editevent.php?schedule={$schedule->id}" class="btn newschedule">{str tag="newevent" section=interaction.schedule}</a>
<a href="{$WWWROOT}interaction/edit.php?id={$schedule->id}&amp;group={$groupid}&amp;plugin=schedule&amp;returnto=index" class="btn newschedule">{str tag="schedulesettings" section=interaction.schedule}</a>
{/if}
</div>
{/if}
{if $schedule}
	<div class="color_swatch" style="background-color:{$schedule->color}"></div>
	<h2 class="schedule_title">{$schedule->title}</h2>
	{if $events}
	<div id="viewschedule">
	<table id="schedulelist" class="fullwidth nohead">
		{assign '' olddate}
		{cycle values='r0,r1' assign=class}

		{foreach from=$events item=event name="events"}
		{if $olddate != $event->startdate|format_date:'strfdaymonthyearshort' && !$dwoo.foreach.events.first}<tr class="line_breaker"><td colspan="20"></td></tr>{/if}

		<tr class="{if $olddate == $event->startdate|format_date:'strfdaymonthyearshort'} {$class}{else}{cycle assign=class}{$class}{/if}">
			<td class="event_date">
				{if $olddate != $event->startdate|format_date:'strfdaymonthyearshort'}<span class="event_date"> {$event->startdate|format_date:'strftimedayvshort'}</span>{/if}
			</td>
				<td class="event_time">			
					{$event->startdate|format_date:'strftimetime'} - {if $event->longerthanaday}
					{$event->enddate|format_date:'strftimedaydatetime'}
					{else}
					{$event->enddate|format_date:'strftimetime'}
					{/if}

				</td>
				<td class="event_details">
					<span><a  class="event_title {if $event->attendance}attendance{/if}" style="color:{$event->color}" href="{$WWWROOT}interaction/schedule/view.php?id={$event->id}">{$event->title}</a></span>
					{if $event->schedule != $schedule->id}<span class="event_schedule_title"> : <a href="{$WWWROOT}interaction/schedule/index.php?group={$event->eventgroup}" style="color:{$event->color}">{$event->scheduletitle}</a></span>{/if}
					{if $event->location}<span class="event_location"> - {$event->location}</span>{/if}
					<div class="detail hidden">
						<table class="event_details">
							<tr>
								<td class="event_details_title">{str tag=where section=interaction.schedule}</td>
								<td>{$event->location}</td>
							</tr>
							<tr>
								
								<td class="event_details_title">{str tag=attendance section=interaction.schedule}</td>
								<td>{if $event->attendance}{str tag=recorded section=interaction.schedule}{else}{str tag=notrecorded section=interaction.schedule}{/if}</td>
							</tr>
							<tr>
								<td class="event_details_title">{str tag=schedule section=interaction.schedule}</td>
								<td>
									<span class="event_schedule_title"><a href="{$WWWROOT}interaction/schedule/index.php?group={$event->eventgroup}" style="color:{$event->color}">{$event->scheduletitle}</a></span>
								</td>
							</tr>
							{if $event->description}
							<tr>
								<td class="event_details_title">{str tag=details section=interaction.schedule}</td>
								<td>{$event->description|safe}</td>
							</tr>
							{/if}
						</table>							
					</div>
				</td>
			<td class="right btns2">
			{if $admin}
				<a href="{$WWWROOT}interaction/schedule/editevent.php?id={$event->id}&amp;schedule={$schedule->id}&amp;returnto=index" class="icon btn-big-edit" title="{str tag=edit}">
					{str tag=editspecific arg1=$event->title}
				</a>
				<a href="{$WWWROOT}interaction/schedule/deleteevent.php?id={$event->id}&amp;returnto=index" class="icon btn-big-del" title="{str tag=delete}">
					{str tag=deletespecific arg1=$event->title}
				</a>
			{/if}
			</td>
		</tr>
		{assign $event->startdate|format_date:'strfdaymonthyearshort' olddate}

		{/foreach}
		<tr class="line_breaker"><td colspan="20"></td></tr>
	</table></div>
	{else}
	<div class="message">{str tag=noevents section=interaction.schedule}</div>
	{/if}
{else}
	<h2>{str tag=name section=interaction.schedule}</h2>

	<div class="message">{str tag=noschedules section=interaction.schedule}</div>
	<h2>{str tag=subgroupevents section=interaction.schedule}</h2>
	{if $events}
	<div id="viewschedule"><table id="schedulelist" class="fullwidth nohead">
		{assign '' olddate}
		{cycle values='r0,r1' assign=class}

		{foreach from=$events item=event}
		<tr class="{if $olddate == $event->startdate|format_date:'strfdaymonthyearshort'} {$class}{else}{cycle assign=class}{$class}{/if}">
			<td>
				{if $olddate != $event->startdate|format_date:'strfdaymonthyearshort'} {$event->startdate|format_date:'strftimedaydate'}{/if}
			</td>
			<td>
				{$event->startdate|format_date:'strftimetime'}
			</td>
			<td>
				{if $event->longerthanaday}
				{$event->enddate|format_date:'strftimedaydatetime'}
				{else}
				{$event->enddate|format_date:'strftimetime'}
				{/if}
			</td>
			<td>
				<h3 class="title"><a href="{$WWWROOT}interaction/schedule/view.php?id={$event->id}">{$event->title}</a></h3>
				<div class="detail">{$event->description|str_shorten_html:1000:true|safe}</div>
			</td>
			<td>
				{$event->location}
			</td>
			<td class="right btns2">
			{if $admin}
				<a href="{$WWWROOT}interaction/schedule/editevent.php?id={$event->id}&amp;schedule={$schedule->id}&amp;returnto=index" class="icon btn-big-edit" title="{str tag=edit}">
					{str tag=editspecific arg1=$event->title}
				</a>
				<a href="{$WWWROOT}interaction/schedule/deleteevent.php?id={$event->id}&amp;returnto=index" class="icon btn-big-del" title="{str tag=delete}">
					{str tag=deletespecific arg1=$event->title}
				</a>
			{/if}
			</td>
		</tr>
		{assign $event->startdate|format_date:'strfdaymonthyearshort' olddate}

		{/foreach}
	</table></div>
	{else}
	<div class="message">{str tag=noevents section=interaction.schedule}</div>
	{/if}
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
