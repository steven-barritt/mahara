function add_event_click_events() {
    forEach(getElementsByTagAndClassName('a', 'event_title'), function(link) {
		connect(link, 'onclick', function(e) {
    			e.preventDefault();
    			var details = getFirstElementByTagAndClassName('div', 'detail', this.parentNode.parentNode);
    			toggleElementClass("hidden",details);
        });
    });
};