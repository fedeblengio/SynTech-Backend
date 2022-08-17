<!DOCTYPE html>
<html>

<head>
    <title>Notificacion de Creacion de Usuario</title>
</head>

    <body>
        <h1> BIENVENIDO A LMS </h1>
        <br> 
        <p> Usted ha sido registrado en nuestro sistema, sus credenciales son su cedula tanto para usuario como para contraseña, procure cambiarla cuando entre al sitio </p>
        <br>
        <p>{{ $details['usuario'] }}</p>
        <br>
        <p>{{ $details['contrasenia'] }}</p>
        <center><img src="https://cdn.discordapp.com/attachments/480881732724195328/1009536092212580392/unknown.png"></center>
    </body>
</html>
