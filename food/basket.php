<?php 
	
	session_start();
	require "admin/includes/functions.php";
	require "admin/includes/db.php";
	error_reporting(0);
	

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Section 1 (if user attempts to add something to the cart from the product page)
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (isset($_GET['fid']) && isset($_GET['qty'])) {
		
		$fid = $_GET['fid'];
		$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
		$wasFound = false;
		$i = 0;
		// If the cart session variable is not set or cart array is empty
		if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) { 
			
			// RUN IF THE CART IS EMPTY OR NOT SET
			$_SESSION["cart_array"] = array(0 => array("item_id" => $fid, "quantity" => $qty));
		} else {
		echo "not empty";
			$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
		
			// RUN IF THE CART HAS AT LEAST ONE ITEM IN IT
			foreach ($_SESSION["cart_array"] as $each_item) { 
				echo $each_item['item_id'];
				echo"/n";
				echo $fid;
				  $i++;
				  foreach($each_item as $key => $value)  {
					echo "////";
					
					  if ($key == "item_id" && $value == $fid) {
						  // That item is in cart already so let's adjust its quantity using array_splice()
						  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $fid, "quantity" => $each_item['quantity'] + $qty)));
						  $wasFound = true;
						  echo " found";
					  } // close if condition
				  } // close while loop
			   } // close foreach loop
			   if ($wasFound == false) {
				echo "not found";
				   array_push($_SESSION["cart_array"], array("item_id" => $fid, "quantity" => $qty));
			   }else{
				echo "fffff";
			   }
		}
		
		header("location: basket.php"); 
		
	}
	

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Section 2 (if user chooses to empty their shopping cart)
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (isset($_GET['cmd']) && $_GET['cmd'] == "emptycart") {
		echo "22222222222";
		unset($_SESSION["cart_array"]);
	}
	

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Section 4 (if user wants to remove an item from cart)
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if (isset($_POST['index_to_remove']) && $_POST['index_to_remove'] != "") {
		echo "3333333333";
		// Access the array and run code to remove that array index
		$key_to_remove = $_POST['index_to_remove'];
		if (count($_SESSION["cart_array"]) <= 1) {
			unset($_SESSION["cart_array"]);
		} else {
			unset($_SESSION["cart_array"]["$key_to_remove"]);
			sort($_SESSION["cart_array"]);
		}
	}
	


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Section 5  (render the cart for the user to view on the page)
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$cartOutput = "";
	$cartTotal = 0;
	$chkbtn = "";
	$empty_cart = "";
	$chkprice = "";
	$product_id_array = "";
	
	if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) {
		
		$cartOutput = "<h3 style=' text-align: center; font-weight: lighter; padding: 10px 0px; background: #ffeeee; color: #333;'>Your shopping basket is empty</h3>";
		
	}else{
		echo "4gdgfsg";
		$cartOutput = "<div class='single_order_head'>
				
							<h3>Food</h3>
							<h3>Price(N)</h3>
							<h3>Qty</h3>
							<h3>Total</h3>
							<h3>Remove</h3>
							
						</div>";
						
		
		$i = 0;
		
		foreach ($_SESSION["cart_array"] as $each_item) { 
			echo "sdfff";
			$item_id = $each_item['item_id'];
		
			$sql = $db->query("SELECT * FROM food WHERE id='$item_id' LIMIT 1");
			while ($row = $sql->fetch_assoc()) {
				
				$foodName = $row['food_name'];
				$price = $row['food_price'];
				echo $foodName;
				echo $price;
				
			}
			// $pricetotal = $price * $each_item['quantity'];
			$pricetotal = $price * $each_item['quantity'];

			$cartTotal  =+ $pricetotal ;
			
			// Dynamic Checkout Btn Assembly
			$x = $i + 1;
			
			$empty_cart = '<div class="empty_cart">
				
								<a href="basket.php?cmd=emptycart">Empty Basket</a>
								
							</div>';
			
			$chkbtn = '<div class="checkout">
				
							<a href="#" onclick="show_overlay(); return false">Checkout</a>
							
						</div>';
			
			// Create the product array variable
			$product_id_array .= "$foodName-".$each_item['quantity'].", "; 
			
			$cartOutput .= '<form style="display: inline; padding: 0; margin: 0;" action="basket.php" method="post">
			
				<div class="single_order">
					
					<p>' . $foodName . '</p>
					<p>' . $price . '</p>
					<p><select name="quantity" id="'.$item_id.'" onChange="update_qty(\''.$item_id.'\', \''.$cartTotal.'\', \''.$pricetotal.'\')"> 
						'.render_options($each_item['quantity'], $item_id).'
					</select></p>
					<p id="ajax_qty_'.$item_id.'">'.$pricetotal.'</p>
					<p><input name="deleteBtn' . $item_id . '" class="remove" onclick="return verify_choice();" type="submit" value="x" /><input name="index_to_remove" type="hidden" value="' . $i . '" /></p>
					
				</div>
			
			</form>';
			
			$chkprice .= '<input type="hidden" id="chkprice" name="chkprice" value="'.$cartTotal.'" />';
			$chkfood = '<input type="hidden" id="chkfood" name="chkfood" value="'.$product_id_array.'" />';
				
			$i++; 
		}
		
		$cartTotal = '<p class="p_total"><span>Basket Total</span> : #<span id="cart-total">'.$cartTotal.'</span></p>';
		
	}
	
?>
<style>
	.block {
    width: 100%;
  padding:12px;
  font-size: 17px;
  background: #27ae60;
  color:#fff;
  margin-top: 5px;
  cursor: pointer;
}
.rblock {
    width: 100%;
  padding:12px;
  font-size: 17px;
  margin-top: 5px;
  cursor: pointer;
}
@media all and (max-width:30em){
.rblock{
     display:block;
     margin:0.4em auto;
}
}
@media all and (max-width:30em){
.block{
     display:block;
     margin:0.4em auto;
}
}
</style>

<!Doctype html>

<html lang="en">

<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<meta name="description" content="" />

<meta name="keywords" content="" />

<head>
	
<title>Food cafe</title>

<link rel="stylesheet" href="css/main.css" />

<script src="js/jquery.min.js" ></script>

<script src="js/myscript.js"></script>

</head>

<body>
	
<?php require "includes/header.php"; ?>

<div class="parallax_basket" onclick="remove_class()">
	
	<div class="parallax_head_basket">
		
		<h2>Your</h2>
		<h3>Basket</h3>
		
	</div>
	
</div>

<div class="content remove_pad" onclick="remove_class()">
	
	<div class="inner_content on_parallax">
		
		<h2><span class="cart">Food Basket</span></h2>
		
		<div class="order_holder">
			
			<?php echo $cartOutput; ?>
			
		</div>
		
		<?php echo $cartTotal; ?>
		
		<div class="checkout_section">
			
			<?php echo $empty_cart; ?>
			
			<?php echo $chkbtn; ?>
			
		</div>
		
	</div>
	
</div>

<div class="content" onclick="remove_class()">
	
	<div class="inner_content">
		
		<div class="contact">
			
			<div class="left">
				
				<h3>LOCATION</h3>
				<p>Al ameen college, Edathala</p>
				<p>Edathala</p>
				
			</div>
			
			<div class="left">
				
				<h3>CONTACT</h3>
				<p>1234567890</p>
				<p>Demo@gmail.com</p>
				
			</div>
			
			<p class="left"></p>
			
			<div class="icon_holder">
				
				<a href="#"><img src="image/icons/Facebook.png" alt="image/icons/Facebook.png" /></a>
				<a href="#"><img src="image/icons/Google+.png" alt="image/icons/Google+.png"  /></a>
				<a href="#"><img src="image/icons/Twitter.png" alt="image/icons/Twitter.png"  /></a>
				
			</div>
			
		</div>
		
	</div>
	
</div>

<div class="footer_parallax" onclick="remove_class()">
	
	<div class="on_footer_parallax">
		
		<p>&copy; <?php echo strftime("%Y", time()); ?> <span>Food cafe</span>. All Rights Reserved</p>
		
	</div>
	
</div>

<!---------Contact Information---------->

<div class="overlay" id="overlay" onclick="hide_overlay()"></div>
	
	<div class="info_holder">
		
		<p class="close_p"><span class="close_sp" onclick="hide_overlay()"></span></p>
		
		<h2><span class="tag">Complete Your Order</span></h2>
		
		<form method="post" action="" onSubmit="validate_input(); return false">
			
			<div class="form_group">
					 
				<div class="form_group">
					
					<label>Name</label>
					<input type="text" id="name" name="name" placeholder="Enter your full name" required>
					
				</div>
				
				<div class="form_group">
					
					<label>Address</label>
					<input type="text" id="addr"  name="addr" placeholder="Enter your address" required>
					
				</div>
				
				<div class="form_group">
					
					<label>Email</label>
					<input type="Email" id="email" name="email" placeholder="Enter your email" required>
					
				</div>
				
				<div class="form_group">
					
					<label>Phone Number</label>
					<input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
					
					<?php echo $chkfood; ?>
					
					<?php echo $chkprice; ?>
					
				</div>

				<div class="form_group">
					<label>Reservation <small><b>(optional. If you want to go reservation page click here)</b></small></label>
				<button class="rblock"onclick = "window.location.href='reservation.php';">Reservation</botton>
                </div>

				<div class="form_group">
					<label>Payment</label>
				<button class="block"onclick = "window.location.href='payment.html';"><b>PAYMENT</b></botton>
              </div>
				
				<div class="form_group">
					
					<input type="submit" class="submit" value="PLACE ORDER" />
					
				</div>
				
			</div>
			
		</form>
		
	</div>

</body>

</html>