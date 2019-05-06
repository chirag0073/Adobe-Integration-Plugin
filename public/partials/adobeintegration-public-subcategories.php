<?php

/**
 * Provide a public post view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       himanshu.u
 * @since      1.0.0
 *
 * @package    Crosslink
 * @subpackage Crosslink/public/partials
 */
?>

<?php
	$contentData = json_decode($contentRes,true);
?>

<div class="row adobintegration_content">	
<?php

foreach($contentData as $key => $data){?>
	<div class="col-md-3 col-lg-3 col-sm-2 col-xs-6">
			<a href="javascript:void(0);" id="Test" data-id="<?php echo $data['id'];?>" data-type="media" data-name="<?php echo $data['name'];?>" onclick="
			get_media(this);">
				<div class="subcategories">				
					<h4><label><?php echo $data['name']; ?></label></h4>
				</div>
			</a>
	</div>
<?php 

if(($key+1)%4==0) echo '</div><div class="row adobintegration_content">';

} ?>
</div>
<input type="hidden" name="do_infinite" class="do_infinite" value="true" />