<?php
  include_once 'conn.php';

  $a=array();
  $b=array();
  $c=array();
  define('UPLOAD_DIR', 'profile/');
  date_default_timezone_set('America/Sao_Paulo');

  if ( isset($_POST["q"]) ){
    $q = $_POST["q"];
    if ( $q == "sel" ){
      if ( isset($_POST["idusu"]) ){
        $id = $_POST["idusu"];
        $idusu = $_POST["idusuario"];

        $sql = "SELECT *, TIMESTAMPDIFF(YEAR, dt_nasc, NOW()) AS idade, DATE_FORMAT(dt_nasc,'%d/%m/%Y') AS dt FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario WHERE usuario.idusuario = ".$id;
        $res = mysqli_query($conn, $sql);

        if(mysqli_num_rows($res) > 0){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["dt_nasc"] = $row["dt"];
          $b["nome"] = $row["nome"];
          $b["perfil"] = $row["ft_perfil"];
          $b["capa"] = $row["ft_capa"];
          $b["idade"] = $row["idade"];
          $sql1 = "SELECT cidade.nome, estado.sigla FROM perfil
          INNER JOIN cidade ON perfil.idcidade = cidade.idcidade
          INNER JOIN estado ON cidade.idestado = estado.idestado
          WHERE perfil.idusuario = ".$id;
          $res1 = mysqli_query($conn, $sql1);
          if (mysqli_num_rows($res1) > 0){
            $row1 =  mysqli_fetch_array($res1);
            $b["cidade"] = $row1["nome"]." (".$row1["sigla"].")";
          } else {
            $b["cidade"] = null;
          }
          $sql2 = "SELECT COUNT(idseguindo) as c FROM seguidor WHERE idseguindo = ".$id." AND idseguido != ".$id;
          $res2 = mysqli_query($conn, $sql2);
          $row2 =  mysqli_fetch_array($res2);
          $sql3 = "SELECT COUNT(idseguido) as c FROM seguidor WHERE idseguido = ".$id." AND idseguindo != ".$id;
          $res3 = mysqli_query($conn, $sql3);
          $row3 =  mysqli_fetch_array($res3);
          if ( $id != $idusu ){
            $sql4 = "SELECT COUNT(idseguido) as c FROM seguidor WHERE idseguido = ".$id." AND idseguindo = ".$idusu;
            $res4 = mysqli_query($conn, $sql4);
            $row4 =  mysqli_fetch_array($res4);
            if ( $row4['c'] > 0 ){
              $b["seg"] = true;
            } else {
              $b["seg"] = false;
            }
          } else {
            $b["seg"] = false;
          }
          $b["seguindo"] = $row2["c"];
          $b["seguidores"] = $row3["c"];
        }else{
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } else if( $q == "about" ) {
      if ( isset($_POST["id"]) ){
        $id = $_POST["id"];

        $sql = "SELECT *, DATE_FORMAT(dt_nasc,'%d/%m') AS dt FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idusuario WHERE usuario.idusuario = ".$id;
        $res = mysqli_query($conn, $sql);

        if(mysqli_num_rows($res) > 0){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["data"] = $row["dt"];
          $b["descricao"] = $row["descricao"];
          $b["genero"] = $row["genero"];
          $sql1 = "SELECT * FROM perfil INNER JOIN relacionamento ON perfil.idrelacionamento = relacionamento.idrelacionamento WHERE perfil.idusuario = ".$id;
          $res1 = mysqli_query($conn, $sql1);
          if ( mysqli_num_rows($res1) > 0 ){
            $row1 =  mysqli_fetch_array($res1);
            $b["idrelacionamento"] = $row1["idrelacionamento"];
            $b["relacionamento"] = $row1["nome"];
          } else {
            $b["idrelacionamento"] = null;
            $b["relacionamento"] = null;
          }
          $sql_tag = "SELECT tipo_evento.idtipo_evento, tipo_evento.nome FROM tipo_evento
                      INNER JOIN interesse ON tipo_evento.idtipo_evento = interesse.idtipo_evento
                      INNER JOIN usuario ON interesse.idusuario = usuario.idusuario
                      WHERE usuario.idusuario = ".$id;
          $res_tag = mysqli_query($conn, $sql_tag);

          if ( mysqli_num_rows($res_tag) > 0 ){
            $t = '';
            $n = '';
            while($row = mysqli_fetch_array($res_tag)){
              $t .= $row["idtipo_evento"].',';
              $n .= $row["nome"].',';
            }
            $t = substr($t, 0, strlen($t)-1);
            $n = substr($n, 0, strlen($n)-1);
            $b["t"] = $t;
            $b["nt"] = $n;
          } else {
            $b["t"] = 0;
          }
        }else{
          $b["res"] = 0;
        }

        array_push($a,$b);
      }
    } else if( $q == "rel" ) {
      $sql = "SELECT * FROM relacionamento";
      $res = mysqli_query($conn, $sql);

      if(mysqli_num_rows($res) > 0){
        $b["res"] = 1;
        while ($row =  mysqli_fetch_array($res)){
          $b["id"] = $row["idrelacionamento"];
          $b["nome"] = $row["nome"];
          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "city" ) {
      $sql = "SELECT cidade.idcidade,cidade.nome,estado.sigla FROM cidade INNER JOIN estado ON cidade.idestado = estado.idestado ORDER BY cidade.nome ASC";
      $res = mysqli_query($conn, $sql);

      if(mysqli_num_rows($res) > 0){
        $b["res"] = 1;
        while ($row =  mysqli_fetch_array($res)){
          $b["id"] = $row["idcidade"];
          $b["nome"] = $row["nome"]." (".$row["sigla"].")";
          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "social" ) {
      $sql = "SELECT * FROM rede_social";
      $res = mysqli_query($conn, $sql);

      if(mysqli_num_rows($res) > 0){
        $b["res"] = 1;
        while ($row =  mysqli_fetch_array($res)){
          $b["id"] = $row["idrede_social"];
          $b["img"] = $row["img"];
          $b["nome"] = $row["nome"];
          array_push($a,$b);
        }
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "update" ) {
      $id = $_POST["id"];
      $nome = $_POST["nome"];
      $date = $_POST["date"];
      $cidade = $_POST["cidade"];
      $rel = $_POST["rel"];
      $gen = $_POST["genero"];
      $int = $_POST["interesse"];
      $desc = $_POST["desc"];
      if ( isset($_POST["tags"]) ){
        $tags = $_POST["tags"];
      } else {
        $tags = [];
      }
      if ( $cidade != null ){
        $estado = substr($cidade,strlen($cidade)-3,2);
        $cidade = substr($cidade,0,strlen($cidade)-5);
        $sql1 = "SELECT cidade.idcidade FROM cidade INNER JOIN estado ON cidade.idestado = estado.idestado WHERE cidade.nome = '".$cidade."' AND estado.sigla = '".$estado."'";
        $res1 = mysqli_query($conn, $sql1);
        if ( mysqli_num_rows($res1) > 0 ){
          $row = mysqli_fetch_array($res1);
          $c = $row["idcidade"];
        } else {
          $c = null;
        }
      }
      $sql = "UPDATE usuario INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
              SET usuario.nome = '".$nome."', perfil.dt_nasc = '".$date."', perfil.idcidade = ".($cidade==null?"null":($c==null?"null":$c)).",
              perfil.descricao = ".($desc==null?"null":"'".$desc."'").", perfil.idrelacionamento = ".($rel==null?"null":$rel).",
              perfil.genero = ".($gen==null?"null":$gen)." WHERE usuario.idusuario = ".$id;

      $res = mysqli_query($conn, $sql);

      $sql_del = "DELETE FROM interesse WHERE idusuario = ".$id;
      $res_del = mysqli_query($conn, $sql_del);

      if($res){
        $ok = 0;
        for($i=0;$i<count($tags);$i++){
          $sql_tag = "INSERT INTO interesse VALUES(".$id.",".$tags[$i].")";
          $res_tag = mysqli_query($conn, $sql_tag);
          $ok++;
        }
        if ( $ok == count($tags) ){
          $b["res"] = 1;
        }
        array_push($a,$b);
      }else{
        $b["res"] = 0;
        array_push($a,$b);
      }
    } else if( $q == "perfil" ) {
      if ( $_POST["login"] ){
        $login = $_POST["login"];
        $perfil = $_POST["foto"];

        $base64img = str_replace('data:image/jpeg;base64,', '', $perfil);
        $date = date('YmdHis', time());
        $data = base64_decode($base64img);
        $file = UPLOAD_DIR.'ft_perfil/IMG_'.$date.'.png';
        file_put_contents('../'.$file, $data);

        $sql = "UPDATE perfil INNER JOIN usuario ON perfil.idusuario = usuario.idusuario SET perfil.ft_perfil = 'http://usevou.com/".$file."' WHERE usuario.login = '".$login."'";
        $res = mysqli_query($conn, $sql);

        if($res){
          $b["res"] = 1;
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "capa" ) {
      if ( $_POST["login"] ){
        $login = $_POST["login"];
        $capa = $_POST["foto"];

        $base64img = str_replace('data:image/jpeg;base64,', '', $capa);
        $date = date('YmdHis', time());
        $data = base64_decode($base64img);
        $file = UPLOAD_DIR.'ft_capa/IMG_'.$date.'.png';
        file_put_contents('../'.$file, $data);

        $sql = "UPDATE perfil INNER JOIN usuario ON perfil.idusuario = usuario.idusuario SET perfil.ft_capa = 'http://usevou.com/".$file."' WHERE usuario.login = '".$login."'";
        $res = mysqli_query($conn, $sql);

        if($res){
          $b["res"] = 1;
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "seg" ) {
      if ( $_POST["idusu"] ){
        $idusu = $_POST["idusu"];
        $idusuario = $_POST["idusuario"];

        $sql = "INSERT INTO seguidor VALUES(null, ".$idusuario.", ".$idusu.", 1, NOW())";
        $res = mysqli_query($conn, $sql);

        if($res){
          $b["res"] = 1;
          $sql = "INSERT INTO notificacao VALUES(null, ".$idusu.", ".$idusuario.", null, null, 5, NOW())";
          $res = mysqli_query($conn, $sql);
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "deseg" ) {
      if ( $_POST["idusu"] ){
        $idusu = $_POST["idusu"];
        $idusuario = $_POST["idusuario"];

        $sql = "DELETE FROM seguidor WHERE idseguindo = ".$idusuario." AND idseguido = ".$idusu;
        $res = mysqli_query($conn, $sql);

        if($res){
          $b["res"] = 1;
          $sql = "DELETE FROM notificacao WHERE idnotificado =  ".$idusu." AND idusuario = ".$idusuario." AND tipo = 5";
          $res = mysqli_query($conn, $sql);
          array_push($a,$b);
        }else{
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "quem_seguindo" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $limit = $_POST["limit"];

        $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM seguidor
                INNER JOIN usuario ON usuario.idusuario = seguidor.idseguido INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                WHERE seguidor.idseguindo = ".$idusuario." AND seguidor.idseguido != ".$idusuario." ORDER BY seguidor.dt_hr DESC LIMIT ".$limit.",20";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b['idusuario'] = $row['idusuario'];
            $b['nome'] = $row['nome'];
            $b['foto'] = $row['ft_perfil'];
            $b["seguindo"] = true;
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "quem_seguidores" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $limit = $_POST["limit"];

        $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM seguidor
                INNER JOIN usuario ON usuario.idusuario = seguidor.idseguindo INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                WHERE seguidor.idseguido = ".$idusuario." AND seguidor.idseguindo != ".$idusuario." ORDER BY seguidor.dt_hr DESC LIMIT ".$limit.",20";
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
    } else if ( $q == "notify" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $limit = $_POST["limit"];

        $sql = "SELECT *, DATE_FORMAT(dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),dt_hr) as diff FROM notificacao
                WHERE idnotificado = ".$idusuario." AND idusuario != ".$idusuario." ORDER BY dt_hr DESC LIMIT ".$limit.",20";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b['idusu'] = $row['idusuario'];
            $b['idpost'] = $row['idpostagem'];
            $b['ideve'] = $row['idevento'];
            $b['tipo'] = $row['tipo'];
            $b['dt_hr'] = $row['dt_hr'];
            $b['diff'] = $row['diff'];

            $sql1 = 'SELECT usuario.nome, perfil.ft_perfil FROM usuario INNER JOIN perfil ON perfil.idusuario = usuario.idusuario WHERE usuario.idusuario = '.$row['idusuario'];
            $res1 = mysqli_query($conn,$sql1);
            $r = mysqli_fetch_array($res1);
            $b['nome'] = $r['nome'];
            $b['foto'] = $r['ft_perfil'];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "gallery" ){
      if ( isset($_POST["id"]) ){
        $id = $_POST["id"];
        $limit = $_POST["limit"];

        $sql = "SELECT postagem.idpostagem,midia.url FROM midia
                INNER JOIN postagem ON postagem.idpostagem = midia.idpostagem
                WHERE postagem.idusuario = ".$id." LIMIT ".$limit.",12";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b['id'] = $row['idpostagem'];
            $b['url'] = $row['url'];
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if( $q == "my_conf_prox" ) {
      $id = $_POST["id"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN presenca ON presenca.idevento = evento.idevento WHERE (presenca.tipo = 3 OR presenca.tipo = 1) AND evento.dt_hr_ini >= NOW() AND presenca.idusuario = ".$id;
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
    } else if( $q == "my_conf_ant" ) {
      $id = $_POST["id"];
      $sql = "SELECT *, DATE_FORMAT(evento.dt_hr_ini,'%d/%m/%Y') as dt_ini,
                        DATE_FORMAT(evento.dt_hr_ini,'%H:%i') as hr_ini,
                        DATE_FORMAT(evento.dt_hr_fim,'%d/%m/%Y') as dt_fim,
                        DATE_FORMAT(evento.dt_hr_fim,'%H:%i') as hr_fim
              FROM evento INNER JOIN presenca ON presenca.idevento = evento.idevento WHERE (presenca.tipo = 3 OR presenca.tipo = 1) AND evento.dt_hr_ini < NOW() AND presenca.idusuario = ".$id;
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
