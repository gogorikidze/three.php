<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$camera = new RasterCamera(new Vec3(0,0.5,5), 50, 200, 3, 30, false);

$geometry = new Geometry();
$geometry->addVertex(new Vec3(0,1,0));
$geometry->addVertex(new Vec3(-1,0,0));
$geometry->addVertex(new Vec3(1,0,0));
$geometry->addFace(new Face(0,1,2,'#'));
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$geometry1 = new Geometry();
$geometry1->addVertex(new Vec3(0,1,0));
$geometry1->addVertex(new Vec3(-1,0,0));
$geometry1->addVertex(new Vec3(1,0,0));
$geometry1->addFace(new Face(0,1,2,'$'));
$mesh1 = new Mesh($geometry1, '$');
$mesh1->setPosition(1,0,2);
$scene->addMesh($mesh1);

$camera->render($scene, $frame, true, false);
?>
