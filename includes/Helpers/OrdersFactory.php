<?php

namespace WCGQL\Helpers;

class OrdersFactory
{
    /** @var int */
    private $args;

    /** @var bool */
    private $should_bypass_customer = false;

    public function __construct()
    {
        $customer_id = WC()->GQL_User->get_id();
        if (\is_numeric($customer_id)) {
            $this->args = compact('customer_id');
        }
    }

    /**
     * Change the scope to return current customer's orders.
     * true: returns all orders from the database.
     * false: returns only current customer's orders.
     * default: false
     *
     * @param bool $value
     * @return \WCGQL\Helpers\OrdersFactory
     */
    public function bypass_customer($value)
    {
        if (\is_bool($value)) {
            $this->should_bypass_customer = $value;
        }

        return $this;
    }

    /**
     * Sets current customer's id.
     * default: logged in customer id.
     *
     * @param int $customer_id
     * @return \WCGQL\Helpers\OrdersFactory
     */
    public function customer($customer_id)
    {
        if (\is_numeric($customer_id)) {
            $this->args = compact('customer_id');
        }

        return $this;
    }

    /**
     * returns order.
     *
     * @param int $id
     * @return \WCGQL\Helpers\OrdersFactory
     */
    public function get_order($id = 0)
    {
        if (\is_numeric($id) && $id > 0) {
            return new Order($id);
        }

        throw new \Exception("Expected order id found '$id'.");
    }

    /**
     * Sets the offset for the orders batch.
     *
     * @param int $offset
     * @return \WCGQL\Helpers\OrdersFactory
     */
    public function offset($offset)
    {
        if (\is_numeric($offset)) {
            $this->args['offset'] = \intval($offset);
        }

        return $this;
    }

    /**
     * Sets maximum number of orders to return.
     *
     * @param int $limit
     * @return \WCGQL\Helpers\OrdersFactory
     */
    public function limit($limit)
    {
        if (\is_numeric($limit)) {
            $this->args['limit'] = \intval($limit);
        }

        return $this;
    }

    /**
     * return a list of orders given an arguments array.
     *
     * @param array $args
     * @return \WCGQL\Helpers\Order[]
     */
    public function get_orders()
    {
        $args = $this->args;
        if (!$args['customer_id']) {
            return array();
        }
        
        if ($this->should_bypass_customer) {
            unset($args['customer_id']);
        }

        return wc_get_orders($args);
    }
}
