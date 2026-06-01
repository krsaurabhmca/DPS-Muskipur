<?php require_once("required/header.php"); ?>
<?php require_once("required/menu.php");
if (isset($_REQUEST['date'])) {
    $date = $_REQUEST['date'];
} else {
    $date = date("Y-m-d");
}
if (isset($_REQUEST['subject']) and isset($_REQUEST['teacher_id'])) {
    extract($_REQUEST);
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Teacher-specific Timetable</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Timetable Management</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Teacher-specific Timetable [<span class="text-md text-bold"><?php echo get_data('employee', $teacher_id, 'e_name')['data'] . "-" . $subject ?></span>] </h3>
                <!-- <div class="box-tools pull-right"> -->
                <!-- <a class='fa fa-plus btn btn-success btn-sm' title='New Timetable' href='create_timetable'> </a> -->
                <!-- </div> -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form>
                                            <div class="row">
                                                <div class="form-group col-md-5">
                                                    <label for="subject" class="subject"></label>
                                                    <div>
                                                        <select name="subject" id="subject" required class="form-control">
                                                            <?php
                                                            $subjects = get_all('subject')['data'];
                                                            foreach ($subjects as $subject_group) {
                                                                extract($subject_group);
                                                                $subject_list[$subject_name] = $subject_name;
                                                            }
                                                            dropdown($subject_list, $subject); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-5">
                                                    <label for="teacher_list" class="form-label"></label>
                                                    <div>
                                                        <select name="teacher_id" id="teacher_list" required class="form-control">
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 btn-submit my-auto">
                                                    <button class="btn btn-warning btn-sm" onclick="submit()"> Show </button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="table-responsive">
                                            <a href="create_timetable" class="btn btn-primary btn-sm">Create</a>
                                            <a href="employee_attendance" class="btn btn-danger btn-sm">Attendance</a>
                                            <a href="" class="btn btn-warning btn-sm">Excel</a>
                                            <a onclick="window.print()" class="btn btn-secondary btn-sm">Print</a>
                                            <div class=""></div>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($day_list as $week) {
                                                            echo "<th>" . $week . "</th>";
                                                            $day = date('l', strtotime($date));
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
<?php require_once("required/footer2.php"); ?>
<script>
    function get_teacher() {
        let subject = $("#subject").find(":selected").text();
        $.ajax({
            'url': "required/master_process?task=get_teacher",
            'type': "POST",
            'data': {
                'subject': subject
            },
            'success': function(data) {
                $("#teacher_list").html(data);
            }
        })
    }
</script>