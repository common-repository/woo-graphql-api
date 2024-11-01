<?php

namespace WCGQL\GQL;

use WCGQL\Helpers\Address;
use WCGQL\Helpers\Cart;
use WCGQL\Helpers\Coupon;
use WCGQL\Helpers\Order;
use WCGQL\Helpers\Shipping;
use WCGQL\Helpers\Payment;
use WCGQL\Helpers\Utils;
use WCGQL\Mobile\MobileManager;
use WCGQL\Translators\TranslatorsFactory;

trait MutationTypeResolver
{
    public function MutationType_addOrder($root, $args, $ctx)
    {
        $orderId = Order::create_order($args['input']);
        if ($orderId) {
            Coupon::removeCoupon();
        }
        return $orderId;
    }

    public function MutationType_editOrder($root, $args, $ctx)
    {
        // require_once __DIR__ . '/includes/Converters/OrdersConverters.php';
        // $input = $args['input'];
        // $data  = gq_convert_orderInput($input);
        // $res   = rest_put("orders/{$id}", $data);
        // return isset($res);
    }

    public function MutationType_deleteOrder($root, $args, $ctx)
    {
        // $id   = $args['order_id'];
        // $res  = rest_delete ("orders/{$id}", ['force' => true]);
        // return isset($res);
    }

    public function MutationType_confirmOrder($root, $args, $ctx)
    {
        $order_id = $args['order_id'];
        $confirmation = $args['confirmation'];
        return Order::set_confirmation($order_id, $confirmation);
    }

    public function MutationType_addItemToCart($root, $args, $ctx)
    {
        if (Cart::add_product($args['input']))
            return Cart::to_schema();
    }

    public function MutationType_addItemsToCart($root, $args, $ctx)
    {
        if (Cart::add_products($args['input']))
            return Cart::to_schema();
    }

    public function MutationType_updateCartItem($root, $args, $ctx)
    {
        return Cart::update_quantity($args['cart_id'], $args['quantity']);
    }

    public function MutationType_addCoupon($root, $args, $ctx)
    {
        $result = Coupon::addCoupon($args['code']);
        return Cart::to_schema();
    }

    public function MutationType_removeCoupon($root, $args, $ctx)
    {
        Coupon::removeCoupon();
        return Cart::to_schema();
    }

    public function MutationType_setPaymentAddress($root, $args, $ctx)
    {
        $address = Address::from_input($args['input']);
        $address_id = Address::addAddress($address);
        return Shipping::setShippingAddress($address_id, 'billing');
    }

    public function MutationType_setPaymentAddressById($root, $args, $ctx)
    {
        return Shipping::setShippingAddress($args['address_id'], 'billing');
    }

    public function MutationType_setPaymentMethod($root, $args, $ctx)
    {
        $code = $args['code'];
        return Payment::setPaymentMethod($code);
    }

    public function MutationType_setShippingAddress($root, $args, $ctx)
    {
        $address = Address::from_input($args['input']);
        $address_id = Address::addAddress($address);
        return Shipping::setShippingAddress($address_id, 'shipping');
    }

    public function MutationType_setShippingAddressById($root, $args, $ctx)
    {
        Shipping::setShippingAddress($args['address_id'], 'shipping');
        Shipping::setCurrentAddressAsUserMeta();
        return true;
    }

    public function MutationType_setShippingMethod($root, $args, $ctx)
    {
        return Shipping::setShippingMethod($args['code']);
    }

    public function MutationType_addWishlist($root, $args, $ctx)
    {
        return WC()->GQL_Wishlist->add_product($args['product_id']);
    }

    public function MutationType_deleteWishlist($root, $args, $ctx)
    {
        return WC()->GQL_Wishlist->delete_product($args['product_id']);
    }

    public function MutationType_editCustomer($root, $args, $ctx)
    {
        $input = Utils::get_prop($args, 'input');
        return WC()->GQL_User->edit_customer(
            Utils::get_prop($input, 'firstname'),
            Utils::get_prop($input, 'lastname'),
            Utils::get_prop($input, 'email'),
            Utils::get_prop($input, 'telephone')
        );
    }

    public function MutationType_editPassword($root, $args, $ctx)
    {
        return WC()->GQL_User->edit_password(
            Utils::get_prop($args, 'oldPassword'),
            Utils::get_prop($args, 'password'),
            Utils::get_prop($args, 'confirm')
        );
    }

    public function MutationType_register($root, $args, $ctx)
    {
        WC()->GQL_User->register($args['input']);
        // WC()->GQL_User->sendCustomerEmail();
        return WC()->GQL_User->login($args['input']['email'], $args['input']['password']);
    }

    public function MutationType_login($root, $args, $ctx)
    {
        return WC()->GQL_User->login($args['email'], $args['password']);
    }

    public function MutationType_loginByMobileNumber($root, $args, $ctx)
    {
        return WC()->GQL_User->loginByMobileNumber($args['mobile'], $args['password']);
    }

    public function MutationType_logout()
    {
        return WC()->GQL_User->logout();
    }

    public function MutationType_forgotten($root, $args, $ctx)
    {

        ob_start();
        require_once ABSPATH . '/wp-login.php';
        $_POST['user_login'] = $args['email'];
        $result = retrieve_password();
        ob_get_clean();

        return $result === true;
    }

    public function MutationType_contactus($root, $args, $ctx)
    {
        return Utils::mail_admin(
            Utils::get_prop($args, 'email'),
            Utils::get_prop($args, 'name'),
            Utils::get_prop($args, 'enquiry')
        );
    }

    public function MutationType_setLanguage($root, $args, $ctx)
    {
        return TranslatorsFactory::get_translator()->set_language($args['code']);
    }

    public function MutationType_setCurrency($root, $args, $ctx)
    {
        return null;
    }

    public function MutationType_emptyCart($root, $args, $ctx)
    {
        return WC()->cart->empty_cart();
    }

    public function MutationType_sendOTP($root, $args, $ctx)
    {
        $via = $args['via'] ?? 'sms';
        $purpose = $args['purpose'] ?? '';

        $to = [
            'country_code' => $args['country_code'],
            'phone_number' => $args['phone_number'],
        ];
        $options = [
            'purpose' => $purpose,
            'via' => $via
        ];

        return (new MobileManager())->sendOTP($to, $options);
    }

    public function MutationType_sendForgetPassword($root, $args, $ctx)
    {
        $via = $args['via'] ?? 'sms';
        $to = [
            'country_code' => $args['country_code'],
            'phone_number' => $args['phone_number'],
        ];
        $options = [
            'via' => $via
        ];
        return (new MobileManager())->sendForgetPassword($to, $options);
    }

    public function MutationType_verifyOTP($root, $args, $ctx)
    {
        $to = [
            'country_code' => $args['country_code'],
            'phone_number' => $args['phone_number'],
        ];
        $token = $args['token'];
        return (new MobileManager())->verifyOTP($to, $token);
    }

    public function MutationType_loginByMobileNumberOTP($root, $args, $ctx)
    {
        $response = [
            'data' => [],
            'errors' => []
        ];
        $to = [
            'country_code' => $args['country_code'],
            'phone_number' => $args['phone_number'],
        ];
        $isValid = (new MobileManager())->verifyOTP($to, $args['token']);

        if ($isValid) {
            // We need to handle if user number is invalid and has no associated User account
            $loginToken = WC()->GQL_User->loginByMobileNumberOTP($args['country_code'] . $args['phone_number']);
            if ($loginToken) {
                $response['data'][] = [
                    'code' => 'SUCCESS',
                    'title' => 'LOGINTOKEN',
                    'content' => $loginToken,
                ];
            }
        } else {
            $response['errors'][] = [
                'code' => 'INVALIDTOKEN',
                'title' => 'Invalid Token',
                'content' => 'The token is invalid!',
            ];
        }

        return $response;
    }

    public function MutationType_changeOrderStatus($root, $args, $ctx)
    {
      $order_id = $args['order_id'];
      $status = $args['status'];
      $note = $args['note'];
      $transactionID = $args['transactionID'];

      $order = new \WC_Order($order_id);
      $currentStatus = $order->get_status();
      if($currentStatus == $status) {
        return false;
      }
      
      Order::sendProperEmails($order_id, $status);

      if($transactionID && strcmp($status, "processing") == 0) {
        $order->add_order_note( "Payment done successfully, Transaction_ID: " . $transactionID );
        $order->set_transaction_id( $transactionID );
      }

      return $order->update_status($status, $note);
    }

    public function MutationType_removeCartItems($root, $args, $ctx)
    {
      $ids = $args['ids'];
      foreach($ids as $id) {
        WC()->cart->remove_cart_item($id);
      }
      return true;
    }

    public function MutationType_addWishlistItems($root, $args, $ctx)
    {
      $product_ids = $args['product_ids'];
      foreach($product_ids as $product_id) {
        WC()->GQL_Wishlist->add_product ($product_id);
      }
      return true;
    }
}
