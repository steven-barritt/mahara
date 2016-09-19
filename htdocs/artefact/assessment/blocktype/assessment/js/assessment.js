var $j = jQuery;


function setgrade(link) {

	var assessment = getNodeAttribute(link,'data-assessment');
	var criteria = getNodeAttribute(link,'data-criteria');
	var grade = getNodeAttribute(link,'data-value');

    sendjsonrequest('../artefact/assessment/assessment.json.php',
        {'assessment': assessment, 'criteria': criteria, 'grade':grade},
        'GET',
        function(data) {
			var selected = $j(link).parent().parent().find('.selected');
			selected.toggleClass('selected');
			selected.toggleClass('unselected');
			$j(link).parent().toggleClass('selected');
			$j(link).parent().toggleClass('unselected');
			
			//update the grade
			var finalgrade = $j(link).parents('.assessment').find('.finalgrade');
			selected = finalgrade.find('.selected');
			selected.toggleClass('selected');
			selected.toggleClass('unselected');
			selected = finalgrade.find("td[data-value='"+data['data']+"']")
			selected.toggleClass('selected');
			selected.toggleClass('unselected');
        },
        function() {
            // @todo error
        }
    );
    return false;
}

function reset_assessment(button){
/*	var assessment = getNodeAttribute(button,'data-assessment');
    sendjsonrequest('../artefact/assessment/reset.json.php',
    	{'assessment':assessment},
    	'GET',
    	
    	function(data){
    		var i;
    		for(i = 0,i < data['data'].length, ++i){
    			var crit = data['data'][i];
    			
    		}
    	},
    	function(){
    	}
    );
    return false;*/
}

function publish_assessment(button){
	var assessment = getNodeAttribute(button,'data-assessment');
    sendjsonrequest('../artefact/assessment/assessment.json.php',
    	{'assessment':assessment,'publish':true},
    	'GET',
    	
    	function(data){
    			var $this =  $j(button).parents('.blockinstance').find('.publishedmsg');
    			$this.find('.published').toggleClass('hidden');
    			$this.find('.unpublished').toggleClass('hidden');
    			$this.find('.publish').toggleClass('hidden');
    			$this.find('.unpublish').toggleClass('hidden');
    	},
    	function(){
    	}
    );
    return false;
}


function edit_assessment(button){
	var bob = $j(button).parents('.blockinstance').find('.assessment .grade a');

	//if the element is retracted then expand it
	var $this =  $j(button).parents('.blockinstance').find('.blockinstance-header');
    if ($this.hasClass('retracted')) {
        $this.removeClass('retracted');
        $this.next().slideDown('fast');
    }

	
	$j(button).parents('.blockinstance').find('.editingmsg').toggleClass('hidden');
	$j(button).parents('.blockinstance').find('.editing-buttons').toggleClass('hidden');
	$j(button).parents('.blockinstance').find('.blockinstance-content').toggleClass('editing');
	if($j(button).parents('.blockinstance').find('.blockinstance-content').hasClass('editing')){
		bob.click(function(e){
			e.preventDefault();
			setgrade(this);
		});
		bob.css('cursor','cell');
		bob.on( "mouseenter", function() {
			$j(this).addClass('hovering');
		  })
		  .on( "mouseleave", function() {
			$j(this).removeClass('hovering');
		});
	}else{
		bob.unbind();
		bob.click(function(e){
			e.preventDefault();
		});
		bob.css('cursor','help');
	}
}


$j(document).ready(function(){
	$j('.bt-assessment').find('.configurebutton').click(function(e){
		e.preventDefault();
		edit_assessment(this);
	});
	$j('.bt-assessment .reset').click(function(e){
		e.preventDefault();
		reset_assessment(this);
	});
	$j('.bt-assessment .done').click(function(e){
		e.preventDefault();
		edit_assessment(this);
		if($j(this).attr('href').length > 0){
	    	window.location = $j(this).attr('href');
    	}
		
	});
	$j('.bt-assessment .publish').click(function(e){
		e.preventDefault();
		publish_assessment(this);
	});
	$j('.bt-assessment .unpublish').click(function(e){
		e.preventDefault();
		publish_assessment(this);
	});
	$j('.assessment a').click(function(e){
		e.preventDefault();
	});

});
