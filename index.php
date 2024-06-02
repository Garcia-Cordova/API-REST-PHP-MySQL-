<?php

$host="localhost";
$usuario="root";
$password="";
$basededatos="biblioteca";
$puerto="3307";

$conexion = new mysqli($host,$usuario,$password,$basededatos,$puerto);

if($conexion->connect_error){
    die ("Conexion no establecida". $conexion->connect_error);
}

header("Content-Type: application/json");
$metodo= $_SERVER["REQUEST_METHOD"];

$path= isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';

$buscarId = explode("/",$path);

$id= ($path!=='/')? end($buscarId):null;


switch ($metodo){

    // CONSULTA SELECT
    case 'GET':
        
        consulta($conexion);
        break;

    // INSERT
    case 'POST':
        
        insertar($conexion);
        break;

    // UPDATE
    case 'PUT':

        actualizar($conexion, $id);
       
        break;

    // DELETE
    case 'DELETE':
        borrar($conexion, $id);
        echo "  Borrado del registro - DELETE";
        break;

    default:
        echo "Metodo no permitido";
        break;
}

function consulta($conexion) {
    $sql = "SELECT * FROM libros";
    $resultado= $conexion->query($sql);

    if($resultado){
        $datos= array();
        while($fila= $resultado->fetch_assoc()){
            $datos[]= $fila;
        }
        echo json_encode($datos);
    }
}

function insertar($conexion) {
    $dato= json_decode(file_get_contents('php://input'),true);
    $nombre= $dato['nombre'];
    $imagen= $dato['imagen'];
    
    $sql = "INSERT INTO libros(nombre,imagen) VALUES('$nombre','$imagen')";
    $resultado= $conexion->query($sql);

    if($resultado){
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    }else{
        echo json_encode(array('error'=>'Error al crear libro'));
    }
}

function borrar($conexion, $id) {

    echo "El id a borrar es: ". $id;

    $sql = "DELETE FROM libros WHERE id = $id";
    $resultado= $conexion->query($sql);

    if($resultado){
        echo json_encode(array('mensaje'=>'Libro borrado'));
    }else{
        echo json_encode(array('error'=>'Error al crear libro'));
    }
}

function actualizar($conexion, $id) {

    $dato= json_decode(file_get_contents('php://input'),true);
    $nombre= $dato['nombre'];
    $imagen= $dato['imagen'];

    echo "El id a actualizar es: ". $id. " con el dato " .$nombre . "." . $imagen;

    $sql = "UPDATE libros SET nombre = '$nombre', imagen ='$imagen' WHERE id = $id";
    $resultado= $conexion->query($sql);

    if($resultado){
        echo json_encode(array('mensaje'=>'Libro actualizado'));
    }else{
        echo json_encode(array('error'=>'Error al actualizar libro'));
    }
}
?>