<?php
include 'config.php';
session_start();

/* ===========
   Autenticación
   =========== */
if (empty($_SESSION['usermail'])) {
  header("Location: index.php");
  exit;
}
$usermail = $_SESSION['usermail'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/home.css">
    <title>Hotel Andino</title>
    <!-- boot -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <!-- sweet alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="./admin/css/roombook.css">
    <style>
      #guestdetailpanel{
        display: none;
      }
      #guestdetailpanel .middle{
        height: 450px;
      }
    </style>
</head>

<body>
  <nav>
    <div class="logo">
      <img class="HotelAndino" src="./image/LogoAndino.png" alt="logo">
      <p>HOTEL ANDINO</p>
    </div>
    <ul>
      <li><a href="#firstsection">Inicio</a></li>
      <li><a href="#secondsection">Habitaciones</a></li>
      <li><a href="#thirdsection">Servicios</a></li>
      <li><a href="chatbot.html">Chatbot</a></li>
      <li><a href="#contactus">Contáctanos</a></li>
      <a href="./logout.php"><button class="btn btn-danger">Cerrar sesión</button></a>
    </ul>
  </nav>

  <section id="firstsection" class="carousel slide carousel_section" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="carousel-image" src="./image/hotel1.jpg" alt="Hotel 1">
        </div>
        <div class="carousel-item">
            <img class="carousel-image" src="./image/hotel2.jpg" alt="Hotel 2">
        </div>
        <div class="carousel-item">
            <img class="carousel-image" src="./image/hotel3.jpg" alt="Hotel 3">
        </div>
        <div class="carousel-item">
            <img class="carousel-image" src="./image/hotel4.jpg" alt="Hotel 4">
        </div>

        <div class="welcomeline">
          <h1 class="welcometag">Bienvenido al cielo en la tierra</h1>
        </div>

      <!-- bookbox -->
      <div id="guestdetailpanel">
        <form action="" method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>RESERVA</h3>
                <i class="fa-solid fa-circle-xmark" onclick="closebox()"></i>
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
                        <option value="" selected disabled hidden>Selecciona tu país</option>
                        <?php foreach($countries as $value): ?>
                          <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>
                          </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="Phone" placeholder="Número de teléfono">
                </div>

                <div class="line"></div>

                <div class="reservationinfo">
                    <h4>Información de la reserva</h4>
                    <select name="RoomType" class="selectinput" required>
                        <option value="" selected disabled hidden>Tipo de habitación</option>
                        <option value="Superior Room">HABITACIÓN SUPERIOR</option>
                        <option value="Deluxe Room">HABITACIÓN DELUXE</option>
                        <option value="Guest House">CASA DE HUÉSPEDES</option>
                        <option value="Single Room">HABITACIÓN INDIVIDUAL</option>
                    </select>
                    <select name="Bed" class="selectinput" required>
                        <option value="" selected disabled hidden>Tipo de cama</option>
                        <option value="Single">Individual</option>
                        <option value="Double">Doble</option>
                        <option value="Triple">Triple</option>
                        <option value="Quad">Cuádruple</option>
                        <option value="None">Ninguna</option>
                    </select>
                    <select name="NoofRoom" class="selectinput" required>
                        <option value="" selected disabled hidden>Número de habitaciones</option>
                        <option value="1">1</option>
                    </select>
                    <select name="Meal" class="selectinput" required>
                        <option value="" selected disabled hidden>Comidas</option>
                        <option value="Room only">Solo habitación</option>
                        <option value="Breakfast">Desayuno</option>
                        <option value="Half Board">Media pensión</option>
                        <option value="Full Board">Pensión completa</option>
                    </select>
                    <div class="datesection">
                        <span>
                            <label for="cin">Llegada</label>
                            <input id="cin" name="cin" type="date" required>
                        </span>
                        <span>
                            <label for="cout">Salida</label>
                            <input id="cout" name="cout" type="date" required>
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailsubmit">Enviar</button>
            </div>
        </form>

        <!-- ==== room book php ====-->
        <?php
            if (isset($_POST['guestdetailsubmit'])) {
                $Name     = trim($_POST['Name'] ?? '');
                $Email    = trim($_POST['Email'] ?? '');
                $Country  = trim($_POST['Country'] ?? '');
                $Phone    = trim($_POST['Phone'] ?? '');
                $RoomType = trim($_POST['RoomType'] ?? '');
                $Bed      = trim($_POST['Bed'] ?? '');
                $NoofRoom = (int)($_POST['NoofRoom'] ?? 0);
                $Meal     = trim($_POST['Meal'] ?? '');
                $cin      = $_POST['cin'] ?? '';
                $cout     = $_POST['cout'] ?? '';

                if ($Name === "" || $Email === "" || $Country === "" || $RoomType === "" || $Bed === "" || $NoofRoom < 1 || $Meal === "" || $cin === "" || $cout === "") {
                    echo "<script>swal({ title: 'Completa los datos correctamente', icon: 'error' });</script>";
                } else {
                    $d1 = strtotime($cin);
                    $d2 = strtotime($cout);
                    if ($d1 === false || $d2 === false || $d2 <= $d1) {
                        echo "<script>swal({ title: 'Rango de fechas inválido', icon: 'error' });</script>";
                    } else {
                        $nodays = (int)round(($d2 - $d1) / 86400); // días
                        $sta = "NotConfirm";

                        $sql = "INSERT INTO roombook
                                (Name, Email, Country, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, stat, nodays)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param(
                                $stmt,
                                "ssssssissssi",
                                $Name, $Email, $Country, $Phone, $RoomType, $Bed, $NoofRoom, $Meal, $cin, $cout, $sta, $nodays
                            );
                            $ok = mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);

                            if ($ok) {
                                echo "<script>
                                    swal({ title: 'Reserva exitosa', icon: 'success' });
                                </script>";
                            } else {
                                echo "<script>swal({ title: 'Algo salió mal al guardar', icon: 'error' });</script>";
                            }
                        } else {
                            echo "<script>swal({ title: 'Error preparando consulta', icon: 'error' });</script>";
                        }
                    }
                }
            }
            ?>
          </div>

    </div>
  </section>

  <section id="secondsection">
    <img src="./image/homeanimatebg.svg" alt="Decoración">
    <div class="ourroom">
      <h1 class="head">≼ Nuestras habitaciones ≽</h1>
      <div class="roomselect">
        <div class="roombox">
          <div class="hotelphoto h1"></div>
          <div class="roomdata">
            <h2>Habitación Superior</h2>
            <div class="services">
              <i class="fa-solid fa-wifi"></i>
              <i class="fa-solid fa-burger"></i>
              <i class="fa-solid fa-spa"></i>
              <i class="fa-solid fa-dumbbell"></i>
              <i class="fa-solid fa-person-swimming"></i>
            </div>
            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Reservar</button>
          </div>
        </div>
        <div class="roombox">
          <div class="hotelphoto h2"></div>
          <div class="roomdata">
            <h2>Habitación Deluxe</h2>
            <div class="services">
              <i class="fa-solid fa-wifi"></i>
              <i class="fa-solid fa-burger"></i>
              <i class="fa-solid fa-spa"></i>
              <i class="fa-solid fa-dumbbell"></i>
            </div>
            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Reservar</button>
          </div>
        </div>
        <div class="roombox">
          <div class="hotelphoto h3"></div>
          <div class="roomdata">
            <h2>Habitación de Huéspedes</h2>
            <div class="services">
              <i class="fa-solid fa-wifi"></i>
              <i class="fa-solid fa-burger"></i>
              <i class="fa-solid fa-spa"></i>
            </div>
            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Reservar</button>
          </div>
        </div>
        <div class="roombox">
          <div class="hotelphoto h4"></div>
          <div class="roomdata">
            <h2>Habitación Individual</h2>
            <div class="services">
              <i class="fa-solid fa-wifi"></i>
              <i class="fa-solid fa-burger"></i>
            </div>
            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Reservar</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="thirdsection">
    <h1 class="head">≼ Servicios ≽</h1>
    <div class="facility">
      <div class="box">
        <h2>Piscina</h2>
      </div>
      <div class="box">
        <h2>Spa</h2>
      </div>
      <div class="box">
        <h2>Restaurantes 24/7</h2>
      </div>
      <div class="box">
        <h2>Gimnasio 24/7</h2>
      </div>
      <div class="box">
        <h2>Servicio de helicóptero</h2>
      </div>
    </div>
  </section>

  <section id="contactus">
    <div class="social">
      <i class="fa-brands fa-instagram"></i>
      <i class="fa-brands fa-facebook"></i>
      <i class="fa-solid fa-envelope"></i>
    </div>
  </section>
</body>

<script>
    var bookbox = document.getElementById("guestdetailpanel");

    function openbookbox(){
      bookbox.style.display = "flex";
    }
    function closebox(){
      bookbox.style.display = "none";
    }
</script>
</html>
