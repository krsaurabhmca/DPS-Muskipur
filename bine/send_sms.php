<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Send SMS</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#transport">Extra</a></li>
            <li class="breadcrumb-item active">Send SMS</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Type SMS and Send (English & Hindi) </h3>

                <div class="box-tools pull-right">
                    <span id='msg_count' class='badge badge-success p-1'></span>
                    <a href='https://chrome.google.com/webstore/detail/google-input-tools/mclkkofklkfljcocdinagocijmpgbhab?hl=en-US&utm_source=chrome-ntp-launcher' target='_blank' class='badge badge-danger p-2'> <i class='fa fa-language'></i> Click to use Google Hindi Input </a>

                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class='col-lg-4'>
                        <form action='#' method='post'>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Select Class </label>
                                    <select class="form-control" name='student_class' id='msg_no' required>
                                        <?php dropdown($class_list); ?>
                                        <option value='ALL_STUDENT'>All Student</option>
                                        <option value='ALL_STAFF'>All Staff</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Select Section </label>
                                    <select class="form-control" name='student_section' id='student_section'>
                                        <?php dropdown($section_list); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Select Template </label>

                                <?php $list = get_all('sms_template')['data']; ?>
                                <select class="form-control" id='template_id' required>
                                    <option value=''></option>
                                    <?php
                                    foreach ($list as $single) {
                                        echo "<option data-sms='" . $single['sms'] . "' data-tid='" . $single['template_id'] . "'  value='" . $single['id'] . "' >" . $single['template_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type='hidden' name='sms_id' id='sms_id' class='form-control'>
                            <div class="form-group">
                                <label>Message (160 charecter Per SMS)</label>
                                <textarea class="form-control msg_box" rows="6" name='message' required id='msg_text'></textarea>
                            </div>
                            <div class="checkbox text-sm">
                                <input type="checkbox" id="unicode1" name='ContentType' value='Unicode'>
                                <label for="unicode1">Must Be Checked to send in Hindi </label>
                            </div>
                        </form>

                        <div class="form-group">
                            <button class="btn btn-sm btn-primary " name='group_sms' id='group_sms'> SEND SMS TO ALL </button>
                        </div>

                    </div>

                    <div class="col-lg-8">

                        <form action='#' method='post' id='sms_frm'>
                            <div class="form-group">
                                <label>Enter Mobile Nos. </label>
                                <textarea class="form-control" rows="2" id='mobile2'><?php if (isset($_REQUEST['mobiles'])) {
                                                                                            echo $_REQUEST['mobiles'];
                                                                                        } ?></textarea>
                                <p>Use Enter, Comma or space between two number </p>
                            </div>
                            <div class="form-group">
                                <label>Select Template </label>

                                <?php $list = get_all('sms_template')['data']; ?>
                                <select class="form-control" id='template_id2' required>
                                    <option value=''></option>
                                    <?php
                                    foreach ($list as $single) {
                                        echo "<option data-sms='" . $single['sms'] . "' data-tid='" . $single['template_id'] . "'  value='" . $single['id'] . "' >" . $single['template_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type='hidden' id='sms_id2' class='form-control'>
                            <div class="form-group">
                                <label>Message (160 character Per SMS) </label>
                                <textarea class="form-control msg_box" rows="4" id='msg_text2' required></textarea>
                            </div>
                            <div class="checkbox text-sm">
                                <input type="checkbox" id="unicode2" name='ContentType' value='unicode'>
                                <label for="unicode2">Must Be Checked to send in Hindi </label>
                            </div>
                        </form>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary " id='send_sms'> SEND SMS TO NUMBER </button>
                        </div>


                    </div>
                </div>
            </div>
    </section>
</div>
<?php require_once('required/footer2.php'); ?>

<script>
    $(document).ready(function() {
        $("#template_id").on('change', function() {
            var sms = $(this).find(':selected').data('sms');
            var id = $(this).find(':selected').val();
            $("#msg_text").text(sms);
            $("#sms_id").val(id);
        });


        $("#template_id2").on('change', function() {
            var sms = $(this).find(':selected').data('sms');
            var id = $(this).find(':selected').val();
            $("#msg_text2").text(sms);
            $("#sms_id2").val(id);
        });
    });
</script>