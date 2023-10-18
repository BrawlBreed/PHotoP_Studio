<?php
    $sql = require('db.php');
    include('functions.php');
    
    session_start();
    $user = [];

    $sql = "
    SELECT
        `email` 
        ,`first_name`
        ,`last_name`
        ,`birthday`
        ,`added` 
        FROM `users`
    WHERE `id` = '" . $_SESSION['user']['id'] . "' 
    ";

    $query = mysqli_query($conn, $sql);

    if(mysqli_num_rows($query)){
        $user = mysqli_fetch_assoc($query);
    }
    
    $user['added'] = date('H:i:s d-m-Y', strtotime($user['added']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('meta.php'); ?>
</head>
<body>
    <?php include('menu.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-1">
                <div class="table-responsive">
                    <table style="border: 10px solid #C1A3A3;">
                        <tbody>
                            <tr>
                                <th scope="row">Username</th>
                                    <td >
                                        <?php echo $_SESSION['user']['username'];?>
                                    </td>                             
                                <th scope="row">Email</th>
                                    <td >
                                        <?php echo $user['email'];?>
                                    </td>
                                <th scope="row">First Name</th>                             
                                    <td >
                                        <?php echo $user['first_name'];?>
                                    </td>
                                <th scope="row">Last Name</th>                             
                                    <td >
                                        <?php echo $user['last_name'];?>
                                    </td>
                                <th scope="row">Birthday</th>                             
                                    <td >
                                        <?php echo $user['birthday'];?>
                                    </td>                             
                                <th scope="row">Added</th>                             
                                    <td >
                                        <?php echo $user['added'];?>
                                    </td>                             
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>