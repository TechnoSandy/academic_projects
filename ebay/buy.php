<html>



<head>
	<title>Buy Products</title>
	<h1>Name:Sandeep Satone | UTA ID:1001556868 </h1>
	<h1>Project:Programming Assignment 4 PHP Scripting</h1>
</head>
<!--
NOTE: API key used is not generated and the demo API Key is used in this project DEMO API KEY URL:https://ebaycommercenetwork.force.com/publisher/s/article/API-Demo
-->

<body>
	<?php
	session_start();
	if(isset($_GET["buy"])) {	
	foreach ($_SESSION['products'] as $productId=>$id) {
		if($_GET["buy"] == $productId) {
			$_SESSION['cart'][(string)$productId] = array($id[0], $id[1], $id[2]);			
			}
		}
	}
	
	if(isset($_GET["delete"])) {
	foreach ($_SESSION['cart'] as $productId=>$id) {
			if($_GET["delete"] == $productId) {
			unset($_SESSION['cart'][(string)$productId]);
			}
		}
	}
	
	if(isset($_GET["clear"])) {
		foreach ($_SESSION['cart'] as $productId=>$id) {			
			unset($_SESSION['cart'][(string)$productId]);
		}
	}
	
	if(isset($_SESSION['cart'])) {
	print '<p>Shopping Basket :</p>';
	print '<table>';
	print '<tr>';
	print '<th>' . 'Name' . '</th>';
//	print '<th>' . 'Description' . '</th>';
	print '<th>' . 'BasePrice' . '</th>';
	print '<th>' . 'Remove' . '</th>';
	print '</tr>';
	foreach ($_SESSION['cart'] as $productId=>$id) {
        echo "<tr>";
        echo "<td>.$id[0].</td>";
//        echo "<td>".$id[1]."</td>";
		echo "<td><label>$</label>".$id[2]."</td>";
        echo "<td><a href=buy.php?delete=".(string)$productId.">delete</a></td>";
        echo "</tr>";
    }
		print '</table>';		
		$cartArray = $_SESSION['cart'];
		calculateTotalCost($cartArray);
	}
	
	function calculateTotalCost($cartArray){
		$total = 0;
		foreach ($cartArray as $productId=>$id) {
		$total = $total + $id[2];		
    	}
		$_SESSION['sumTotalofCost'] = $total;
		echo "<hr> Total Cost :  $ ".$_SESSION['sumTotalofCost']."<hr>";
		echo "<form action='buy.php' method='get'>";
		echo "<input type='hidden' name='clear' value='1'> ";
		echo "<input type='submit' value='Empty Cart'/>";
		echo "</form>";
	}
	?>
	
<!--SEARCH FORM STARTS-->
<form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
<fieldset>
<legend>Find Products: </legend>
<?php
$categories = "http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true";

$allCategoriesAndSubcategories = new SimpleXMLElement(file_get_contents($categories));
	print '<label>Category: <select name="category">';
	print '<option value="' . $allCategoriesAndSubcategories->category['id'] . '">' . $allCategoriesAndSubcategories->category->name . '</option>';
	print '<optgroup label="' . $allCategoriesAndSubcategories->category->name . ':">';
foreach ($allCategoriesAndSubcategories->category->categories->category as $category) {	
    print '<option value="' . $category['id'] . '">' . $category->name . '</option>';
    print '<optgroup label="' . $category->name . ':">';
    foreach ($category->categories->category as $childValues) {
        if (!empty($childValues)) {
            print '<option value="' . $childValues['id'] . '">' . $childValues->name . '</option>';
        }
    }
}
print '</select></label>';
?>

					<label>Search Keywords : <input type="text" name="product"/></label>
					<input type="submit" value="Search" name="submit" />
			</fieldset>
		</form>

</body>
<!--SEARCH FORM ENDS-->

<!--Searching Products-->
<?php
//SEARCH USING API CALL
function searchProducts()
{
    $searchURL = "http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610";
    if (strlen($_GET['product']) > 0) {
        $searchURL         = $searchURL . '&categoryId=' . $_GET["category"] . '&keyword=' . urlencode($_GET["product"]) . '&numItems=20';
        $productsAvailable = file_get_contents($searchURL);
		if (isset($productsAvailable)) {
            $productsAvailableXMLFormat = new SimpleXMLElement($productsAvailable);
            showProducts($productsAvailableXMLFormat);
        }
    } else {
        echo "<h1>NO PRODUCT TO SEARCH</h1>";
    }
    
}
	
//SHOW PRODUCT TABLE
function showProducts($productsAvailableXMLFormat)
{
		if($productsAvailableXMLFormat->categories->intActualCategoryCount !=0){ 
			print '<table>';
			print '<tr>';
			print '<th>' . 'Name' . '</th>';
			print '<th>' . 'Description' . '</th>';
			print '<th>' . 'BasePrice' . '</th>';
			print '</tr>';
			foreach ($productsAvailableXMLFormat->categories->category->items->offer as $individual_item) {
			$_SESSION['products'][(string)$individual_item['id']] = array(
				(string)$individual_item->name,
				(string)$individual_item->description,
				(float)$individual_item->basePrice
			);
 			print '<tr>';
			print '	<td><a href="buy.php?buy='. $individual_item['id'].'">' . $individual_item->name . '</a></td>';
			print '<td>' . $individual_item->description . '</td>';
			print '<td>'.'$'. $individual_item->basePrice . '</td>';
			print '</tr>';			
			}
			print '</table>';	
		}
		else{
			echo "<h1>NO PRODUCT FOUND!</h1>";
		}     

}

//CLICK HANDLER FOR SEARCH BUTTON
if (isset($_GET['submit'])) { searchProducts(); }
?>

	<link rel="stylesheet" href="CSS/buy.css">

</html>
