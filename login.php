<?php

$mysql = require 'db.php';

$username = isset($_POST['username']) ? $_POST['username'] : "";

if(isset($_POST['submit'])){
    $user_data = $errors = [];
    $success = false;

    $user_username = $_POST['username'];
    $user_password = $_POST['password'];

    $sql = "
        SELECT 
            `id`
            ,`username`
            ,`first_name`
            ,`last_name`
            ,password
            ,`email`
        FROM users
        WHERE `username` = '" . $user_username . "'
    ";

    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query)){
        $user_data = mysqli_fetch_assoc($query);
    } else {
        $errors['username'] = "Не съществува такъв потребител!";
    }

    if(count($errors) == 0){
        if(password_verify($user_password, $user_data['password'])){
            $success = true;

            session_start();

            $_SESSION['user'] =[
                'id' => $user_data['id'],
                'username' => $user_data['username']
            ];
        } else {
            $errors['password'] = 'Грешна парола!';
        }

        if($success == true){
            header('Location: profile.php');
            exit;
        }

    }

}


?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include('meta.php'); ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-heading text-center">Вход</h1>
                </div>
            </div>
            <?php if(isset($errors) && count($errors) > 0){ ?>
                <div class="row pb-2">
                    <div class="col-md-12">
                        <p class="text-danger text-center">Възникнаха грешки при опит за вход!</p>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <form action="" method="POST" id="login_form" class="col-md-8  offset-md-2">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="username">Потребителско име*</label>
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" placeholder="Въведете потребителско име" required >
                            <?php if(isset($errors['username'])){?>
                                <span class="text-danger"><?php echo $errors['username']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="form-group col-12">
                            <label for="password">Парола*</label>
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Въведете парола" required>
                            <?php if(isset($errors['password'])){?>
                                <span class="text-danger"><?php echo $errors['password']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary" name="submit">Вход</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <p class="text-center">Все още нямате акаунт? <a href="registration.php" class="login_link">Регистрация</a></p>
                    </div>
                </div>
            </div>
        </div> 
    </body>
</html>