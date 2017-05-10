<?php
  include_once 'conn.php';

  $a=array();
  $b=array();

  define('UPLOAD_DIR', 'events/');

  if ( isset($_POST["q"]) ){
    $q = $_POST["q"];
    if( $q == "tag" ) {
      $sql = "SELECT * FROM tipo_evento ORDER BY nome ASC";
      $res = mysqli_query($conn, $sql);

      if(mysqli_num_rows($res) > 0){
        $b["res"] = 1;
        while ($row =  mysqli_fetch_array($res)){
          $b["id"] = $row["idtipo_evento"];
          $b["nome"] = $row["nome"];
          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "tag_filtro" ) {
      $sql = "SELECT tipo_evento.idtipo_evento, tipo_evento.nome FROM tipo_evento INNER JOIN tag_evento ON tag_evento.idtipo_evento = tipo_evento.idtipo_evento ORDER BY nome ASC";
      $res = mysqli_query($conn, $sql);

      if(mysqli_num_rows($res) > 0){
        $b["res"] = 1;
        while ($row =  mysqli_fetch_array($res)){
          $b["id"] = $row["idtipo_evento"];
          $b["nome"] = $row["nome"];
          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "cadtag" ) {
      if ( isset($_POST["nome"]) ){
        $nome = $_POST["nome"];
        $sql = "INSERT INTO tipo_evento VALUES(null,'".$nome."')";
        $res = mysqli_query($conn, $sql);

        if($res){
          $id = mysqli_insert_id($conn);
          $b["res"] = 1;
          $b["id"] = $id;
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "org" ) {
      if ( isset($_POST["id"]) ){
        $id = $_POST["id"];
        $sql = "SELECT usuario.idusuario, perfil.ft_perfil, usuario.nome FROM seguidor INNER JOIN usuario ON usuario.idusuario = seguidor.idseguido INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                WHERE seguidor.idseguindo = ".$id." AND seguidor.idseguido != ".$id." ORDER BY nome ASC";
        $res = mysqli_query($conn, $sql);

        if(mysqli_num_rows($res) > 0){
          $b["res"] = 1;
          while ($row =  mysqli_fetch_array($res)){
            $b["id"] = $row["idusuario"];
            $b["foto"] = $row["ft_perfil"];
            $b["nome"] = $row["nome"];
            array_push($a,$b);
          }
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "cad" ) {
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $nome = $_POST["nome"];
        $desc = $_POST["desc"];
        $lat = $_POST["lat"];
        $lng = $_POST["lng"];
        $dtini = $_POST["dtini"];
        $hrini = $_POST["hrini"];
        $dtfim = $_POST["dtfim"];
        $hrfim = $_POST["hrfim"];
        $fini = $_POST["de"];
        $ffim = $_POST["ate"];
        $coins = $_POST["coins"];
        $img = $_POST["img"];
        $priv = $_POST["priv"];
        $pub = $_POST["pub"];
        if ( isset($_POST["tags"]) ){
          $tags = $_POST["tags"];
        } else {
          $tags = [];
        }
        if ( isset($_POST["orgs"]) ){
          $orgs = $_POST["orgs"];
        } else {
          $orgs = [];
        }

        if ( $img != null ){
          date_default_timezone_set('America/Sao_Paulo');
          $date = date('YmdHis', time());
          $base64img = str_replace('data:image/jpeg;base64,', '', $img);
          $data = base64_decode($base64img);
          $file = UPLOAD_DIR.'IMG_'.$date.'_'.str_replace(' ','_',$nome).'.png';
          file_put_contents('../'.$file, $data);
        }

        $sql = "INSERT INTO evento VALUES(
          null,
          ".$idusuario.",
          '".$nome."',
          ".($desc==null?"null":"'".$desc."'").",
          ".$lat.",
          ".$lng.",
          '".$dtini." ".$hrini."',
          ".($dtfim==null?"null":"'".$dtfim." ".$hrfim."'").",
          ".($fini==null?"null,null":$fini.",".$ffim).",
          ".$coins.",
          ".($img==null?"'http://usevou.com/events/padrao.png'":"'http://usevou.com/".$file."'").",
          ".($priv==0||$priv==1?"false":"true").", NOW(), ".($pub=='on'?"true":"false").")";
        $res = mysqli_query($conn, $sql);

        if($res){
          $id = mysqli_insert_id($conn);
          $ok = 0;
          for($i=0;$i<count($tags);$i++){
            $sql_tag = "INSERT INTO tag_evento VALUES(".$id.",".$tags[$i].")";
            $res_tag = mysqli_query($conn, $sql_tag);
            $ok++;
          }
          if ( $ok == count($tags) ){
            $ok = 0;
            for($i=0;$i<count($orgs);$i++){
              $sql_org = "INSERT INTO organizador VALUES(null,".$id.",".$orgs[$i].",NOW())";
              $res_org = mysqli_query($conn, $sql_org);
              $ok++;
              $sql = "INSERT INTO notificacao VALUES(null, ".$orgs[$i].", ".$idusuario.", null, ".$id.", 6, NOW())";
              $res = mysqli_query($conn, $sql);
            }
            if ( $ok == count($orgs) ){
              $b["res"] = 1;
              $b["id"] = $id;
              array_push($a,$b);
            } else {
              $b["res"] = 0;
              array_push($a,$b);
            }
          } else {
            $b["res"] = 0;
            array_push($a,$b);
          }
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "update" ) {
      if ( isset($_POST["idusuario"]) ){
        $ideve = $_POST["ideve"];
        $idusuario = $_POST["idusuario"];
        $nome = $_POST["nome"];
        $desc = $_POST["desc"];
        $lat = $_POST["lat"];
        $lng = $_POST["lng"];
        $dtini = $_POST["dtini"];
        $hrini = $_POST["hrini"];
        $dtfim = $_POST["dtfim"];
        $hrfim = $_POST["hrfim"];
        $fini = $_POST["de"];
        $ffim = $_POST["ate"];
        $coins = $_POST["coins"];
        $img = $_POST["img"];
        $priv = $_POST["priv"];
        $pub = $_POST["pub"];
        if ( isset($_POST["tags"]) ){
          $tags = $_POST["tags"];
        } else {
          $tags = [];
        }
        if ( isset($_POST["orgs"]) ){
          $orgs = $_POST["orgs"];
        } else {
          $orgs = [];
        }

        if ( strpos($img,'data:image/jpeg;base64,') !== false ){
          date_default_timezone_set('America/Sao_Paulo');
          $date = date('YmdHis', time());
          $base64img = str_replace('data:image/jpeg;base64,', '', $img);
          $data = base64_decode($base64img);
          $file = UPLOAD_DIR.'IMG_'.$date.'_'.str_replace(' ','_',$nome).'.png';
          file_put_contents('../'.$file, $data);
        }

        $sql = "UPDATE evento SET
          idusuario = ".$idusuario.",
          nome = '".$nome."',
          descricao = ".($desc==null?"null":"'".$desc."'").",
          latitude = ".$lat.",
          longitude = ".$lng.",
          dt_hr_ini = '".$dtini." ".$hrini."',
          dt_hr_fim = ".($dtfim==null?"null":"'".$dtfim." ".$hrfim."'").",
          ".($fini==null?"faixa_ini = null, faixa_fim = null":"faixa_ini = ".$fini.", faixa_fim = ".$ffim).",
          coins = ".$coins.",
          img = ".(strpos($img,'data:image/jpeg;base64,') === false?"'".$img."'":"'http://usevou.com/".$file."'").",
          privado = ".($priv==0||$priv==1?"false":"true").", publico = ".($pub=='on'?"true":"false")." WHERE idevento = ".$ideve;
        $res = mysqli_query($conn, $sql);

        $sql_del = "DELETE FROM organizador WHERE idevento = ".$ideve;
        $res_del = mysqli_query($conn, $sql_del);

        $sql_del = "DELETE FROM notificacao WHERE idusuario = ".$idusuario." AND idevento = ".$ideve." AND tipo = 6";
        $res_del = mysqli_query($conn, $sql_del);

        $sql_del = "DELETE FROM tag_evento WHERE idevento = ".$ideve;
        $res_del = mysqli_query($conn, $sql_del);

        if($res){
          $ok = 0;
          for($i=0;$i<count($tags);$i++){
            $sql_tag = "INSERT INTO tag_evento VALUES(".$ideve.",".$tags[$i].")";
            $res_tag = mysqli_query($conn, $sql_tag);
            $ok++;
          }
          if ( $ok == count($tags) ){
            $ok = 0;
            for($i=0;$i<count($orgs);$i++){
              $sql_org = "INSERT INTO organizador VALUES(null,".$ideve.",".$orgs[$i].",NOW())";
              $res_org = mysqli_query($conn, $sql_org);
              $ok++;
              $sql = "INSERT INTO notificacao VALUES(null, ".$orgs[$i].", ".$idusuario.", null, ".$ideve.", 6, NOW())";
              $res = mysqli_query($conn, $sql);
            }
            if ( $ok == count($orgs) ){
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
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "convite" ) {
      if ( isset($_POST["idusu"]) ){
        $idusu = $_POST["idusu"];
        $ideve = $_POST["ideve"];
        $convi = $_POST["convites"];
        $ok = 0;
        $t = 0;

        for ( $i=0;$i<count($convi);$i++ ){
          if ( $convi[$i][1] == 'true' ){
            $t++;
            $sql = "INSERT INTO convite VALUES(null, ".$ideve.",".$idusu.",".$convi[$i][0].",NOW())";
            $res = mysqli_query($conn, $sql);
            if ( $res ){
              $ok++;
            }
          }
        }
        if( $ok == $t ){
          $b["res"] = 1;
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "sel" ) {
      if ( isset($_POST["idusu"]) ){
        $idusu = $_POST["idusu"];
        $ideve = $_POST["ideve"];

        $sql = "SELECT *, DATE_FORMAT(dt_hr_ini,'%d/%m/%Y') as dt_ini, DATE_FORMAT(dt_hr_ini,'%H:%i') as hr_ini, DATE_FORMAT(dt_hr_fim,'%d/%m/%Y') as dt_fim, DATE_FORMAT(dt_hr_fim,'%H:%i') as hr_fim FROM evento WHERE idevento = ".$ideve;
        $res = mysqli_query($conn, $sql);

        if( mysqli_num_rows($res) > 0 ){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["de"] = $row["faixa_ini"];
          $b["ate"] = $row["faixa_fim"];
          $b["img"] = $row["img"];
          $b["post"] = $row["publico"];
          $b["coins"] = $row["coins"];
          $b["privado"] = $row["privado"];
          if ( $idusu == $row["idusuario"] ){
            $b["adm"] = true;
          } else {
            $b["adm"] = false;
          }
          $sql_org = "SELECT * FROM organizador WHERE idusuario = ".$idusu." AND idevento = ".$ideve;
          $res_org = mysqli_query($conn, $sql_org);
          if ( mysqli_num_rows($res_org) > 0 ){
            $b["org"] = true;
          } else {
            $b["org"] = false;
          }
          $sql_pre = "SELECT COUNT(*) as c FROM presenca
                      INNER JOIN usuario ON presenca.idusuario = usuario.idusuario
                      INNER JOIN seguidor ON usuario.idusuario = seguidor.idseguido
                      WHERE seguidor.idseguindo = ".$idusu." AND seguidor.idseguido != ".$idusu." AND presenca.idevento = ".$ideve;
          $res_pre = mysqli_query($conn, $sql_pre);
          $b["pre"] = mysqli_fetch_array($res_pre)["c"];

          $sql_conv = "SELECT COUNT(*) as c FROM presenca WHERE tipo = 0 AND idevento = ".$ideve;
          $res_conv = mysqli_query($conn, $sql_conv);
          $b["conv"] = mysqli_fetch_array($res_conv)["c"];

          $sql_conf = "SELECT COUNT(*) as c FROM presenca WHERE tipo = 1 AND idevento = ".$ideve;
          $res_conf = mysqli_query($conn, $sql_conf);
          $b["conf"] = mysqli_fetch_array($res_conf)["c"];

          $sql_check = "SELECT COUNT(*) as c FROM presenca WHERE tipo = 3 AND idevento = ".$ideve;
          $res_check = mysqli_query($conn, $sql_check);
          $b["check"] = mysqli_fetch_array($res_check)["c"];

          $sql_conf1 = "SELECT tipo FROM presenca WHERE idusuario = ".$idusu." AND idevento = ".$ideve;
          $res_conf1 = mysqli_query($conn, $sql_conf1);
          $b["conf_usu"] = mysqli_fetch_array($res_conf1)["tipo"];

          $sql_org = "SELECT idusuario FROM organizador
                      WHERE idevento = ".$ideve;
          $res_org = mysqli_query($conn, $sql_org);

          if ( mysqli_num_rows($res_org) > 0 ){
            $o = '';
            while($row = mysqli_fetch_array($res_org)){
              $o .= $row["idusuario"].',';
            }
            $o = substr($o, 0, strlen($o)-1);
            $b["o"] = $o;
          } else {
            $b["o"] = 0;
          }

          $sql_tag = "SELECT tipo_evento.idtipo_evento FROM tipo_evento
                      INNER JOIN tag_evento ON tipo_evento.idtipo_evento = tag_evento.idtipo_evento
                      INNER JOIN evento ON tag_evento.idevento = evento.idevento
                      WHERE evento.idevento = ".$ideve;
          $res_tag = mysqli_query($conn, $sql_tag);

          if ( mysqli_num_rows($res_tag) > 0 ){
            $t = '';
            while($row = mysqli_fetch_array($res_tag)){
              $t .= $row["idtipo_evento"].',';
            }
            $t = substr($t, 0, strlen($t)-1);
            $b["t"] = $t;
          } else {
            $b["t"] = 0;
          }
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "about" ) {
      if ( isset($_POST["ideve"]) ){
        $ideve = $_POST["ideve"];

        $sql = "SELECT evento.descricao, evento.latitude, evento.longitude, usuario.idusuario, usuario.nome, perfil.ft_perfil
                FROM evento INNER JOIN usuario ON evento.idusuario = usuario.idusuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario
                WHERE evento.idevento = ".$ideve;
        $res = mysqli_query($conn, $sql);

        if( $res ){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["idusu"] = $row["idusuario"];
          $b["nome"] = $row["nome"];
          $b["foto"] = $row["ft_perfil"];

          $sql_org = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM perfil
                      INNER JOIN usuario ON perfil.idusuario = usuario.idusuario
                      INNER JOIN organizador ON usuario.idusuario = organizador.idusuario
                      WHERE organizador.idevento = ".$ideve;
          $res_org = mysqli_query($conn, $sql_org);

          if ( mysqli_num_rows($res_org) > 0 ){
            $b["org"] = mysqli_num_rows($res_org);
            $i = 0;
            while($row = mysqli_fetch_array($res_org)){
              $b["o"][$i]["idusu"] = $row["idusuario"];
              $b["o"][$i]["nome"] = $row["nome"];
              $b["o"][$i]["foto"] = $row["ft_perfil"];
              $i++;
            }
          } else {
            $b["org"] = 0;
          }

          $sql_tag = "SELECT tipo_evento.nome FROM tipo_evento
                      INNER JOIN tag_evento ON tipo_evento.idtipo_evento = tag_evento.idtipo_evento
                      INNER JOIN evento ON tag_evento.idevento = evento.idevento
                      WHERE evento.idevento = ".$ideve;
          $res_tag = mysqli_query($conn, $sql_tag);

          if ( mysqli_num_rows($res_tag) > 0 ){
            $b["tag"] = mysqli_num_rows($res_tag);
            $i = 0;
            while($row = mysqli_fetch_array($res_tag)){
              $b["t"][$i]["nome"] = $row["nome"];
              $i++;
            }
          } else {
            $b["tag"] = 0;
          }

          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "conf" ) {
      if ( isset($_POST["idusu"]) ){
        $idusu = $_POST["idusu"];
        $ideve = $_POST["ideve"];
        $tipo = $_POST["tipo"];

        $sql_s = "SELECT * FROM presenca WHERE idusuario = ".$idusu." AND idevento = ".$ideve;
        $res_s = mysqli_query($conn, $sql_s);

        if ( mysqli_num_rows($res_s) > 0 ){
          $sql = "UPDATE presenca SET tipo = ".$tipo." WHERE idusuario = ".$idusu." AND idevento = ".$ideve;
          $res = mysqli_query($conn, $sql);
        } else {
          $sql = "INSERT INTO presenca VALUES(null, ".$ideve.",".$idusu.",".$tipo.",NOW())";
          $res = mysqli_query($conn, $sql);
        }

        if( $res ){
          $b["res"] = 1;
          if ( $tipo == 1 ){
            $sql = "SELECT idusuario FROM evento WHERE idevento = ".$ideve;
            $res = mysqli_query($conn, $sql);
            $id = mysqli_fetch_array($res)['idusuario'];

            $sql = "INSERT INTO notificacao VALUES(null, ".$id.", ".$idusu.", null, ".$ideve.", 4, NOW())";
            $res = mysqli_query($conn, $sql);
          } else if ( $tipo == 2 || $tipo == 0 ){
            $sql = "SELECT idusuario FROM evento WHERE idevento = ".$ideve;
            $res = mysqli_query($conn, $sql);
            $id = mysqli_fetch_array($res)['idusuario'];

            $sql = "DELETE FROM notificacao WHERE idnotificado = ".$id." AND idusuario = ".$idusu." AND idevento = ".$ideve." AND tipo = 4";
            $res = mysqli_query($conn, $sql);
          } else if ( $tipo == 3 ){
            $sql = "SELECT nome FROM evento WHERE idevento = ".$ideve;
            $res = mysqli_query($conn, $sql);
            $nome = mysqli_fetch_array($res)['nome'];

            $sql = "SELECT idconta FROM conta WHERE idusuario = ".$idusu;
            $res = mysqli_query($conn, $sql);
            $idconta = mysqli_fetch_array($res)['idconta'];

            $sql = "INSERT INTO postagem VALUES (null, ".$idusu.", null, null,'Fez check-in no evento <b>".$nome."</b>', NOW(), true, false)";
            $res = mysqli_query($conn, $sql);

            $sql = "INSERT INTO movimento_conta VALUES (null, ".$idconta.", 'Check-in realizado no evento <b>".$nome."</b>', 25, 1, NOW(), 1)";
            $res = mysqli_query($conn, $sql);

            $sql = "UPDATE conta SET saldo = saldo + 25 WHERE idconta = ".$idconta;
            $res = mysqli_query($conn, $sql);
          }
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "del" ) {
      if ( isset($_POST["ideve"]) ){
        $ideve = $_POST["ideve"];

        $sql1 = "DELETE FROM tag_evento WHERE idevento = ".$ideve;
        $res1 = mysqli_query($conn, $sql1);

        $sql2 = "DELETE FROM organizador WHERE idevento = ".$ideve;
        $res2 = mysqli_query($conn, $sql2);

        $sql4 = "DELETE FROM presenca WHERE idevento = ".$ideve;
        $res4 = mysqli_query($conn, $sql4);

        $sql10 = "DELETE FROM convite WHERE idevento = ".$ideve;
        $res10 = mysqli_query($conn, $sql10);

        $sql5 = "DELETE FROM notificacao WHERE idevento = ".$ideve;
        $res5 = mysqli_query($conn, $sql5);

        $sql8 = "SELECT * FROM postagem WHERE idevento = ".$ideve;
        $res8 = mysqli_query($conn, $sql8);
        while($row = mysqli_fetch_array($res8)){
          $sql7 = "DELETE FROM curtida WHERE idpostagem = ".$row['idpostagem'];
          $res7 = mysqli_query($conn, $sql7);

          $sql7 = "DELETE FROM midia WHERE idpostagem = ".$row['idpostagem'];
          $res7 = mysqli_query($conn, $sql7);

          $sql7 = "DELETE FROM notificacao WHERE idpostagem = ".$row['idpostagem'];
          $res7 = mysqli_query($conn, $sql7);

          $sql7 = "SELECT * FROM comentario WHERE idpostagem = ".$row['idpostagem'];
          $res7 = mysqli_query($conn, $sql7);

          while($row1 = mysqli_fetch_array($res7)){
            $sql9 = "DELETE FROM curtida WHERE idcomentario = ".$row1['idcomentario'];
            $res9 = mysqli_query($conn, $sql9);
          }

          $sql7 = "DELETE FROM comentario WHERE idpostagem = ".$row['idpostagem'];
          $res7 = mysqli_query($conn, $sql7);
        }

        $sql6 = "DELETE FROM postagem WHERE idevento = ".$ideve;
        $res6 = mysqli_query($conn, $sql6);

        $sql3 = "DELETE FROM evento WHERE idevento = ".$ideve;
        $res3 = mysqli_query($conn, $sql3);

        if( $res1 && $res2 && $res3 && $res4 && $res5 && $res6 && $res10 ){
          $b["res"] = 1;
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "map" ) {
      $sql = "SELECT *, DATE_FORMAT(dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(dt_hr_fim,'%H:%i') as hr_fim
              FROM evento WHERE privado = 0 AND dt_hr_ini >= ADDDATE(NOW(),INTERVAL -1 DAY)";
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "map_tag" ) {
      $tag = $_POST["tag"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN tag_evento ON tag_evento.idevento = evento.idevento WHERE evento.privado = 0 AND evento.dt_hr_ini >= ADDDATE(NOW(),INTERVAL -1 DAY) AND tag_evento.idtipo_evento = ".$tag;
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "map_date" ) {
      $dt_ini = $_POST["dtini"];
      $dt_fim = $_POST["dtfim"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento WHERE evento.privado = 0 AND evento.dt_hr_ini BETWEEN ADDDATE('".$dt_ini."',INTERVAL -1 DAY) AND ADDDATE('".$dt_fim."',INTERVAL 1 DAY)";
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "map_tag_date" ) {
      $tag = $_POST["tag"];
      $dt_ini = $_POST["dtini"];
      $dt_fim = $_POST["dtfim"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN tag_evento ON tag_evento.idevento = evento.idevento WHERE evento.privado = 0 AND tag_evento.idtipo_evento = ".$tag." AND evento.dt_hr_ini BETWEEN ADDDATE('".$dt_ini."',INTERVAL -1 DAY) AND ADDDATE('".$dt_fim."',INTERVAL 1 DAY)";
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "invites" ) {
      $idusu = $_POST["idusu"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim, usuario.nome as nome_usu, evento.nome as nome_eve
              FROM evento INNER JOIN convite ON convite.idevento = evento.idevento INNER JOIN usuario ON usuario.idusuario = convite.idconvidando
              WHERE convite.idconvidado = ".$idusu;
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["ideve"] = $row["idevento"];
          $b["idusu"] = $row["idusuario"];
          $b["nome"] = $row["nome_eve"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];
          $b["nome_usu"] = $row["nome_usu"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if ( $q == "quem_conf" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $idevento = $_POST["idevento"];
        $tipo = $_POST["tipo"];
        $limit = $_POST["limit"];

        if ($tipo == 3 || $tipo == 1){
          $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM presenca
                  INNER JOIN usuario ON usuario.idusuario = presenca.idusuario INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                  WHERE presenca.idevento = ".$idevento." AND presenca.tipo = ".$tipo." ORDER BY presenca.dt_hr DESC LIMIT ".$limit.",20";
        } else {
          $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM convite
                  INNER JOIN usuario ON usuario.idusuario = convite.idconvidado INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                  WHERE convite.idevento = ".$idevento." ORDER BY convite.dt_hr DESC LIMIT ".$limit.",20";
        }
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b['idusuario'] = $row['idusuario'];
            $b['nome'] = $row['nome'];
            $b['foto'] = $row['ft_perfil'];
            $sql1 = "SELECT * FROM seguidor WHERE idseguindo = ".$idusuario." AND idseguido = ".$row["idusuario"];
            $res1 = mysqli_query($conn, $sql1);
            if ( mysqli_num_rows($res1) > 0 ){
              $b["seguindo"] = true;
            } else {
              $b["seguindo"] = false;
            }
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "agenda" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $ano = $_POST["ano"];
        $mes = $_POST["mes"];

        $sql = "SELECT DATE_FORMAT(evento.dt_hr_ini,'%Y-%m-%d') as data FROM presenca INNER JOIN evento ON evento.idevento = presenca.idevento
                WHERE presenca.idusuario = ".$idusuario." AND YEAR(evento.dt_hr_ini) = '".$ano."' AND MONTH(evento.dt_hr_ini) = '".$mes."'";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b['date'] = $row['data'];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "agenda_day" ) {
      $idusu = $_POST["idusu"];
      $dia = $_POST["dia"];
      $mes = $_POST["mes"];
      $ano = $_POST["ano"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN presenca ON presenca.idevento = evento.idevento
              WHERE presenca.idusuario = ".$idusu." AND DAY(evento.dt_hr_ini) = '".$dia."' AND MONTH(evento.dt_hr_ini) = '".$mes."' AND YEAR(evento.dt_hr_ini) = '".$ano."'";
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["ideve"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "my_conf" ) {
      $id = $_POST["id"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN presenca ON presenca.idevento = evento.idevento WHERE (presenca.tipo = 3 OR presenca.tipo = 1) AND presenca.idusuario = ".$id;
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "my_cri" ) {
      $id = $_POST["id"];
      $sql = "SELECT *, DATE_FORMAT(dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(dt_hr_fim,'%H:%i') as hr_fim
              FROM evento WHERE idusuario = ".$id;
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "my_org" ) {
      $id = $_POST["id"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN organizador ON organizador.idevento = evento.idevento WHERE organizador.idusuario = ".$id;
      $res = mysqli_query($conn, $sql);

      if( mysqli_num_rows($res) > 0 ){
        $b["res"] = 1;
        while($row = mysqli_fetch_array($res)){
          $b["id"] = $row["idevento"];
          $b["nome"] = $row["nome"];
          $b["desc"] = $row["descricao"];
          $b["lat"] = $row["latitude"];
          $b["lng"] = $row["longitude"];
          $b["dt_ini"] = $row["dt_ini"];
          $b["hr_ini"] = $row["hr_ini"];
          $b["dt_fim"] = $row["dt_fim"];
          $b["hr_fim"] = $row["hr_fim"];
          $b["img"] = $row["img"];
          $b["coins"] = $row["coins"];

          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    }
  }

  echo json_encode($a);
?>
