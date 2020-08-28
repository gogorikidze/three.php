<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();

//sets up geometry
$geometry = new Geometry();

$geometry->addVertex(new Vec3(0,0,0));
$geometry->addVertex(new Vec3(1,0,0));
$geometry->addVertex(new Vec3(0.5,0,0.5));
$geometry->addVertex(new Vec3(0.5,1,0.5));
$geometry->addVertex(new Vec3(0.5,0.5,0.5));

$geometry->addFace(new Face(new Vec3(0,1,2), '#'));
$geometry->addFace(new Face(new Vec3(0,2,3), '$'));
$geometry->addFace(new Face(new Vec3(1,2,3), '9'));
$geometry->addFace(new Face(new Vec3(0,1,4), '0'));

$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera = new Camera(new Vec3(0,0,-5), 133/2, 239/2, 3, 30/2, true);
$camera->render($scene, $frame);
?>
