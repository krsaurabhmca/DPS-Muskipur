<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Timetable</h1>
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
                <h3 class="box-title">Manage Timetable </h3>
                <div class="box-tools pull-right">
                    <a class='fa fa-plus btn btn-success btn-sm' title='New Timetable' href='create_timetable'> </a>
                </div>
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
                                                        <th>Class</th>
                                                        <th>Subject</th>
                                                        <th>Faculty</th>
                                                        <th>Day</th>
                                                        <th>Timeslot</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $res = get_all("timetable");
                                                    foreach ($res['data'] as $row) {
                                                        extract($row);
                                                        echo "<tr class='odd'>";
                                                        echo "<td>" . $class . "-" . $section . "</td>";
                                                        echo "<td>" . $subject . "</td>";
                                                        echo "<td>" . get_data('employee', $faculty_id, 'e_name')['data'] . "</td>";
                                                        echo "<td>" . $week_day . "</td>";
                                                        echo "<td>" . get_data('timeslot', $timeslot, 'timeslot')['data'] . "</td>";
                                                        echo "<td>" . $status . "</td>";
                                                        echo "<td align='right'>";
                                                        echo btn_view('timetable', $id, $class);
                                                        echo btn_edit('create_timetable', $id);
                                                        echo btn_delete('timetable', $id);
                                                        echo "</td></tr>";
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
<?php require_once('required/footer2.php'); ?>