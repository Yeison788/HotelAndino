<?php
session_start();
include '../config.php';

function formatGuestCapacity($value)
{
    $capacity = (int)$value;
    if ($capacity <= 0) {
        return 'Sin definir';
    }
    return $capacity . ' huésped' . ($capacity === 1 ? '' : 'es');
}

function formatCurrencyCOP($amount)
{
    return number_format((float)$amount, 0, ',', '.');
}

?>

<!DOCTYPE html>
<html lang="es">

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
    <title>Hotel Andino - Admin</title>
</head>

<body>
    <!-- guestdetailpanel -->

    <div id="guestdetailpanel">
        <form action="" method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>RESERVA</h3>
                <i class="fa-solid fa-circle-xmark" onclick="adduserclose()"></i>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Información del huésped</h4>
                    <input type="text" name="Name" placeholder="Nombre completo" required>
                    <input type="email" name="Email" placeholder="Correo electrónico" required>

                    <?php
                    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                    ?>

                    <select name="Country" class="selectinput" required>
						<option value selected >Selecciona tu país</option>
                        <?php
							foreach($countries as $key => $value):
							echo '<option value="'.$value.'">'.$value.'</option>';
                            //close your tags!!
							endforeach;
						?>
                    </select>
                    <input type="text" name="Phone" placeholder="Número de teléfono" required>
                </div>

                <div class="line"></div>

                <div class="reservationinfo">
                    <h4>Información de la reserva</h4>
                    <select name="RoomType" class="selectinput" required>
                        <option value selected>Tipo de habitación</option>
                        <option value="Habitación Sencilla">Habitación Sencilla</option>
                        <option value="Habitación Doble">Habitación Doble</option>
                        <option value="Habitación Múltiple">Habitación Múltiple</option>
                    </select>
                    <select name="Bed" class="selectinput" required>
                        <option value selected>Cantidad de huéspedes</option>
                        <option value="1">1 huésped</option>
                        <option value="2">2 huéspedes</option>
                        <option value="3">3 huéspedes</option>
                        <option value="4">4 huéspedes</option>
                        <option value="5">5 huéspedes</option>
                        <option value="6">6 huéspedes</option>
                    </select>
                    <select name="NoofRoom" class="selectinput">
						<option value selected >Número de habitaciones</option>
                        <option value="1">1</option>
                        <!-- <option value="1">2</option>
                        <option value="1">3</option> -->
                    </select>
                    <select name="Comidas" class="selectinput">
						<option value selected >Comida</option>
                        <option value="Solo habitación">Room only</option>
                        <option value="Desayuno">Breakfast</option>
						<option value="Media pensión">Half Board</option>
						<option value="Pensión completa">Full Board</option>
					</select>
                    <div class="datesection">
                        <span>
                            <label for="cin"> Llegada</label>
                            <input name="cin" type ="date">
                        </span>
                        <span>
                            <label for="cin"> Salida</label>
                            <input name="cout" type ="date">
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailsubmit">Enviar</button>
            </div>
        </form>

        <?php       
        // <!-- room availablity start-->

        $rsql = "SELECT * FROM room";
        $rre = mysqli_query($conn, $rsql);

        $roomTypeTotals = [
            'Habitación Sencilla' => 0,
            'Habitación Doble'    => 0,
            'Habitación Múltiple' => 0,
        ];

        while ($rrow = mysqli_fetch_array($rre)) {
            $type = $rrow['type'];
            if (array_key_exists($type, $roomTypeTotals)) {
                $roomTypeTotals[$type]++;
            }
        }

        $reservationTotals = [
            'Habitación Sencilla' => 0,
            'Habitación Doble'    => 0,
            'Habitación Múltiple' => 0,
        ];

        $csql = "SELECT RoomType FROM payment";
        $cre = mysqli_query($conn, $csql);
        while ($crow = mysqli_fetch_array($cre)) {
            $type = $crow['RoomType'];
            if (array_key_exists($type, $reservationTotals)) {
                $reservationTotals[$type]++;
            }
        }

        $availabilityByType = [];
        foreach ($roomTypeTotals as $type => $totalRooms) {
            $confirmed = $reservationTotals[$type] ?? 0;
            $available = $totalRooms - $confirmed;
            $availabilityByType[$type] = $available > 0 ? $available : "NO";
        }

        $totalRooms = array_sum($roomTypeTotals);
        $totalConfirmed = array_sum($reservationTotals);
        $totalAvailable = $totalRooms - $totalConfirmed;
        if ($totalAvailable <= 0) {
            $totalAvailable = "NO";
        }

        $f1 = $availabilityByType['Habitación Sencilla'];
        $f2 = $availabilityByType['Habitación Doble'];
        $f3 = $availabilityByType['Habitación Múltiple'];
        $f5 = $totalAvailable;
        ?>
        <!-- room availablity end-->

        <!-- ==== room book php ====-->
        <?php
            if (isset($_POST['guestdetailsubmit'])) {
                $Name = trim($_POST['Name'] ?? '');
                $Email = trim($_POST['Email'] ?? '');
                $Country = trim($_POST['Country'] ?? '');
                $Phone = trim($_POST['Phone'] ?? '');
                $RoomType = trim($_POST['RoomType'] ?? '');
                $guestCount = (int)($_POST['Bed'] ?? 0);
                $NoofRoom = (int)($_POST['NoofRoom'] ?? 0);
                $Meal = trim($_POST['Meal'] ?? '');
                $cin = $_POST['cin'] ?? '';
                $cout = $_POST['cout'] ?? '';

                if ($Name === '' || $Email === '' || $Country === '' || $RoomType === '' || $guestCount <= 0 || $NoofRoom < 1 || $Meal === '' || $cin === '' || $cout === '') {
                    echo "<script>swal({ title: 'Completa los datos correctamente', icon: 'error' });</script>";
                } else {
                    $d1 = strtotime($cin);
                    $d2 = strtotime($cout);
                    if ($d1 === false || $d2 === false || $d2 <= $d1) {
                        echo "<script>swal({ title: 'Rango de fechas inválido', icon: 'error' });</script>";
                    } else {
                        $nodays = max(1, (int)ceil(($d2 - $d1) / 86400));
                        $guestCount = max(1, $guestCount);
                        $NoofRoom = max(1, $NoofRoom);

                        $baseRate = 60000;
                        $extraGuestRate = 25000;
                        $ratePerNight = $baseRate + max(0, $guestCount - 1) * $extraGuestRate;
                        $totalPrice = $ratePerNight * $nodays * $NoofRoom;

                        $assignedRoomId = null;
                        $assignedRoomNumber = null;

                        if ($roomStmt = $conn->prepare("SELECT id, room_number FROM room WHERE type = ? AND status = 'Disponible' AND CAST(bedding AS UNSIGNED) >= ? ORDER BY CAST(room_number AS UNSIGNED) ASC LIMIT 1")) {
                            $roomStmt->bind_param('si', $RoomType, $guestCount);
                            if ($roomStmt->execute()) {
                                $roomStmt->bind_result($roomId, $roomNumber);
                                if ($roomStmt->fetch()) {
                                    $assignedRoomId = $roomId;
                                    $assignedRoomNumber = $roomNumber;
                                }
                            }
                            $roomStmt->close();
                        }

                        if ($assignedRoomId === null) {
                            echo "<script>swal({ title: 'No hay habitaciones disponibles para esa capacidad', icon: 'error' });</script>";
                        } else {
                            $sta = 'NotConfirm';
                            $guestCountValue = (string)$guestCount;

                            $sql = "INSERT INTO roombook
                                    (room_id, Name, Email, Country, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, stat, nodays, total_price)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                            if ($stmt = $conn->prepare($sql)) {
                                $stmt->bind_param(
                                    'issssssisssssid',
                                    $assignedRoomId,
                                    $Name,
                                    $Email,
                                    $Country,
                                    $Phone,
                                    $RoomType,
                                    $guestCountValue,
                                    $NoofRoom,
                                    $Meal,
                                    $cin,
                                    $cout,
                                    $sta,
                                    $nodays,
                                    $totalPrice
                                );
                                $ok = $stmt->execute();
                                $stmt->close();

                                if ($ok) {
                                    if ($update = $conn->prepare("UPDATE room SET status='Reservada' WHERE id=?")) {
                                        $update->bind_param('i', $assignedRoomId);
                                        $update->execute();
                                        $update->close();
                                    }

                                    $priceFormatted = number_format($totalPrice, 0, ',', '.');
                                    $roomText = $assignedRoomNumber ? 'Habitación ' . $assignedRoomNumber : 'una habitación disponible';
                                    $alertPayload = [
                                        'title' => 'Reserva creada',
                                        'text'  => 'Asignada ' . $roomText . '. Total: COP ' . $priceFormatted,
                                        'icon'  => 'success'
                                    ];
                                    echo '<script>swal(' . json_encode($alertPayload, JSON_UNESCAPED_UNICODE) . ');</script>';
                                } else {
                                    echo "<script>swal({ title: 'Algo salió mal al guardar', icon: 'error' });</script>";
                                }
                            } else {
                                echo "<script>swal({ title: 'Error preparando consulta', icon: 'error' });</script>";
                            }
                        }
                    }
                }
            }
        ?>
    </div>

    
    <!-- ================================================= -->
    <div class="searchsection">
        <input type="text" name="search_bar" id="search_bar" placeholder="search..." onkeyup="searchFun()">
        <button class="adduser" id="adduser" onclick="adduseropen()"><i class="fa-solid fa-bookmark"></i> Add</button>
        <form action="./exportdata.php" method="post">
            <button class="exportexcel" id="exportexcel" name="exportexcel" type="submit"><i class="fa-solid fa-file-arrow-down"></i></button>
        </form>
    </div>

    <div class="roombooktable" class="table-responsive-xl">
        <?php
            $roombooktablesql = "SELECT * FROM roombook";
            $roombookresult = mysqli_query($conn, $roombooktablesql);
            $nums = mysqli_num_rows($roombookresult);
        ?>
        <table class="table table-bordered" id="table-data">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">País</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Tipo de habitación</th>
                    <th scope="col">Cantidad de huéspedes</th>
                    <th scope="col">Número de habitaciones</th>
                    <th scope="col">Comida</th>
                    <th scope="col">Llegada</th>
                    <th scope="col">Salida</th>
                    <th scope="col">Número de días</th>
                    <th scope="col">Precio total (COP)</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="action">Acción</th>
                    <!-- <th>Delete</th> -->
                </tr>
            </thead>

            <tbody>
            <?php
            while ($res = mysqli_fetch_array($roombookresult)) {
            ?>
                <tr>
                    <td><?php echo $res['id'] ?></td>
                    <td><?php echo $res['Name'] ?></td>
                    <td><?php echo $res['Email'] ?></td>
                    <td><?php echo $res['Country'] ?></td>
                    <td><?php echo $res['Phone'] ?></td>
                    <td><?php echo $res['RoomType'] ?></td>
                    <td><?php echo htmlspecialchars(formatGuestCapacity($res['Bed'])); ?></td>
                    <td><?php echo $res['NoofRoom'] ?></td>
                    <td><?php echo $res['Meal'] ?></td>
                    <td><?php echo $res['cin'] ?></td>
                    <td><?php echo $res['cout'] ?></td>
                    <td><?php echo $res['nodays'] ?></td>
                    <td><?php echo 'COP ' . formatCurrencyCOP($res['total_price'] ?? 0); ?></td>
                    <td><?php echo $res['stat'] ?></td>
                    <td class="action">
                        <?php
                            if($res['stat'] == "Confirm")
                            {
                                echo " ";
                            }
                            else
                            {
                                echo "<a href='roomconfirm.php?id=". $res['id'] ."'><button class='btn btn-success'>Confirm</button></a>";
                            }
                        ?>
                        <a href="roombookedit.php?id=<?php echo $res['id'] ?>"><button class="btn btn-primary">Edit</button></a>
                        <a href="roombookdelete.php?id=<?php echo $res['id'] ?>"><button class='btn btn-danger'>Delete</button></a>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
<script src="./javascript/roombook.js"></script>



</html>