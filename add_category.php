<?php
    $mysql = require 'db.php';
    require_once('functions.php');

    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $active = isset($_POST['active']) ? $_POST['active'] : 'yes';

    $success = isset($_GET['success']) ? $_GET['success'] : false;
    $category = isset($_GET['category']) ? $_GET['category'] : '';


    if(isset($_POST['submit'])){
        $data = $errors  = [];

        session_start();

        $data = sanitizeUserData($_POST);

        if(strlen($data['name']) < 70 && strlen($data['name']) > 2){
            $sql = "
                SELECT `id`
                FROM `categories`
                WHERE name = '".$data['name']."'
                    AND deleted IS NULL
            ";

            $query =mysqli_query($conn, $sql);

            if(mysqli_num_rows($query)){
                $errors['name'] = 'Съществува такава категория!';
            }

        } else {
            $errors['name'] = 'Невалидна дължина!';
        }

        if(strlen($data['description']) < 5){
            $errors['description'] = 'Твърде кратко oписание!';
        }

        if($data['active'] == 'yes'){
            $data['active'] = '1';
        } else {
            $data['active'] = null;
        }

        if(count($errors) == 0){
            $sql ="
                INSERT into `categories`(
                    `name`,
                    `description`,
                    `active`,
                    `added`,
                    `added_user`
                ) VALUES(
                    '".mysqli_real_escape_string($conn, $data['name'])."',
                    '".mysqli_real_escape_string($conn, $data['description'])."',
                    '".mysqli_real_escape_string($conn, $data['active'])."',
                    NOW(),
                    '".mysqli_real_escape_string($conn, $_SESSION['user']['id'])."'
                )
            ";

            $query = mysqli_query($conn, $sql);
            if(!$query){
                // echo mysqli_error($conn);
                $errors['mysql'] = 'Възникна грешка при добавяне на данните! Моля, свържете се с администратор!';
            } else {
                $category = $data['name'];
                header('Location:add_category.php?success=true&category=$category');
                exit;
            }
        }

    }


?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require 'meta.php'; ?>
    </head>
    <body>
        <?php require 'menu.php'; ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center">Добавяне на категория</h2>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <a class="btn btn-info" href="categories.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Списък</a>
                </div>
            </div>
            <?php if($success == true){ ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <p class="text-center">Вие успешно добавихте "<?php print_r($category); ?>"!</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($errors) && count($errors) > 0){ ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <?php if(isset($errors['mysql'])){ ?>
                                <p class="text-center"><?php echo $errors['mysql']; ?></p>
                            <?php } else { ?>
                                <p class="text-center">Възникнаха грешки при добавяне на категория!</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Наименование</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" placeholder="Въведете наименование" aria-describedby="nameHelp">
                            <small id="emailHelp" class="form-text text-muted">Дължина до 70 символа</small>
                            <?php if(isset($errors['name'])){ ?>
                                <span class="text-danger"><?php echo $errors['name']; ?></span>
                            <?php } ?>    
                        </div>
                        <div class="form-group">
                            <label for="description">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="5" placeholder="Въведете описание" maxlength="200" aria-describedby="descriptionHelp"><?php echo $description; ?></textarea>
                            <small id="descriptionHelp" class="form-text text-muted">Дължина до 200 символа</small>
                            <?php if(isset($errors['description'])){ ?>
                                <span class="text-danger"><?php echo $errors['description']; ?></span>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="active">Активност</label>
                            <select class="form-control" id="active" name="active">
                                <option value="yes" <?php if($active=='yes'){ ?> selected <?php } ?> >Да</option>
                                <option value="no" <?php if($active=='no'){ ?> selected <?php } ?> >Не</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Добави</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>