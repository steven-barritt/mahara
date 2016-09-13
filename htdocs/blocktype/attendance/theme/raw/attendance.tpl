<div id="viewattendance">
	<table id="attendancelist" class="fullwidth nohead">

		<tr>
			{assign '' olddate}
			{cycle values='r0,r1' assign=class}
		{foreach from=$events item=attendance}
			<th class=" {if $olddate == $attendance->startdate|format_date:'strfdaymonthyearshort'} {$class}{else}{cycle assign=class}{$class}{/if} attendanceColumnHeadVertical" style="height:{$columnheight}">
				<div class="verticalText"><span>{if $canedit}<a href="{$WWWROOT}interaction/schedule/takeattendance.php?event={$attendance->id}&amp;group={$groupid}&amp;returnto=last">{/if}{if $olddate != $attendance->startdate|format_date:'strfdaymonthyearshort'}{$attendance->startdate|format_date:'strftimedayvshortyear'}<br/>{else}&nbsp;&nbsp;{/if}{$attendance->startdate|format_date:'strftimetimezero'}{if $canedit}</a>{/if}</span>


				</div>
			</th>
			{assign $attendance->startdate|format_date:'strfdaymonthyearshort' olddate}
		{/foreach}
			
		</tr>
		<tr class="r0">
		{foreach from=$attendances item=attendance}
				<td class="attendancecol {if $attendance->attendance == 1}attendPresent{/if}{if $attendance->attendance == 2}attendLate{/if}{if $attendance->attendance == 3}attendAbsent{/if}{if $attendance->attendance == 4}attendExcused{/if}">{if $attendance->attendance == null}-{/if}</td>
		{/foreach}
		</tr>
	</table>
</div>
