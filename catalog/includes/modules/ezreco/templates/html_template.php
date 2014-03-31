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
/*
    This template is the default rendering for the recommendation module.
    Available variables are:
    $ezreco_products : Array of product
        $ezreco_product : One product containing: 
        	- name
        	- link
        	- image
        	- price
        	- special_price
*/
$DISPLAY_PRODUCT_PER_LINE = 3;

?>
<div class="ui-widget infoBoxContainer">
	<div class="ui-widget-header ui-corner-top infoBoxHeading">
		<span><?php echo isset($titleDefinedName)?constant($titleDefinedName):EZ_RECO_TEXT_PRODUCT ?></span>
	</div>

	<table class="ui-widget-content ui-corner-bottom" width="100%" cellspacing="0" cellpadding="2" border="0">
		<tbody>
			<tr>
				<?php $ezreco_p_per_line = 0;
					foreach ($ezreco_products as $ezreco_product) { 
                		if($ezreco_p_per_line >= $DISPLAY_PRODUCT_PER_LINE) {
                			$ezreco_p_per_line = 0;
        		?>
      		</tr><tr>
        		<?php   } ?>
       
        		<td width="33%" valign="top" align="center" >
	      			<a onmousedown='javascript:_ezaq.push(["setCustomVariable", 110, "rclicked", "<?php echo $ezreco_product['id'] ?>" ]);_ezaq.push(["trackPageView", "reco"]);' 
	      			    href="<?php echo $ezreco_product['link'] ?>"><?php echo $ezreco_product['image'] ?>
	      				<br />
	      				<?php echo $ezreco_product['name'] ?> 
		    			<?php if (tep_not_null($ezreco_product['special_price'])) {?>
		      			<strike><?php echo $ezreco_product['price'] ?></strike> <strong><?php echo $ezreco_product['special_price'] ?></strong>
		    			<?php } else { ?>
			  			<strong><?php echo $ezreco_product['price'] ?></strong>
		    			<?php }?>
	      			</a>
        		</td>
       
        		<?php   $ezreco_p_per_line++;
              			} ?>
			</tr>		
		</tbody>	
	</table>
</div>
