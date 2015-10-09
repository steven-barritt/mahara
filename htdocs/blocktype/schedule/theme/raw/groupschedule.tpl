    {if $events}
    	<div id="viewschedule">
	<table id="schedulelist" class="fullwidth nohead">
		{assign '' olddate}
		{cycle values='r0,r1' assign=class}

		{foreach from=$events item=event name="events"}
		{if $olddate != $event->startdate|format_date:'strfdaymonthyearshort' && !$dwoo.foreach.events.first}<tr class="line_breaker"><td colspan="20"></td></tr>{/if}
		{if $olddate != $event->startdate|format_date:'strfdaymonthyearshort'}
			<tr class="{if $olddate == $event->startdate|format_date:'strfdaymonthyearshort'} {$class}{else}{cycle assign=class}{$class}{/if}">
				<td  colspan="4">
					<span  class="event_date">{$event->startdate|format_date:'strftimedayvshort'}</span>
				</td>
			</tr>
		{/if}

		<tr class="{$class}">
				<td class="event_time">			
					{$event->startdate|format_date:'strftimetime'}{if $event->startdate != $event->enddate} - {if $event->longerthanaday}
					{$event->enddate|format_date:'strftimedayvshorttime'}
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
								
								<td class="event_details_title">{str tag=attendance section=blocktype.schedule}</td>
								<td>{if $event->attendance}{str tag=recorded section=blocktype.schedule}{else}{str tag=notrecorded section=blocktype.schedule}{/if}</td>
							</tr>
							<tr>
								<td class="event_details_title">{str tag=where section=blocktype.schedule}</td>
								<td>{$event->location}</td>
							</tr>
							<tr>
								<td class="event_details_title">{str tag=schedule section=blocktype.schedule}</td>
								<td>
									<span class="event_schedule_title"><a href="{$WWWROOT}interaction/schedule/index.php?group={$event->eventgroup}" style="color:{$event->color}">{$event->scheduletitle}</a></span>
								</td>
							</tr>
							{if $event->description}
							<tr>
								<td class="event_details_title">{str tag=details section=blocktype.schedule}</td>
								<td>{$event->description|safe}</td>
							</tr>
							{/if}
						</table>							
					</div>
				</td>
		</tr>
		{assign $event->startdate|format_date:'strfdaymonthyearshort' olddate}

		{/foreach}
		<tr class="line_breaker"><td colspan="20"></td></tr>
	</table></div>
    {else}
        <table class="fullwidth"><tr class="{cycle values='r0,r1'}">
                <td align="center">{str tag=noeventsforgroup section=interaction.schedule}</td>
            </tr>
        </table>
    {/if}
    <div class="morelinkwrap"><a class="morelink" href="{$WWWROOT}interaction/schedule/index.php?group={$group->id}">{str tag=gotoschedule section=interaction.schedule} &raquo;</a></div>
