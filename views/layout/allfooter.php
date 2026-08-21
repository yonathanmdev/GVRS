    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
   
    <?php include_once 'footer.php'; ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->

<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<!-- AdminLTE for demo purposes -->
 <script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->

<!-- Page specific script -->
 <?php if (isset($is_dashboard) && $is_dashboard === true): ?>
  <!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart 
   <script src="dist/js/pages/dashboard.js"></script>  if you want uncomment this  -->
<?php endif; ?>
<?php
function myAsset($path) {
    $fullPath = __DIR__ . '/' . $path;
    $version = file_exists($fullPath) ? filemtime($fullPath) : time();
    return $path . '?v=' . $version;
}
?>
<script src="<?= myAsset('js/confirm-delete.js') ?>"></script>

<?php if (isset($is_organization_page) && $is_organization_page === true): ?>
    
    <script src="<?= myAsset('js/organizations-logic.js') ?>"></script>
    <?php endif; ?>
   <?php if (isset($is_organization_deleted_page) && $is_organization_deleted_page === true): ?>
     <script src="<?= myAsset('js/deleted-organizations.js') ?>"></script>
    <?php endif; ?>
<?php if (isset($is_archived_organizations_page) && $is_archived_organizations_page === true): ?>
    <script src="<?= myAsset('js/archived-organization.js') ?>"></script>
<?php endif; ?>
<?php if (isset($is_bureau_page) && $is_bureau_page === true): ?>
    <script src="<?= myAsset('js/bureau-logic.js') ?>"></script>
    <script src="<?= myAsset('js/delete-branch.js') ?>"></script>
    <?php endif; ?>

    <?php if (isset($is_zone_page) && $is_zone_page === true): ?>
    <script src="<?= myAsset('js/zone-logic.js') ?>"></script>
    <script src="<?= myAsset('js/delete-branch.js') ?>"></script>
    <?php endif; ?>

    <?php if (isset($is_woreda_page) && $is_woreda_page === true): ?>
    <script src="<?= myAsset('js/woreda-logic.js') ?>"></script>
    <script src="<?= myAsset('js/delete-branch.js') ?>"></script>
    <?php endif; ?>
      <?php if (isset($is_accountable_page) && $is_accountable_page === true): ?>
    <script src="<?= myAsset('js/accountable-office-logic.js') ?>"></script>
    <script src="<?= myAsset('js/delete-branch.js') ?>"></script>
    <?php endif; ?>


      <?php if (isset($is_vehicle_page) && $is_vehicle_page === true): ?>
    <script src="<?= myAsset('js/vehicle-form.js') ?>"></script>
     <script src="<?= myAsset('js/vehicle-type-loader.js') ?>"></script>
    <script src="<?= myAsset('js/vehicle-type-cascade.js') ?>"></script>
    <?php endif; ?>
          <?php if (isset($is_vehicles_list_page) && $is_vehicles_list_page === true): ?>
    <script src="<?= myAsset('js/delete-vehicle.js') ?>"></script>
    <script src="<?= myAsset('js/vehicle-type-loader.js') ?>"></script>
      <?php endif; ?>
  
 <?php if (isset($is_branch_deleted_page) && $is_branch_deleted_page === true): ?>
     <script src="<?= myAsset('js/deleted-branches.js') ?>"></script>
    <?php endif; ?>
    <?php if (isset($is_archived_branches_page) && $is_archived_branches_page === true): ?>
    <script src="<?= myAsset('js/archived-branches.js') ?>"></script>
<?php endif; ?>


 <?php if (isset($is_register_user_page) && $is_register_user_page === true): ?>
        <script src="<?= myAsset('js/edit-user.js') ?>"></script>
    <?php endif; ?>
      <?php if (isset($is_user_deleted_page) && $is_user_deleted_page === true): ?>
        <script src="<?= myAsset('js/deleted-users.js') ?>"></script>
    <?php endif; ?>


    
  <script nonce="<?php echo $GLOBALS['nonce']; ?>">
  $(function () {

    function resetSubmitButtons($form) {
      $form.data('submitting', false);
      $form.removeData('submitButton');
      $form.removeData('prevent-submit');

      $form.find('button[type="submit"], input[type="submit"]')
        .prop('disabled', false);
    }

    $(document).on('click', 'form button[type="submit"], form input[type="submit"]', function () {
      var $button = $(this);
      var $form = $button.closest('form');

      if ($form.data('submitting')) {
        return;
      }

      $form.data('submitButton', $button);
    });

    $(document).on('submit', 'form', function (event) {
      var $form = $(this);
      var formEl = this;

      // ❌ BLOCK global disabling when validation already failed
      if ($form.data('prevent-submit') === true) {
        event.preventDefault();
        return false;
      }

      if ($form.data('submitting')) {
        event.preventDefault();
        return false;
      }

      // ❌ Don't disable if the form itself is invalid (empty/malformed
      // required fields). checkValidity() works regardless of the
      // novalidate attribute — novalidate only stops the browser's own
      // automatic submit-blocking, it doesn't change what checkValidity()
      // reports. So this check is safe for every form on the site,
      // whether or not that form uses novalidate.
      if (typeof formEl.checkValidity === 'function' && !formEl.checkValidity()) {
        return;
      }

      $form.data('submitting', true);

      $form.find('button[type="submit"], input[type="submit"]')
        .prop('disabled', true);
    });

    $(document).on('invalid-form.validate invalid', 'form', function () {
      resetSubmitButtons($(this));
    });

  });
</script>
</body>
</html>

<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  $(function () {
    // Define translations based on the current language
    var lang = "<?php echo $currentLang ?? 'am'; ?>";
    
    var translations = {
        'am': {
            "emptyTable": $("#example1").data("empty-msg") || "ምንም መረጃ የለም።",
            "zeroRecords": "ምንም የሚዛመድ መረጃ አልተገኘም",
            "search": "ፈልግ:",
      
            "paginate": {
                "next": "ቀጣይ",
                "previous": "ቀዳሚ"
            }
        },
        'en': {
            "emptyTable": $("#example1").data("empty-msg") || "No data available in table",
            "zeroRecords": "No matching records found",
            "search": "Search:",
            "paginate": {
                "next": "Next",
                "previous": "Previous"
            }
        }
    };

    // Pick the correct language pack (default to Amharic if not found)
    var currentTranslation = translations[lang] || translations['am'];

    $("#example1").DataTable({
      "responsive": true, 
      "lengthChange": false, 
      "autoWidth": false,
      "deferRender": true,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
      "paging": false,
      "language": currentTranslation
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('#example2').DataTable({
      "deferRender": true,
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": currentTranslation // Applied here too if needed
    });
  });
</script>
<!-- 1. First: flash data -->
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
  window.__flash = {
    success: <?php echo json_encode($_SESSION['success'] ?? null); ?>,
    error:   <?php echo json_encode($_SESSION['error']   ?? null); ?>
  };
  <?php unset($_SESSION['success'], $_SESSION['error']); ?>
</script>
<script src="<?= myAsset('js/toast.js') ?>"></script>