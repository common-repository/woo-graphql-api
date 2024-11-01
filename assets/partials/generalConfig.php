<?php
  $misc_settings_nonce = wp_create_nonce('misc_settings_form_nonce');
  $migrate_database_changes_nonce = wp_create_nonce('migrate_database_changes_nonce');
  $isDBUpToDate = get_option('wc_gql_db_version');
?> 
<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <table>
        <tr valign="top">
            <input type="hidden" name="action" value="misc_settings">
            <input type="hidden" name="misc_settings_nonce" value="<?php echo $misc_settings_nonce; ?>" />
            <th colspan="2" scope="row">
                <h3>Misc Configs</h3>
            </th>
        </tr>
        <tr>
            <td>Contact us form email subject</td>
            <td>
                <input type="text" name="contactUsSubject" placeholder="Enter Subject"
                    value="<?php echo get_option('contactUsSubject'); ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_misc_config" value="Save Settings" />
            </td>
        </tr>
        <?php if (!$isDBUpToDate) { ?>
        <tr valign="top">
            <input type="hidden" name="action" value="migrate_database_changes">
            <input type="hidden" name="migrate_database_changes_nonce"
                value="<?php echo $migrate_database_changes_nonce; ?>" />
            <th colspan="2" scope="row">
                <h3>Changes Management</h3>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="migrate_database_changes" value="Sync Changes" />
            </td>
        </tr>
        <?php } ?>
    </table>
</form>