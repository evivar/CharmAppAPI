<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use Slim\Factory\AppFactory;

// Check the directory
require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../includes/DbPatients.php';

require __DIR__ . '/../includes/DbDiaries.php';

require __DIR__ . '/../includes/DbDoctors.php';

require __DIR__ . '/../includes/DbHeadaches.php';


$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

// FUNCTIONS

function emptyParameters($parametrosEntrada, $request, $response)
{
    $error = false;
    $error_params = '';
    $request_params = $request->getParsedBody();
    foreach ($parametrosEntrada as $param) {
        if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0) {
            $error = true;
            $error_params .= $param . ' ';
        }
    }
    if ($error) {
        $error_detail = array();
        $error_detail['Error'] = true;
        $error_detail['Mensaje'] = 'Parametros vacios: ' . rtrim($error_params);
        $error_detail['Resultado'] = null;
        $response->getBody()->write(json_encode($error_detail));
    }
    return $error;
}

// PATIENT

$app->post('/CharmAppAPI/public/createPatient_login', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'email', 'password', 'name', 'surname1', 'surname2', 'init_date', 'end_date', 'phone'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $email = $request_data['email'];
        $password = $request_data['password'];
        $name = $request_data['name'];
        $surname1 = $request_data['surname1'];
        $surname2 = $request_data['surname2'];
        $init_date = $request_data['init_date'];
        $end_date = $request_data['end_date'];
        $phone = $request_data['phone'];

        $db = new DbPatients;
        $result = $db->createPatient_login($patient_id, $email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone);
        if ($result == USER_CREATED) {
            $message = array();
            $message['Error'] = false;
            $message['Mensaje'] = 'Paciente creado correctamente';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == USER_FAILURE) {
            $message = array();
            $message['Error'] = true;
            $message['Mensaje'] = 'El paciente no se ha creado debido a un error';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } elseif ($result == USER_EXISTS) {
            $message = array();
            $message['Error'] = true;
            $message['Mensaje'] = 'El paciente ya existe';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/createPatient', function (Request $request, Response $response) {
    if (!emptyParameters(array('email', 'password', 'name', 'surname1', 'surname2', 'init_date', 'end_date', 'phone'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];
        $password = $request_data['password'];
        $name = $request_data['name'];
        $surname1 = $request_data['surname1'];
        $surname2 = $request_data['surname2'];
        $init_date = $request_data['init_date'];
        $end_date = $request_data['end_date'];
        $phone = $request_data['phone'];

        $db = new DbPatients;
        $result = $db->createPatient($email, $password, $name, $surname1, $surname2, $init_date, $end_date, $phone);
        if ($result == USER_CREATED) {
            $message = array();
            $message['Error'] = false;
            $message['Mensaje'] = 'Paciente creado correctamente';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == USER_FAILURE) {
            $message = array();
            $message['Error'] = true;
            $message['Mensaje'] = 'El paciente no se ha creado debido a un error';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } elseif ($result == USER_EXISTS) {
            $message = array();
            $message['Error'] = true;
            $message['Mensaje'] = 'El paciente ya existe';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->get('/CharmAppAPI/public/readAllPatients', function (Request $request, Response $response) {
    $db = new DbPatients;
    $patients = $db->readAllPatients();
    $message = array();
    $message['Error'] = false;
    $message['Mensaje'] = "Pacientes leidos correctamente";
    $message['Resultado'] = $patients;
    $response->getBody()->write(json_encode($message));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->post('/CharmAppAPI/public/readPatientByEmail', function (Request $request, Response $response) {
    if (!emptyParameters(array('email'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];

        $db = new DbPatients;
        $result = $db->readPatientByEmail($email);
        $message = array();
        if ($result) {
            $message['Error'] = false;
            $message['Mensaje'] = "Paciente leido correctamente";
            $message['Resultado'] = $result;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "El paciente con email '" . $email . "' no existe";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

$app->post('/CharmAppAPI/public/readPatientById', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbPatients;
        $result = $db->readPatientById($patient_id);
        $message = array();
        if ($result) {
            $message['Error'] = false;
            $message['Mensaje'] = "Paciente leido correctamente";
            $message['Resultado'] = $result;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "El paciente con id '" . $patient_id . "' no existe";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

$app->post('/CharmAppAPI/public/readPasswordByEmail', function (Request $request, Response $response) {
    if (!emptyParameters(array('email'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];

        $db = new DbPatients;
        $result = $db->readPasswordByEmail($email);
        $message = array();
        if ($result) {
            $message['Error'] = false;
            $message['Mensaje'] = "Contraseña leida correctamente";
            $message['Resultado'] = $result;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "El paciente con email '" . $email . "' no existe";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

$app->post('/CharmAppAPI/public/updatePatientPassword', function (Request $request, Response $response): Response {
    if (!emptyParameters(array('email', 'old_password', 'new_password'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data["email"];
        $old_password = $request_data['old_password'];
        $new_password = $request_data['new_password'];

        $db = new DbPatients;
        $result = $db->updatePatientPassword($email, $old_password, $new_password);
        $message = array();
        if ($result == PASSWORD_CHANGED) {
            $message['Error'] = false;
            $message['Mensaje'] = 'Contraseña cambiada correctamente';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PASSWORD_NOT_CHANGED) {
            $message['Error'] = true;
            $message['Mensaje'] = 'Contraseña no cambiada, intentelo de nuevo mas tarde';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PASSWORD_DONT_MATCH) {
            $message['Error'] = true;
            $message['Mensaje'] = 'La contraseña actual no es correcta';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

/**
 * TODO: Revisar la URL paramétrica
 */
$app->delete('/CharmAppAPI/public/deletePatient/{patient_id}', function (Request $request, Response $response, array $args): Response {
    // Obtenemos el id de los parametros $args
    $patient_id = $args['patient_id'];

    // Creamos un objeto DbPatients
    $db = new DbPatients;
    // Creamos un array donde guardaremos el resultado de la operacion
    $message = array();
    // Comprobamos si se ejecuta la operacion DELETE llamandola dentro de un if
    if ($db->deletePatient($patient_id)) {
        // Si se ejecuta correctamente
        $message['Estado del error'] = false;
        $message['Mensaje'] = 'Paciente eliminado correctamente';
    } else {
        // Si no se ejecuta
        $message['Estado del error'] = true;
        $message['Mensaje'] = 'Paciente no eliminado, inténtelo de nuevo más tarde';
    }

    // Escribimos la respuesta
    $response->getBody()->write(json_encode($message));
    // Devolvemos la respuesta
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->post('/CharmAppAPI/public/patientLogin', function (Request $request, Response $response) {
    if (!emptyParameters(array('email', 'password'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];
        $password = $request_data['password'];

        $db = new DbPatients;
        $result = $db->patientLogin($email, $password);
        $message = array();
        if ($result == PATIENT_AUTHENTICATED) {
            $patient = $db->readPatientByEmail($email);
            $message['Error'] = false;
            $message['Mensaje'] = "Inicio de sesión correcto";
            $message['Resultado'] = $patient;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PATIENT_PASSWORD_DONT_MATCH) {
            $message['Error'] = true;
            $message['Mensaje'] = "Inicio de sesión fallido, usuario y-o contraseña incorrectos";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == PATIENT_NOT_FOUND) {
            $message['Error'] = true;
            $message['Mensaje'] = "Inicio de sesión fallido, el paciente no existe";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(422);
    }
});

// DIARY

$app->post('/CharmAppAPI/public/createDiary', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'date', 'sleep_time', 'change_residence', 'sport_time', 'alcohol', 'smoke', 'feeling'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];
        $sleep_time = $request_data['sleep_time'];
        $change_residence = $request_data['change_residence'];
        $sport_time = $request_data['sport_time'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $feeling = $request_data['feeling'];

        $db = new DbDiaries;
        $result = $db->createDiary($patient_id, $date, $sleep_time, $change_residence, $sport_time, $alcohol, $smoke, $feeling);
        $message = array();
        if ($result == DIARY_CREATED) {
            $message['Error'] = false;
            $message['Mensaje'] = 'Formulario diario insertado correctamente';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201);
        } elseif ($result == DIARY_FAILURE) {
            $message['Error'] = true;
            $message['Mensaje'] = 'El formulario de diario no se ha insertado debido a un error';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } elseif ($result == DIARY_ALREADY_INSERTED) {
            $message['Estado del error'] = true;
            $message['Mensaje'] = 'Ya ha rellenado el formulario para el dia de hoy';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->get('/CharmAppAPI/public/readAllDiaries', function (Request $request, Response $response) {
    $db = new DbDiaries;
    $diaries = $db->readAllDiaries();
    $message = array();
    $message['Error'] = false;
    $message['Mensaje'] = "Diarios leidos correctamente";
    $message['Resultado'] = $diaries;
    $response->getBody()->write(json_encode($message));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->post('/CharmAppAPI/public/readAllDiariesByEmail', function (Request $request, Response $response) {
    if (!emptyParameters(array('email'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];

        $db = new DbDiaries;
        $diaries = $db->readAllDiariesByEmail($email);
        $message = array();
        if ($diaries) {
            $message['Error'] = false;
            $message['Mensaje'] = "Diarios leidos correctamente";
            $message['Resultado'] = $diaries;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ningún diario asociado al paciente con email '" . $email . "'";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readAllDiariesById', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbDiaries;
        $diaries = $db->readAllDiariesById($patient_id);
        $message = array();
        if ($diaries) {
            $message['Error'] = false;
            $message['Mensaje'] = "Diarios leidos correctamente";
            $message['Resultado'] = $diaries;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ningún diario asociado al paciente con id '" . $patient_id . "'";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readLastDiary', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbDiaries;
        $diary = $db->readLastDiary($patient_id);
        $message = array();
        if ($diary) {
            $message['Error'] = false;
            $message['Mensaje'] = "Último diario leido correctamente con fecha " . $diary['date'] . "";
            $message['Resultado'] = $diary;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ningún diario introducido";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readDiaryByDate', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'date'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];

        $db = new DbDiaries;
        $diary = $db->readDiaryByDate($patient_id, $date);
        $message = array();
        if ($diary) {
            $message['Error'] = false;
            $message['Mensaje'] = "Diario con fecha " . $diary['date'] . " para el paciente con id " . $diary['patient_id'] . " leido correctamente";
            $message['Resultado'] = $diary;

            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ningún diario para la fecha " . $date . "";
            $message['Resultado'] = null;

            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/updateDiary', function (Request $request, Response $response) {
    //var_dump($request->getParsedBody());
    if (!emptyParameters(array('patient_id', 'date', 'sleep_time', 'sport_time', 'alcohol', 'smoke', 'feeling'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];
        $sleep_time = $request_data['sleep_time'];
        $sport_time = $request_data['sport_time'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $feeling = $request_data['feeling'];

        $db = new DbDiaries;
        $result = $db->updateDiary($patient_id, $date, $sleep_time, $sport_time, $alcohol, $smoke, $feeling);
        $message = array();
        if ($result) {
            $message['Error'] = false;
            $message['Mensaje'] = 'Diario actualizado correctamente';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = 'No se ha podido actualizar el diario';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

// TODO: Faltan deleteDiary y ¿checkDiaryFilled? de las peticiones relacionadas con los diarios

// HEADACHE

$app->post('/CharmAppAPI/public/createCrisis', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'start_datetime', 'end_datetime', 'sport', 'alcohol', 'smoke', 'medication', 'feeling', 'pain_scale'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $start_datetime = $request_data['start_datetime'];
        $end_datetime = $request_data['end_datetime'];
        $sport = $request_data['sport'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $medication = $request_data['medication'];
        $feeling = $request_data['feeling'];
        $pain_scale = $request_data['pain_scale'];

        $db = new DbHeadaches;
        $result = $db->createCrisis($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $message = array();
        if ($result == HEADACHE_CREATED) {
            $message['Error'] = false;
            $message['Mensaje'] = 'Crisis creada correctamente';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } elseif ($result == HEADACHE_FAILURE) {
            $message['Error'] = true;
            $message['Mensaje'] = 'Error al crear la crisis';
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = 'No existe el paciente con id ' . $patient_id . "";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->get('/CharmAppAPI/public/readAllCrisis', function (Request $request, Response $response) {
    $db = new DbHeadaches;
    $headaches = $db->readAllCrisis();
    $message = array();
    $message['Error'] = false;
    $message['Mensaje'] = "Crisis leidas correctamente";
    $message['Resultado'] = $headaches;
    $response->getBody()->write(json_encode($message));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->post('/CharmAppAPI/public/readAllCrisisByEmail', function (Request $request, Response $response) {
    if (!emptyParameters(array('email'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];

        $db = new DbHeadaches;
        $crisis = $db->readAllCrisisByEmail($email);
        $message = array();
        if ($crisis) {
            $message['Error'] = false;
            $message['Mensaje'] = "Crisis leidas correctamente";
            $message['Resultado'] = $crisis;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ninguna crisis asociada al paciente con email '" . $email . "'";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readAllCrisisById', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $crisis = $db->readAllCrisisById($patient_id);
        $message = array();
        if ($crisis) {
            $message['Error'] = false;
            $message['Mensaje'] = "Crisis leidas correctamente";
            $message['Resultado'] = $crisis;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ninguna crisis asociada al paciente con id '" . $patient_id . "'";
            $message['Resultado'] = null;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readCrisisByDate', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'date'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $date = $request_data['date'];

        $db = new DbHeadaches;
        $crisis = $db->readCrisisByDate($patient_id, $date);
        $message = array();
        if ($crisis) {
            $message['Error'] = false;
            $message['Mensaje'] = "Crisis con fecha de inicio " . $crisis['start_datetime'] . " para el paciente con id " . $crisis['patient_id'] . " leida correctamente";
            $message['Resultado'] = $crisis;

            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ninguna crisis para la fecha " . $date . "";
            $message['Resultado'] = null;

            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->get('/CharmAppAPI/public/readAllActiveCrisis', function (Request $request, Response $response) {
    $db = new DbHeadaches;
    $crisis = $db->readAllActiveCrisis();
    $message = array();
    if ($crisis) {
        $message['Error'] = false;
        $message['Mensaje'] = "Crisis activas leidas correctamente";
        $message['Resultado'] = $crisis;
        $response->getBody()->write(json_encode($message));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        $message['Error'] = true;
        $message['Mensaje'] = "No hay ninguna crisis activa actualmente";
        $message['Resultado'] = null;
        $response->getBody()->write(json_encode($message));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(404);
    }
});

$app->post('/CharmAppAPI/public/readActiveCrisisByEmail', function (Request $request, Response $response) {
    if (!emptyParameters(array('email'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $email = $request_data['email'];

        $db = new DbHeadaches;
        $crisis = $db->readActiveCrisisByEmail($email);
        $message = array();
        if ($crisis) {
            $message['Error'] = false;
            $message['Mensaje'] = "Crisis activa leida correctamente";
            $message['Resultado'] = $crisis;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = true;
            $message['Mensaje'] = "No hay ninguna crisis activa actualmente para el paciente con email '" . $email . "'";
            $message['Resultado'] = $crisis;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/readActiveCrisisById', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];

        $db = new DbHeadaches;
        $crisis = $db->readActiveCrisisById($patient_id);
        $message = array();
        if ($crisis) {
            $message['Error'] = false;
            $message['Mensaje'] = "Crisis activa leida correctamente";
            $message['Resultado'] = $crisis;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message['Error'] = false;
            $message['Mensaje'] = "No hay ninguna crisis activa actualmente para el paciente con id '" . $patient_id . "'";
            $message['Resultado'] = $crisis;
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/CharmAppAPI/public/updateCrisis', function (Request $request, Response $response) {
    if (!emptyParameters(array('patient_id', 'start_datetime', 'end_datetime', 'sport', 'alcohol', 'smoke', 'medication', 'feeling', 'pain_scale'), $request, $response)) {
        $request_data = $request->getParsedBody();
        $patient_id = $request_data['patient_id'];
        $start_datetime = $request_data['start_datetime'];
        $end_datetime = $request_data['end_datetime'];
        $sport = $request_data['sport'];
        $alcohol = $request_data['alcohol'];
        $smoke = $request_data['smoke'];
        $medication = $request_data['medication'];
        $feeling = $request_data['feeling'];
        $pain_scale = $request_data['pain_scale'];

        $db = new DbHeadaches;
        $result = $db->updateCrisis($patient_id, $start_datetime, $end_datetime, $sport, $alcohol, $smoke, $medication, $feeling, $pain_scale);
        $message = array();
        if ($result) {
            $message['Error'] = false;
            $message['Mensaje'] = 'Crisis actualizada correctamente';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
        } else {
            $message = array();
            $message['Error'] = true;
            $message['Mensaje'] = 'No se ha podido actualizar la crisis';
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);
        }
    } else {
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
});

// TODO: Falta el deleteCrisis y el ¿activeCrisis?

$app->run();
