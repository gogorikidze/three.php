<?php
include('../three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$renderer = new HTMLRenderer(true);

//sets up geometry
$geometry = new Geometry();

$geometry->addVertex(new Vec3(-1,0,-1));
$geometry->addVertex(new Vec3(1,0,-1));
$geometry->addVertex(new Vec3(0,0,1));
$geometry->addVertex(new Vec3(0,1.5,0));

$geometry->rotate($frame*7*pi()/180,0,0);

$geometry->addFace(new Face(0,3,1,'blue'));
$geometry->addFace(new Face(1,3,2,'red'));
$geometry->addFace(new Face(0,3,2,'green'));
$geometry->addFace(new Face(0,1,2,'white'));

$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera = new RayCamera(new Vec3(0,0.7,5), 35, 35, 3, 10, false);
$camera->render($renderer, $scene, $frame, true);
?>
