<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-blog
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'content/blogs');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'blog');
define('SECTION_PAGE', 'post');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
require_once('license.php');

safe_require('artefact', 'blog');
safe_require('artefact', 'file');
if (!PluginArtefactBlog::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('blog','artefact.blog')));
}
/*
 * For a new post, the 'blog' parameter will be set to the blog's
 * artefact id.  For an existing post, the 'blogpost' parameter will
 * be set to the blogpost's artefact id.
 */
$posttype = 0; //SB new type for posts 
$blogpost = param_integer('blogpost', param_integer('id', 0));
$focuselement = 'title';
if (!$blogpost) {
/*
 *  For a new post, a tag can be set from tagged blogpost block
 */
    $tagselect = param_variable('tagselect', '');
    $blog = param_integer('blog');
	$posttype = param_integer('type',0);/*SB this is for quick posting types are 1 - photo; 2 - text; 3 - link; 0 - html, full editor*/
	if(!$blog){
		$pagetitle = get_string('newblankblogpost', 'artefact.blog');
		$focuselement = 'blog';
	}
	else{
	    $blogobj = new ArtefactTypeBlog($blog);
	    $blogobj->check_permission();
	    $pagetitle = get_string('newblogpost', 'artefact.blog', get_field('artefact', 'title', 'id', $blog));
		if($posttype == 0){
			$focuselement = 'title';
		}elseif($posttype == 3){
			$focuselement = 'url';
		}
	}
    $title = '';
    $description = '';
    $tags = array($tagselect);
    $checked = 0;
    $sensitive = 0;
    $attachments = array();
    define('TITLE', $pagetitle);
}
else {
    $blogpostobj = new ArtefactTypeBlogPost($blogpost);
    $blogpostobj->check_permission();
    if ($blogpostobj->get('locked')) {
        throw new AccessDeniedException(get_string('submittedforassessment', 'view'));
    }
    $blog = $blogpostobj->get('parent');
    $title = $blogpostobj->get('title');
    $description = $blogpostobj->get('description');
    $tags = $blogpostobj->get('tags');
    $checked = !$blogpostobj->get('published');
    $sensitive = $blogpostobj->get('sensitive');
    $pagetitle = get_string('editblogpost', 'artefact.blog');
    $focuselement = 'description'; // Doesn't seem to work with tinyMCE.
    $attachments = $blogpostobj->attachment_id_list();
    define('TITLE', get_string('editblogpost','artefact.blog'));
}

$folder = param_integer('folder', 0);
$browse = (int) param_variable('browse', 0);
$highlight = null;
if ($file = param_integer('file', 0)) {
    $highlight = array($file);
}

$tempform = array(
			'name'               => 'editpost',
			'method'             => 'post',
			'autofocus'          => $focuselement,
			'jsform'             => true,
			'newiframeonsubmit'  => true,
			'jssuccesscallback'  => 'editpost_callback',
			'jserrorcallback'    => 'editpost_callback',
			'plugintype'         => 'artefact',
			'pluginname'         => 'blog',
			'configdirs'         => array(get_config('libroot') . 'form/', get_config('docroot') . 'artefact/file/form/'),
);

$elements = array();
if($blog){
	$elements['blog'] = array(
		'type' => 'hidden',
		'value' => $blog,
	);
	$elements['blogpost'] = array(
		'type' => 'hidden',
		'value' => $blogpost,
	);

}else{
	$bl = ArtefactTypeBlog::get_blog_list(0,0);
	$bls = $bl[1];
	$blogs = array();
	$blogs[] = NULL; //add an empty option
	foreach($bls as $rs){
		if(!$rs->locked){
			$blogs[$rs->id] = $rs->title;
		}
	}
	$elements['blog'] = array(
		'title' => get_string('blog', 'artefact.blog'),
		'type' => 'select',
        'options' => $blogs,
		'defaultvalue' => NULL,
		'rules' => array(
			'required' => true
		),
	);
}
$elements['blogtype'] = array(
	'type' => 'hidden',
	'value' => $posttype,
);



if($posttype == 0){
	$elements['title'] = array(
		'type' => 'text',
		'title' => get_string('posttitle', 'artefact.blog'),
		'rules' => array(
			'required' => true
		),
		'defaultvalue' => $title,
	);
	
	$elements['description'] = array(
		'type' => 'tinywysiwyg',
		'rows' => 20,
		'cols' => 60,
		'title' => get_string('postbody', 'artefact.blog'),
		'description' => get_string('postbodydesc', 'artefact.blog'),
		'rules' => array(
			'maxlength' => 65536,
			'required' => true
		),
		'defaultvalue' => $description,
	);
	$elements['tags']  = array(
		'defaultvalue' => $tags,
		'type'         => 'tags',
		'title'        => get_string('tags'),
/*		'description'  => get_string('tagsdesc'),*/
		'help' => false,
	);
	$elements['license'] = license_form_el_basic(isset($blogpostobj) ? $blogpostobj : null);
	
	$elements['licensing_advanced'] = license_form_el_advanced(isset($blogpostobj) ? $blogpostobj : null);
	$elements['filebrowser'] = array(
		'type'         => 'filebrowser',
		'title'        => get_string('attachments', 'artefact.blog'),
		'folder'       => $folder,
		'highlight'    => $highlight,
		'browse'       => $browse,
		'page'         => get_config('wwwroot') . 'artefact/blog/post.php?' . ($blogpost ? ('id=' . $blogpost) : ('blog=' . $blog)) . '&browse=1',
		'browsehelp'   => 'browsemyfiles',
		'config'       => array(
			'upload'          => true,
			'uploadagreement' => get_config_plugin('artefact', 'file', 'uploadagreement'),
			'resizeonuploaduseroption' => get_config_plugin('artefact', 'file', 'resizeonuploaduseroption'),
			'resizeonuploaduserdefault' => $USER->get_account_preference('resizeonuploaduserdefault'),
			'createfolder'    => false,
			'edit'            => false,
			'select'          => true,
		),
		'defaultvalue'       => $attachments,
		'selectlistcallback' => 'artefact_get_records_by_id',
		'selectcallback'     => 'add_attachment',
		'unselectcallback'   => 'delete_attachment',
	);

}elseif($posttype ==1){
	$elements['filebrowser'] = array(
		'type'         => 'filebrowser',
		'title'        => get_string('attachmentsimg', 'artefact.blog'),
		'folder'       => $folder,
		'highlight'    => $highlight,
		'browse'       => $browse,
		'filters'		=> array('filetype' => array('image/jpeg','image/png','image/gif')),
		'page'         => get_config('wwwroot') . 'artefact/blog/post.php?' . ($blogpost ? ('id=' . $blogpost) : ('blog=' . $blog)) . '&browse=1',
		'browsehelp'   => 'browsemyfiles',
		'config'       => array(
			'upload'          => true,
			'uploadagreement' => get_config_plugin('artefact', 'file', 'uploadagreement'),
			'resizeonuploaduseroption' => get_config_plugin('artefact', 'file', 'resizeonuploaduseroption'),
			'resizeonuploaduserdefault' => $USER->get_account_preference('resizeonuploaduserdefault'),
			'createfolder'    => true,
			'edit'            => false,
			'select'          => true,
			'alwaysopen'		=> false,
		),
		'rules' => array(
			'required' => true,
		),
		'defaultvalue'       => $attachments,
		'selectlistcallback' => 'artefact_get_records_by_id',
		'selectcallback'     => 'add_attachment',
		'unselectcallback'   => 'delete_attachment',
	);
	$elements['title'] = array(
		'type' => 'text',
		'title' => get_string('posttitle', 'artefact.blog'),
		'rules' => array(
			'required' => false
		),
		'defaultvalue' => $title,
	);
	$elements['description'] = array(
		'type' => 'wysiwyg',
		'rows' => 20,
		'cols' => 60,
		'title' => get_string('postbodyimg', 'artefact.blog'),
		'description' => get_string('postbodydesc', 'artefact.blog'),
		'rules' => array(
			'maxlength' => 65536,
			'required' => false
		),
		'defaultvalue' => $description,
	);
	$elements['tags']  = array(
		'defaultvalue' => $tags,
		'type'         => 'tags',
		'title'        => get_string('tags'),
/*		'description'  => get_string('tagsdesc'),*/
		'help' => false,
	);

}elseif($posttype ==2){
	$elements['title'] = array(
		'type' => 'text',
		'title' => get_string('posttitle', 'artefact.blog'),
		'rules' => array(
			'required' => true
		),
		'defaultvalue' => $title,
	);
	$elements['description'] = array(
		'type' => 'wysiwyg',
		'rows' => 20,
		'cols' => 60,
		'title' => get_string('postbody', 'artefact.blog'),
		'description' => get_string('postbodydesc', 'artefact.blog'),
		'rules' => array(
			'maxlength' => 65536,
			'required' => true
		),
		'defaultvalue' => $description,
	);
	$elements['tags']  = array(
		'defaultvalue' => $tags,
		'type'         => 'tags',
		'title'        => get_string('tags'),
/*		'description'  => get_string('tagsdesc'),*/
		'help' => false,
	);
	$elements['license'] = license_form_el_basic(isset($blogpostobj) ? $blogpostobj : null);
	
	$elements['licensing_advanced'] = license_form_el_advanced(isset($blogpostobj) ? $blogpostobj : null);
	$elements['filebrowser'] = array(
		'type'         => 'filebrowser',
		'title'        => get_string('attachments', 'artefact.blog'),
		'folder'       => $folder,
		'highlight'    => $highlight,
		'browse'       => $browse,
		'page'         => get_config('wwwroot') . 'artefact/blog/post.php?' . ($blogpost ? ('id=' . $blogpost) : ('blog=' . $blog)) . '&browse=1',
		'browsehelp'   => 'browsemyfiles',
		'config'       => array(
			'upload'          => true,
			'uploadagreement' => get_config_plugin('artefact', 'file', 'uploadagreement'),
			'resizeonuploaduseroption' => get_config_plugin('artefact', 'file', 'resizeonuploaduseroption'),
			'resizeonuploaduserdefault' => $USER->get_account_preference('resizeonuploaduserdefault'),
			'createfolder'    => false,
			'edit'            => false,
			'select'          => true,
		),
		'defaultvalue'       => $attachments,
		'selectlistcallback' => 'artefact_get_records_by_id',
		'selectcallback'     => 'add_attachment',
		'unselectcallback'   => 'delete_attachment',
	);
}elseif($posttype ==3){
	$tempform['jssuccesscallback'] = 'editpost_urlcallback';
	$tempform['jserrorcallback'] = 'editpost_urlcallback';
	
	$elements['title'] = array(
		'type' => 'text',
		'title' => get_string('posttitle', 'artefact.blog'),
		'rules' => array(
			'required' => false
		),
		'defaultvalue' => $title,
	);
	$elements['url'] = array(
		'type' => 'text',
		'title' => get_string('posturl', 'artefact.blog'),
		'rules' => array(
			'required' => true
		),
		'defaultvalue' => $description,
	);
	$elements['tags']  = array(
		'defaultvalue' => $tags,
		'type'         => 'tags',
		'title'        => get_string('tags'),
/*		'description'  => get_string('tagsdesc'),*/
		'help' => false,
	);

}


$elements['draft'] = array(
	'type' => 'checkbox',
	'title' => get_string('draft', 'artefact.blog'),
/*	'description' => get_string('thisisdraftdesc', 'artefact.blog'),*/
	'defaultvalue' => $checked,
	'help' => false,
);
$elements['sensitive'] = array(
	'type' => 'checkbox',
	'title' => get_string('sensitive', 'artefact.blog'),
	'description' => get_string('sensitivedescription', 'artefact.blog'),
	'defaultvalue' => $checked,
	'help' => false,
);

$elements['allowcomments'] = array(
	'type'         => 'checkbox',
	'title'        => get_string('allowcomments','artefact.comment'),
/*	'description'  => get_string('allowcommentsonpost','artefact.blog'),*/
	'defaultvalue' => $blogpost ? $blogpostobj->get('allowcomments') : 1,
);
$elements['submitpost'] = array(
	'type' => 'submitcancel',
	'value' => array(get_string('savepost', 'artefact.blog'), get_string('cancel')),
	'goto' => get_config('wwwroot') . 'artefact/blog/view/index.php?id=' . $blog,
);

$tempform['elements'] = $elements;
$form = pieform($tempform);

/*
 * Javascript specific to this page.  Creates the list of files
 * attached to the blog post.
 */
$wwwroot = get_config('wwwroot');
$noimagesmessage = json_encode(get_string('noimageshavebeenattachedtothispost', 'artefact.blog'));

$javascript = <<<EOF



// Override the image button on the tinyMCE editor.  Rather than the
// normal image popup, open up a modified popup which allows the user
// to select an image from the list of image files attached to the
// post.

// Get all the files in the attached files list that have been
// recognised as images.  This function is called by the the popup
// window, but needs access to the attachment list on this page
function attachedImageList() {
    var images = [];
    var attachments = editpost_filebrowser.selecteddata;
    for (var a in attachments) {
        if (attachments[a].artefacttype == 'image' || attachments[a].artefacttype == 'profileicon') {
            images.push({
                'id': attachments[a].id,
                'name': attachments[a].title,
                'description': attachments[a].description ? attachments[a].description : ''
            });
        }
    }
    return images;
}


function imageSrcFromId(imageid) {
    return config.wwwroot + 'artefact/file/download.php?file=' + imageid;
}

function imageIdFromSrc(src) {
    var artefactstring = 'download.php?file=';
    var ind = src.indexOf(artefactstring);
    if (ind != -1) {
        return src.substring(ind+artefactstring.length, src.length);
    }
    return '';
}

var imageList = {};

function blogpostImageWindow(ui, v) {
    var t = tinyMCE.activeEditor;

    imageList = attachedImageList();

    var template = new Array();

    template['file'] = '{$wwwroot}artefact/blog/image_popup.php';
    template['width'] = 355;
    template['height'] = 275 + (tinyMCE.isMSIE ? 25 : 0);

    // Language specific width and height addons
    template['width'] += t.getLang('lang_insert_image_delta_width', 0);
    template['height'] += t.getLang('lang_insert_image_delta_height', 0);
    template['inline'] = true;

    t.windowManager.open(template);
}
function editpost_callback(form, data) {
    editpost_filebrowser.callback(form, data);
};
function editpost_urlcallback(form, data){
	if(data.goto != null){
		location.href = data.goto;
	}
}

EOF;

$smarty = smarty(array(), array(), array(), array(
    'tinymceconfig' => '
        plugins: "tooltoggle,textcolor,hr,link,maharaimage,table,emoticons,spellchecker,paste,code,fullscreen",
        image_filebrowser: "editpost_filebrowser",
    ',
    'sideblocks' => array(
        array(
            'name'   => 'quota',
            'weight' => -10,
            'data'   => array(),
        ),
    ),
));
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign_by_ref('form', $form);
$smarty->assign('PAGEHEADING', $pagetitle);
$smarty->display('artefact:blog:editpost.tpl');


// Get all the files in the attached files list that have been
// recognised as images.  This function is called by the the popup
// window, but needs access to the attachment list on this page
function attachedImageList($files) {
    $images = array();
    foreach ($files as $a) {
        if ($a->artefacttype == 'image' || $a->artefacttype == 'profileicon') {
            $images = array(
                'id'=> $a->id,
            );
        }
    }
    return images;
}


function imageSrcFromId($imageid) {
    return get_config('wwwroot') . 'artefact/file/download.php?file=' .$imageid;
}


function buildimgshtml($imgs){
	if(count($imgs)){
	//	$imgs = attachedImageList($files);
		$imghtml = '<div id="post_imgs">';
		foreach($imgs as $img){
			$imgsrc = imageSrcFromId($img);
			$imghtml = $imghtml.'<p><a href="'.$imgsrc.'"> <img src="'.$imgsrc.'" /></a></p>';
		}
		$imghtml = $imghtml.'</div>';
		return $imghtml;
	}else{
		return '';
	}
}


function extractimages($files, $desc){

	
	$dom = new DOMDocument();

	$imgs = array();
	$dom->loadHTML($desc);

	foreach($dom->getElementsByTagName('img') as $node)
	{
		$src = $node->attributes->getNamedItem("src")->nodeValue;
		$imgs[] = intval(substr($src, strpos($src,'file=')+5));
		
	}
	$returnfiles = array_diff($files, $imgs);
	
	return $returnfiles;		
}

/**
 * This function get called to cancel the form submission. It returns to the
 * blog list.
 */
function editpost_cancel_submit() {
    global $blog;
    redirect(get_config('wwwroot') . 'artefact/blog/view/index.php?id=' . $blog);
}

function editpost_submit(Pieform $form, $values) {
    global $USER, $SESSION, $blogpost, $blog;
    db_begin();
    $postobj = new ArtefactTypeBlogPost($blogpost, null);
    $blogobj = new ArtefactTypeBlog($blog);
    $postobj->set('title', $values['title']);
    $postobj->set('description', $values['description']);
	if($values['blogtype'] == 0 || $values['blogtype'] == 2){
		$files = is_array($values['filebrowser']) ? $values['filebrowser'] : array();
		$images = extractimages($files,$values['description'] );
		$desc = buildimgshtml($images);
		$desc = $values['description'] . $desc;
	    $postobj->set('description', $desc);
	}
	elseif($values['blogtype'] == 1){
		$files = is_array($values['filebrowser']) ? $values['filebrowser'] : array();
		$desc = buildimgshtml($files);
		$desc = $desc . $values['description'];
	    $postobj->set('description', $desc);
	}
	elseif($values['blogtype'] == 3){
    	$postobj->set('description', $values['url']);
	}
    $postobj->set('tags', $values['tags']);
    if (get_config('licensemetadata')) {
        $postobj->set('license', $values['license']);
        $postobj->set('licensor', $values['licensor']);
        $postobj->set('licensorurl', $values['licensorurl']);
    }
    $postobj->set('published', !$values['draft']);
    $postobj->set('sensitive', $values['sensitive']);
/*	if(!$values['draft']){
		$postobj->set('ctime',time());
	}*/
    $postobj->set('allowcomments', (int) $values['allowcomments']);
    if (!$blogpost) {
        $postobj->set('parent', $blog);
	    if($blogobj->get('group')){
	    	$postobj->set('group',$blogobj->get('group'));
	    }else{
	        $postobj->set('owner', $USER->id);
	    }
    }
    $postobj->commit();
    $blogpost = $postobj->get('id');

    // Attachments
    $old = $postobj->attachment_id_list();
    // $new = is_array($values['filebrowser']['selected']) ? $values['filebrowser']['selected'] : array();
    $new = is_array($values['filebrowser']) ? $values['filebrowser'] : array();
    // only allow the attaching of files that exist and are editable by user
    foreach ($new as $key => $fileid) {
        $file = artefact_instance_from_id($fileid);
        if (!($file instanceof ArtefactTypeFile) || !$USER->can_publish_artefact($file)) {
            unset($new[$key]);
        }
    }
    if (!empty($new) || !empty($old)) {
        foreach ($old as $o) {
            if (!in_array($o, $new)) {
                try {
                    $postobj->detach($o);
                }
                catch (ArtefactNotFoundException $e) {}
            }
        }
        foreach ($new as $n) {
            if (!in_array($n, $old)) {
                try {
                    $postobj->attach($n);
                }
                catch (ArtefactNotFoundException $e) {}
            }
        }
    }
    db_commit();

    $result = array(
        'error'   => false,
        'message' => get_string('blogpostsaved', 'artefact.blog'),
        'goto'    => get_config('wwwroot') . 'artefact/blog/view/index.php?id=' . $blog,
    );
    if ($form->submitted_by_js()) {
        // Redirect back to the blog page from within the iframe
        $SESSION->add_ok_msg($result['message']);
        $form->json_reply(PIEFORM_OK, $result, false);
    }
    $form->reply(PIEFORM_OK, $result);
}

function add_attachment($attachmentid) {
    global $blogpostobj;
    if ($blogpostobj) {
        $blogpostobj->attach($attachmentid);
    }
}

function delete_attachment($attachmentid) {
    global $blogpostobj;
    if ($blogpostobj) {
        $blogpostobj->detach($attachmentid);
    }
}