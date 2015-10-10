<?php
/*
 *  FUNÇÕES DE ADMIN
 */
?>
<script type="text/javascript" src="script.js"></script>
<?php
class Admin {
    
    public function Capa($userid, $usernome, $CodCurso) {
	$this->put_menu($usernome, 1);
	$this->put_cursos($userid, 0, $CodCurso);
	return  0;
    }
    
    public function Checa_Aulas($iduser, $nomeuser ,$cod_curso, $pos, $func){
        //echo "Checa_Aulas-> iduser:$iduser, nomeuser: $nomeuser ,cod_curso: $cod_curso, pos: $pos, func:$func<br>";
        $this->put_menu($nomeuser,'voltarAdm');
	$this->lista_aulas($cod_curso, 0);
	return 0;
    }//Checa_Aulas
    
    public function Edita_Aula($iduser, $nomeuser, $codaula, $posi, $msg){
        $db = new avdb;
        $db->Conn_av();
        //echo "<br> Edita aula $codaula";
        $this->put_menu($nomeuser,'voltarChkAulas');		
	
        $this->put_Edita_Aula($codaula, $msg);
	
        $this-> put_tit_aula($codaula);
		
	$this->listaquestoes($codaula,$posi);
        $db->close_conn_av();
        
    }//Edita Aula
    
    public function Exibe_alternativas($nomeuser, $codcurso, $cod_aula, $cod_quest, $cod_Alternativa, $msg){
        $db = new avdb;
        $db->Conn_av();
        $this->put_menu($nomeuser,'voltarChkAulas');		
	$this-> put_tit_aula($cod_aula);
	$this->put_questao($cod_quest);
	//verifica se a questão é de múltipla escolha e, caso positivo, exibe as alternativas
	$sql = pg_query("SELECT \"Tipo\" FROM aulas_avaliacoes WHERE \"Chave\" = $cod_quest");
	$r = pg_fetch_row($sql);
	
      
        //echo "<br>Tipo: $r[0] <br>";
	if ($r[0] == '' || $r[0] == 'MP' || $r[0] == 'SM' ) {		
            //echo "exibe tudo r[0] =$r[0] <br>";
            $this->lista_alternativas($cod_quest, $cod_Alternativa, $msg); //$cod_Alternativa alternativa para edição
	}
	$this->listaquestoes($cod_aula,0);
        $db->close_conn_av();
    }// Exibe_alternativas($codcurso, $cod_aula, $cod_quest){
    
    public function UpdateQuestao($nomeuser, $Cod_Curso, $cod_aula, $cod_quest, $msg, $Indice, $texto, $Peso, 
            $tipo, $Min, $Max){
        $db = new avdb;
        $db->Conn_av();
        $this->put_menu($nomeuser,'voltarChkAulas');		
	$this-> put_tit_aula($cod_aula);
		
	$this->put_form_EditaQuestao($msg, $cod_quest, $Indice, $texto, $Peso, $tipo, $Min, $Max);
		
	if ($tipo == 'MP' || $tipo == 'SM') {
            $this->lista_alternativas($cod_quest, 0, ''); //$cod_Alternativa alternativa para edição
	}
	$this->listaquestoes($cod_aula,0);
        $db->close_conn_av();
    }//UpdateQuestao($nomeuser, $Cod_Curso, $cod_aula, $cod_quest);
    
    public function Salva_Update_Questao($codaula, $Cod_Curso, $Cod_quest, $indice, $texto, $peso, $Cod_quest, $tipo,
            $Min, $Max){
        $msg = '';
	if ( !is_numeric($indice) || ($indice <= 0 || $indice >= 51) ) {
            $msg = "Sequ&ecirc;ncia deve ser um valor inteiro entre 1 e 50.";
	}
        if (strlen(trim($texto)) <= 10) {
            if ( strlen($msg) > 0) { 
                $msg = $msg."<br>"; 
            }
            $msg = $msg."Quest&atilde;o deve conter ao menos 10 caracteres.";
        }
        if (!(is_int($peso) ) && ($peso <= 0) ) {
            if ( strlen($msg) > 0) { 
                $msg = $msg."<br>"; 
            }
            $msg = $msg."Peso deve ser um número maior que zero.";
        }
        if ($Min != '' && !is_numeric($Min) || $Min < 0 ){
            $msg .= "O N&uacute;mero M&iacute;nimo de palavras ($Min) deve ser maior que Zero.\n";
        }
        if ($Max <> '' && !is_numeric($Max) || $Max < $Min ) {
            $msg .= "O N&uacute;mero M&aacute;ximo de palavras deve ser maior que o M&iacute;nimo.\n";
        }

        //echo "<br>UPDATE aulas_avaliacoes SET CodCurso = $Cod_Curso, CodAula = $codaula, IndexQuestao = $indice, Questao = '$texto', Peso = $peso, Tipo = '$tipo' WHERE Chave = $Cod_quest<br>";
        //Grava
        if (strlen($msg)==0) {
            $db = new avdb;
            $db->Conn_av();
            if($Min == '') $Min = 'NULL';
            if($Max == '') $Max = 'NULL';
            $textoa = addslashes($texto);
            if ($sql = pg_query("UPDATE aulas_avaliacoes SET \"CodCurso\" = $Cod_Curso, \"CodAula\" = $codaula, 
                \"IndexQuestao\" = $indice, \"Questao\" = '$textoa', \"Peso\" = $peso, \"Tipo\" = '$tipo', \"Min\" = $Min, \"Max\" = $Max 
                    WHERE \"Chave\" = $Cod_quest")) {
                return $msg;
            } else {
                $msg = "N&atilde;o foi possivel salvar o registro";
                return $msg;
            }
        }
	return $msg;

    }//Salva_Update_Questao($codaula, $Cod_Curso, $Cod_quest, $indice, $texto, $peso, $Cod_quest);
    
    public function Update_Alternativa($CodAula, $Cod_Curso, $Cod_quest, $Cod_Alternativa, $Indice, $texto, $valor) {
        $db = new avdb;
        $db->Conn_av();
        $msg = '';
	if ( !is_numeric($Indice) || ($Indice <= 0 || $Indice >= 11) ) {
            $msg = "N&uacute;mero da Alternativa: deve ser um valor inteiro entre 1 e 50.";
        }
	if (strlen(trim($texto)) == 0) {
            if ( strlen($msg) > 0) { $msg = $msg."<br>"; }
            $msg = $msg."Alternativa n&atilde;o foi preenchida.";
        }
        


	//Grava
	if (strlen($msg)==0) {
            //echo "INSERT INTO aulas_avaliacoes_alternativas VALUES(Null, $cod_aula, $codAvaliacao, $Indice, '$Quest', $valor)<br>";
            //echo "<br> VALOR RESPOSTA: $valor";
            //echo "<br> UPDATE aulas_avaliacoes_alternativas SET CodAula = $CodAula, CodAvaliacao = $Cod_quest, IndexAlternativa = $Indice, Alternativa = '$texto', Resposta = $valor WHERE Chave = $Cod_quest"; //erro no envio de cod quest
            if ($sql == pg_query("UPDATE aulas_avaliacoes_alternativas SET \"CodAula\" = $CodAula, 
                \"CodAvaliacao\" = $Cod_quest, \"IndexAlternativa\" = $Indice, \"Alternativa\" = '$texto', 
                \"Resposta\" = $valor WHERE \"Chave\" = $Cod_Alternativa") ) {
                return $msg;
            } else {
                $msg = "N&atilde;o foi possivel salvar o registro";
                return $msg;
            }
	}
	//echo "<br> MSG: $msg";
	return $msg;
		
    }//Update_Alternativa

    public function Nova_Questao($iduser, $nomeuser, $cod_curso, $cod_aula, $msg, $Indice, $Quest, $Peso, 
            $tipo, $Min, $Max){
        $db = new avdb;
        $db->Conn_av();

        $this->put_menu($nomeuser,'voltarChkAulas');		
		
	$this-> put_tit_aula($cod_aula);
	$this->put_form_NovaQuestao($msg, $Indice, $Quest, $Peso, $tipo,$cod_curso, $cod_aula, $Min, $Max);
		
		//echo "<br> Listar alternativas";
		
	$this->listaquestoes($cod_aula,0);
	
	
    }//Nova_Questao($iduser, $nomeuser, $cod_curso, $cod_aula, $msg);
    
    public function SalvarNovaQuestao($codcurso, $cod_aula, $Indice, $Quest, $Peso, $tipo, $Min, $Max){
        //echo '<br> Entrou Salvar Questão';
	$msg = '';
	if ( !is_numeric($Indice) || ($Indice <= 0 || $Indice >= 51) ) {
            $msg = "Sequ&ecirc;ncia deve ser um valor inteiro entre 1 e 50.";
	}
	if (strlen(trim($Quest)) <= 10) {
            if ( strlen($msg) > 0) { $msg = $msg."<br>"; }
            $msg = $msg."Quest&atilde;o deve conter ao menos 10 caracteres.";
	}
	if ($tipo <> 'SM'){
            if (!(is_int($Peso) ) && ($Peso <= 0) ) {
                if ( strlen($msg) > 0) { $msg = $msg."<br>"; }
                $msg = $msg."Peso deve ser um número maior que zero.";
            }
        } else {
            if (!(is_int($Peso) ) && ($Peso < 0) ) {
                if ( strlen($msg) > 0) { $msg = $msg."<br>"; }
                $msg = $msg."Peso deve ser um n&uacute;mero maior ou igual a zero.";
            }
            
        }
        if ($Min != '' && !is_numeric($Min) || $Min < 0 ){
            $msg .= "O N&uacute;mero M&iacute;nimo de palavras ($Min) deve ser maior que Zero.\n";
        }
        if ($Max <> '' && !is_numeric($Max) || $Max < $Min ) {
            $msg .= "O N&uacute;mero M&aacute;ximo de palavras deve ser maior que o M&iacute;nimo.\n";
        }
	//Grava
	if (strlen($msg)==0) {
            $db = new avdb;
            $db->Conn_av();
            if($Min == '') $Min = 'NULL';
            if($Max == '') $Max = 'NULL';
            $Questa = addslashes($Quest);
            //echo "INSERT INTO aulas_avaliacoes VALUES(Null, $codcurso, $cod_aula, $Indice, '$Quest', $Peso, '$tipo')<br>";
            if ($sql = pg_query("INSERT INTO aulas_avaliacoes (\"CodCurso\", \"CodAula\", \"IndexQuestao\", \"Questao\", \"Peso\", \"Tipo\", \"Min\", \"Max\") VALUES( $codcurso, $cod_aula, $Indice, '$Questa', $Peso, '$tipo', $Min, $Max)") ) {
                return $msg;
            } else {
                $msg = "N&atilde;o foi possivel salvar o registro";
		return $msg;
            }
	}
	return $msg;
    }//SalvarNovaQuestao
    
    public function get_last_quest($codcurso, $cod_aula) {
        $db = new avdb;
        $db->Conn_av();
	
        if ($sql = pg_query("SELECT MAX(\"Chave\") FROM aulas_avaliacoes WHERE ((\"CodCurso\" = $codcurso) AND (\"CodAula\" = $cod_aula))") ){
            $r = pg_fetch_array($sql);
        
            return $r[0];
	} else {
            return 0;
	}			
    }//get_last_quest
    
    public function Nova_alternativa($nomeuser, $codcurso, $cod_aula, $cod_quest, $msg, $ind, $alt, $val){
        $db = new avdb;
        $db->Conn_av();
	$this->put_menu($nomeuser,'voltarChkAulas');		
	$this-> put_tit_aula($cod_aula);
	$this->put_questao($cod_quest);
	$this->lista_alternativas($cod_quest,0, ''); //0 alternativa para edição
	$this->form_nova_alternativa($codcurso, $cod_aula, $msg, $cod_quest, $ind, $alt, $val);
	$this->listaquestoes($cod_aula,0);
    }//Nova_alternativa($nomeuser, $codcurso, $cod_aula, $cod_quest);
    
    public function SalvarNovaAlternativa($cod_aula, $codAvaliacao, $Indice, $Quest, $valor) {
        $msg = '';
	if ( !is_numeric($Indice) || ($Indice <= 0 || $Indice >= 11) ) {
            $msg = "Número da Alternativa: deve ser um valor inteiro entre 1 e 50.";
	}
        if (strlen(trim($Quest)) == 0) {
            if ( strlen($msg) > 0) { $msg = $msg."<br>"; }
            $msg = $msg."Alternativa não foi preenchida.";
        }
	//Grava
	if (strlen($msg)==0) {
            $db = new avdb;
            $db->Conn_av();
            //echo "INSERT INTO aulas_avaliacoes_alternativas VALUES(Null, $cod_aula, $codAvaliacao, $Indice, '$Quest', $valor)<br>";
            if ($sql = pg_query("INSERT INTO aulas_avaliacoes_alternativas (\"CodAula\", \"CodAvaliacao\", \"IndexAlternativa\", \"Alternativa\", \"Resposta\") VALUES($cod_aula, $codAvaliacao, $Indice, '$Quest', $valor)") ) {
                return $msg;
            } else {
                $msg = "Não foi possivel salvar o registro";
                return $msg;
            }
	}
	return $msg;
		
    }//SalvarNovaAlternativa($cod_aula, $codAvaliacao $Indice, $Quest, $valor)
    
    public function Nova_Aula($iduser, $nomeuser, $cod_curso, $msg) {
        $db = new avdb;
        $db->Conn_av();
	$this->put_menu($nomeuser,'voltarAdm');
        $this->put_Form_NovaAula($msg);
	$this->lista_aulas($cod_curso, 0);
    }
    
    public function Lista_Alunos_Curso($nomeuser, $Cod_Curso, $aluno, $msg, $aula, $idquest){
        $db = new avdb;
        $db->Conn_av();

        $this->put_menu($nomeuser,'voltarAdm');
	$this->put_Lista_Alunos($Cod_Curso, $aluno, $msg, $aula, $idquest);
	//echo "<br> Lista Alunos do CURSO: $Cod_Curso";

	return  0;
    }//Lista_Alunos_Curso($nomeuser, $nomeuser, $pos);	
    
    public function DesenpenhoAluno($nomeuser, $codCurso, $aluno, $msg, $edita){
        $db = new avdb;
        $db->Conn_av();
	
        //div para reposicionamento do scroll
	$this->put_menu($nomeuser,'voltarAdm');
	$this->put_Quest_Aluno($codCurso, $aluno, $msg, $edita);
	$this->put_Lista_Alunos($codCurso, 0, '');
    }
    
    public function Exporta_DesempenhoAluno($codCurso, $aluno){
        $db = new avdb;
        $db->Conn_av();
        $this->exporta($codCurso, $aluno);
    }
    
    
    function put_menu($nomeuser, $voltar){
        ?>
            <div align="center">
                <table class="tablemenuAdm"> 
		<tr>					  
                <td align="left"  class="tdmenuAdm"><b> Bem vindo, 
                    <?php echo($nomeuser) ?> </b>
                </td>
						
		<td width="100" align="center" valign="middle">&nbsp;
		</td>
		</form>
	<?php
                if($voltar == 0 ) {
                    echo "<td align=\"right\" class=\"tdmenuAdm\" ></td>
                    <form id=\"voltar\" name=\"voltar\" method=\"post\" action=\"\">
                    <td width=\"100\" align=\"center\" valign=\"middle\"> <input name=\"".$voltar."\" type=\"submit\" value=\"Voltar\" class=\"botaoAdm\"/> </td>
                    </form>";
		}
	?>
                <!--
                <form id="sair" name="sair" method="post" action="">
		<td width="50" align="center" valign="middle"><input name="Sair" type="submit" id="Sair" value="Sair" class="botaoAdm"/> 
		</form>
		</td>
                -->
                </tr>			  
						
		</table>
				
		<table class="tblinha">
		<tr>
		<td>&nbsp;</td>
		</tr>
		</table>
            </div>
				
	<?php
    } // fim do put menu
    
    function put_cursos($userid, $inicio, $CodCurso) {
        ?>
            <div align="center">
            <table class="tabTitle">
            <tr>
	<?php
            
            //exibe o nÂº de cursos cadastrados para o adm selecionado
            $db = new avdb;
            $db->Conn_av();
            /*
            $sql = "SELECT COUNT(Chave)FROM cursos WHERE Responsavel = $userid";
            $res = mysql_query($sql)or die(mysql_error());
            $r = mysql_fetch_row($res);
            * 
            */
	?>
            <!--
            <td align="left"><b><//?php echo($r[0]) ?> cursos cadastrados</b></td>
            -->
            </tr>
            </table>
            <table class="tablemenuAdm">
            <tr>
            <td width="30" align="center" class="tdmenuAdm"><b>Id</b></td>
            <td align="center" class="tdmenuAdm"><b>Curso</b></td>
            <td align="center" width="60" class="tdmenuAdm"><b>Aulas</b></td>
            <td align="center" width="20" class="tdmenuAdm"></td>
            <td align="center" width="60" class="tdmenuAdm"><b>Alunos</b></td>
            <td align="center" width="20" class="tdmenuAdm"></td>
            </tr>
            </table>
			
            <table class="tabValores">
        <?php
            //preenche 10 Ãºltimas linhas da tabela Cursos
						
            $c1 = "#FFFFFF";
            $c2 = "#CCCCCC";
            $c = 0;
            
            ////consulta lista de cursos do usuário com contador de aulas e alunos para cada curso listadocount aula e alunos por curso
            /*echo "SELECT cursos.Chave, cursos.Nome, COUNT( aulas.CodCurso ) 
                AS Aulas FROM cursos LEFT JOIN aulas ON aulas.CodCurso = cursos.Chave WHERE 
                (cursos.Responsavel = $userid AND cursos.Chave = $CodCurso) GROUP BY cursos.Chave, cursos.Nome 
                ORDER BY cursos.Chave DESC<br>";
             * 
             */
            if ($res = pg_query("SELECT cursos.\"Chave\", cursos.\"Nome\", COUNT( aulas.\"CodCurso\" ) 
                AS \"Aulas\" FROM cursos LEFT JOIN aulas ON aulas.\"CodCurso\" = cursos.\"Chave\" WHERE 
                (cursos.\"Responsavel\" = $userid AND cursos.\"Chave\" = $CodCurso) GROUP BY cursos.\"Chave\", cursos.\"Nome\" 
                ORDER BY cursos.\"Chave\" DESC")) {
                while($r = pg_fetch_array($res)) {
                    //conta alunos
                    $s = pg_query("SELECT COUNT(\"CodCurso\") FROM curso_alunos WHERE \"CodCurso\" = $CodCurso");
                    $a = pg_fetch_array($s);
                    //echo $r; tr bgcolor=".(($c++&1)?$c1:$c2)."
                    $aulas = $r['Chave'];
                    echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> 
                    <td width=\"30\">".$r['Chave']."</td>
                    <td align=\"left\">".$r['Nome']."</td>
                    <td width=\"60\">".$r['Aulas']."</td>
                    
                    <form name=\"F_ChecaAulas\" id=\"checaaulas\" method=\"post\">
                    <input type=\"hidden\" name=\"F_ChecaAulas\" value=\"".$aulas."\">
                    <td width=\"20\">
                    <input type=\"submit\" class=\"btnEdita\" name=\"btn\" title=\"Editar Aulas\" alt=\"Edita Aulas\" value=\"\">
                    </td>
                    </form>
                    
                    <td width=\"60\">".$a[0]."</td>
                    
                    <form name=\"F_ChecaAlunos".$r['Chave']."\" method=\"post\">
                    <input type=\"hidden\" name=\"ChecaAlunos\" value=\"".$r['Chave']."\">
                    <td width=\"20\">
                    <input type=\"submit\" class=\"btnEdita\" name=\"btn\" title=\"Editar Alunos\" alt=\"Editar Alunos\" value=\"\">
                    </td>
                    </form>
                    </tr>
                    ";
		}
            }
	?>
            </table>
            </table>
            <table class="tabTitle">
            <form id="MoreCursos" name="Morecursos" method="post" action="">
            <tr><td align="right">
            <!--
            <input name="MoreCursos" type="submit" id="MoreCursos" value="Mais 10" class="botaoAdm" />
            -->
            </form>
            </td></tr>
            </table>
            <table class="tblinha">
            <tr>
            <td>&nbsp;</td>
            </tr>
            </table>

            </div>
	<?php	
        $db->close_conn_av();
    } // put_put_cursos
    
    function lista_aulas($cod_curso, $inicio){
        $db = new avdb;
        $db->Conn_av();
        $this->titulo_new_aula($cod_curso);
			
	?>
            <div align="center">
            <table class="tablemenuAdm">
            <tr>
            <td width="45" align="center" class="tdmenuAdm"><b>Id</b></td>
            <td align="left" class="tdmenuAdm"><b>Aulas</b></td>
            <td width="45" align="center" class="tdmenuAdm"><b>Quest&otilde;es</b></td>
            <td align="center" width="20" class="tdmenuAdm"></td>
            </tr>
            </table>
            </div>
	<?php
            //<!-- Tabela de dados -->
            echo "<div align=\"center\">
            <table class=\"tabValores\">";
            
            $c1 = "#FFFFFF";
            $c2 = "#CCCCCC";
            $c = 0;
            if ($sql =  pg_query("SELECT aulas.\"Chave\", aulas.\"Nome\", COUNT( aulas_avaliacoes.\"CodAula\" ) AS \"c_Aulas\"
                    FROM aulas LEFT JOIN aulas_avaliacoes ON aulas.\"Chave\" = aulas_avaliacoes.\"CodAula\" 
                    WHERE aulas.\"CodCurso\" = $cod_curso
                    GROUP BY aulas.\"Chave\", aulas.\"Nome\" ORDER BY aulas.\"Chave\" DESC") ) {
				// LIMIT $inicio , $inicio10
				//echo "<br> SQL: $sql";
				
		while ($r = pg_fetch_array($sql)){
                        echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> 
			<td width=\"45\" align=\"center\">".$r['Chave']."</td>
			<td align=\"left\">".$r['Nome']."</td>
			<td width=\"45\" align=\"center\">".$r['c_Aulas']."</td>
			<form name=\"F_EditaAulas\"".$r['Chave']."\" method=\"post\">
                        <td align=\"center\" width=\"20\"> 
			<input type=\"hidden\" name=\"EditaAula\" value=\"".$r['Chave']."\">
			<input type=\"submit\" class=\"btnEdita\" name=\"btn\" title=\"Editar Aula\" alt=\"Editar
                        Aula\" value=\"\">
                        </td>
                        </form>
                        </tr>";
		} //while
            } 	//if
            echo "</table>
            </div>";
			
			
		echo "
		<!-- Botões nova aula e mais 10 -->
		<div align=\"center\">
		</table>
		<table class=\"tabTitle\">
		<tr>
		<td align=\"left\" width=\"45\">
		<form id=\"NovaAula\" name=\"NovaAula\" method=\"post\" action=\"\">
		<input name=\"NovaAula\" type=\"submit\" id=\"NovaAula\" value=\"NovaAula\" class=\"botaoAdm\" />
		</form>
		</td>
		<td align=\"left\">
		<!--
		<form id=\"ImportAulas\" name=\"ImportAulas\" method=\"post\" action=\"\">
		<input name=\"ImportAula\" type=\"submit\" id=\"ImportAula\" value=\"Importar Aulas\" class=\"botaoAdm\" />
		</form>
		-->
		</td>
		<td align=\"right\">
		<form id=\"MoreCursos\" name=\"Morecursos\" method=\"post\" action=\"\">
		<!--
		<input name=\"MoreCursos\" type=\"submit\" id=\"MoreCursos\" value=\"Mais 10\" class=\"botaoAdm\" />
		-->
		</form>
		</td>
		</tr>	
		</table>
		<table class=\"tblinha\">
		<tr>
		<td>&nbsp;</td>
		</tr>
		</table>
			
                </div>";
			
		//echo "<br> Listou aulas";
			
								
		//*/
        $db->close_conn_av();
    }//lista_aulas

    function titulo_new_aula($cod_curso){
        $sql = pg_query("SELECT cursos.\"Nome\", COUNT(aulas.\"Chave\") FROM cursos LEFT JOIN aulas ON aulas.\"CodCurso\" = cursos.\"Chave\" WHERE (cursos.\"Chave\" = $cod_curso) GROUP BY cursos.\"Nome\"");
	$r = pg_fetch_row($sql);
			
	?>
	<div align="center">
        <table class="tabTitle">
	<tr>
	<td><b><?php echo($r[0]." - ".$r[1]) ?> aulas cadastradas</b></td>
	</tr>
	</table>
	</div>
	<?php
    }//titulo_new_aula($cod_curso);
    
    function put_tit_aula($codaula){
        $sql = pg_query("SELECT aulas.\"Nome\", COUNT(aulas_avaliacoes.\"Chave\") FROM aulas LEFT JOIN aulas_avaliacoes ON aulas_avaliacoes.\"CodAula\" = aulas.\"Chave\" WHERE (aulas.\"Chave\" = $codaula) GROUP BY aulas.\"Nome\"");
	$r = pg_fetch_row($sql);
			
	?>
	<div align="center">
        <table class="tabTitle">
	<tr>
	<td><b><?php echo $r[0]." - ".$r[1] ?> quest&otilde;es cadastradas</b></td>
	</tr>
	</table>
	</div>
	<?php
    }//put_tit_aula($codaula);
    
    function put_Edita_Aula($codaula, $msg){
        $sql = pg_query("SELECT * FROM aulas WHERE \"Chave\" = $codaula");
        $r = pg_fetch_array($sql);
        
        //echo "<br>DATA LIMITE:".$r['DataLimite']."<br>";
        //formata a data para dd/mm/aaaa
        $dataLimite = '';
        if(($r['DataLimite'] <> NULL)&& ($r['DataLimite'] <> '0000-00-00') ){
            $ano = substr($r['DataLimite'], 0,4);
            $mes = substr($r['DataLimite'], 5,2);
            $dia = substr($r['DataLimite'], 8,2);
            $dataLimite = $dia.'/'.$mes.'/'.$ano;
        } else $dataLimite = '';
        
        
        ?>	
	<div align="center">
	<table class="tbform">
	<form id="objnovaAula" name="novaAula" method="post" action="" class="tbform">
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Nome da Aula:<br> 
        <input type="text" name="NomeAula" size="45" value="<?php echo $r['Nome']?>"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">M&iacute;nimo de acertos em quest&otilde;es de m&uacute;ltipla escolha<br/> 
                        ou <br/>
                        M&iacute;nimo de respostas em quest&otilde;es discursivas:<br/>
            <input type="text" name="MimMP" size="20" value="<?php echo $r['MinimoMP'] ?>" ><br/>
                        (Esta verificação pode gerar erros no caso de aula com tipos diferentes combinados)
        </td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Data Limite (dd/mm/aaaa):<br> 
            <input type="text" name="DataLimite" size="20" value="<?php echo $dataLimite ?>" OnKeyUp="mascaraData(this, 1);" maxlength="10"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
        <input type="hidden" name="CodCurso" value="<?php echo $r['CodCurso']?>">
        <input type="hidden" name="CodAula" value="<?php echo $codaula?>">            
        <tr>
        <td width="15" >&nbsp;</td>
        <td align="right"><input type="submit" name="SalvarEditaAula" value="Salvar"></td>
        <td width="15" >&nbsp;</td> 
        
        </tr>
        </form>	
        </table> 
	<?php 
	if ($msg != '') {
            echo "
            <table class=\"tbform\">
            <tr>
            <td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
            </tr>
            </table>";
	}
	?>
	</div>
	<?php
    }//put_Edita_Aula

    function listaquestoes($codaula, $inicio){
			
        ?>
			
	<div align="center">
	<table class="tablemenuAdm">
	<tr>
	<td width="20" align="center" class="tdmenuAdm"><b>Id</b></td>
	<td align="left" class="tdmenuAdm"><b>Quest&atilde;o</b></td>
	<td width="40"align="center" class="tdmenuAdm" ><b>Peso</b></td>
	<td align="center" width="20" class="tdmenuAdm"></td>
	</tr>
	</table>
			
			
	<!-- Tabela de dados -->
	<table class="tabValores">
			
	<?php
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0;
	
			
	//echo "<br> Cod Aula: $codaula";
			
	if ($sql = pg_query("SELECT \"Chave\", \"IndexQuestao\", \"Questao\", \"Peso\" 
            FROM aulas_avaliacoes 
            WHERE \"CodAula\" = $codaula ORDER BY \"IndexQuestao\" ") ) {
            //LIMIT $inicio , $inicio10
				
            while ($r = pg_fetch_array($sql)){
                echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> <td width=\"20\" align=\"center\">".$r['IndexQuestao']."</td>
                    <td  align=\"left\">".$r['Questao']."</td>
                    <td align=\"center\">".$r['Peso']."</td>
                    <form name=\"F_EditaQuestao".$r['Chave']."\" method=\"post\">
                    <td align=\"center\" width=\"20\"> 
                    <input type=\"hidden\" name=\"EditaQuestao\" value=\"".$r['Chave']."\">
                    <input type=\"submit\" class=\"btnEdita\" name=\"btn\" title=\"Editar Quest&atilde;o\" alt=\"Editar Quest&atilde;o\" value=\"\">
                    </td>
                    </form>
                    </tr>";
            } //while
	} 	//if
	?>
        </table>
	<!-- Botões nova aula e mais 10 -->
	</table>
	<table class="tabTitle">
	<tr>
	<td align="left">
	<form id="NovaQuestao" name="NovaQuestao" method="post" action="">
	<input name="NovaQuestao" type="submit" id="NovaQuestao" value="Nova Questao" class="botaoAdm" />
	</form>
	</td>
	<td align="right">
	<form id="MoreCursos" name="Morecursos" method="post" action="">
	<!--
	<input name="MoreCursos" type="submit" id="MoreCursos" value="Mais 10" class="botaoAdm" />
	-->
	</form>
	</td>
	</tr>	
	</table>
	<table class="tblinha">
	<tr>
	<td>&nbsp;</td>
	</tr>
	</table>
			
	</div>
			
					
	<?php
    }//listaquestoes($codaula);
    
    function put_questao($cod_quest) {
        if ($cod_quest >= 1) {
            if ($sql = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"Chave\" = $cod_quest ORDER BY \"IndexQuestao\"" ) ) {
                $r = pg_fetch_array($sql);
                //echo "tipo:".$r[6]."<br>";
                //echo "SELECT * FROM aulas_avaliacoes WHERE Chave = $cod_quest ORDER BY IndexQuestao<br>";
                $quest = $r[4];//htmlspecialchars($r[4]);
                
                
                //addslashes($r[4]);
                //echo $r[4]."\n";
	?>	
                <div align="center">
                <table width="400" class="tbform">
                <tr>
                <form id="UpdateQuestao" name="UpdateQuestao" method="post" action="" class="tbform">
                <td width="5"></td>
                <td colspan="2" align="right">
                <input type="submit" class="btnEdita" name="UpdateQuestao" alt="Editar Quest&atilde;o" value="<?php echo $r[0]?>">
                <input type="hidden" name="Indice" value="<?php echo $r[3]?>" >
                <input type="hidden" name="texto" value="<?php echo preg_replace("/(\\r)?\\n/i", "<br/>", $quest) ?>" >
                <input type="hidden" name="peso" value="<?php echo $r[5]?>" >
                <input type="hidden" name="tipo" value="<?php echo $r[6]?>" >
                <input type="hidden" name="min" value="<?php echo $r[7]?>" >
                <input type="hidden" name="max" value="<?php echo $r[8]?>" >
                </td>
                </form>
                <td width="5"></td>
                </tr>
                <tr>
                <td width="5" >&nbsp;</td>
                <td width="200" align="left"><b> Quest&atilde;o:</b> <?php echo $r['IndexQuestao'];?></td>
                <td width="190"align="right"><b>Peso:</b><?php echo $r['Peso'];?></td>
                <td width="5" >&nbsp;</td> 
                </tr>
		<tr>
                <td width="5" >&nbsp;</td>
                <td align="left" colspan="2"><?php echo $r['Questao'];?></td>
                <td width="5" >&nbsp;</td> 
                </tr>
								
                </table> 
		</div>	
        <?php 
            }
        }
			
    }//put_questao($cod_quest)
    
    function lista_alternativas($cod_quest, $edita, $msg){
			
        //contador de alternativas cadastradas
	if ($sql = pg_query("SELECT COUNT(\"Chave\") FROM aulas_avaliacoes_alternativas  WHERE \"CodAvaliacao\" = $cod_quest") ) {
            $r = pg_fetch_row($sql);
	?>
            <div align="center">
    	    <table class="tabTitle">
            <tr>
            <td><b><?php echo " $r[0]"; ?> Alternativas cadastradas</b></td>
            </tr>
            </table>
            </div>
	<?php
	}
        //echo "<br>SELECT * FROM aulas_avaliacoes_alternativas WHERE CodAvaliacao = $cod_quest ORDER BY IndexAlternativa";
	if ($sql = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAvaliacao\" = $cod_quest ORDER BY \"IndexAlternativa\"") ) {
            if (pg_num_rows($sql) >= 1){
                while ($r = pg_fetch_array($sql)) {
                    if ($edita == $r[0]) {
                        //echo "<br>Edita registro $r[0]";
                        //desenha o formulário 
			?>
			<div align="center">
			<table class="tbform">
			<form id="novaAula" name="Salvar_Edicoes_Alternativa" method="post" action="" class="tbform">
			<tr>
                        <td width="10" >&nbsp;</td>
                        <td align="left">N&uacute;mero da Alternativa:<br> 
                        <input type="text" name="Indice" size="15" value="<?php echo $r[3] ?>"></td>     
                        <td width="10" >&nbsp;</td> 
                        </tr>
			<tr>
                    	<td width="10" >&nbsp;</td>
                        <td align="left">Texto Alternativa:<br> 
                        <textarea rows="3" cols="45" name="Quest"><?php echo $r[4] ?></textarea></td>     
                        <td width="10" >&nbsp;</td> 
			</tr>
                        <tr>
                    	<td width="10" >&nbsp;</td>
                        <td align="left">Verdadeiro:  
                       	<input type="checkbox" name="Valor"<?php echo (($r[5]==1)?"checked=\"checked\"":''); ?> >
			<input type="hidden" name="codquest" value="<?php echo $cod_quest ?>">
			<input type="hidden" name="codAlternativa" value="<?php echo $r[0] ?>">
			</td>     
                        <td width="10" >&nbsp;</td> 
			</tr>
                        <tr>
                    	<td width="15" >&nbsp;</td>
                        <td align="right"><input type="submit" name="Salvar_Edicoes_Alternativa" value="Salvar"></td>
                        <td width="15" >&nbsp;</td> 
			</tr>
			</form>	
              		</table> 
			</div>	
			<?php	
			if (strlen($msg)!=0) { // exibe mensagem de erro
                            echo " <div align=\"center\">
                            <table class=\"tbform\">
                            <tr>
                            <td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
                            </tr>
                            </table>
                            </div>";
			}	
                    } else {  // exibe as alternativas
                        ?>	
                        <div align="center">
                        <table width="400" class="tbform">
			<tr>
                        <td width="5" >&nbsp;</td>
                        <td width="200" align="left"><b>Alternativa:</b> <?php echo $r['IndexAlternativa'];?></td>
			<td width="170"align="right"><b>Valor:</b><?php echo (($r[5]==1)?'Verdadeiro':'Falso'); ?></td>
			<td width="20"align="right"><form name="EditaAlternativa" method="post">
			<input type="hidden" name="cod_quest" value="<?php echo $cod_quest?>">
			<input type="submit" class="btnEdita" name="EditaAlternativa" alt="Editar Alternativa" 
			value="<?php echo $r['Chave']?>">
			</form>
			</td>
                        <td width="5" >&nbsp;</td> 
                        </tr>
			<tr>
                        <td width="5" >&nbsp;</td>
                        <td align="left" colspan="2"><?php echo $r['Alternativa']; ?></td>
                        <td width="5" >&nbsp;</td> 
                        </tr>
                	</table>
			</div> 
			<?php
                    }
                }//while
            }
	}
	?>
	<!-- botão Nova Alternativa -->
	<div align="center">
	<table width="400" class="tbform">
	<tr>
	<form id="novaAlternativa" name="novaAlternativa" method="post" action="" class="tbform">
	<td align="left"><input type="hidden" name="CodQuestao" value="<?php echo $cod_quest; ?>"><input type="submit" name="novaAlternativa" label="Nova Alternativa" value="Nova Alternativa"></td>
	</form>
	</tr>
	</table>
	</div>
	<?php 
	//echo "exibe aletranativas $cod_quest <br>";
		
    }//exibe_alternativas($cod_quest);
    
    function put_form_editaQuestao($msg, $cod_quest, $Indice, $Quest, $Peso, $tipo, $Min, $Max){
        
        ?>	
	<div align="center">
	<table class="tbform">
	<form id="SalvarUpdateQuestao" name="SalvarUpdateQuestao" method="post" action="" class="tbform">
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Sequ&ecirc;ncia:<br> 
        <input type="text" name="Indice" size="15" value="<?php echo $Indice ?>"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Quest&atilde;o:<br> 
        <textarea rows="3" cols="45" name="Quest"><?php echo $Quest ?></textarea></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Tipo:<br> 
        <?php
            //echo "tipo: $tipo<br>";
            if ($tipo == ''){
                $tipo = "MP";
            }
        ?>
	<input type="radio" name="Tipo" value="MP" <?php echo ($tipo == 'MP') ?  'checked' : '' ?> />M&uacute;ltipla Escolha
	<input type="radio" name="Tipo" value="DS" <?php echo ($tipo == 'DS') ?  'checked' : '' ?> />Discursiva <?php /*echo $tipo */?>
        <input type="radio" name="Tipo" value="SM" <?php echo ($tipo == 'SM') ?  'checked' : '' ?> />Somat&oacute;ria <?php /*echo $tipo */?>
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="10" >&nbsp;</td>
        <td align="left">N&uacute;mero m&iacute;nimo e m&aacute;ximo de palavras:<br>
            <input type="text" size="15" name="Min" value="<?php echo $Min ?>">
            &nbsp;&nbsp;
            <input type="text" size="15" name="Max" value="<?php echo $Max ?>">
        </td>    
        </tr>
        <tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Peso:<br> 
        <input type="text" name="Peso" size="25" value="<?php echo $Peso ?>"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="15" >&nbsp;</td>
        <td align="right">
	<input type="hidden" name="codQuest" value="<?php echo $cod_quest ?>" >
	<input type="submit" name="SalvarUpdateQuestao" value="Salvar"></td>
        <td width="15" >&nbsp;</td> 
        
        </tr>
	</form>	
        </table> 
	<?php 
	if ($msg != '') {
            echo "
            <table class=\"tbform\">
            <tr>
            <td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
            </tr>
            </table>";
        }
	?>
	</div>
	<?php
    }//put_form_editaQuestao		
    
    function put_form_NovaQuestao($msg, $Indice, $Quest, $Peso, $tipo, $cod_curso, $cod_aula, $Min, $Max) {
        if($Indice == '') {
            $sql = pg_query("SELECT COUNT(\"Chave\") FROM aulas_avaliacoes WHERE ((\"CodCurso\" = $cod_curso) AND (\"CodAula\" = $cod_aula))");
            $r = pg_fetch_row($sql);
            $Indice = $r[0]+1;
	}
	?>	
	<div align="center">
	<table class="tbform">
	<form id="novaAula" name="novaAula" method="post" action="" class="tbform">
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Sequ&ecirc;ncia:<br> 
        <input type="text" name="Indice" size="15" value="<?php echo $Indice ?>"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Quest&atilde;o:<br> 
        <textarea rows="3" cols="45" name="Quest"><?php echo $Quest ?></textarea></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Tipo:<br> 
            <input type="radio" name="Tipo" value="MP" <?php echo ($tipo == 'MP') ?  'checked' : '' ?> />M&uacute;ltipla Escolha
	<input type="radio" name="Tipo" value="DS" <?php echo ($tipo == 'DS') ?  'checked' : '' ?> />Discursiva
        <input type="radio" name="Tipo" value="SM" <?php echo ($tipo == 'SM') ?  'checked' : '' ?> />Somat&oacute;ria
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="10" >&nbsp;</td>    
        <td align="left">N&uacute;mero m&iacute;nimo e m&aacute;ximo de palavras:<br>
            <input type="text" size="15" name="Min" value="<?php echo $Min ?>"> 
            &nbsp; &nbsp;
            <input type="text" size="15" name="Max" value="<?php echo $Max ?>">
        </tr>
        <tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Peso:<br> 
        <input type="text" name="Peso" size="25" value="<?php echo $Peso ?>"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="15" >&nbsp;</td>
        <td align="right"><input type="submit" name="SalvarNovaQuestao" value="Salvar"></td>
        <td width="15" >&nbsp;</td> 
        
        </tr>
	</form>	
        </table> 
	<?php 
	if ($msg != '') {
            echo "
            <table class=\"tbform\">
            <tr>
            <td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
            </tr>
            </table>";
	}
	?>
	</div>
	<?php
    }//put_form_NovaQuestao($iduser, $nomeuser, $cod_curso, $cod_aula, $msg);
    
    function form_nova_alternativa($codcurso, $cod_aula, $msg, $cod_quest, $ind, $alt, $val) {
        //atribui o nº da questão 
	if ($ind == '') {
            $sql = pg_query("SELECT COUNT(\"CodAvaliacao\") FROM aulas_avaliacoes_alternativas WHERE \"CodAvaliacao\" = $cod_quest"); 
            $r = pg_fetch_row($sql);
            $ind = $r[0]+1;
	}
	//desenha o formulário 
	?>
	<div align="center">
	<table class="tbform">
	<form id="novaAula" name="novaAlternativa" method="post" action="" class="tbform">
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">N&uacute;mero da Alternativa:<br> 
        <input type="text" name="Indice" size="15" value="<?php echo $ind ?>"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Texto Alternativa:<br> 
        <textarea rows="3" cols="45" name="Quest"><?php echo $alt ?></textarea></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Verdadeiro:  
        <input type="checkbox" name="Valor"<?php echo (($val==1)?"checked=\"checked\"":''); ?> >
	<input type="hidden" name="codquest" value="<?php echo $cod_quest ?>">
	</td>     
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="15" >&nbsp;</td>
        <td align="right"><input type="submit" name="SalvarNovaAlternativa" value="Salvar"></td>
        <td width="15" >&nbsp;</td> 
	</tr>
	</form>	
        </table> 
	</div>			
	<?php 
	if ($msg != '') {
            echo " <div align=\"center\">
            <table class=\"tbform\">
            <tr>
            <td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
            </tr>
            </table>
            </div>";
	}
	echo "<br>";	
    }//form_nova_alternativa($codcurso, $cod_aula, $cod_quest);
  
    function put_Form_NovaAula($msg) {

        ?>	
	<div align="center">
	<table class="tbform">
	<form id="objnovaAula" name="novaAula" method="post" action="" class="tbform">
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Nome da Aula:<br> 
        <input type="text" name="NomeAula" size="45" value=""></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">M&iacute;nimo de acertos em quest&otilde;es de m&uacute;ltipla escolha<br/> 
                        ou <br/>
                        M&iacute;nimo de respostas em quest&otilde;es discursivas:<br/> 
            <input type="text" name="MimMP" size="20" value=""><br/>
            (Esta verifica&ccedil;&atilde;o pode gerar erros no caso de aulas com tipos diferentes combinados)
        </td>     
        <td width="10" >&nbsp;</td> 
        </tr>
	<tr>
        <td width="10" >&nbsp;</td>
        <td align="left">Data Limite (dd/mm/aaaa):<br> 
            <input type="text" name="DataLimite" size="20" value="" OnKeyUp="mascaraData(this, 1);" maxlength="10"></td>     
        <td width="10" >&nbsp;</td> 
        </tr>
        <tr>
        <td width="15" >&nbsp;</td>
        <td align="right"><input type="submit" name="SalvarNovaAula" value="Salvar"></td>
        <td width="15" >&nbsp;</td> 
        
        </tr>
        </form>	
        </table> 
	<?php 
	if ($msg != '') {
            echo "
            <table class=\"tbform\">
            <tr>
            <td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
            </tr>
            </table>";
	}
	?>
	</div>
	<?php
    }//put_Form_NovaAula($iduser, $nomeuser, $cod_curso);
    
    function put_Lista_Alunos($Cod_Curso, $aluno, $msg, $aula, $idquest){
        ?>
	<div align="center">
        <table class="tabTitle">
	<tr>
	<?php
	//exibe o nÂº de alunos cadastrados para o curso selecionado
	$sql = "SELECT COUNT(curso_alunos.\"Chave\"), cursos.\"Nome\" FROM cursos INNER JOIN curso_alunos ON curso_alunos.\"CodCurso\" = cursos.\"Chave\"  WHERE cursos.\"Chave\" = $Cod_Curso GROUP BY cursos.\"Nome\"";
	$res = pg_query($sql);
        $r = pg_fetch_row($res);
	?>
	<td align="left"><b><?php echo "$r[1] -  $r[0] Alunos cadastrados "?> </b></td>
	</tr>
	</table>
	<table class="tablemenuAdm">
	<tr>
	
        <td width="30" align="center" class="tdmenuAdm"><b>Id</b></td>
	<td align="center" class="tdmenuAdm"><b>Aluno</b></td>
	<td width="38" align="center" class="tdmenuAdm"><b>Aulas</b></td>
	<td width="38" align="center"  class="tdmenuAdm"><b>Compl.</b></td>
        <td width="35" align="center"  class="tdmenuAdm"><b>pts.</b></td>
        <td width="20" align="center" class="tdmenuAdm"><b><image src="images/chktit.jpg"> </b></td>
        <td width="38" align="center"  class="tdmenuAdm"><b>Soma</b></td>
        <td width="38" align="center"  class="tdmenuAdm"><b>M&eacute;dia</b></td>
	<td align="center" width="20" class="tdmenuAdm"></td>
	
	</tr>
	</table>
	
	
	<?php
	//preenche 10 Ãºltimas linhas da tabela Cursos
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0; 
	
        //Lista os nomes dos alunos por ordem alafabética, nº de questionários pendentes e a soma de pontos em cada avaliação 
	//total de aulas no curso 
	if ($sqlAulas = pg_query("SELECT COUNT(\"Chave\") FROM aulas WHERE \"CodCurso\" = $Cod_Curso") ) {
            $ra = pg_fetch_array($sqlAulas);
            $totaluas = $ra[0];
        }
        //echo "<br>Lista os nomes dos alunos por ordem alafabética, nº de questionários pendentes e a soma de pontos em cada avaliação";
	//echo "<br>SELECT curso_alunos.Coduser, usuarios.Nome FROM curso_alunos INNER JOIN usuarios ON usuarios.Chave = curso_alunos.Coduser WHERE curso_alunos.CodCurso = $Cod_Curso  ORDER BY usuarios.Nome, LIMIT $inicio, $inicio10";
	if ($res1 = pg_query("SELECT * FROM curso_alunos_aulas($Cod_Curso)")){
               // "SELECT curso_alunos.\"CodUser\", usuarios.\"Nome\", curso_alunos.\"Visto\" FROM curso_alunos INNER JOIN usuarios ON usuarios.\"Chave\" = curso_alunos.\"CodUser\" WHERE curso_alunos.\"CodCurso\" = $Cod_Curso  ORDER BY usuarios.\"Nome\"") ) {
            // LIMIT $inicio, $inicio10
            //echo "<br>".$res;
            ?>
            <table class="tabValores">
            <?php    
            while($r= pg_fetch_array($res1)) {
                //seleciona a imagem para o visto
                if($r['visto']==1){
                    $img = "images/visto.gif";
                }else{
                    $img = "images/NaoVisto.gif";
                }

                //informa a pontuação do aluno
                $sqlpts = pg_query("SELECT * FROM curso_alunos_aulas_notas($Cod_Curso, ".$r['aluno'].")");
		//curso , aluno , nm_aluno , aulas , completadas , ptotal , visto				
                $pts = pg_fetch_row($sqlpts); 
                ?>
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td width="30"><?php echo $r['aluno']?></td>
                    <td  align="left"><?php echo $r['nm_aluno']?></td>
                    <td width="38"><?php echo $r['aulas']?></td>
                    <td width="38"><?php echo $r['completadas']?></td>
                    <td width="35"><?php echo $r['ptotal'] ?></td>    
                    <td width="20" align="center"><image src="<?php echo $img ?>"></td>
                    <td width="38"><?php echo $pts[0] ?></td>    
                    <td width="38"><?php echo round(($pts[0]/$r['ptotal'] * 100),2)."%"?> </td>  
                    <form name="F_ChecaAlunos" <?php echo $r['aluno']?> method="post" onsubmit="gettopo();">    
                    <td width="20">
                        <input type="hidden" name="CodCurso" value="$Cod_Curso">
                        <input type="hidden" name="VerDesempenhoAluno" value="<?php echo $r['aluno'] ?>">
                        <input type="submit" class="btnEdita" name="btn" title="Avalia&ccedil;&atilde;o Aluno" alt="Avalia&ccedil;&atilde;o Aluno" value="" >
                    </td>
                    </form>
                </tr>
                <?php
                if ($aluno == $r['aluno'] ) {
                    ?>
                    <tr>
                        <!--<td colspan="9>">-->
                    <?php
                    $this->lista_questionarios_aula($Cod_Curso, $aluno, $aula, $idquest, $msg);
                    ?>  
                         <!--</td> -->
                    </tr>
                    <!--reinicia a tabela delarada em 1148 -->
                    <table class="tabValores">
                    <?php
                }
                    
            }
            ?>
            </table>
            
            <?php
	}
						
        //Exportaçao de relatório
	?>
        <table class="tbForm" width="540">
            <tr>
                <td colspan="2" align="center">
                    <b><font color="#366">Exportar Relatório</font><b>
                </td>
            </tr>
            <tr>
                <td>
                    Em breve
                </td>
            </tr>
            <!--
            <tr>
                <form name="ExportaRel" method="post" onsubmit="gettopo();">
                    <td>
                        <input type="radio" name="tipo" value="csv" checked>Planilha (valores separados por v&iacute;rgula ".csv")
                        <br/>
                        <input type="radio" name="tipo" value="rtf">Texto (arquivo de texto tabulado ".rtf")                            
                    </td>
                    <td valign="center">
                        <input type="submit" name="gerarel" value="Gerar Arquivo" title="Gerar Arquivo">
                    </td>
                </form>
            </tr>
           -->
        </table>
	
	<!-- Botões novo aluno e mais 10 -->
	</table>
	<table class="tabTitle">
	<tr>
	<td align="left">
	<form id="NovoAluno" name="NovoAluno" method="post" action="">
	<!--
	<input name="NovoAluno" type="submit" id="NovoAluno" value="Adicionar Aluno" class="botaoAdm" />
	-->
	</form>
	</td>
	<td align="right">
	<form id="MoreAlunos" name="MoreAlunos" method="post" action="">
	<!--
	<input name="MoreAlunos" type="submit" id="MoreAlunos" value="Mais 10" class="botaoAdm" />
	-->
	</form>
	</td>
	</tr>	
	</table>
	<table class="tblinha">
	<tr>
	<td>&nbsp;</td>
	</tr>
	</table>
        </div>
	<?php	
	if ($msg != '') {
            if ($msg != '') {
                echo " <div align=\"center\">
		<table class=\"tbform\">
		<tr>
		<td align=\"center\"><font color=\"#FF0000\">".$msg."</font></td>
		</tr>
		</table>
		</div>";
            }
	}
    }//put_Lista_Alunos($Cod_Curso, 0);
    
    public function lista_questionarios_aula($Cod_Curso, $aluno, $aula, $idquest, $msg){
        $d1 = "#F0E68C";
        $d2 = "#eae6bf";
        $d=0;
        ?>
        <!--<tr><td colspan="10" width="540"> 
            <!--
            <table class="tablemenuAdm" style="padding:0px 40px 5px 20px;" >
            <tr>
            <td width="30" align="center" class="tdmenuAdm">Id</td>
            <td align="center" class="tdmenuAdm">Aula</td>
            <td width="30" align="center" class="tdmenuAdm">Pts.</td>
            <td width="30" align="center" class="tdmenuAdm">Nota</td>
            <td align="center" width="20" class="tdmenuAdm"></td>
            </tr>
            </table>
            -->
            <table  class="tabValores" style="width: 540px;"> 
            <?php
            if($res2 = pg_query("SELECT * FROM aluno_aulas_notas($Cod_Curso, $aluno)")){
                while ($r2 = pg_fetch_array($res2)){
                ?>
                    <tr bgcolor="<?php echo(($d++&1)?$d1:$d2)?>">
                        <td width="30" align="center" ><?php echo $r2['aula']?></td>
                        <td><?php echo $r2['nome']?></td>
                        <td width="30" align="center"><?php echo $r2['pts']?></td>
                        <td width="30" align="center"><?php echo $r2['nota']?></td>
                        <form name="F_ChecaAaula" method="post" onsubmit="gettopo();">
                        <td align="center" width="20">
                            <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>">
                            <input type="hidden" name="Aluno" value="<?php echo $aluno ?>">
                            <input type="hidden" name="Aula" value="<?php echo $r2['aula'] ?>">
                            <input type="submit" class="btnEdita" name="ChecaAula" title="Avalia&ccedil;&atilde;o Aula" value="<?php echo $aluno ?>">
                        </td>
                        </form> 
                        <td width="80" bgcolor="white"></td>
                    </tr>
                    <?php
                    if($aula == $r2['aula'] ){
                        ?>
                        <tr>
                           <!-- <td colspan="5"> -->
                        <?php
                        $this->questionario_aula($Cod_Curso, $aluno, $aula, $idquest, $msg );
                        ?>
                          <!--  </td> -->
                        </tr>
                        <!-- reinicia a tabela em 1261 -->
                        <table  class="tabValores" style="width: 540px;">
                        <?php
                    }
                }
            }
            ?>
            </table>
            <?php
    }
 
    
    public function questionario_aula($Cod_Curso, $aluno, $aula, $idquest, $msg ){
        $res3 = pg_query("SELECT * FROM aluno_aula($Cod_Curso, $aula, $aluno)");
        if(pg_num_rows($res3)== 0 ){
        ?>
            <!--<tr> <td colspan="7">  -->
                <table class="tbform" style="width: 540px;">
                    <tr>
                        <td width="5"></td>
                        <td align="center" style="color: red" ><b>Avalia&ccedil;&atilde;o Pendente.</b></td>
                        <td width="5"></td>
                    </tr>                                                
                </table>
            <!--</tr> -->
        <?php
        } else {
            //verifica se o questionário está marcado para refazer
            //echo "<br>SELECT \"Refazer\" FROM public.aulas_avaliacoes_obs WHERE \"CodCurso\" = $Cod_Curso AND \"CodAula\" = $aula AND \"CodAluno\" = $aluno AND \"Refazer\"= True";
            $res4 = pg_query ("SELECT \"Refazer\" FROM public.aulas_avaliacoes_obs WHERE \"CodCurso\" = $Cod_Curso AND \"CodAula\" = $aula AND \"CodAluno\" = $aluno AND \"Refazer\"= True");
            if(pg_num_rows($res4) >= 1){
             $refaz = true;   
            } else {
                $refaz = false;
            }
            
            while ($r3 = pg_fetch_array($res3)) {
                //var_dump($r3);
                ?>
                    <table class="tbform" style="width: 540px; <?php echo ($refaz ? "background-color: #fdbcb9;": "") ?>">
                        <?php
                        if($refaz){
                        ?>
                        <tr>
                            <td width="5"></td>
                            <td colspan="2" align="center"><b><font color="red">Maracado para Refazer</font></b> </td>
                            <td width="5"></td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td width="5"></td>
                            <td align="left"><b>Quest&atilde;o: <?php echo $r3['indexquestao'] ?></b> </td>
                            <td align="right"><b>Peso: <?php echo $r3['peso'] ?></b></td>
                            <td width="5"></td>
                        </tr>
                        <tr>
                            <td width="5"></td>
                            <td colspan="2"><?php echo nl2br($r3['questao'])?></td>
                            <td width="5"></td>
                        </tr>
                    </table>
                    <table class="tbform" style="width: 540px; <?php echo ($refaz ? "background-color: #fdbcb9;": "") ?>">
                        <tr>
                            <td width="5"></td> 
                            <td align="left" colspan="2"><b> 
                            <?php echo ($r3['tipo']=='DS' ? "Resposta:" : "Alternativa: ")?></b></td>
                            <td width="5"></td>
                        </tr>
                        <tr>
                            <td width="5"></td>
                            <td align="left" colspan="2"><?php echo nl2br($r3['resposta'])?></td>
                            <td width="5"></td>
                         </tr>
                         <tr>
                            <td width="5"></td>
                            <?php
                            if ($r3['tipo']=='DS'){
                                if (($r3['nota'] == 0) || ($r3['id']== $idquest)) {
                            ?>
                                    <form name="lancanota" method="post" onsubmit="gettopo();">
                                    <td colspan="2" align="right"><b>Nota:</b> 
                                    <input type="text" name="nota" value="<?php echo $r3['nota'] ?>" />
                                    <input type="hidden" name="id" value="<?php echo $r3['id']?>" >
                                    <input type="hidden" name="peso" value="<?php echo $r3['peso']?>">
                                    <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>">
                                    <input type="hidden" name="Aluno" value="<?php echo $aluno ?>">
                                    <input type="hidden" name="Aula" value="<?php echo $aula ?>">
                                    <input type="submit" name="lancanota" value="Grava" title="Grava">
                                    </td>
                                    </form>
                            <?php
                                }else{
                            ?>
                                    <form name="Editanota" method="post" onsubmit="gettopo()">
                                    <td colspan="2" align="right"><b>Nota:<font color="#0000FF"><?php echo " ".$r3['nota']." " ?></font></b> 
                                    <input type="hidden" name="id" value="<?php echo $r3['id']?>" >
                                    <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>">
                                    <input type="hidden" name="Aluno" value="<?php echo $aluno ?>">
                                    <input type="hidden" name="Aula" value="<?php echo $aula ?>">
                                    <input class="btnEdita" name="editanota" type="submit">
                                    </td>
                                    </form>
                            <?php            
                                }
                            }else{
                                ?>
                                <td align="left" colspan="2"><b><?php echo ($r3['alternativa']== 1 ? "Verdadeiro: ".$r3['peso']." pts." : "Falso 0 pts.") ?> </td>
                                <?php
                            }
                            ?>
                            <td width="5"></td>
                         </tr>
                         <?php
                         if($msg != ''){
                             ?>
                            <tr>
                                <td width="5"></td>
                                <td colspan="2" align="left">
                                <b><font color="red"><?php echo $msg ?></font></b>
                                </td>
                                <td width="5"></td>
                            </tr>
                         <?php
                         }
                         ?>
                   <?php
             }
             //botões Comentar resposta do aluno e
             //Marcar para aluno refazer avaliação
             //verifica se há observações para o questionário selecionado

             //echo "<br>SELECT * FROM avaliacao_aluno_obs($Cod_Curso, $aula, $aluno)<br>";
             $sqlobs = pg_query("SELECT * FROM avaliacao_aluno_obs($Cod_Curso, $aula, $aluno)");
             $obs = pg_fetch_array($sqlobs);
             //var_dump($obs);
             ?>
            <table class="tbForm" style="width: 540px; border: none">
                
                <tr>            
                    <td width="10"></td>
                    <td>
                        <table class="tbForm" style="border-color: #366;">
                        <?php
                        if ($refaz){
                        ?>
                            <tr>
                            <form name="desarcarpararefazer" method="post" onsubmit="gettopo();">
                                <td colspan="2" align="center">
                                    <font color="red"><b>Maracado para Refazer .</b> </font>                                    
                                        <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>">
                                        <input type="hidden" name="Aluno" value="<?php echo $aluno ?>">
                                        <input type="hidden" name="Aula" value="<?php echo $aula ?>">
                                        <input type="submit" class="btnRefaz" name="desfazer"  title="Desmarcar para refazer" value=""/>
                                </td>
                            </form>
                            </tr>
                        <?php
                        }//if ($refazer)
                        ?>                        
                            <tr>
                                <td width="500" align="left"><b><font color="#366"> Observa&ccedil;&otilde;es do monitor:</font></b> </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="500" align="center">
                                    <form name="comentaravaliacao" method="post" onsubmit="gettopo();">
                                    <textarea rows="6" cols="57" name="obs"><?php echo $obs['obs']?></textarea>
                                    <input type="hidden" name="Chaveobs" value="<?php echo $obs['chave'] ?>"/>
                                    <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>"/>
                                    <input type="hidden" name="Aluno" value="<?php echo $aluno ?>"/>
                                    <input type="hidden" name="Aula" value="<?php echo $aula ?>"/>
                                    <div align="right">                                         
                                    <input type="submit" name="GravaObservacao" value="Grava" title="Grava" />
                                    </div>
                                    </form>
                                </td>
                                <td valign="top">
                                    <?php
                                    if (!$refaz){
                                    ?>
                                    <form name="Marcarpararefazer" method="post" onsubmit="gettopo();"/>
                                        <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>"/>
                                        <input type="hidden" name="Aluno" value="<?php echo $aluno ?>"/>
                                        <input type="hidden" name="Aula" value="<?php echo $aula ?>"/>
                                        <input type="submit" class="btnRefaz" name="refazer"  title="Marcar para refazer" value="">
                                        <!--
                                        <input type="submit" class="btnEdita" name="EditaCurso" title="Editar Curso" value="<?php echo $r['Chave'] ?>">
                                        -->
                                    </form>
                                    <?php
                                    }
                                    ?>                                        
                                </td>
                            </tr>
                        </table>
                       
                    </td>    
                    <td width="10"></td>
                    </td>
                </tr>
            </table>
            <table class="tblinha" name="tb">
                <tr>
                    <td></td>
                </tr>
            </table>
            <?php
        }
    }
        

    function put_Quest_Aluno($codCurso, $aluno, $msg, $edita){
        //exibe o nome do aluno
	$sql = pg_query("SELECT \"Nome\" FROM usuarios WHERE \"Chave\" = $aluno");
	$n = pg_fetch_array($sql);
	echo "<div align=\"center\">
	<table width=\"400\" class=\"tblinha\">
	<tr>
	<td><b>Aluno: $n[0]</b></td>
	</tr>
	</table>
	</div>";	
	// aula
	$sql = pg_query("SELECT * FROM aulas WHERE \"CodCurso\" = $codCurso ORDER BY \"Chave\"");
	while($r = pg_fetch_array($sql)){
            echo "<br>";
            echo "<div align=\"center\">
            <table width=\"400\" class=\"tblinha\">
            <tr>
            <td>Aula: $r[0] - $r[2] </td>
            </tr>
            </table>
            </div>";	
            //lista o questionário sequido da resposta selecionada pelo anulo
            $sqlQuest = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodCurso\" = $codCurso AND \"CodAula\" = 
                    $r[0] ORDER BY \"IndexQuestao\"");
            while($q = pg_fetch_array($sqlQuest)){
                echo "<div align=\"center\">
                <table width=\"400\" class=\"tbform\">
		<tr>
                <td width=\"5\" >&nbsp;</td>
                <td width=\"200\" align=\"left\"><b> Quest&atilde;o:</b> $q[3]</td>
  		<td width=\"190\"align=\"right\"><b>Peso:</b>$q[5]</td>
                <td width=\"5\" >&nbsp;</td> 
                </tr>
		<tr>
                <td width=\"5\" >&nbsp;</td>
                <td align=\"left\" colspan=\"2\">$q[4]</td>
                <td width=\"5\" >&nbsp;</td> 
                </tr>
		
                </table> 
		</div>";
		//exibe a respeosta do aluno se ela existir
		$sqlAlt = pg_query("SELECT aulas_avaliacoes_alternativas.\"IndexAlternativa\", 
                    aulas_avaliacoes_alternativas.\"Alternativa\", aulas_avaliacoes_alunos_respostas.\"CodCurso\", 
                    aulas_avaliacoes_alunos_respostas.\"CodAula\", aulas_avaliacoes_alunos_respostas.\"CodAvaliacao\", 
                    aulas_avaliacoes_alunos_respostas.\"CodAlternativa\", aulas_avaliacoes_alunos_respostas.\"Discursiva\", 
                    aulas_avaliacoes_alunos_respostas.\"Nota\", aulas_avaliacoes_alternativas.\"Resposta\", 
                    aulas_avaliacoes_alunos_respostas.\"CodAluno\", aulas_avaliacoes_alunos_respostas.\"Chave\" 
                    FROM aulas_avaliacoes_alunos_respostas LEFT JOIN aulas_avaliacoes_alternativas ON 
                    aulas_avaliacoes_alunos_respostas.\"CodAlternativa\" = aulas_avaliacoes_alternativas.\"Chave\" 
                    WHERE (((aulas_avaliacoes_alunos_respostas.\"CodCurso\")=$codCurso) AND 
                    ((aulas_avaliacoes_alunos_respostas.\"CodAula\")=$q[2]) AND 
                    ((aulas_avaliacoes_alunos_respostas.\"CodAvaliacao\")=$q[0]) AND 
                    ((aulas_avaliacoes_alunos_respostas.\"CodAluno\")=$aluno))");
		//echo "<br>SELECT aulas_avaliacoes_alternativas.IndexAlternativa, aulas_avaliacoes_alternativas.Alternativa, aulas_avaliacoes_alunos_respostas.CodCurso, aulas_avaliacoes_alunos_respostas.CodAula, aulas_avaliacoes_alunos_respostas.CodAvaliacao, aulas_avaliacoes_alunos_respostas.CodAlternativa, aulas_avaliacoes_alunos_respostas.Discursiva, aulas_avaliacoes_alunos_respostas.Nota, aulas_avaliacoes_alternativas.Resposta, aulas_avaliacoes_alunos_respostas.CodAluno FROM aulas_avaliacoes_alunos_respostas INNER JOIN aulas_avaliacoes_alternativas ON aulas_avaliacoes_alunos_respostas.CodAlternativa = aulas_avaliacoes_alternativas.Chave WHERE (((aulas_avaliacoes_alunos_respostas.CodCurso)=$codCurso) AND ((aulas_avaliacoes_alunos_respostas.CodAula)=$q[2]) AND ((aulas_avaliacoes_alunos_respostas.CodAvaliacao)=$q[0]) AND ((aulas_avaliacoes_alunos_respostas.CodAluno)=$aluno))";
		if (pg_num_rows($sqlAlt) >= 1) {
                    $a = pg_fetch_array($sqlAlt);
                    //múltipla escolha			
                    if ($a['CodAlternativa'] != -1) {	
                        //verifica o tipo
                        //echo "SELECT Tipo FROM aulas_avaliacoes WHERE Chave = ".$a['CodAvaliacao']."<br>";
                        $qtipo = pg_query("SELECT \"Tipo\" FROM aulas_avaliacoes WHERE \"Chave\" = ".$a['CodAvaliacao']);
                        $tipo = pg_fetch_array($qtipo);
                        if($tipo['Tipo'] == '') $tipo['Tipo'] = 'MP';
                         //echo $tipo['Tipo']."<br>";
                         switch ($tipo['Tipo'])   {
                             case 'MP' :
                                    
                                echo "<div align=\"center\">
                                <table width=\"400\" class=\"tbform\">
                                <tr>
                                <td width=\"5\" >&nbsp;</td>
                                <td width=\"200\" align=\"left\"><b> Alternativa:</b> $a[0]</td>
                                <td width=\"190\"align=\"right\">&nbsp;</td>
                                <td width=\"5\" >&nbsp;</td> 
                                </tr>
                                <tr>
                                <td width=\"5\" >&nbsp;</td>
                                <td align=\"left\" colspan=\"2\">$a[1]</td>
                                <td width=\"5\" >&nbsp;</td> 
                                </tr>
                                <tr>
                                <td width=\"5\" >&nbsp;</td>";
                                if ($a['Resposta']== 1){
                                    echo "<td align=\"left\" colspan=\"2\"><b>Verdadeiro - ".$q[5]." Pts.</b></td>";									
                                } else {
                                    echo "<td align=\"left\" colspan=\"2\"><b>Falso - 0 Pts.</b></td>";
                                }									
                                echo "<td width=\"5\" >&nbsp;</td> 
                                </tr>
                                </table> 
                                <table class=\"tblinha\">
                                <tr> <td> </td> </tr>
                                </table>
                                </div>"	;
                                break;
                             case 'SM' :
                                 //inicia a tabela
                                 echo "<div align=\"center\">
                                <table width=\"400\" class=\"tbform\">";
                                 //localiza as alternativas corretas
                                 $SMalt = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE 
                                     \"CodAvaliacao\" = ".$q['Chave']." AND \"Resposta\" = 1");
                                 $SMcorr = array();
                                 while ($SMa = pg_fetch_array($SMalt)) {
                                  $SMcorr[] = $SMa[0];   
                                 }
                                 //exibe as alternativas marcadas pelo aluno
                                 $qsm = pg_query("SELECT \"CodAlternativa\" FROM aulas_avaliacoes_alunos_respostas 
                                        WHERE \"CodAvaliacao\" = ".$q['Chave']." AND \"CodAluno\" = $aluno");
                                 $parc = 0;
                                 $x = TRUE;
                                 while ($sm = pg_fetch_array($qsm)){
                                    $show = pg_query("SELECT \"IndexAlternativa\", \"Resposta\" FROM 
                                        aulas_avaliacoes_alternativas WHERE \"Chave\" = ".$sm['CodAlternativa']);
                                    $s = pg_fetch_array($show);
                                    if ($s['Resposta'] == 1){
                                        $parc++;
                                        //exibe e marca a alternativa correta
                                        echo "<tr>
                                            <td width=\"5\" >&nbsp;</td>
                                            <td>Alternativa ".$s['IndexAlternativa']." <b>CORRETA</b></td>
                                            </tr>";    
                                    }else{
                                        $x = False;
                                        //exibe e marca a alternativa correta
                                        echo "<tr>
                                            <td width=\"5\" >&nbsp;</td>
                                            <td>Alternativa ".$s['IndexAlternativa']." <b>INCORRETA</b></td>
                                            </tr>";    
                                    }
                                 }
                                 if(!$x){
                                     echo "<tr>
                                            <td width=\"5\" >&nbsp;</td>
                                            <td><b>INCORRETA - 0</b> pts.</td>
                                            </tr>";  
                                 }else if ($parc == count($SMcorr)){
                                     //CORRETA
                                      echo  "<tr>
                                            <td width=\"5\" >&nbsp;</td>
                                            <td><b>CORRETA - ".$q['Peso']." pts.</b></td>
                                            </tr>";                                       
                                 } else {
                                     //parcial
                                      echo  "<tr>
                                            <td width=\"5\" >&nbsp;</td>
                                            <td><b>PARCIAL - ".$q['Peso']/2 ." pts.</b></td>
                                            </tr>";                                       
                                 }
                                 echo "</table> 
                                <table class=\"tblinha\">
                                <tr> <td> </td> </tr>
                                </table>
                                </div>"	;

                                 //exibe a nota do aluno
                                 break;
                         }//switch        
                    } else {//Alternativa = -1, Discursiva
                            $Discur = preg_replace("/(\\r)?\\n/i", "<br/>", $a['Discursiva']);
                            $chave = $a['Chave'];
                            $nota = $a['Nota'];
                            $peso =  $q['Peso'];
			if ($nota == NULL || $nota == 0 || $edita == $chave ) {
                            ?>						
                            <div align="center">
                            <table width="400" class="tbform">
                            <tr>
        		    <td width="5" >&nbsp;</td>
                            <td width="380" align="left">  <?php echo $Discur ?> </td>
                            <td width="5" >&nbsp;</td> 
                            </tr>
                            <tr>
                            <td width="5" >&nbsp;</td>
                            <td width="380" align="right">
                            <form id="Nota" name="Nota" method="post" onsubmit="gettopo();">
                            Nota:
                            <input type="text" name="nota" value="<?php echo $nota ?>" />
                            <input type="hidden" name="codresp" value=" <?php echo $chave ?>" />
                            <input type="hidden" name="CodCurso" value="<?php echo $codCurso ?>"/>
                            <input type="hidden" name="Aluno" value="<?php echo $aluno ?>"/>
                            <input type="hidden" name="peso" value="<?php echo $peso ?>"  />
                            <input type="submit" name="GravaNota" value="Grava" />
                            </form>
                            <?php 
                            if($edita == $chave) {
                                ?>
				<font color="#FF0000"> <?php echo ($a['Chave'] == $chave)? "<br> $msg" : '' ?></font>
				<?php
                            }
                            ?>
                            </td>
                            <td width="5" >&nbsp;</td> 
		            </tr>
        		    </table> 
                            <table class="tblinha">
                            <tr> <td> </td> </tr>
                            </table>
                            </div>	
                            <?php
                        } else { // nota lançada Exibe botão editar
                            ?>
                            <div align="center">
                            <table width="400" class="tbform">
                            <tr>
        		    <td width="5" >&nbsp;</td>
                            <td width="380" align="left">  <?php echo $Discur ?> </td>
                            <td width="5" >&nbsp;</td> 
		            </tr>
                            <tr>
                            <td width="5" >&nbsp;</td>
                            <td width="380" align="right">
                            <form id="EditaNota" name="EditaNota" method="post" onsubmit="gettopo();" >
                            Nota: <font color="#0000FF"><b><?php echo $nota ?></b></font>
                            <input type="hidden" name="CodCurso" value="<?php echo $codCurso ?>"/>
                            <input type="hidden" name="Aluno" value="<?php echo $aluno ?>"/>
                            <input type="hidden" name="codresp" value="<?php echo $chave ?>"/>
                            <input type="hidden" name="peso" value="<?php echo $peso ?>"  />
                            <input type="submit" class="btnEdita" name="EditaNota" />
                            </form>
                            </td>
                            <td width="5" >&nbsp;</td> 
		            </tr>
        		    </table> 
                            <table class="tblinha">
                            <tr> <td> </td> </tr>
                            </table>
                            </div>	
                            <?php 
                        }
                    }
		} else { // retornou um  exibe o sem avaliação.
                    ?>
                    <div align="center">
                    <table width="400" class="tbform">
                    <tr>
                    <td width="5" >&nbsp;</td>
                    <td width="380" align="left"><font color="#FF0000"> <b> Avalia&ccedil;&atilde;o Pendente </b> </font>   </td>
                    <td width="5" >&nbsp;</td> 
		    </tr>
                    </table>
                    </table> 
                    <table class="tblinha">
                    <tr> <td> </td> </tr>
                    </table>
                    </div>
                    <?php
                }
            }//while sqlquest
	}//while sql
	?>
	<div align="center">
	<table class="tblinha">
	<form id="exporta" name="exporta" method="post" onsubmit="gettopo();" >
        <tr> 
	<td align="right">
	<!--
        <input type="hidden" name="CodCurso" value="<?php //echo $codCurso ?>"  />
	<input type="hidden" name="Aluno" value="<?php //echo $aluno ?>"  />
	
        <input name="Exportar" type="submit" id="exportar" value="Exportar" class="botaobco"/>
	-->
        <?php
        echo $this->exporta($codCurso, $aluno);
        ?>
        </td> 
	</tr>
	</form>
	</table>
	</div>
	<?php
    }//put_Quest_Aluno
    
    function exporta($codCurso, $aluno) {
        
        //marca aluno como corrigido
        //caso não existam questões discurssivas DS
        $sqlv = pg_query("SELECT COUNT(\"Tipo\") FROM aulas_avaliacoes WHERE \"Tipo\" = 'DS' AND \"CodCurso\"=$codCurso");
        $v = pg_fetch_array($sqlv);
        if ($v[0]==0){
            $sql = pg_query("UPDATE curso_alunos SET \"Visto\" = 1 WHERE \"CodCurso\"=$codCurso AND \"CodUser\"=$aluno");
        }

        $sep = ';';
	$quebra = "\n";
	$aspas = '"';

    	
        //nome do aluno
	$sql = pg_query("SELECT \"Nome\" FROM usuarios WHERE \"Chave\" = $aluno");
	$a = pg_fetch_array($sql);
	//nome do curso
	//echo "SELECT Nome FROM cursos WHERE Chave = $codCurso"."<br>";
        $sqlc = pg_query("SELECT \"Nome\" FROM cursos WHERE \"Chave\" = $codCurso");
	$c =  pg_fetch_array($sqlc);
		
	$data = date('YmdHisu');
        $filename = "Relatorios/$c[0]_$a[0]".$data.".csv";
        
        //inicializa os dados
        $out  = "";
        $out = $aspas."CURSO: ".$c[0]." ALUNO: ".$a[0].$aspas."\n";
        
        $ptscurso = 0;
		
	//aulas
	// aula
	$sqlau = pg_query("SELECT * FROM aulas WHERE \"CodCurso\" = $codCurso ORDER BY \"Chave\"");
        while($au = pg_fetch_array($sqlau)){
            $ptsAula =0;
            //insere o nome da aula
            $out .= $quebra.$au['Nome'].$quebra;
            //questionários
            //Questão
            $sqlQuest = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodCurso\" = $codCurso AND \"CodAula\" = $au[0] 
                ORDER BY \"IndexQuestao\"");
            while($q = pg_fetch_array($sqlQuest)){
                $out .= $q['IndexQuestao'].$sep.$aspas.$q['Questao'].$aspas.$quebra;
                //Resposta
                $sqlAlt = pg_query("SELECT aulas_avaliacoes_alternativas.\"IndexAlternativa\", 
                        aulas_avaliacoes_alternativas.\"Alternativa\", aulas_avaliacoes_alunos_respostas.\"CodCurso\", 
                        aulas_avaliacoes_alunos_respostas.\"CodAula\", aulas_avaliacoes_alunos_respostas.\"CodAvaliacao\", 
                        aulas_avaliacoes_alunos_respostas.\"CodAlternativa\", aulas_avaliacoes_alunos_respostas.\"Discursiva\",
                        aulas_avaliacoes_alunos_respostas.\"Nota\", aulas_avaliacoes_alternativas.\"Resposta\", 
                        aulas_avaliacoes_alunos_respostas.\"CodAluno\", aulas_avaliacoes_alunos_respostas.\"Chave\"
                        FROM aulas_avaliacoes_alunos_respostas LEFT JOIN aulas_avaliacoes_alternativas ON 
                        aulas_avaliacoes_alunos_respostas.\"CodAlternativa\" = aulas_avaliacoes_alternativas.\"Chave\" 
                        WHERE (((aulas_avaliacoes_alunos_respostas.\"CodCurso\")=$codCurso) AND 
                        ((aulas_avaliacoes_alunos_respostas.\"CodAula\")=$q[2]) AND 
                        ((aulas_avaliacoes_alunos_respostas.\"CodAvaliacao\")=$q[0]) AND 
                        ((aulas_avaliacoes_alunos_respostas.\"CodAluno\")=$aluno))");
                if (pg_num_rows($sqlAlt) >= 1) {
                    $r = pg_fetch_array($sqlAlt);
                    	//múltipla escolha
                    if ($r['CodAlternativa'] != -1) {
                        if ($r['Resposta'] == 1 ) { //Verdadeira
                            $out .= $aspas."Alretnativa: ".$r['IndexAlternativa'].$aspas.$sep.$aspas.$r['Alternativa'].$aspas.$sep.$q['Peso'].$quebra;
                            //acumula a nota
                            $ptsAula = $ptsAula + $q['Peso'];
                        } else { //falsa
                            $out .= $aspas."Alretnativa: ".$r['IndexAlternativa'].$aspas.$sep.$aspas.$r['Alternativa'].$aspas.$sep.'0'.$quebra;
                        }
                         //Discursiva
                    } else {
                        $out .= $aspas."R:".$aspas.$sep.$aspas.$r['Discursiva'].$aspas.$sep.$r['Nota'].$quebra;
                        $ptsAula = $ptsAula + $r['Nota'];
                    }
                } //if 	num rows = 1
            }// while questão
            //exibe a pontuação acumulada para a aula
            $out .= $sep."Pontuacao: ".$sep.$ptsAula.$quebra;
            //acumula pontuação do curso
            $ptscurso = $ptscurso + $ptsAula;
            $ptsAula = 0;		
        }//while aulas
	//exibe a pontuação total do curso
	$out .= $quebra.$sep."Pontuacao Total: ".$sep.$ptscurso.$quebra;
        
        //verifica permissão de escrita no diretório
        if(fwrite($file=  fopen($filename,'w+'), $out)){
            //echo "Arquivo Gravado com sucesso!<br>";
            return  "<a href=\"$filename\"> Clique aqui para baixar o relat&oacuterio </a><br>";
        } else {
            return "Não foi possivel gravar o arquivo.\n Tente novamente ou entre em contato com a equipe e suporte.";
        }
        
        
        
        
        /*
        $sep = ';';
	$quebra = "\n";
	$aspas = '"';
	
        $out = ''; //texto
	//nome do aluno
	$sql = mysql_query("SELECT Nome FROM usuarios WHERE Chave = $aluno");
	$a = mysql_fetch_array($sql);
	//nome do curso
	//echo "SELECT Nome FROM cursos WHERE Chave = $codCurso"."<br>";
        $sqlc = mysql_query("SELECT Nome FROM cursos WHERE Chave = $codCurso");
	$c =  mysql_fetch_array($sqlc);
		
	$filename = "$c[0]_$a[0].csv";
	
        
        echo $filename."<br>";
        
			//Curso				//aluno
	$out .= $aspas.$c['Nome'].$aspas.$sep.$aspas.$a['Nome'].$aspas.$quebra;

	echo $out."<br>";
        $ptscurso = 0;
		
	//aulas
	// aula
	$sqlau = mysql_query("SELECT * FROM aulas WHERE CodCurso = $codCurso ORDER BY Chave");
        while($au = mysql_fetch_array($sqlau)){
            $ptsAula =0;
            //insere o nome da aula
            $out .= $quebra.$au['Nome'].$quebra;
            //questionários
            //Questão
            $sqlQuest = mysql_query("SELECT * FROM aulas_avaliacoes WHERE CodCurso = $codCurso AND CodAula = $au[0] 
                ORDER BY IndexQuestao");
            while($q = mysql_fetch_array($sqlQuest)){
                $out .= $q['IndexQuestao'].$sep.$aspas.$q['Questao'].$aspas.$quebra;
                //Resposta
                $sqlAlt = mysql_query("SELECT aulas_avaliacoes_alternativas.IndexAlternativa, 
                        aulas_avaliacoes_alternativas.Alternativa, aulas_avaliacoes_alunos_respostas.CodCurso, 
                        aulas_avaliacoes_alunos_respostas.CodAula, aulas_avaliacoes_alunos_respostas.CodAvaliacao, 
                        aulas_avaliacoes_alunos_respostas.CodAlternativa, aulas_avaliacoes_alunos_respostas.Discursiva,
                        aulas_avaliacoes_alunos_respostas.Nota, aulas_avaliacoes_alternativas.Resposta, 
                        aulas_avaliacoes_alunos_respostas.CodAluno, aulas_avaliacoes_alunos_respostas.Chave
                        FROM aulas_avaliacoes_alunos_respostas LEFT JOIN aulas_avaliacoes_alternativas ON 
                        aulas_avaliacoes_alunos_respostas.CodAlternativa = aulas_avaliacoes_alternativas.Chave 
                        WHERE (((aulas_avaliacoes_alunos_respostas.CodCurso)=$codCurso) AND 
                        ((aulas_avaliacoes_alunos_respostas.CodAula)=$q[2]) AND 
                        ((aulas_avaliacoes_alunos_respostas.CodAvaliacao)=$q[0]) AND 
                        ((aulas_avaliacoes_alunos_respostas.CodAluno)=$aluno))");
                if (mysql_num_rows($sqlAlt) >= 1) {
                    $r = mysql_fetch_array($sqlAlt);
                    	//múltipla escolha
                    if ($r['CodAlternativa'] != -1) {
                        if ($r['Resposta'] == 1 ) { //Verdadeira
                            $out .= $aspas."Alretnativa: ".$r['IndexAlternativa'].$aspas.$sep.$aspas.$r['Alternativa'].$aspas.$sep.$q['Peso'].$quebra;
                            //acumula a nota
                            $ptsAula = $ptsAula + $q['Peso'];
                        } else { //falsa
                            $out .= $aspas."Alretnativa: ".$r['IndexAlternativa'].$aspas.$sep.$aspas.$r['Alternativa'].$aspas.$sep.'0'.$quebra;
                        }
                         //Discursiva
                    } else {
                        $out .= $aspas."R:".$aspas.$sep.$aspas.$r['Discursiva'].$aspas.$sep.$r['Nota'].$quebra;
                        $ptsAula = $ptsAula + $r['Nota'];
                    }
                } //if 	num rows = 1
            }// while questão
            //exibe a pontuação acumulada para a aula
            $out .= $sep."Pontuacao: ".$sep.$ptsAula.$quebra;
            //acumula pontuação do curso
            $ptscurso = $ptscurso + $ptsAula;
            $ptsAula = 0;		
        }//while aulas
	//exibe a pontuação total do curso
	$out .= $quebra.$sep."Pontuacao Total: ".$sep.$ptscurso.$quebra;
        //gera e exporta o relatório
	//header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	
	//header("Content-type: text/csv"); 
	
	//echo $out."<br>";
        //desmarcar esta linha na sequencia
        //limpa o buffer de saída
        ob_clean();
        //$filename .= ".CSV";
	header("Content-type: text/x-csv"); 
        header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=$filename"); 
        echo $out;
	exit;	
         * 
         */
    }
    
}
?>
