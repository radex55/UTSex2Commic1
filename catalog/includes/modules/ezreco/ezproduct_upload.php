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

// Refresh all products on the server with a merchant feed format

// Do we have a context?
if(!defined('EZ_RECO_IDSITE')) {
	// This is standalone mode
	chdir(dirname(__FILE__) . '/../../../');
	
	// Load framework config
	include_once 'includes/application_top.php';
	
	// Load configurations
	$DIR_RECO = DIR_WS_MODULES . 'ezreco/';
	include_once ($DIR_RECO . 'config.php');
}
/*
id
title
description: submit around 500 to 1,000 characters
price: '15.00 USD' optional currency, should be without VAT for the us, TVA included in europe
sale_price: prix soldé (no value if not on sale)
condition: new/used/refurbished
link
availability: 'in stock' /'available for order'/'out of stock'/'preorder'
product_type: catégorie " > " separated
image_link
brand: marque: 
color : optional
size: optional
*/

// Queries
$SELECT_EZ_PRODUCTS = "select p.products_id, p.products_price, p.products_status, p.products_image, p.manufacturers_id, DATE(p.products_date_added) as date_added, products_tax_class_id from " . TABLE_PRODUCTS . " p order by p.products_id";

global $languages_id, $currency;

$updata_products = array();
$up_query_p = tep_db_query( $SELECT_EZ_PRODUCTS );
while ($productLine = tep_db_fetch_array($up_query_p)) {
	$ez_pid = $productLine['products_id'];
	
	// Manufacturer
	$ez_collection_manufacturer_query = tep_db_query("select manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id=" . $productLine['manufacturers_id'] );
	$ez_collection_manufacturer_res = tep_db_fetch_array($ez_collection_manufacturer_query);
	$ez_collection_manufacturer_name = $ez_collection_manufacturer_res['manufacturers_name'];
	// Categories
	$cat = '';
	foreach (pu_tep_category_path($ez_pid, 'product') as $ez_collector_cat_g ) {
		foreach ($ez_collector_cat_g as $ez_collector_cat) {
			if ($cat != '') $cat .= ' > ';
			$cat .= $ez_collector_cat['text'];
		}
	}
	
	$sPrice = tep_add_tax(tep_get_products_special_price($ez_pid), tep_get_tax_rate($productLine['products_tax_class_id']));
	
	$updata_products[] = array(
			'id' => $ez_pid ,
			'title' => tep_get_products_name($ez_pid),
			'price' => tep_add_tax($productLine['products_price'], tep_get_tax_rate($productLine['products_tax_class_id'])) . " " . $currency ,
			//'price' => $currencies->display_price($productLine['products_price'], tep_get_tax_rate($productLine['products_tax_class_id'])),
			'sale_price' => ( ($sPrice==0)? "": $sPrice . " " . $currency),
			//'sale_price' => ((tep_get_products_special_price($ez_pid)!=0)?$currencies->display_price(tep_get_products_special_price($ez_pid), tep_get_tax_rate($productLine['products_tax_class_id'])):""),
			'condition' => 'new',
			'link' => HTTP_SERVER . "/product_info.php?products_id=" . $ez_pid,
			'availability' => ($productLine['products_status']==1?'in stock':'out of stock'),
			'product_type' => $cat,
			'image_link' => HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES .$productLine['products_image'],
			'brand' => $ez_collection_manufacturer_name,
			//'color' => null,
			//'size' => null,
			'description' => ez_clean_txt( substr(pu_get_products_description($ez_pid, $languages_id),0,1010))
	);
}

$rtext = "id\t";
$rtext .= "title\t";
$rtext .= "price\t";
$rtext .= "sale_price\t";
$rtext .= "condition\t";
$rtext .= "link\t";
$rtext .= "availability\t";
$rtext .= "product_type\t";
$rtext .= "image_link\t";
$rtext .= "brand\t";
//$rtext .= "color\t";
//$rtext .= "size\t";
$rtext .= "description\n";
foreach ($updata_products as $up_product) {
	$rtext .= implode("\t", $up_product);
	$rtext .= "\n";
}

//print_r($rtext);

// Send to server
$ch = curl_init();
//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, EZ_RECO_SERVER_URL . "/pixel/products.txt");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'idsite: ' . EZ_RECO_IDSITE,
		'token: ' . EZ_RECO_PRIVATE_TOKEN,
		// 'Content-Type: text/plain; charset='. mb_internal_encoding()
		'Content-Type: text/plain; charset='. defined('CHARSET')?constant('CHARSET'):mb_internal_encoding()
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, $rtext);
//execute post
curl_exec($ch);
//close connection
curl_close($ch);

// For some reasons these function is missing in general.php in some versions
function pu_tep_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
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
				if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = pu_tep_category_path($category['parent_id'], 'category', $categories_array, $index);
				$categories_array[$index] = array_reverse($categories_array[$index]);
			}
			$index++;
		}
	} elseif ($from == 'category') {
		$category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
		$category = tep_db_fetch_array($category_query);
		$categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
		if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = pu_tep_category_path($category['parent_id'], 'category', $categories_array, $index);
	}

	return $categories_array;
}
function pu_get_products_description($product_id, $language_id) {
	$product_query = tep_db_query("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = tep_db_fetch_array($product_query);

	return $product['products_description'];
}

function ez_clean_txt($string) {
	
	// ----- remove HTML TAGs -----
	$string = preg_replace ('/<[^>]*>/', ' ', $string);
	
	// ----- remove control characters -----
	$string = str_replace("\r", '', $string);    // --- replace with empty space
	$string = str_replace("\n", ' ', $string);   // --- replace with space
	$string = str_replace("\t", ' ', $string);   // --- replace with space
	
	// ----- remove multiple spaces -----
	$string = trim(preg_replace('/ {2,}/', ' ', $string));
	
	return $string;
}

