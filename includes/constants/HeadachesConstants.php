<?php


    // Definimos constantes para la operacion createHeadache
    // Si el headache no existe y se añade un nuevo registro en la tabla -> 101
    define('HEADACHE_CREATED', 101);
    // Si el headache ya existe y por lo tanto no se añade ningun registro a la tabla -> 102
    define('HEADACHE_FAILURE', 102);
    // Si el patient_id no existe -> 103
    define('PATIENTID_NOT_VALID', 103);

    define('NO_ACTIVE_HEADACHES', 101);
    define('ONE_ACTIVE_HEADACHE', 102);
    define('MORE_THAN_ONE_ACTIVE_HEADACHE', 103);

