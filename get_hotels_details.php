<?php
include("db.php");
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = mysqli_query($conn,"SELECT * FROM hotels WHERE id='$id'");
    if(mysqli_num_rows($sql)>0){
        $hotel = mysqli_fetch_assoc($sql);
        echo json_encode([
            "id"=>$hotel['id'],
            "name"=>$hotel['name'],
            "price"=>$hotel['price'],
            "room_facilities"=>$hotel['room_facilities'],
            "services"=>$hotel['services'],
            "amenities"=>$hotel['amenities'],
            "image"=>'hotel image/'.$hotel['image']
        ]);
    } else {
        echo json_encode(["error"=>"Hotel not found"]);
    }
}
?>
