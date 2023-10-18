<?php 

//Function to print info
function P($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
/*
//1.Remove unnecesary symbols
//2.@param array $user_data user data to inputs
//3.return sanitized array
*/
function sanitizeUserData($user_data){
    if(is_array($user_data)){
        $array_data = [];
        foreach($user_data as $key=>$data){
            $data = trim($data);
            $data = stripcslashes($data);
            $data = htmlspecialchars($data);
            $array_data[$key] = $data;
        }
        return $array_data;
    } else {
        return false;
    }
}

function checkMyData($data){
    $user_data = explode('-', $data);
    return checkdate($user_data[1], $user_data[0], $user_data[2]);
}
?>