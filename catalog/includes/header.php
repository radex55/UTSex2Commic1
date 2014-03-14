<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  if ($messageStack->size('header') > 0) {
    echo '<div class="grid_24">' . $messageStack->output('header') . '</div>';
  }
?>

<div id="header">
<div  class="container_<?php echo $oscTemplate->getGridContainerWidth(); ?>">
  <div id="storeLogo"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'store_logo.png', STORE_NAME) . '</a>'; ?></div>

  <div id="headerShortcuts"><?php if (!tep_session_is_registered('customer_id')) { 
	 $error = false;
  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && isset($HTTP_POST_VARS['formid']) && ($HTTP_POST_VARS['formid'] == $sessiontoken)) {
    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);
    $password = tep_db_prepare_input($HTTP_POST_VARS['password']);

// Check if email exists
    $check_customer_query = tep_db_query("select customers_id, customers_firstname, customers_password, customers_email_address, customers_default_address_id from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "'");
    if (!tep_db_num_rows($check_customer_query)) {
      $error = true;
    } else {
      $check_customer = tep_db_fetch_array($check_customer_query);
// Check that password is good
      if (!tep_validate_password($password, $check_customer['customers_password'])) {
        $error = true;
      } else {
        if (SESSION_RECREATE == 'True') {
          tep_session_recreate();
        }

// migrate old hashed password to new phpass password
        if (tep_password_type($check_customer['customers_password']) != 'phpass') {
          tep_db_query("update " . TABLE_CUSTOMERS . " set customers_password = '" . tep_encrypt_password($password) . "' where customers_id = '" . (int)$check_customer['customers_id'] . "'");
        }

        $check_country_query = tep_db_query("select entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$check_customer['customers_id'] . "' and address_book_id = '" . (int)$check_customer['customers_default_address_id'] . "'");
        $check_country = tep_db_fetch_array($check_country_query);

        $customer_id = $check_customer['customers_id'];
        $customer_default_address_id = $check_customer['customers_default_address_id'];
        $customer_first_name = $check_customer['customers_firstname'];
        $customer_country_id = $check_country['entry_country_id'];
        $customer_zone_id = $check_country['entry_zone_id'];
        tep_session_register('customer_id');
        tep_session_register('customer_default_address_id');
        tep_session_register('customer_first_name');
        tep_session_register('customer_country_id');
        tep_session_register('customer_zone_id');

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1, password_reset_key = null, password_reset_date = null where customers_info_id = '" . (int)$customer_id . "'");

// reset session token
        $sessiontoken = md5(tep_rand() . tep_rand() . tep_rand() . tep_rand());

// restore cart contents
        $cart->restore_contents();

        if (sizeof($navigation->snapshot) > 0) {
          $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
          $navigation->clear_snapshot();
          tep_redirect($origin_href);
        } else {
          tep_redirect(tep_href_link(basename($PHP_SELF)));
        }
      }
    }
  }  
      if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {
        if (!isset($lng) || (isset($lng) && !is_object($lng))) {
          include(DIR_WS_CLASSES . 'language.php');
          $lng = new language;
        }

        if (count($lng->catalog_languages) > 1) {
          $languages_string = '';
          reset($lng->catalog_languages);
          while (list($key, $value) = each($lng->catalog_languages)) {
            $languages_string .= ' <a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $request_type) . '">' . tep_image(DIR_WS_LANGUAGES .  $value['directory'] . '/images/' . $value['image'], $value['name']) . '</a> ';
          }

         
        }
      }	  
	  
	  echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', '', true); ?>

 <div id="header_link"><?php echo $languages_string; ?>
 <span class="fieldKey"><?php echo ENTRY_EMAIL_ADDRESS; ?></span>
        <span class="fieldValue"><?php echo tep_draw_input_field('email_address'); ?></span>
  
        <span class="fieldKey"><?php echo ENTRY_PASSWORD; ?></span>
        <span class="fieldValue"><?php echo tep_draw_password_field('password'); ?></span>

  <?php echo tep_draw_button(IMAGE_BUTTON_LOGIN, null, null, 'primary'); ?>
   
<?php echo tep_draw_button(HEADER_TITLE_CREATE_ACCOUNT, 'person', tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL')); ?>
   <br /><?php echo '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '"><span style="font-size:1.2em;">' . TEXT_PASSWORD_FORGOTTEN . '</span></a>'; ?></div>
    </form>

<?php } else {?>

 <div id="header_link">

  <?php echo tep_draw_button(HEADER_TITLE_MY_ACCOUNT,  'person', tep_href_link('account.php', '', 'SSL'), 'primary'); ?>
<?php echo tep_draw_button(HEADER_TITLE_LOGOFF, 'triangle-1-e', tep_href_link(FILENAME_LOGOFF, '', 'SSL')); ?>
<span><?php echo $languages_string; ?></span></div>

<?php }?>

 <div id="header_cart">
<p><?php echo BOX_HEADING_SHOPPING_CART;?>: <br /><a href="<?php echo tep_href_link('shopping_cart.php')?>">
						
	<?php if ($cart->count_contents() > 0) {	$cart_text= $cart->count_contents()  .  BOX_SHOPPING_CART_EMPTY . '' ;  } 	else {		
						$cart_text= BOX_SHOPPING_CART_NO_EMPTY ; }
		?>				
						<?php echo $cart_text ?></a>
					
				
<span style="padding-left:0px;"><strong><?php echo  $currencies->format($cart->show_total()) ?></strong></span></p>

</div>
</div>
<ul class="menu">  
          <li class="menu_item <?php if ($PHP_SELF == 'index.php') echo "selected"?>"><a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo HEADER_TITLE_TOP ?></a></li><li class="menu_item <?php if ($PHP_SELF == 'products_new.php') echo "selected"?>"><a href="<?php echo tep_href_link(FILENAME_PRODUCTS_NEW);?>"><?php echo BOX_HEADING_WHATS_NEW ?></a></li><li class="menu_item <?php if ($PHP_SELF == 'specials.php') echo "selected"?>"><a href="<?php echo tep_href_link(FILENAME_SPECIALS);?>"><?php echo BOX_HEADING_SPECIALS ?></a></li><li class="menu_item <?php if ($PHP_SELF == 'contact_us.php')  echo "selected"?>"><a href="<?php echo tep_href_link(FILENAME_CONTACT_US);?>"><?php echo BOX_INFORMATION_CONTACT?></a></li>
   
</ul>
<div class=" search"><?php echo tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get'); ?><input type="text" name="keywords" class="search_go"  value="<?php echo BOX_HEADING_SEARCH; ?>..." onblur="if(this.value=='') this.value='<?php echo BOX_HEADING_SEARCH; ?>...'" onfocus="if(this.value =='<?php echo BOX_HEADING_SEARCH; ?>...' ) this.value=''" /><span class="search_button"><?php echo tep_draw_button(IMAGE_BUTTON_SEARCH, null, null, 'primary'); ?></span></form></div>
  </div>
</div>
<div class="container_<?php echo $oscTemplate->getGridContainerWidth(); ?>">


<?php
  if (isset($HTTP_GET_VARS['error_message']) && tep_not_null($HTTP_GET_VARS['error_message'])) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="headerError">
    <td class="headerError"><?php echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['error_message']))); ?></td>
  </tr>
</table>
<?php
  }

  if (isset($HTTP_GET_VARS['info_message']) && tep_not_null($HTTP_GET_VARS['info_message'])) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="headerInfo">
    <td class="headerInfo"><?php echo htmlspecialchars(stripslashes(urldecode($HTTP_GET_VARS['info_message']))); ?></td>
  </tr>
</table>
<?php
  }
?>
