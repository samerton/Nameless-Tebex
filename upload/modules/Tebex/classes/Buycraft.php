<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Buycraft integration class
 */

class Buycraft {
    // Get all info using secret key $server_key
    public static function updateAll($server_key = null){
        if($server_key){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $db = DB::getInstance();

            // Start with basic information
            self::updateInformation($server_key, $ch, $db);

            // Command queue
            self::updateCommandQueue($server_key, $ch, $db);

            // Listing
            self::updateListing($server_key, $ch, $db);

            // Payments
            self::updatePayments($server_key, $ch, $db);

            // Gift cards
            self::updateGiftCards($server_key, $ch, $db);

            // Coupons
            self::updateCoupons($server_key, $ch, $db);

            // Bans
            self::updateBans($server_key, $ch, $db);

            curl_close($ch);
        }

        return false;
    }

    // Get basic information
    public static function updateInformation($server_key = null, $ch = null, $db = null){
        if($server_key){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/information');

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(!isset($result->error_code)){
                // Update database
                if(isset($result->account->domain)){
                    $query = $db->get('buycraft_settings', array('name', '=', 'domain'));

                    if($query->count()){
                        $db->update('buycraft_settings', $query->first()->id, array(
                            'value' => Output::getClean($result->account->domain)
                        ));

                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'domain',
                            'value' => Output::getClean($result->account->domain)
                        ));

                    }
                }

                if(isset($result->account->currency->iso_4217)){
                    $query = $db->get('buycraft_settings', array('name', '=', 'currency_iso'));

                    if($query->count()){
                        $db->update('buycraft_settings', $query->first()->id, array(
                            'value' => Output::getClean($result->account->currency->iso_4217)
                        ));

                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'currency_iso',
                            'value' => Output::getClean($result->account->currency->iso_4217)
                        ));

                    }
                }

                if(isset($result->account->currency->symbol)){
                    $query = $db->get('buycraft_settings', array('name', '=', 'currency_symbol'));

                    if($query->count()){
                        $db->update('buycraft_settings', $query->first()->id, array(
                            'value' => Output::getClean($result->account->currency->symbol)
                        ));

                    } else {
                        $db->insert('buycraft_settings', array(
                            'name' => 'currency_symbol',
                            'value' => Output::getClean($result->account->currency->symbol)
                        ));

                    }
                }

            }

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }

    // Get command queue
    public static function updateCommandQueue($server_key = null, $ch = null, $db = null){
        if($server_key){
            // Get next check time
            $next_check = $db->get('buycraft_settings', array('name', '=', 'next_check'));
            if($next_check->count()){
                $next_check_id = $next_check->first()->id;
                $next_check = $next_check->first()->value;

                if($next_check < date('U')){
                    $continue = true;

                } else {
                    $ret = new stdClass;
                    $ret->error_code = 403;
                    $ret->error_message = 'Please wait ' . ($next_check - date('U')) . ' seconds before checking the command queue again.';

                    return $ret;
                }

            } else
                $continue = true;

            if(isset($continue)){
                // Delete existing commands
                $db->delete('buycraft_commands', array('id', '<>', 0));

                if(!$ch){
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $close = true;
                }

                curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/queue');

                $ch_result = curl_exec($ch);

                $result = json_decode($ch_result);

                if(isset($result->meta->next_check)){
                    if(isset($next_check_id)){
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

                if(!isset($result->error_code)){
                    if(isset($result->meta->execute_offline) && $result->meta->execute_offline == true){
                        // Get offline commands
                        curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/queue/offline-commands');

                        $offline_commands_result = curl_exec($ch);

                        $offline_commands = json_decode($offline_commands_result);

                        if(isset($offline_commands->commands) && count($offline_commands->commands)){
                            foreach($offline_commands as $command){
                                if(isset($command->player->name))
                                    $player_name = Output::getClean($command->player->name);
                                else
                                    $player_name = null;

                                if(isset($command->player->uuid))
                                    $player_uuid = Output::getClean($command->player->uuid);
                                else
                                    $player_uuid = null;

                                $db->insert('buycraft_commands', array(
                                    'id' => $command->id,
                                    'type' => 0,
                                    'command' => Output::getClean($command->command),
                                    'payment' => $command->payment,
                                    'package' => $command->package,
                                    'player_name' => $player_name,
                                    'player_uuid' => $player_uuid
                                ));
                            }
                        }

                        $offline_commands_result = null;
                        $offline_commands = null;
                    }

                    if(isset($result->players) && count($result->players)){
                        foreach($result->players as $player){
                            $player_name = Output::getClean($player->name);
                            $player_uuid = Output::getClean($player->uuid);
                            $player_id = $player->id;

                            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/queue/online-commands/' . $player_id);

                            $online_commands_result = curl_exec($ch);
                            $online_commands = json_decode($online_commands_result);

                            if(isset($online_commands->commands) && count($online_commands->commands)){
                                foreach($online_commands->commands as $command){
                                    $db->insert('buycraft_commands', array(
                                        'id' => $command->id,
                                        'type' => 1,
                                        'command' => Output::getClean($command->command),
                                        'payment' => $command->payment,
                                        'package' => $command->package,
                                        'player_name' => $player_name,
                                        'player_uuid' => $player_uuid
                                    ));
                                }
                            }
                        }

                        $online_commands_result = null;
                        $online_commands = null;
                    }
                }

                if(isset($close))
                    curl_close($ch);

                return $result;
            }
        }

        return null;
    }

    // Get listing
    public static function updateListing($server_key = null, $ch = null, $db = null){
        if($server_key){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/listing');

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(!isset($result->error_code)){
                $db->delete('buycraft_categories', array('id', '<>', 0));
                $db->delete('buycraft_packages', array('id', '<>', 0));

                if(isset($result->categories) && count($result->categories)){
                    foreach($result->categories as $category){
                        $db->insert('buycraft_categories', array(
                            'id' => $category->id,
                            'order' => $category->order,
                            'name' => Output::getClean($category->name),
                            'only_subcategories' => $category->only_subcategories,
                            'parent_category' => null
                        ));

                        if(isset($category->subcategories) && count($category->subcategories)){
                            foreach($category->subcategories as $subcategory){
                                $db->insert('buycraft_categories', array(
                                    'id' => $subcategory->id,
                                    'order' => $subcategory->order,
                                    'name' => Output::getClean($subcategory->name),
                                    'only_subcategories' => 0,
                                    'parent_category' => $category->id
                                ));

                                if(isset($subcategory->packages) && count($subcategory->packages)){
                                    foreach($subcategory->packages as $subcategory_package){
                                        $db->insert('buycraft_packages', array(
                                            'id' => $subcategory_package->id,
                                            'category_id' => $subcategory->id,
                                            'order' => $subcategory_package->order,
                                            'name' => Output::getClean($subcategory_package->name),
                                            'price' => Output::getClean($subcategory_package->price),
                                            'sale_active' => (isset($subcategory_package->sale->active) && $subcategory_package->sale->active === true ? 1 : 0),
                                            'sale_discount' => (isset($subcategory_package->sale->discount) ? Output::getClean($subcategory_package->sale->discount) : null)
                                        ));
                                    }
                                }
                            }
                        }

                        if(isset($category->packages) && count($category->packages)){
                            foreach($category->packages as $package){
                                $db->insert('buycraft_packages', array(
                                    'id' => $package->id,
                                    'category_id' => $category->id,
                                    'order' => $package->order,
                                    'name' => Output::getClean($package->name),
                                    'price' => Output::getClean($package->price),
                                    'sale_active' => (isset($package->sale->active) && $package->sale->active === true ? 1 : 0),
                                    'sale_discount' => (isset($package->sale->discount) ? Output::getClean($package->sale->discount) : null)
                                ));
                            }
                        }


                    }
                }
            }

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }

    // Get payments
    public static function updatePayments($server_key = null, $ch = null, $db = null){
        if($server_key){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/payments');

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(!isset($result->error_code)){
            	$db->createQuery('TRUNCATE TABLE nl2_buycraft_payments');

                if(count($result)){
                    foreach($result as $payment){
                        $db->createQuery('INSERT INTO nl2_buycraft_payments (`id`, `amount`, `date`, `currency_iso`, `currency_symbol`, `player_uuid`, `player_name`) VALUES (?, ?, ?, ?, ?, ?, ?)', array(
                            $payment->id,
                            Output::getClean($payment->amount),
                            strtotime($payment->date),
                            Output::getClean($payment->currency->iso_4217),
                            Output::getClean($payment->currency->symbol),
                            Output::getClean($payment->player->uuid),
                            Output::getClean($payment->player->name)
                        ));
                    }
                }
            }

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }

    // Get gift cards
    public static function updateGiftCards($server_key = null, $ch = null, $db = null){
        if($server_key){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/gift-cards');

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(!isset($result->error_code)){
                $db->delete('buycraft_gift_cards', array('id', '<>', 0));

                if(isset($result->data) && count($result->data)){
                    foreach($result->data as $gift_card){
                        $db->insert('buycraft_gift_cards', array(
                            'id' => $gift_card->id,
                            'code' => Output::getClean($gift_card->code),
                            'balance_starting' => Output::getClean($gift_card->balance->starting),
                            'balance_remaining' => Output::getClean($gift_card->balance->remaining),
                            'balance_currency' => Output::getClean($gift_card->balance->currency),
                            'note' => Output::getClean($gift_card->note),
                            'void' => (isset($gift_card->void) && $gift_card->void === true ? 1 : 0)
                        ));
                    }
                }
            }

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }

    // Get coupons
    public static function updateCoupons($server_key = null, $ch = null, $db = null){
        if($server_key){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/coupons');

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(!isset($result->error_code)){
                $db->delete('buycraft_coupons', array('id', '<>', 0));

                if(isset($result->data) && count($result->data)){
                    foreach($result->data as $coupon){
                        $db->insert('buycraft_coupons', array(
                            'id' => $coupon->id,
                            'code' => Output::getClean($coupon->code),
                            'effective_type' => Output::getClean($coupon->effective->type),
                            'effective_packages' => json_encode($coupon->effective->packages),
                            'effective_categories' => json_encode($coupon->effective->categories),
                            'discount_type' => Output::getClean($coupon->discount->type),
                            'discount_percentage' => $coupon->discount->percentage,
                            'discount_value' => $coupon->discount->value,
                            'redeem_unlimited' => (isset($coupon->expire->redeem_unlimited) && $coupon->expire->redeem_unlimited == 'true' ? 1 : 0),
                            'expires' => (isset($coupon->expire->expire_never) && $coupon->expire->expire_never == 'true' ? 0 : 1),
                            'redeem_limit' => $coupon->expire->limit,
                            'date' => strtotime($coupon->expire->date),
                            'basket_type' => Output::getClean($coupon->basket_type),
                            'start_date' => strtotime($coupon->start_date),
                            'minimum' => $coupon->minimum,
                            'username' => (isset($coupon->username) ? Output::getClean($coupon->username) : null),
                            'note' => Output::getClean($coupon->note)
                        ));
                    }
                }
            }

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }

    // Get bans
    public static function updateBans($server_key = null, $ch = null, $db = null){
        if($server_key){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/bans');

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(!isset($result->error_code)){
                $db->delete('buycraft_bans', array('id', '<>', 0));

                if(isset($result->data) && count($result->data)){
                    foreach($result->data as $ban){
                        $db->insert('buycraft_bans', array(
                            'id' => $ban->id,
                            'time' => strtotime($ban->time),
                            'ip' => Output::getClean($ban->ip),
                            'payment_email' => Output::getClean($ban->payment_email),
                            'reason' => Output::getClean($ban->reason),
                            'user_ign' => Output::getClean($ban->user->ign),
                            'uuid' => Output::getClean($ban->user->uuid)
                        ));
                    }
                }
            }

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }

    // Get fields for package
    public static function getPackageFields($server_key = null, $package_id = null, $ch = null){
        if($server_key && $package_id){
            if(!$ch){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $close = true;
            }

            curl_setopt($ch, CURLOPT_URL, 'https://plugin.buycraft.net/payments/fields/' . $package_id);

            $ch_result = curl_exec($ch);

            $result = json_decode($ch_result);

            if(isset($close))
                curl_close($ch);

            return $result;
        }

        return null;
    }
}