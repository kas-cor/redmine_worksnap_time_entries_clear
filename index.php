<?php

$db = require __DIR__ . '/config.php';

function getHash($comment) {
    preg_match_all('/v\?k=([0-9a-f]+)\ *\)*/m', $comment, $matches, PREG_SET_ORDER, 0);
    return $matches[0][1];
}

function getMaxId($hash) {
    global $mysqli;
    $query = "SELECT MAX(`id`) as `max_id` FROM `time_entries` WHERE `comments` LIKE '%" . $hash . "%'";
    if ($result = mysqli_query($mysqli, $query)) {
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['max_id'];
        }
    }
    return false;
}

function delTime($hash, $maxId) {
    echo $hash . ' - ' . $maxId . PHP_EOL;
    /*
    $query = "SELECT FROM `time_entries` WHERE `comments` LIKE '%" . $hash . "%' AND `id`!='" . $maxId . "'";
    mysqli_query($mysqli, $query);
     * 
     */
}

if ($mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['base'])) {
    $query = "SELECT `comments`,COUNT(*) FROM `time_entries` WHERE `comments` LIKE '%Synced from Worksnaps%' GROUP BY `comments` HAVING COUNT(*)>1";
    if ($result = mysqli_query($mysqli, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $hash = getHash($row['comments']);
            if ($maxId = getMaxId($hash)) {
                delTime($hash, $maxId);
            }
        }
    }
} else {
    echo mysqli_connect_error() . PHP_EOL;
    exit(0);
}

mysqli_close($mysqli);

exit(1);
