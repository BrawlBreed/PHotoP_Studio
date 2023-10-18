<?php
    $mysql = require 'db.php';
    require_once('functions.php');

    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $active = isset($_POST['active']) ? $_POST['active'] : 'yes';

    $sql = "
        SELECT `id`,
            `name`
        FROM categories
        WHERE deleted IS NULL
    ";

    $query = mysqli_query($conn, $sql);

    $categories = [];

    while($row = mysqli_fetch_assoc($query)){
        $categories[] = $row;
    }

    if(isset($_POST['submit'])){
        $data = $errros = [];

        session_start();

        $data = sanitizeUserData($_POST);

        if(strlen($data['name']) < 70 && strlen($data['name']) > 2){
            $sql = "
                SELECT `id`
                FROM `products`
                WHERE name = '".$data['name']."'
                    AND deleted IS NULL
            ";

            $query =mysqli_query($conn, $sql);

            if(mysqli_num_rows($query)){
                $errors['name'] = 'Съществува такъв продукт!';
            }

        } else {
            $errors['name'] = 'Невалидна дължина!';
        }

        $data['price'] = floatval($data['price']);

        if(is_float($data['price']) || is_int($data['price'])){
            if($data['price'] < 0){
                $errors['price'] = 'Невалидна сума';
            }
        } else {
            $errors['price'] = 'Невалидно число';
        }

        if(count($errors) == 0){
            $sql = " INSERT INTO `products`(
                `name`,
                `description`,
                `category_id`,
                `price`,
                `active`,
                `added`,
                `added_user`
            ) VALUES (
                '".mysqli_real_escape_string($conn, $data['name'])."',
                '".mysqli_real_escape_string($conn, $data['description'])."',
                '".mysqli_real_escape_string($conn, $data['category'])."',
                '".mysqli_real_escape_string($conn, $data['price'])."',
                '".mysqli_real_escape_string($conn, $data['active'])."',
                NOW(),
                '".mysqli_real_escape_string($conn, $_SESSION['user']['id'])."'
            )
            ";

            $query = mysqli_query($conn, $sql);

            if(!$query){
                $errors['mysql'] = 'Възникна грешка при добавяне на данните! Моля, свържете се с администратор!';
            } else {
                header('Location:products.php');
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
                    <h2>Dобавяне на продукт</h2>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <a class="btn btn-info" href="products.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Списък</a>
                </div>
            </div>
            <?php if(isset($errors) && count($errors) > 0){ ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <?php if(isset($errors['mysql'])){ ?>
                                <p class="text-center"><?php echo $errors['mysql']; ?></p>
                            <?php } else { ?>
                                <p class="text-center">Възникнаха грешки при добавяне на продукт!</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-8 offset-col-2">
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
                            <label for="category">Категория</label>
                            <select class="form-control" id="category" name="category">
                                <?php foreach ($categories as $category){ ?>
                                    <option value="<?php echo $category['id']; ?>" ><?php echo $category['name']; ?></option>
                                <?php } ?>    
                            </select>
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
                            <label for="price">Цена</label>
                            <input type="text" class="form-control" id="price" name="price" value="<?php echo $price; ?>" placeholder="Въведете цена" >
                            <?php if(isset($errors['price'])){ ?>
                                <span class="text-danger"><?php echo $errors['price']; ?></span>
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