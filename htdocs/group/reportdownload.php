<?PHP


define('INTERNAL', 1);
require(dirname(dirname(__FILE__)) . '/init.php');
require_once('view.php');
require_once('group.php');
require_once('user.php');
safe_require('artefact', 'comment');
safe_require('artefact', 'assessment');
safe_require('interaction', 'schedule');
define('TITLE', 'Statements');
define('MENUITEM', 'groups/report');
define('GROUP', param_integer('group'));

$wwwroot = get_config('wwwroot');
$needsubdomain = get_config('cleanurlusersubdomains');

$group = group_current_group();
$role = group_user_access($group->id);
if (!group_role_can_access_report($group, $role)) {
    throw new AccessDeniedException();
}


$sql = "
	SELECT DISTINCT u.firstname, u.lastname, 
			TRIM(BOTH '\"' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(bi.configdata,'\"text\";',-1),';',1),':',-1)) AS statement
	FROM {view} v
		JOIN {view_access} va ON v.id = va.view
		JOIN {usr} u ON v.owner = u.id
		JOIN {block_instance} bi ON bi.view = v.id
		WHERE va.group = ?
		AND v.group IS NULL
		AND v.type = 'portfolio'
		AND bi.title = 'Exhibition Statement'
							";
	if (!$statementdata = get_records_sql_array($sql, array($group->id))) {
		$statementdata = array();
	}
	
$smarty = smarty(array(),array(),array(),array());
	$smarty->assign('baseurl', get_config('wwwroot') . 'group/report.php?group=' . $group->id);
	$smarty->assign('heading', $group->name);
	$smarty->assign('statementdata', $statementdata);
	$smarty->display('group/statement.tpl');
	

  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.
/*
  function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // filename for download
  $filename = "website_data_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  $result = pg_query("SELECT * FROM table ORDER BY field") or die('Query failed!');
  while(false !== ($row = pg_fetch_assoc($result))) {
    if(!$flag) {
      // display field/column names as first row
      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;
  */
?>