<?php

include '../config.php';

// fetch room data
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql ="Select * from roombook where id = '$id'";
$re = mysqli_query($conn,$sql);
while($row=mysqli_fetch_array($re))
{
    $Name = $row['Name'];
    $Email = $row['Email'];
    $Country = $row['Country'];
    $Phone = $row['Phone'];
    $cin = $row['cin'];
    $cout = $row['cout'];
    $noofday = $row['nodays'];
    $stat = $row['stat'];
    $CurrentRoomType = $row['RoomType'];
    $CurrentBed = $row['Bed'];
    $CurrentNoofRoom = $row['NoofRoom'];
    $CurrentMeal = $row['Meal'];
    $CurrentTotal = isset($row['total_price']) ? (float)$row['total_price'] : 0;
}

if (isset($_POST['guestdetailedit'])) {
    $EditName = trim($_POST['Name'] ?? '');
    $EditEmail = trim($_POST['Email'] ?? '');
    $EditCountry = trim($_POST['Country'] ?? '');
    $EditPhone = trim($_POST['Phone'] ?? '');
    $EditRoomType = trim($_POST['RoomType'] ?? '');
    $EditBed = (int)($_POST['Bed'] ?? 0);
    $EditNoofRoom = (int)($_POST['NoofRoom'] ?? 1);
    $EditMeal = trim($_POST['Meal'] ?? '');
    $Editcin = $_POST['cin'] ?? '';
    $Editcout = $_POST['cout'] ?? '';

    $d1 = strtotime($Editcin);
    $d2 = strtotime($Editcout);
    $Editnodays = ($d1 && $d2 && $d2 > $d1) ? max(1, (int)ceil(($d2 - $d1) / 86400)) : 1;

    $guestCount = max(1, $EditBed);
    $roomsBooked = max(1, $EditNoofRoom);
    $guestCountValue = (string)$guestCount;

    $ratePerNight = 60000 + max(0, $guestCount - 1) * 25000;
    $totalPrice = $ratePerNight * $Editnodays * $roomsBooked;

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
    $mealRate = $mealRates[$EditMeal] ?? 0;
    $mealTotal = $mealRate * $guestCount * $Editnodays;
    $bedTotal = 0.0;
    $finalTotal = $totalPrice + $mealTotal;

    if ($stmt = $conn->prepare("UPDATE roombook SET Name = ?, Email = ?, Country = ?, Phone = ?, RoomType = ?, Bed = ?, NoofRoom = ?, Meal = ?, cin = ?, cout = ?, nodays = ?, total_price = ? WHERE id = ?")) {
        $stmt->bind_param(
            'ssssssisssiddi',
            $EditName,
            $EditEmail,
            $EditCountry,
            $EditPhone,
            $EditRoomType,
            $guestCountValue,
            $roomsBooked,
            $EditMeal,
            $Editcin,
            $Editcout,
            $Editnodays,
            $totalPrice,
            $id
        );
        $stmt->execute();
        $stmt->close();
    }

    if ($stmt = $conn->prepare("UPDATE payment SET Name = ?, Email = ?, RoomType = ?, Bed = ?, NoofRoom = ?, Meal = ?, cin = ?, cout = ?, noofdays = ?, roomtotal = ?, bedtotal = ?, mealtotal = ?, finaltotal = ? WHERE id = ?")) {
        $stmt->bind_param(
            'ssssisssiddddi',
            $EditName,
            $EditEmail,
            $EditRoomType,
            $guestCountValue,
            $roomsBooked,
            $EditMeal,
            $Editcin,
            $Editcout,
            $Editnodays,
            $totalPrice,
            $bedTotal,
            $mealTotal,
            $finalTotal,
            $id
        );
        $stmt->execute();
        $stmt->close();
    }

    header("Location:roombook.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- boot -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- fontowesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- sweet alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="./css/roombook.css">
    <style>
        #editpanel{
            position : fixed;
            z-index: 1000;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            /* align-items: center; */
            background-color: #00000079;
        }
        #editpanel .guestdetailpanelform{
            height: 620px;
            width: 1170px;
            background-color: #ccdff4;
            border-radius: 10px;  
            /* temp */
            position: relative;
            top: 20px;
            animation: guestinfoform .3s ease;
        }

    </style>
    <title>Document</title>
</head>
<body>
    <div id="editpanel">
        <form method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>EDIT RESERVATION</h3>
                <a href="./roombook.php"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Guest information</h4>
                    <input type="text" name="Name" placeholder="Enter Full name" value="<?php echo $Name ?>">
                    <input type="email" name="Email" placeholder="Enter Email" value="<?php echo $Email ?>">

                    <?php
                    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                    ?>

                    <select name="Country" class="selectinput">
						<option value selected >Select your country</option>
                        <?php
							foreach($countries as $key => $value):
							echo '<option value="'.$value.'">'.$value.'</option>';
                            //close your tags!!
							endforeach;
						?>
                    </select>
                    <input type="text" name="Phone" placeholder="Enter Phoneno"  value="<?php echo $Phone ?>">
                </div>

                <div class="line"></div>

                <div class="reservationinfo">
                    <h4>Reservation information</h4>
                    <select name="RoomType" class="selectinput" required>
                        <option value="" disabled <?php echo empty($CurrentRoomType) ? 'selected' : ''; ?>>Tipo de habitación</option>
                        <option value="Habitación Sencilla" <?php echo $CurrentRoomType === 'Habitación Sencilla' ? 'selected' : ''; ?>>Habitación Sencilla</option>
                        <option value="Habitación Doble" <?php echo $CurrentRoomType === 'Habitación Doble' ? 'selected' : ''; ?>>Habitación Doble</option>
                        <option value="Habitación Múltiple" <?php echo $CurrentRoomType === 'Habitación Múltiple' ? 'selected' : ''; ?>>Habitación Múltiple</option>
                    </select>
                    <select name="Bed" class="selectinput" required>
                        <option value="" disabled <?php echo empty($CurrentBed) ? 'selected' : ''; ?>>Cantidad de huéspedes</option>
                        <?php for ($guests = 1; $guests <= 6; $guests++): ?>
                            <option value="<?php echo $guests; ?>" <?php echo (int)$CurrentBed === $guests ? 'selected' : ''; ?>><?php echo $guests; ?> huésped<?php echo $guests > 1 ? 'es' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="NoofRoom" class="selectinput" required>
                        <option value="1" <?php echo (int)$CurrentNoofRoom === 1 ? 'selected' : ''; ?>>1</option>
                    </select>
                    <select name="Meal" class="selectinput" required>
                        <option value="Room only" <?php echo $CurrentMeal === 'Room only' ? 'selected' : ''; ?>>Room only</option>
                        <option value="Breakfast" <?php echo $CurrentMeal === 'Breakfast' ? 'selected' : ''; ?>>Breakfast</option>
                        <option value="Half Board" <?php echo $CurrentMeal === 'Half Board' ? 'selected' : ''; ?>>Half Board</option>
                        <option value="Full Board" <?php echo $CurrentMeal === 'Full Board' ? 'selected' : ''; ?>>Full Board</option>
                    </select>
                    <div class="datesection">
                        <span>
                            <label for="cin"> Check-In</label>
                            <input name="cin" type ="date" value="<?php echo $cin ?>">
                        </span>
                        <span>
                            <label for="cin"> Check-Out</label>
                            <input name="cout" type ="date" value="<?php echo $cout ?>">
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailedit">Edit</button>
            </div>
        </form>
    </div>
</body>
</html>