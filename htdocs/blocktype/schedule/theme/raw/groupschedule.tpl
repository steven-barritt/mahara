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
					{$event->startdate|format_date:'strftimetime'} - {if $event->longerthanaday}
					{$event->enddate|format_date:'strftimedaydatetime'}
					{else}
					{$event->enddate|format_date:'strftimetime'}
					{/if}

				</td>
				<td class="event_details">
					<span><a  class="event_title" style="color:{$event->color}" href="{$WWWROOT}interaction/schedule/view.php?id={$event->id}">{$event->title}</a></span>
					{if $event->location}<span class="event_location"> - {$event->location}</span>{/if}
					<div class="detail hidden">
						<table class="event_details">
							<tr>
								<td class="event_details_title">Where:</td>
								<td>{$event->location}</td>
							</tr>
							{if $event->description}
							<tr>
								<td class="event_details_title">Details:</td>
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
