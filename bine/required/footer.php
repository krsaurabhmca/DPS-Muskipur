 <footer class="main-footer">
   <div class="pull-right d-none d-sm-inline-block">
     <b>Bine </b> 2.0
   </div>Copyright &copy; 2020 | Planted By<a href="https://offerplant.com/"> OfferPlant </a>. All Rights Reserved.
 </footer>

 <!-- Modal -->
 <div class="modal fade" id="about_app" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
       <!--<div class="modal-header">-->
       <!--  <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $app_name; ?></h5>-->
       <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
       <!--    <span aria-hidden="true">&times;</span>-->
       <!--  </button>-->
       <!--</div>-->
       <div class="modal-body text-center">
         <img src='images/logo.png' height='100px' style='border-radius:50%;'>
         <h2> <?php echo  $app_name; ?> </h2>
         <p class='badge badge-warning'> A Digital Backbone of School </p>

         <h4> <?php echo  $dev_company ?> </h4>
         <hr>

         <p>
           <i> Any Query Or Suggestion </i> <br>
           <?php echo  "<i class='fa fa-globe'></i> <a href='$dev_url'>$dev_by</a> | <i class ='fa fa-mobile'></i>  <a href='tel:$dev_contact'>$dev_contact | <i class ='fa fa-envelope'></i> <a href='mailto:$dev_email'>$dev_email</a>"; ?>
         </p>

       </div>

     </div>
   </div>
 </div>

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

 <!-- Control Sidebar -->
 <aside class="control-sidebar control-sidebar-dark">
   <!-- Create the tabs -->
   <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
     <li class="nav-item"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
     <li class="nav-item"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-cog fa-spin"></i></a></li>
   </ul>
   <!-- Tab panes -->
   <div class="tab-content">
     <!-- Home tab content -->
     <div class="tab-pane" id="control-sidebar-home-tab">
       <h3 class="control-sidebar-heading">Recent Activity</h3>
       <ul class="control-sidebar-menu">
         <li>
           <a href="javascript:void(0)">
             <i class="menu-icon fa fa-birthday-cake bg-red"></i>

             <div class="menu-info">
               <h4 class="control-sidebar-subheading">Admin Birthday</h4>

               <p>Will be July 24th</p>
             </div>
           </a>
         </li>
         <li>
           <a href="javascript:void(0)">
             <i class="menu-icon fa fa-user bg-yellow"></i>

             <div class="menu-info">
               <h4 class="control-sidebar-subheading">Jhone Updated His Profile</h4>

               <p>New Email : jhone_doe@demo.com</p>
             </div>
           </a>
         </li>
         <li>
           <a href="javascript:void(0)">
             <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

             <div class="menu-info">
               <h4 class="control-sidebar-subheading">Disha Joined Mailing List</h4>

               <p>disha@demo.com</p>
             </div>
           </a>
         </li>
         <li>
           <a href="javascript:void(0)">
             <i class="menu-icon fa fa-file-code-o bg-green"></i>

             <div class="menu-info">
               <h4 class="control-sidebar-subheading">Code Change</h4>

               <p>Execution time 15 Days</p>
             </div>
           </a>
         </li>
       </ul>
       <!-- /.control-sidebar-menu -->

       <h3 class="control-sidebar-heading">Tasks Progress</h3>
       <ul class="control-sidebar-menu">
         <li>
           <a href="javascript:void(0)">
             <h4 class="control-sidebar-subheading">
               Web Design
               <span class="label label-danger pull-right">40%</span>
             </h4>

             <div class="progress progress-xxs">
               <div class="progress-bar progress-bar-danger" style="width: 40%"></div>
             </div>
           </a>
         </li>
         <li>
           <a href="javascript:void(0)">
             <h4 class="control-sidebar-subheading">
               Update Data
               <span class="label label-success pull-right">75%</span>
             </h4>

             <div class="progress progress-xxs">
               <div class="progress-bar progress-bar-success" style="width: 75%"></div>
             </div>
           </a>
         </li>
         <li>
           <a href="javascript:void(0)">
             <h4 class="control-sidebar-subheading">
               Order Process
               <span class="label label-warning pull-right">89%</span>
             </h4>

             <div class="progress progress-xxs">
               <div class="progress-bar progress-bar-warning" style="width: 89%"></div>
             </div>
           </a>
         </li>
         <li>
           <a href="javascript:void(0)">
             <h4 class="control-sidebar-subheading">
               Development
               <span class="label label-primary pull-right">72%</span>
             </h4>

             <div class="progress progress-xxs">
               <div class="progress-bar progress-bar-primary" style="width: 72%"></div>
             </div>
           </a>
         </li>
       </ul>
       <!-- /.control-sidebar-menu -->

     </div>
     <!-- /.tab-pane -->
     <!-- Stats tab content -->
     <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
     <!-- /.tab-pane -->
     <!-- Settings tab content -->
     <div class="tab-pane" id="control-sidebar-settings-tab">
       <form method="post">
         <h3 class="control-sidebar-heading">General Settings</h3>

         <div class="form-group">
           <input type="checkbox" id="report_panel" class="chk-col-grey">
           <label for="report_panel" class="control-sidebar-subheading ">Report panel usage</label>

           <p>
             general settings information
           </p>
         </div>
         <!-- /.form-group -->

         <div class="form-group">
           <input type="checkbox" id="allow_mail" class="chk-col-grey">
           <label for="allow_mail" class="control-sidebar-subheading ">Mail redirect</label>

           <p>
             Other sets of options are available
           </p>
         </div>
         <!-- /.form-group -->

         <div class="form-group">
           <input type="checkbox" id="expose_author" class="chk-col-grey">
           <label for="expose_author" class="control-sidebar-subheading ">Expose author name</label>

           <p>
             Allow the user to show his name in blog posts
           </p>
         </div>
         <!-- /.form-group -->

         <h3 class="control-sidebar-heading">Chat Settings</h3>

         <div class="form-group">
           <input type="checkbox" id="show_me_online" class="chk-col-grey">
           <label for="show_me_online" class="control-sidebar-subheading ">Show me as online</label>
         </div>
         <!-- /.form-group -->

         <div class="form-group">
           <input type="checkbox" id="off_notifications" class="chk-col-grey">
           <label for="off_notifications" class="control-sidebar-subheading ">Turn off notifications</label>
         </div>
         <!-- /.form-group -->

         <div class="form-group">
           <label class="control-sidebar-subheading">
             <a href="javascript:void(0)" class="text-red margin-r-5"><i class="fa fa-trash-o"></i></a>
             Delete chat history
           </label>
         </div>
         <!-- /.form-group -->
       </form>
     </div>
     <!-- /.tab-pane -->
   </div>
 </aside>
 <!-- /.control-sidebar -->

 <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
 <div class="control-sidebar-bg"></div>

 </div>
 <!-- ./wrapper -->

 <!-- jQuery 3 -->
 <script src="assets/vendor_components/jquery-3.3.1/jquery-3.3.1.js"></script>

 <!-- popper -->
 <script src="assets/vendor_components/popper/dist/popper.min.js"></script>

 <!-- Bootstrap 4.1.3-->
 <script src="assets/vendor_components/bootstrap/js/bootstrap.min.js"></script>

 <!-- ChartJS -->
 <script src="assets/vendor_components/chart-js/chart.js"></script>

 <!-- Sparkline -->
 <script src="assets/vendor_components/jquery-sparkline/dist/jquery.sparkline.js"></script>

 <!-- jvectormap -->
 <script src="assets/vendor_plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
 <script src="assets/vendor_plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

 <!-- Morris.js charts -->
 <script src="assets/vendor_components/raphael/raphael.min.js"></script>
 <script src="assets/vendor_components/morris.js/morris.min.js"></script>

 <!-- Slimscroll -->
 <script src="assets/vendor_components/jquery-slimscroll/jquery.slimscroll.js"></script>

 <!-- FastClick -->
 <script src="assets/vendor_components/fastclick/lib/fastclick.js"></script>

 <!-- Minimal-art Admin App -->
 <script src="js/template.js"></script>

 <!-- OfferPlant Support JS -->
 <script src="js/jquery.validate.min.js"></script>
 <script src="js/bootbox.all.js"></script>
 <script src="js/notify.min.js"></script>
 <script src="js/shortcut.js"></script>
 <script src="js/op.js"></script>

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