<?php
  $mobileSettingsNonce = wp_create_nonce('mobile_settings_form_nonce');
  $mobileProvider = get_option('mobile_provider');
  $jawalyUsername = get_option('jawaly_username');
  $jawalyPassword = get_option('jawaly_password');
  $jawalySendername = get_option('jawaly_sendername');
?>
<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <table>
        <tr>
            <input type="hidden" name="action" value="mobile_settings">
            <input type="hidden" name="mobile_settings_nonce" value="<?php echo $mobileSettingsNonce; ?>" />
            <th colspan="2" scope="row">
                <h3>Add Mobile Provider Settings</h3>
            </th>
        </tr>
        <tr>
            <td>Mobile Provider</td>
            <td>
                <select name="providerName">
                    <option value="Jawaly"
                        <?php echo $mobileProvider == "Jawaly"?"selected='selected'":"";?>>
                        Jawaly</option>
                    <option value="Mobily"
                        <?php echo $mobileProvider == "Mobily"?"selected='selected'":"";?>>
                        Mobily</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Username</td>
            <td>
                <input type="text" name="jawaly_username" placeholder="Enter Username"
                    value="<?php echo $jawalyUsername; ?>">
            </td>
        </tr>
        <tr>
            <td>Password</td>
            <td>
                <input type="password" name="jawaly_password" placeholder="Enter Password"
                    value="<?php echo $jawalyPassword; ?>">
            </td>
        </tr>
        <tr>
            <td>Sender Username</td>
            <td>
                <input type="text" name="jawaly_sendername" placeholder="Enter Sender Name"
                    value="<?php echo $jawalySendername; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_mobile" value="Save Settings" />
            </td>
        </tr>
    </table>
</form>