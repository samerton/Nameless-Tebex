<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Buycraft integration class
 */

class Buycraft {
    // Get all info using secret key $server_key
    public static function updateAll($server_key = null): bool {
        if ($server_key) {
            $db = DB::getInstance();

            // Start with basic information
            self::updateInformation($server_key, $db);

            // Command queue
            self::updateCommandQueue($server_key, $db);

            // Listing
            self::updateListing($server_key, $db);

            // Packages
            self::updatePackages($server_key, $db);

            // Payments
            self::updatePayments($server_key, $db);

            // Gift cards
            self::updateGiftCards($server_key, $db);

            // Coupons
            self::updateCoupons($server_key, $db);

            // Bans
            self::updateBans($server_key, $db);

            return true;
        }

        return false;
    }

    // Get basic information
    public static function updateInformation($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/information', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                // Update database
                if (isset($result->account->domain)) {
                    $query = $db->get('buycraft_settings', array('name', '=', 'domain'));

                    if ($query->count()) {
                        $db->update('buycraft_settings', $query->first()->id, array(
                            'value' => $result->account->domain
                        ));

                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'domain',
                            'value' => $result->account->domain
                        ));

                    }
                }

                if (isset($result->account->currency->iso_4217)) {
                    $query = $db->get('buycraft_settings', array('name', '=', 'currency_iso'));

                    if ($query->count()) {
                        $db->update('buycraft_settings', $query->first()->id, array(
                            'value' => $result->account->currency->iso_4217
                        ));

                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'currency_iso',
                            'value' => $result->account->currency->iso_4217
                        ));

                    }
                }

                if (isset($result->account->currency->symbol)) {
                    $query = $db->get('buycraft_settings', array('name', '=', 'currency_symbol'));

                    if ($query->count()) {
                        $db->update('buycraft_settings', $query->first()->id, array(
                            'value' => $result->account->currency->symbol
                        ));

                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'currency_symbol',
                            'value' => $result->account->currency->symbol
                        ));

                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get command queue
    public static function updateCommandQueue($server_key = null, $db = null): array {
        if ($server_key) {
            // Get next check time
            $next_check = $db->get('buycraft_settings', array('name', '=', 'next_check'));
            if ($next_check->count()) {
                $next_check_id = $next_check->first()->id;
                $next_check = $next_check->first()->value;

                if ($next_check >= date('U')) {
                    return ['error' => 'Please wait ' . ($next_check - date('U')) . ' seconds before checking the command queue again.'];
                }

            }

            // Delete existing commands
            $db->delete('buycraft_commands', array('id', '<>', 0));

            $result = HttpClient::get('https://plugin.tebex.io/queue', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                if (isset($result->meta->next_check)) {
                    if (isset($next_check_id)) {
                        $db->update('buycraft_settings', $next_check_id, array(
                            'value' => (date('U') + $result->meta->next_check)
                        ));
                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'next_check',
                            'value' => (date('U') + $result->meta->next_check)
                        ));
                    }
                }

                if (isset($result->meta->execute_offline) && $result->meta->execute_offline) {
                    // Get offline commands
                    $offline_commands_result = HttpClient::get('https://plugin.tebex.io/queue/offline-commands', ['headers' => ['X-Tebex-Secret' => $server_key]]);

                    if (!$offline_commands_result->hasError()) {
                        $offline_commands_result = $offline_commands_result->json();

                        if (isset($offline_commands_result->commands) && count($offline_commands_result->commands)) {
                            foreach($offline_commands_result->commands as $command){
                                if (isset($command->player->name))
                                    $player_name = $command->player->name;
                                else
                                    $player_name = null;

                                if (isset($command->player->uuid))
                                    $player_uuid = $command->player->uuid;
                                else
                                    $player_uuid = null;

                                $db->insert('buycraft_commands', array(
                                    'id' => $command->id,
                                    'type' => 0,
                                    'command' => $command->command,
                                    'payment' => $command->payment,
                                    'package' => $command->package,
                                    'player_name' => $player_name,
                                    'player_uuid' => $player_uuid
                                ));
                            }
                        }
                    }

                    $offline_commands_result = null;
                }

                if (isset($result->players) && count($result->players)) {
                    foreach($result->players as $player){
                        $player_name = $player->name;
                        $player_uuid = $player->uuid;
                        $player_id = $player->id;

                        $online_commands_result = HttpClient::get('https://plugin.tebex.io/queue/online-commands/' . $player_id, ['headers' => ['X-Tebex-Secret' => $server_key]]);

                        if (!$online_commands_result->hasError()) {
                            $online_commands_result = $online_commands_result->json();

                            if (isset($online_commands_result->commands) && count($online_commands_result->commands)) {
                                foreach($online_commands_result->commands as $command){
                                    $db->insert('buycraft_commands', array(
                                        'id' => $command->id,
                                        'type' => 1,
                                        'command' => $command->command,
                                        'payment' => $command->payment,
                                        'package' => $command->package,
                                        'player_name' => $player_name,
                                        'player_uuid' => $player_uuid
                                    ));
                                }
                            }
                        }

                        $online_commands_result = null;
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get listing
    public static function updateListing($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/listing', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                $db->delete('buycraft_categories', array('id', '<>', 0));
                $db->delete('buycraft_packages', array('id', '<>', 0));

                if (isset($result->categories) && count($result->categories)) {
                    foreach($result->categories as $category){
                        $db->insert('buycraft_categories', array(
                            'id' => $category->id,
                            'order' => $category->order,
                            'name' => $category->name,
                            'only_subcategories' => $category->only_subcategories ? 1 : 0,
                            'parent_category' => null
                        ));

                        if (isset($category->subcategories) && count($category->subcategories)) {
                            foreach($category->subcategories as $subcategory){
                                $db->insert('buycraft_categories', array(
                                    'id' => $subcategory->id,
                                    'order' => $subcategory->order,
                                    'name' => $subcategory->name,
                                    'only_subcategories' => isset($subcategory->only_subcategories) && $subcategory->only_subcategories ? 1 : 0,
                                    'parent_category' => $category->id
                                ));

                                if (isset($subcategory->packages) && count($subcategory->packages)) {
                                    foreach($subcategory->packages as $subcategory_package){
                                        $db->insert('buycraft_packages', array(
                                            'id' => $subcategory_package->id,
                                            'category_id' => $subcategory->id,
                                            'order' => $subcategory_package->order,
                                            'name' => $subcategory_package->name,
                                            'price' => $subcategory_package->price,
                                            'sale_active' => (isset($subcategory_package->sale->active) && $subcategory_package->sale->active === true ? 1 : 0),
                                            'sale_discount' => ($subcategory_package->sale->discount ?? null)
                                        ));
                                    }
                                }
                            }
                        }

                        if (isset($category->packages) && count($category->packages)) {
                            foreach($category->packages as $package){
                                $db->insert('buycraft_packages', array(
                                    'id' => $package->id,
                                    'category_id' => $category->id,
                                    'order' => $package->order,
                                    'name' => $package->name,
                                    'price' => $package->price,
                                    'sale_active' => (isset($package->sale->active) && $package->sale->active === true ? 1 : 0),
                                    'sale_discount' => ($package->sale->discount ?? null)
                                ));
                            }
                        }
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get packages
    public static function updatePackages($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/packages?verbose=true', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                $db->delete('buycraft_packages_descriptions', array('id', '<>', 0));

                if (count($result)) {
                    foreach ($result as $package) {
                        $db->insert('buycraft_packages_descriptions', array(
                            'id' => $package->id,
                            'package_id' => $package->id,
                            'description' => $package->description,
                            'image' => $package->image
                        ));
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get payments
    public static function updatePayments($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/payments', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

            	$db->createQuery('TRUNCATE TABLE nl2_buycraft_payments');

                if (count($result)) {
                    foreach($result as $payment){
                        $db->createQuery('INSERT INTO nl2_buycraft_payments (`id`, `amount`, `date`, `currency_iso`, `currency_symbol`, `player_uuid`, `player_name`) VALUES (?, ?, ?, ?, ?, ?, ?)', array(
                            $payment->id,
                            $payment->amount,
                            strtotime($payment->date),
                            $payment->currency->iso_4217,
                            $payment->currency->symbol,
                            $payment->player->uuid,
                            $payment->player->name
                        ));
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get gift cards
    public static function updateGiftCards($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/gift-cards', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                $db->delete('buycraft_gift_cards', array('id', '<>', 0));

                if (isset($result->data) && count($result->data)) {
                    foreach($result->data as $gift_card){
                        $db->insert('buycraft_gift_cards', array(
                            'id' => $gift_card->id,
                            'code' => $gift_card->code,
                            'balance_starting' => $gift_card->balance->starting,
                            'balance_remaining' => $gift_card->balance->remaining,
                            'balance_currency' => $gift_card->balance->currency,
                            'note' => $gift_card->note,
                            'void' => (isset($gift_card->void) && $gift_card->void === true ? 1 : 0)
                        ));
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get coupons
    public static function updateCoupons($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/coupons', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                $db->delete('buycraft_coupons', array('id', '<>', 0));

                if (isset($result->data) && count($result->data)) {
                    foreach($result->data as $coupon){
                        $db->insert('buycraft_coupons', array(
                            'id' => $coupon->id,
                            'code' => $coupon->code,
                            'effective_type' => $coupon->effective->type,
                            'effective_packages' => json_encode($coupon->effective->packages),
                            'effective_categories' => json_encode($coupon->effective->categories),
                            'discount_type' => $coupon->discount->type,
                            'discount_percentage' => $coupon->discount->percentage,
                            'discount_value' => $coupon->discount->value,
                            'redeem_unlimited' => (isset($coupon->expire->redeem_unlimited) && $coupon->expire->redeem_unlimited == 'true' ? 1 : 0),
                            'expires' => (isset($coupon->expire->expire_never) && $coupon->expire->expire_never == 'true' ? 0 : 1),
                            'redeem_limit' => $coupon->expire->limit,
                            'date' => strtotime($coupon->expire->date),
                            'basket_type' => $coupon->basket_type,
                            'start_date' => strtotime($coupon->start_date),
                            'minimum' => $coupon->minimum,
                            'username' => ($coupon->username ?? null),
                            'note' => $coupon->note
                        ));
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get bans
    public static function updateBans($server_key = null, $db = null): array {
        if ($server_key) {
            $result = HttpClient::get('https://plugin.tebex.io/bans', ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                $result = $result->json();

                $db->delete('buycraft_bans', array('id', '<>', 0));

                if (isset($result->data) && count($result->data)) {
                    foreach($result->data as $ban){
                        $db->insert('buycraft_bans', array(
                            'id' => $ban->id,
                            'time' => strtotime($ban->time),
                            'ip' => $ban->ip,
                            'payment_email' => $ban->payment_email,
                            'reason' => $ban->reason,
                            'user_ign' => $ban->user->ign,
                            'uuid' => $ban->user->uuid
                        ));
                    }
                }

                return ['response' => $result];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key'];
    }

    // Get fields for package
    public static function getPackageFields($server_key = null, $package_id = null): array {
        if ($server_key && $package_id) {
            $result = HttpClient::get('https://plugin.tebex.io/payments/fields/' . $package_id, ['headers' => ['X-Tebex-Secret' => $server_key]]);

            if (!$result->hasError()) {
                return ['response' => $result->json()];
            }

            return ['error' => $result->getError()];
        }

        return ['error' => 'No server key or package ID'];
    }

    /**
     * Helper function to validate a date
     * Taken from v2 pre-release 7 as this was removed from Nameless in pre-release 8
     *
     * @param $date string Date to check
     * @param $format ?string Format to use, defaults to m/d/Y
     * @return bool Whether date is valid or not
     */
    public static function validateDate(string $date, ?string $format = 'm/d/Y'): bool {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
