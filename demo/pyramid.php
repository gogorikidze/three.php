<?php
include('../three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$renderer = new Renderer(true);

//sets up geometry
$geometry = new Geometry();

$geometry->addVertex(new Vec3(-1,0,-1));
$geometry->addVertex(new Vec3(1,0,-1));
$geometry->addVertex(new Vec3(0,0,1));
$geometry->addVertex(new Vec3(0,1.5,0));

$geometry->rotate($frame*7*pi()/180,0,0);

$geometry->addFace(new Face(0,3,1,'$'));
$geometry->addFace(new Face(1,3,2,'#'));
$geometry->addFace(new Face(0,3,2,'9'));
$geometry->addFace(new Face(0,1,2,'p'));

$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera = new RayCamera(new Vec3(0,0.7,5), 15, 35, 3, 10, false);
$camera->render($renderer, $scene, $frame, true);
?>
