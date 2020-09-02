<?php
include('../threeHTML.php');
$frame = $_GET['frame'];

$scene = new Scene();
$camera = new RayCamera(new Vec3(0,0,5), 25, 25, 3, 6, false);

$angle = $frame*7*pi()/180;

$geometry = cube(1);
$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$scene->addMesh($mesh);

$camera->render($scene, $frame, true, true);

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
  $geometry->addFace(new Face(3,2,0,'blue'));
  $geometry->addFace(new Face(2,1,0,'blue'));
  //bottom
  $geometry->addFace(new Face(7,6,5,'red'));
  $geometry->addFace(new Face(4,7,5,'red'));

  $geometry->addFace(new Face(0,5,1,'yellow'));
  $geometry->addFace(new Face(0,5,4,'yellow'));

  $geometry->addFace(new Face(4,7,0,'black'));
  $geometry->addFace(new Face(0,3,7,'black'));

  $geometry->addFace(new Face(5,6,1,'grey'));
  $geometry->addFace(new Face(6,2,1,'grey'));

  $geometry->addFace(new Face(7,6,3,'white'));
  $geometry->addFace(new Face(6,2,3,'white'));

  return $geometry;
}
?>
