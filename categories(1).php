<?php
require 'db.php';
require_once ('functions.php');
$name = isset($_POST['name']) ? $_POST['name']:'';
$description = isset($_POST['description']) ? $_POST['description']:'';
$active = isset($_POST['active']) ? $_POST['active']:'yes';


$sql = "";


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require 'meta.php'; ?>
    </head>
    <body>
        <?php require 'menu.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7 offset-2 text-right">
                    <a class="btn btn-success mt-2" href="add_category.php"><i class="fa fa-plus-circle " aria-hidden="true"></i> Add new</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 offset-2">
                    <div class="table-responsive">
                        <table class="table">
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>