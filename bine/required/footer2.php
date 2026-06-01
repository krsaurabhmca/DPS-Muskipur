<footer class="main-footer">
  <div class="pull-right d-none d-sm-inline-block">
    <b>Bine </b> 2.0
  </div>Copyright &copy; 2020-<?php echo date("Y"); ?> | Planted By<a href="https://offerplant.com/"> OfferPlant </a>. All Rights Reserved.
</footer>

<!-- =========== View Data IN modal ========= -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id='view_data'>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalCenterTitle"></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>


<!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="assets/vendor_components/jquery-3.3.1/jquery-3.3.1.js"></script>
<script src="js/jquery.table2excel.min.js"></script>

<!-- popper -->
<script src="assets/vendor_components/popper/dist/popper.min.js"></script>

<!-- Bootstrap 4.1.3-->
<script src="assets/vendor_components/bootstrap/js/bootstrap.min.js"></script>


<!-- SlimScroll -->
<script src="assets/vendor_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<!-- FastClick -->
<script src="assets/vendor_components/fastclick/lib/fastclick.js"></script>

<!-- Minimal-art Admin App -->
<script src="js/template.js"></script>

<!-- This is data table -->
<script src="assets/vendor_plugins/DataTables-1.10.15/media/js/jquery.dataTables.min.js"></script>

<!-- start - This is for export functionality only -->
<script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/dataTables.buttons.min.js"></script>
<script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/buttons.flash.min.js"></script>
<script src="assets/vendor_plugins/DataTables-1.10.15/ex-js/jszip.min.js"></script>
<script src="assets/vendor_plugins/DataTables-1.10.15/ex-js/pdfmake.min.js"></script>
<script src="assets/vendor_plugins/DataTables-1.10.15/ex-js/vfs_fonts.js"></script>
<script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/buttons.html5.min.js"></script>
<script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/buttons.print.min.js"></script>
<!-- end - This is for export functionality only -->
<!-- Minimal-art Admin for Data Table -->
<script src="js/pages/data-table.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/bootbox.all.js"></script>
<script src="js/notify.min.js"></script>
<script type="text/javascript" src="cam/webcam.min.js"></script>
<script src="js/shortcut.js"></script>
<script src="js/op.js"></script>

<script language="JavaScript">
  if ($("#my_camera").length > 0) {

    var myEle = document.getElementById("my_camera");
    if (myEle) {
      Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
      });
      Webcam.attach('#my_camera');
    }

    function take_snapshot2() {
      // take snapshot and get image data
      Webcam.snap(function(data_uri) {
        $.ajax({
          url: "required/master_process.php?task=save_emp_photo",
          // send the base64 post parameter
          data: {
            'e_pic': data_uri
          },
          // important POST method !
          type: "post",
          success: function(data) {
            var myobj = JSON.parse(data);
            $("#result").attr('src', 'required/upload/' + myobj.id);
            $("#targetimg").val(myobj.id);
            console.log(myobj);
          },
          complete: function() {
            $("#uploadmodal").hide();
          }
        });
      });
    }

    function take_snapshot() {
      // take snapshot and get image data
      Webcam.snap(function(data_uri) {
        $.ajax({
          url: "required/master_process.php?task=save_photo",
          // send the base64 post parameter
          data: {
            'student_photo': data_uri
          },
          // important POST method !
          type: "post",
          success: function(data) {
            var myobj = JSON.parse(data);
            $("#result").attr('src', 'required/upload/' + myobj.id);
            $("#targetimg").val(myobj.id);
            console.log(myobj);
          },
          complete: function() {
            $("#uploadmodal").hide();
          }
        });
      });
    }
  }
</script>
<script>
  function get_id(ele) {
    $("#vehicle_id").val($("#assign_vehicle").attr("data-id"));
  }

  function get_id2(ele) {
    $("#vid").val($("#change_vehicle_driver").attr("data-id"));
  }
</script>

<script>   
$(document).ready(function() {
var images = document.querySelectorAll('img');
    for (var i = 0; i < images.length; i++) {
      images[i].onerror = function() {
        $(this).attr("src","<?= $base_url;?>images/no_image.jpg");
      }
    }  
});
</script>

</body>

</html>