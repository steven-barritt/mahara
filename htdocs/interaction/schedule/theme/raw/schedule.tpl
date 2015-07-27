{include file="header.tpl"}
		<h2>{$heading}</h2>
	<div id="viewschedule">
<div class="">{str tag=showingfrom section=interaction.schedule}{$mindate|format_date:'strftimedayvshortyear'}  <a href="{$WWWROOT}interaction/schedule/schedule.php?limit=31&offset={$offset-7}">{str tag=showearlier section=interaction.schedule}</a></div>
	{if $events}
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
					{$event->startdate|format_date:'strftimetime'}{if $event->startdate != $event->enddate} - {if $event->longerthanaday}
					{$event->enddate|format_date:'strftimedaydatetime'}
					{else}
					{$event->enddate|format_date:'strftimetime'}
					{/if}{/if}
				</td>
				<td class="event_details">
					<span {if $event->attendance}class="attendance"{/if}><a  class="event_title" style="color:{$event->color}" href="{$WWWROOT}interaction/schedule/view.php?id={$event->id}">{$event->title}</a></span>
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
	</table>
	{else}
	<div class="message">{str tag=noeventsyet section=interaction.schedule arg1=$limit}</div>
	{/if}
	<div class="">{str tag=showingto section=interaction.schedule}{$maxdate|format_date:'strftimedayvshortyear'}
	<a href="{$WWWROOT}interaction/schedule/schedule.php?limit={if $limit < 63}{$limit+14}&offset={$offset}{else}{$limit}&offset={$offset+7}{/if}">{str tag=showmore section=interaction.schedule}</a></div>
	</div>
{include file="footer.tpl"}
