<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require(DIR_WS_INCLUDES . 'counter.php');
?>
<div class="grid_24 infoBoxContainer">
<ul class="footer">
   <li><a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo HEADER_TITLE_TOP ?></a></li>
   <li><a href="<?php echo tep_href_link(FILENAME_PRODUCTS_NEW);?>"><?php echo BOX_HEADING_WHATS_NEW ?></a></li>
   <li><a href="<?php echo tep_href_link(FILENAME_SPECIALS);?>"><?php echo BOX_HEADING_SPECIALS ?></a></li>
   <li><a href="<?php echo tep_href_link(FILENAME_CONTACT_US);?>"><?php echo BOX_INFORMATION_CONTACT?></a></li>
<li><a href="<?php echo tep_href_link('advanced_search.php')?>"><?php echo BOX_SEARCH_ADVANCED_SEARCH?></a></li>
<li><a href="<?php echo tep_href_link('reviews.php')?>"><?php echo BOX_HEADING_REVIEWS?></a></li><? if (tep_session_is_registered('customer_id')) { 
?><li><a href="<?php echo tep_href_link('account.php')?>"><?php echo HEADER_TITLE_MY_ACCOUNT?></a></li><? } else 
{ ?><li><a href="<?php echo tep_href_link('create_account.php')?>"><?php echo HEADER_TITLE_CREATE_ACCOUNT?></a></li><?php } 
?>
  </ul> 
</div>

<div class="grid_24">
  <p align="center"><?php echo FOOTER_TEXT_BODY; ?></p>
<p align="center">Created by: <a href="http://www.oscommerceforyou.hu" target="_blank">O.F.Y.</a></p>
<p align="center"><a href="http://www.oscommerce.com" target="_blank">osCommerce</a> Online Merchant</p>
</div>

<script type="text/javascript">
$('.productListTable tr:nth-child(even)').addClass('alt');
</script>
