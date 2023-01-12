<?php
  
   $name = $_POST['hotelName'];
   $name = trim($name);
   $name = "%$name%";
   
   $pos = $_POST['pos'];
   $pos = trim($pos);
   $neg = $_POST['neg'];
   $neg = trim($neg);
   $pos_word_count = 1;
   $neg_word_count = 1;
   
   $nat = $_POST['nat'];
   $nat = trim($nat);
   $nat = "%$nat%";
   $score = $_POST['score']; $score = trim($score);
   $temp = array();

   $con = new mysqli("localhost", "root", "Snehal1995%", "hotel_reviews");
   if($con->connect_error){
    die("Failed to connect: ".$con->connect_error);
   } else {

    if(strcasecmp($name, "") == 0){
       exit("Please enter something in the hotel name field!");
    } else if(strcasecmp($pos, "") == 0){
        exit("Please enter something in the positive review field!");
    } else if(strcasecmp($neg, "") == 0){
        exit("Please enter something in the negative review field!");
    }else if(strcasecmp($nat, "") == 0){
        exit("Please enter your nationality!");
    }else if($score < 0 || $score > 10.0){
        exit("Please enter a score between 0.0 and 10.0!");
    } 

    $stmt = $con->prepare("select hotel_name from Hotel where Hotel.hotel_name LIKE ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    $correctHotel = $stmt_result->fetch_all(MYSQLI_ASSOC);
    
    if($stmt_result->num_rows == 0){
        exit("No hotels of this name found! Please retry!");
    }else if($stmt_result->num_rows > 1){
        exit("Please enter a more determinative hotel name!");
    } 

    if(strcasecmp($pos, "No positive") == 0){ // No positive review given by the user
      $pos_word_count = 0;
    } else if(strcasecmp($neg, "No negative") == 0){ // No negative review given by the user
      $neg_word_count = 0;
    }

    
    $stmt = $con->prepare("select reviewer_id from Reviewer where Reviewer.nationality LIKE ?");
    $stmt->bind_param("s", $nat);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    if($stmt_result->num_rows != 1){
        exit ("<h2>Please enter a valid nationality!</h2>");
    }
    $temp = $stmt_result->fetch_assoc();
    $reviewer_id = (int) $temp['reviewer_id'];
    
    $stmt = $con->prepare("select hotel_id from Hotel where Hotel.hotel_name LIKE ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    $temp = $stmt_result->fetch_assoc();
    $hotel_id = (int) $temp['hotel_id'];
    $date = date("m/d/Y");

    $stmt = $con->prepare("select review_id from Review ORDER BY review_id DESC LIMIT 1");
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    $temp = $stmt_result->fetch_assoc();
    $reviewID = (int) $temp['review_id'];
    $reviewID++;

    $stmt = $con->prepare("alter table Review AUTO_INCREMENT = $reviewID");
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    $stmt = $con->prepare("insert into Review(date, negative_review, neg_word_count, positive_review, pos_word_count, score, hotel_id, reviewer_id) values (?,?,?,?,?,?,?,?);");
    $stmt->bind_param("ssssssii", $date, $neg, $neg_word_count, $pos,$pos_word_count, $score, $hotel_id, $reviewer_id);
    $stmt->execute();
    $stmt_result = $stmt->get_result();

    $stmt = $con->prepare("update Hotel set hotel.avg_score = (select AVG(score) from Review where Review.hotel_id = ?) where hotel.hotel_id = ?;");
    $stmt->bind_param("ii", $hotel_id, $hotel_id);
    $stmt->execute();
    $stmt->get_result();

    $stmt = $con->prepare("update Hotel set total_reviews = total_reviews + 1 where hotel_id = ?;");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();

    if(!$stmt){
      echo "Insert failed!";
    }
    
    echo "<h2> Inserted Review Successfully! </h2>";
}
?>