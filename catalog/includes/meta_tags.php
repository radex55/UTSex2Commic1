<?php
	if(!empty($HTTP_GET_VARS['products_id'])){
		// modified by Sean McCabe - 25-03-2014
		//Step 1. Construct the general Query!

		$metaQuery  = "SELECT pd.products_description as `products_description`, pd.products_name as `products_name`, p.products_image as `products_image`, ";
		$metaQuery  .= "cd.categories_name as `categories_name`, m.manufacturers_name as `manufacturers_name` ";
		$metaQuery  .= "FROM products p, products_description pd, products_to_categories pc,";
		$metaQuery  .= "categories c, categories_description cd, languages l, manufacturers m, configuration cf ";
		$metaQuery  .= "WHERE pd.products_id = '".$HTTP_GET_VARS['products_id']."' AND p.products_id = pd.products_id AND pd.language_id = l.languages_id ";
		$metaQuery  .= "AND pd.products_id = pc.products_id AND pc.categories_id = c.categories_id ";
		$metaQuery  .= "AND c.categories_id = cd.categories_id AND cd.language_id = l.languages_id ";
		$metaQuery  .= "AND p.manufacturers_id = m.manufacturers_id ";
		$metaQuery  .= "AND p.products_status = 1 AND cf.configuration_key = 'DEFAULT_LANGUAGE' ";
		$metaQuery  .= "AND l.code = cf.configuration_value ";

		//Step 2. Narrow the search!

		//Are we looking within a category?
		if (isset($cPath) && tep_not_null($cPath))
		{
			$metaCategoryArray = explode ("_",$cPath);
			if (strpos($cPath, '_'))
			{
				$metaCategoryArray  = array_reverse($metaCategoryArray);
			}

			$metaCategoryId = $metaCategoryArray[0];

			$metaQuery .= "AND c.categories_id = '" . $metaCategoryId . "' ";
		}

		//Are we looking within a manufacturer?
		if (isset($manufacturers_id) && tep_not_null($manufacturers_id))
		{

			$metaManufacturersId = $manufacturers_id;

			$metaQuery .= "AND m.manufacturers_id = '" . $metaManufacturersId . "' ";
		}

		//Step 3. Extract the info from the DB
		$metaQueryResult = tep_db_query ( $metaQuery );

		$metaProductsNames = array();
		$metaProductsDescr = array();
		$metaProductsImg = array();
		$metaCategoriesNames = array();
		$metaManufacturersNames = array();
		$metaProductsDescr = array();

		//Step 4. Remove duplicates by using the name as the key in an array
		while($metaQueryData = tep_db_fetch_array ($metaQueryResult))
		{
			$metaProductsNames[$metaQueryData['products_name']] = $metaQueryData['products_name'];
			$metaProductsDescr[$metaQueryData['products_description']] = $metaQueryData['products_description'];
			$metaProductsImg[$metaQueryData['products_image']] = $metaQueryData['products_image'];
			$metaCategoriesNames[$metaQueryData['categories_name']] = $metaQueryData['categories_name'];
			$metaManufacturersNames[$metaQueryData['manufacturers_name']] = $metaQueryData['manufacturers_name'];
		}

		//Step 4.5. Remove any duplicate keywords, but 


		//Step 5. Construct the keywords
		$metaKeywords = "";
		$keywords_array = array();
		$keywords = 0; //set keyword start
		$keywords_max = 20; //set max. number of keywords (recommended)

		foreach($metaProductsNames as $metaProductsName){
			$metaProductsName = explode(" ", $metaProductsName);
			foreach($metaProductsName as $Keyword){
				if(!in_array($Keyword, $keywords_array)){
					$keywords_array[] = $Keyword;
				}
			}
		}

		foreach($metaCategoriesNames as $metaCategoriesName){
			$metaCategoriesName = explode(" ", $metaCategoriesName);
			foreach($metaCategoriesName as $Keyword){
				if(!in_array($Keyword, $keywords_array)){
					$keywords_array[] = $Keyword;
				}
			}
		}

		foreach($keywords_array as $keywordinsert){
			$keywords++;
			if($metaKeywords == ""){
				$metaKeywords = $keywordinsert;
			} elseif($keywords <= $keywords_max){
				$metaKeywords .= ", ".$keywordinsert."";
			}
		}

		//Limit the keywords to 1000 characters
		$metaKeywords = substr($metaKeywords, 0, 999);

		//Step 6. Construct the description

		$metaDescription = "";
		foreach($metaProductsDescr as $metaProductDescr)
		{
			if($metaProductDescr == "")
			{
				//No previous entries
				$metaDescription = $metaProductDescr;
			}
			else
			{
				//Other Rows
				$metaDescription .= $metaProductDescr;
			}
		}
		
		//Step 7. Construct the product image
		
		$metaImg = "";
		$metaImgloop = 1;
		foreach($metaProductsImg as $metaProductImg)
		{
			if($metaImgloop == 1){
				$metaImg = $metaProductImg;
			} else {
				$metaImgloop++;
				break; //GET ONLY ONE IMAGE
			}
		}
		$metaImg = tep_href_link(DIR_WS_IMAGES . $metaImg, '', 'NONSSL', false);

		//Limit the description to xxx characters
		$length = 250;
		// for delete " in description
		while (strstr($metaKeywords, '"')) $metaKeywords = str_replace('"', '', $metaKeywords);
		while (strstr($metaDescription, '"')) $metaDescription = str_replace('"', '', $metaDescription);
		$metaDescription = strip_tags($metaDescription);
		$metaDescription = substr($metaDescription, 0, strrpos(substr($metaDescription, 0, $length), ' '));
		$metaDescription = rtrim($metaDescription);
		//add continuation
		$metaDescription .= "...";
		
		// echo '<!-- query = ' . $metaQuery . '"-->' . "\n";
		echo '<meta name="keywords" content="' . $metaKeywords . '" />' . "\n";
		echo '<meta name="description" content="' . $metaDescription . '" />' . "\n";
		echo '<!-- Open Graph Tags -->' . "\n";
		echo '<meta property="og:type" content="product" />' . "\n";
		echo '<meta property="og:image" content="'.$metaImg.'" />' . "\n";
		echo '<meta property="og:url" content="'.HTTP_SERVER.''.$_SERVER['REQUEST_URI'].'" />' . "\n";
		echo '<meta property="og:description" content="'.$metaDescription.' "/>' . "\n";
	}
?>