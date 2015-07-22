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

    	global $USER;
        $userid = $instance->get_view()->get('owner');
        if (!$userid) {
            return '';
        }

		$owner = $userid == $USER->get('id');
		$isstaff = $USER->is_staff_for_user(new User($userid));
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
        //TODO: This should be put into a function somewhere or where the grades are stored should be worked out better
        //there must be a better way to link the grades to the view more easily rather than this whole rigmerole.
        foreach($views as &$view){
			$published = false;
			$grade = 20;
			require_once(get_config('docroot') . 'blocktype/lib.php');
	
			$sql = "SELECT bi.*
					FROM {block_instance} bi
					WHERE bi.view = ?
					AND bi.blocktype = 'mdxevaluation'
					";
			if (!$evaldata = get_records_sql_array($sql, array($view['id']))) {
				$evaldata = array();
			}
	
			foreach ($evaldata as $eval){
				$bi = new BlockInstance($eval->id, (array)$eval);
				$configdata = $bi->get('configdata');
				if(isset($configdata['evaltype'])){
					if($configdata['evaltype'] == 3){
						$published = isset($configdata['published']) ? $configdata['published'] : false;
						$grade = isset($configdata['selfmark']) ? $configdata['selfmark'] : 20;
					}
				}		
			}
			$view['grade'] = $grade;
			$view['published'] = $published;
			$view['duedate'] = View::due_date($view['id']);
		}
		
        $smarty->assign('isstaff',$isstaff);
        $smarty->assign('owner',$owner);
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
