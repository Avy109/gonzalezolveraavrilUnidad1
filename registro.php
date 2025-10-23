<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "agencia_viajes";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Error en la conexión: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena']; 

        if (empty($nombre) || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        $error = "<div style='color:red; margin-top:5px;'>El nombre solo puede contener letras y espacios.<br><br></div>";

        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "<div style='color:red; margin-top:5px;'>Correo electrónico no válido.<br><br></div>";

    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $contrasena)) {
        $error = "<div style='color:red; margin-top:5px;'>
                    <p>La contraseña debe cumplir con los siguientes requisitos:</p>
                    <ul>
                        <li>Al menos 8 caracteres</li>
                        <li>Una letra mayúscula</li>
                        <li>Una letra minúscula</li>
                        <li>Al menos un número</li>
                        <li>Al menos un carácter especial</li>
                    </ul><br>
                  </div>";

    } else {
        
        $nombre = mysqli_real_escape_string($conn, $nombre);
        $correo = mysqli_real_escape_string($conn, $correo);

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT, ["cost" => 10]);

        $sql = "INSERT INTO usuarios (nombre, correo, contrasena) 
                VALUES ('$nombre', '$correo', '$contrasena_hash')";

        if (mysqli_query($conn, $sql)) {
            $error = "<div style='color:green; margin-top:5px;'>¡Registro exitoso!<br><br></div>";
        } else {
           
            if (mysqli_errno($conn) == 1062) {
                $error = "<div style='color:red; margin-top:5px;'>El correo '$correo' ya ha sido registrado.<br><br></div>";
            } else {
                $error = "<div style='color:red; margin-top:5px;'>Error al registrar: " . mysqli_error($conn) . "<br><br></div>";
            }
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="estiloregistro.css">
</head>
<body>

<header class="navbar">
    <div class="logo">DER</div>
    <nav>
      <ul>
        <li><a href="index.html">Inicio</a></li>
        <li><a href="#">Tours</a></li>
        <li><a href="#">Explorar</a></li>
        <li><a href="#">Acerca de</a></li>
        <li><a href="registro.php">Registro</a></li>
      </ul>
    </nav>
</header>

<div class="contenedor">
    <div class="info">
        <h1>¡Comienza ya!</h1>
        <p>
            Descubre los mejores lugares para visitar y los hoteles ideales para tu próxima aventura.
            Regístrate y accede a recomendaciones personalizadas, ofertas exclusivas y guías creadas
            especialmente para ti.
        </p>
        <p class="destino"><em>Tu próximo destino comienza aquí.</em></p>
    </div>

    <div class="formulario">
        <h2>Registrarse</h2>

        <form action="registro.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="correo" placeholder="Correo Electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            
            <?php if(!empty($error)) echo $error; ?>

            <button type="submit">Registrarse</button>
        </form>
        <p class="login">¿Ya tienes una cuenta? <a href="#">Inicia Sesión</a></p>
    </div>
</div>

</body>
</html>
