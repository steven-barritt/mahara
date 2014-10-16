{include file="header.tpl"}
<div class="message deletemessage">
  <p>
    {if $collectionnote}{$collectionnote|clean_html|safe}<br>{/if}
    {str tag="resetviewconfirm1" section="view"}
    {if $view->get('owner')}<br>{str tag="resetviewconfirmbackup" section="view" arg1=$WWWROOT}{/if}
  </p>
  {$form|safe}
  <p>{str tag="resetviewconfirmnote1" section="view"}</p>
</div>
{include file="footer.tpl"}
