<?php
   class funcs {
       public function add_new_user($email, $nome, $ident, $curso){
           include_once 'database.php';
           $db = new avdb();
           $res = array();
           //echo "<br>****cadastra o novo usuário***<br>";
           $nomea = str_replace( "'"," ",$nome);
           $identa = str_replace( "'", " ", $ident);
           if ($db->runINS("usuarios (\"Nome\", \"Mail\", \"Nivel\", \"Id\") VALUES( '$nomea', '$email', 2, '$identa')")== 1){
               //inser o novo usuário no curso
               $res['idUser']= $db->runSEL("\"Chave\" FROM usuarios WHERE \"Mail\" = '$email'");
               //localiza o código do curso
               $res['codcur'] = $db->runSEL("\"Chave\" FROM cursos WHERE \"Nome\" = '$curso'");
               $res['add'] = $db->runINS("curso_alunos (\"CodCurso\", \"CodUser\", \"Visto\") VALUES(".$res['codcur'].", ".$res['idUser'].", 0)");
               return $res;
           }
           
       }
       //public function setCurso($email, $curso){
       public function setCurso($user, $curso){
           include_once 'database.php';
           $db = new avdb();
           //$em = $db->runSEL("Email FROM tblog WHERE Email = '$email'");
           $us = $db->Select("\"user\" FROM tblog WHERE \"user\" = $user");
           if ($us == 0){
               //echo "INSRT INTO tblog (user) VALUES($user)<br>";
               $in = $db->runINS("tblog (\"user\") VALUES($user)");
               //$in = $db->runINS("tblog (ID) VALUES('$identifier')");
           }
           //grava o curso
           $db->runUpSQL("tblog SET \"CodCurso\" = $curso WHERE \"user\" = $user");
           //$db->runUpSQL("tblog SET CodCurso = $curso WHERE ID = '$identifier'");
           //echo "curso gravado<br>";
       }
       
       //public function setUser($email){
       public function setUser($identifier){
           include_once 'database.php';
           $db = new avdb();
           //$us =$db->runSEL("Chave FROM usuarios WHERE Mail = '$email'");
           $us =$db->Select("\"Chave\", \"Nome\", \"Mail\" FROM usuarios WHERE \"ID\" = '$identifier'");
           //$db->runUpSQL("tblog SET user = $us WHERE Email = '$email'");
           $db->runUpSQL("tblog SET \"nomeuser\" = '".pg_escape_string($us[0]['Nome'])."', \"Email\" = '".$us[0]['Mail']."' WHERE \"user\" = '".$us[0]['Chave']."'");
       }
       
       public function setNomeUser($identifier){
       //public function setNomeUser($email){
           include_once 'database.php';
           $db = new avdb();
           //$nm =$db->runSEL("Nome FROM usuarios WHERE Mail = '$email'");
           $nm =$db->Select("\"Nome\" FROM usuarios WHERE \"ID\" = '$identifier'");
           //$db->runUpSQL("tblog SET nomeuser = '$nm' WHERE Email = '$email'");
           $db->runUpSQL("tblog SET \"nomeuser\" = '$nm' WHERE \"ID\" = '$identifier'");
       }
       
       public function setAula($user, $CodAula){
           include_once 'database.php';
           $db = new avdb();
           $db->runUpSQL("tblog SET \"CodAula\" = '$CodAula' WHERE \"user\" = '$user'");
       }
       
       //public function getLog($email){
       public function getLog($user){
           include_once 'database.php';
           $db = new avdb();
           //$res = $db->runSEL("* FROM tblog WHERE Mail = '$email'");
           $res = $db->Select("* FROM tblog WHERE \"user\" = $user");
           
           $log['Email'] = $res[0][1];
           $log['user'] = $res[0][2];
           $log['nomeuser'] = $res[0][3];
           $log['CodCurso'] = $res[0][4];
           $log['CodAula'] = $res[0][5];
           return $log;
       }
            
       public function contapalavras($texto,$Min,$Max){
            // elimina o espaços redundantes
            while(strstr($texto,"  ")) {
                $texto = str_replace("  "," ",$texto);
            }
            // elimina as quebras de linhas 
            while(strstr($texto,PHP_EOL.PHP_EOL)) {
                $texto = str_replace(PHP_EOL.PHP_EOL,PHP_EOL,$texto);
            }
            // extrair linhas
            $linhas = explode(PHP_EOL,$texto);
            $n_palavras_total = 0;
            // contar palavras por linha
            for($l=0;$l<count($linhas);$l++) {
                $palavras = explode(" ",$linhas[$l]);
                $n_palavras_total += count($palavras);
            }
            $str = '';
            //if ($n_palavras_total < $Min || $n_palavras_total > $Max){
            
            if ($n_palavras_total > 1){
                $str = " $n_palavras_total palavras"; 
            }
            
            return $str;
       }
       
       public function sendmail_Monitor($CodCurso, $CodAula, $user, $nomeuser){
           $sql = pg_query("SELECT usuarios.\"Mail\" FROM usuarios INNER JOIN cursos ON usuarios.\"Chave\" = cursos.\"Responsavel\" WHERE cursos.\"Chave\"=$CodCurso");
           $monitor = pg_fetch_array($sql);
           $mailMonitor = $monitor['Mail'];
           
           $sql = pg_query("SELECT \"Nome\", \"Mail\" FROM usuarios WHERE \"Chave\" = $user");
           $suser = pg_fetch_array($sql);
           
           /************************************************************************************************************
           * Entre as aspas duplas
           *Insira endereço de email do superusuário caso queira receber notificação de todos os usuários que responderem o questionário.
           * Esta conta vai para o campo "Com Cópia Oculta" do email enviado. */
           $mailSuperUser = "blogoosfero@skora.com.br";
           /**********************************************************************************************/
           
           /************************************************************************************************************
           * Entre as aspas duplas
           * Informe a conta de email responsável pelo envio das mensagens.*/
            $emailsender = "blogoosfero@skora.com.br";// por exemplo: 'webmaster@seudominio'
           /****************************************************************************************************/ 
 
            // Verifica qual é o sistema operacional do servidor para ajustar o cabeçalho de forma correta.  
            if(PHP_OS == "Linux") $quebra_linha = "\n"; //Se for Linux
            elseif(PHP_OS == "WINNT") $quebra_linha = "\r\n"; // Se for Windows
            else die("Este código nao esta preparado para funcionar com o sistema operacional de seu servidor");
 
            // Parâmetros da mensagem
            $nomeremetente     = $suser['Nome'];
            $emailremetente    = trim($suser['Mail']);
            $emaildestinatario = $mailMonitor;
            $comcopia          = '';
            $comcopiaoculta    = $mailSuperUser;
            $assunto           = "$nomeuser Respondeu um question&aacute;rio";
            //envia as respostas do aluno no corpo da mensagem
            //curso
            $sql = pg_query("SELECT \"Nome\" FROM cursos WHERE \"Chave\" = $CodCurso");
            $curso = pg_fetch_array($sql);
            //aula
            $sql = pg_query("SELECT \"Nome\" FROM aulas WHERE \"Chave\"=$CodAula");
            $aula = pg_fetch_array($sql);
            
            
            $mensagem = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/><style = type=\"text/css\">td{font-family:Arial, Helvetica, sans-serif;font-size:12px}.a{color:blue;}.v{color:red;}.g{color:green;}</style></head><body><div align=center><TABLE border=0 cellSpacing=0 cellPadding=0 width=\"80%\"><tr><TD width=\"80\" align=\"left\" class=\"b\"><B>Curso:</B></TD><TD align=\"left\"><I>".$curso['Nome']."</I></TD></TR><TR><TD width=\"80\" align=\"left\" class=\"b\"><B>Aula:</B></TD><TD align=\"left\"><I>".$aula['Nome']."</I></TD></TR><TR><TD width=\"80\" align=\"left\" class=\"b\"><B>Aluno:</B></TD><TD align=\"left\"><I>".$suser['Nome']."</I></TD></TR></TABLE></DIV>".$quebra_linha;
            
            $mensagem .= "<DIV align=center><table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
            
            $pts = 0;
             
            $sqlq = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodAula\"=$CodAula ORDER BY \"IndexQuestao\"");
            while($q = pg_fetch_array($sqlq)){
                $mensagem .= "<tr><td width=\"80\" valign=\"top\" align=\"left\"><b>q - ".$q['IndexQuestao'].") [".$q['Tipo']."]</b></td><td class=\"a\" align=\"left\">".preg_replace("/(\\r)?\\n/i", "<br/>", $q['Questao'])."</td><td width=\"110\" valign=\"bottom\" align=\"right\"><b>Pts.:".$q['Peso']."</b></td></tr>";              
                switch ($q['Tipo']){
                    case ''   :  
                    case 'MP' :
                        $sqlr = pg_query("SELECT aulas_avaliacoes_alternativas.\"Alternativa\", aulas_avaliacoes_alternativas.\"Resposta\" FROM aulas_avaliacoes_alunos_respostas INNER JOIN aulas_avaliacoes_alternativas ON aulas_avaliacoes_alunos_respostas.\"CodAlternativa\" = aulas_avaliacoes_alternativas.\"Chave\" WHERE aulas_avaliacoes_alunos_respostas.\"CodAvaliacao\"=".$q['Chave']." AND aulas_avaliacoes_alunos_respostas.\"CodAluno\"=$user");
                        $r = pg_fetch_array($sqlr);
                        
                        if($r['Resposta']==1){
                            $Resposta = "Verdadeiro";
                            $class = "class=\"a\"";
                            $pts = $pts + $q['Peso']; 
                        }else{
                            $Resposta = "Falso";
                            $class = "class=\"v\"";                                    
                        }
                        $mensagem .= "<tr><td width=\"80\" valign=\"top\" align=\"left\"><b>Atlternativa</b></td><td align=\"left\">".preg_replace("/(\\r)?\\n/i", "<br/>", $r['Alternativa'])."</td><td width=\"110\" valign=\"bottom\" $class align=\"right\"><b>$Resposta</b></td></tr>";
                        break;
                    case 'DS' :
                        $sqlr = pg_query("SELECT * FROM aulas_avaliacoes_alunos_respostas WHERE \"CodCurso\" = $CodCurso AND \"CodAula\"=$CodAula AND \"CodAvaliacao\"= ".$q['Chave']." AND \"CodAluno\" = $user");
                        $r = pg_fetch_array($sqlr);
                        if($r['Nota']> 0){
                            $nota = $r['Nota'];
                            $pts = $pts + $r['Nota'];
                        }else{
                            $nota = "N/A";
                        }
                        $mensagem .= "<tr><td width=\"80\" valign=\"top\" align=\"left\">><b>Resposta</b></td><td class=\"a\" align=\"left\">".preg_replace("/(\\r)?\\n/i", "<br/>",$r['Discursiva'])."</td><td width=\"110\" valign=\"bottom\" class=\"a\" align=\"right\"><b>Pts.: $nota </b></td></tr>";
                        break;
                        
                    default :  $mensagem .= "<tr><td colspan=\"3\" align=\"center\">SEM DADOS DISPONIVEIS</td</tr>";
                                
                        
                }
            }
            $mensagem .= "</table></div>";
            
            $mensagem .= "<div align=\"center\"><table width=\"80%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">  <tr><td align=\"right\"><b>Pontua&ccedil;&atilde;o Total:</b></td><td width=\"110\" align=\"center\" class=\"a\"><b> $pts</b></td></tr></table></div></body></html>";
 
            
            /* Montando o cabeçalho da mensagem */
            $headers = "MIME-Version: 1.1".$quebra_linha;
            $headers .= "Content-type: text/html; charset=iso-8859-1".$quebra_linha;
            // Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
            $headers .= "From: ".$emailsender.$quebra_linha;
            $headers .= "Return-Path: " . $emailsender . $quebra_linha;
            // Se não houver um valor, o item não deverá ser especificado.
            if(strlen($comcopia) > 0) 
                $headers .= "Cc: ".$comcopia.$quebra_linha;
            if(strlen($comcopiaoculta) > 0) 
                $headers .= "Bcc: ".$comcopiaoculta.$quebra_linha;
            $headers .= "Reply-To: ".$emailremetente.$quebra_linha;
            
	
            /* Enviando a mensagem */
            //echo "reabilitar linha 51 antes de postar";
            //echo "remover comentário desta linha";
            mail($emaildestinatario, $assunto, $mensagem, $headers); //, "-r". $emailsender);
            //mail("destinatario@algum-email.com", "Assunto", "Texto", $headers);
            //fwrite($file=  fopen($filename,'w+'), $mensagem);
           
       }
       
       public function sendmail($mailremetente, $nomeremetente, $maildestinatario, $subject, $Msg){
            /* Verifica qual é o sistema operacional do servidor para ajustar o cabeçalho de forma correta. Não alterar */
            if(PHP_OS == "Linux") 
                $quebra_linha = "\n"; 
            elseif(PHP_OS == "WINNT") 
                $quebra_linha = "\r\n"; 
            else die("Este script nao esta preparado para funcionar com o sistema operacional de seu servidor");
           
           /************************************************************************************************************
           * Entre as aspas duplas
           *Insira endereço de email do superusuário caso queira receber notificação de todos os usuários que responderem o questionário.
           * Esta conta vai para o campo "Com Cópia Oculta" do email enviado. */
           $mailSuperUser = "blogoosfero@skora.com.br";
           /**********************************************************************************************/
           
           /************************************************************************************************************
           * Entre as aspas duplas
           * Informe a conta de email responsável pelo envio das mensagens.*/
            $emailsender = "blogoosfero@skora.com.br";// por exemplo: 'webmaster@seudominio'
           /****************************************************************************************************/ 
 
            // Verifica qual é o sistema operacional do servidor para ajustar o cabeçalho de forma correta.  
            if(PHP_OS == "Linux") $quebra_linha = "\n"; //Se for Linux
            elseif(PHP_OS == "WINNT") $quebra_linha = "\r\n"; // Se for Windows
            else die("Este código nao esta preparado para funcionar com o sistema operacional de seu servidor");
 
            // Parâmetros da mensagem
            $nmremetente     = $nomeremetente;
            $remetente    = $mailremetente;
            $destinatario = $maildestinatario;
            $comcopia          = '';
            $comcopiaoculta    = $mailSuperUser;
            $assunto           = $subject;
            
            
            
            $mensagem = $Msg;
             
            
 
            
            /* Montando o cabeçalho da mensagem */
            $headers = "MIME-Version: 1.1".$quebra_linha;
            $headers .= "Content-type: text/html; charset=iso-8859-1".$quebra_linha;
            // Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
            $headers .= "From: ".$remetente.$quebra_linha;
            $headers .= "Return-Path: " . $emailsender . $quebra_linha;
            // Se não houver um valor, o item não deverá ser especificado.
            if(strlen($comcopia) > 0) 
                $headers .= "Cc: ".$comcopia.$quebra_linha;
            if(strlen($comcopiaoculta) > 0) 
                $headers .= "Bcc: ".$comcopiaoculta.$quebra_linha;
            $headers .= "Reply-To: ".$remetente.$quebra_linha;
            
	
            /* Enviando a mensagem */
            //echo "reabilitar linha 51 antes de postar";
            //echo "remover comentário desta linha";
            //echo $destinatario."<br/>".$assunto."<br/>".$mensagem."<br/>.$headers";
            
            mail($destinatario, $assunto, $mensagem, $headers, "-r". $emailsender);

            
       }
               
   }
?>
