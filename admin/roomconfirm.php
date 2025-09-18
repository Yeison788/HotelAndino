<?php

include '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql ="Select * from roombook where id = '$id'";
$re = mysqli_query($conn,$sql);
while($row=mysqli_fetch_array($re))
{
	$Name = $row['Name'];
    $Email = $row['Email'];
    $Country = $row['Country'];
    $Phone = $row['Phone'];
    $RoomType = $row['RoomType'];
    $Bed = $row['Bed'];
    $NoofRoom = $row['NoofRoom'];
    $Meal = $row['Meal'];
    $cin = $row['cin'];
    $cout = $row['cout'];
    $noofday = $row['nodays'];
    $stat = $row['stat'];
    $totalPrice = isset($row['total_price']) ? (float)$row['total_price'] : 0;
}


if($stat == "NotConfirm")
{
    $st = "Confirm";

    $sql = "UPDATE roombook SET stat = '$st' WHERE id = '$id'";
    $result = mysqli_query($conn,$sql);

    if($result){
        $guestCount = max(1, (int)$Bed);
        $roomsBooked = max(1, (int)$NoofRoom);
        $daysBooked = max(1, (int)$noofday);

        $ratePerNight = 60000 + max(0, $guestCount - 1) * 25000;
        $roomTotal = $ratePerNight * $daysBooked * $roomsBooked;
        if ($totalPrice > 0) {
            $roomTotal = $totalPrice;
        }

        $mealRates = [
            'Room only'  => 0,
            'Breakfast'  => 15000,
            'Half Board' => 28000,
            'Full Board' => 42000,
            'Solo habitación' => 0,
            'Desayuno' => 15000,
            'Media pensión' => 28000,
            'Pensión completa' => 42000,
        ];
        $mealRate = $mealRates[$Meal] ?? 0;
        $mealTotal = $mealRate * $guestCount * $daysBooked;

        $bedTotal = 0;
        $finalTotal = $roomTotal + $mealTotal;

        if ($stmt = $conn->prepare("INSERT INTO payment(id,Name,Email,RoomType,Bed,NoofRoom,cin,cout,noofdays,roomtotal,bedtotal,meal,mealtotal,finaltotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param(
                'issssissiddsdd',
                $id,
                $Name,
                $Email,
                $RoomType,
                $Bed,
                $roomsBooked,
                $cin,
                $cout,
                $noofday,
                $roomTotal,
                $bedTotal,
                $Meal,
                $mealTotal,
                $finalTotal
            );
            $stmt->execute();
            $stmt->close();
        }

        header("Location:roombook.php");
    }
}
// else
// {
//     echo "<script>alert('Guest Already Confirmed')</script>";
//     header("Location:roombook.php");
// }


?>