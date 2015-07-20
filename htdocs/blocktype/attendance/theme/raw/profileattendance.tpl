<div id="viewattendance">
	<h4>{str tag=attendanceregister section=blocktype.attendance}</h4>
	<table id="profileattendancelist" class="fullwidth nohead">

		<tr class="r0">
		{foreach from=$attendances item=attendance name=attend}
			{if ($dwoo.foreach.attend.index % $factor) == 0}
				</tr>
				<tr class="r0">
			{/if}
				<td class="attendancecol {if $attendance->attendance == 1}present{/if}{if $attendance->attendance == 2}late{/if}{if $attendance->attendance == 3}absent{/if}{if $attendance->attendance == 4}excused{/if}">
				<a class="eventlink" href="{$WWWROOT}interaction/schedule/view.php?event={$attendance->id}" title="{$attendance->startdate|format_date:'strftimedayvshortyear'} - {$attendance->title}">&nbsp;</a></td>
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
		<div class="attendancekey">
			<div class="one-quater">
				<span class="present">&nbsp;</span><span>{str tag=present section=blocktype.attendance}: {$percentages[0]->percentage}%</span>
			</div>
			<div class="one-quater">
				<span class="late">&nbsp;</span><span>{str tag=late section=blocktype.attendance}: {$percentages[1]->percentage}%</span>
			</div>
			<div class="one-quater">
				<span class="absent">&nbsp;</span><span>{str tag=absent section=blocktype.attendance}: {$percentages[2]->percentage}%</span>
			</div>
			<div class="one-quater">
				<span class="excused">&nbsp;</span><span>{str tag=excused section=blocktype.attendance}: {$percentages[3]->percentage}%</span>			
			</div>
		</div>
	</div>
</div>
