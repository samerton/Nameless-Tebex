<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Featured package widget settings
 */

// Check input
$cache->setCache('buycraft_data');

if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$packages = array();

		if(isset($_POST['featured_packages']) && count($_POST['featured_packages'])){
			foreach($_POST['featured_packages'] as $package){
				$packages[] = intval($package);
			}
		}

		$cache->store('featured_packages', $packages);

	} else {
		$error = $language->get('general', 'invalid_token');
	}
}

if($cache->isCached('featured_packages'))
	$featured_packages = $cache->retrieve('featured_packages');
else
	$featured_packages = array();

$packages = $queries->getWhere('buycraft_packages', array('id', '<>', 0));
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

<div class="alert alert-info"><?php echo $buycraft_language->get('language', 'featured_packages_info'); ?></div>

<form action="" method="post">
	<div class="form-group">
		<label for="inputFeaturedPackages"><?php echo $buycraft_language->get('language', 'featured_packages'); ?></label> <small><?php echo $buycraft_language->get('language', 'select_multiple_with_ctrl'); ?></small>
		<select class="form-control" name="featured_packages[]" id="inputFeaturedPackages" multiple>
			<?php
			foreach($packages as $package){
				echo '<option value="' . Output::getClean($package->id) . '"' . (in_array($package->id, $featured_packages) ? ' selected' : '') . '>' . Output::getClean($package->name) . '</option>';
			}
			?>
		</select>
	</div>
	<div type="form-group">
		<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
		<input type="submit" class="btn btn-primary" value="<?php echo $language->get('general', 'submit'); ?>">
	</div>
</form>