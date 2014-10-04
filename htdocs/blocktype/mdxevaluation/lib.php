<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2009 Catalyst IT Ltd and others; see:
 *                         http://wiki.mahara.org/Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage blocktype-newviews
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2010 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

defined('INTERNAL') || die();

class PluginBlocktypeMdxEvaluation extends SystemBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.mdxevaluation');
    }

    public static function get_description() {
        return get_string('description1', 'blocktype.mdxevaluation');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('portfolio');
    }

    public static function allow_inlineediting(BlockInstance $instance) {
		global $USER;
        $userid = (!empty($USER) ? $USER->get('id') : 0);
        //$view = 
        $configdata = $instance->get('configdata');
		$view = $instance->get_view();
		require_once('group.php');
        if(isset($configdata['inlineediting']) && $configdata['inlineediting']){
			if($configdata['evaltype'] == 1){
				//if it is self evaluation then the owner has to equal the user
				//and the view has to be not locked or submitted
				
				if($view->get('owner') == $userid && !$view->is_submitted()){
					return true;
				}
				else{
					return false;
				}
			} else if($configdata['evaltype'] == 2){
				//the user has to have access to the page but not be the current user
				// the page can be submitted
				if($view->get('owner') != $userid){
					return true;
				}else{
					return false;
				}
			} else if($configdata['evaltype'] == 3){
				//Page must be submitted AND
				// User must be a tutor of the group that it is submitted to
				$submitteddata = $view->submitted_to();
				if($submitteddata != NULL && group_user_can_assess_submitted_views($submitteddata['id'],$userid)){
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
    public static function render_instance(BlockInstance $instance, $editing=false) {
        $formstr = '';
		global $USER;
        $userid = (!empty($USER) ? $USER->get('id') : 0);
        if (!$editing && $userid != 0 && self::allow_inlineediting($instance)) {
			
	        $formstr .= self::evaluation_form($instance, false);
            $formstr .= self::mdxevaluation_js();
        }
        $configdata = $instance->get('configdata');
        $smarty = smarty_core();
        $smarty->assign('research', $configdata['research']);
        $smarty->assign('concept', $configdata['concept']);
        $smarty->assign('technical', $configdata['technical']);
        $smarty->assign('presentation', $configdata['presentation']);
        $smarty->assign('studentship', $configdata['studentship']);
        $smarty->assign('workbook', $configdata['workbook']);
        $smarty->assign('selfmark', $configdata['selfmark']);
        $smarty->assign('form', $formstr);
        $smarty->assign('id', $instance->get('id'));
		return $smarty->fetch('blocktype:mdxevaluation:mdxevaluation.tpl');
		
    }

    public function evaluation_form(BlockInstance $instance, $descriptions=true) {
        require_once('pieforms/pieform.php');

//		$retrunstr = "BOB<div><div>";
		$returnstr = "";
		$elements = self::instance_edit_form($instance);
/*		if(!$descriptions){
			foreach($elements as $element){
				unset($element['description']);
			}
		}*/
		$configdata = $instance->get('configdata');
		$elements = array_merge($elements,array(
			'instance' => array(
                    'type' => 'hidden',
                    'value' => $instance->get('id'),
                	),
			'evaltype' => array(
                    'type'         => 'hidden',
                    'value' 		=> $configdata['evaltype'],
                	),
			'inlineediting' => array(
                    'type'         => 'hidden',
                    'value' 		=> $configdata['inlineediting'],
                	),				
			'retractable' => array(
                    'type'         => 'hidden',
                    'value' 		=> $configdata['retractable'],
                	),				
			'retractedonload' => array(
                    'type'         => 'hidden',
                    'value' 		=> $configdata['retractedonload'],
                	),				
			'action_configureblockinstance_id_' . $instance->get('id') => array(
                    'type' => 'submitcancel',
                    'value' => array(get_string('update'),get_string('cancel')),
		            'goto' => View::make_base_url(),
                	),
					)
					);
        $returnstr .= pieform(array(
            'name'      => 'instconf_'.$instance->get('id'),
			'id'		=> 'instconf',
            'renderer'  => 'maharatable',
            'autofocus' => false,
            'jsform'    => true,
            'plugintype' => 'blocktype',
            'pluginname' => 'mdxevaluation',
//            'template'  => 'wallpost.php',
//            'templatedir' => pieform_template_dir('wallpost.php', 'blocktype/wall'),
            'validatecallback' => array('PluginBlocktypeMdxevaluation', 'mdxevaluation_validate'),
            'successcallback' => array('PluginBlocktypeMdxevaluation', 'mdxevaluation_submit'),
            'jssuccesscallback' => 'mdxevaluation_success',
            'elements' => $elements
			)
        );
//		$returnstr .= "</div></div>";
		return $returnstr;
        // TODO if replying here, add select element for replyto other wall or own wall
        // depending on if the user we're replying to has a wall
    }


    // Called by $instance->get_data('grades', ...).
    public static function get_instance_grades($id) {
        return get_record(
            'blocktype_mdxevaluation_data', 'id', $id, null, null, null, null,
            'id,url,link,title,description,content,authuser,authpassword,insecuresslmode,' . db_format_tsfield('lastupdate') . ',image'
        );
    }


    public static function instance_config_save($values) {
        // we need to turn the feed url into an id in the feed_data table..
/*        if (strpos($values['url'], '://') == false) {
            // try add on http://
            $values['url'] = 'http://' . $values['url'];
        }
        // We know this is safe because self::parse_feed caches its result and
        // the validate method would have failed if the feed was invalid
        $authpassword = !empty($values['authpassword']['submittedvalue']) ? $values['authpassword']['submittedvalue'] : (!empty($values['authpassword']['defaultvalue']) ? $values['authpassword']['defaultvalue'] : '');
        $data = self::parse_feed($values['url'], $values['insecuresslmode'], $values['authuser'], $authpassword);
        $data->content  = serialize($data->content);
        $data->image    = serialize($data->image);
        $data->lastupdate = db_format_timestamp(time());
        $wheredata = array('url' => $values['url'], 'authuser' => $values['authuser'], 'authpassword' => $authpassword);
        $id = ensure_record_exists('blocktype_externalfeed_data', $wheredata, $data, 'id', true);
        $values['feedid'] = $id;
        unset($values['url']);
        */
        return $values;

    }


    public static function has_instance_config() {
        return true;
    }
	
/*
	$elements['blog'] = array(
		'title' => get_string('blog', 'artefact.blog'),
		'type' => 'select',
        'options' => $blogs,
		'defaultvalue' => $blog,
	);
*/

    public static function instance_config_form($instance) {
        $configdata = $instance->get('configdata');
		$returnarr = self::instance_edit_form($instance);
		$returnarr = array_merge(array(
			'evaltype' => array(
                    'type'         => 'select',
                    'options' 		=> array(1 => 'Self',2 => 'Peer',3 => 'Tutor'),
                    'title'        => get_string('evaluationtype', 'blocktype.mdxevaluation'),
                    'description'  => get_string('evaluationtypedescription', 'blocktype.mdxevaluation'),
                    'defaultvalue' => (isset($configdata['evaltype'])) ? $configdata['evaltype'] : false
                )
			),array(
			'inlineediting' => array(
                    'type'         => 'checkbox',
                    'title'        => get_string('inlineediting', 'blocktype.mdxevaluation'),
                    'description'  => get_string('inlineeditingdescription', 'blocktype.mdxevaluation'),
                    'defaultvalue' => (isset($configdata['inlineediting'])) ? $configdata['inlineediting'] : false
                )
			),$returnarr
		);
		return $returnarr;
    }

	private function instance_edit_form($instance){
        $configdata = $instance->get('configdata');

		//TODO : get the data from the DB rather than the config data
		return array(
            'research' => array(
			'type' => 'radio',
            'title' => get_string('research', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            //'description' => get_string('researchdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['research'])) ? intval($configdata['research']) : 5,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
        	),
			'concept' => array(
			'type' => 'radio',
            'title' => get_string('concept', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            //'description' => get_string('conceptdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['concept'])) ? intval($configdata['concept']) : 5,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'technical' => array(
			'type' => 'radio',
            'title' => get_string('technical', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            //'description' => get_string('technicaldescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['technical'])) ? intval($configdata['technical']) : 5,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'presentation' => array(
			'type' => 'radio',
            'title' => get_string('presentation', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            //'description' => get_string('presentationdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['presentation'])) ? intval($configdata['presentation']) : 5,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'studentship' => array(
			'type' => 'radio',
            'title' => get_string('studentship', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            //'description' => get_string('studentshipdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['studentship'])) ? intval($configdata['studentship']) : 5,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
       	),
			'workbook' => array(
			'type' => 'radio',
            'title' => get_string('workbook', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => get_string('exc', 'blocktype.mdxevaluation'),
                                   2 => get_string('vgood', 'blocktype.mdxevaluation'),
                                   3 => get_string('good', 'blocktype.mdxevaluation'),
                                   4 => get_string('pass', 'blocktype.mdxevaluation'),
                                   5 => get_string('fail', 'blocktype.mdxevaluation'),
                                   ),
            //'description' => get_string('workbookdescription', 'blocktype.mdxevaluation'),
            'defaultvalue' => (isset($configdata['workbook'])) ? intval($configdata['workbook']) : 5,
			'separator' => ' | ',
                'help' => true,
                'rules' => array('required'    => true),
        	),
			'selfmark' => array(
            'type' => 'radio',
            'title' => get_string('grade', 'blocktype.mdxevaluation'),
            //'description' => get_string('selfmarkdescription', 'blocktype.mdxevaluation'),
			'options' => array(
                                   1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',6 => '6',7 => '7',8 => '8',9 => '9',10 => '10'
								   ,11 => '11',12 => '12',13 => '13',14 => '14',15 => '15',16 => '16',17 => '17'
                                   ),
            'defaultvalue' => (isset($configdata['selfmark'])) ? intval($configdata['selfmark']) : 17,
			'rules' => array('required'    => true),
                'help' => true,
			)
		);
	}
	
	
    public function mdxevaluation_js() {
        $js = <<<EOF
function mdxevaluation_success(form, data) {
		window.location.replace(data.goto);
        
    }
EOF;
        return "<script>$js</script>";
    }

	
	
public function mdxevaluation_submit(Pieform $form, $values) {
        global $SESSION, $USER;
        $instance = new BlockInstance($values['instance']);

        // Destroy form values we don't care about
        unset($values['sesskey']);
        unset($values['blockinstance']);
        unset($values['action_configureblockinstance_id_' . $instance->get('id')]);
        unset($values['blockconfig']);
        unset($values['id']);
        unset($values['change']);
        unset($values['new']);


        $redirect = get_config('wwwroot').'/view/view.php?id=' . $instance->get('view');
        //$redirect = '/view/index.php';

        $result = array(
            'block'    => $values['instance'],
            'goto' => $redirect,
        );


        $instance->set('configdata', $values);
		$rendered = '';
        $instance->commit();
        $form->reply(PIEFORM_OK, $result);
    }

	
	/*this is to get the types being used TODO: add the types to the plugin configuration and then use them*/
    private static function get_types() {
        if ($data = get_config_plugin('blocktype', 'mdxevaluation', 'types')) {
            return unserialize($data);
        }
        return array();
    }


    public static function default_copy_type() {
        return 'shallow';
    }

    public static function get_instance_title(BlockInstance $instance) {
        return get_string('title', 'blocktype.mdxevaluation');
    }
}
