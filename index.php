<?php
$log_directory = dirname(__FILE__);
// $files = [];
$path = $log_directory . '\scripts\*.*';

$output = array();
$res = array();
$fullnameRegex = "/\sis\s(.*)\swith\semail/";
$idRegex = "/\sID\s(.*)\susing/";
$languageRegex = "/\susing\s(.*)\sfor/";
$emailRegex = "/\semail\s(.*)\swith/";
$generalRegex = "/\Hello World, this is (.*) with email (.*) with HNGi7 ID (.*) using (.*) for stage 2 task/";

foreach (glob($path) as $key => $file) {
    $new = array();
    if (pathinfo($file)['extension'] == 'js') {
        exec("node $file", $output);
    } else if (pathinfo($file)['extension'] == 'py') {
        exec("python $file", $output);
    } else if (pathinfo($file)['extension'] == 'dart') {
        exec("dart $file", $output);
    } else if (pathinfo($file)['extension'] == 'php') {
        exec("php $file", $output);
    }
    $new['output'] = $output[$key];

    //check status
    if (preg_match("$generalRegex", $new['output'])) {
        $new['status'] = 'Passed';
    } else {
        $new['status'] = 'Failed';
    }

    //get file
    $new['file'] = pathinfo($file)['basename'];

    //get fullname
    if (preg_match("$fullnameRegex", $output[$key], $matches1)) {
        $new['name'] = $matches1[1];
    }

    //get hng id
    if (preg_match("$idRegex", $output[$key], $matches1)) {
        $new['ID'] = $matches1[1];
    }

    //get langugae
    if (preg_match("$languageRegex", $output[$key], $matches1)) {
        $new['language'] = $matches1[1];
    }

    //get email 
    if (preg_match("$emailRegex", $output[$key], $matches1)) {
        $new['email'] = $matches1[1];
    }

    array_push($res, $new);
}

$final_res = json_encode($res, true);


if (isset($_GET["json"])) {
    // $content = json_encode($content);
    echo $final_res;
    exit;
} else {
    var_dump($res);

    ob_flush();
    $exit; //flusing the output stream
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Storm</title>
</head>

<body>
    <h1>Team Storm</h1>
    <?php foreach ($res as $data) : ?>
        <?php if (isset($data['error'])) : ?>
            <p><?= "error" ?></p>
        <?php else : ?>
            <p><?= $data['output'] ?></?>
            <?php endif; ?>
        <?php endforeach; ?>

</body>

</html>