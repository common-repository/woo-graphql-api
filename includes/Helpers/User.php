<?php

namespace WCGQL\Helpers;

class User extends \WC_Customer
{
    private static $token;

    public function __construct($data = 0, $is_session = false)
    {
        parent::__construct($data);
        if (isset($GLOBALS['gq_session_id'])) {
            self::set_token(sanitize_text_field($GLOBALS['gq_session_id']));
        }
    }

    public static function set_token($token)
    {
        self::$token = $token;
    }

    public static function login($email, $password)
    {
        $creds = array(
            'user_login' => trim(wp_unslash($email)),
            'user_password' => wp_unslash($password),
            'remember' => true,
        );
        // Check valid user.
        $user = wp_authenticate($email, $password);
        // check if the user doesn't use the same email as username.
        $user = wp_authenticate_email_password($user, $email, $password);
        if (!$user instanceof \WP_User) {
            throw new ClientException("Warning: E-Mail/Password is incorrect!");
        }

        $secret = Session::generateSecrets();

        $payload = [
            'id' => $user->ID,
            'token' => $secret[1],
            'guest' => 0,
        ];

        wp_set_current_user($user->ID);
        
        $token = Session::generateSessionToken($payload);
        self::set_token($token);
        return $token;
    }

    public static function loginByMobileNumber($phone_number, $password)
    {
        $user = self::getUserByMobile($phone_number);

        if (!$user) {
            throw new ClientException("Warning: PhoneNumber/Password is incorrect!");
        }

        $is_valid_password = wp_check_password($password, $user->data->user_pass);

        if (!$user instanceof \WP_User || !$is_valid_password) {
            throw new ClientException("Warning: PhoneNumber/Password is incorrect!");
        }

        $secret = Session::generateSecrets();
        $payload = [
            'id' => $user->ID,
            'token' => $secret[1],
            'guest' => 0,
        ];
        
        wp_set_current_user($user->ID);

        $token = Session::generateSessionToken($payload);
        self::set_token($token);
        return $token;
    }

    public static function getUserByMobile($phone_number)
    {
        if (empty($phone_number)) {
            return false;
        }
        
        $_user = get_users([
            'meta_key' => 'billing_phone',
            'meta_value' => $phone_number,
            'number' => 1,
            'count_total' => false,
        ]);

        if (count($_user) == 0) {
            return false;
        }

        return reset($_user);
    }

    public static function loginByMobileNumberOTP($phone_number)
    {
        $user = self::getUserByMobile($phone_number);

        if (!$user) {
            throw new ClientException("Warning: PhoneNumber/Verification code is incorrect!");
        }

        $secret = Session::generateSecrets();
        $payload = [
            'id' => $user->ID,
            'token' => $secret[1],
            'guest' => 0,
        ];
        
        wp_set_current_user($user->ID);
        
        $token = Session::generateSessionToken($payload);
        self::set_token($token);
        return $token;
    }

    /**
     * @throws ClientException
     */
    public static function register($input)
    {
        if (!self::validate_input($input)) {
            throw new ClientException("Warning: INVALID_REGISTERATION_DATA");
        }

        if (self::doesUserExists($input)) {
            throw new ClientException("Warning: E-Mail Address/Phone Number is already registered!");
        }

        $customer = self::createCustomer($input);
        return $customer->get_id();
    }

    public static function validate_input($input)
    {
        if (!self::isValidEmail($input['email']) ||
            !$input['agree'] ||
            ($input['password'] !== $input['confirm'])) {
            return false;
        }
        return true;
    }

    private static function isValidEmail($email)
    {
        if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    public static function doesUserExists($input): bool
    {
        $userByEmail = self::doesEmailExist($input['email']);
        $userByPhone = self::getUserByMobile($input['telephone']);
        if ((isset($userByEmail) && !empty($userByEmail))
            || (isset($userByPhone) && !empty($userByPhone))) {
            return true;
        }
        return false;
    }

    public static function doesEmailExist($email)
    {
        $user = get_user_by('email', $email);
        if ($user instanceof \WP_User) {
            return $user;
        }
        return false;
    }

    public static function createCustomer($metaData)
    {
        $customer = new \WC_Customer();
        $customer->set_username($metaData['email']);
        $customer->set_password($metaData['password']);
        $customer->set_email($metaData['email']);

        $customer->set_first_name(Utils::get_prop($metaData, 'firstname'));
        $customer->set_last_name(Utils::get_prop($metaData, 'lastname'));
        $customer->set_shipping_location(
            Utils::get_prop($metaData, 'country_id'),
            Utils::get_prop($metaData, 'zone_id'),
            Utils::get_prop($metaData, 'postcode'),
            Utils::get_prop($metaData, 'city')
        );
        $customer->set_shipping_address_1(Utils::get_prop($metaData, 'address_1'));
        $customer->set_shipping_address_2(Utils::get_prop($metaData, 'address_2'));
        $customer->set_shipping_company(Utils::get_prop($metaData, 'company'));

        $customer->set_billing_location(
            Utils::get_prop($metaData, 'country_id'),
            Utils::get_prop($metaData, 'zone_id'),
            Utils::get_prop($metaData, 'postcode'),
            Utils::get_prop($metaData, 'city')
        );
        $customer->set_billing_address_1(Utils::get_prop($metaData, 'address_1'));
        $customer->set_billing_address_2(Utils::get_prop($metaData, 'address_2'));
        $customer->set_billing_company(Utils::get_prop($metaData, 'company'));
        $customer->set_billing_phone(Utils::get_prop($metaData, 'telephone'));

        $customer->save();
        \update_user_meta($customer->get_id(), 'mobile_username', Utils::get_prop($metaData, 'telephone'));
        return $customer;
    }

    public static function getCustomerFromSessionToken()
    {
        if (empty($GLOBALS['gq_session_id'])) {
            return new User();
        }

        $data = Session::decodeSentToken(sanitize_text_field($GLOBALS['gq_session_id']));
        if (\is_null($data)) {
            return new User();
        }

        $user = get_user_by('id', $data['id']);
        if (!$user) {
            return new User();
        }
        wp_set_current_user($user->ID);
        return new User($user->ID);
    }

    public function get_language_code()
    {
        return $GLOBALS['gq_lang']?? $this->getUserMeta('gq_mob_lang')?? null; // TODO: refactor, kept like this for backward compatibility
    }

    public function getUserMeta($meta_key)
    {
        if (!\is_user_logged_in()) {
            return false;
        }

        return get_user_meta($this->get_id(), $meta_key, true);
    }

    public function set_language_code($code)
    {
        $GLOBALS['gq_lang'] = $code;
        $this->setUserMeta(array('gq_mob_lang' => $code)); // TODO: delete, kept only for backward compatibility
        return true;
    }

    public function setUserMeta($arr)
    {
        if (!\is_user_logged_in()) {
            return false;
        }

        if (!is_array($arr)) {
            return false;
        }

        foreach ($arr as $key => $value) {
            \update_user_meta($this->get_id(), $key, $value);
        }
        return true;
    }

    public function get_token()
    {
        if (!is_user_logged_in()) {
            $id = uniqid('gq_sess');
            $secret = Session::generateSecrets();
            $payload = [
                'id' => $id,
                'token' => $secret[1],
                'guest' => 1,
            ];
            
            self::$token = Session::generateSessionToken($payload);
        }

        return self::$token;
    }

    public function edit_password($old_password = '', $password = '', $confirm = '')
    {
        if (!\is_user_logged_in()) {
            throw new \Exception("Can't edit password, user not logged in");
        }

        if (
            !\is_string($password) || !\is_string($confirm) ||
            \strlen($password) < 6 || $password != $confirm
        ) {
            throw new \Exception("Can't edit password, failed password policy");
        }

        if (!wp_authenticate($this->get_email(), $old_password) instanceof \WP_User) {
            throw new \Exception("Can't edit password, old password is incorrect");
        }

        try {
            $customer = new \WC_Customer($this->get_id());
            $customer->set_password($password);
            $customer->save();
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Can't edit password");
        }
    }

    public function edit_customer($firstname, $lastname, $email, $phone)
    {
        if (!\is_user_logged_in()) {
            throw new \Exception("Can't edit customer data, user not logged in");
        }

        if (
            !Utils::string_not_empty($firstname) ||
            !Utils::string_not_empty($lastname) ||
            !self::isValidEmail($email)
        ) {
            throw new \Exception("Can't edit customer data, invalid data");
        }

        $customer = new \WC_Customer($this->get_id());
        $customer->set_first_name($firstname);
        $customer->set_last_name($lastname);
        $customer->set_email($email);
        $customer->set_billing_phone($phone);
        $customer->save();

        if (get_user_meta($this->get_id(), 'mobile_username', true)) {
            \update_user_meta($this->get_id(), 'mobile_username', $phone);
        }

        return true;
    }

    private function validate_email($email)
    {
        if (!self::isValidEmail($email)) {
            return false;
        }

        if (!self::doesEmailExist($email)) {
            return false;
        }
        return true;
    }

    public function sendCustomerEmail($customerId)
    {
        (new \WC_Email_Customer_New_Account())->trigger($customerId);
    }

    public function logout()
    {
        wp_destroy_current_session();
        wp_clear_auth_cookie();
        wp_set_current_user(0);
        do_action('wp_logout');
        return $this->setUserMeta(array('gq_session' => '')) !== false;
    }

    public function input_to_api($input)
    {
        $output = [
            'email' => Utils::get_prop($input, 'email'),
            'first_name' => Utils::get_prop($input, 'firstname'),
            'last_name' => Utils::get_prop($input, 'lastname'),
            'billing' => [
                'first_name' => Utils::get_prop($input, 'firstname'),
                'last_name' => Utils::get_prop($input, 'lastname'),
                'company' => Utils::get_prop($input, 'company'),
                'address_1' => Utils::get_prop($input, 'address_1'),
                'address_2' => Utils::get_prop($input, 'address_2'),
                'city' => Utils::get_prop($input, 'city'),
                'country' => Utils::get_prop($input, 'country_id'),
                'email' => Utils::get_prop($input, 'email'),
                'phone' => Utils::get_prop($input, 'telephone'),
            ],
            'shipping' => [
                'first_name' => Utils::get_prop($input, 'firstname'),
                'last_name' => Utils::get_prop($input, 'lastname'),
                'company' => Utils::get_prop($input, 'company'),
                'address_1' => Utils::get_prop($input, 'address_1'),
                'address_2' => Utils::get_prop($input, 'address_2'),
                'city' => Utils::get_prop($input, 'city'),
                'country' => Utils::get_prop($input, 'country_id'),
                'email' => Utils::get_prop($input, 'email'),
                'phone' => Utils::get_prop($input, 'telephone'),
            ],
        ];
        return Utils::clean_empty($output);
    }

    public function from_api_to_schema()
    {
        return $this->to_schema();
    }

    public function to_schema($_customer = false)
    {
        if (!\is_user_logged_in()) {
            return null;
        }

        if ($_customer) {
            $customer = $_customer;
        } else {
            $customer = WC()->GQL_User;
        }

        $telephone = $customer->get_billing_phone();
        if (get_user_meta($customer->get_id(), 'mobile_username', true)) {
            $telephone = get_user_meta($customer->get_id(), 'mobile_username', true);
        }
  
        return array(
            'customer_id' => $customer->get_id(),
            'firstname' => $customer->get_first_name(),
            'lastname' => $customer->get_last_name(),
            'telephone' =>$telephone,
            'email' => $customer->get_email(),
            'fax' => '',
        );
    }
}
