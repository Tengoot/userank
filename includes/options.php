<?php
add_action('admin_menu', 'userank_plugin_create_menu');

function userank_plugin_create_menu() {
    // create custom plugin settings menu
    add_menu_page('Userank Settings', 'Userank Settings', 'administrator', __FILE__, 'userank_plugin_settings_page');
    //call register settings function
	add_action( 'admin_init', 'register_userank_plugin_settings');
}

function register_userank_plugin_settings() {
    register_setting('userank-plugin-settings-group', 'colors');
    register_setting('userank-plugin-settings-group', 'ranks');
}

function userank_plugin_settings_page() {
?>
    <div class="wrap">
    <h1>Userank</h1>
    <form method="post" action="options.php">
<?php settings_fields( 'userank-plugin-settings-group' ); ?>
<?php do_settings_sections( 'userank-plugin-settings-group' ); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Colors</th>
        <td><input type="text" name="colors" value="<?php echo esc_attr( get_option('colors') ); ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Ranks</th>
        <td><input type="text" name="ranks" value="<?php echo esc_attr( get_option('ranks') ); ?>" /></td>
      </tr>
    </table>
<?php submit_button(); ?>
  </form>
</div>
<?php
}
?>
