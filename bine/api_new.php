<?php
require_once("required/op_lib.php");
if (isset($_REQUEST['token'])) {
    if (isset($_REQUEST['task'])) {
        $task = $_REQUEST['task'];
    } else { ?>
        <script>
            window.location = "<?php echo $_SERVER['HTTP_REFERER']; ?>";
        </script>
<?php
    }
    switch ($task) {
        case "admit_card":
            extract($_POST);
            if (isset($student_admission) or isset($student_roll)) {
                header("location:print_admit");
            } else {
                echo '<script>alert("Invalid Admission or roll Number")</script>';
            }
            break;

        case "exam_routine":
            extract($_POST);
            if (isset($student_class)) {
                header("location:print_exam_routine");
            } else {
                echo '<script>alert("No routine available")</script>';
            }
            break;

        case 'add_enquiry':
            extract($_POST);
            insert_data('enquiry', $_POST);
            $subject = "Enquiry From Website";
            $msg = "An enquiry is created from website please contact him or her for better approach.";
            $mail = rtf_mail($inst_email, $subject, $msg);
            if ($mail == "success") {
                $res['msg'] = "Your Enquiry is successfully sent to the concern person";
                $res['status'] = $mail;
            } else {
                $res['msg'] = "Sorry! Something get wrong";
                $res['status'] = $mail;
            }
            echo json_encode($res);
            break;

        case "student_info":
            extract($_POST);
            $res = get_all('student', "*", array('student_admission' => $admission_no))['data'];
            echo json_encode($res);
            break;

        case "get_homework":
            extract($_POST);
            $h_date = date("Y-m-d", strtotime($date));
            $res = get_all('homework', '*', array('class' => $student_class, 'date' => $h_date))['data'][0];
            echo json_encode($res);
            break;

            // case "get_attendance":
            //     extract($_POST);
            //     if(isset($date)){

            //     }
        default:
            echo "invalid action";
    }
}
