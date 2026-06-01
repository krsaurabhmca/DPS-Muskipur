<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
$table_name = 'book_txn';
if ($_GET['status'] != '') {
  $res = get_all($table_name, '*', array('status' => $_GET['status']));
} else {
  $res = get_all($table_name);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Issued Books
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item">Library</li>
      <li class="breadcrumb-item active"> Issued Book </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Details of Issued Books </h3>

            <div class="box-tools pull-right">
              <form>
                <select name='status' onchange='submit()'>
                  <?php dropdown($book_txn_status, $_GET['status']); ?>
                </select>
              </form>
            </div>

          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Student</th>
                    <th>Adm No.</th>
                    <th>Book No.</th>
                    <th>Book Name</th>
                    <th>Issue Date</th>
                    <th>Issue By</th>
                    <th>Status</th>
                    <th class='text-right'>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if ($res['count'] > 0) {
                    $fee = 0;
                    foreach ($res['data'] as $row) {
                      $id = $row['id'];
                      $student = get_data('student', $row['student_id'])['data'];
                      $book = get_data('book_list', $row['book_id'])['data'];
                      $issue_by = get_data('user', $row['issue_by'], 'user_name')['data'];
                  ?>
                      <tr>
                        <td><?php echo $student['student_name'] ?></td>
                        <td><?php echo $student['student_admission'] ?></td>
                        <td><?php echo $book['accession_no'] ?></td>
                        <td><?php echo $book['book_name']; ?></td>
                        <td><?php echo $row['issue_date']; ?></td>
                        <td><?php echo $issue_by; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td class='text-right'>
                          <?php echo btn_view($table_name, $id, $book['book_name']); ?>
                          <?php //echo btn_edit('add_book', $id); 
                          ?>
                          <?php //echo btn_delete($table_name, $id); 
                          ?>
                          <?php if ($row['status'] == 'ISSUED') { ?>
                            <span class='return_book btn btn-dark btn-xs' data-id='<?php echo $id; ?>' data-book='<?php echo $book['book_name']; ?>' data-book_id='<?php echo $row['book_id']; ?>'> <i class="fa fa-undo" aria-hidden="true"></i></span>
                          <?php } ?>
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
<script>
  $(document).on('change blur', '#search_by', function() {
    var x = $(this).val();
    console.log(x);
    if (x == 'author_name' || x == 'book_name' || x == '') {
      $("#cat_id").attr("required", true);
    } else {
      $("#cat_id").removeAttr("required");
    }
  });
  $(document).on('click', '.return_book', function() {
    $("#search_book").modal('show');
    var txn_id = $(this).data('id');
    var book_id = $(this).data('book_id');
    var book_name = $(this).data('book');
    var lst = '<tr><td> Book Name </td><td><input type="hidden" id="txn_id" value="' + txn_id + '"> <input type="hidden" id="book_id" value="' + book_id + '">' + book_name + '</td></tr>';
    var lst = lst + '</form><tr><td colspan="2"><button class="btn btn-success btn-md" id="return_btn"> Marks as Return </button> </td></tr>';

    $("#search_result table tbody").html('');
    $("#search_result table tbody").append(lst);
  });

  $(document).on('click', '#return_btn', function() {
    var book_id = $("#book_id").val();
    var id = $("#txn_id").val();
    var return_date = $("#return_date").val();
    var book_fine = $("#book_fine").val();
    var remarks = $("#remarks").val();
    if (return_date == '') {
      $.notify("Please select a valid Return Date ")
    } else {
      var res = fetch_data('return_book', {
        'id': id,
        'remarks': remarks,
        'book_fine': book_fine,
        'return_date': return_date,
        'book_id': book_id
      });
      $.notify(res.msg, res.status);
      if (res.status == 'success') {
        window.location = res.url;
      }
    }
  });
</script>
<!-- =========== Confiramtion Modal ========= -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id='search_book'>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border">
        <h3 class="modal-title" id="exampleModalCenterTitle"> Return Details </h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div id='search_result'>

          <table class='table' width='100%'>
            <thead>
              <tr>
                <td> Issue Date & Time </td>

                <td>
                  <?php echo date('d-M-Y h:i A', strtotime($current_date_time)); ?> </td>
              </tr>
              <tr>
                <td> Issue By </td>
                <td> <?php echo $user_name; ?> </td>
              </tr>
              <tr>
                <td> Student Name </td>
                <td>
                  <input type='hidden' value='<?php echo $student_id; ?>' id='student_id'>
                  <?php echo $student['student_name']; ?>
                </td>
              </tr>
              <tr>
                <td> Return Date </td>
                <td> <input type='date' id='return_date' value='<?php echo date('Y-m-d'); ?>'> </td>
              </tr>
              <tr>
                <td> Fine (if Any) </td>
                <td> <input type='text' id='book_fine' value=''> </td>
              </tr>

              <tr>
                <td> Remarks </td>
                <td> <input type='text' id='remarks' value=''> </td>
              </tr>

            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>