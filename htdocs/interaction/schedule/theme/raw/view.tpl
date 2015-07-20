{include file="header.tpl"}

<h2>{$subheading}</h2>
<table  class="event_details">
	<tr>
		<td>{str tag=event section=interaction.schedule}</td>
		<td>{$event->title}</td>
	</tr>
	<tr>
		<td>{str tag=when section=interaction.schedule}</td>
		<td class="event_date">
			<span class="event_date"> {$event->startdate|format_date:'strftimedayvshort'}</span>
		</td>
		<td class="event_time">			
					{$event->startdate|format_date:'strftimetime'}{if $event->startdate != $event->enddate} - {if $event->longerthanaday}
					{$event->enddate|format_date:'strftimedaydatetime'}
						{else}
						{$event->enddate|format_date:'strftimetime'}
						{/if}
					{/if}

		</td>

	</tr>
	<tr>
		<td>{str tag=details section=interaction.schedule}</td>
		<td>{$event->description}</td>
	</tr>
	<tr>
		<td>{str tag=location section=interaction.schedule}</td>
		<td>{$event->location}</td>
	</tr>
	<tr>
		<td>{str tag=attendance section=interaction.schedule}</td>
		<td>{if $event->attendancetaken}{str tag=recorded section=interaction.schedule}{else}{str tag=notrecorded section=interaction.schedule}{/if}</td>
	</tr>
</table>

{include file="footer.tpl"}
