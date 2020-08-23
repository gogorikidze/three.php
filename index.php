<?php
$frame = $_GET['frame'];
if($frame < 1000){
  echo $frame;
  $frame += 1;
  header('refresh:0.05; url=http://localhost/three/index.php?frame='.$frame);
}
?>
