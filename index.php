<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();

//sets up geometry
$geometry = new Geometry();

$geometry->addVertex(new Vec3(-1,0,-1));
$geometry->addVertex(new Vec3(1,0,-1));
$geometry->addVertex(new Vec3(0,0,1));
$geometry->addVertex(new Vec3(0,1,0));

$geometry->rotate($frame*5*pi()/180,0,0);

$geometry->addFace(new Face(0,3,1,'$'));
$geometry->addFace(new Face(1,3,2,'#'));
$geometry->addFace(new Face(0,3,2,'9'));
$geometry->addFace(new Face(0,1,2,'p'));

$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera = new Camera(new Vec3(0,2,5), 133/4, 239/4, 3, 30/4, false);
$camera->render($scene, $frame);
?>
