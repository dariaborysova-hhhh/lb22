<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->iteh2lb1var4;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nurse_id = (int)$_POST['nurse_id'];
    $ward_id = (int)$_POST['ward_id'];
    $shift = $_POST['shift'];

    // 1. Палати, де чергує обрана медсестра
    $nurse_wards = $db->nurse_ward->find(['fid_nurse' => $nurse_id]);
    $ward_ids = [];
    foreach ($nurse_wards as $nw) {
        $ward_ids[] = $nw['fid_ward'];
    }

    $wards_for_nurse = [];
    if (!empty($ward_ids)) {
        $cursor = $db->wards->find(['_id' => ['$in' => $ward_ids]]);
        foreach ($cursor as $ward) {
            $wards_for_nurse[] = ['name' => $ward['name']];
        }
    }

    // 2. Медсестри обраного відділення
    $nurse = $db->nurses->findOne(['_id' => $nurse_id]);
    $nurses_in_department = [];
    if ($nurse) {
        $cursor = $db->nurses->find(['department' => $nurse['department']]);
        foreach ($cursor as $n) {
            $nurses_in_department[] = ['name' => $n['name']];
        }
    }

    // 3. Чергування в обрану зміну
    $nurses_in_shift = $db->nurses->find(['shift' => $shift]);
    $shifts = [];

    foreach ($nurses_in_shift as $n) {
        $links = $db->nurse_ward->find(['fid_nurse' => $n['_id']]);
        foreach ($links as $link) {
            $ward = $db->wards->findOne(['_id' => $link['fid_ward']]);
            if ($ward) {
                $shifts[] = ['name' => $n['name'], 'ward_name' => $ward['name']];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Результати запиту MongoDB</title>
</head>
<body>
    <h1>Результати запиту MongoDB</h1>

    <h2>Перелік палат, у яких чергує обрана медсестра:</h2>
    <table border="1">
        <thead>
            <tr><th>Назва палати</th></tr>
        </thead>
        <tbody>
            <?php foreach ($wards_for_nurse as $ward): ?>
                <tr><td><?= $ward['name'] ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Медсестри обраного відділення:</h2>
    <table border="1">
        <thead>
            <tr><th>Назва медсестри</th></tr>
        </thead>
        <tbody>
            <?php foreach ($nurses_in_department as $nurse): ?>
                <tr><td><?= $nurse['name'] ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Чергування в зазначену зміну:</h2>
    <table border="1">
        <thead>
            <tr><th>Медсестра</th><th>Палата</th></tr>
        </thead>
        <tbody>
            <?php foreach ($shifts as $shift): ?>
                <tr>
                    <td><?= $shift['name'] ?></td>
                    <td><?= $shift['ward_name'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
