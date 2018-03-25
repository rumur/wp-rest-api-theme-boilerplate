<?php
// Make sure this file is called by wp.
defined( 'ABSPATH' ) || die();

// We need to run only in admin area.
if (is_admin()) {
  // Include the whole `admin` directory.
  dir_loader( __DIR__, __FILE__ );
}
