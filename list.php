<?php
   $add = $_POST['address'];
   $add = trim($add);
   $add = "%$add%";

//    if(strcasecmp($add, "") == 0){
//     exit("Please enter something in the required field!");
//    }

   
   $data = array();
   $con = new mysqli("localhost", "root", "Snehal1995%", "hotel_reviews");
   if($con->connect_error){
    die("Failed to connect: ".$con->connect_error);
   } else {

    if(strcasecmp($add, "") == 0){
      $stmt = $con->prepare("select hotel_name, address, avg_score, total_reviews from Hotel");
      $stmt->execute();
      $stmt_result = $stmt->get_result();
    } else {
      $stmt = $con->prepare(" select * from Hotel where Hotel.address_id in (select Address.address_id from Address where Address.hotel_address LIKE ?);");
      $stmt->bind_param("s", $add);
      $stmt->execute();
      $stmt_result = $stmt->get_result();
    }
    
    if($stmt_result->num_rows > 0){
    $data = $stmt_result->fetch_all(MYSQLI_ASSOC);
    header('Content-type: text/plain');
    echo "<pre>".print_r($data)."</pre>";
    } else {
        echo "<h2>No Hotel of this Address found!</h2>";
    }

   }
?>