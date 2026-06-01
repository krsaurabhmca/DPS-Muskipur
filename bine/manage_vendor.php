<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Vendor <span class="badge badge-success badge-sm p-2">NEW</span></h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Inventory</a></li>
            <li class="breadcrumb-item active">Vendor</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"> Vendor Details </h3>
                <div class="box-tools pull-right">
                    <a class='fa fa-plus btn btn-success btn-sm' title='New Vendor' href='add_vendor'> </a>
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
                                                        <th> # </th>
                                                        <th> Name </th>
                                                        <th> Mobile</th>
                                                        <th>Opening Balance</th>
                                                        <th>Closing Balance</th>
                                                        <th>GST</th>
                                                        <th>Joining Date</th>
                                                        <th>Status</th>
                                                        <th> Operation </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php
                                                    $i = 1;
                                                    $res = get_all('vendor');
                                                    if ($res['count'] > 0) {
                                                        foreach ($res['data'] as $row) {
                                                            $id = $row['id'];
                                                            echo "<tr>";
                                                            echo "<td>" . $i . "</td>";
                                                            echo "<td>" . $row['name'] . "</td>";
                                                            echo "<td>" . $row['mobile'] . "</td>";
                                                            echo "<td>" . $row['opening_balance'] . "</td>";
                                                            echo "<td>" . $row['closing_balance'] . "</td>";
                                                            echo "<td>" . $row['gst'] . "</td>";
                                                            echo "<td>" . date('d M Y', strtotime($row['doj'])) . "</td>";
                                                            echo "<td>" . $row['status'] . "</td>";
                                                    ?>
                                                            <td>
                                                                <a href='add_vendor?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                                                <span class='delete_btn btn btn-danger btn-sm' data-table='vendor' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
                                                            </td>
                                                    <?php
                                                            $i++;
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
<?php require_once('required/footer2.php'); ?>