<?php
   $name = $_POST['hotelName'];
   $name = trim($name);
   $name = "%$name%";
   $data = array();
   $con = new mysqli("localhost", "root", "Snehal1995%", "hotel_reviews");
   if($con->connect_error){
    die("Failed to connect: ".$con->connect_error);
   } else {

    $stmt = $con->prepare("select tag,hotel_name from Tag where Tag.hotel_id in (select Hotel.hotel_id from Hotel where Hotel.hotel_name LIKE ?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    if($stmt_result->num_rows > 0){
    $data = $stmt_result->fetch_all(MYSQLI_ASSOC);
    header('Content-type: text/plain');
    echo "<pre>".print_r($data)."</pre>";
    } else {
        echo "<h2>No Hotel of this name found!</h2>";
    }

   }
?>