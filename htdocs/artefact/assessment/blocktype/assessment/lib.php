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

class PluginBlocktypeAssessment extends PluginBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.assessment/assessment/assessment');
    }

    public static function get_description() {
        return get_string('description1', 'blocktype.assessment/assessment');
    }

    public static function get_categories() {
        return array('general');
    }

    public static function get_viewtypes() {
        return array('portfolio');
    }

	
	public static function can_view_this_instance(BlockInstance $instance){
	    $configdata = $instance->get('configdata');
		if (!empty($configdata['artefactid'])) {
			$assessment = $instance->get_artefact_instance($configdata['artefactid']);
			return $assessment->user_can_view_assessment();
		}
		return false;
	}
	
    public static function render_instance(BlockInstance $instance, $editing=false) {
        $formstr = '';
		global $USER;
        $configdata = $instance->get('configdata');
        $smarty = smarty_core();
        $smarty->assign('id', $instance->get('id'));
        $assessment = null;
        if (!empty($configdata['artefactid'])) {
            $assessment =  $instance->get_artefact_instance($configdata['artefactid']);
        }
        $smarty->assign('canedit',PluginBlocktypeAssessment::allow_inlineediting($instance));
        $smarty->assign('canview',PluginBlocktypeAssessment::can_view_this_instance($instance));
        $smarty->assign('tutor',ArtefactTypeAssessment::TUTOR_ASSESSMENT);
        $smarty->assign('self',ArtefactTypeAssessment::SELF_ASSESSMENT);
        $smarty->assign('peer',ArtefactTypeAssessment::PEER_ASSESSMENT);
        $smarty->assign('assessment',$assessment);
		return $smarty->fetch('artefact:assessment:viewassessment.tpl');
		
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
        $configdata = $instance->get('configdata');
		if (!empty($configdata['artefactid'])) {
			$assessment = $instance->get_artefact_instance($configdata['artefactid']);
			return $assessment->user_can_edit_assessment();
		}
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
		$type = isset($assessment) ? $assessment->get('type') : null;
		$grade_type = isset($assessment) ? $assessment->get('grade_type'): null;
		$assessment_scheme = isset($assessment) ? $assessment->get('assessment_scheme'): null;
		$returnarr = array(
			'evaltype' => array(
                    'type'         => 'select',
                    'options' 		=> ArtefactTypeAssessment::$assessment_types,
                    'title'        => get_string('evaluationtype', 'blocktype.assessment/assessment'),
                    'description'  => get_string('evaluationtypedescription', 'blocktype.assessment/assessment'),
                    'defaultvalue' => (isset($type)) ? $type : ArtefactTypeAssessment::TUTOR_ASSESSMENT
                ),
			'gradetype' => array(
					'type'         => 'select',
                    'options'		=> $grade_type_options,
					'title'        => get_string('gradetype', 'blocktype.assessment/assessment'),
					'description'  => get_string('gradetypedescription', 'blocktype.assessment/assessment'),
					'defaultvalue' => isset($grade_type) ? $grade_type->id : null,//TODO:
					'required'		=>true,
				),
			'assessmentscheme' => array(
                    'type'         => 'select',
                    'options'		=> $assessment_scheme_options,
                    'title'        => get_string('assessmentscheme', 'blocktype.assessment/assessment'),
                    'description'  => get_string('assessmentschemedescription', 'blocktype.assessment/assessment'),
                    'defaultvalue' => isset($assessment_scheme) ? $assessment_scheme->id : null,//TODO:
					'required'		=>true,
                ),
            'artefactid' => array(
            		'type'	=> 'hidden',
            		'value' => isset($configdata['artefactid']) ? $configdata['artefactid']:null,
            		),
			'visibility' => array(
					'type'         => 'checkbox',
					'title'        => get_string('visibility', 'blocktype.assessment/assessment'),
					'description'  => get_string('visibilitydescription', 'blocktype.assessment/assessment'),
					'defaultvalue' => isset($assessment) ? $assessment->get('visibility'): null,
				),
		);
		return $returnarr;
    }
    
    
    public static function instance_config_save($values, $instance) {
        global $USER;
        $data = array();
        $view = $instance->get_view();

        foreach (array('owner', 'group', 'institution') as $f) {
            $data[$f] = $view->get($f);
        }

        if (empty($values['artefactid']) || (isset($values['makecopy']) && $values['makecopy'])) {
            $aretefacttitle = $view->get('title').' - '.get_string('title2', 'blocktype.assessment/assessment',ArtefactTypeAssessment::$assessment_types[$values['evaltype']]);
            $artefact = new ArtefactTypeAssessment(0, $data);
            if (empty($values['title'])) {
            	$values['title'] = get_string('title2', 'blocktype.assessment/assessment',ArtefactTypeAssessment::$assessment_types[$values['evaltype']]);
            }

            $artefact->set('title', $aretefacttitle);
            if (get_config('licensemetadata')) {
                $artefact->set('license', $values['license']);
                $artefact->set('licensor', $values['licensor']);
                $artefact->set('licensorurl', $values['licensorurl']);
            }
            
        }
        else {
            $artefact = new ArtefactTypeAssessment((int)$values['artefactid']);

            if (!$USER->can_publish_artefact($artefact)) {
                throw new AccessDeniedException(get_string('nopublishpermissiononartefact', 'mahara', hsc($artefact->get('title'))));
            }

			if (get_config('licensemetadata')) {
				$artefact->set('license', $values['license']);
				$artefact->set('licensor', $values['licensor']);
				$artefact->set('licensorurl', $values['licensorurl']);
			}
        }
        $artefact->set('description', '');//TODO: Make SOmethign useful for the description
        $artefact->set('type',(int)$values['evaltype']);
        $artefact->set('assessment_scheme',(int)$values['assessmentscheme']);
        $artefact->set('grade_type',(int)$values['gradetype']);
        $artefact->set('visibility',(int)$values['visibility']);

        $artefact->commit();

        $values['artefactid'] = $artefact->get('id');
        $instance->save_artefact_instance($artefact);

        unset($values['type']);
        unset($values['assessmentscheme']);
        unset($values['gradetype']);

        return $values;
    }
    
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

    public static function get_instance_javascript($instance) {
        return array(
            array(
                'file'   => 'js/assessment.js',
                'initjs' => "add_event_click_events();",
            )
        );

	}	
	
	public function assessment_submit(Pieform $form, $values) {
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
        if ($data = get_config_plugin('blocktype', 'assessment', 'types')) {
            return unserialize($data);
        }
        return array();
    }


    public static function default_copy_type() {
        return 'full';
    }

    public static function get_instance_title(BlockInstance $instance) {
        return get_string('title', 'blocktype.assessment/assessment');
    }}
