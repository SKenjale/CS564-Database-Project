<?php
   $nat = $_POST['nat'];
   echo $nat;
   echo "\n";
   $nat = trim($nat);
   $nat = "%$nat%";
   $data = array();
   $con = new mysqli("localhost", "root", "Snehal1995%", "hotel_reviews");

   if($con->connect_error){
    die("Failed to connect: ".$con->connect_error);
   } else {

    if(strcasecmp($nat, "") == 0){
        exit("Please enter your nationality!");
    }

    $stmt = $con->prepare("select hotel_name, address,avg_score,total_reviews from Hotel where Hotel.hotel_id in (select distinct hotel_id from Review where Review.pos_word_count < Review.neg_word_count and Review.reviewer_id = (select Reviewer.reviewer_id from Reviewer where Reviewer.nationality LIKE ?)) ORDER BY Hotel.avg_score DESC, Hotel.total_reviews DESC;");
    $stmt->bind_param("s", $nat);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    if($stmt_result->num_rows > 0){
    $data = $stmt_result->fetch_all(MYSQLI_ASSOC);
    header('Content-type: text/plain');
    echo "<pre>".print_r($data)."</pre>";
    } else {
        echo "<h2>There are no good hotels liked most by the people of your nationality!</h2>";
    }

   }
?>