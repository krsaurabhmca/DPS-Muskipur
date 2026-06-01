<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('subject');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('subject', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Exam Setting</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Exam</a></li>
			<li class="breadcrumb-item active">Add Exam</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Exam Details</h3>

				<!--<div class="box-tools pull-right">-->
				<!--	<a class='fa fa-plus btn btn-info btn-sm' href='subject_setting' title='Add Fee Head'> </a>-->

				<!--</div>-->
			</div>
			<!-- /.box-header -->

		<div class="box-body">
    <div class='row'>
        <div class='col-lg-4'>
            <form action='update_exam' id='update_frm' enctype='multipart/form-data'>
               <div class="form-group">
                <label>Subject Name</label>
                <input class="form-control" type='hidden' name='id' value='<?php echo $id; ?>' required>
                <select class="form-control" name="subject_id" required>
                <option value="">Select Subject</option>
                <?php
                // Assuming you have a database connection established
                $query = "SELECT id, subject_name FROM subject WHERE status='ACTIVE'";
                $result = direct_sql($query);
                    foreach ((array)$result['data'] as $row) {
                        $selected = ($row['id'] == $subject_id) ? "selected" : "";
                        echo "<option value='" . $row['id'] . "' $selected>" . $row['subject_name'] . "</option>";
                    }
                
                ?>
            </select>
            </div>
                <div class="form-group">
                    <label>Exam Name</label>
                    <select class='form-control' name='exam_name'>
                        <option value=''>Select Exam Name</option>
                        <option><?= dropdown($exam_list) ?></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                    <label>Theory Marks</label>
                    <input class="form-control" type='number' name='f_marks' value='<?php echo $f_marks; ?>' required>
                </div>
                <div class="form-group">
                    <label>Pass Marks</label>
                    <input class="form-control" type='number' name='p_marks' value='<?php echo $p_marks; ?>' required>
                </div>
                
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                    <label>Oral Marks</label>
                    <input class="form-control" type='text' name='o_marks' value='<?php echo $o_marks; ?>'>
                </div>
            </div>
        </div>
    </div>
</form>

         <div class="text-right">
            <button class="btn btn-primary" id='update_btn'>Add New Exam</button>
        </div>
				<hr>
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th> # </th>
								<th> Subject Name </th>
								<th> Exam Name</th>
								<th> Full Marks </th>
								<th> Pass Marks </th>
								<th> Oral Marks </th>
								<th> Action </td>
							</tr>
						</thead>
						<tbody>

							<?php
							$query = "select exam_setting.*, subject.subject_name from exam_setting, subject where exam_setting.status='ACTIVE' and exam_setting.subject_id=subject.id";

							$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
							while ($row = mysqli_fetch_array($res)) {
								$id = $row['id'];
								echo "<tr><td>" . $row['subject_name'] . "</td>";
								echo "<td>" . $row['exam_name'] . "</td>";
								echo "<td>" . $row['f_marks'] . "</td>";
								echo "<td>" . $row['p_marks'] . "</td>";
								echo "<td>" . $row['o_marks'] . "</td>";

							?>
								<td>
									<a href='create_exam.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
									<!--<span class='delete_subject btn btn-danger btn-sm' data-table='subject' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>-->
								</td>
								</tr>
							<?php

							}
							?>


						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>

<script>
    document.getElementById('yes').addEventListener('click', function() {
        document.getElementById('oral_marks_group').style.display = 'block';
    });

    document.getElementById('no').addEventListener('click', function() {
        document.getElementById('oral_marks_group').style.display = 'none';
    });
</script>