@component('mail::message')

    # Administracion Backoffice
     Se le informa que su usuario a sido creado correctamente, las credenciales para acceder son
        Usuario : {{ $details['usuario'] }}
        Contraseña : {{ $details['contrasenia'] }}

    Consejo : Una vez accedas al sitio, recuerda cambiar tus credenciales rapidamente 

    @component('mail::button', ['url' => 'http://localhost:8080/'])
        Ir al Sitio
    @endcomponent
  
@endcomponent