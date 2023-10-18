<?php
    $sql = require('db.php');
    include('functions.php');

    session_start();

    $data = [];

    $sql = "
        SELECT 
            `email`
            ,`first_name`
            ,`surname`
            ,`last_name`
            ,`birthday`
            ,`added`
        FROM `users`
        WHERE id = '" . $_SESSION['user']['id'] . "'
    ";

    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query)){
        $data = mysqli_fetch_assoc($query);
    }

    $data['added'] = date('H:i:s d-m-Y', strtotime($data['added']));

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include('meta.php'); ?>
    </head>
    <body>
        <?php include('menu.php'); ?>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th scope="row">Потребителско име</th>
                                    <td><?php echo $_SESSION['user']['username']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Електронна поща</th>
                                    <td><?php echo $data['email']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Собствено име</th>
                                    <td><?php echo $data['first_name']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Презиме</th>
                                    <td><?php echo $data['surname']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Фамилия</th>
                                    <td><?php echo $data['last_name']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Дата на раждане</th>
                                    <td><?php echo $data['birthday']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Дата на регистрация</th>
                                    <td><?php echo $data['added']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>