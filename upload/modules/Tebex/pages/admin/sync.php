<?php
/*
 *	Made by Samerton
 *  https://github.com/samerton
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Buycraft integration for NamelessMC - admin sync
 */

// Can the user view the AdminCP?
if($user->isLoggedIn()){
    if(!$user->canViewACP()){
        // No
        Redirect::to(URL::build('/'));
        die();
    } else {
        // Check the user has re-authenticated
        if(!$user->isAdmLoggedIn()){
            // They haven't, do so now
            Redirect::to(URL::build('/admin/auth'));
            die();
        } else {
            if(!$user->hasPermission('admincp.buycraft')){
                Redirect::to(URL::build('/admin'));
                die();
            }
        }
    }
} else {
    // Not logged in
    Redirect::to(URL::build('/login'));
    die();
}

$page = 'admin';
$admin_page = 'buycraft';

?>
<!DOCTYPE html>
<html lang="<?php echo(defined('HTML_LANG') ? HTML_LANG : 'en'); ?>" <?php if(defined('HTML_RTL') && HTML_RTL === true) echo ' dir="rtl"'; ?>>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <?php
    $title = $language->get('admin', 'admin_cp');
    require(ROOT_PATH . '/core/templates/admin_header.php');
    ?>

    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.css">

</head>
<body>
<?php require(ROOT_PATH . '/modules/Core/pages/admin/navbar.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <?php require(ROOT_PATH . '/modules/Core/pages/admin/sidebar.php'); ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-block">
                    <h3 style="display:inline;"><?php echo $buycraft_language->get('language', 'buycraft'); ?></h3>

                    <a href="<?php echo URL::build('/admin/buycraft'); ?>" class="btn btn-primary float-right"><?php echo $language->get('general', 'back'); ?></a>

                    <hr />

                    <?php
                    // Get server key
                    $server_key = $queries->getWhere('buycraft_settings', array('name', '=', 'server_key'));

                    if(count($server_key)){
                        $server_key = $server_key[0]->value;

                        if(strlen($server_key) == 40){
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Buycraft-Secret: ' . $server_key));
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            $db = DB::getInstance();

                            $result = Buycraft::updateInformation($server_key, $ch, $db);

                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_information')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'information_retrieved_successfully') . '</div>';
                            }

                            $result = Buycraft::updateCommandQueue($server_key, $ch, $db);

                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_command_queue')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'command_queue_retrieved_successfully') . '</div>';
                            }

                            $result = Buycraft::updateListing($server_key, $ch, $db);

                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_listing')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'listing_retrieved_successfully') . '</div>';
                            }

                            $result = Buycraft::updatePayments($server_key, $ch, $db);
                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_payments')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'payments_retrieved_successfully') . '</div>';
                            }

                            $result = Buycraft::updateGiftCards($server_key, $ch, $db);
                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_gift_cards')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'gift_cards_retrieved_successfully') . '</div>';
                            }

                            $result = Buycraft::updateCoupons($server_key, $ch, $db);
                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_coupons')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'coupons_retrieved_successfully') . '</div>';
                            }

                            $result = Buycraft::updateBans($server_key, $ch, $db);
                            if(!$result || isset($result->error_code)){
                                echo '<div class="alert alert-danger">' . str_replace('{x}', (isset($result->error_message) ? Output::getClean($result->error_message) . ' (' . Output::getClean($result->error_code) . ')' : ''), $buycraft_language->get('language', 'unable_to_get_bans')) . '</div>';
                            } else {
                                echo '<div class="alert alert-success">' . $buycraft_language->get('language', 'bans_retrieved_successfully') . '</div>';
                            }

                            curl_close($ch);

                        } else {
                            echo '<div class="alert alert-danger">' . $buycraft_language->get('language', 'invalid_server_key');
                        }
                    } else {
                        echo '<div class="alert alert-danger">' . $buycraft_language->get('language', 'invalid_server_key');
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require(ROOT_PATH . '/modules/Core/pages/admin/footer.php'); ?>
<?php require(ROOT_PATH . '/modules/Core/pages/admin/scripts.php'); ?>

</body>
</html>