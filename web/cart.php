<?php
include('header.php');

?>

<br>

<article>

<div class="textBack" align="left" style="float:top; padding-bottom: 50px"" >

<h1>Cart</h1>
<br>
<br>
<br>
<hr name = "productLine">

<?php 
include('connectionSQL.php');
$userId = $_SESSION['user_id'];
//print "hello <br>";
if(isset($_GET['removeId'])) {
	$gameId = $_GET['removeId'];

	$removeCartQuery = 'DELETE FROM shopping_cart WHERE  user_id = ' . '"' . $userId . '"' . ' and game_id = ' . '"' . $gameId . '"';
	
	@mysqli_query($link, $removeCartQuery);

}
if(isset($_GET['gameId'])) {
	$gameId = $_GET['gameId'];
	$getCartQuery = 'SELECT * FROM shopping_cart inner join games using (game_id) where user_id = ' . '"' . $userId . '"';
$result = mysqli_query($link, $getCartQuery);
$quantityDefault = 1;
 	if ($result)   {
     	while ($row = mysqli_fetch_array($result)) {
     		$priceDuplicate = $row['price'];
        	 if($gameId==$row['game_id']){
        	 	$duplicate = true; 

  			}
		}		
	}
		//TEST VALUE change to $_SESSION later
			//INITIAL INSERT VALUE -- implement the number input box to do update query on this in the value.

	$addToCartQuery = "INSERT INTO shopping_cart (user_id, game_id, quantity) VALUES ('$userId', '$gameId', '$quantityDefault')";
	@mysqli_query($link, $addToCartQuery);
}

//use to get which game_id(s) and theyre quantities from shopping cart to store in variables based on userId
$getCartQuery = 'SELECT * FROM shopping_cart inner join games using (game_id) where user_id = ' . '"' . $userId . '"';
//remove from cart button query

//print "remove cart query: " . $removeCartQuery;
$result = mysqli_query($link, $getCartQuery);
 if ($result)   {
     $row_count = mysqli_num_rows($result);
     //print 'Retreived '. $row_count . ' rows from the <b> games </b> table<BR><BR>';
     $count = 0;
     
     while ($row = mysqli_fetch_array($result)) {
         //print $row['name'] . '<br>' .
     	//print_r($_COOKIE[$currGameId]);
     	$currGameId = $row['game_id'];
     	if(isset($_COOKIE[$currGameId])){
     		$quantity = $_COOKIE[$currGameId];
     		//print $quantity;
     	}
     	else{
     		$quantity = 1;
     	}

      
        $price = $row['price'];
          print '<form method ="POST" action='.'"'.'cart.php?removeId='.$currGameId.'"><div class="clearfix">' . 
          '<img class =' . '"' . 'images' . '"' . 'src =' . '"' . $row['image'] . '">
          <p class="gameName">' . $row['name'] . '</p><br>' .
          '<span class="consoleName">' . $row['console_name'] . '<br>' .'</span><br>' . '<br><br>
          <div id=' .'"' . 'price'. $currGameId .'"'.'>'. $price*$quantity . '</div>
          <input id=' .'"' . 'quantity'. $currGameId .'"'. 'style=float:right;  type="textbox" value='. $quantity.'>
          <input style=float:right; type="button" value="+" onclick='.'"'.'addQ('.$currGameId.','.$price.')"'. '>
          <input style=float:right; type="button" value="-" onclick='.'"'.'remQ('.$currGameId.','.$price.')"'. '>
          <input type="submit" value="Remove" id=' .'"' . 'remove'. $currGameId .'"'.'></form></div>';
         echo '<hr name = "productLine">';
         $totalPrice = $totalPrice + $price;
         $gameArray[$count]=$row['game_id'];
         $count = $count + 1;
         
     }
     
 }

$_GET['count'] = $count;

$quantityQuery = 'UPDATE shopping_cart set quantity =' . $quantity .   'where game_id ='. $gameId .  'and user_id=' . $userId;
//echo json_encode($gameArray);
?>
<div id="total" style=float:right;></div><br><br>
<script>//to change the quantity and individual prices
	function addQ(count,price) {
		var quantity = document.getElementById('quantity'+ count).value;
		quantity++;
		document.getElementById('quantity'+count).value = quantity;
		var cookieString = count + "=" + quantity;  
		document.cookie = cookieString;

		 document.getElementById('price' + count).innerHTML = (price * quantity).toFixed(2);
		 calcTotal(<?php echo json_encode($gameArray); ?>);
	}
	function remQ(count,price) {
		var quantity = document.getElementById('quantity'+ count).value;
		quantity--;
		if(quantity == 0){
			document.getElementById('remove' + count).click();
		}
		document.getElementById('quantity'+count).value = quantity;
		var cookieString = count + "=" + quantity; 
		document.cookie = cookieString;

		 document.getElementById('price' + count).innerHTML = (price * quantity).toFixed(2);
		 calcTotal(<?php echo json_encode($gameArray); ?>);
	}
	function calcTotal(gameArray){
		console.log(gameArray[0]);
		var total = 0;
		for (var i = 0; i < gameArray.length; i++) {
			total = total + Number(document.getElementById('price' + gameArray[i]).innerHTML);
		}
		document.getElementById('total').innerHTML = total.toFixed(2);
	}
	
	calcTotal(<?php echo json_encode($gameArray); ?>);
</script>
<?php  
if($duplicate == true){
     	echo '<script>addQ('.$gameId.','.$priceDuplicate.')</script>';	
     	}
?>
<input type="submit" name="checkout" value="Checkout" style="float: right;">

</div>
</article>
<br>
<br>
<br>
<br>




</article>

<?php
include "footer.php";
?>
