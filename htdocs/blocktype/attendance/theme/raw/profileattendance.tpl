<div id="viewattendance">
{if $cansee}
	<h4>{str tag=attendanceregister section=blocktype.attendance}</h4>
	<table id="profileattendancelist" class="fullwidth nohead">

		<tr class="r0">
		{foreach from=$attendances item=attendance name=attend}
			{if ($dwoo.foreach.attend.index % $factor) == 0}
				</tr>
				<tr class="r0">
			{/if}
				<td class="attendancecol {if $attendance->attendance == 1}present{/if}{if $attendance->attendance == 2}late{/if}{if $attendance->attendance == 3}absent{/if}{if $attendance->attendance == 4}excused{/if}">
				<a class="eventlink" href="{$WWWROOT}interaction/schedule/view.php?event={$attendance->id}" title="{$attendance->startdate|format_date:'strftimedayvshortyear'}&#13;{$attendance->title} - {$attendance->scheduletitle}&#13;{str tag=when section=interaction.schedule}{$attendance->startdate|format_date:'strftimetime'}&#13;{str tag=where section=interaction.schedule}{$atendance->location}">&nbsp;</a></td>
		{/foreach}
		</tr>
	</table>
	<div class="attendancepercent">
	<h4>{str tag=percentage section=blocktype.attendance}</h4>
	<table id="profileattendancepercent" class="fullwidth nohead">
		<tr class="r0">
				{if $percentages[0]->percentage > 0}
				<td class="present" style="width:{$percentages[0]->percentage}%">&nbsp;</td>
				{/if}
				{if $percentages[1]->percentage > 0}
				<td class="late" style="width:{$percentages[1]->percentage}%">&nbsp;</td>
				{/if}
				{if $percentages[2]->percentage > 0}
				<td class="absent" style="width:{$percentages[2]->percentage}%">&nbsp;</td>
				{/if}
				{if $percentages[3]->percentage > 0}
				<td class="excused" style="width:{$percentages[3]->percentage}%">&nbsp;</td>
				{/if}
		</tr>
	</table>
		<table class="attendancekey">
			<tr>
			<td class="one-quater">
				<span class="present">{$percentages[0]->percentage}</span><span>{str tag=present section=blocktype.attendance}</span>
			</td>
			<td class="one-quater">
				<span class="late">{$percentages[1]->percentage}</span><span>{str tag=late section=blocktype.attendance}</span>
			</td>
			<td class="one-quater">
				<span class="absent">{$percentages[2]->percentage}</span><span>{str tag=absent section=blocktype.attendance}</span>
			</td>
			<td class="one-quater">
				<span class="excused">{$percentages[3]->percentage}</span><span>{str tag=excused section=blocktype.attendance}</span>			
			</td>
			</tr>
		</table>
	</div>
	{/if}
</div>
