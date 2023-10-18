<?php
    $mysql = require 'db.php';
    require_once 'functions.php';
    
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : "";
    $surname = isset($_POST['surname']) ? $_POST['surname'] : "";
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : "";
    $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : "";

    $success = isset($_GET['success']) ? $_GET['success'] : false;

    if (isset($_POST['submit'])){
        $data = $errors = [];

        $password_1 = $_POST['password1'];
        $password_2 = $_POST['password2'];
        
        if(strlen($password_1) > 10 || strlen($password_2) > 10){
            if($password_1 == $password_2){
                $password = password_hash($password_1, PASSWORD_DEFAULT);
            }else{
                $errors['password2'] = "The passwords do not match";
            }
        }else{
            $errors['password1'] = "The password you typed is too short";
        }

        unset($password_1);
        unset($password_2);
        unset($_POST['submit']);

        $data = sanitizeUserData($_POST);

        if(strlen($data['username']) < 40 && strlen($data['username']) > 3){
            if(preg_match("/^[a-zA-Z0-9-_.]+$/i", $data['username']) == '0'){
                $errors['username'] = "Въвели сте непозволени символи!";
            }
        } else {
            $errors['username'] = "Дължината на потребителското име е твърде кратка";
        }
        
        if(strlen($data['email']) < 10){
            $errors['email'] = 'Не сте въвели коректен имейл!';
        } else {
            $sql = "
                SELECT `id`
                FROM users 
                WHERE email = '" . $data['email'] ."'
            ";

            $query = mysqli_query($conn, $sql);

            if(mysqli_num_rows($query)){
                $errors['email'] = 'There is an existing user under that data!';
            }
        }

        if(strlen($data['birthday']) == 10){
            if(checkMyData($data['birthday']) == true){
                $data['birthday'] = strtotime($data['birthday']);
                $data['birthday'] = date('Y-m-d', $data['birthday']);
            } else {
                $errors['birthday'] = 'Невалиден формат на дата!';
            }
        } else {
            $errors['birthday'] = 'Грешно въведена дата!';
        }

        if(count($errors) == 0){
            $sql = "
                INSERT INTO `users`(
                    `username`,
                    `email`,
                    `password`,
                    `first_name`,
                    `surname`,
                    `last_name`,
                    `birthday`,
                    `active`,
                    `added`
                ) VALUES (
                    '" .mysqli_real_escape_string($conn, $data['username']). "',
                    '" .mysqli_real_escape_string($conn, $data['email']). "',
                    '" .mysqli_real_escape_string($conn, $password). "',
                    '" .mysqli_real_escape_string($conn, $data['first_name']). "',
                    '" .mysqli_real_escape_string($conn, $data['surname']). "',
                    '" .mysqli_real_escape_string($conn, $data['last_name']). "',
                    '" .mysqli_real_escape_string($conn, $data['birthday']). "',
                    '1',
                    NOW()
                )
            ";

            $query = mysqli_query($conn, $sql);

            if(!$query){
                echo mysqli_error($conn);
                $errors['mysql'] = 'Възникна грешка при добавянето на данните! Моля, свържете се с администратор!';
            } else {
                header("Location:registration.php?success=true");
                exit;
            }
        }
    }
        
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require('meta.php'); ?>        
    </head>
    <body>
        <section id="registration">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                    <h1 class="page-heading">Registration</h1>
                    </div>
                </div>
                <?php if(isset($errors) && count($errors) > 0) { ?>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <p class="text-danger">An error occured while creating the user...</p>
                        </div>
                    </div>
                <?php } ?>
                <?php if($success == true) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="alert alert-success">You successfully registered into our system!</p>
                            <p class="alert alert-info">Now you can login with the following link: <a href="login.php" id="login-button">Click here!</a></p> 
                        </div>
                    </div>
                <?php } ?>
                <form action="" class="row" method="POST" id="registration-form">
                    <div class="col-3"></div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <input type="text" class="form-control" id="username" name="username" value= "<?php echo $username; ?>" placeholder="for exp: 'johnycash'" required>
                        </div>
                            <?php if(isset($errors['username'])){ ?>
                            <span class="text-danger"><?php echo $errors['username']; ?></span>
                            <?php } ?>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            <input type="text" class="form-control" name="email" id="email"  value= "<?php echo $email; ?>" placeholder="Your email here!" required>
                            <small id="emailHelp" class="form-text text-muted">Type in your email.</small>
                        </div>
                            <?php if(isset($errors['email'])){ ?>
                            <span class="text-danger"><?php echo $errors['email']; ?></span>
                            <?php } ?>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <i class="fa fa-key" id="gridlock" aria-hidden="true"></i>
                            <input type="password" class="form-control" id="password-primary" name="password1" required aria-describedby="passwordHelp">
                            <small id="emailHelp" class="form-text text-muted">Type in your password,at least 10 characters.</small>
                        </div>
                            <?php if(isset($errors['password1'])){ ?>
                            <span class="text-danger"><?php echo $errors['password1']; ?></span>
                            <?php } ?>
                        <div class="form-group">
                            <label for="confirm-password">Confirm Password</label>
                            <i class="fa fa-unlock-alt" id="key" aria-hidden="true"></i>
                            <input type="password" class="form-control" id="password-check" name="password2" required placeholder="Same as the one above">
                            <small id="emailHelp" class="form-text text-muted">Confirm your password.</small>
                        </div>
                            <?php if(isset($errors['password2'])){ ?>
                            <span class="text-danger"><?php echo $errors['password2']; ?></span>
                            <?php } ?>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <i class="fa fa-address-card" aria-hidden="true"></i>
                            <input type="text" class="form-control" name="first_name" id="firstName" value= "<?php echo $first_name; ?>" required placeholder="Your name here">
                        </div>
                            <?php if(isset($errors['first_name'])){ ?>
                            <span class="text-danger"><?php echo $errors['first_name']; ?></span>
                            <?php } ?>
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <i class="fa fa-address-card" aria-hidden="true"></i>
                            <input type="text" class="form-control" name="surname" id="surname" value= "<?php echo $surname; ?>" placeholder="Your surfname here">
                            <small id="emailHelp" class="form-text text-muted">*Not required</small>
                        </div>
                            <?php if(isset($errors['surname'])){ ?>
                            <span class="text-danger"><?php echo $errors['surname']; ?></span>
                            <?php } ?>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <i class="fa fa-address-card" aria-hidden="true"></i>
                            <input type="text" class="form-control" name="last_name" id="lastName" value= "<?php echo $last_name; ?>" required placeholder="Your last name here">
                        </div>
                            <?php if(isset($errors['last_name'])){ ?>
                            <span class="text-danger"><?php echo $errors['last_name']; ?></span>
                            <?php } ?>
                        <div class="form-group">
                            <label for="birthday">Birthdate</label>
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            <input type="text" class="form-control" name="birthday" id="birthday" value= "<?php echo $birthday; ?>" required placeholder="MM-DD-YYYY" aria-describedby="birthdayHelp">
                            <small id="emailHelp" class="form-text text-muted">Type your birthdate</small>
                        </div>
                            <?php if(isset($errors['birthday'])){ ?>
                            <span class="text-danger"><?php echo $errors['birthday']; ?></span>
                            <?php } ?>
                    </div>
                    <div class="col-3"></div>
                    <div class="col-12">
                        <div class="form-group">
                            <div class="text-center" style="margin-bottom: 15px;">
                                <button type="submit" class="btn" name="submit" style="background-color: #886F6F;">Submit</button>
                                </div>
                                <div class="row pt-2">
                                    <div class="col-md-12">
                                        <div class="alert-warning" style="text-align: center;">
                                            <p>All the fields without "Surname" are required in order to continue!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-8 offset-2" style="text-align: center;">
                        <div class="alert alert-success">
                            <p>Already have an account? <a href="login.php" id="er-login">login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>