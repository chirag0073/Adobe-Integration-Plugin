<?php

/**
 * Provide a public post view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       himanshu.u
 * @since      1.0.0
 *
 * @package    Adobintegration
 * @subpackage Adobintegration/public/partials
 */
?>

<?php
$contentData = json_decode($contentRes,true);

?>

<div class="raw adobintegration breadcrumb">
    <div class="col-md-6 col-lg-12 col-sm-12 col-xs-12">
        <span class="categories">Categories:</span>
        <span class="categories-name-1"></span>
        <span class="categories-name-2"></span>
    </div>
</div>
<div id="adobintegration_media" >
	<div class="row adobintegration_content">	
	<?php

	foreach($contentData as $key => $data){?>
		<div class="col-md-3 col-lg-3 col-sm-6 col-xs-12">
				<a href="javascript:void(0);" data-id="<?php echo $data['id'];?>" data-type="subcategory" data-name="<?php echo $data['name'];?>" onclick="
				get_media(this);">
					<div class="categories">				
						<h4><label><?php echo $data['name']; ?></label></h4>
					</div>
				</a>
		</div>
	<?php 

	if(($key+1)%4==0) echo '</div><div class="row adobintegration_content">';

	} ?>
	</div>
</div>