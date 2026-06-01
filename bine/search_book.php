<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $student = get_data('student', $student_id)['data'];
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Search Book </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#fee">Library</a></li>
            <li class="breadcrumb-item active">Search Book</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title" data-student='<?php echo json_encode($student); ?>'>Search A Book for <b><?php echo $student['student_name'] . " : " . $student['student_admission']; ?> </b></h3>

                <div class="box-tools pull-right">
                    <a class='fa fa-search btn btn-info btn-sm' href='issue_book' title='Search Student '> </a>

                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form action='' method='post'>
                    <div class='row'>

                        <div class="col-lg-2 col-offset-lg-2">
                            <div class="form-group">
                                <label>Search Via</label>
                                <select class="form-control" name='search_by' id='search_by' required>
                                    <!--<option value=''>Search Via</option>-->
                                    <option value='accession_no'>Accession No.</option>
                                    <option value='book_no'>Book No</option>
                                    <option value='book_name'>Book Name </option>
                                    <option value='author_name'>Author Name </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 ">
                            <div class="form-group">
                                <label>Select Category</label>
                                <select class="form-control" name='cat_id' id='cat_id'>
                                    <option value=''> Select Category </option>
                                    <?php dropdown_list('book_cat', 'id', 'cat_name'); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">

                                <div class="form-group has-success">
                                    <label class="control-label" for="inputSuccess">Enter value</label>
                                    <input type="text" class='form-control' name='search_text' required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp; Alt +S to Search </label>
                                <input type="submit" class='btn btn-success btn-block' value='Search' name='search' accesskey='s'>
                            </div>
                        </div>
                    </div>
                </form>

                <div class='row'>
                    <div class="col-lg-12">
                        <hr>
                        <!-- Advanced Tables -->
                        <?php
                        if (isset($_REQUEST['cat_id']) and isset($_REQUEST['search_text'])) {
                            $sql = "select * from book_list where status in('AVAILABLE') and  ";
                            $student_class = xss_clean(trim($_REQUEST['student_class']));
                            $search_by = xss_clean(trim($_REQUEST['search_by']));
                            $search_text = xss_clean(trim($_REQUEST['search_text']));


                            if ($search_by == 'book_no' or $search_by == 'accession_no') {
                                $sql .= " $search_by = '$search_text'";
                            }
                            if ($search_by == 'book_name' or $search_by == 'author_name') {
                                $sql .= " cat_id = '$cat_id' and $search_by like '%$search_text%'";
                            }
                        ?>

                            <div class="table-responsive">
                                <table id="example1" rules='all' border='1' width='100%' cellpadding='5'>
                                    <thead>
                                        <tr class='bg-secondary text-light'>
                                            <th>Accession No.</th>
                                            <th>Book Name</th>
                                            <th>Author Name</th>
                                            <th>Publisher Name</th>
                                            <th>Category </th>
                                            <th>Price</th>
                                            <th>Operation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    $res = direct_sql($sql);
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $dt = json_encode($row);
                                            $id = $row['id'];

                                            $status = $row['status'];
                                            $author = $row['author_name'];
                                            $pub = get_data('book_pub', $row['pub_id'], 'pub_name')['data'];
                                            $cat = get_data('book_cat', $row['cat_id'], 'cat_name')['data'];
                                            echo "<tr class='odd gradeX'>";

                                            echo "<td>" . $row['accession_no'] . "</td>";
                                            echo "<td>" . $row['book_name'] . "</td>";
                                            echo "<td>" . $row['author_name'] . "</td>";
                                            echo "<td>" . $pub . "</td>";
                                            echo "<td>" . $cat . "</td>";
                                            echo "<td>" . $row['book_price'] . "</td>";

                                            echo "<td>";
                                            echo "<button title='Select this Book' class='confirm_book btn btn-primary btn-sm' data-book ='$dt' data-publisher ='$pub' data-category ='$cat' data-author='$author' name='Confirm Now' > Issue </button>";

                                            echo "</td></tr>";
                                        }
                                    }
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
    $(document).on('change blur', '#search_by', function() {
        var x = $(this).val();
        console.log(x);
        if (x == 'author_name' || x == 'book_name' || x == '') {
            $("#cat_id").attr("required", true);
        } else {
            $("#cat_id").removeAttr("required");
        }
    });
    $(document).on('click', '.confirm_book', function() {
        $("#search_book").modal('show');
        var book = $(this).data('book');
        var pub = $(this).data('publisher');
        var author = $(this).data('author');
        var category = $(this).data('category');
        var lst = '<tr><td> Book Name </td><td><input type="hidden" id="book_id" value="' + book.id + '">' + book.book_name + '</td></tr>';
        var lst = lst + '<tr><td> Book Publisher </td><td>' + pub + '</td></tr>';
        var lst = lst + '<tr><td> Author </td><td>' + author + '</td></tr>';
        var lst = lst + '<tr><td> Category </td><td>' + category + '</td></tr>';
        var lst = lst + '</form><tr><td colspan="2"><button class="btn btn-success" id="confirm_btn"> Issue Now </button> </td></tr>';

        $("#search_result table tbody").html('');
        $("#search_result table tbody").append(lst);
    });

    $(document).on('click', '#confirm_btn', function() {
        alert("Hello");
        var book_id = $("#book_id").val();
        var student_id = $("#student_id").val();
        console.log(student_id + book_id);
        var text1 = $("#search_value").val();
        if (book_id == '') {
            $.notify("Please select a valid Book ")
        }
        if (student_id == '') {
            $.notify("Student Not Selected ")
        } else {
            var res = fetch_data('issue_book', {
                'student_id': student_id,
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
                <h3 class="modal-title" id="exampleModalCenterTitle"> Confirmation </h3>
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
                                <td> <?php echo date('d-M-Y h:i A', strtotime($current_date_time)); ?> </td>
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
                                <td> Class /Section </td>
                                <td> <?php echo $student['student_class'] . "/" . $student['student_section'] ?> </td>
                            </tr>
                            <tr>
                                <td> Admission No. / Roll No. </td>
                                <td> <?php echo $student['student_admission'] . " /" . $student['student_roll']; ?> </td>
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