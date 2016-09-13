<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-blog
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();
safe_require('artefact', 'assessment');

class PluginBlocktypeAverageAssessment extends PluginBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.assessment/averageassessment');
    }

    public static function get_description() {
        return get_string('description1', 'blocktype.assessment/averageassessment');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('profile');
    }

	
	public static function can_view_this_instance(BlockInstance $instance){
/*	    $configdata = $instance->get('configdata');
		if (!empty($configdata['artefactid'])) {
			$assessment = $instance->get_artefact_instance($configdata['artefactid']);
			return $assessment->user_can_view_assessment();
		}*/
		//can view this if you are staff or the owner
		global $USER;
		$owner = $instance->get_view()->get_owner_object();
		if($owner->id == $USER->get('id')){
			return true;
		}else{		
			return $USER->is_staff_for_user($owner);		
		}
	}
	
    public static function render_instance(BlockInstance $instance, $editing=false) {
        $formstr = '';
		global $USER;
        $configdata = $instance->get('configdata');
        $smarty = smarty_core();
        $smarty->assign('id', $instance->get('id'));
        $assessment = null;
        $assessmentscheme = new AssessmentScheme($configdata['assessmentscheme']);
        $type = ArtefactTypeAssessment::TUTOR_ASSESSMENT;// this should be an option
		//we need to know the user can we get the page owner from the instance?
		$owner = $instance->get_view()->get('owner');
        $assessmentscheme->get_user_averages($owner,$type);
        $smarty->assign('assessmentscheme', $assessmentscheme);
        
        $smarty->assign('canview',PluginBlocktypeAverageAssessment::can_view_this_instance($instance));
		return $smarty->fetch('blocktype:averageassessment:viewaverageassessment.tpl');
		
    }



    // Called by $instance->get_data('grades', ...).
    public static function get_instance_grades($id) {
        return get_record(
            'blocktype_assessment_data', 'id', $id, null, null, null, null,
            'id,url,link,title,description,content,authuser,authpassword,insecuresslmode,' . db_format_tsfield('lastupdate') . ',image'
        );
    }




    public static function has_instance_config() {
        return true;
    }
	
    public static function allow_inlineediting(BlockInstance $instance) {
/*        $configdata = $instance->get('configdata');
		if (!empty($configdata['artefactid'])) {
			$assessment = $instance->get_artefact_instance($configdata['artefactid']);
			return $assessment->user_can_edit_assessment();
		}*/
		return false;
	}

    public static function instance_config_form($instance) {
		global $USER;
		$insts = array();
		$institutions = $USER->get('institutions');
        $configdata = $instance->get('configdata');
        if (!empty($configdata['artefactid'])) {
            $assessment = $instance->get_artefact_instance($configdata['artefactid']);
        }
		$grade_type_options = GradeType::get_grade_type_options();
		$assessment_scheme_options = AssessmentScheme::get_assessment_scheme_options($institutions);
//		$grade_type = isset($configdata['grade_type']) ? $configdata['grade_type']: null;
		$assessment_scheme = isset($configdata['assessmentscheme']) ? $configdata['assessmentscheme']: null;
		$returnarr = array(
			'assessmentscheme' => array(
                    'type'         => 'select',
                    'options'		=> $assessment_scheme_options,
                    'title'        => get_string('assessmentscheme', 'blocktype.assessment/averageassessment'),
                    'description'  => get_string('assessmentschemedescription', 'blocktype.assessment/averageassessment'),
                    'defaultvalue' => isset($assessment_scheme) ? $assessment_scheme : null,//TODO:
					'required'		=>true,
                ),
		);
		return $returnarr;
    }
    
    
/*    public static function instance_config_save($values, $instance) {
        return $values;
    }
 */   
    public static function artefactchooser_element($default=null) {
        return array(
            'name'             => 'artefactid',
            'type'             => 'artefactchooser',
            'class'            => 'hidden',
            'defaultvalue'     => $default,
            'blocktype'        => 'assessment',
            'limit'            => 5,
            'selectone'        => true,
            'getblocks'        => true,
            'artefacttypes'    => array('assessment'),
            'template'         => 'artefact:assessment:assessment-artefactchooser-element.tpl',
        );
    }


    public static function default_copy_type() {
        return 'full';
    }

    public static function get_instance_title(BlockInstance $instance) {
        return get_string('title', 'blocktype.assessment/averageassessment');
    }}
