<?php
  $appSecretsNonce = wp_create_nonce('app_secrets_form_nonce');
  $appId = get_option('shopz-app-id');
  $clientId = get_option('shopz-client-id');
  $clientSecret = get_option('shopz-client-secret');
?>
<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <table>
        <tr valign="top">
            <input type="hidden" name="action" value="app_secrets">
            <input type="hidden" name="app_secrets_nonce" value="<?php echo $appSecretsNonce; ?>" />
            <th colspan="2" scope="row">
                <h3>Shopz App secrets</h3>
            </th>
        </tr>
        <tr>
            <td>Shopz App ID</td>
            <td>
                <input type="text" name="shopz-app-id" placeholder="Enter App ID"
                    value="<?php echo $appId; ?>">
            </td>
        </tr>
        <tr>
            <td>Shopz Client ID</td>
            <td>
                <input type="text" name="shopz-client-id" placeholder="Enter Client ID"
                    value="<?php echo $clientId; ?>">
            </td>
        </tr>
        <tr>
            <td>Shopz Client Secret</td>
            <td>
                <input type="text" name="shopz-client-secret" placeholder="Enter Client Secret"
                    value="<?php echo $clientSecret; ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_app_secrets" value="Save App Secrets" />
            </td>
        </tr>
    </table>
</form>