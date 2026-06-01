<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Question
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Lesson Plan</li>
            <li class="breadcrumb-item active">Manage Question</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Question Details</h3>
                        <div class="box-tools pull-right">
                            <a class='fa fa-plus btn btn-success btn-sm' title='Create New Question' href='add_question'> </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Lesson</th>
                                        <th>Topic</th>
                                        <th>Question</th>
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $res = get_all('question_bank');
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            extract($row);
                                    ?>
                                            <tr>
                                                <td><?php echo $class; ?></td>
                                                <td><?php echo $subject; ?></td>
                                                <td><?php echo get_data('lesson_plan', $lesson_id, 'lesson')['data']; ?></td>
                                                <td><?php echo get_data('lesson_plan', $lesson_id, 'topic')['data']; ?></td>
                                                <td><?php echo $question; ?></td>
                                                <td><?php echo $status; ?></td>
                                                <td class='text-right'>
                                                    <?php echo btn_view('question_bank', $id, get_data('lesson_plan', $lesson_id, 'lesson')['data']); ?>
                                                    <?php echo btn_edit('add_question', $id); ?>
                                                    <?php echo btn_delete('question_bank', $id); ?>
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