<form method="post" action="options.php">
  <table class="form-table">
    <?php settings_fields('wc_gql'); ?>
    <tr valign="top">
        <th colspan="2" scope="row">
            <h3>Select GraphQL Menu</h3>
            <p>This menu will be the default menu returned by the `menu` query.</p>
        </th>
    </tr>

    <tr valign="top">
        <th scope="row">
            <label for="wc_gql_default_menu">Default GraphQL Menu:</label>
        </th>
        <td>
            <select name="wc_gql_default_menu" id="wc_gql_default_menu">
                <?php foreach (wp_get_nav_menus() as $item): ?>
                <option value="<?=$item->name?>" <?=(get_option('wc_gql_default_menu') == $item->name)?'selected':''?>>
                    <?=$item->name?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th colspan="2" scope="row">
            <h3>Shopz.io Internal Key</h3>
            <p>For Shopz.io mobile application users.</p>
        </th>
    </tr>

    <tr <?php if (get_option('wc_gql_internal_key')) {
    echo 'class="hidden"';
} ?> valign="top">
        <th scope="row">
            <label for="shopz_username">Login:</label>
        </th>
        <td>
            <input type="text" id="shopz_username" class="regular-text" placeholder="Enter your Shopz username" />

            <input type="password" id="shopz_password" class="regular-text" placeholder="Enter your shopz password" />

            <input type="button" id="shopz_update" class="button button-primary" value="Update List" />
        </td>
    </tr>

    <tr <?php if (get_option('wc_gql_internal_key')) {
    echo 'class="hidden"';
} ?> valign="top">
        <th scope="row">
            <label for="wc_gql_internal_key">Application:</label>
        </th>
        <td>
            <input type="hidden" value="<?php echo htmlspecialchars(get_option('wc_gql_shopz_app_data')); ?>"
                name="wc_gql_shopz_app_data" id="wc_gql_shopz_app_data">
            <select name="wc_gql_internal_key" id="wc_gql_internal_key">
                <option value="<?php echo get_option('wc_gql_internal_key'); ?>" selected>Please login & Update Apps
                    first!</option>
            </select>
        </td>
    </tr>
    <tr <?php if (!get_option('wc_gql_internal_key')) {
    echo 'class="hidden"';
} ?> valign="top">
        <th scope="row">
            <?php $data = json_decode(get_option('wc_gql_shopz_app_data')) ?>
            <p>Your website already has an Internal Key for the app:
                <?php echo $data->appName ?> using the username: <?php echo $data->username ?>!
                <a href="#" id="show_button">update it ?</a>
            </p>
        </th>
    </tr>

    <?php submit_button(); ?>
</table>
</form>