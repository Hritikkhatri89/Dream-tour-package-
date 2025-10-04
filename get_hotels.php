<?php
include("db.php");
if(isset($_GET['star'])){
    $star = intval($_GET['star']);
    $sql = mysqli_query($conn,"SELECT * FROM hotels WHERE star='$star'");
    if(mysqli_num_rows($sql)>0){
        while($hotel = mysqli_fetch_assoc($sql)){
            echo '<div style="text-align:center;margin-right:10px;">
                <img src="hotel image/'.$hotel['image'].'" class="hotel-img" data-id="'.$hotel['id'].'"><br>
                <button class="btn btn-sm btn-info read-more mt-1" data-id="'.$hotel['id'].'">Read More</button>
            </div>';
        }
    } else{
        echo "<p>No $star star hotels found.</p>";
    }
}
?>
