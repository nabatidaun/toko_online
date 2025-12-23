<?php

/**
 * Cart Helper Functions
 * File: app/Helpers/cart_helper.php
 * 
 * Helper untuk mengelola shopping cart berbasis session
 */

if (!function_exists('get_cart')) {
    /**
     * Get cart data from session
     * 
     * @return array
     */
    function get_cart()
    {
        $session = \Config\Services::session();
        return $session->get('cart') ?? [];
    }
}

if (!function_exists('save_cart')) {
    /**
     * Save cart to session
     * 
     * @param array $cart
     * @return void
     */
    function save_cart($cart)
    {
        $session = \Config\Services::session();
        $session->set('cart', $cart);
    }
}

if (!function_exists('add_to_cart')) {
    /**
     * Add item to cart
     * 
     * @param array $item
     * @return bool
     */
    function add_to_cart($item)
    {
        $cart = get_cart();
        
        // Check if item already exists
        $exists = false;
        foreach ($cart as $key => $cartItem) {
            if ($cartItem['id'] == $item['id']) {
                $cart[$key]['qty'] += $item['qty'];
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $cart[] = $item;
        }
        
        save_cart($cart);
        return true;
    }
}

if (!function_exists('remove_from_cart')) {
    /**
     * Remove item from cart by ID
     * 
     * @param int $id
     * @return bool
     */
    function remove_from_cart($id)
    {
        $cart = get_cart();
        
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                unset($cart[$key]);
                $cart = array_values($cart); // Re-index
                save_cart($cart);
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('update_cart')) {
    /**
     * Update cart item quantity
     * 
     * @param int $id
     * @param int $qty
     * @return bool
     */
    function update_cart($id, $qty)
    {
        $cart = get_cart();
        
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                $cart[$key]['qty'] = $qty;
                save_cart($cart);
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('clear_cart')) {
    /**
     * Clear all cart items
     * 
     * @return void
     */
    function clear_cart()
    {
        $session = \Config\Services::session();
        $session->remove('cart');
    }
}

if (!function_exists('cart_total')) {
    /**
     * Calculate cart total
     * 
     * @return int
     */
    function cart_total()
    {
        $cart = get_cart();
        $total = 0;
        
        foreach ($cart as $item) {
            $total += ($item['harga'] * $item['qty']);
        }
        
        return $total;
    }
}

if (!function_exists('cart_count')) {
    /**
     * Count total items in cart
     * 
     * @return int
     */
    function cart_count()
    {
        $cart = get_cart();
        return count($cart);
    }
}

if (!function_exists('cart_items_count')) {
    /**
     * Count total quantity of all items
     * 
     * @return int
     */
    function cart_items_count()
    {
        $cart = get_cart();
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['qty'];
        }
        
        return $total;
    }
}