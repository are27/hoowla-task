<?php
//CHANGE THIS TO REFLECT THE DOMAIN THE SCRIPT IS RUNNING ON
$script_root='http://asemachine.ac.uk/hoowla/';
$action=$id=$mode=null;

$footer=getTemplate();
require getcwd().'/lib/mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();
$m = new Mustache_Engine(array( 'entity_flags' => ENT_QUOTES,
                                'loader' => new Mustache_Loader_FilesystemLoader(getcwd().'/views'),
                                'partials_loader' => new Mustache_Loader_FilesystemLoader(getcwd().'/views/partials')
                              )
                        );

$approved_actions=array('add','view','edit', 'submit');

if(array_key_exists('id', $_REQUEST))
{   $id=$_REQUEST['id'];
}    
if(array_key_exists('mode', $_REQUEST))
{   $mode=$_REQUEST['mode'];
}    
if(!array_key_exists('action', $_REQUEST))
{   $data=json_decode(urldecode(file_get_contents($script_root.'api/?action=listDrivers')), true);
    $tpl = $m->loadTemplate('listing');
    echo $tpl->render(array("drivers"=>$data));
}
else
{   $action=$_REQUEST['action'];
    if(in_array($action, $approved_actions))
    {   switch($action){
        case 'add':
            addDriver();
            break;
        case 'view':
            viewDriver($id);
            break;
        case 'edit':
            editDriver($id);
            break;
        case 'submit':
            process($mode);
            break;
        }
    }
}
echo $footer;

function addDriver()
{   global $m;
    $tpl = $m->loadTemplate('form');
    echo $tpl->render(array("info"=>array("is_add"=>"true", "title"=>"Add a Driver", "mode"=>"&mode=add")));
}

function editDriver($id)
{   global $m, $script_root;
    $data=urldecode(file_get_contents($script_root.'api/?action=getDriver&id='.$id));
    $tpl = $m->loadTemplate('form');
    echo $tpl->render(array("info"=>array("is_edit"=>"true", "title"=>"Edit a Driver",  "data"=>$data, "mode"=>"&mode=edit")));
}

function viewDriver($id)
{   global $m, $script_root;
    $data=urldecode(file_get_contents($script_root.'api/?action=getDriver&id='.$id));
    $tpl = $m->loadTemplate('form');
    echo $tpl->render(array("info"=>array("is_view"=>"true", "title"=>"View a Driver", "data"=>$data )));
}

function process($mode)
{   global $m, $script_root;
    $data=$_POST;
    unset($data['submit']);
    if($mode=='add') 
    {  run_curl('addDriver', $data);
    }
    if($mode=='edit') 
    {  run_curl('updateDriver', $data);
    }
    $tpl = $m->loadTemplate('success');
    echo $tpl->render(array());
}


function run_curl($endpoint, $data)
{   global $script_root; 
    
    $url=$script_root.'api/?action='.$endpoint;
    $post = curl_init();

    curl_setopt($post, CURLOPT_URL, $url);
    curl_setopt($post, CURLOPT_POST, count($data));
    curl_setopt($post, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($post);
    curl_close($post);
}

function getTemplate()
{   $html_parts=explode("<!--split-->", file_get_contents('template/template.html'));
    echo $html_parts[0];
    return $html_parts[1];
}
?>
