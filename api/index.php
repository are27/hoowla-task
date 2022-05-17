<?php
header('Content-Type: application/json; charset=utf-8');
$action=$id=$data=$has_data=null;
$db = new PDO("sqlite:data/drivers.sqlite3");

$approved_actions=array('listDrivers','getDriver','updateDriver', 'addDriver');
if(array_key_exists('action', $_REQUEST))
{   $action=$_REQUEST['action'];
}
if(array_key_exists('id', $_REQUEST))
{   $id=$_REQUEST['id'];
}
try
{   $fp = fopen('php://input', 'r');
    $rawData = stream_get_contents($fp);
    $data=json_decode($rawData, true);
    if(!empty($data))
    {   $has_data=1;    
    }    
} 
catch (Exception $ex) {
     echo '{"error": "Driver data must be specified"}';
}

if(in_array($action, $approved_actions))
{   switch($action){
        case 'listDrivers':
            listDrivers();
            break;
        case 'getDriver':
            if(array_key_exists('id', $_REQUEST))
            {   getDriver($id);
            }
            else
            {   echo '{"error": "Driver ID must be specified"}';                
            }
            break;
        case 'updateDriver':
            if($has_data==1)
            {   updateDriver($data);
            }
            else
            {   echo '{"error": "Driver data must be specified"}';                
            }
            break;
        case 'addDriver':
            if($has_data==1)
            {   addDriver($data);
            }
            else
            {   echo '{"error": " "Driver data must be specified"}';                
            }
            break;
    }
}
else
{   echo json_encode(array("available_actions"=>$approved_actions));
}


function listDrivers()
{   global $db;
    $stmt = $db->prepare('select * from driver;');
    $stmt->execute();
    print_r(json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)));
}

function getDriver($id)
{   global $db;
    $stmt = $db->prepare('select * from driver where id=:id;');
    $stmt->execute();
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    print_r(json_encode($stmt->fetch(PDO::FETCH_ASSOC)));
}

function addDriver($data)
{   global $db;
    $name=$age=$team=null;
    if(array_key_exists('name', $data))
    {   $name=$data['name'];
    }
    if(array_key_exists('age', $data))
    {   $age=(int)$data['age'];
    }
    if(array_key_exists('team', $data))
    {   $team=$data['team'];
    }
    
    $sql = $db->prepare("INSERT INTO driver (name, age, team) VALUES (:name, :age, :team)");
    $sql->bindParam(':name', $name, PDO::PARAM_STR);
    $sql->bindParam(':age', $age, PDO::PARAM_INT);
    $sql->bindParam(':team', $team, PDO::PARAM_STR);
    if ($sql->execute())
    {   $last_id = $db->lastInsertId();
        echo '{"success":"Driver data added","ID":"'.$last_id.'"}';    
    }
}

function updateDriver($data)
{   global $db;
    $id=$name=$age=$team=null;
    if(array_key_exists('id', $data))
    {   $id=(int)$data['id'];
    }
    if(array_key_exists('name', $data))
    {   $name=$data['name'];
    }
    if(array_key_exists('age', $data))
    {   $age=(int)$data['age'];
    }
    if(array_key_exists('team', $data))
    {   $team=$data['team'];
    }
    
    $stmt = $db->prepare('SELECT * FROM driver WHERE id=:id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if( ! $row)
    {   echo '{"error": " "Driver not found"}'; 
    }
    else
    {   $sql = $db->prepare("UPDATE driver SET name=:name, age=:age, team=:team WHERE id=:id");
        $sql->bindParam(':id', $id, PDO::PARAM_INT);
        $sql->bindParam(':name', $name, PDO::PARAM_STR);
        $sql->bindParam(':age', $age, PDO::PARAM_INT);
        $sql->bindParam(':team', $team, PDO::PARAM_STR);
        if ($sql->execute())
        {    echo '{"success":"Driver data updated","ID":"'.$id.'"}';    
        }
    }
}

?> 
