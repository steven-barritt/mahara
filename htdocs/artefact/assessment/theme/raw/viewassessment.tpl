{if $assessment}
{if $canedit}
	<div class="editingmsg hidden">{str tag='editingmsg' section='artefact.assessment'}</div>
{/if}
{if $assessment->get('type') == $tutor && $canedit}
<div class="publishedmsg ">
	<div class="msg published {if !$assessment->get('published')}hidden{/if}">{str tag='published' section='artefact.assessment'}</div>
	<div class="msg unpublished {if $assessment->get('published')}hidden{/if}">{str tag='unpublished' section='artefact.assessment'}</div>
{if $canedit}
	<div class="publishbtns editing-buttons hidden">
		<a href="#editbtn_{$id}" class="btn unpublish {if !$assessment->get('published')}hidden{/if}" data-assessment="{$assessment->get('id')}">{str tag='unpublish' section='artefact.assessment'}</a>
		<a href="#editbtn_{$id}" class="btn publish {if $assessment->get('published')}hidden{/if}" data-assessment="{$assessment->get('id')}">{str tag='publish' section='artefact.assessment'}</a>
	</div>
{/if}
</div>
{/if}

{if $canedit || $assessment->get('published')}
<table class="assessment">
<!-- the header -->
	<tr>
		<td></td>
		{assign $assessment->get('grade_type') grade_type}
		{foreach $assessment->get('grade_type')->grade_levels as level name='gradehead'}
			<td class="gradeh {if $dwoo.foreach.gradehead.first}first{/if}" colspan="{$grade_type->colspan}">
				<a href="#blockinstance_{$id}" title="{$level->description}">{$level->title}</a>
			</td>
		{/foreach}
	</tr>
	{assign "" criteriagroup}
	{foreach $assessment->get('assessment_scheme')->criteria as criteria name='criteriarows'}
		{if $criteriagroup != $criteria->criteria_group->id}
			<tr>
				<td class="criteria criteria_group" colspan="150"><a href="#blockinstance_{$id}" title="{$criteria->criteria_group->description}">{$criteria->criteria_group->title}</a></td>
			</tr>
			{assign $criteria->criteria_group->id criteriagroup}
		{/if}
		<tr>
			<td class="criteria"><a href="" title="{$criteria->description}">{$criteria->title}</a></td>
			{foreach $criteria->grade_type->grade_levels as level name='gradelevel'}
				<td class="grade gr{$level->title} {if $criteria->grade >= $level->min_percent && $criteria->grade <= $level->max_percent}selected{else}unselected{/if}" colspan="{if $level->mean_percent == -1}5{else}{$criteria->grade_type->colspan}{/if}">
					<a href="#blockinstance_{$id}" title="{if $level->rubric->title}{$level->rubric->title}&#10;{$level->rubric->description}{/if}" data-assessment="{$assessment->get('id')}" data-criteria="{$criteria->id}" data-value="{$level->id}"></a>
				</td>
			{/foreach}
		</tr>
	{/foreach}
	{if $assessment->get('visibility')}
	<tr><td>&nbsp;</td></tr>
	<tr class="finalgrade">
		<td>Grade</td>
			{foreach $assessment->get('grade_type')->grade_levels as level name='gradelevel'}
				<td class="overallgrade gr{$level->title} {if $assessment->get('grade') >= $level->min_percent && $assessment->get('grade') <= $level->max_percent}selected{else}unselected{/if}" colspan="{$grade_type->colspan}"  data-value="{$level->id}">
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
<p>You cannot view this item.</p><p> If you are a Tutor the page must be submitted for assessment before you can grade it.</p>
    	<p>If this is your page the project hasn't been graded yet.</p>
{/if}
{if $canedit}

	<div class="editing-buttons hidden" id="editbtn_{$id}">
		<div>
		<!--<a href="#editbtn_{$id}" class="btn reset" data-assessment="{$assessment->get('id')}">Reset</a>-->
		
		<a href="#editbtn_{$id}" class="btn done">{str tag='done' section='artefact.assessment'}</a></div>
	</div>
	<div class="editingmsg hidden">{str tag='editingmsg' section='artefact.assessment'}</div>
{/if}
{else}
{str tag='noartefact' section='artefact.assessment'}
{/if}