<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Latest purchases widget settings
 */

// Check input
$cache->setCache('buycraft_data');

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		if(isset($_POST['limit']) && $_POST['limit'] > 0)
			$cache->store('purchase_limit', (int)$_POST['limit']);
		else
			$cache->store('purchase_limit', 10);

	} else {
		$error = $language->get('general', 'invalid_token');
	}
}

if($cache->isCached('purchase_limit'))
	$purchase_limit = (int)$cache->retrieve('purchase_limit');
else
	$purchase_limit = 10;
?>

<h4 style="display:inline;"><?php echo str_replace('{x}', Output::getClean($widget->name), $language->get('admin', 'editing_widget_x')); ?></h4>
<span class="pull-right">
    <a class="btn btn-warning"
       href="<?php echo URL::build('/admin/widgets/', 'action=edit&amp;w=' . $widget->id); ?>"><?php echo $language->get('general', 'back'); ?></a>
</span>
<br /><br />

<?php
if(isset($error))
	echo '<div class="alert alert-danger">' . $error . '</div>';
?>

<form action="" method="post">
	<div class="form-group">
		<label for="inputPackageLimit"><?php echo $buycraft_language->get('language', 'latest_purchases_limit'); ?></label>
		<input id="inputPackageLimit" name="limit" type="number" min="1" class="form-control" placeholder="<?php echo $buycraft_language->get('language', 'latest_purchases_limit'); ?>" value="<?php echo $purchase_limit; ?>">
	</div>
	<div type="form-group">
		<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
		<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
	</div>
</form>
<br />
<div class="alert alert-info"><?php echo $buycraft_language->get('language', 'latest_posts_widget_cached'); ?></div>
