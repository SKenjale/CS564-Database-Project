<?php
   $name = $_POST['hotelName'];
   $pos = $_POST['pos'];
   $name = "%$name%";
   $data = array();
   $con = new mysqli("localhost", "root", "Snehal1995%", "hotel_reviews");
   if($con->connect_error){
    die("Failed to connect: ".$con->connect_error);
   } else {
    if(strcasecmp($pos, "positive") == 0){
      $stmt = $con->prepare("select Review.positive_review, Hotel.hotel_name from Review, Hotel where Review.pos_word_count != 0 and Review.hotel_id in (select hotel_id from Hotel where Hotel.hotel_name LIKE ?) and Review.hotel_id = Hotel.hotel_id");
    } else if(strcasecmp($pos, "negative") == 0){
        $stmt = $con->prepare("select Review.negative_review, Hotel.hotel_name from Review, Hotel where Review.neg_word_count != 0 and Review.hotel_id in (select hotel_id from Hotel where Hotel.hotel_name LIKE ?) and Review.hotel_id = Hotel.hotel_id");
    } else if(strcasecmp($pos, "all") == 0) {
        $stmt = $con->prepare("select Review.positive_review, Review.negative_review, Hotel.hotel_name from Review, Hotel where Review.hotel_id in (select hotel_id from Hotel where Hotel.hotel_name LIKE ?) and Review.hotel_id = Hotel.hotel_id");
    } else {
        exit("<h2>Please enter either 'Positive', 'Negative' or 'All'!</h2>");
    }
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    if($stmt_result->num_rows > 0){
    $data = $stmt_result->fetch_all(MYSQLI_ASSOC);
    header('Content-type: text/plain');
    echo "<pre>".print_r($data)."</pre>";
    } else {
        echo "<h2>Please enter a correct hotel name!</h2>";
    }
   }
?>