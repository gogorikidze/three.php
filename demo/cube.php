<?php
include('../three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$camera = new RayCamera(new Vec3(0,0,5), 25, 25, 3, 6, false);

$angle = $frame*7*pi()/180;

$geometry = cube(1);
$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera->render($scene, $frame, true);

function cube($size){
  $geometry = new Geometry();

  $geometry->addVertex(new Vec3(-$size,-$size,-$size));
  $geometry->addVertex(new Vec3($size,-$size,-$size));
  $geometry->addVertex(new Vec3($size,-$size,$size));
  $geometry->addVertex(new Vec3(-$size,-$size,$size));

  $geometry->addVertex(new Vec3(-$size,$size,-$size));
  $geometry->addVertex(new Vec3($size,$size,-$size));
  $geometry->addVertex(new Vec3($size,$size,$size));
  $geometry->addVertex(new Vec3(-$size,$size,$size));

  //top
  $geometry->addFace(new Face(3,2,0,'5'));
  $geometry->addFace(new Face(2,1,0,'5'));
  //bottom
  $geometry->addFace(new Face(7,6,5,'4'));
  $geometry->addFace(new Face(4,7,5,'4'));

  $geometry->addFace(new Face(0,5,1,'0'));
  $geometry->addFace(new Face(0,5,4,'0'));

  $geometry->addFace(new Face(4,7,0,'1'));
  $geometry->addFace(new Face(0,3,7,'1'));

  $geometry->addFace(new Face(5,6,1,'2'));
  $geometry->addFace(new Face(6,2,1,'2'));

  $geometry->addFace(new Face(7,6,3,'3'));
  $geometry->addFace(new Face(6,2,3,'3'));

  return $geometry;
}
?>
