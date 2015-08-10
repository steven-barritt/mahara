var _throttleTimer = null;
var _throttleDelay = 50;
var _pagenumber = 0;

var offset = 0;

function display_posts(post){
	$j(window).off('scroll', ScrollHandler);
    
    sendjsonrequest('./blocktype/newsfeed/newsfeed.json.php',
        {'limit': 5, 'offset': offset},
        'GET',
        function(data) {
			$j("#recentblogpost").append(data['data']);
			offset += 5;
			clearTimeout(_throttleTimer);
			_throttleTimer = setTimeout(function () {
				$j(window).on('scroll', ScrollHandler);
			});
			if(data['data'] == ''){
				$j('#loading').addClass('hidden');
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
//        console.log('scroll');

        //do work
//		alert(getDocHeight());
//		alert($j(window).scrollTop());
//		alert($j(window).height());
        if ($j(window).scrollTop() + $j(window).height() >= getDocHeight()-2000) {
			//alert('bob');
			//var blog = eval($.ajax({url:"blog/page/10"}));	
			
          // do not make ajax req
//			$j('#loading').toggleClass('hidden')		
			display_posts();
			
			
            //alert("near bottom!");
        }

    }, _throttleDelay);
}

function add_click_events() {
    			display_posts();

    forEach(getElementsByTagAndClassName('a', 'showmore'), function(link) {
		connect(link, 'onclick', function(e) {
    			e.preventDefault();
        });
    });
};