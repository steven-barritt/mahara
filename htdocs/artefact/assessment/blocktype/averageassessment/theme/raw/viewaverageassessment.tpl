
{if $canview}
<table class="assessment">
<!-- the header -->
	<tr>
		<td></td>
		{assign $assessment->get('grade_type') grade_type}
		{foreach $assessment->get('grade_type')->grade_levels as level name='gradehead'}
			<td class="gradeh {if $dwoo.foreach.gradehead.first}first{/if}" colspan="{if $level->mean_percent == -1}5{else}{$grade_type->colspan}{/if}">
				<a href="#blockinstance_{$id}" title="{$level->description}">{$level->title}</a>
			</td>
		{/foreach}
	</tr>
	{assign "" criteriagroup}
	{foreach $assessmentscheme->criteria as criteria name='criteriarows'}
		{if $criteriagroup != $criteria->criteria_group->id}
			<tr>
				<td class="criteria criteria_group" colspan="150"><a href="#blockinstance_{$id}" title="{$criteria->criteria_group->description}">{$criteria->criteria_group->title}</a></td>
			</tr>
			{assign $criteria->criteria_group->id criteriagroup}
		{/if}
		<tr>
			<td class="criteria"><a href="" title="{$criteria->description}">{$criteria->title}</a></td>
			{foreach $criteria->grade_type->grade_levels as level name='gradelevel'}
				<td class="grader gr{$level->title} {if $criteria->grade >= $level->min_percent && $criteria->grade <= $level->max_percent}selected{else}unselected{/if}" colspan="{if $level->mean_percent == -1}5{else}{$criteria->grade_type->colspan}{/if}">
					<a href="#blockinstance_{$id}" title="{if $level->rubric->title}{$level->rubric->title}&#10;{$level->rubric->description}{/if}"></a>
				</td>
			{/foreach}
		</tr>
	{/foreach}
	{if $assessment->get('visibility')}
	<tr><td>&nbsp;</td></tr>
	<tr class="finalgrade">
		<td>Grade</td>
			{foreach $assessment->get('grade_type')->grade_levels as level name='gradelevel'}
				<td class="overallgrade gr{$level->title} {if $assessment->get('grade') >= $level->min_percent && $assessment->get('grade') <= $level->max_percent}selected{else}unselected{/if}" colspan="{if $level->mean_percent == -1}5{else}{$grade_type->colspan}{/if}"  data-value="{$level->id}">
					<a href="#blockinstance_{$id}" title="{$level->description}">{$level->title}</a>
				</td>
			{/foreach}
	</tr>
	<tr>
		<td></td>
		<td colspan="5"></td>
		<td class="gradeheader first" colspan="20">Fail</td>
		<td class="gradeheader" colspan="20">3rd</td>
		<td class="gradeheader" colspan="20">2.2</td>
		<td class="gradeheader" colspan="20">2.1</td>
		<td class="gradeheader last" colspan="20">1st</td>
	</tr>
	{/if}
</table>
{else}
<p>You cannot view this item.</p>
{/if}
