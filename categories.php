<?php

$mysql = require 'db.php';

require_once('functions.php');

$sql = "
    SELECT `categories`.`name`,
    `categories`.`description`,
    `categories`.`active`,
    `categories`.`added`,
    CONCAT_WS(' ', `users`.`first_name`, `users`.`last_name`) as `names`
    FROM `categories`
    LEFT JOIN `users` ON `categories`.`added_user` = `users`.`id`
    WHERE `categories`.`deleted` IS NULL
";

$query = mysqli_query($conn, $sql);
if(!$query){
    P(mysqli_error($conn));
    exit;
}

$categories = [];

while($row = mysqli_fetch_assoc($query)){
    if($row['status'] == '1'){
        $row['status'] = 'Да';
    } else {
        $row['status'] = 'Не';
    }

    if($row['added']){
        $row['added'] = date('H:i:s d-m-Y', strtotime($row['added']));
    }

    $categories[] = $row;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <?php require 'meta.php'; ?>
    </head>
    <body>
        <?php require 'menu.php'; ?>
        <div class="container">
            <div class="row mb-2">
                <div class="col-md-12 text-right">
                    <a class="btn btn-success" href="add_category.php"><i class="fa fa-plus-circle" aria-hidden="true"></i> Добави нова</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Наименование</th>
                                    <th scope="col">Описание</th>
                                    <th scope="col">Активност</th>
                                    <th scope="col">Добавена от</th>
                                    <th scope="col">Добавена на</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($categories) > 0){ ?>
                                    <?php foreach($categories as $category){ ?>
                                        <tr>
                                            <td><?php echo $category['name']; ?> </td>
                                            <td><?php echo $category['description']; ?> </td>
                                            <td><?php echo $category['status']; ?> </td>
                                            <td><?php echo $category['added']; ?> </td>
                                            <td><?php echo $category['names']; ?> </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5">Няма намерени резултати</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5"><b>Общо: </b> <?php echo count($categories); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>