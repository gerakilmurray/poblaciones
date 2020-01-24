<?php

use helena\classes\App;
use minga\framework\Context;
use minga\framework\settings\CacheSettings;
use minga\framework\settings\MailSettings;

// enable the debug mode
App::SetDebug(true);

// **** Servidores
Context::Settings()->Servers()->RegisterServers("https://desa.poblaciones.org", "https://desa.poblaciones.org");

// **** Keys de terceros
Context::Settings()->Keys()->GoogleMapsKey = "AIzaSyCvwo3r7L_WUENLJQgqlwQT3D4XUyvIpSc";

// En caso de usarse SendGrid para la salida de mails:
// Context::Settings()->Mail()->SendGridApiKey = 'SEND_GRID_API_KEY';
//
// En caso de usarse AddThis para que los usuarios vinculen contenidos en redes sociales:
// Context::Settings()->Keys()->AddThisKey = "ADD_THIS_KEY";

// **** Mail
// Posibles providers: Context::Settings()->Mail()->Provider: MailSettings::SendGrid, MailSettings::SMTP;
Context::Settings()->Mail()->From = 'no-responder@aacademica.org';
Context::Settings()->Mail()->NotifyAddressErrors = '';
Context::Settings()->Mail()->SMTPSecure = "";
Context::Settings()->Mail()->SMTPHost = "localhost";

// Base de datos MySQL
Context::Settings()->Db()->SetDatabase("54.173.213.1", "ffg_maps_desa", "u_ffg_dev", "uffG.2019");

// Ubicaci�n de python
Context::Settings()->Servers()->Python27 = '/usr/bin/python';
