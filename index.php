<?php
require 'vendor/autoload.php'; // якщо використовуєш composer і бібліотеку mongodb/mongodb

$client = new MongoDB\Client("mongodb://localhost:27017");
$collectionNurses = $client->iteh2lb1var4->nurses;
$collectionWards = $client->iteh2lb1var4->wards;

// Отримання медсестер
$nurses = $collectionNurses->find([], ['sort' => ['_id' => 1]]);

// Отримання палат
$wards = $collectionWards->find([], ['sort' => ['_id' => 1]]);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Медсестри та їх чергування MongoDB</title>
</head>
<body>
    <h1>Запити до бази даних MongoDB</h1>

    <form action="query_results.php" method="POST">
        <label for="nurse_id">Виберіть медсестру:</label>
        <select name="nurse_id" id="nurse_id">
            <?php foreach ($nurses as $nurse): ?>
                <option value="<?= $nurse->_id ?>"><?= $nurse->name ?></option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label for="ward_id">Виберіть палату:</label>
        <select name="ward_id" id="ward_id">
            <?php foreach ($wards as $ward): ?>
                <option value="<?= $ward->_id ?>"><?= $ward->name ?></option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label for="shift">Виберіть зміну:</label>
        <select name="shift" id="shift">
            <option value="First">Перша</option>
            <option value="Second">Друга</option>
            <option value="Third">Третя</option>
        </select>

        <br><br>
        <input type="submit" value="Отримати дані">
    </form>
</body>
</html>
