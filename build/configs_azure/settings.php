<?php

use helena\classes\App;
use minga\framework\Context;
use minga\framework\settings\CacheSettings;
use minga\framework\settings\MailSettings;

// enable the debug mode
App::SetDebug(true);

// **** Servidores
Context::Settings()->Servers()->RegisterServers("https://desa-poblaciones.eastus.cloudapp.azure.com", "https://desa-poblaciones.eastus.cloudapp.azure.com");

// **** Opcionales
Context::Settings()->Map()->UseTileBlocks = true;
Context::Settings()->Map()->UseGradients = true;

// **** Keys de terceros
Context::Settings()->Keys()->GoogleMapsKey = "AIzaSyCvwo3r7L_WUENLJQgqlwQT3D4XUyvIpSc";

// En caso de usarse SendGrid para la salida de mails:
//Context::Settings()->Mail()->SendGridApiKey = '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';
//
// En caso de usarse AddThis para que los usuarios vinculen contenidos en redes sociales:
// Context::Settings()->Keys()->AddThisKey = "ADD_THIS_KEY";

// **** Mail
// Posibles providers: Context::Settings()->Mail()->Provider: MailSettings::SendGrid, MailSettings::SMTP;
Context::Settings()->Mail()->Provider= MailSettings::SMTP;
Context::Settings()->Mail()->From = 'no-responder@aacademica.org';
Context::Settings()->Mail()->NotifyAddressErrors = '';
Context::Settings()->Mail()->SMTPSecure = "";
Context::Settings()->Mail()->SMTPHost = "localhost";

// **** Región de inicio
Context::Settings()->Map()->DefaultClippingRegion = 15476;

// Base de datos MySQL
Context::Settings()->Db()->SetDatabase("localhost", "ffg_maps_desa", "u_ffg_dev", "uffG.2019");

// Ubicación de python
Context::Settings()->Servers()->Python3 = '/usr/bin/python3';
Context::Settings()->Servers()->Python27 = '/usr/bin/python';

// Ubicación del binario de php para ejecutar en línea de comandos
Context::Settings()->Servers()->PhpCli = 'php';
