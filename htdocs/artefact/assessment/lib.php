<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-assessment
 * @author     Steven Barritt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

class PluginArtefactAssessment extends PluginArtefact {

    public static function get_artefact_types() {
        return array(
            'assessment',
        );
    }

    public static function get_block_types() {
        return array();
    }
    
    public static function has_progressbar_options() {
        return false;
    }

    public static function get_plugin_name() {
        return 'assessment';
    }

    public static function is_active() {
        return get_field('artefact_installed', 'active', 'name', 'assessment');
    }

    public static function menu_items() {
        return array(
				'manageinstitutions/institutionassessment' => array(
				'path'   => 'manageinstitutions/institutionassessment',
				'url'    => 'artefact/assessment/institutionassessment.php',
				'title'  => 'bob',
				'weight' => 70,
			),
    	);
		/*
        return array(
            'content/plans' => array(
                'path' => 'content/plans',
                'url'  => 'artefact/plans/index.php',
                'title' => get_string('Plans', 'artefact.plans'),
                'weight' => 60,
            ),
        );*/
    }

}

class ArtefactTypeAssessment extends ArtefactType {

	const TUTOR_ASSESSMENT = 0;
	const SELF_ASSESSMENT = 1;
	const PEER_ASSESSMENT = 2;
	public static $assessment_types = array(self::TUTOR_ASSESSMENT => 'Tutor',self::SELF_ASSESSMENT => 'Self', self::PEER_ASSESSMENT => 'Peer');
    protected $published = false;
	protected $grade;
	protected $type = self::TUTOR_ASSESSMENT;
	protected $visibility = true;
	protected $grade_type;
	protected $assessment_scheme;
	
    public function __construct($id = 0, $data = null) {
        parent::__construct($id, $data);

        if ($this->id && ($extra = get_record('artefact_assessment', 'assessment', $this->id))) {
            foreach($extra as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->{$name} = $value;
                }
            }
            if(isset($this->assessment_scheme)){
            	$this->assessment_scheme = new AssessmentScheme($this->assessment_scheme,null,$this->id);
            }
            if(isset($this->grade_type)){
            	$this->grade_type = new GradeType($this->grade_type);
            }
        }
    }

    public function set($field, $value) {
        if (property_exists($this, $field)) {
        	//exceptions to the rule we set the value with the id so need to make it an object
        	if($field == 'type'){
        		switch($value){
        			case self::TUTOR_ASSESSMENT :
        				$this->dirty = true;
	        			$this->type = self::TUTOR_ASSESSMENT;
	        			$this->published = false;
        				break;
        			case self::SELF_ASSESSMENT :
        				$this->dirty = true;
	        			$this->type = self::SELF_ASSESSMENT;
	        			$this->published = true;
        				break;
        			case self::PEER_ASSESSMENT :
        				$this->dirty = true;
	        			$this->type = self::PEER_ASSESSMENT;
	        			$this->published = true;
        				break;
        		}
        	}
        	if($field == 'grade_type'){
        		if(is_int($value)){
        			if(!isset($this->grade_type) || $this->grade_type->id != $value){
        				$this->dirty = true;
	        			$this->grade_type = new GradeType($value);
	        		}
        		}elseif(is_object($value) && get_class($value) == 'GradeType'){
        			if(!isset($this->grade_type) || $this->grade_type->id != $value->id){
	        			$this->grade_type = $value;
        				$this->dirty = true;
	        		}
        			
        		}else{        		
	        		throw new InvalidArgumentException("Wrong type given for GradeType should be integer or GradeType and must exists " . get_class($this));
	        	}
        	}
        	elseif($field == 'assessment_scheme'){
        		if(is_int($value)){
        			if(!isset($this->assessment_scheme) || $this->assessment_scheme->id != $value){
        				$this->dirty = true;
	        			$this->assessment_scheme = new AssessmentScheme($value);
	        		}
        		}elseif(is_object($value) && get_class($value) == 'AssessmentScheme'){
        			if(!isset($this->assessment_scheme) || $this->assessment_scheme->id != $value->id){
	        			$this->assessment_scheme = $value;
        				$this->dirty = true;
	        		}
        			
        		}else{        		
	        		throw new InvalidArgumentException("Wrong type given for GradeType should be integer or GradeType and must exists " . get_class($this));
	        	}
        	
        	}else{
				//DEFAULT behavour
				if ($this->{$field} != $value) {
					// Only set it to dirty if it's changed.
					$this->dirty = true;
					// Set oldparent only if it has changed.
					if ($field == 'parent') {
						$this->oldparent = $this->parent;
					}
				}
				$this->{$field} = $value;
			}
            if ($field == 'mtime') {
                $this->mtimemanuallyset = true;
            }
            else if (!$this->mtimemanuallyset) {
                $this->mtime = time();
            }
            return true;
        }
        throw new InvalidArgumentException("Field $field wasn't found in class " . get_class($this));
    }

    public function commit() {

        if (empty($this->dirty)) {
            return;
        }

		//These should be set as we cannot have an assessment without them so thow an exception
		if(!isset($this->grade_type) || !isset($this->assessment_scheme)){
	        throw new InvalidArgumentException("You have not set the grade_type or assessment_scheme - " . get_class($this));
		}
        $new = empty($this->id);

        db_begin();

        parent::commit();


        $data = (object)array(
            'assessment'      => $this->id,
            'published'        => $this->published,
            'grade'    => $this->grade,
            'type'       => $this->type,
            'visibility'     => $this->visibility,
            'grade_type' => $this->grade_type->id,
            'assessment_scheme' => $this->assessment_scheme->id,
        );

        if ($new) {
            insert_record('artefact_assessment', $data);
        }
        else {
            update_record('artefact_assessment', $data, 'assessment');
        }

        db_commit();
        $this->dirty = false;
	}

    public static function get_links($id) {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/assessment/assessment.php?id=' . $id,
        );
    }


    public function delete() {
        if (empty($this->id)) {
            return;
        }
        db_begin();
        $this->detach();
        delete_records('artefact_assessment', 'assessment', $this->id);
        delete_records('artefact_assessment_results','assessment',$this->id);
        parent::delete();
        db_commit();
    }

    public static function bulk_delete($artefactids) {
        if (empty($artefactids)) {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select('artefact_assessment', 'assessment IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }


    public static function get_icon($options=null) {
        global $THEME;
        return $THEME->get_url('images/assessment.png', false, 'artefact/assessment');
    }

    public static function is_singular() {
        return false;
    }

	/*
	This function gets all the results for the assessment they combine the criteria 
	into one thing from the scheme
	*/
	private function get_results(){
		
	}

	/*
	function returns an array with the assessment scheme title and description
	as well as the criteria
	*/
	public function get_scheme_results(){
		return array();
	}

    public function render_self($options) {
    	global $THEME;
		$smarty = smarty_core();
        $smarty->assign('canview',$this->user_can_view_assessment());
		$smarty->assign('criteria',$this->assessment_scheme->criteria);
		$smarty->assign('assessment',$this);
        $smarty->assign('artefacttitle', hsc($this->title));
        $css = $THEME->get_url('style/style.css', false, 'artefact/assessment');
        return array('html' => $smarty->fetch('artefact:assessment:view.tpl'), 'javascript' => '', 'css' => $css);
		
    }

	//this function returns a view object if the artefact is on on a view
	private function get_view(){
		//assessments should only appear on one view
		$views = $this->get_views_instances();
		return $views[0];
	}
	
	public function user_can_edit_assessment(){
		global $USER;
        $userid = (!empty($USER) ? $USER->get('id') : 0);
        //$view = 
		$view = $this->get_view();
		require_once('group.php');
        if(isset($view)){
			if($this->type == self::SELF_ASSESSMENT){
				//if it is self evaluation then the owner has to equal the user
				//and the view has to be not locked or submitted
				if($view->get('owner') == $userid && !$view->is_submitted()){
					return true;
				}
				else{
					return false;
				}
			} else if($this->type == self::PEER_ASSESSMENT){
				//the user has to have access to the page but not be the current user
				// the page can be submitted
				if($view->get('owner') != $userid){
					return true;
				}else{
					return false;
				}
			} else if($this->type == self::TUTOR_ASSESSMENT){
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

	public function user_can_view_assessment(){
		global $USER;
        $userid = (!empty($USER) ? $USER->get('id') : 0);
        //$view = 
		$view = $this->get_view();
		require_once('group.php');
        if(isset($view)){
			if($this->type == self::TUTOR_ASSESSMENT){
				
				if($view->get('owner') == $userid){
					//always let the owner see the evaluation
					//but not if it is unpublished
					if($this->published){
						return true; 
					}else{
						return false;
					}
				}else{
					$submitteddata = $view->submitted_to();
					//for now it only lets the tutor of the gorup see it but should let all tutors see it.
					//$submitteddata = $view->submitted_to();
					if($submitteddata != NULL && (group_user_can_assess_submitted_views($view->get('id'),$userid) || $view->is_staff_or_admin_for_page())){
						return true;
					}else{
						return false;
					}
				}

			}else{
				return true;
			}
		}else{
			return false;
		}		
		
	}

	
	//TODO: these might be better as  static functions that directly manipulate the DB
	//so when we are doing json requests it is not creating the whole object each time
	//which might access the DB excessivley

	public function set_criteria_result($criteria, $level){
		if($this->user_can_edit_assessment()){
//			$valid_criteria = array_search($criteria,array_column($this->assessment_scheme->criteria,'id'));
			$valid_criteria = false;
			foreach($this->assessment_scheme->criteria as &$crit) {
				if ($criteria == $crit->id) {
					$criteria = $crit;
					$valid_criteria = true;
					break;
				}
			}
			if($valid_criteria){
				$grade = $criteria->grade_type->get_mean_grade($level);
				//Error check the values
				if($grade == null){
					return false;
				}
				$data = new stdClass();
				$data->assessment = $this->id;
				$data->criteria = $criteria->id;
				$data->grade = $grade;
				db_begin();
				delete_records('artefact_assessment_results', 'assessment', $this->id, 'criteria', $criteria->id);
				insert_record('artefact_assessment_results', $data);
				db_commit();
				$criteria->grade = $grade;
				$this->update_grade();
				return true;
			}
			return false;
		}else{
			throw new AccessDeniedException(get_string('accessdenied','artefact.assessment'));
		}
	}
	
	private function update_grade(){
		$grade = 0;
		$count = 0;
		foreach($this->assessment_scheme->criteria as $criteria){
			if(isset($criteria->grade) && $criteria->grade >= 0){
				$count++;
				$grade += $criteria->grade;
			}
		}
		//this should mark as dirty and on destruct it will commit??
		if($count > 0){
			$this->set('grade',round($grade/$count,1));
		}else{
			$this->set('grade',0);
		}
		//push the commit. again in the json world this will happen everytime someone clicks on the assessment
		//this might cause far too much DB action.
		$this->commit();
	}

	public function change_published_state($newstate = null){
		//pass it the state to change it to
		//todo here we need to raise a notification for the user that the grade has been published

		if(isset($newstate)){
			$this->published = (bool)$newstate;
		}else{
			//toggle the current state
			$this->set('published',!$this->published);
			//TODO: logic to notify about state change
		}
	}

    public static function is_countable_progressbar() {
        return false;
    }

    public static function publish_grade($viewid, $type=self::TUTOR_ASSESSMENT){
    	//this is  statuc function tht tweaks the db directly so as not to have to load te object first
    	//this should be much quicker when running through lots of them to publish
    	
    	//todo here we need to raise a notification for the user that the grade has been published
		//activity_occurred('feedback', $activity, 'artefact', 'comment');
		$sql = "update {artefact_assessment} aa set aa.published = 1
					Where aa.assessment in (
					select va.artefact from {view_artefact} va 
					join {artefact} a on va.artefact = a.id
					where va.view = ? AND a.artefacttype = 'assessment' ) and aa.type = ?";
		execute_sql($sql,
			array($viewid,$type)
		);

    }

    
    public static function get_view_grade($viewid, $type=self::TUTOR_ASSESSMENT){
    	//what if there is the possibility of mulitple grades???
    	/*
			select aagl.title from artefact_assessment_grade_level aagl 
			join artefact_assessment_grade_type aagt on aagl.grade_type = aagt.id
			join artefact_assessment a_s on aagt.id = a_s.grade_type
			join view_artefact va on a_s.assessment = va.artefact
			where va.view = 153 and a_s.type = 0
			and
			aagl.min_percent <= (
			select aa.grade from view_artefact va 
			join artefact_assessment aa on va.artefact = aa.assessment
			where
			va.view = 153
			and aa.type = 0) and aagl.max_percent >= (
			select aa.grade from view_artefact va 
			join artefact_assessment aa on va.artefact = aa.assessment
			where
			va.view = 153
			and aa.type = 0)
    	*/
    	$sql = "select aagl.title as grade, a_s.published as published, a_s.visibility as visible from {artefact_assessment_grade_level} aagl 
			join {artefact_assessment_grade_type} aagt on aagl.grade_type = aagt.id
			join {artefact_assessment} a_s on aagt.id = a_s.grade_type
			join {view_artefact} va on a_s.assessment = va.artefact
			where va.view = ? and a_s.type = ?
			and
			aagl.min_percent <= (
			select aa.grade from {view_artefact} va 
			join {artefact_assessment} aa on va.artefact = aa.assessment
			where
			va.view = ?
			and aa.type = ?) and aagl.max_percent >= (
			select aa.grade from {view_artefact} va 
			join {artefact_assessment} aa on va.artefact = aa.assessment
			where
			va.view = ?
			and aa.type = ?)";
		if($grade = get_record_sql($sql,array($viewid,$type,$viewid,$type,$viewid,$type))){
			return $grade;
		}else{
			//this is really bad bullshit
			switch($type){
				case self::TUTOR_ASSESSMENT :
					$type = 3;
					break;
				case self::PEER_ASSESSMENT :
					$type = 2;
					break;
				case self::SELF_ASSESSMENT :
					$type = 3;
					break;
			}
			$return = new stdClass();
			$grade = null;
			$published = null;
			//fall back to old school method
			require_once(get_config('docroot') . 'blocktype/lib.php');

			$sql = "SELECT bi.*
					FROM {block_instance} bi
					WHERE bi.view = ?
					AND bi.blocktype = 'mdxevaluation'
					";
			if (!$evaldata = get_records_sql_array($sql, array($viewid))) {
				$evaldata = array();
			}

			foreach ($evaldata as $eval){
				$bi = new BlockInstance($eval->id, (array)$eval);
				$configdata = $bi->get('configdata');
				if(isset($configdata['evaltype'])){
					if($configdata['evaltype'] == $type){
						$published = isset($configdata['published']) ? $configdata['published'] : false;
						$grade = isset($configdata['selfmark']) ? $configdata['selfmark'] : 20;
					}
				}		
			}
			if(isset($grade)){
				$return->grade = $grade;
				$return->published = $published;
				return $return;
			}		
			return false;
		}
    	
    }
}

/*
class ActivityTypeInteractionAssessmentPublished extends ActivityTypePlugin {
}
*/

class AssessmentObject{
}

class GradeType{
	public $id;
	public $title;
	public $description;
	public $grade_levels;
	public $colspan = 1;
	public function __construct($id = 0, $data = null) {
		//we have an existing object in the DB so fetch it.
        if (!empty($id)) {
            if (empty($data)) {
                if (!$data = get_record('artefact_assessment_grade_type','id',$id)) {
                    throw new ArtefactNotFoundException(get_string('gradertypenotfound', 'artefact.assessment', $id));
                }
            }
            $this->id = $id;
        }
        if (empty($data)) {
            $data = array();
        }
        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

        // load levels
        if ($this->id) {
            $this->grade_levels = GradeType::get_levels($this->id);
            if(count($this->grade_levels) > 0){
            	if($this->grade_levels[0]->mean_percent == -1){
		            $this->colspan = round(100/(count($this->grade_levels)-1));
		        }else{
		            $this->colspan = round(100/count($this->grade_levels));
		        }
	        }
        }

	}
	
	public static function get_levels($id){
		$levels = array();
		if($records = get_records_array('artefact_assessment_grade_level','grade_type',$id,'min_percent')){
			foreach($records as $data){
				$data->rubric = null;//this is for the rubric mapping
				$levels[] = $data;
			}
		}
		return $levels;		
	}
	
	/*pass it an array containing the following representinb the rubric
	stdObject(
		title
		description
		min_percent
		max_percent
		)
	*/
	public function map_rubric_to_levels($rubric){
		$precision = 0.01;//this is because these levels are floats
		foreach($this->grade_levels as &$level){
			foreach($rubric as $rublevel){
				if( ($level->mean_percent - $rublevel->min_percent) >= $precision and ($level->mean_percent -  $rublevel->max_percent) <= $precision )
				{
				  $level->rubric = $rublevel;
				  break;
				}
			}
		}
	}
	
	//pass it the id of the gradetype_level and get the mean percent
	public function get_mean_grade($id){
		foreach($this->grade_levels as $level){
			if($level->id == $id){
				return $level->mean_percent;
			}
		}
		return null;
	}

	//pass it the id of the gradetype_level and get the mean percent
	public function get_level_id($grade){
		foreach($this->grade_levels as $level){
			if($grade >= $level->min_percent && $grade <= $level->max_percent){
				return $level->id;
			}
		}
		return null;
	}
	
	//function returns a list of gradetypes for select objects
	public static function get_grade_type_options(){
		$grades = array();
		if($records = get_records_array('artefact_assessment_grade_type')){
			foreach($records as $data){
				$grades[$data->id] =  $data->title;
			}
		}
		return $grades;				
	}
}


class AssessmentScheme{
	public $id;
	public $title;
	public $description;
	public $institution;
	public $criteria;
	public $assessment; //this is if the object belongs to an assessment rather than if it is a stand alone tmeplate
	public function __construct($id = 0, $data = null, $assessment = 0) {
		//we have an existing object in the DB so fetch it.
        if (!empty($id)) {
            if (empty($data)) {
                if (!$data = get_record('artefact_assessment_scheme','id',$id)) {
                    throw new ArtefactNotFoundException(get_string('assessmentnotfound', 'artefact.assessment', $id));
                }
            }
            $this->id = $id;
        }
        if (empty($data)) {
            $data = array();
        }
        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

		if(!empty($assessment)){
			$this->assessment = $assessment;
		}
        // load criteria
        if ($this->id) {
            $this->criteria = AssessmentScheme::get_criteria($this->id, $this->assessment);
        }

	}
	
	public static function get_criteria($id, $assessment=0){
		$criteria = array();
		if($records = get_records_array('artefact_assessment_criteria','scheme',$id,'criteria_group, `order`, id')){
			foreach($records as $data){
				$criteria[] = new AssessmentCriteria(0,$data,$assessment);
			}
		}
		return $criteria;
	}
	
	//function returns a list of schemes for select objects
	public static function get_assessment_scheme_options($institutions){
		$schemes = array();
		if($records = get_records_select_array('artefact_assessment_scheme','','','title')){
			foreach($records as $data){
				$schemes[$data->id] =  $data->title;
			}
		}
/*institution stuff has been disabled for now needs to be done at some point
		if(is_array($institutions) && count($institutions) > 0){
			$inst_string = "(".implode(",",$institutions).")";		
			if($records = get_records_select_array('artefact_assessment_scheme','institution in',array($inst_string),'title')){
				foreach($records as $data){
					$schemes[$data->id] =  $data->title;
				}
			}
		}else{
			//no instotution, for now return them all this is dodgy and should be removed later
			if($records = get_records_array('artefact_assessment_scheme','','','title')){
				foreach($records as $data){
					$schemes[$data->id] =  $data->title;
				}
			}
		}*/
		return $schemes;				
	}

}

class AssessmentCriteria{
	public $id;
	public $title;
	public $description;
	public $scheme;
	public $criteria_group;
	public $grade_type;
	public $order;
	public $rubric;
	public $grade;
	public function __construct($id = 0, $data = null, $assessment = 0) {
		//we have an existing object in the DB so fetch it.
        if (!empty($id)) {
            if (empty($data)) {
                if (!$data = get_record('artefact_assessment_criteria','id',$id)) {
                    throw new ArtefactNotFoundException(get_string('criterianotfound', 'artefact.assessment', $id));
                }
            }
            $this->id = $id;
        }
        if (empty($data)) {
            $data = array();
        }
        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }


        // load rubrics
        if ($this->id) {
            $this->rubric = AssessmentCriteria::get_rubric($this->id);
        }
        
        if(!empty($this->grade_type)){
        	$this->grade_type = new GradeType($this->grade_type);
        	if(count($this->rubric) > 0){
        		$this->grade_type->map_rubric_to_levels($this->rubric);
        	}
        }
        
        if(!empty($this->criteria_group)){
        	if($record = get_record('artefact_assessment_criteria_group','id',$this->criteria_group)){
				$this->criteria_group = $record;
        	}
		}
        
        if(!empty($assessment)){
        	$results = null;
        	//get results from DB for this particular assessment
        	try{
        	if(!$results = get_record('artefact_assessment_results','assessment',$assessment,'criteria', $this->id)){
        		//there is no result yet
        		//this is where we should pu in default results if they are needed
        		$this->grade = null;
        	}else{
        		$this->grade = $results->grade;//this is dodge
        	}
        	}catch(SQLException $e){
        		//this is becasue we got more than one record which we shouldn't so it should fall over gracefully
        		//TODO: fall over gracefully
        	}
        }
	}
	
	public static function get_rubric($id){
		$rubric = array();
		if($records = get_records_array('artefact_assessment_rubric_level','criteria',$id,'min_percent')){
			foreach($records as $data){
				$rubric[] = $data;
			}
		}
		return $rubric;
	}
}



