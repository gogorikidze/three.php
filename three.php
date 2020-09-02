<?php session_start();
class Vec3{
  public function __construct($x, $y, $z) {
    $this->x = $x;
    $this->y = $y;
    $this->z = $z;
  }
  public function subv($other){
    return new Vec3($this->x - $other->x, $this->y - $other->y, $this->z - $other->z);
  }
  public function scalarProduct($other){
    return $this->x * $other->x + $this->y * $other->y + $this->z * $other->z;
  }
  public function vectorProduct($other){
    return new Vec3($this->y * $other->z - $this->z * $other->y, $this->z * $other->x - $this->x * $other->z, $this->x * $other->y - $this->y * $other->x);
  }
}
class Camera{
  public function __construct($position, $h, $w, $far, $density, $cacheEnabled){
    $this->frameStart = microtime(true);
    $this->density = $density;
    $this->far = $far;
    $this->position = $position;
    $this->h = $h;
    $this->w = $w;
    $this->frustum = [];
    $this->buffer = [];
    $this->computations = 0;

    if($cacheEnabled){
      if(isset($_SESSION['emptyBuffer'])){
        $this->buffer = $_SESSION['emptyBuffer'];
      }else{
        $this->createBuffer();
      }

      if(isset($_SESSION['frustum'])){
        $this->frustum = $_SESSION['frustum'];
      }else{
        $this->createfrustum();
      }
    }else{
      $this->createBuffer();
      $this->createfrustum();
    }
  }
  public function createfrustum(){ // this grid creates an array of pixels in front of the camera
    $frustum = [];
    //these variables are used to center the array in front of the camera
    $halfwfrustum = $this->w/$this->density/2 - 1/$this->density/2;
    $halfhfrustum = $this->h/$this->density/2 - 1/$this->density/2;

    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        $thisray = new Vec3(
          $this->position->x - $w/$this->density + $halfwfrustum,
          $this->position->y - $h/$this->density + $halfhfrustum,
          $this->position->z + $this->far
        );
        array_push($line, $thisray); //every pixel is pushed in line array
      }
      array_push($frustum, $line); //this scanline (arr) is pushed to main array
    }

    $_SESSION['frustum'] = $frustum;
    $this->frustum = $_SESSION['frustum'];
  }
  public function createBuffer(){
    $buffer = [];
    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        array_push($line, ["empty", 1000]);
      }
      array_push($buffer, $line);
    }
    $_SESSION['emptyBuffer'] = $buffer;
    $this->buffer = $_SESSION['emptyBuffer'];
  }
  public function displayStats($display, $scene){
    if($display){
      $frameTime = microtime(true) - $this->frameStart;
      $fps = 1 / $frameTime;
      $faces = 0;
      foreach ($scene->meshes as $mesh) foreach ($mesh->geometry->faces as $face) $faces += 1;
      echo nl2br("stats: \n");
      echo nl2br("FramesPerSecond: ".$fps."\n");
      echo nl2br("frameTime: ".$frameTime."\n");
      echo nl2br("resolution: ".$this->w."x".$this->h."CPX \n");
      echo nl2br("Calculations: ".$this->computations."\n");
    }
  }
}
class RasterCamera extends Camera{
  public function renderPixel($h, $w, $scene){
    foreach($scene->meshes as $mesh){
      foreach($mesh->geometry->faces as $face){
        $point = $this->frustum[$h][$w];
        $barycentric = $face->barycentricAt($point, $mesh->geometry);
        $z = $face->zCoordAt($face, $barycentric);
        if($face->isPointInTriangle($barycentric) && $z < $this->buffer[$h][$w][1]){
            $this->buffer[$h][$w] = [$face->colorChar, $z];
        }
        $this->computations += 1;
      }
    }
  }
  public function projectFaces($scene){ //just changes point Z co-ords to Cameras Z co-ords
    foreach($scene->meshes as $mesh){
      foreach($mesh->geometry->faces as $face){
        $v0 = $mesh->geometry->vertices[$face->v0index];
        $v1 = $mesh->geometry->vertices[$face->v1index];
        $v2 = $mesh->geometry->vertices[$face->v2index];

        $face->originalZ->x = $v0->z;
        $face->originalZ->y = $v1->z;
        $face->originalZ->z = $v2->z;

        $v0->z = $this->position->z;
        $v1->z = $this->position->z;
        $v2->z = $this->position->z;
      }
    }
  }
  public function render($renderer, $scene, $frame, $sysInfo){
    $renderer->nextFrame($frame);

    $this->projectFaces($scene);

    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        $this->renderPixel($h, $w, $scene);
      }
    }

    $this->renderBuffer();

    $this->displayStats($sysInfo, $scene);
  }
}
class RayCamera extends Camera{
  public function renderFace($face, $geometry, $mesh){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        $rayorigin = $this->frustum[$h][$w];
        $raydirection = new Vec3(0,0,-1);
        $ray = $face->checkRay($rayorigin, $raydirection, $geometry);
        if($ray && $ray < $this->buffer[$h][$w][1]){
            $this->buffer[$h][$w] = [$face->colorChar, $ray];
        }
        $this->computations += 1;
      }
    }
  }
  public function render($renderer, $scene, $frame, $sysInfo){
    $renderer->nextFrame($frame);

    foreach($scene->meshes as $mesh){
      foreach($mesh->geometry->faces as $face){
          $this->renderFace($face, $mesh->geometry, $mesh);
      }
    }

    $renderer->renderBuffer($this);

    $this->displayStats($sysInfo, $scene);
  }
}
class Face{
  public function __construct($v0index, $v1index, $v2index, $colorChar){
    $this->v0index = $v0index;
    $this->v1index = $v1index;
    $this->v2index = $v2index;
    $this->colorChar = $colorChar;
    $this->originalZ = new Vec3(0,0,0);
  }
  public function checkRay($o, $d, $geometry){ //checks for ray triangle intersection
    $v0 = $geometry->vertices[$this->v0index];
    $v1 = $geometry->vertices[$this->v1index];
    $v2 = $geometry->vertices[$this->v2index];
    $eO = $o; // origin point
    $eD = $d; // direction
    $eV0 = $v0;
    $eV1 = $v1;
    $eV2 = $v2;
    $eE1 = $eV1->subv($eV0);
    $eE2 = $eV2->subv($eV0);
    $eT = $eO->subv($eV0);
    $eP = $eD->vectorProduct($eE2);
    $eQ = $eT->vectorProduct($eE1);
    if($eP->scalarProduct($eE1) != 0){
      $equc = 1 / $eP->scalarProduct($eE1);
    }else{
      return false;
    }
    $u = $equc * $eP->scalarProduct($eT);
    if($u < 0){return false;}
    $v = $equc * $eQ->scalarProduct($eD);
    if($v < 0 || $v + $u > 1){return false;}
    $t = $equc * $eQ->scalarProduct($eE2);
    if($t < 0){return false;}
    return $t;
  }
  public function isPointInTriangle($barycentric){
    return $barycentric->x > 0 && $barycentric->x < 1 && $barycentric->y > 0 && $barycentric->y < 1 && $barycentric->z > 0 && $barycentric->z < 1;
  }
  public function barycentricAt($point, $geometry){
    $a = $geometry->vertices[$this->v0index];
    $b = $geometry->vertices[$this->v1index];
    $c = $geometry->vertices[$this->v2index];

    $v0 = $b->subv($a);
    $v1 = $c->subv($a);
    $v2 = $point->subv($a);
    $d00 = $v0->scalarProduct($v0);
    $d01 = $v0->scalarProduct($v1);
    $d11 = $v1->scalarProduct($v1);
    $d20 = $v2->scalarProduct($v0);
    $d21 = $v2->scalarProduct($v1);
    $denom = $d00 * $d11 - $d01 * $d01;
    $v = ($d11 * $d20 - $d01 * $d21) / $denom;
    $w = ($d00 * $d21 - $d01 * $d20) / $denom;
    $u = 1 - $v - $w;
    return new Vec3($u, $v, $w);
  }
  public function zCoordAt($face, $barycentric){
    $v0z = $face->originalZ->x;
    $v1z = $face->originalZ->y;
    $v2z = $face->originalZ->z;
    return $barycentric->x*$v0z + $barycentric->y*$v1z + $barycentric->z*$v2z;
  }
}
class Scene{
  public function __construct(){
    $this->meshes = [];
  }
  public function addMesh($mesh){
    array_push($this->meshes, $mesh);
  }
}
class Mesh{
  public function __construct($geometry, $colorChar){
    $this->geometry = $geometry;
    $this->colorChar = $colorChar;
  }
  public function setPosition($x, $y, $z){
    foreach ($this->geometry->vertices as $vertex) {
      $vertex->x += $x;
      $vertex->y += $y;
      $vertex->z += $z;
    }
  }
}
class Geometry{
  public function __construct(){
    $this->faces = [];
    $this->vertices = [];
  }
  public function addFace($face){
    array_push($this->faces, $face);
  }
  public function addVertex($vertex){
    array_push($this->vertices, $vertex);
  }
  public function rotate($pitch, $roll, $yaw) {
    $cosa = cos($yaw);
    $sina = sin($yaw);

    $cosb = cos($pitch);
    $sinb = sin($pitch);

    $cosc = cos($roll);
    $sinc = sin($roll);

    $Axx = $cosa*$cosb;
    $Axy = $cosa*$sinb*$sinc - $sina*$cosc;
    $Axz = $cosa*$sinb*$cosc + $sina*$sinc;

    $Ayx = $sina*$cosb;
    $Ayy = $sina*$sinb*$sinc + $cosa*$cosc;
    $Ayz = $sina*$sinb*$cosc - $cosa*$sinc;

    $Azx = -$sinb;
    $Azy = $cosb*$sinc;
    $Azz = $cosb*$cosc;

    foreach($this->vertices as $vertex){
      $px = $vertex->x;
      $py = $vertex->y;
      $pz = $vertex->z;

      $vertex->x = $Axx*$px + $Axy*$py + $Axz*$pz;
      $vertex->y = $Ayx*$px + $Ayy*$py + $Ayz*$pz;
      $vertex->z = $Azx*$px + $Azy*$py + $Azz*$pz;
    }
  }
}
class CubeGeometry extends Geometry{
  public function __construct($size, $colors){
    $this->faces = [];
    $this->vertices = [];

    $top = $colors[0];
    $bottom = $colors[1];
    $front = $colors[2];
    $back = $colors[3];
    $left = $colors[4];
    $right = $colors[5];

    $this->addVertex(new Vec3(-$size,-$size,-$size));
    $this->addVertex(new Vec3($size,-$size,-$size));
    $this->addVertex(new Vec3($size,-$size,$size));
    $this->addVertex(new Vec3(-$size,-$size,$size));

    $this->addVertex(new Vec3(-$size,$size,-$size));
    $this->addVertex(new Vec3($size,$size,-$size));
    $this->addVertex(new Vec3($size,$size,$size));
    $this->addVertex(new Vec3(-$size,$size,$size));

    //top
    $this->addFace(new Face(3,2,0,$top));
    $this->addFace(new Face(2,1,0,$top));
    //bottom
    $this->addFace(new Face(7,6,5,$bottom));
    $this->addFace(new Face(4,7,5,$bottom));

    $this->addFace(new Face(0,5,1,$front));
    $this->addFace(new Face(0,5,4,$front));

    $this->addFace(new Face(4,7,0,$left));
    $this->addFace(new Face(0,3,7,$left));

    $this->addFace(new Face(5,6,1,$right));
    $this->addFace(new Face(6,2,1,$right));

    $this->addFace(new Face(7,6,3,$back));
    $this->addFace(new Face(6,2,3,$back));
  }
}
class Renderer{
  public function __construct($animate){
    $this->animate = $animate;
  }
  public function nextFrame($frame){
    if($this->animate){
      header('refresh:0.001; url='.basename($_SERVER['PHP_SELF']).'?frame='.($frame+1));
    }
  }
  public function renderBuffer($camera){
    for($h = 0; $h < $camera->h; $h++){
      for($w = 0; $w < $camera->w; $w++){
        if($camera->buffer[$h][$w][0] != "empty"){
          echo $camera->buffer[$h][$w][0];
          echo $camera->buffer[$h][$w][0];
        }else{
          echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
      }
      echo nl2br("\n");
    }
  }
}
class HTMLRenderer extends Renderer{
  public function renderBuffer($camera){
    for($h = 0; $h < $camera->h; $h++){
      echo "<div style='overflow:hidden'>";
      for($w = 0; $w < $camera->w; $w++){
        if($camera->buffer[$h][$w][0] != "empty"){
          echo "<div style='float: left;height:2px;width:2px;background-color:".$camera->buffer[$h][$w][0].";padding:0;margin:0'></div>";
        }else{
          echo "<div style='float: left;height:2px;width:2px;background-color:cyan;padding:0;margin:0'></div>";
        }
      }
      echo "</div>";
    }
  }
}
?>
