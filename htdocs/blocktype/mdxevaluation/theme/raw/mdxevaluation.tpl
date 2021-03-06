<div class="inline-form hidden" id="mdxevaluation_{$id}">
	{$form|safe}
</div>
<div class="blockinstance-content-view">    
	{if $viewable}
	{if !$published}
	<h4>Unpublished</h4>
	{/if}
	
    <table class="mdxevaluation {if $published}published{else}unpublished{/if}">
    	{if !$gradeonly}
        <thead>
            <tr>
            <th></th><th>{str tag='exc' section='blocktype.mdxevaluation'}</th><th>{str tag='vgood' section='blocktype.mdxevaluation'}</th><th>{str tag='good' section='blocktype.mdxevaluation'}</th><th>{str tag='pass' section='blocktype.mdxevaluation'}</th><th>{str tag='fail' section='blocktype.mdxevaluation'}</th>
            </tr>
            </thead>
            {/if}
        <tbody>
    	{if !$gradeonly}
            <tr>
            <td class="first_col">{str tag='research' section='blocktype.mdxevaluation'}</td>
            <td>{if $research eq 1}&#10004;{/if}</td>
            <td>{if $research eq 2}&#10004;{/if}</td>
            <td>{if $research eq 3}&#10004;{/if}</td>
            <td>{if $research eq 4}&#10004;{/if}</td>
            <td>{if $research eq 5}&#10004;{/if}</td>
            </tr>
            <tr>
            <td class="first_col">{str tag='concept' section='blocktype.mdxevaluation'}</td>
            <td>{if $concept eq 1}&#10004;{/if}</td>
            <td>{if $concept eq 2}&#10004;{/if}</td>
            <td>{if $concept eq 3}&#10004;{/if}</td>
            <td>{if $concept eq 4}&#10004;{/if}</td>
            <td>{if $concept eq 5}&#10004;{/if}</td>
            </tr>
            <tr>
            <td class="first_col">{str tag='technical' section='blocktype.mdxevaluation'}</td>
            <td>{if $technical eq 1}&#10004;{/if}</td>
            <td>{if $technical eq 2}&#10004;{/if}</td>
            <td>{if $technical eq 3}&#10004;{/if}</td>
            <td>{if $technical eq 4}&#10004;{/if}</td>
            <td>{if $technical eq 5}&#10004;{/if}</td>
            
            </tr>
            <tr>
            <td class="first_col">{str tag='presentation' section='blocktype.mdxevaluation'}</td>
            <td>{if $presentation eq 1}&#10004;{/if}</td>
            <td>{if $presentation eq 2}&#10004;{/if}</td>
            <td>{if $presentation eq 3}&#10004;{/if}</td>
            <td>{if $presentation eq 4}&#10004;{/if}</td>
            <td>{if $presentation eq 5}&#10004;{/if}</td>
            </tr>
            <tr>
            <td class="first_col">{str tag='studentship' section='blocktype.mdxevaluation'}</td>
            <td>{if $studentship eq 1}&#10004;{/if}</td>
            <td>{if $studentship eq 2}&#10004;{/if}</td>
            <td>{if $studentship eq 3}&#10004;{/if}</td>
            <td>{if $studentship eq 4}&#10004;{/if}</td>
            <td>{if $studentship eq 5}&#10004;{/if}</td>
            </tr>
            <td class="first_col">{str tag='workbook' section='blocktype.mdxevaluation'}</td>
            <td>{if $workbook eq 1}&#10004;{/if}</td>
            <td>{if $workbook eq 2}&#10004;{/if}</td>
            <td>{if $workbook eq 3}&#10004;{/if}</td>
            <td>{if $workbook eq 4}&#10004;{/if}</td>
            <td>{if $workbook eq 5}&#10004;{/if}</td>
            
            </tr>
{/if}
            <tr><td>&nbsp;</td></tr>
            <tr>
            <td class="first_col selfgrade">{str tag='grade' section='blocktype.mdxevaluation'}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><div class="mdxselfmark">{$selfmark}</div></td>
            </tr>
        </tbody>
    </table>
    {else}
    	<p>You cannot view this item.</p><p> If you are a Tutor the page must be submitted for assessment before you can grade it.</p>
    	<p>If this is your page the project hasn't been graded yet.</p>
    {/if}
</div>  
    
