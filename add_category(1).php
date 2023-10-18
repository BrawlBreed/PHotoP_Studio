<?php 
$mysql = require 'db.php';
require_once('functions.php');

$name = isset($_POST['name']) ? $_POST['name']:'';
$description = isset($_POST['description']) ? $_POST['description']:'';
$active = isset($_POST['active']) ? $_POST['active']:'yes';

$success = isset($_GET['success']) ? $_POST['success']: false;
$category = isset($_GET['category']) ? $_GET['category']:'';

if(isset($_POST['submit'])){
    $data = $errors = [];

    session_start();

    $data = sanitizeUserData($_POST);
    
    if(strlen($data['name']) < 70 && strlen($data['name']) > 2){
        $sql = "
        SELECT `id`
        FROM `categories`
        WHERE `name` = '".$data['name'] . "'
            AND deleted IS NULL
        ";
        $query = mysqli_query($conn, $sql);

        if(mysqli_num_rows($query)){
            $errors['name'] = 'There is an existing category with that data..';
        }
    } else {
        $errors['name'] = 'The name you typed is too short!';
    }
    if(strlen($data['description']) < 5){
        $errors['description'] = 'The discription is too short!';
    }

    if($data['active'] == 'yes'){
        $data['active'] = '1';
    }else{
        $data['active'] = null;
    }
    
    if($errors == 0){
        $sql = "
        INSERT INTO `categories`(
        `name`
        ,`description`
        ,`active`
        ,`added`
        ,`added_user`
        )
        VALUES(
            '" .mysqli_real_escape_string($conn, $data['name']). "',
            '" .mysqli_real_escape_string($conn, $data['description']). "',
            '" .mysqli_real_escape_string($conn, $data['active']). "',
            NOW(),
            '" .mysqli_real_escape_string($conn, $_SESSION['user']['id']). "'
        )
        ";
        
        $query = mysqli_query($conn, $sql);
        if(!$query){
            echo mysqli_error($mysql);
            $errors['mysql'] = 'An error occured while saving the data of the category.Contact an administrator!';
        }else {
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
    <?php require('meta.php'); ?>        
</head>
<body>
    <?php require 'menu.php'; ?>
    <div class="container-fluid">
        <?php if($success == true){?>
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <p class="text-center">Successfully added category <?php echo $category ;?></p>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if(isset($errors) && $errors > 0){?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <?php if(isset($errors['mysql'])){ ?>
                            <p class="text-center"><?php echo $errors['mysql'] ;?></p>
                        <?php } else { ?>
                            <p class="text-center">Errors occured while creating the category!</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        <div class="row">
            <div class="col-4 pt-3">
                <a href="categories.php" class="btn"><i class="fa fa-arrow-left" aria-hidden="true"></i> List</a>
            </div>            
            <div class="col-4 text-center">
                <h2 class="center pt-5 pb-2" style="color: #C1A3A3;">Category Add</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form method="POST" action="" id="category-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" placeholder="Type in a name" aria-describedby="nameHelp" required> 
                        <small id="emailHelp" class="form-text form-muted">A lenght up to 70 symbols</small>
                        <?php if(isset($errors['name'])){ ?>
                            <span class="text text-danger"><?php echo $errors['name'] ?></span>
                        <?php } ?>                    
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Add your description"><?php echo $description; ?></textarea>
                        <small id="emailHelp" class="form-text form-muted">A lenght up to 500 symbols</small>
                        <?php if(isset($errors['description'])){ ?>
                            <span class="text text-danger"><?php echo $errors['description'] ?></span>
                        <?php } ?>   
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Activity</label>
                        <select class="form-control" id="active" name="active">
                            <option value="yes" <?php if($active == 'yes'){ ?> selected <?php ;} ?>>Yes</option>
                            <option value="no" <?php if($active == 'no'){ ?> selected <?php ;} ?>>No</option>
                        </select>
                    </div> 
                    <div class="form-group text-center">
                    <button type="submit" class="btn" name="submit" style="background-color: #886F6F;">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</body>
</html>