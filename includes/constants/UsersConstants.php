<?php

// Definimos constantes para la operación userLogin
// Si se logea correctamente -> 201
define('USER_AUTHENTICATED', 201);
// Si el usuario no existe -> 202
define('USER_NOT_FOUND', 202);
// Si la contraseña y el usuario no concuerdan -> 203
define('USER_PASSWORD_DONT_MATCH', 203);
