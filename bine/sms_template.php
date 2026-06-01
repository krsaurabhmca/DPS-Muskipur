<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>SMS Template</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#transport">Extra</a></li>
      <li class="breadcrumb-item active">SMS Template</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Basic Forms -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Add SMS Template (English & Hindi) </h3>

        <div class="box-tools pull-right">
          <span id='msg_count' class='badge badge-success p-1'></span>
          <a href='https://chrome.google.com/webstore/detail/google-input-tools/mclkkofklkfljcocdinagocijmpgbhab?hl=en-US&utm_source=chrome-ntp-launcher' target='_blank' class='badge badge-danger p-2'> <i class='fa fa-language'></i> Click to use Google Hindi Input </a>

        </div>
      </div>
      <!-- /.box-header -->

      <div class="box-body">
        <div class='row'>
          <div class='col-lg-3'>
            <form action='add_template' method='post' id='insert_frm'>

              <div class="form-group">
                <label>Sender ID </label>
                <input type='text' name='sender_id' class='form-control' maxlength='6' value='<?php echo $sender_id; ?>'>

              </div>

              <div class="form-group ">
                <label>Template Name </label>
                <input type='text' name='template_name' class='form-control' value='<?php echo $template_name; ?>'>
              </div>

              <div class="form-group ">
                <label>Template ID </label>
                <input type='number' name='template_id' class='form-control' value='<?php echo $template_id; ?>'>
              </div>
              
               <div class="form-group ">
                <label>Content Type</label>
                <select name='content_type' class='form-control'>
                    <?php dropdown_with_key($sms_content_type_list, $content_type); ?> 
                </select>
              </div>

              <div class="form-group">
                <label>Template</label>
                <textarea class="form-control msg_box" rows="4" name='sms' required><?php echo $sms; ?></textarea>
              </div>

            </form>

            <div class="form-group">
              <button class="btn btn-sm btn-primary " name='group_sms' id='insert_btn'> SAVE TEMPLATE </button>
            </div>

          </div>

          <div class="col-lg-9">
            <div class="table-responsive">

              <table id="example1" class="table table-striped table-bordered table-hover" rules='all'>
                <thead>
                  <tr>
                    <th> SENDER ID</th>
                    <th> TEMPLATE NAME</th>
                    <th> TEMPLATE ID</th>
                    <th> TEMPLATE </th>
                    <th> Action </th>
                  </tr>
                </thead>
                <tbody>
                  <?php $data  = get_all('sms_template')['data'];

                  foreach ($data as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['sender_id'] ."<br>". $row['content_type']. "</td>";
                    echo "<td>" . $row['template_name'] . "</td>";
                    echo "<td>" . $row['template_id'] . "</td>";
                    echo "<td>" . $row['sms'] . "</td>";
                  ?>
                    <td> <span class='delete_btn fa fa-trash text-orange' data-table='sms_template' data-id='<?php echo $row['id']; ?>' data-pkey='id'></span>
                    </td>
                  <?php
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
  </section>
</div>
<?php require_once('required/footer2.php'); ?>