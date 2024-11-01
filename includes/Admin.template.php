<?php
namespace WCGQL\Views;
function notificationsForm() {
    include plugin_dir_path( __DIR__ ) . 'assets/partials/notificationsForm.php';
}

function adminPanel() {
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }

  $default_tab = null;
  $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
  include plugin_dir_path( __DIR__ ) . 'assets/admin.php';
}
?>
