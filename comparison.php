<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$camera = new RayCamera(new Vec3(0,0.5,5), 100, 200, 3, 30, false);

$geometry = new Geometry();

$geometry->addVertex(new Vec3(0,1,0));
$geometry->addVertex(new Vec3(-1,0,0));
$geometry->addVertex(new Vec3(1,0,0));

$geometry->addFace(new Face(0,1,2,'#'));

$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera->render($scene, $frame, true);
?>
