<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
	<link rel="stylesheet" href="login.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    
<?php
include("conexion.php");

session_start();

if (isset($_SESSION['id_usuario'])) {
    header("Location: Principal/index.html");
}
if (isset($_SESSION['id_gerente'])) {
    header("Location: Gerente/panel.php");
}
if (isset($_SESSION['id_asesor'])) {
    header("Location: Asesor/paginaindexyael.html");
}

if (isset($_POST["ingresar"])) {
    $usuario = mysqli_real_escape_string($conexion, $_POST['user']);
    $password = mysqli_real_escape_string($conexion, $_POST['pass']);
    $password_encriptada = sha1($password);

    $sql_gerente = "SELECT idgerentes FROM gerentes WHERE usuario ='$usuario' AND password ='$password_encriptada'";
    $resultado_gerente = $conexion->query($sql_gerente);
    if ($resultado_gerente->num_rows > 0) {
        $row = $resultado_gerente->fetch_assoc();
        $_SESSION['id_gerente'] = $row['idgerentes'];
        header("Location: Gerente/panel.php");
        exit();
    }   else {
        echo "<script>
            alert('Usuario o Password incorrectos');
            window.location = 'index.php';
        </script>";
    }

    $sql_asesor = "SELECT idasesores FROM asesores WHERE usuario ='$usuario' AND password ='$password_encriptada'";
    $resultado_asesor = $conexion->query($sql_asesor);
    if ($resultado_asesor->num_rows > 0) {
        $row = $resultado_asesor->fetch_assoc();
        $_SESSION['id_asesor'] = $row['idasesores'];
        header("Location: Asesor/paginaindexyael.html");
        exit();
    }   else {
        echo "<script>
            alert('Usuario o Password incorrectos');
            window.location = 'index.php';
        </script>";
    }

    $sql = "SELECT idusuarios FROM usuarios WHERE usuario ='$usuario' AND password ='$password_encriptada'";
    $resultado = $conexion->query($sql);
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $_SESSION['id_usuario'] = $row['idusuarios'];
        header("Location: Principal/index.html");
        exit();
    } else {
        echo "<script>
            alert('Usuario o Password incorrectos');
            window.location = 'index.php';
        </script>";
    }
}

if (isset($_POST["registrar"])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $usuario = mysqli_real_escape_string($conexion, $_POST['user']);
    $password = mysqli_real_escape_string($conexion, $_POST['pass']);
    $confirm_password = mysqli_real_escape_string($conexion, $_POST['confirm_pass']);
    
    // Opcional: código solo para el 3er formulario
    $codigo = isset($_POST['codigo']) ? mysqli_real_escape_string($conexion, $_POST['codigo']) : null;

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo "<script>
            alert('Las contraseñas no coinciden');
            window.location = 'index.php';
        </script>";
        exit;
    }

    $password_encriptada = sha1($password);

    // Verificar si el usuario ya existe en cualquiera de las tablas
    $sqlCheckUsuario = "SELECT usuario FROM usuarios WHERE usuario = '$usuario'
                        UNION
                        SELECT usuario FROM asesores WHERE usuario = '$usuario'
                        UNION
                        SELECT usuario FROM gerentes WHERE usuario = '$usuario'";
    $resultadoCheck = $conexion->query($sqlCheckUsuario);

    if ($resultadoCheck->num_rows > 0) {
        echo "<script>
            alert('El usuario ya existe');
            window.location = 'index.php';
        </script>";
        exit;
    }

    // Lógica de inserción
    if ($codigo === null || $codigo === "") {
        // Registro normal (form 1 y 2)
        $sqlInsert = "INSERT INTO usuarios (NombreC, Correo, usuario, password) 
                      VALUES ('$nombre', '$correo', '$usuario', '$password_encriptada')";
    } elseif ($codigo == "333") {
        // Registro como asesor
        $sqlInsert = "INSERT INTO asesores (NombreC, Correo, usuario, password) 
                      VALUES ('$nombre', '$correo', '$usuario', '$password_encriptada')";
    } elseif ($codigo == "444") {
        // Registro como gerente
        $sqlInsert = "INSERT INTO gerentes (NombreC, Correo, usuario, password) 
                      VALUES ('$nombre', '$correo', '$usuario', '$password_encriptada')";
    } else {
        echo "<script>
            alert('Código no válido');
            window.location = 'index.php';
        </script>";
        exit;
    }

    if ($conexion->query($sqlInsert)) {
        echo "<script>
            alert('Registro exitoso');
            window.location = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al registrarse: " . mysqli_error($conexion) . "');
            window.location = 'index.php';
        </script>";
    }
}
?>
<div id="login-box" class="login-container">
    <h1><i class="fas fa-user-circle"></i> Iniciar Sesión</h1>
    <form method="POST" autocomplete="off" action="">
        <input type="text" name="user" placeholder="Usuario" required>
        <input type="password" name="pass" placeholder="Contraseña" required>
        <button type="submit" name="ingresar"><b>Ingresar</b></button>
    </form>
    <div class="link">
        <p>¿No tienes una cuenta? <a href="#" onclick="mostrarRegistro()">Registrarse</a></p>
    </div>
</div>

<div id="register-box" class="login-container" style="display:none;">
    <h1><i class="fas fa-user-plus"></i> Crear una cuenta</h1>
    <form method="POST" autocomplete="off" action="">
        <input type="text" name="nombre" placeholder="Nombre Completo" required>
        <input type="email" name="correo" placeholder="Correo Electrónico" required>
        <input type="text" name="user" placeholder="Nombre de Usuario" required>
        <input type="password" name="pass" placeholder="Contraseña" id="contrase" required>
        <i class="fa-solid fa-eye" id="togglePassword"></i>
        <input type="password" name="confirm_pass" placeholder="Confirmar Contraseña" required>
    
    
        <!--<label><input type="radio" name="rol" value="gerente" required> Gerente</label>
        <label><input type="radio" name="rol" value="asesor"> Asesor</label>
        <label><input type="radio" name="rol" value="usuario"> Usuario</label>-->
        <button type="submit" name="registrar"><b>Registrar</b></button>
    </form>
    <div class="link" >
        <p>¿Ya tienes una cuenta? <a href="#" onclick="mostrarLogin()">Ingresar</a></p>
        <p>¿Registrar gerente o asesor? <a href="#" onclick="mostrarLogin2()">Registrar</a></p>
    </div>
</div>
<!--Formulario de gerente y asesor -->
    <div id="register-asesorgeren" class="login-container" style="display:none;">
    <h1><i class="fas fa-user-plus"></i> Crear una cuenta</h1>
    <form method="POST" autocomplete="off" action="">
        <input type="text" name="nombre" placeholder="Nombre Completo" required>
        <input type="email" name="correo" placeholder="Correo Electrónico" required>
        <input type="text" name="user" placeholder="Nombre de Usuario" required>
        <input type="password" name="pass" placeholder="Contraseña" id="contrase" required>
        <i class="fa-solid fa-eye" id="togglePassword"></i>
        <input type="password" name="confirm_pass" placeholder="Confirmar Contraseña" required>
        <input type="text" name="codigo" placeholder="Código de validación XXXX" required>
    
        <!--<label><input type="radio" name="rol" value="gerente" required> Gerente</label>
        <label><input type="radio" name="rol" value="asesor"> Asesor</label>
        <label><input type="radio" name="rol" value="usuario"> Usuario</label>-->
        <button type="submit" name="registrar"><b>Registrar</b></button>
        <div class="link">
        <a href="#" onclick="mostrarLogin()">Inicio</a>
        </div>
    </form>

</div>
    
</div>
    <script src="login.js"></script>
</body>
</html>

