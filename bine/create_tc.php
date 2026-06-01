<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['student_admission'])) {
    $adm = $_GET['student_admission'];
    $res = insert_data('tbl_tc', array('student_admission' => $adm, 'doa_certificate' => date('Y-m-d'), 'doi_certificate' => date('Y-m-d')));
    $stu_data = get_data('student', $adm, null, 'student_admission')['data'];
    $data = get_data('tbl_tc', $adm, null, 'student_admission')['data'];
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Transfer Certificate</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#transport">Certificate</a></li>
            <li class="breadcrumb-item active">Transfer Certificate</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Transfer Certificate</h3>

                <div class="box-tools pull-right">
                    <div style='float:right'>
                        <form>
                            <input type='text' name='student_admission' placeholder='Enter Admission No. '>
                            <button class='btn btn-danger btn-sm'>Search </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class='row'>
                    <div class="col-lg-4">
                        <form action='create_tc' method='post' enctype='multipart/form-data' target='_blank' id="update_frm">

                            <div class="form-group">
                                <label>Student Id</label>
                                <input class="form-control" name='id' type='hidden' value='<?php echo $data['id']; ?>' required>
                                <input class="form-control" name='student_admission' value='<?php echo $data['student_admission']; ?>' required readonly>
                            </div>

                            <div class="form-group">
                                <label>Enter Student Name</label>
                                <input class="form-control" value='<?php echo $stu_data['student_name']; ?>' name='student_name' readonly>

                            </div>
                            <div class="form-group">
                                <label>Enter Mother's Name</label>
                                <input class="form-control" value='<?php echo $stu_data['student_mother']; ?>' name='student_mother'>
                            </div>
                            <div class="form-group">
                                <label>Enter Father's Name</label>
                                <input class="form-control" value='<?php echo $stu_data['student_father']; ?>' name='student_father'>
                            </div>
                            
                           
                            
                            <div class="form-group">
                                <label>Whether the candidate belongs to Sechduled Caste or Sechduled Tribe</label>
                                <input class="form-control" name='sc_st' value='<?php echo $stu_data['student_category']; ?>' readonly>
                            </div>
                            
                            
                            <div class="form-group">
                                <label>Admission No.</label>
                                <input class="form-control" value='<?php echo $data['admission_no']; ?>' name='admission_no' >

                            </div>
                            <div class="form-group">
                                <label>CBSE Reg No.(if class IX,X)</label>
                                <input class="form-control" value='<?php echo $data['cbsereg_no']; ?>' name='cbsereg_no' >

                            </div>
                            <div class="form-group">
                                <label>Date of First admission in school with class</label>
                                <select name='d_first' class='form-control' required>
                                    <?php dropdown($class_list, $data['d_first']); ?>
                                </select>
                                
                                <div class="form-group">
                                <input class="form-control" name='d_first_date' value='<?php $data['d_first']; ?>' type='date'>
                                 </div>
                            </div>



                            <div class="form-group">
                                <label>Date of Birth (in Christion era) according to Admission Register</label>
                                <input class="form-control" name='d_birth' type='date' value='<?php echo date('Y-m-d', strtotime($stu_data['date_of_birth'])); ?>' readonly>

                                <input class="form-control mt-2" name='dob_text' type='text' value="<?php echo $data['dob_text']; ?>" placeholder='DOB in Text'>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Class in which the student last studied</label>
                            <select name='last_class' class='form-control' required>
                                <?php dropdown($class_list, $stu_data['student_class']); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>School/ Board Annual examination last taken with result</label>
                            <input class="form-control" name='last_result' value='<?php echo $data['last_result']; ?>'>
                        </div>

                        <div class="form-group">
                            <label>Whether failed, if so once/ twice in the higher class</label>
                            <input class="form-control" name='higher_class' value='<?php echo $data['higher_class']; ?>'>
                        </div>

                        <div class="form-group">
                            <label>Subject Studies</label>
                            <input class="form-control" name='subject_studies' value='<?php echo $data['subject_studies']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Whether qualified for promotion to the heigher class</label>
                            <input class="form-control" name='promotion_higher_class' value='<?php echo $data['promotion_higher_class']; ?>'>
                        </div>



                        <div class="form-group">
                            <label>Month upto which the (student has paid) school dues paid</label>
                            <input class="form-control" name='dues_paid' value='<?php echo $data['dues_paid']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Any fee consession availed of : if so, the nature of such consession</label>
                            <input class="form-control" name='consession' value='<?php echo $data['consession']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Total number of working days</label>
                            <input class="form-control" name='working_day' value='<?php echo $data['working_day']; ?>'>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group">
                            <label>Total number of working day present</label>
                            <input class="form-control" name='total_present' value='<?php echo $data['total_present']; ?>'>
                        </div>

                        <div class="form-group">
                            <label>Whether NCC Cadet/ Boy Scout/ Girl Guide (Details my be Given)</label>
                            <input class="form-control" name='ncc' value='<?php echo $data['ncc']; ?>'>
                        </div>


                        <div class="form-group">
                            <label>Game Played or Extra Curicular Activities in which the student usually took part (mention achievment level therein) </label>
                            <input class="form-control" name='game' value='<?php echo $data['game']; ?>'>
                        </div>

                        <div class="form-group">
                            <label>General Conduct</label>
                            <input class="form-control" name='conduct' value='<?php echo $data['conduct']; ?>'>
                        </div>
                        <div class="form-group">
                            <label>Date of application for certificate</label>
                            <input class="form-control" name='doa_certificate' type='date' value='<?php echo date('Y-m-d', strtotime($data['doa_certificate'])); ?>'>
                        </div>

                        <div class="form-group">
                            <label>Date of issue of certificate</label>
                            <input class="form-control" name='doi_certificate' type='date' value='<?php echo date('Y-m-d', strtotime($data['doi_certificate'])); ?>'>
                        </div>

                        <div class="form-group">
                            <label>Reason of leaving school</label>
                            <input class="form-control" name='reason_leaving' value='<?php echo $data['reason_leaving']; ?>'>
                        </div>

                        <div class="form-group">
                            <label>Any other remarks</label>
                            <input class="form-control" name='other_remarks' value='<?php echo $data['other_remarks']; ?>'>
                        </div>


                        </form>
                        <input id="update_btn" class="btn btn-primary" value=' Generate & Print '>

                    </div>

                    <!-- /.row -->

                </div>
            </div>
        </div>
</div>

<?php require_once('required/footer.php'); ?>