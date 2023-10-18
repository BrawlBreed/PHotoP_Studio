<?php

$mysql = require 'db.php';

$username = isset($_POST['username']) ? $_POST['username']:'';

if(isset($_POST['submit'])){
    $user_data = $errors = array();
    
    $user_username = $_POST['username'];
    $user_password = $_POST['password1'];
    $success = false;

    $sql = "
    SELECT 
    `id`
    ,`username`
    ,`password`
    ,`first_name`
    ,`last_name`
    ,`email`
    FROM users
    WHERE
    `username` = '" . $user_username . "'
    ";

    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query)){
        $user_data = mysqli_fetch_assoc($query);
    }else{
        $errors['username'] = "You typed in an unexistent account!";
    }

    if(count($errors) == 0){
        if(password_verify($user_password, $user_data['password'])){
            $success = true ;
            
            session_start();

            $_SESSION['user'] = [
                'id' => $user_data['id'], 
                'username' => $user_data['username'],               
            ];
        }else{
            $errors['password1'] = 'The password you typed is incorrect!';
        } 
    }
    if($success == true){
        header('Location:profile.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('meta.php'); ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <h1 class="page-heading text-center">Login</h1>
            </div>
        </div>
        <?php if(isset($errors) && count($errors) > 0) { ?>
            <div class="row pb-2">
                <div class="col-md-12">
                    <p class="text-danger">An error occured while logging in...</p>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-6 offset-3">
                <form method="POST" action="" id="login-form" class="col-md-8 offset-md-2">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="username" placeholder="Your user here" required>
                    </div>
                    <?php if(isset($errors['username'])){ ?>
                        <span class="text-danger"><?php echo $errors['username']; ?></span>
                        <?php } ?>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <i class="fa fa-key" aria-hidden="true"></i>
                        <input type="password" class="form-control" name="password1" required aria-describedby="passwordHelp">
                        <small class="form-text text-muted" style="color: rgba(255, 228, 196, 0.7) !important;">Your password here!</small>
                    </div>
                        <?php if(isset($errors['password1'])){ ?>
                        <span class="text-danger"><?php echo $errors['password1']; ?></span>
                        <?php } ?>
                    <div class="text-center">
                    <button type="submit" class="btn" name="submit" style="background-color: #886F6F;">Sign in</button>
                    </div>
                </form> 
            </div>
        </div> 
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="login-register">
                    <p class="text-center">
                        You don't have an account yet?<a href="registration.php" id="login-button1">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>   
</body>
</html>