{include file="header.tpl"}

<h2>{$subheading}</h2>
			<div>
				<h3 class="title">{$event->title}</h3>
				<div class="detail">{$event->description|str_shorten_html:1000:true|safe}</div>
			</div>
			<div>
				{str tag=startdate section=interaction.schedule} : {$event->startdate|format_date:'strftimedaydatetime'}
			</div>
			<div>
				{str tag=enddate section=interaction.schedule} : {$event->enddate|format_date:'strftimedaydatetime'}
			</div>
			<div>
				{str tag=location section=interaction.schedule} : {$event->location}
			</div>

<div class="message deletemessage">{$deleteform|safe}</div>

{include file="footer.tpl"}
