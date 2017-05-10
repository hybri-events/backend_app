<?php
  include_once 'conn.php';

  $a=array();
  $b=array();
  define('UPLOAD_DIR', 'profile/ft_perfil/');

  if ( isset($_POST["q"]) ){
    $q = $_POST["q"];
    if ( $q == "cad" ){
      if ( isset($_POST["nome"]) ){
        $nome = $_POST["nome"];
        $data = $_POST["data"];
        $cidade = $_POST["cidade"];
        $email = $_POST["email"];
        $fone = $_POST["fone"];
        $foto = $_POST["foto"];
        $login = $_POST["login"];
        $senha = $_POST["senha"];
        $key = $_POST["key"];

        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i:s', time());

        $sql = "INSERT INTO usuario VALUES (null, '".$nome."', '".$email."', '".$fone."', '".$login."', '".password_hash($senha,PASSWORD_DEFAULT)."', '".$date."')";
        $res = mysqli_query($conn, $sql);

        if($res){
          $sql1 = "SELECT * FROM usuario WHERE login = '".$login."'";
          $res1 = mysqli_query($conn, $sql1);
          $row = mysqli_fetch_array($res1);
          if ( $foto != 'profile/ft_perfil/padrao.png' ){
            $base64img = str_replace('data:image/png;base64,', '', $foto);
            $d = base64_decode($base64img);
            $file = UPLOAD_DIR.'IMG_'.$login.'.png';
            file_put_contents('../'.$file, $d);
          } else {
            $file = $foto;
          }
          $sql2 = "INSERT INTO perfil VALUES (null, ".$row['idusuario'].", ".$cidade.", null, '".$data."', null, 'http://usevou.com/".$file."', 'http://usevou.com/profile/ft_capa/padrao.png', null, null)";
          $res2 = mysqli_query($conn, $sql2);
          $sql3 = "INSERT INTO conta VALUES (null, ".$row['idusuario'].", 100)";
          $res3 = mysqli_query($conn, $sql3);
          $sql4 = "INSERT INTO seguidor VALUES (null, ".$row['idusuario'].", ".$row['idusuario'].", 1, NOW())";
          $res4 = mysqli_query($conn, $sql4);
          if($res2 && $res3 && $res4){
            $sql2 = "SELECT * FROM conta WHERE idusuario = '".$row['idusuario']."'";
            $res2 = mysqli_query($conn, $sql2);
            $row1 = mysqli_fetch_array($res2);
            $sql4 = "INSERT INTO movimento_conta VALUES (null, ".$row1['idconta'].",'Bonificação por se juntar à nós.',100,1,'".$date."',true)";
            $res4 = mysqli_query($conn, $sql4);
            if ( $res4 ){
              $b["res"] = 1;
            }else{
              $b["res"] = 0;
            }
          }else{
            $b["res"] = 0;
          }
        }else{
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "alt" ) {

    } elseif ( $q == "sel" ){
      if ( isset($_POST["login"]) ){
        $login = $_POST["login"];

        $sql = "SELECT usuario.idusuario,nome,email,fone,login,ft_perfil,ft_capa FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario WHERE login = '".$login."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["idusuario"] = $row['idusuario'];
          $b["nome"] = $row['nome'];
          $b["email"] = $row['email'];
          $b["fone"] = $row['fone'];
          $b["login"] = $row['login'];
          $b["ft_perfil"] = $row['ft_perfil'];
          $b["ft_capa"] = $row['ft_capa'];
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "email" ){
      if ( isset($_POST["email"]) ){
        $email = $_POST["email"];

        $sql = "SELECT * FROM usuario WHERE email = '".$email."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "update_email" ){
      if ( isset($_POST["email"]) ){
        $id = $_POST["id"];
        $email = $_POST["email"];

        $sql = "UPDATE usuario SET email = '".$email."' WHERE idusuario = ".$id;
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "fone" ){
      if ( isset($_POST["fone"]) ){
        $fone = $_POST["fone"];

        $sql = "SELECT * FROM usuario WHERE fone = '".$fone."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "update_fone" ){
      if ( isset($_POST["fone"]) ){
        $id = $_POST["id"];
        $fone = $_POST["fone"];

        $sql = "UPDATE usuario SET fone = '".$fone."' WHERE idusuario = ".$id;
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "login" ){
      if ( isset($_POST["login"]) ){
        $login = $_POST["login"];

        $sql = "SELECT * FROM usuario WHERE login = '".$login."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "update_login" ){
      if ( isset($_POST["login"]) ){
        $id = $_POST["id"];
        $login = $_POST["login"];

        $sql = "UPDATE usuario SET login = '".$login."' WHERE idusuario = ".$id;
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "update_senha" ){
      if ( isset($_POST["senha"]) ){
        $id = $_POST["id"];
        $senha = $_POST["senha"];

        $sql = "UPDATE usuario SET senha = '".password_hash($senha,PASSWORD_DEFAULT)."' WHERE idusuario = ".$id;
        echo $sql;
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "logar" ){
      if ( isset($_POST["login"]) ){
        $login = $_POST["login"];
        $senha = $_POST["senha"];

        $sql = "SELECT * FROM usuario WHERE login = '".$login."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $row = mysqli_fetch_array($res);
          if ( password_verify($senha,$row['senha']) ){
            $b["res"] = 1;
            $b["id"] = $row['idusuario'];
          } else {
            $b["res"] = 0;
          }
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } elseif ( $q == "ft_perfil" ){
      if ( isset($_POST["login"]) ){
        $login = $_POST["login"];

        $sql = "SELECT * FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario WHERE login = '".$login."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["ft_perfil"] = $row['ft_perfil'];
        } else {
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    }
  }

  echo json_encode($a);
?>
