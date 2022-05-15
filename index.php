<?php
session_start();
$_SESSION['num_rooms'] = $_POST['num_rooms'];
?>
<!DOCTYPE html>
<html>
<head>
   <title></title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   </head>
   <body>
   <form method='POST'>
   <h2>Input number of rooms (between 2 and 100):</h2>
   <p>(Sometimes you get fewer rooms than you requested, this happens when new rooms connect back to existing neighbors).</p>
 <input type="text" name="num_rooms">
 <input type="submit" value="Generate">
 </form>
<br>
<img src="mapgen.php" alt="generated image"/>
</body>
</html>
