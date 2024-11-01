<?php

namespace WCGQL;

use WCGQL\Helpers\ApiAuth;

require_once __DIR__ . '/Admin.template.php';

function active_plugin()
{
    // Check dependencies before activation.
    $dependencies = array(
        array(
            'name' => 'WooCommerce',
            'path' => 'woocommerce/woocommerce.php',
            'url' => 'https://wordpress.org/plugins/woocommerce/'
        ),
    );

    $errors = '';
    foreach ($dependencies as $dep) {
        if (!\is_plugin_active($dep['path'])) {
            $errors .= "<li><a href='{$dep['url']}'>{$dep['name']}</a></li>";
        }
    }

    if (!empty ($errors)) {
        \deactivate_plugins('wc-graphql/wc-graphql.php');
        $error = (
            'WooCommerce GraphQL depends on the following plugins:<br /> <ul>'
            . $errors
            . '</ul>'
        );
        die ($error);
    }

    // add options
    update_option('wc_gql_default_menu', '');
    update_option('wc_gql_internal_key', '');
    update_option('wc_gql_shopz_app_data', '');
    if (!get_option('wc_gql_salt')) {
        update_option('wc_gql_salt', substr(md5(mt_rand()), 0, 7)); // generate some random characters
    }
}

function register_options()
{
    register_setting('wc_gql', 'wc_gql_default_menu');
    register_setting('wc_gql', 'wc_gql_internal_key');
    register_setting('wc_gql', 'wc_gql_shopz_app_data');
}

function register_options_page()
{
    $page = add_options_page(
        'GraphQL API Plugin Settings',
        'GraphQL API',
        'manage_options',
        'wc-graphql',
        '\WCGQL\Views\adminPanel'
    );

    add_action('load-' . $page, '\WCGQL\register_settings_assets');
}

function register_dashboard_page()
{
    $page = add_menu_page(
        'Mobile Application Dashboard',
        'Mobile Application',
        'manage_options',
        'mobile-dashboard',
        '\WCGQL\Views\notificationsForm'
    );

    add_action('load-' . $page, '\WCGQL\register_dashboard_assets');
}

function register_settings_assets($page)
{
    // Get users' apps, select app and update appToken
    wp_enqueue_script('updateAppToken', plugins_url('../assets/js/updateAppToken.js', __FILE__), array('jquery'), null, true);
}

function register_dashboard_assets($page)
{
    wp_enqueue_style('bootstrap-style', plugins_url('../assets/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('dashboardTemplate', plugins_url('../assets/js/dashboardTemplate.js', __FILE__), array('jquery'), null, true);
}

function saveMobileSetting()
{
    if (isset($_POST['mobile_settings_nonce']) && wp_verify_nonce($_POST['mobile_settings_nonce'], 'mobile_settings_form_nonce')) {
        // sanitize the input
        $mobile_provider = sanitize_text_field($_POST['providerName']);
        $mobile_username = sanitize_text_field($_POST['jawaly_username']);
        $mobile_password = sanitize_text_field($_POST['jawaly_password']);
        $mobile_sendername = sanitize_text_field($_POST['jawaly_sendername']);

        // do the processing
        update_option('mobile_provider', $mobile_provider);
        update_option('jawaly_username', $mobile_username);
        update_option('jawaly_password', $mobile_password);
        update_option('jawaly_sendername', $mobile_sendername);
        update_option('message_template_otp', 'أدخل الرمز التالي لإتمام عملية تسجيل حسابك:
    SHOPZOTPCODE');
        update_option('message_template_otp-ar', 'أدخل الرمز التالي لإتمام عملية تسجيل حسابك:
    SHOPZOTPCODE');
        update_option('message_template_otp-en', 'Please enter the following code to complete your registration:      :
    SHOPZOTPCODE');
        update_option('message_template_forgot', 'تغيير كلمة المرور عبر الرابط التالي
    SHOPZFORGOT');
        update_option('message_template_forgot-ar', 'تغيير كلمة المرور عبر الرابط التالي
    SHOPZFORGOT');
        update_option('message_template_forgot-en', 'To reset the password please follow the link:
    SHOPZFORGOT');

        // redirect the user to the appropriate page
        exit(wp_redirect(admin_url('options-general.php?page=wc-graphql')));
    } else {
        wp_die(__('Invalid nonce specified'));
    }
}

function saveMiscSettings()
{
    if (isset($_POST['misc_settings_nonce']) && wp_verify_nonce($_POST['misc_settings_nonce'], 'misc_settings_form_nonce')) {
        $contactUsSubject = sanitize_text_field($_POST['contactUsSubject']);

        update_option('contactUsSubject', $contactUsSubject);

        exit(wp_redirect(admin_url('options-general.php?page=wc-graphql')));
    } else {
        wp_die(__('Invalid nonce specified'));
    }
}

function migrateChanges( $upgrader_object, $options ) {
  global $wpdb;
  $our_plugin = plugin_basename( 'woo-graphql-api/wc-graphql.php' );

  if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
      foreach ($options['plugins'] as $plugin) {
          if ($plugin == $our_plugin) {
              $migrationsFolder = __DIR__.'/migrations';
              $files = preg_grep('~\.(sql)$~', scandir($migrationsFolder));
              foreach ($files as $file) {
                  $parts = explode('_', $file);
                  if (!WC_GQL_DB_VERSION || WC_GQL_DB_VERSION < $parts[1]) {
                      $wpdb->get_results(file_get_contents($migrationsFolder.'/'.$file));
                      update_option('wc_gql_db_version', $parts[1]);
                  }
              }
          }
      }
  }
}

function addCountriesStates( $states ) {
  $states['SA'] = array(
    '01' => __('Riyadh','porto'),
    '02' => __('Makkah','porto'),
    '03' => __('Al-Madinah','porto'),
    '04' => __('Al-Damam','porto'),
    '05' => __('Al-Qassim','porto'),
    '06' => __('Hail','porto'),
    '07' => __('Tabuk','porto'),
    '08' => __('Northern Borders','porto'),
    '09' => __('Jizan','porto'),
    '10' => __('Najran','porto'),
    '11' => __('Al-Bahah','porto'),
    '12' => __('Al-Jawf','porto'),
    '14' => __('Abha','porto'),
  );

  $states['KW'] = array(
    'AH' => __('Al-Ahmadi','porto'),
    'FA' => __('Al-Farwaniyah','porto'),
    'JA' => __('Al-Jahrah','porto'),
    'KU' => __('Al-Kuwait','porto'),
    'MU' => __('Mubarak al-Kabir','porto'),
    'HA' => __('Hawalli','porto'),
  );

  $states['AE'] = array(
    'UQ' => __('Umm Al-Quwain','porto'),
    'SH' => __('Al-Sharjah','porto'),
    'RK' => __('Ras Al-Khaimah','porto'),
    'FU' => __('Al-Fujairah','porto'),
    'DU' => __('Dubai','porto'),
    'AD' => __('Abu Dhabi','porto'),
    'AJ' => __('Ajman','porto'),
    'AL' => __('Al-Ain','porto'),
  );
  
  return $states;
}

function migrateChangesManually() {
    global $wpdb;
    $migrationsFolder = __DIR__.'/migrations';
    $files = preg_grep('~\.(sql)$~', scandir($migrationsFolder));
    foreach ($files as $file) {
        $parts = explode('_', $file);
        if (!WC_GQL_DB_VERSION || WC_GQL_DB_VERSION < $parts[1]) {
            $wpdb->get_results(file_get_contents($migrationsFolder.'/'.$file));
            update_option('wc_gql_db_version', $parts[1]);
        }
    }
    exit(wp_redirect(admin_url('options-general.php?page=wc-graphql')));
}  

function saveAppSecrets()
{
    if (isset($_POST['app_secrets_nonce']) && wp_verify_nonce($_POST['app_secrets_nonce'], 'app_secrets_form_nonce')) {
        $shopzAppId = sanitize_text_field($_POST['shopz-app-id']);
        $shopzClientId = sanitize_text_field($_POST['shopz-client-id']);
        $shopzClientSecret = sanitize_text_field($_POST['shopz-client-secret']);

        update_option('shopz-app-id', $shopzAppId);
        update_option('shopz-client-id', $shopzClientId);
        update_option('shopz-client-secret', $shopzClientSecret);

        exit(wp_redirect(admin_url('options-general.php?page=wc-graphql')));
    } else {
        wp_die(__('Invalid nonce specified'));
    }
}

function sendNotification()
{
    if (isset($_POST['send_notification_nonce']) && wp_verify_nonce($_POST['send_notification_nonce'], 'send_notification_form_nonce')) {
        $title = sanitize_text_field($_POST['title']);
        $message = sanitize_text_field($_POST['message']);

        $body = [
            'title' => $title,
            'message' => $message
        ];

        $headers = [
            'X-Parse-Application-Id' => 'EpxJ14t7s9aSJIturx1klEIz3H17wk7h',
            'X-Shopz-Client-Id' => get_option('shopz-client-id'),
            'X-Shopz-Client-Secret' => get_option('shopz-client-secret')
        ];

        $args = [
            'body' => $body,
            'headers' => $headers
        ];

        // TODO: Bring URL from env
        $response = wp_remote_post( 'https://shopz-parse.dokku.shopz.io/parse/functions/createNotification', $args );
        if($response['response']['code'] == 200) {
            $result = "success";
        } else {
            $result = "fail";
        }

        exit(wp_redirect(admin_url("admin.php?page=mobile-dashboard&result={$result}")));
    } else {
        wp_die(__('Invalid nonce specified'));
    }
}

function notificationSuccessMessage()
{
    if (isset($_GET['page']) && $_GET['page'] === 'mobile-dashboard' 
    && isset($_GET['result']) && $_GET['result'] === 'success'  ) {
        echo "
            <div class='updated notice is-dismissible'>
                <p> Notification sent successfully </p>
            </div>
        ";
    }
}

function notificationErrorMessage()
{
    if (isset($_GET['page']) && $_GET['page'] === 'mobile-dashboard'
    && isset($_GET['result']) && $_GET['result'] === 'fail') {
        echo "
            <div class='error notice is-dismissible'>
                <p>An error occurred, notification not sent </p>
            </div>
        ";
    }
}

function extra_user_profile_fields($user) { ?>
    <h3><?php _e("Extra profile information", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="mobile_username"><?php _e("Mobile Username"); ?></label></th>
        <td>
            <input type="text" name="mobile_username" id="mobile_username" value="<?php echo esc_attr(get_the_author_meta('mobile_username', $user->ID)); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter mobile number."); ?></span>
        </td>
    </tr>
    </table>
<?php }

function save_extra_user_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'mobile_username', sanitize_text_field($_POST['mobile_username']));
}


function generateSecrets()
{
    try {
        $data = json_decode(file_get_contents('php://input'), true);
    
        $apiAuth = new ApiAuth();
        $apiKeyData = $apiAuth->generateSecrets($data);
        
        wp_send_json($apiKeyData, 200);
    } catch (\Exception $exception) {
        wp_send_json_error($exception->getMessage(), $exception->getCode());
    }
}
