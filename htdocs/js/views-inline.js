/**
 * Javascript for the views interface
 * @source: http://gitorious.org/mahara/mahara
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 * @copyright  (C) 2013 Mike Kelly UAL m.f.kelly@arts.ac.uk
 *
 TODO
 find blockinstance-controls
 replace default action
 on click hide content and show form
 TODO form need scancel button as well
 Think this should belog to the blocktype not be general
 
 
 TODO this is currently a hack for mdxevaluation it needs to be more generic so that it can be used by any blockintance
 ideally it would call a function on the block instance to get a form like the edit page does. This form could then be different from the 
 main config form and provide a simplified edit for just the content for example.
 */
 
     /* Rewrites the blockinstance configure buttons to be AJAX
     */
	 

(function( ViewManager, $, undefined ) {    
	function rewriteConfigureButtons() {
		//var bottomPane = $('#bottom-pane').each();
        $('#bottom-pane input.configurebutton').each(function() {
            rewriteConfigureButton($(this));
        });
    }

    /**
     * Rewrites one configure button to be AJAX
     */
    function rewriteConfigureButton(button) {
        button.click(function(event) {
            event.stopPropagation();
            event.preventDefault();
            toggleForm(button.closest('div.blockinstance'));
        });
    }
	
	function toggleForm(blockinstance){
        /*var form = blockinstance.find('div.inline-form');*/
        var blockinstanceId = blockinstance.attr('id').substr(blockinstance.attr('id').lastIndexOf('_') + 1);
        var contentDiv = blockinstance.find('div.blockinstance-content');
		var form = contentDiv.find('div.inline-form');
		var mainContent = contentDiv.find('div.blockinstance-content-view');
		mainContent.addClass('hidden');
		form.removeClass('hidden');
	}


     $(document).ready(function() {
    	rewriteConfigureButtons();	
    });

}( window.ViewManager = window.ViewManager || {}, jQuery ));
// JavaScript Document