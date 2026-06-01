<?php require_once("required/header.php"); ?>
<?php require_once("required/menu.php"); ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Faculty Timetable</h1>
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
                <h3 class="box-title">Faculty Timetable </h3>
                <!-- <div class="box-tools pull-right">
                    <a class='fa fa-plus btn btn-success btn-sm' title='New Timetable' href='create_timetable'> </a>
                </div> -->
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Timeslot</th>
                                                        <?php
                                                        $teachers = get_all('employee', '*', array("e_category" => "TEACHER"))['data'];
                                                        foreach ($teachers as $details) {
                                                            extract($details);
                                                            echo "<th>" . $e_name . "</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT * from timeslot_table where status not in('AUTO','DELETED') order by period";
                                                    $timeslot_data = direct_sql($sql)['data'];
                                                    foreach ($timeslot_data as $order_by_timeslot) {
                                                        extract($order_by_timeslot);
                                                        echo "<tr>";
                                                        echo "<td width='100px'>" . $timeslot . "</td>";
                                                        $teacher_list = get_all('employee', '*', array("e_category" => "TEACHER"))['data'];
                                                        foreach ($teacher_list as $details) {
                                                            $get_data = get_all('timetable', "*", array('faculty_id' => $details['id'], 'timeslot' => $id));
                                                            if ($get_data['count'] > 0) {
                                                                echo "<td>" . strtoupper($get_data['data']['0']['subject']) . "<br>" . $get_data['data'][0]['class'] . "-" . $get_data['data'][0]['section'] . "</td>";
                                                            }
                                                        }
                                                    }
                                                    ?>
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