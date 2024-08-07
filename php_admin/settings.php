<?php
	global $wpdb;
  $sm_utilities_table = $wpdb->prefix . "sm_utilities";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['sm_utilities_nonce_field']) || !wp_verify_nonce($_POST['sm_utilities_nonce_field'], 'sm_utilities_nonce')) {
      die('Sicherheitsprüfung fehlgeschlagen.');
    }

    $key_activate = sanitize_text_field($_POST["key_activate"]);
    $key_deactivate = sanitize_text_field($_POST["key_deactivate"]);
  }

  if (!empty($key_activate)) {
    $data = array("status" => "1");
    $where = array("name" => $key_activate);

    $update_data = $wpdb->update($sm_utilities_table, $data, $where);

    echo "<meta http-equiv='refresh' content='0'>";
    exit;
  }

  if (!empty($key_deactivate)) {
    $data = array("status" => "0");
    $where = array("name" => $key_deactivate);

    $wpdb->update($sm_utilities_table, $data, $where);

    echo "<meta http-equiv='refresh' content='0'>";
    exit;
  }

  $functions = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix ."sm_utilities");

  $bereiche = [
    ["Allgemein", "Allgemein"],
    ["Admin Menü", "Menü"],
    ["Top Menü", "Top"]
  ];
?>

<h1>Utilities</h1>
<hr>
<form action="#" method="post">

<?php
  wp_nonce_field('sm_utilities_nonce', 'sm_utilities_nonce_field');

  foreach ($bereiche as $bereich) {
    echo '
      <h2>'.esc_html($bereich[0]).'</h2>
        <table class="bknd_tabell">
          <tbody>';

    foreach ($functions as $function) {
      if ($function->bereich == $bereich[1]) {
        echo '
          <tr>
            <td><button class="status_button" id="post-query-submit" class="button" type="submit" title="'.($function->status  == 1 ? "Deaktivieren" : "Aktivieren").'" name="key_'.($function->status  == 1 ? "deactivate" : "activate").'" value="'.esc_html($function->name).'">'.($function->status  == 1 ? "&#128994" : "&#128308").'</button></td>
            <td>'. esc_html($function->name) .'</td>
            <td>'. esc_html($function->beschreibung) .'</td>
          </tr>';
      }
    }

    echo '
        </tbody>
      </table>';
  }
?>  

</form>
<hr>
<p>Copyright by <a href="https://stillermedia.de">stiller media</a> &copy; 2024 | Version <?php echo $GLOBALS["Version"] ?></p>