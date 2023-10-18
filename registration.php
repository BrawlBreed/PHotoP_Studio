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
        
        if($_POST['password1'] > 10 || $_POST['password2'] > 10){
            if($_POST['password1'] == $_POST['password2']){
                $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);
            } else {
                $errors['password'] = 'Паролите не съвпадат!';
            }
        } else {
            $errors['password'] = 'Дължината на паролата е твърде кратка!';
        }

        unset($_POST['password1']);
        unset($_POST['password2']);
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
                $errors['email'] = 'Съществува такъв потребител!';
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
<html>
    <head>
        <?php include('meta.php'); ?>
    </head>
    <body>
        <section id="registration">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-heading text-center">Регистрация</h1>
                    </div>
                </div>
                <?php if(isset($errors) && count($errors) > 0){ ?>
                    <div class="row pb-2">
                        <div class="col-md-12">
                            <p class="text-danger text-center">Възникнаха грешки при създаване на потребител!</p>
                        </div>
                    </div>
                <?php } ?>
                <?php if($success == true){ ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-success text-center">
                                <p>Успешна регистрация!</p>
                                <p>Вече може да влезете във Вашият акаунт от <a href="login.php" id="custom-link-login">ТУК</a></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="POST" id="registration-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Потребителско име*</label>
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" placeholder="Въведете потребителско име" required aria-describedby="usernameHelp">
                                        <small id="usernameHelp" class="form-text text-muted">Възможно е въвеждане между 3 и 40 символа. Позволени: a-z A-Z 0-9 _-.</small>
                                        <?php if(isset($errors['username'])){?>
                                            <span class="text-danger"><?php echo $errors['username']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Имейл адрес*</label>
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Въведете Вашият имейл" required>
                                        <?php if(isset($errors['email'])){?>
                                            <span class="text-danger"><?php echo $errors['email']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password1">Парола*</label>
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        <input type="password" class="form-control" id="password1" name="password1" placeholder="Въведете парола" required aria-describedby="password1Help">
                                        <small id=""password1Help" class="form-text text-muted">Дължината на паролата трябва да е поне 10 символа.</small>
                                        <?php if(isset($errors['password'])){?>
                                            <span class="text-danger"><?php echo $errors['password']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password2">Потвърдете паролата*</label>
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        <input type="password" class="form-control" id="password2" name="password2" placeholder="Въведете парола" required aria-describedby="password2Help">
                                        <small id=""password2Help" class="form-text text-muted">Паролите трябва да съвпадат.</small>
                                        <?php if(isset($errors['password'])){?>
                                            <span class="text-danger"><?php echo $errors['password']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Име*</label>
                                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name; ?>" placeholder="Въведете име" required>
                                        <?php if(isset($errors['first_name'])){?>
                                            <span class="text-danger"><?php echo $errors['first_name']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="surname">Бащино име</label>
                                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                                        <input type="text" class="form-control" id="surname" name="surname" value="<?php echo $surname; ?>" placeholder="Въведете бащино име">
                                        <?php if(isset($errors['surname'])){?>
                                            <span class="text-danger"><?php echo $errors['surname']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Фамилия*</label>
                                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $last_name; ?>" placeholder="Въведете фамилия" required>
                                        <?php if(isset($errors['last_name'])){?>
                                            <span class="text-danger"><?php echo $errors['last_name']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="birthday">Дата на раждане*</label>
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        <input type="text" class="form-control" id="birthday" name="birthday" value="<?php echo $birthday; ?>" placeholder="Въведете рожденна дата" required aria-describedby="birthdayHelp">
                                        <small id="birthdayHelp" class="form-text text-muted">Моля, въведете дата на раждане във формат: ДД-ММ-ГГГГ.</small>
                                        <?php if(isset($errors['birthday'])){?>
                                            <span class="text-danger"><?php echo $errors['birthday']; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary" name="submit">Регистрация</button>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <p class="text-center">Полетата отбелязани със звездичка (*) са задължителни!</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <p class="text-center">Имате вече акаунт? <a href="login.php" class="login_link">Вход</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>