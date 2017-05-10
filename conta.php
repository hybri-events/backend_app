<?php
  include_once 'conn.php';

  $a=array();
  $b=array();
  $c=array();

  if ( isset($_POST["q"]) ){
    $q = $_POST["q"];
    if ( $q == "sel" ){
      if ( isset($_POST["login"]) ){
        $login = $_POST["login"];
        $dt_ini = $_POST["dt_ini"];
        $dt_fim = $_POST["dt_fim"];

        $sql = "SELECT conta.saldo,movimento_conta.descricao,DATE_FORMAT(movimento_conta.dt_hr,'%d/%m/%Y %H:%i:%s') as dt_hr,movimento_conta.tipo,movimento_conta.valor
                FROM usuario INNER JOIN conta ON usuario.idusuario = conta.idusuario INNER JOIN movimento_conta ON movimento_conta.idconta = conta.idconta
                WHERE movimento_conta.dt_hr BETWEEN '".$dt_ini."' AND ADDDATE('".$dt_fim."',INTERVAL 1 DAY) AND usuario.login = '".$login."'";

        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b["saldo"] = $row['saldo'];
            $b["descricao"] = $row['descricao'];
            $b["dt_hr"] = $row['dt_hr'];
            $b["tipo"] = $row['tipo'];
            $b["valor"] = $row['valor'];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "pay" ){
      if ( isset($_POST["idpagante"]) ){
        $idpagante = $_POST["idpagante"];
        $idrecebedor = $_POST["idrecebedor"];
        $nomepag = $_POST["nomepag"];
        $nomerec = $_POST["nomerec"];
        $valor = $_POST["valor"];
        $data = $_POST["data"];

        $sql_cpag = "SELECT idconta FROM usuario INNER JOIN conta ON conta.idusuario = usuario.idusuario WHERE usuario.idusuario = ".$idpagante;
        $res_cpag = mysqli_query($conn, $sql_cpag);

        $sql_crec = "SELECT idconta FROM usuario INNER JOIN conta ON conta.idusuario = usuario.idusuario WHERE usuario.idusuario = ".$idrecebedor;
        $res_crec = mysqli_query($conn, $sql_crec);

        if ( mysqli_num_rows($res_cpag) > 0 && mysqli_num_rows($res_crec) > 0 ){
          $cpag = mysqli_fetch_array($res_cpag)['idconta'];
          $crec = mysqli_fetch_array($res_crec)['idconta'];

          $sql_mpag = "INSERT INTO movimento_conta VALUES(null,".$cpag.",'Pagamento efetuado para ".$nomerec.".',".$valor.",0,NOW(),1)";
          $res_mpag = mysqli_query($conn, $sql_mpag);

          $sql_mrec = "INSERT INTO movimento_conta VALUES(null,".$crec.",'Recebimento efetuado por ".$nomepag.".',".$valor.",1,NOW(),1)";
          $res_mrec = mysqli_query($conn, $sql_mrec);

          if ( $res_mpag && $res_mrec ){
            $sql_npag = "UPDATE conta SET saldo = saldo - ".$valor." WHERE idconta = ".$cpag;
            $res_npag = mysqli_query($conn, $sql_npag);

            $sql_nrec = "UPDATE conta SET saldo = saldo + ".$valor." WHERE idconta = ".$crec;
            $res_nrec = mysqli_query($conn, $sql_nrec);
            if ( $res_npag && $res_nrec ){
              $b["res"] = 1;
              array_push($a,$b);
            } else {
              $b["res"] = 0;
              array_push($a,$b);
            }
          } else {
            $b["res"] = 0;
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
