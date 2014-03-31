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
function ezReco($recoType='ALL', $template = 'html_template', $nbRecoMax = 6, $nbRecoMin = 2, $titleDefinedName = null) {
	// The configuration directory
	$DIR_RECO = DIR_WS_MODULES . 'ezreco/';
	
	// Load configurations
	include_once ($DIR_RECO . 'config.php');
	
	$recommendationServerUrl = "%s/reco/?idsite=%s&t=%s&pid=%s&id=%s&type=%s&nb=%d&v=osc-1.6";
	
	global $currencies, $language, $_COOKIE, $HTTP_GET_VARS, $ignoreRecoIds, $curlRecoConn;
	
	$productId = isset($HTTP_GET_VARS['products_id'])?$HTTP_GET_VARS['products_id']:'';
	// clean productId if necessary
	$pos = strpos($productId, '{');
	if($pos !== FALSE) $productId = substr($productId, 0, $pos);
	
	$userId = '';
	foreach ($_COOKIE as $cookieKey => $cookieValue) {
		$cookiePkid = str_replace('.', "_", "_pk_id." . EZ_RECO_IDSITE);
		if (!strncmp($cookieKey, $cookiePkid, 7 + strlen(EZ_RECO_IDSITE))) {
			$userId = $cookieValue;
			break;
		}
	}

	// Init ignore list
	if (!isset($ignoreRecoIds)) $ignoreRecoIds = array();
	// Add ignore products to list
	if (!empty($ignoreRecoIds)) {
		foreach ($ignoreRecoIds as $ignoreRecoId) {
			$recommendationServerUrl .= "&ig=$ignoreRecoId";
		}
	}

	//error_log(sprintf ( $recommendationServerUrl, EZ_RECO_SERVER_URL, EZ_RECO_IDSITE, EZ_RECO_PRIVATE_TOKEN, $productId, $userId, $recoType, $nbRecoMax ));
	// Get the remote recommended products
	if (!isset($curlRecoConn)) $curlRecoConn=curl_init();
	curl_setopt( $curlRecoConn, CURLOPT_URL, sprintf ( $recommendationServerUrl, EZ_RECO_SERVER_URL, EZ_RECO_IDSITE, EZ_RECO_PRIVATE_TOKEN, $productId, $userId, $recoType, $nbRecoMax ) );
	curl_setopt( $curlRecoConn,CURLOPT_FAILONERROR, true);
	curl_setopt( $curlRecoConn, CURLOPT_TIMEOUT, 1 );
	curl_setopt( $curlRecoConn, CURLOPT_RETURNTRANSFER, 1 );
	$recoText = curl_exec( $curlRecoConn );
	
	if ($recoText === false) {
		error_log("EZreco module: connection error on connection: " . curl_error($curlRecoConn) );
		$ezreco_reco = null;
	} else {
		$ezreco_reco = json_decode ( $recoText, true );
		if (isset($ezreco_reco['error']))
			error_log("EZreco module: ".$ezreco_reco['error']);
	}
	
	// Do we need an automatic refresh?
	if (EZ_RECO_AUTO_UPDATE && isset ( $ezreco_reco ['needrefresh'] ) && $ezreco_reco ['needrefresh'] == "true") {
		// Automatic products refresh, might take some times to be taken into account
		include_once ($DIR_RECO . 'ezproduct_upload.php');
	}
	// The result is valid ?
	if (isset ( $ezreco_reco ['ids'] ) && $ezreco_reco ['count'] > 0) {
		// Query the products details		
		$ezreco_reco_query = tep_db_query ( "select p.products_id, p.products_image, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p where p.products_id in( " . implode ( ",", $ezreco_reco ['ids'] ) . ") and p.products_status = '1' " . "order by FIELD(p.products_id," . implode ( ",", $ezreco_reco ['ids'] ) . ") limit " . $nbRecoMax );
		$ezreco_num_products_reco = tep_db_num_rows ( $ezreco_reco_query );
		
		if ($ezreco_num_products_reco >= $nbRecoMin) {
			// loads the main bean for rendering page
			$ezreco_products = array ();
			while ( $ezreco_orders = tep_db_fetch_array ( $ezreco_reco_query ) ) {
				$ezreco_product = array ();
				$ezreco_product ['name'] = tep_get_products_name ( $ezreco_orders ['products_id'] );
				$ezreco_product ['link'] = tep_href_link ( FILENAME_PRODUCT_INFO, 'products_id=' . $ezreco_orders ['products_id'] );
				$ezreco_product ['image'] = tep_image ( DIR_WS_IMAGES . $ezreco_orders ['products_image'], $ezreco_product ['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT );
				//$ezreco_product ['text'] = $ezreco_orders ['products_name'];
				$ezreco_product ['price'] = $currencies->display_price( $ezreco_orders ['products_price'], tep_get_tax_rate( $ezreco_orders ['products_tax_class_id'] ) );
				$ezreco_product ['special_price'] = tep_get_products_special_price ( $ezreco_orders ['products_id'] );
				if (tep_not_null ( $ezreco_product ['special_price'] )) {
					$ezreco_product ['special_price'] = $currencies->display_price ( $ezreco_product ['special_price'], tep_get_tax_rate ( $ezreco_orders ['products_tax_class_id'] ) );
				}
				$ezreco_product['id'] = $ezreco_orders ['products_id'];
				$ezreco_products [] = $ezreco_product;
				// Add this product to ignore list for this request
				$ignoreRecoIds[] = $ezreco_orders ['products_id'];
			}
			
			// Get languages
			include_once ($DIR_RECO . 'languages/' . $language . '.php');
			// Render the result
			include ($DIR_RECO . 'templates/' . $template . '.php');
		} // else Not enough results to be displayed
	} // else The result is invalid or nothing, we do not show
}
// List of products to ignore (already shown to user)
$ignoreRecoIds;
$curlRecoConn;
