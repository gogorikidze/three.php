<?php
include('../three.php');
$frame = $_GET['frame'];

$scene = new Scene();
$camera = new RasterCamera(new Vec3(0,0,5), 60, 60, 3, 6, false);

$angle = $frame*7*pi()/180;

$geometry = cube(1);
$geometry->rotate($angle,-$angle,$angle*3);
$mesh = new Mesh($geometry, '#');
$mesh->setPosition(-3.5,3.5,0);
$scene->addMesh($mesh);

$geometry1 = cube(1);
$geometry1->rotate(-$angle*1.2,$angle,-$angle);
$mesh1 = new Mesh($geometry1, '#');
$mesh1->setPosition(0,3.5,0);
$scene->addMesh($mesh1);

$geometry2 = cube(1);
$geometry2->rotate($angle,-$angle*2,-$angle);
$mesh2 = new Mesh($geometry2, '#');
$mesh2->setPosition(3.5,3.5,0);
$scene->addMesh($mesh2);

$geometry3 = cube(1);
$geometry3->rotate($angle,-$angle,$angle*3);
$mesh3 = new Mesh($geometry3, '#');
$mesh3->setPosition(-3.5,-3.5,0);
$scene->addMesh($mesh3);

$geometry4 = cube(1);
$geometry4->rotate(-$angle*1.2,$angle,-$angle);
$mesh4 = new Mesh($geometry4, '#');
$mesh4->setPosition(0,-3.5,0);
$scene->addMesh($mesh4);

$geometry5 = cube(1);
$geometry5->rotate($angle,-$angle*2,-$angle);
$mesh5 = new Mesh($geometry5, '#');
$mesh5->setPosition(3.5,-3.5,0);
$scene->addMesh($mesh5);

//
$geometry6 = cube(1);
$geometry6->rotate(-$angle,$angle,-$angle*3);
$mesh6 = new Mesh($geometry6, '#');
$mesh6->setPosition(-3.5,0,0);
$scene->addMesh($mesh6);

$geometry7 = cube(1);
$geometry7->rotate($angle*1.2,-$angle,$angle);
$mesh7 = new Mesh($geometry7, '#');
$mesh7->setPosition(0,0,0);
$scene->addMesh($mesh7);

$geometry8 = cube(1);
$geometry8->rotate(-$angle,$angle*2,$angle);
$mesh8 = new Mesh($geometry8, '#');
$mesh8->setPosition(3.5,0,0);
$scene->addMesh($mesh8);

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
