<?php
include '/Users/vineetsingh/Projects/Expo/DPS-Muskipur/bine/required/connection.php';
include '/Users/vineetsingh/Projects/Expo/DPS-Muskipur/bine/required/op_lib.php';

$selected_date = '2026-05-30';
$att_sql = "
    SELECT emp_id, latitude, longitude, selfie_file, created_at,
           checkout_latitude, checkout_longitude, checkout_file, checkout_time
    FROM selfie_attendance
    WHERE att_date = '$selected_date'
";
$res = direct_sql($att_sql);
print_r($res);
