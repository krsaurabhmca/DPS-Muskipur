<?php require_once("required/header.php"); ?>
<?php require_once("required/menu.php"); ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Classroom Timetable</h1>
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
                <h3 class="box-title">Classroom Timetable</h3>
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
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="150px">Timeslot</th>
                                                        <?php
                                                        foreach ($class_list as $class) {
                                                            if ($class == "") {
                                                                continue;
                                                            }
                                                            for ($i = 1; $i <= 2; $i++) {
                                                                if ($i == 1) {
                                                                    $sec = "A";
                                                                } else {
                                                                    $sec = "B";
                                                                }
                                                                echo "<th class='text-center'>" . $class . "-" . $sec . "</th>";
                                                            }
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
                                                        echo "<td width='120px'>" . $timeslot . "</td>";
                                                        foreach ($class_list as $class) {
                                                            if ($class == "") {
                                                                continue;
                                                            }
                                                            for ($i = 1; $i <= 2; $i++) {
                                                                if ($i == 1) {
                                                                    $sec = "A";
                                                                } else {
                                                                    $sec = "B";
                                                                }
                                                                $get_data = get_all('timetable', "*", array('class' => $class, 'section' => $sec, 'timeslot' => $id));
                                                                if ($get_data['count'] > 0) {
                                                                    foreach ($get_data['data'] as $data) {
                                                                        echo "<td align='center'>" . strtoupper($data['subject']) . "<br>[" .
                                                                            get_data('employee', $data['faculty_id'], 'e_name')['data'] . "]</td>";
                                                                    }
                                                                } else {
                                                                    echo "<td></td>";
                                                                }
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