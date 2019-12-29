<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'cluster_devel');

// Definimos constantes para la operacion createPatient_login
// Si el usuario no existe y se añade un nuevo registro en la tabla -> 101
define('USER_CREATED', 101);
// Si el usuario ya existe y por lo tanto no se añade ningun registro a la tabla -> 102
define('USER_EXISTS', 102);
// Si algo falla -> 103
define('USER_FAILURE', 103);

// Definimos constantes para la operacion readPatientByEmail
// Si se lee correctamente
define('USER_READED', 101);
// Si algo falla
define('USER_NOT_READED', 103);

// Definimos constantes para la operacion readAllPatients_login
// Si se han leido correctamente los usuarios -> 101
define('ALLUSERS_READED', 101);
// Si algo falla -> 103
define('ALLUSERS_FAILURE', 103);

// Definimos constantes para la operación patientLogin
// Si se logea correctamente -> 201
define('PATIENT_AUTHENTICATED', 201);
// Si el usuario no existe -> 202
define('PATIENT_NOT_FOUND', 202);
// Si la contraseña y el usuario no concuerdan -> 203
define('PATIENT_PASSWORD_DONT_MATCH', 203);

// Definimos constantes para la operación updatePatientPassword
// Si se cambia correcamente -> 301
define('PASSWORD_CHANGED', 301);
// Si la contraseña actual y la introducida no concuerdan -> 302
define('PASSWORD_DONT_MATCH', 302);
// Si no se cambia la contraseña -> 303
define('PASSWORD_NOT_CHANGED', 303);