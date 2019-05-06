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
$category_id=(isset($_POST['category_id']))?$_POST['category_id']:'1';

?>
<div class="row adobintegration_content adobe_items media_lib_container" data-type="lib_media"  data-id="<?php echo $category_id;?>" >
	<?php
	foreach($contentData['files'] as $key => $data) {?>
		<div class="col-md-2 col-lg-3 col-sm-3 col-xs-6">
				<div class="image_box">
					
							<div class="media_item">
								<a href="<?php echo $data['thumbnail_1000_url'];?>" data-rel="prettyPhoto" title="<?php echo $data['title'];?>" data-thumbnail_url="<?php echo $data['thumbnail_1000_url'];?>" data-id="<?php echo $data['id'];?>" data-type="media" data-title="<?php echo $data['title'];?>"  data-stock_id="<?php echo $data['id'];?>" onclick="//get_media(this);">
								<img class="lazy-load" src="<?php echo $data['thumbnail_1000_url']; ?>" data-src="<?php echo $data['thumbnail_1000_url']; ?>" data-srcset="<?php echo $data['thumbnail_1000_url']; ?>" >
								</a>
							</div>
							<div class="fusion-product-content">
									<div class="product-details">
										<div class="product-details-container">
											<h3 class="product-title" data-fontsize="18" data-lineheight="33">
												<a href="javascript:void(0)"><?php echo substr($data['title'],0,25).'...'; ?></a>
											</h3>
											<div class="fusion-price-rating">
												<span class="price">From <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>100.00</span></span>
													</div>
										</div>
									</div>
									<div class="product-buttons">
										<div class="fusion-content-sep sep-double sep-solid"></div>
										<div class="product-buttons-container clearfix">
											<div class="tinv-wraper woocommerce tinv-wishlist tinvwl-after-add-to-cart tinvwl-loop-button-wrapper">
												<a class="tinvwl_add_to_wishlist_button tinvwl-icon-heart no-txt  tinvwl-position-after" data-tinv-wl-list="[{&quot;ID&quot;:1,&quot;title&quot;:&quot;Default wishlist&quot;,&quot;url&quot;:&quot;https:\/\/dev.derbywallprints.us\/wishlist\/471360\/&quot;,&quot;in&quot;:false}]" data-tinv-wl-product="19992" data-tinv-wl-productvariation="0" data-tinv-wl-producttype="simple" data-tinv-wl-action="addto" rel="nofollow"></a>		
												<div class="tinvwl-tooltip">Add to Wishlist</div>
											</div>
											<button type="submit" class="fusion-button button-default fusion-button-default-size button create" name="create" value="create" data-thumbnail_url="<?php echo $data['thumbnail_1000_url'];?>" data-name="<?php echo $data['title'];?>" data-stock-id="<?php echo $data['id'];?>" onclick="create_adobe_product(this);">Create</button>
										</div>
									</div>
							</div>
				</div>
		</div>
	<?php 
		if(($key+1)%4==0) echo '</div><div class="row adobintegration_content adobe_items media_lib_container">';

	} ?>

</div>
<?php
$offset=(isset($_POST['offset'])) ? ($_POST['offset']+$this->page_limit):(count($contentData['files']));
$do_infinite = ($contentData['nb_results'] <= $this->page_limit) ? 'false' : 'true' ;
?>
<input type="hidden" name="do_lib_infinite" class="do_lib_infinite" value="<?php echo $do_infinite; ?>" />
<script type="text/javascript">
	adobeintegration.lib_offset='<?php echo $offset; ?>';
</script>