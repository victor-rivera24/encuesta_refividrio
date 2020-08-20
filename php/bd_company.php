<?php
//action.php

require_once "postgres.php";
session_start();

    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();

    if ($received_data->action == 'fetchall') {
        $query = " SELECT * FROM empresa ORDER BY id_empresa DESC ";
        $statement = $connect->prepare($query);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

if ($received_data->action == 'Agregar') {
    $data = array(
        ':first_name' => $received_data->firstName,
        ':last_name' => $received_data->lastName,
        ':checked' => $received_data->checked,
        ':id_creado' =>  $_SESSION['id_empleado'],
        ':id_actualizadopor' =>  $_SESSION['id_empleado']


    );

    $query = "INSERT INTO empresa (id_creado,fecha_creado,empresa_nombre, empresa_rfc,empresa_activo,id_actualizado,fecha_actualizado)
     VALUES (:id_creado,CURRENT_TIMESTAMP,:first_name, :last_name,:checked::boolean,:id_actualizadopor,CURRENT_TIMESTAMP)";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    $output = array(
        'message' => 'Empresa Registrada'
    );

    echo json_encode($output);

}

if ($received_data->action == 'fetchSingle') {
    $query = "SELECT * FROM empresa WHERE id_empresa = '" . $received_data->id . "' ";

    $statement = $connect->prepare($query);

    $statement->execute();

    $result = $statement->fetchAll();

    foreach ($result as $row) {
        $data['id'] = $row['id_empresa'];
        $data['first_name'] = $row['empresa_nombre'];
        $data['last_name'] = $row['empresa_rfc'];
        $data['checked'] = $row['empresa_activo'];

    } 
    echo json_encode($data);
} 

if ($received_data->action == 'Modificar') {
    $data = array(
        ':first_name' => $received_data->firstName,
        ':last_name' => $received_data->lastName,
        ':checked' => $received_data->checked,
        ':id_actualizadopor' =>  $_SESSION['id_empleado'],
        ':id' => $received_data->hiddenId

    );

    $query = "UPDATE empresa SET empresa_nombre = :first_name
                                 ,empresa_rfc = :last_name 
                                 ,fecha_actualizado = CURRENT_TIMESTAMP
                                 ,empresa_activo = :checked 
                                 ,id_actualizado = :id_actualizadopor
             WHERE id_empresa = :id";

    $statement = $connect->prepare($query);
    $statement->execute($data);

    $output = array(
        'message' => 'Empresa Actualizada'
    );

    echo json_encode($output);
}

if ($received_data->action == 'delete') {

    $query = "DELETE FROM empresa WHERE id_empresa = '" . $received_data->id . "' ";

    $statement = $connect->prepare($query);

    $statement->execute();

    $output = array(
        'message' => 'Empresa Eliminada'
    );

    echo json_encode($output);
}