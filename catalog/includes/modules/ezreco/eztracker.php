<?php
/*
 * Plugin for osCommerce by Ezako.
 * osCommerce is an Open Source E-Commerce Solutions http://www.oscommerce.com 
 * 
 * Copyright (c) 2013 Ezako Released under the 
 * GNU General Public License 
 * Get an account to the recommendation for free 
 * (1 month testing) 
 * http://www.ezako.com
 * 
 * THIS SOFTWARE IS PROVIDED BY EZAKO SAS ''AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL EZAKO SAS OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation
 * are those of the authors and should not be interpreted as representing
 * official policies, either expressed or implied, of EZAKO SAS.
 * 
 */

// The configuration directory
$DIR_RECO = DIR_WS_MODULES . 'ezreco/';

// Load configurations
include_once ($DIR_RECO . 'config.php');

?>
<script type="text/javascript"> 
var _ezaq = _ezaq || [];
<?php if ((basename($PHP_SELF) == FILENAME_DEFAULT) && isset($HTTP_GET_VARS['cPath'])) { ?>
_ezaq.push(['setCustomVariable', 1, "type", "category" ]);

<?php   global $current_category_id;
	  	if (isset($current_category_id) && ($current_category_id != '')) {
?>
_ezaq.push(['setCustomVariable', 5, "cat", "<?php echo ezt_categories($current_category_id) ?>" ]);
<?php 	} ?>
<?php } else if (basename($PHP_SELF) == FILENAME_DEFAULT) { ?>
_ezaq.push(['setCustomVariable', 1, "type", "home" ]);
<?php } else if (basename($PHP_SELF) == FILENAME_PRODUCT_INFO) { ?>
_ezaq.push(['setCustomVariable', 1, "type", "product" ]);
	<?php if (isset($HTTP_GET_VARS['products_id'])) { ?>
_ezaq.push(['setCustomVariable', 2, "pid", "<?php echo ezt_clean_pid($HTTP_GET_VARS['products_id']) ?>" ]);
_ezaq.push(['setCustomVariable', 5, "cat", "<?php echo ezt_categories(ezt_clean_pid($HTTP_GET_VARS['products_id']), 'product') ?>" ]);
_ezaq.push(['setCustomVariable', 3, "pname", "<?php echo tep_get_products_name(ezt_clean_pid($HTTP_GET_VARS['products_id'])) ?>" ]);
_ezaq.push(['setCustomVariable', 9, "price", "<?php echo ezt_get_products_price(ezt_clean_pid($HTTP_GET_VARS['products_id'])) ?>" ]);
_ezaq.push(['setCustomVariable', 8, "brand", "<?php echo ezt_get_products_brand(ezt_clean_pid($HTTP_GET_VARS['products_id'])) ?>" ]);
		<?php $ezt_sprice = tep_get_products_special_price(ezt_clean_pid($HTTP_GET_VARS['products_id']));
			  if ($ezt_sprice != '') {?>
_ezaq.push(['setCustomVariable', 10, "sprice", "<?php echo $ezt_sprice ?>" ]);
		<?php } ?>
	<?php } ?>
	
<?php } else if (basename($PHP_SELF) == FILENAME_SHOPPING_CART) { ?>
_ezaq.push(['setCustomVariable', 1, "type", "viewbasket" ]);
<?php $ezt_cart=ezt_cart_products(); ?>
_ezaq.push(['setCustomVariable', 2, "pid", "<?php echo implode("|", $ezt_cart['pid']); ?>" ]);
_ezaq.push(['setCustomVariable', 13, "quantity", "<?php echo implode("|", $ezt_cart['quantity']); ?>" ]);

<?php } else if ( (basename($PHP_SELF) == FILENAME_CHECKOUT_PAYMENT)
				|| (basename($PHP_SELF) ==FILENAME_CHECKOUT_PAYMENT_ADDRESS) 
				|| (basename($PHP_SELF) ==FILENAME_CHECKOUT_PROCESS)
				|| (basename($PHP_SELF) ==FILENAME_CHECKOUT_SHIPPING)
				|| (basename($PHP_SELF) ==FILENAME_CHECKOUT_SHIPPING_ADDRESS)
				|| (basename($PHP_SELF) ==FILENAME_CHECKOUT_CONFIRMATION) ) { ?>
_ezaq.push(['setCustomVariable', 1, "type", "order" ]);
<?php $ezt_cart = ezt_cart_products();?>
_ezaq.push(['setCustomVariable', 2, "pid", "<?php echo implode("|", $ezt_cart['pid']) ?>" ]);
_ezaq.push(['setCustomVariable', 13, "quantity", "<?php echo implode("|", $ezt_cart['quantity']); ?>" ]);

<?php } else if (basename($PHP_SELF) == FILENAME_CHECKOUT_SUCCESS) { 
		$ez_cart_suc = ezt_cart_success();
?>
_ezaq.push(['setCustomVariable', 1, "type", "order_conf" ]);
_ezaq.push(['setCustomVariable', 2, "pid", "<?php echo implode('|', $ez_cart_suc['pid']) ?>" ]);
_ezaq.push(['setCustomVariable', 12, "total", "<?php echo $ez_cart_suc['total'] ?>" ]);
_ezaq.push(['setCustomVariable', 13, "quantity", "<?php echo implode('|', $ez_cart_suc['quantity']) ?>" ]);
_ezaq.push(['setCustomVariable', 22, "subtotal", "<?php echo $ez_cart_suc['subtotal'] ?>" ]);
_ezaq.push(['setCustomVariable', 23, "tax", "<?php echo $ez_cart_suc['tax'] ?>" ]);
_ezaq.push(['setCustomVariable', 24, "shipping", "<?php echo $ez_cart_suc['shipping'] ?>" ]);

<?php } else { ?>
_ezaq.push(['setCustomVariable', 1, "type", "other" ]);
<?php } ?>

<?php if (tep_session_is_registered('customer_id')) { ?>
_ezaq.push(['setCustomVariable', 16, "userid", "<?php echo $customer_id ?>" ]);
<?php } ?>

_ezaq.push(['setCustomVariable', 15, "sessionid", "<?php echo session_id() ?>" ]);

_ezaq.push(['setSiteId', '<?php echo EZ_RECO_IDSITE ?>']);
//_ezaq.push(['setTrackerUrl', '<?php echo EZ_RECO_SERVER_URL?>/pixel/pixel.png']);
_ezaq.push(['setTrackerUrl', '<?php echo str_replace('http:', '', EZ_RECO_SERVER_URL) ?>/pixel/pixel.png']);

_ezaq.push(['trackPageView']); 
_ezaq.push(['enableLinkTracking']); 
(function(){ 
var u=('https:' == document.location.protocol ? '<?php echo HTTPS_SERVER . DIR_WS_HTTPS_CATALOG?>' : '<?php echo HTTP_SERVER . DIR_WS_HTTP_CATALOG?>') + '/ext/ezjs/ez-analytics-min.js';
var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.defer=true; g.async=true; g.src=u; 
s.parentNode.insertBefore(g,s); })();
</script>

<?php 
// Utility functions
function ezt_get_products_price($product_id) {
	$product_query = tep_db_query("select products_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
	$product = tep_db_fetch_array($product_query);
	return $product['products_price'];
}

function ezt_get_products_brand($product_id) {
	$product_query = tep_db_query("select manufacturers_name from " . TABLE_PRODUCTS . " P," . TABLE_MANUFACTURERS . " M where products_id = '" . (int)$product_id . "' and P.manufacturers_id=M.manufacturers_id");
	$product = tep_db_fetch_array($product_query);
	return $product['manufacturers_name'];
}

// For some reasons these function is missing in general.php in some versions
function ezt_tep_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
	global $languages_id;

	if (!is_array($categories_array)) $categories_array = array();

	if ($from == 'product') {
		$categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$id . "'");
		while ($categories = tep_db_fetch_array($categories_query)) {
			if ($categories['categories_id'] == '0') {
				$categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
			} else {
				$category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
				$category = tep_db_fetch_array($category_query);
				$categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
				if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = ezt_tep_category_path($category['parent_id'], 'category', $categories_array, $index);
				$categories_array[$index] = $categories_array[$index];
			}
			$index++;
		}
	} elseif ($from == 'category') {
		$category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
		$category = tep_db_fetch_array($category_query);
		$categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
		if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = ezt_tep_category_path($category['parent_id'], 'category', $categories_array, $index);
	}

	return $categories_array;
}

function ezt_categories($id, $type='category') {
	$cat = '';
	foreach ( ezt_tep_category_path($id, $type) as $ez_collector_cat_g ) {
		foreach (array_reverse($ez_collector_cat_g) as $ez_collector_cat) {
			if ($cat != '') $cat .= '|';
			$cat .= $ez_collector_cat['text'];
		}
	}
	return $cat;
}

function ezt_clean_pid($id) {
	$pos = strpos($id, '{');
	if($pos === FALSE) return $id;
	$pid = substr($id, 0, $pos);
	return $pid;
}

function ezt_cart_products() {
	global $cart;
	$res = array();
	$prod = array();
	$res['pid'] = &$prod;
	$quantity=array();
	$res['quantity']= &$quantity;
	
	if ( !isset($cart) || $cart==null || ($cart->count_contents() <= 0))
		return $res;
	
	$products = $cart->get_products();
	for ($i=0, $n=sizeof($products); $i<$n; $i++) {
		$prod[]= ezt_clean_pid($products[$i]['id']);
		$quantity[]= $products[$i]['quantity'];
	}
	
	return $res;
}

function ezt_cart_success() {
	$res = array();
	$res['pid'] = array();
	$res['total'] = '';
	$res['quantity'] = array();
	$res['subtotal'] = 0;
	$res['tax'] = 0;
	$res['shipping'] = 0;
	//$res['discount'] = '';
	
	global $customer_id;
	if (!isset($customer_id) || $customer_id == '' )
		return $res;
	
	$order_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$customer_id . "' order by date_purchased desc limit 1");
	if (tep_db_num_rows($order_query) != 1) 
		return $res;
	$lorder = tep_db_fetch_array($order_query);
	$order_products_query = tep_db_query("select op.products_id, op.final_price, op.products_quantity, op.products_tax from " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_LANGUAGES . " l where op.orders_id = '" . (int)$lorder['orders_id'] . "' and l.code = '" . tep_db_input(DEFAULT_LANGUAGE) . "'");
	while ($order_products = tep_db_fetch_array($order_products_query)) {
		$res['pid'][] = $order_products['products_id'];
		$res['quantity'][] = $order_products['products_quantity'];
		$res['tax'] += (($order_products['final_price'] * $order_products['products_tax'] / 100) * $order_products['products_quantity']);
		//$res['total'] += ($order_products['final_price'] * ((int)$order_products['products_quantity']));
	}
	
	$total_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where class='ot_total' and orders_id = '" . (int)$lorder['orders_id'] . "' limit 1");
	if (tep_db_num_rows($total_query) == 1) {
		$restot = tep_db_fetch_array($total_query);
		$res['total'] = $restot['value'];
	}
	$subtotal_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where class='ot_subtotal' and orders_id = '" . (int)$lorder['orders_id'] . "' limit 1");
	if (tep_db_num_rows($subtotal_query) == 1) {
		$subtotal = tep_db_fetch_array($subtotal_query);
		$res['subtotal'] = $subtotal['value'];
	}
	$shipping_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where class='ot_shipping' and orders_id = '" . (int)$lorder['orders_id'] . "' limit 1");
	if (tep_db_num_rows($shipping_query) == 1) {
		$restship = tep_db_fetch_array($shipping_query);
		$res['shipping'] = $restship['value'];
	}
	return $res;
}

?>