<?php
include '/Users/vineetsingh/Projects/Expo/DPS-Muskipur/bine/required/connection.php';
$res = mysqli_query($con, "SELECT id, emp_id, att_month, d_30 FROM employee_att WHERE emp_id=2");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
