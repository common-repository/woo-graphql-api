<?php

namespace WCGQL\Helpers;

class ApiAuth extends \WC_Auth
{
    private function validateData($data)
    {
        $params = array(
            'username',
            'password',
            'appName',
            'scope',
        );
        
        foreach ($params as $param) {
            if (empty($data[$param])) {
                throw new \Exception(sprintf(__('Missing parameter %s', 'woocommerce'), $param));
            }

            $data[$param] = wp_unslash($data[ $param ]);
        }

        if (! in_array($data['scope'], array( 'read', 'write', 'read_write' ), true)) {
            throw new \Exception(sprintf(__('Invalid scope %s', 'woocommerce'), wc_clean($data['scope'])));
        }

        return $data;
    }

    public function generateSecrets($data)
    {
        try {
            $data = $this->validateData($data);

            $username = $data['username'];
            $password = $data['password'];
            $appName = $data['appName'];
            $scope = $data['scope'];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
        
        $user = wp_authenticate($username, $password);
        if (!$user instanceof \WP_User) {
            throw new \Exception('Invalid user', 400);
        }
        
        wp_set_current_user($user->ID);

        if (!current_user_can('manage_woocommerce')) {
            throw new \Exception('Permission denied', 401);
        }

        try {
            $consumer_data = $this->create_keys($appName, $user->ID, $scope);

            return $consumer_data;
        } catch (\Exception $exception) {
            $this->maybe_delete_key($consumer_data);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
        
        return true;
    }
}
