<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-myviews
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

class PluginBlocktypeMyviews extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.myviews');
    }

    public static function get_description() {
        return get_string('description', 'blocktype.myviews');
    }

    public static function single_only() {
        return true;
    }

    public static function get_categories() {
        return array('internal');
    }

    public static function get_viewtypes() {
        return array('profile', 'dashboard');
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {

        $userid = $instance->get_view()->get('owner');
        if (!$userid) {
            return '';
        }

        $smarty = smarty_core();

        // Get viewable views
             /* @param string   $query       Search string
     * @param string   $ownerquery  Search string for owner
     * @param StdClass $ownedby     Only return views owned by this owner (owner, group, institution)
     * @param StdClass $copyableby  Only return views copyable by this owner (owner, group, institution)
     * @param integer  $limit
     * @param integer  $offset
     * @param bool     $extra       Return full set of properties on each view including an artefact list
     * @param array    $sort        Order by, each element of the array is an array containing "column" (string) and "desc" (boolean)
     * @param array    $types       List of view types to filter by
     * @param bool     $collection  Use query against collection names and descriptions
     * @param array    $accesstypes Only return views visible due to the given access types
     * @param array    $tag         Only return views with this tag
     * @param bool		$copynewuser	only get views which can be copied to new users of a group
*/
        //TODO - in here get if the current user is institution staff then pass this to the search in order to get all pages
        $accesstypes = null;
    	global $USER;
        $staff = $USER->get('staff');
        if($staff){
        	$accesstypes = array('staff');
        }
        //$query=null, $ownerquery=null, $ownedby=null, $copyableby=null, $limit=null, $offset=0,
        //                               $extra=true, $sort=null, $types=null, $collection=false, $accesstypes=null, $tag=null,$copynewuser=false, $getbloginfo=false
        $sort = array(array('column'=>'submittedtime','desc'=>false));
        $views = View::view_search(null, null, (object) array('owner' => $userid), null, null, 0, true, $sort, array('portfolio'),false,$accesstypes,null,false,true);
//        $views = View::view_search(null, null, (object) array('owner' => $userid), null, null, 0, true, null, array('portfolio'),false,$accesstypes,null,false,true);
        
        $views = $views->count ? $views->data : array();
        
        $smarty->assign('VIEWS',$views);
        return $smarty->fetch('blocktype:myviews:myviews.tpl');
    }

    public static function has_instance_config() {
        return false;
    }

    public static function default_copy_type() {
        return 'shallow';
    }

    /**
     * Myviews only makes sense for personal views
     */
    public static function allowed_in_view(View $view) {
        return $view->get('owner') != null;
    }

    public static function override_instance_title(BlockInstance $instance) {
        global $USER;
        $ownerid = $instance->get_view()->get('owner');
        if ($ownerid === null || $ownerid == $USER->get('id')) {
            return get_string('title', 'blocktype.myviews');
        }
        return get_string('otherusertitle', 'blocktype.myviews', display_name($ownerid, null, true));
    }

}
