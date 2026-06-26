<?php
$url = "https://dpsmushkipur.com/bine/api.php?task=get_student_profile";
$data = array('student_id' => '5');

$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) { 
    echo "Error";
} else {
    var_dump($result);
}
?>
