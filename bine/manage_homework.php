<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Homework
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Student Management</li>
            <li class="breadcrumb-item active">Manage Homework</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Homework Details</h3>
                        <div class="box-tools pull-right">
                            <a class='fa fa-plus btn btn-success btn-sm' title='New Lesson' href='add_homework'> </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $res = get_all('homework');
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                    ?>
                                            <tr>
                                                <td><?php echo $row['class']; ?></td>
                                                <td><?php echo $row['section']; ?></td>
                                                <td><?php echo $row['subject']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($row['date'])); ?></td>
                                                <td><?php echo $row['status']; ?></td>
                                                <td class='text-right'>
                                                    <?php echo btn_view('homework', $id, $row['subject']); ?>
                                                    <?php echo btn_edit('add_homework', $id); ?>
                                                    <?php echo btn_delete('homework', $id); ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once('required/footer2.php'); ?>