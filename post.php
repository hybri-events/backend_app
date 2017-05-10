<?php
  include_once 'conn.php';

  $a=array();
  $b=array();
  define('UPLOAD_DIR', 'posts/');

  if ( isset($_POST["q"]) ){
    $q = $_POST["q"];
    if ( $q == "cad" ){
      if ( isset($_POST["post"]) ){
        $post = $_POST["post"];
        $img = $_POST["img"];
        $idusuario = $_POST["idusuario"];
        $idevento = $_POST["idevento"];
        $evento = $_POST["evento"];

        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i:s', time());

        $sql = "INSERT INTO postagem VALUES (null, ".($idusuario==null?'null':$idusuario).", ".($idevento==null?'null':$idevento).", null,'".$post."', '".$date."',true,".$evento.")";
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $id = mysqli_insert_id($conn);
          $ok = 0;
          if ( $img != 'null' ){
            for ( $i=0;$i<count($img);$i++ ){
              $date = date('YmdHis', time());
              $base64img = str_replace('data:image/jpeg;base64,', '', $img[$i]);
              $data = base64_decode($base64img);
              $file = UPLOAD_DIR.'IMG_'.$date.'_'.$i.'.png';
              file_put_contents('../'.$file, $data);
              $sql = "INSERT INTO midia VALUES (null, 1, ".$id.",".($idevento==null?'null':$idevento).",'http://usevou.com/".$file."')";
              $res = mysqli_query($conn, $sql);
              $ok++;
            }

            if ( $ok == count($img) ){
              $b["res"] = 1;
              array_push($a,$b);
            } else {
              $b["res"] = 0;
              array_push($a,$b);
            }
          } else {
            $b["res"] = 1;
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "sel_home" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $limit = $_POST["limit"];
        $sql = "SELECT postagem.idpostagem, postagem.idcompartilhado, usuario.idusuario, perfil.ft_perfil, usuario.nome, DATE_FORMAT(postagem.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),postagem.dt_hr) as diff, postagem.conteudo
                FROM seguidor INNER JOIN usuario ON usuario.idusuario = seguidor.idseguido INNER JOIN perfil ON usuario.idusuario = perfil.idperfil
                INNER JOIN postagem ON postagem.idusuario = usuario.idusuario
                WHERE seguidor.idseguindo = ".$idusuario." AND postagem.idevento IS NULL ORDER BY postagem.dt_hr DESC LIMIT ".$limit.", 10";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          while($row = mysqli_fetch_array($res)){
            $b["res"] = 1;
            $b["idpost"] = $row["idpostagem"];
            $b["idusu"] = $row["idusuario"];
            $b["ft_perfil"] = $row["ft_perfil"];
            $b["nome"] = $row["nome"];
            $b["dt_hr"] = $row["dt_hr"];
            $b["diff"] = $row["diff"];
            $b["conteudo"] = $row["conteudo"];
            $b["idcompartilhado"] = $row["idcompartilhado"];

            if ( $row['idcompartilhado'] == null ){
              $b["compartilhado"] = false;
              $sql_midia = "SELECT url FROM midia WHERE idpostagem = ".$row["idpostagem"];
              $res_midia = mysqli_query($conn,$sql_midia);
              if ( mysqli_num_rows($res_midia) > 0 ){
                $b['midia'] = mysqli_num_rows($res_midia);
                $i = 0;
                while($row_midia = mysqli_fetch_array($res_midia)){
                  $b['url'.$i] = $row_midia['url'];
                  $i++;
                }
              } else {
                $b["midia"] = 0;
              }

              $sql_cur = "SELECT COUNT(idcurtida) as curtida FROM curtida WHERE idpostagem = ".$row["idpostagem"];
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                while($row_cur = mysqli_fetch_array($res_cur)){
                  $b['curtidas'] = $row_cur["curtida"];
                }
              } else {
                $b["curtidas"] = 0;
              }

              $sql_cur = "SELECT idcurtida FROM curtida WHERE idpostagem = ".$row["idpostagem"]." AND idusuario = ".$idusuario;
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                $b['curtido'] = true;
              } else {
                $b["curtido"] = false;
              }

              $sql_com = "SELECT COUNT(idcomentario) as comentario FROM comentario WHERE idpostagem = ".$row["idpostagem"];
              $res_com = mysqli_query($conn,$sql_com);
              if ( mysqli_num_rows($res_com) > 0 ){
                while($row_com = mysqli_fetch_array($res_com)){
                  $b['comentarios'] = $row_com["comentario"];
                }
              } else {
                $b["comentarios"] = 0;
              }

              $sql_sh = "SELECT COUNT(idcompartilhado) as compartilhar FROM postagem WHERE idcompartilhado = ".($row['idcompartilhado']==null?$row["idpostagem"]:$row['idcompartilhado']);
              $res_sh = mysqli_query($conn,$sql_sh);
              if ( mysqli_num_rows($res_sh) > 0 ){
                while($row_sh = mysqli_fetch_array($res_sh)){
                  $b['compartilhar'] = $row_sh["compartilhar"];
                }
              } else {
                $b["compartilhar"] = 0;
              }
            } else {
              $b["compartilhado"] = true;
              $sql_post = "SELECT postagem.idpostagem, postagem.idcompartilhado, usuario.idusuario, perfil.ft_perfil, usuario.nome, DATE_FORMAT(postagem.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),postagem.dt_hr) as diff, postagem.conteudo
                      FROM seguidor INNER JOIN usuario ON usuario.idusuario = seguidor.idseguido INNER JOIN perfil ON usuario.idusuario = perfil.idperfil
                      INNER JOIN postagem ON postagem.idusuario = usuario.idusuario WHERE idpostagem = ".$row["idcompartilhado"];
              $res_post = mysqli_query($conn,$sql_post);
              $row1 = mysqli_fetch_array($res_post);
              $b["ft_perfil_comp"] = $row1["ft_perfil"];
              $b["idusu_comp"] = $row1["idusuario"];
              $b["nome_comp"] = $row1["nome"];
              $b["dt_hr_comp"] = $row1["dt_hr"];
              $b["diff_comp"] = $row1["diff"];
              $b["conteudo_comp"] = $row1["conteudo"];

              $sql_midia = "SELECT url FROM midia WHERE idpostagem = ".$row["idcompartilhado"];
              $res_midia = mysqli_query($conn,$sql_midia);
              if ( mysqli_num_rows($res_midia) > 0 ){
                $b['midia'] = mysqli_num_rows($res_midia);
                $i = 0;
                while($row_midia = mysqli_fetch_array($res_midia)){
                  $b['url'.$i] = $row_midia['url'];
                  $i++;
                }
              } else {
                $b["midia"] = 0;
              }

              $sql_cur = "SELECT COUNT(idcurtida) as curtida FROM curtida WHERE idpostagem = ".$row["idcompartilhado"];
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                while($row_cur = mysqli_fetch_array($res_cur)){
                  $b['curtidas'] = $row_cur["curtida"];
                }
              } else {
                $b["curtidas"] = 0;
              }

              $sql_cur = "SELECT idcurtida FROM curtida WHERE idpostagem = ".$row["idcompartilhado"]." AND idusuario = ".$idusuario;
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                $b['curtido'] = true;
              } else {
                $b["curtido"] = false;
              }

              $sql_com = "SELECT COUNT(idcomentario) as comentario FROM comentario WHERE idpostagem = ".$row["idcompartilhado"];
              $res_com = mysqli_query($conn,$sql_com);
              if ( mysqli_num_rows($res_com) > 0 ){
                while($row_com = mysqli_fetch_array($res_com)){
                  $b['comentarios'] = $row_com["comentario"];
                }
              } else {
                $b["comentarios"] = 0;
              }

              $sql_sh = "SELECT COUNT(idcompartilhado) as compartilhar FROM postagem WHERE idcompartilhado = ".$row['idcompartilhado'];
              $res_sh = mysqli_query($conn,$sql_sh);
              if ( mysqli_num_rows($res_sh) > 0 ){
                while($row_sh = mysqli_fetch_array($res_sh)){
                  $b['compartilhar'] = $row_sh["compartilhar"];
                }
              } else {
                $b["compartilhar"] = 0;
              }
            }

            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "sel_profile" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $limit = $_POST["limit"];
        $sql = "SELECT postagem.idpostagem, postagem.idcompartilhado, usuario.idusuario, perfil.ft_perfil, usuario.nome, DATE_FORMAT(postagem.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),postagem.dt_hr) as diff, postagem.conteudo
                FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idperfil INNER JOIN postagem ON postagem.idusuario = usuario.idusuario
                WHERE postagem.idusuario = ".$idusuario." ORDER BY postagem.dt_hr DESC LIMIT ".$limit.", 10";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          while($row = mysqli_fetch_array($res)){
            $b["res"] = 1;
            $b["idpost"] = $row["idpostagem"];
            $b["idusu"] = $row["idusuario"];
            $b["ft_perfil"] = $row["ft_perfil"];
            $b["nome"] = $row["nome"];
            $b["dt_hr"] = $row["dt_hr"];
            $b["diff"] = $row["diff"];
            $b["conteudo"] = $row["conteudo"];
            $b["idcompartilhado"] = $row["idcompartilhado"];

            if ( $row['idcompartilhado'] == null ){
              $b["compartilhado"] = false;
              $sql_midia = "SELECT url FROM midia WHERE idpostagem = ".$row["idpostagem"];
              $res_midia = mysqli_query($conn,$sql_midia);
              if ( mysqli_num_rows($res_midia) > 0 ){
                $b['midia'] = mysqli_num_rows($res_midia);
                $i = 0;
                while($row_midia = mysqli_fetch_array($res_midia)){
                  $b['url'.$i] = $row_midia['url'];
                  $i++;
                }
              } else {
                $b["midia"] = 0;
              }

              $sql_cur = "SELECT COUNT(idcurtida) as curtida FROM curtida WHERE idpostagem = ".$row["idpostagem"];
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                while($row_cur = mysqli_fetch_array($res_cur)){
                  $b['curtidas'] = $row_cur["curtida"];
                }
              } else {
                $b["curtidas"] = 0;
              }

              $sql_cur = "SELECT idcurtida FROM curtida WHERE idpostagem = ".$row["idpostagem"]." AND idusuario = ".$idusuario;
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                $b['curtido'] = true;
              } else {
                $b["curtido"] = false;
              }

              $sql_com = "SELECT COUNT(idcomentario) as comentario FROM comentario WHERE idpostagem = ".$row["idpostagem"];
              $res_com = mysqli_query($conn,$sql_com);
              if ( mysqli_num_rows($res_com) > 0 ){
                while($row_com = mysqli_fetch_array($res_com)){
                  $b['comentarios'] = $row_com["comentario"];
                }
              } else {
                $b["comentarios"] = 0;
              }

              $sql_sh = "SELECT COUNT(idcompartilhado) as compartilhar FROM postagem WHERE idcompartilhado = ".($row['idcompartilhado']==null?$row["idpostagem"]:$row['idcompartilhado']);
              $res_sh = mysqli_query($conn,$sql_sh);
              if ( mysqli_num_rows($res_sh) > 0 ){
                while($row_sh = mysqli_fetch_array($res_sh)){
                  $b['compartilhar'] = $row_sh["compartilhar"];
                }
              } else {
                $b["compartilhar"] = 0;
              }
            } else {
              $b["compartilhado"] = true;
              $sql_post = "SELECT postagem.idpostagem, postagem.idcompartilhado, usuario.idusuario, perfil.ft_perfil, usuario.nome, DATE_FORMAT(postagem.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),postagem.dt_hr) as diff, postagem.conteudo
                      FROM seguidor INNER JOIN usuario ON usuario.idusuario = seguidor.idseguido INNER JOIN perfil ON usuario.idusuario = perfil.idperfil
                      INNER JOIN postagem ON postagem.idusuario = usuario.idusuario WHERE idpostagem = ".$row["idcompartilhado"];
              $res_post = mysqli_query($conn,$sql_post);
              $row1 = mysqli_fetch_array($res_post);
              $b["ft_perfil_comp"] = $row1["ft_perfil"];
              $b["idusu_comp"] = $row1["idusuario"];
              $b["nome_comp"] = $row1["nome"];
              $b["dt_hr_comp"] = $row1["dt_hr"];
              $b["diff_comp"] = $row1["diff"];
              $b["conteudo_comp"] = $row1["conteudo"];

              $sql_midia = "SELECT url FROM midia WHERE idpostagem = ".$row["idcompartilhado"];
              $res_midia = mysqli_query($conn,$sql_midia);
              if ( mysqli_num_rows($res_midia) > 0 ){
                $b['midia'] = mysqli_num_rows($res_midia);
                $i = 0;
                while($row_midia = mysqli_fetch_array($res_midia)){
                  $b['url'.$i] = $row_midia['url'];
                  $i++;
                }
              } else {
                $b["midia"] = 0;
              }

              $sql_cur = "SELECT COUNT(idcurtida) as curtida FROM curtida WHERE idpostagem = ".$row["idcompartilhado"];
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                while($row_cur = mysqli_fetch_array($res_cur)){
                  $b['curtidas'] = $row_cur["curtida"];
                }
              } else {
                $b["curtidas"] = 0;
              }

              $sql_cur = "SELECT idcurtida FROM curtida WHERE idpostagem = ".$row["idcompartilhado"]." AND idusuario = ".$idusuario;
              $res_cur = mysqli_query($conn,$sql_cur);
              if ( mysqli_num_rows($res_cur) > 0 ){
                $b['curtido'] = true;
              } else {
                $b["curtido"] = false;
              }

              $sql_com = "SELECT COUNT(idcomentario) as comentario FROM comentario WHERE idpostagem = ".$row["idcompartilhado"];
              $res_com = mysqli_query($conn,$sql_com);
              if ( mysqli_num_rows($res_com) > 0 ){
                while($row_com = mysqli_fetch_array($res_com)){
                  $b['comentarios'] = $row_com["comentario"];
                }
              } else {
                $b["comentarios"] = 0;
              }

              $sql_sh = "SELECT COUNT(idcompartilhado) as compartilhar FROM postagem WHERE idcompartilhado = ".$row['idcompartilhado'];
              $res_sh = mysqli_query($conn,$sql_sh);
              if ( mysqli_num_rows($res_sh) > 0 ){
                while($row_sh = mysqli_fetch_array($res_sh)){
                  $b['compartilhar'] = $row_sh["compartilhar"];
                }
              } else {
                $b["compartilhar"] = 0;
              }
            }

            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "sel_event" ){
      if ( isset($_POST["idevent"]) ){
        $idevent = $_POST["idevent"];
        $limit = $_POST["limit"];
        $sql = "SELECT postagem.idpostagem, postagem.idcompartilhado, usuario.idusuario, perfil.ft_perfil, usuario.nome, DATE_FORMAT(postagem.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),postagem.dt_hr) as diff, postagem.conteudo
                FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idperfil INNER JOIN postagem ON postagem.idusuario = usuario.idusuario
                WHERE postagem.idevento = ".$idevent." ORDER BY postagem.dt_hr DESC LIMIT ".$limit.", 10";
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          while($row = mysqli_fetch_array($res)){
            $b["res"] = 1;
            $b["idpost"] = $row["idpostagem"];
            $b["idusu"] = $row["idusuario"];
            $b["ft_perfil"] = $row["ft_perfil"];
            $b["nome"] = $row["nome"];
            $b["dt_hr"] = $row["dt_hr"];
            $b["diff"] = $row["diff"];
            $b["conteudo"] = $row["conteudo"];
            $b["idcompartilhado"] = $row["idcompartilhado"];

            $b["compartilhado"] = false;
            $sql_midia = "SELECT url FROM midia WHERE idpostagem = ".$row["idpostagem"];
            $res_midia = mysqli_query($conn,$sql_midia);
            if ( mysqli_num_rows($res_midia) > 0 ){
              $b['midia'] = mysqli_num_rows($res_midia);
              $i = 0;
              while($row_midia = mysqli_fetch_array($res_midia)){
                $b['url'.$i] = $row_midia['url'];
                $i++;
              }
            } else {
              $b["midia"] = 0;
            }

            $sql_cur = "SELECT COUNT(idcurtida) as curtida FROM curtida WHERE idpostagem = ".$row["idpostagem"];
            $res_cur = mysqli_query($conn,$sql_cur);
            if ( mysqli_num_rows($res_cur) > 0 ){
              while($row_cur = mysqli_fetch_array($res_cur)){
                $b['curtidas'] = $row_cur["curtida"];
              }
            } else {
              $b["curtidas"] = 0;
            }

            $sql_cur = "SELECT idcurtida FROM curtida WHERE idpostagem = ".$row["idpostagem"]." AND idusuario = ".$idusuario;
            $res_cur = mysqli_query($conn,$sql_cur);
            if ( mysqli_num_rows($res_cur) > 0 ){
              $b['curtido'] = true;
            } else {
              $b["curtido"] = false;
            }

            $sql_com = "SELECT COUNT(idcomentario) as comentario FROM comentario WHERE idpostagem = ".$row["idpostagem"];
            $res_com = mysqli_query($conn,$sql_com);
            if ( mysqli_num_rows($res_com) > 0 ){
              while($row_com = mysqli_fetch_array($res_com)){
                $b['comentarios'] = $row_com["comentario"];
              }
            } else {
              $b["comentarios"] = 0;
            }

            $sql_sh = "SELECT COUNT(idcompartilhado) as compartilhar FROM postagem WHERE idcompartilhado = ".($row['idcompartilhado']==null?$row["idpostagem"]:$row['idcompartilhado']);
            $res_sh = mysqli_query($conn,$sql_sh);
            if ( mysqli_num_rows($res_sh) > 0 ){
              while($row_sh = mysqli_fetch_array($res_sh)){
                $b['compartilhar'] = $row_sh["compartilhar"];
              }
            } else {
              $b["compartilhar"] = 0;
            }

            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "sel_post" ){
      if ( isset($_POST["idpost"]) ){
        $idpost = $_POST["idpost"];
        $idusuario = $_POST["idusuario"];

        $sql = "SELECT postagem.idpostagem, postagem.idcompartilhado, usuario.idusuario, perfil.ft_perfil, usuario.nome, DATE_FORMAT(postagem.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr, TIMEDIFF(NOW(),postagem.dt_hr) as diff, postagem.conteudo
                FROM usuario INNER JOIN perfil ON usuario.idusuario = perfil.idperfil INNER JOIN postagem ON postagem.idusuario = usuario.idusuario
                WHERE postagem.idpostagem = ".$idpost;
        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $row = mysqli_fetch_array($res);
          $b["res"] = 1;
          $b["idpost"] = $row["idpostagem"];
          $b["idusu"] = $row["idusuario"];
          $b["ft_perfil"] = $row["ft_perfil"];
          $b["nome"] = $row["nome"];
          $b["dt_hr"] = $row["dt_hr"];
          $b["diff"] = $row["diff"];
          $b["conteudo"] = $row["conteudo"];

          $sql_midia = "SELECT url FROM midia WHERE idpostagem = ".$idpost;
          $res_midia = mysqli_query($conn,$sql_midia);
          if ( mysqli_num_rows($res_midia) > 0 ){
            $b['midia'] = mysqli_num_rows($res_midia);
            $i = 0;
            while($row_midia = mysqli_fetch_array($res_midia)){
              $b['url'.$i] = $row_midia['url'];
              $i++;
            }
          } else {
            $b["midia"] = 0;
          }

          $sql_cur = "SELECT COUNT(idcurtida) as curtida FROM curtida WHERE idpostagem = ".$idpost;
          $res_cur = mysqli_query($conn,$sql_cur);
          if ( mysqli_num_rows($res_cur) > 0 ){
            while($row_cur = mysqli_fetch_array($res_cur)){
              $b['curtidas'] = $row_cur["curtida"];
            }
          } else {
            $b["curtidas"] = 0;
          }

          $sql_cur = "SELECT idcurtida FROM curtida WHERE idpostagem = ".$idpost." AND idusuario = ".$idusuario;
          $res_cur = mysqli_query($conn,$sql_cur);
          if ( mysqli_num_rows($res_cur) > 0 ){
            $b['curtido'] = true;
          } else {
            $b["curtido"] = false;
          }

          $sql_com = "SELECT COUNT(idcomentario) as comentario FROM comentario WHERE idpostagem = ".$idpost;
          $res_com = mysqli_query($conn,$sql_com);
          if ( mysqli_num_rows($res_com) > 0 ){
            while($row_com = mysqli_fetch_array($res_com)){
              $b['comentarios'] = $row_com["comentario"];
            }
          } else {
            $b["comentarios"] = 0;
          }

          $sql_sh = "SELECT COUNT(idcompartilhado) as compartilhar FROM postagem WHERE idcompartilhado = ".$idpost;
          $res_sh = mysqli_query($conn,$sql_sh);
          if ( mysqli_num_rows($res_sh) > 0 ){
            while($row_sh = mysqli_fetch_array($res_sh)){
              $b['compartilhar'] = $row_sh["compartilhar"];
            }
          } else {
            $b["compartilhar"] = 0;
          }

          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "curtir" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $idpost = $_POST["idpost"];
        $idcomentario = $_POST["idcomentario"];

        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i:s', time());

        $sql = "INSERT INTO curtida VALUES (null, ".$idusuario.", ".($idcomentario==null?'null':$idcomentario).", ".($idpost==null?'null':$idpost).", '".$date."')";
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
          if ( $idpost != null ){
            $sql = "SELECT idusuario FROM postagem WHERE idpostagem = ".$idpost;
            $res = mysqli_query($conn, $sql);
            $id = mysqli_fetch_array($res)['idusuario'];
          } else {
            $sql = "SELECT idusuario,idpostagem FROM comentario WHERE idcomentario = ".$idcomentario;
            $res = mysqli_query($conn, $sql);
            $id = mysqli_fetch_array($res)['idusuario'];
            $idp = mysqli_fetch_array($res)['idpostagem'];
          }

          $sql = "INSERT INTO notificacao VALUES(null, ".$id.", ".$idusuario.", ".($idpost != null?$idpost:$idp).", null, ".($idpost != null?'0':'3').", NOW())";
          $res = mysqli_query($conn, $sql);
          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "descurtir" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $idpost = $_POST["idpost"];
        $idcomentario = $_POST["idcomentario"];

        $sql = "DELETE FROM curtida WHERE idusuario = ".$idusuario." ".($idcomentario==null?'':'AND idcomentario = '.$idcomentario)." ".($idpost==null?'':'AND idpostagem = '.$idpost);
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
          if ( $idpost != null ){
            $sql = "SELECT idusuario FROM postagem WHERE idpostagem = ".$idpost;
            $res = mysqli_query($conn, $sql);
            $id = mysqli_fetch_array($res)['idusuario'];
          } else {
            $sql = "SELECT idusuario,idpostagem FROM comentario WHERE idcomentario = ".$idcomentario;
            $res = mysqli_query($conn, $sql);
            $id = mysqli_fetch_array($res)['idusuario'];
            $idpost = mysqli_fetch_array($res)['idpostagem'];
          }

          $sql = "DELETE FROM notificacao WHERE idnotificado = ".$id." AND idusuario = ".$idusuario." AND idpostagem = ".$idpost." AND tipo = ".($idpost != null?'0':'3');
          $res = mysqli_query($conn, $sql);
          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "quem_curtiu" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $idpost = $_POST["idpost"];
        $idcomentario = $_POST["idcomentario"];
        $limit = $_POST["limit"];

        $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM curtida
                INNER JOIN usuario ON usuario.idusuario = curtida.idusuario INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                WHERE ".($idcomentario==null?'':'idcomentario = '.$idcomentario)." ".($idpost==null?'':'idpostagem = '.$idpost)." ORDER BY curtida.dt_hr DESC LIMIT ".$limit.",20";
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
    } else if ( $q == "quem_comp" ){
      if ( isset($_POST["idusuario"]) ){
        $idusuario = $_POST["idusuario"];
        $idpost = $_POST["idpost"];
        $limit = $_POST["limit"];

        $sql = "SELECT usuario.idusuario, usuario.nome, perfil.ft_perfil FROM postagem
                INNER JOIN usuario ON usuario.idusuario = postagem.idusuario INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                WHERE postagem.idcompartilhado = ".$idpost." ORDER BY postagem.dt_hr DESC LIMIT ".$limit.",20";
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
    } else if ( $q == "coments" ){
      if ( isset($_POST["idpost"]) ){
        $idusu = $_POST["idusuario"];
        $idpost = $_POST["idpost"];
        $limit = $_POST["limit"];

        $sql = "SELECT comentario.idcomentario, usuario.idusuario, usuario.nome, perfil.ft_perfil, DATE_FORMAT(comentario.dt_hr,'%d/%m/%Y às %H:%i') as dt_hr,
                TIMEDIFF(NOW(),comentario.dt_hr) as diff, comentario.conteudo
                FROM comentario INNER JOIN usuario ON usuario.idusuario = comentario.idusuario INNER JOIN perfil ON perfil.idusuario = usuario.idusuario
                WHERE idpostagem = ".$idpost." ORDER BY comentario.dt_hr DESC LIMIT ".$limit.",10";

        $res = mysqli_query($conn, $sql);

        if ( mysqli_num_rows($res) > 0 ){
          $b["res"] = 1;
          while($row = mysqli_fetch_array($res)){
            $b['idcomentario'] = $row['idcomentario'];
            $b['idusuario'] = $row['idusuario'];
            $b['nome'] = $row['nome'];
            $b['foto'] = $row['ft_perfil'];
            $b['dt_hr'] = $row['dt_hr'];
            $b['diff'] = $row['diff'];
            $b['conteudo'] = $row['conteudo'];
            $sql1 = "SELECT COUNT(*) as num FROM curtida WHERE idcomentario = ".$row["idcomentario"];
            $res1 = mysqli_query($conn, $sql1);
            if ( mysqli_num_rows($res1) > 0 ){
              $row1 = mysqli_fetch_array($res1);
              $b["curtidas"] = $row1['num'];
            } else {
              $b["curtidas"] = 0;
            }
            $sql_cur = "SELECT idcurtida FROM curtida WHERE idcomentario = ".$row["idcomentario"]." AND idusuario = ".$idusu;
            $res_cur = mysqli_query($conn,$sql_cur);
            if ( mysqli_num_rows($res_cur) > 0 ){
              $b['curtido'] = true;
            } else {
              $b["curtido"] = false;
            }
            array_push($a,$b);
          }
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "comentar" ){
      if ( isset($_POST["idpost"]) ){
        $idpost = $_POST["idpost"];
        $idusuario = $_POST["idusuario"];
        $conteudo = $_POST["conteudo"];

        $sql = "INSERT INTO comentario VALUES(null, ".$idusuario.", ".$idpost.", null, '".$conteudo."', NOW())";
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
          $sql = "SELECT idusuario FROM postagem WHERE idpostagem = ".$idpost;
          $res = mysqli_query($conn, $sql);
          $id = mysqli_fetch_array($res)['idusuario'];

          $sql = "INSERT INTO notificacao VALUES(null, ".$id.", ".$idusuario.", ".$idpost.", null, 1, NOW())";
          $res = mysqli_query($conn, $sql);
          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "del_coment" ){
      if ( isset($_POST["idcomentario"]) ){
        $id = $_POST["idcomentario"];

        $sql = "SELECT postagem.idusuario as idusuario,comentario.idusuario as idusu,postagem.idpostagem FROM comentario INNER JOIN postagem ON postagem.idpostagem = comentario.idpostagem WHERE comentario.idcomentario = ".$id;
        $res = mysqli_query($conn, $sql);
        $r = mysqli_fetch_array($res);
        $idu = $r['idusuario'];
        $idusuario = $r['idusu'];
        $idpost = $r['idpostagem'];

        $sql = "DELETE FROM notificacao WHERE idnotificado = ".$idu." AND idusuario = ".$idusuario." AND idpostagem = ".$idpost." AND tipo = 1";
        $res = mysqli_query($conn, $sql);

        $sql = "DELETE FROM curtida WHERE idcomentario = ".$id;
        $res = mysqli_query($conn, $sql);

        $sql = "DELETE FROM comentario WHERE idcomentario = ".$id;
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "shared" ){
      if ( isset($_POST["idpost"]) ){
        $idpost = $_POST["idpost"];
        $idusu = $_POST["idusuario"];
        $conteudo = $_POST["conteudo"];

        $sql = "INSERT INTO postagem VALUES(null, ".$idusu.", null, ".$idpost.", '".$conteudo."', NOW(), 1, 0)";
        $res = mysqli_query($conn, $sql);

        if ( $res ){
          $b["res"] = 1;
          $sql = "SELECT idusuario FROM postagem WHERE idpostagem = ".$idpost;
          $res = mysqli_query($conn, $sql);
          $id = mysqli_fetch_array($res)['idusuario'];

          $sql = "INSERT INTO notificacao VALUES(null, ".$id.", ".$idusu.", ".$idpost.", null, 2, NOW())";
          $res = mysqli_query($conn, $sql);
          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    } else if ( $q == "del" ){
      if ( isset($_POST["idpost"]) ){
        $idpost = $_POST["idpost"];

        $sql2 = "DELETE FROM postagem WHERE idcompartilhado = ".$idpost;
        $res2 = mysqli_query($conn, $sql2);

        $sql2 = "DELETE FROM curtida WHERE idpostagem = ".$idpost;
        $res2 = mysqli_query($conn, $sql2);

        $sql3 = "DELETE FROM midia WHERE idpostagem = ".$idpost;
        $res3 = mysqli_query($conn, $sql3);

        $sql4 = "DELETE FROM notificacao WHERE idpostagem = ".$idpost;
        $res4 = mysqli_query($conn, $sql4);

        $sql5 = "SELECT * FROM comentario WHERE idpostagem = ".$idpost;
        $res5 = mysqli_query($conn, $sql5);

        while($row = mysqli_fetch_array($res5)){
          $sql6 = "DELETE FROM curtida WHERE idcomentario = ".$row['idcomentario'];
          $res6 = mysqli_query($conn, $sql6);
        }

        $sql7 = "DELETE FROM comentario WHERE idpostagem = ".$idpost;
        $res7 = mysqli_query($conn, $sql7);

        $sql1 = "DELETE FROM postagem WHERE idpostagem = ".$idpost;
        $res1 = mysqli_query($conn, $sql1);

        if ( $res1 && $res2 && $res3 && $res4 && $res5 && $res7 ){
          $b["res"] = 1;
          array_push($a,$b);
        } else {
          $b["res"] = 0;
          array_push($a,$b);
        }
      }
    }
  }

  echo json_encode($a);
?>
