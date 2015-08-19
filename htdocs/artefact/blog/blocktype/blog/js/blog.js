var _throttleTimer = null;
var _throttleDelay = 500;
var _pagenumber = 0;

var offset = 0;
var $j = jQuery;


function display_posts(post){
	$j(window).off('scroll', ScrollHandler);
        //this is well dodgy and assumes only one postlist on the page
    var postlists = $j('[id^=postlist_]');
	var dstr = postlists[0].id;
	var block = dstr.split("_").pop();

    sendjsonrequest('../artefact/blog/posts.json.php',
        {'limit': 5, 'offset': offset,'block': block},
        'GET',
        function(data) {
			if(data['data']['tablerows'] != ''){
				var elems = $j(data['data']['tablerows']);
				$j("#postlist_"+block).append(elems);
				$j("#postlist_"+block).imagesLoaded(function(){
					$j("#postlist_"+block).masonry('appended',elems);
					//$j("#postlist_"+block).masonry('layout');
					$j(".viewpost").each(function(){
						$j(this).featherlight($j(this).find( ".longpost" ));
						});
/*					$j(".shortpost a").each(function(){
						$j(this).featherlight($j(this).closest(".viewpost").find( ".longpost" ));
						});*/
					$j(".expand").off().on('click',function(e) {
						e.preventDefault();
						$j(e.target).closest(".viewpost").find( ".shortpost" ).toggleClass("hidden");
						$j(e.target).closest(".viewpost").find( ".longpost" ).toggleClass("hidden");
						$j(e.target).closest(".viewpost").find( ".expand" ).toggleClass("hidden");
						$j("#postlist_"+block).masonry('layout');
						return false;  
					});
/*					
$j(".viewpost a").off().on('click',function(e) {
						e.preventDefault();
						$j(e.target).closest(".viewpost").find( ".shortpost" ).toggleClass("hidden");
						$j(e.target).closest(".viewpost").find( ".longpost" ).toggleClass("hidden");
						$j("#postlist_"+block).masonry('layout');
						return false;  
					});*/
				});
				offset += 5;
				clearTimeout(_throttleTimer);
				_throttleTimer = setTimeout(function () {
					$j(window).on('scroll', ScrollHandler);
				});
			}else{
				$j('#loading').addClass('hidden');
				$j('#loaded').removeClass('hidden');
			}
        },
        function(){
			clearTimeout(_throttleTimer);
			_throttleTimer = setTimeout(function () {
				$j(window).on('scroll', ScrollHandler);
			});
			$j('#loading').toggleClass('hidden')		
        }
        );

};


function getDocHeight() { 
var D = document; 
return Math.max( D.body.scrollHeight, D.documentElement.scrollHeight, D.body.offsetHeight, D.documentElement.offsetHeight, D.body.clientHeight, D.documentElement.clientHeight ); 
}

function ScrollHandler(e) {
    //throttle event:
    clearTimeout(_throttleTimer);
    _throttleTimer = setTimeout(function () {
        if ($j(window).scrollTop() + $j(window).height() >= getDocHeight()-800) {
			display_posts();
        }

    }, _throttleDelay);
}


$j(document).ready(function(){
	$j(".viewpost a").click(function(e) {
		e.preventDefault();
    	alert('clicked');  
    	return false;  
	});
});




jQuery(window).load(function(){
	jQuery('.postlist').masonry({
		  // options
		  itemSelector: '.viewpost',
		  columnWidth: '.grid-sizer',
		  percentPosition: true,
		  gutter:0
		  	});
	display_posts();
/*	clearTimeout(_throttleTimer);
	_throttleTimer = setTimeout(function () {
		$j(window).on('scroll', ScrollHandler);
	});*/
});

