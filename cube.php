<?php
include('three.php');
$frame = $_GET['frame'];

$scene = new Scene();

//sets up geometry
$geometry = new Geometry();

$geometry->addVertex(new Vec3(-1,-1,-1));
$geometry->addVertex(new Vec3(1,-1,-1));
$geometry->addVertex(new Vec3(1,-1,1));
$geometry->addVertex(new Vec3(-1,-1,1));

$geometry->addVertex(new Vec3(-1,1,-1));
$geometry->addVertex(new Vec3(1,1,-1));
$geometry->addVertex(new Vec3(1,1,1));
$geometry->addVertex(new Vec3(-1,1,1));

$angle = $frame*7*pi()/180;
$geometry->rotate($angle,$angle,$angle);

//top
$geometry->addFace(new Face(3,2,0,'o'));
$geometry->addFace(new Face(2,1,0,'o'));
//bottom
$geometry->addFace(new Face(7,6,5,'x'));
$geometry->addFace(new Face(4,7,5,'x'));

$geometry->addFace(new Face(0,5,1,'0'));
$geometry->addFace(new Face(0,5,4,'0'));

$geometry->addFace(new Face(4,7,0,'1'));
$geometry->addFace(new Face(0,3,7,'1'));

$geometry->addFace(new Face(5,6,1,'2'));
$geometry->addFace(new Face(6,2,1,'2'));

$geometry->addFace(new Face(7,6,3,'3'));
$geometry->addFace(new Face(6,2,3,'3'));

$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera = new Camera(new Vec3(0,0,5), 25, 60, 3, 6, false);
$camera->render($scene, $frame, 'cube.php', true);
?>
