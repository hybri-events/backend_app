<?php
  include_once 'conn.php';

  $a=array();
  $b=array();

  if ( isset($_POST["q"]) ){
    $q = $_POST["q"];
    if ( $q == "usu" ){
      if ( isset($_POST["pesq"]) ){
        $pesq = $_POST["pesq"];
        $limit = $_POST["limit"];
        $max = $_POST["max"];

        $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario
                WHERE usuario.nome LIKE '%".$pesq."%' OR usuario.login LIKE '%".$pesq."%' GROUP BY usuario.idusuario LIMIT ".$limit.",".$max;

        $res = mysqli_query($conn,$sql);

        if ( mysqli_num_rows($res) ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b["id"] = $row["idusuario"];
            $b["nome"] = $row["nome"];
            $b["foto"] = $row["ft_perfil"];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "emp" ){
      if ( isset($_POST["pesq"]) ){
        /*$pesq = $_POST["pesq"];

        $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario
                WHERE usuario.nome LIKE '%".$pesq."%' OR usuario.login LIKE '%".$pesq."%' OR usuario.email LIKE '%".$pesq."%' OR usuario.fone LIKE '%".$pesq."%'";
        $res = mysqli_query($conn,$sql);

        if ( mysqli_num_rows($res) ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b["idusuario"] = $row["idusuario"];
            $b["nome"] = $row["nome"];
            $b["foto"] = $row["ft_perfil"];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }*/
      }
    } else if ( $q == "eve" ){
      if ( isset($_POST["pesq"]) ){
        $pesq = $_POST["pesq"];
        $limit = $_POST["limit"];
        $max = $_POST["max"];

        $sql = "SELECT idevento, nome, img FROM evento
                WHERE nome LIKE '%".$pesq."%' AND privado = 0 GROUP BY idevento LIMIT ".$limit.",".$max;
        $res = mysqli_query($conn,$sql);

        if ( mysqli_num_rows($res) ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b["id"] = $row["idevento"];
            $b["nome"] = $row["nome"];
            $b["foto"] = $row["img"];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "history" ){
      if ( isset($_POST["pesq"]) ){
        $pesq = $_POST["pesq"];
        $idusu = $_POST["idusu"];

        $sql_del = "DELETE FROM historico WHERE idusuario = ".$idusu." AND conteudo = '".$pesq."'";
        $res_del = mysqli_query($conn,$sql_del);

        $sql = "INSERT INTO historico VALUES(null, ".$idusu.", '".$pesq."', NOW())";
        $res = mysqli_query($conn,$sql);

        if($res){
          $b["res"] = 1;
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }

        $sql = "SELECT * FROM historico WHERE idusuario = ".$idusu;
        $res = mysqli_query($conn,$sql);
        $num = mysqli_num_rows($res);

        if ( $num > 5 ){
          for ($i=0;$i<($num-5);$i++){
            $row = mysqli_fetch_array($res);
            $sql_del = "DELETE FROM historico WHERE idhistorico = ".$row["idhistorico"];
            $res_del = mysqli_query($conn,$sql_del);
          }
        }
      }
    } else if ( $q == "gethistory" ){
      if ( isset($_POST["idusu"]) ){
        $idusu = $_POST["idusu"];

        $sql = "SELECT conteudo FROM historico WHERE idusuario = ".$idusu." ORDER BY dt_hr DESC LIMIT 0,5";
        $res = mysqli_query($conn,$sql);

        if ( $res ){
          $b["res"] = 1;
          while( $row = mysqli_fetch_array($res) ){
            $b["conteudo"] = $row["conteudo"];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    }
  }

  echo json_encode($a);
?>
