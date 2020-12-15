<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 *
 *  Language file for Buycraft module for NamelessMC
 */

// Which version of NamelessMC is this language file updated to?
$language_version = '2.0.0-pr6';

$language = array(
    /*
     *  General terms
     */
    'store' => 'Store',
    'buycraft' => 'Tebex',
    'home' => 'Home',
    'view_full_store' => 'View full store',
    'buy' => 'Buy',
    'sale' => 'Sale',

    /*
     *  Admin terms
     */
    'force_sync' => 'Force Sync',
    'settings' => 'Settings',
    'categories' => 'Categories',
    'packages' => 'Packages',
    'payments' => 'Payments',
    'bans' => 'Bans',
    'coupons' => 'Coupons',
    'gift_cards' => 'Gift Cards',
    'server_key' => 'Server Key',
    'server_key_info' => 'This is the secret key shown when editing your server <a href=&quot;https://server.tebex.io/settings/servers&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;>here</a>', // &quot; = ", don't change
    'invalid_server_key' => 'Invalid server key.',
    'store_content_max' => 'The store index content must be a maximum of 1,000,000 characters.',
    'store_path' => 'Store Path',
    'store_index_content' => 'Store Index Content',
    'allow_guests' => 'Allow guests to view the store?',
    'updated_successfully' => 'Updated successfully.',
    'no_categories' => 'No categories have been found! Make sure you have synchronised your store first.',
    'no_packages' => 'No packages have been found! Make sure you have synchronised your store first.',
    'no_payments' => 'No payments have been found!',
    'no_bans' => 'No bans have been found!',
    'no_coupons' => 'No coupons have been found!',
    'no_gift_cards' => 'No gift cards have been found!',
    'no_subcategories' => 'No subcategories.',
    'editing_category_x' => 'Editing category {x}', // Don't replace {x}
    'category_description' => 'Category Description',
    'category_image' => 'Category Image',
    'upload_new_image' => 'Upload New Image',
    'description_max_100000' => 'The description must be a maximum of 100000 characters.',
    'description_updated_successfully' => 'Description updated successfully.',
    'image_updated_successfully' => 'Image updated successfully.',
    'unable_to_upload_image' => 'Unable to upload image: {x}', // Don't replace {x} (error message)
    'unable_to_create_image_directory' => 'Unable to create the <strong>/uploads/store</strong> directory to store images.',
    'editing_package_x' => 'Editing package {x}', // Don't replace {x}
    'package_description' => 'Package Description',
    'package_image' => 'Package Image',
    'user' => 'User',
    'amount' => 'Amount',
    'date' => 'Date',
    'view' => 'View',
    'viewing_payments_for_user_x' => 'Viewing payments for user {x}', // Don't replace {x}
    'no_payments_for_user' => 'No payments were found for that user.',
    'package' => 'Package',
    'ign' => 'Ingame Username',
    'uuid' => 'UUID',
    'please_enter_valid_ign_package' => 'Please enter a valid ingame username and select a package.',
    'price' => 'Price',
    'please_enter_valid_price' => 'Please enter a valid price.',
    'payment_created_successfully' => 'Payment created successfully.',
    'viewing_payment' => 'Viewing payment {x}', // Don't replace {x}
	'pending_commands' => 'Pending commands',
    'no_pending_commands' => 'No pending commands.',
    'reason' => 'Reason',
    'email' => 'Email',
    'must_enter_uuid' => 'You must enter a UUID!',
    'ban_created_successfully' => 'Ban created successfully.',
    'creating_ban' => 'Creating Ban',
    'ip_address' => 'IP Address',
    'optional' => 'Optional',
    'viewing_ban_x' => 'Viewing ban {x}', // Don't replace {x}
	'remove_ban_in_buycraft' => 'You can remove this ban in your Tebex control panel.',
    'creating_coupon' => 'Creating Coupon',
    'coupon_code' => 'Coupon Code',
    'coupon_code_alphanumeric' => 'The coupon code must be alphanumeric.',
    'coupon_code_required' => 'A coupon code is required.',
    'coupon_note' => 'Note',
    'coupon_note_required' => 'A note is required.',
    'invalid_expire_date' => 'Invalid expiry date.',
    'invalid_start_date' => 'Invalid start date.',
    'effective_on' => 'Effective On',
    'cart' => 'Cart',
    'category' => 'Category',
    'select_multiple_with_ctrl' => '(select multiple by holding Ctrl (Cmd on a Mac))',
    'discount_type' => 'Discount Type',
    'value' => 'Value',
    'percentage' => 'Percentage',
    'unlimited_usage' => 'Unlimited Usage',
    'uses' => 'Uses',
    'never_expire' => 'Never Expire',
    'never' => 'Never',
    'expiry_date' => 'Expiry Date (yyyy-mm-dd)',
    'start_date' => 'Start Date (yyyy-mm-dd)',
    'expiry_date_table' => 'Expiry Date', // expiry_date without (yyyy-mm-dd)
    'basket_type' => 'Basket Type',
    'all_purchases' => 'All purchases',
    'one_off_purchases' => 'One-off purchases',
    'subscriptions' => 'Subscriptions',
    'discount_application_type' => 'Discount Application Type',
    'each_package' => 'Apply to each package',
    'basket_before_sales' => 'Apply to basket before sales',
    'basket_after_sales' => 'Apply to basket after sales',
    'minimum_spend' => 'Minimum Spend',
    'user_coupon_for' => 'Username of user the coupon is for',
    'user_limit' => 'Limit per user/IP address',
    'coupon_created_successfully' => 'Coupon created successfully.',
    'confirm_delete_coupon' => 'Are you sure you want to delete this coupon?',
    'coupon_deleted_successfully' => 'Coupon deleted successfully.',
    'viewing_coupon_x' => 'Viewing coupon {x}', // Don't replace {x}
	'edit_coupon_in_buycraft' => 'You can edit this coupon in your Tebex control panel.',
    'creating_gift_card' => 'Creating Gift Card',
    'gift_card_value_required' => 'You must enter a gift card value!',
    'gift_card_value' => 'Value',
    'gift_card_note' => 'Note',
    'gift_card_created_successfully' => 'Gift card created successfully.',
    'gift_card_created_successfully_with_code' => 'Gift card created successfully with code <strong>{x}</strong>.', // Don't replace {x}
    'gift_card_updated_successfully' => 'Gift card updated successfully.',
    'gift_card_voided_successfully' => 'Gift card voided successfully.',
    'gift_card_code' => 'Code',
    'gift_card_start_balance' => 'Starting balance',
    'gift_card_balance_remaining' => 'Remaining balance',
    'gift_card_active' => 'Active',
    'viewing_gift_card_x' => 'Viewing gift card {x}',
    'add_credit' => 'Add credit to gift card',
    'credit' => 'Credit',
    'credit_required' => 'The amount of credit to add is required!',
    'void_gift_card' => 'Void gift card',
    'confirm_void_gift_card' => 'Are you sure you want to void this gift card?',
    'id_x' => 'ID: {x}', // Don't replace {x}

    /*
     *  Other permissions
     */
    'update_category' => 'Update category',
    'update_package' => 'Update package',
    'new_payment' => 'New payment',
    'new_gift_card' => 'New gift card',
    'update_gift_card' => 'Update gift card',
    'new_coupon' => 'New coupon',
    'delete_coupon' => 'Delete coupon',
    'new_ban' => 'New ban',

    /*
     *  Admin force sync
     */
    'unable_to_get_information' => 'Unable to get information from Tebex: {x}', // Don't replace {x} (error code)
    'information_retrieved_successfully' => 'Information retrieved successfully',
    'unable_to_get_command_queue' => 'Unable to get command queue from Tebex: {x}', // Don't replace {x} (error code)
    'command_queue_retrieved_successfully' => 'Command queue retrieved successfully',
    'unable_to_get_listing' => 'Unable to get listing from Tebex: {x}', // Don't replace {x} (error code)
    'listing_retrieved_successfully' => 'Listing retrieved successfully',
    'unable_to_get_packages' => 'Unable to get packages from Tebex: {x}', // Don't replace {x} (error code)
    'packages_retrieved_successfully' => 'Packages retrieved successfully',
    'unable_to_get_payments' => 'Unable to get payments from Tebex: {x}', // Don't replace {x} (error code)
    'payments_retrieved_successfully' => 'Payments retrieved successfully',
    'unable_to_get_gift_cards' => 'Unable to get gift cards from Tebex: {x}', // Don't replace {x} (error code)
    'gift_cards_retrieved_successfully' => 'Gift cards retrieved successfully',
    'unable_to_get_coupons' => 'Unable to get coupons from Tebex: {x}', // Don't replace {x} (error code)
    'coupons_retrieved_successfully' => 'Coupons retrieved successfully',
    'unable_to_get_bans' => 'Unable to get bans from Tebex: {x}', // Don't replace {x} (error code)
    'bans_retrieved_successfully' => 'Bans retrieved successfully',

	/*
	 *  Hooks
	 */
	'purchase_hook_info' => 'New store purchase',
	'new_coupon_hook_info' => 'Store coupon created',
	'new_gift_card_hook_info' => 'Store gift card created',

	/*
	 *  Widgets
	 */
	'latest_purchases' => 'Latest Purchases',
	'no_purchases' => 'No purchases',
	'latest_purchases_limit' => 'Latest purchases limit',
	'latest_posts_widget_cached' => 'The latest purchases widget is cached for 2 minutes, your changes may not take effect immediately.',
	'featured_package' => 'Featured Package',
	'featured_packages' => 'Featured Packages',
	'featured_packages_info' => 'One package will be chosen at random from any selected packages',

);
