
<?php
class Testes {

    public function Capa($userid, $nomeuser, $curso) {
        $db = new avdb;
        $db->Conn_av();
        $this->put_menu($userid, $nomeuser, 1);
	$this->put_cursos($userid, $curso);
		
        return  0;
    }
    
    public function Put_Aulas($cod_Curso, $user, $nomeuser, $voltar, $pos){
        $db = new avdb;
        $db->Conn_av();
        $this->put_menu($user, $nomeuser, $voltar);
	$this->lista_aulas($cod_Curso, $user, $pos);
    }
    
    public function put_questao($cod_Curso, $nomeuser, $user, $codaula, $indexQuest, $voltar, $msg, $val){
        $db = new avdb;
        $db->Conn_av();
        $this->put_menu($user, $nomeuser, $voltar);
	
        //verifica a data de validade do questionário
        $data = date('Y-m-d');
        $validade = pg_query("SELECT * FROM aulas WHERE \"Chave\" = $codaula");
        $va = pg_fetch_array($validade);
        //echo $val['DataLimite']. "->".$data;
        if (empty($va['DataLimite'])){
            $limite = date('Y-m-d');
        }else{
            $limite = $va['DataLimite'];
        }
        //echo $codaula;
        //echo "<br>CodCurs0: $cod_Curso";
        $resultado = true;
        //verifica se na aula há alguma questão de discursiva
        //echo "SELECT \"Tipo\" FROM aulas_avaliacoes WHERE \"CodAula\" = $codaula AND \"Tipo\" = 'DS'<br/>";
        $tipoq = pg_query("SELECT \"Tipo\" FROM aulas_avaliacoes WHERE \"CodAula\" = $codaula AND \"Tipo\" = 'DS'");
       // echo "rows: ". pg_num_rows($tipoq);
        if (pg_num_rows($tipoq) > 0){
            $resultado = false;
        }
        if ($resultado){
            //echo "entrou resultado";
            //verifica aqui se já foi respondido o questionário.
            $sql = pg_query("SELECT \"Chave\" FROM aulas_avaliacoes_alunos_respostas WHERE \"CodCurso\" = $cod_Curso AND \"CodAula\" = $codaula AND \"CodAluno\" = $user");
            if(pg_num_rows($sql) > 0 ){
                $this->put_obs($cod_Curso, $codaula, $user);
                $this->put_resultado($cod_Curso, $codaula, $user);               
            } else {
                $this->put_obs($cod_Curso, $codaula, $user);
                if ($limite >= $data)
                    $this->questionario($cod_Curso, $codaula, $user, $indexQuest, $msg, $val);
                else
                    $this->put_resultado($cod_Curso, $codaula, $user);
                    
            }
        }else{
           // echo "entrou else resultado";
            $this->put_obs($cod_Curso, $codaula, $user);
            if ($limite >= $data)
                $this->questionario($cod_Curso, $codaula, $user, $indexQuest, $msg, $val);
            else
                $this->put_resultado($cod_Curso, $codaula, $user); 
            
        }
       // echo "Saiu";
    }
    
    public function Resultado_Teste($cod_Curso, $codAula, $user, $nomeuser) {
        $db = new avdb;
        $db->Conn_av();
        $this->put_menu($user, $nomeuser, 'Put_aulas');
	$this->put_obs($cod_Curso, $codAula, $user);
        $this->put_resultado($cod_Curso, $codAula, $user);
    }//Resultado_Teste($cod_Curso, $codAula, $user, $NomeUser);
    
    
    
    
    
    
    function put_obs($codCurso, $codaula, $user){
        //echo "SELECT * FROM aulas_avaliacoes_obs WHERE \"CodCurso\" = $codCurso AND \"CodAula\" = $codaula AND \"CodAluno\" = $user<br/>";
        $sql = pg_query("SELECT * FROM aulas_avaliacoes_obs WHERE \"CodCurso\" = $codCurso AND \"CodAula\" = $codaula AND \"CodAluno\" = $user");
            while($r = pg_fetch_array($sql)){
                //var_dump($r);
                if ($r['Refazer'] == 'f'){
                ?>    
                    <div align="center">
                        <table class="tbform">
                            <tr>
                                <td align="left"><font color="blue"><b>
                                       <?php echo  nl2br($r['Obs']) ?>
                                    </b></font>
                                </td>
                            </tr>
                        </table>
                    </div>
            <?php
                } else{
                ?>    
                    <div align="center">
                        <table class="tbform">
                            <tr>
                                <td align="left"><font color="Red"><b>
                                       <?php echo "Question&aacute;rio marcado para reenvio.<br/>
                                                Aguade liberação do administrador." ?>
                                          
                                    </b></font>
                                </td>
                            </tr>
                        </table>
                    </div>
            <?php
                    
                }
            }    
        
    }
    
    
    function put_menu($user, $nomeuser, $voltar)	{
	$r = 0;
	?>
        <div align="center">
	<table class="tablemenuAluno"> 
	<tr>					  
        <td align="left"  class="tdmenuAl"><b> Bem vindo, <?php echo($nomeuser) ?> 
	</b></td>
	<form id="altsenha" name="altsenha" method="post" action="">
	<td width="100" align="center" valign="middle">
	<?php
        if ($r ==1 ){
            ?>							
            <input name="AltSenha" type="submit" id="AltSenha" value="Alterar Senha" class="botaoAluno"/> 						<?php 
	}else {
            ?>
            &nbsp;
            <?php
	}
	?>	
	</td>
	</form>
	<?php
	if($voltar == 0 ) {
            echo "<td align=\"right\" class=\"tdmenuAl\" ></td>
            <form id=\"voltar\" name=\"voltar\" method=\"post\" action=\"\">
            <td width=\"100\" align=\"center\" valign=\"middle\"> <input name=\"".$voltar."\" type=\"submit\" value=\"Voltar\" class=\"botaoAluno\"/> </td>
            </form>";
	}
        ?>
	<!--
        <form id="Sair" name="Sair" method="post" action="">
	<td width="50" align="center" valign="middle"><input name="Sair" type="submit" id="Sair" value="Sair" class="botaoAluno"/> </td>
	</form>
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

    function put_cursos($userid, $curso) {
        ?>
        <div align="center">
        <table class="tabTitle">
	<tr>
	<?php
	//exibe o nÂº de cursos cadastrados para o adm selecionado
	$sql = "SELECT COUNT(\"CodCurso\")FROM curso_alunos WHERE \"CodUser\" = $userid AND \"CodCurso\" = $curso";
	$res = pg_query($sql);
	$r = pg_fetch_row($res);
	?>
	<td align="left"><b><?php echo($r[0]) ?> cursos cadastrados</b></td>
	</tr>
	</table>
	<table class="tablemenuAluno">
	<tr>
	<td width="30" align="center" class="tdmenuAl"><b>Id</b></td>
	<td align="center" class="tdmenuAl"><b>Curso</b></td>
	<td align="center" width="20" class="tdmenuAl"></td>
	
	</tr>
	</table>
	
	<table class="tabValores">
	<?php
	//preenche 10 Ãºltimas linhas da tabela Cursos
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0;
	//$inicio10 = $inicio + 10;
	////consulta lista de cursos do usuário com contador de aulas e alunos para cada curso listadocount aula e alunos por curso
	if ($res = pg_query("SELECT curso_alunos.\"CodCurso\", cursos.\"Nome\" FROM curso_alunos LEFT JOIN cursos ON 
            cursos.\"Chave\" = curso_alunos.\"CodCurso\" WHERE (curso_alunos.\"CodUser\" = $userid AND curso_alunos.\"CodCurso\" = $curso)
            ORDER BY cursos.\"Chave\" DESC")) {
            //LIMIT $inicio , $inicio10												
            //echo "<br>".$res;
            while($r= pg_fetch_array($res)) {
                echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> 
                    <td width=\"30\">".$r[0]."</td>
                    <td align=\"left\">".$r[1]."</td>
                    <form name=\"T_ChecaAulas\" id=\"T_ChecaAulas\" method=\"post\">
                    <td width=\"20\">
                    <input type=\"hidden\" name=\"T_ChecaAulas\" value=\"".$r[0]."\">
                    <input type=\"submit\" class=\"btnEdita\" name=\"btn\" title=\"Aulas\" alt=\"Aulas\" value=\"\">
                    </td>
                    </form>
                    </tr>";
            }
	}
	?>
	</table>
	</table>
	<table class="tabTitle">
	<form id="MoreCursos" name="Morecursos" method="post" action="">
	<tr><td align="right">
	<!--
	<input name="MoreCursos" type="submit" id="MoreCursos" value="Mais 10" class="botaoAluno" />
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
    } // put_put_cursos
    
    function lista_aulas($cod_curso, $user, $inicio){
        ?>
	<div align="center">
	<table class="tablemenuAluno">
	<tr>
	<!--<td width="45" align="center" class="tdmenuAl"><b>Id</b></td>-->
	<td align="left" class="tdmenuAl"><b>Aulas</b></td>
	<td width="45" align="center" class="tdmenuAl"><b>Quest&otilde;es</b></td>
	<td align="center" width="20" class="tdmenuAl"></td>
	</tr>
	</table>
	<!-- Tabela de dados -->
	<table class="tabValores">
	<?php
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0;
	$inicio10 = $inicio+100;
	/*
	echo "<br> SELECT aulas.Chave, aulas.Nome, COUNT( aulas_avaliacoes.Codaula ) AS c_Aulas
            FROM aulas LEFT JOIN aulas_avaliacoes ON aulas.Chave = aulas_avaliacoes.CodAula 
            WHERE aulas.CodCurso = $cod_curso GROUP BY aulas.Chave, aulas.nome ORDER BY aulas.Chave LIMIT $inicio , 
            $inicio10 <br>";
	//*/
	if ($sql =  pg_query("SELECT aulas.\"Chave\", aulas.\"Nome\", COUNT( aulas_avaliacoes.\"CodAula\" ) AS \"c_Aulas\"
            FROM aulas LEFT JOIN aulas_avaliacoes ON aulas.\"Chave\" = aulas_avaliacoes.\"CodAula\" 
            WHERE aulas.\"CodCurso\" = $cod_curso GROUP BY aulas.\"Chave\", aulas.\"Nome\" ORDER BY aulas.\"Chave\" ") ) {
            //echo "<br> entrou na sql.";
            while ($r = pg_fetch_array($sql)){
                //<td width=\"45\" align=\"center\">".$r['Chave']."</td>
                echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> 
		<td align=\"left\">".$r['Nome']."</td>
		<td width=\"45\" align=\"center\">".$r['c_Aulas']."</td>
                <form name=\"T_EditaAulas".$r['Chave']."\" method=\"post\">
                <td align=\"center\" width=\"20\"> 
		<input type=\"hidden\" name=\"Questoes\" value=\"".$r['Chave']."\">
		<input type=\"submit\" class=\"btnEdita\" name=\"btn\" title=\"Quest&otilde;es\" alt=\"Quest&otilde;es\" value=\"\">
		</td>
		</form>
		</tr>";
            } //while
	} 	//if
	?>
	</table>
	<!-- mais 10 -->
	</table>
	<table class="tabTitle">
	<tr>
	<td align="right">
	<form id="MoreCursos" name="Morecursos" method="post" action="">
	<!--
	<input name="MoreCursos" type="submit" id="MoreCursos" value="Mais 10" class="botaoAluno" />
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
    }//lista_aulas($cod_curso, $user, $inicio)
    
    function questionario($cod_Curso, $codaula, $user, $indexQuest, $msg, $val){
        
        
        echo "<br>";
    	//foreach($val as $c=>$d)
	//	echo $c.'-'.$d. ' ';
        $sql = pg_query("SELECT \"Nome\", \"MinimoMP\", \"DataLimite\" FROM aulas WHERE \"Chave\" = $codaula");
	$r = pg_fetch_array($sql);
        //carrega o mínimoMP para verificação de questões DS
        $min = $r['MinimoMP'];
        $verifica = 0;
	?>
	<div align="center">		
	<table class="tablemenuAluno">
	<tr>
	<td align="left"><b><?php echo $r[0] ?></b></td>
        <td align="right"><?php echo "Data limite: $r[2]"?></td>
	</tr>
	</table>
            <form id="Questionario" name="Questionario" method="post" onclick="gettopo();">
	<?php
	//exibe mensagem de erro caso necessário
	if ($msg != '') {
            $av = preg_replace("/(\\r)?\\n/i", "<br/>", $msg);
            echo "<br><table width=\"500\" class=\"tbform\">
            <tr>
            <td><font color=\"red\"><b>$av</b></font></td>
            </tr>
            </table>";
        }	
	//echo "<br> $cod_Curso -- $codaula";
        if ($sql = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodCurso\" = $cod_Curso AND \"CodAula\" = $codaula ORDER BY \"IndexQuestao\"") ){
            while ($r = pg_fetch_array($sql)) {
                //exibe o enunciado da questão 
		$questao = preg_replace("/(\\r)?\\n/i", "<br/>", $r['Questao']);
                $questao = ltrim($questao);
                
		?>
                <table width="500" class="tbform">
		<tr>
                <td width="20" valign="top" align="left"><b><?php echo $r['IndexQuestao'] ?>:</b></td>
                <td align="left" colspan="2"><b><?php echo $questao ?></b></td>
		</tr>
		</table>
		<?php
                $ds = false;
		if ($r['Tipo'] == '') 
                    $r['Tipo'] = 'MP';
		switch($r['Tipo']) {
                    case 'MP' : 				
                        //obtem a sequencia de alternativas pela Chave
                        $array_alts = array();
                        $SQLalts = pg_query("SELECT \"Chave\" FROM aulas_avaliacoes_alternativas 
                            WHERE \"CodAula\" = $codaula and \"CodAvaliacao\" = $r[0] ORDER BY \"IndexAlternativa\"");
                        while($alts = pg_fetch_array($SQLalts)){
                           $array_alts[] = $alts[0]; 
                        }
                        //var_dump($array_alts);
                        //conta o nº de alternativas
                        $numalts = count($array_alts);
                        //echo $numalts."<br>";
                        //cria a sequancia randomica
                        //gera a sequencia aleatória de numalts;
                        $RandSeq = $this->GeraSequencia($numalts);
                        //var_dump($RandSeq);
                        $seq = array();
                        foreach($RandSeq as $s){
                            $seq[] = $array_alts[$s-1];
                        }
                        //var_dump($seq);
                        //exibe as alternativas
			foreach ($seq as $key =>$value) {
                            $sql2 = pg_query("SELECT * FROM aulas_avaliacoes_alternativas 
                                WHERE \"Chave\" = $value"); 
                            $r2 = pg_fetch_array($sql2);
                        
                        /* substituido pela sequencia aleatória
                        //echo "<br> SELECT * FROM aulas_avaliacoes_alternativas WHERE CodAula = $codaula AND CodAvaliacao = $r[0] ORDER BY IndexAlternativa"; 
			if ($sql2 = mysql_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE CodAula = $codaula AND CodAvaliacao = $r[0] ORDER BY IndexAlternativa") or die(mysql_errno()) ){
                            while($r2 = mysql_fetch_array($sql2))
                         */
                                $letra = $this->Num_to_letra($key+1); //. $r2[0]."-".$r2[3]."-".$this->Num_to_letra($r2[3]);
				$alternativa = preg_replace("/(\\r)?\\n/i", "<br/>", $r2['Alternativa']);
				$sel = false;
                                if ($val != '') { 
                                    foreach($val as $c) {
                                        reset($val);
                                        if ($c == $r2[0]) {
                                            $sel = true;
                                            //echo "C-> $c <br>";
					}
                                    }
				}
				?>
				<table width="500" class="tbformQuest"  >
				<tr>
				<td width="10" align="left"><?php  echo $letra ?></td>
                       		<td width="20" valign="middle" align="center" >
				<input type="radio" name="<?php echo $r[0] ?>" value="<?php echo $r2[0]?>" 
                                    <?php echo ($sel)?'Checked':'' ?>></td>
                       		<td align="left" ><b><?php echo $alternativa ?></b></td>
				</tr>
				</table>
				<?php
                            //}
			}
                        //exibe a informação 
                        ?>
                        <table width="500" class="tbformQuest"  >
				<tr>
                                    <td><font color="red">
                                        A Ordem das alternativas &eacute; aleat&oacute;ria e diferente a cada altera&ccedil;&atilde;o de tela.
                                        Provavelmente ser&aacute; diferente da letra da alternativa exibida no resultado da quest&atilde;o.
                                            </font>
                                    </td>
                                </tr>
                        </table>
                        <?php
			//uma linha
			echo "<br>";
			break;
                    case 'DS' :
			$ds = true;
                        $verifica = 1;
                        $sel = '';
			//var_dump($val);
                        if ($val != '') { 
                            reset($val);
                            foreach($val as $chave => $valor) {
                                if ($chave == $r[0]) {
                                    $sel = $valor;
                                    //echo "C-> $c <br>";
				}
                            }
			}else{
                            //obtém $sel do rascunho salvo no banco de dados
                            $rascunho = pg_query("SELECT \"Discursiva\" FROM aulas_avaliacoes_alunos_respostas WHERE \"CodAvaliacao\" = ".$r['Chave']." AND \"CodAluno\" = $user");
                            if(pg_num_rows($rascunho)>0){
                                $rasc = pg_fetch_array($rascunho);
                                $sel = $rasc['Discursiva'];
                            }
                        }
                        //echo "sel: $sel...<br/>";
			$gravado = false;
                        $editavel = true;
                        //echo "SELECT * FROM aulas_avaliacoes_alunos_respostas WHERE \"CodAvaliacao\" = ".$r['Chave']." AND \"CodAluno\" = ".$user."<br/>";
                        $sqlds = pg_query("SELECT * FROM aulas_avaliacoes_alunos_respostas WHERE \"CodAvaliacao\" = ".$r['Chave']." AND \"CodAluno\" = ".$user );
                        if(pg_num_rows($sqlds) > 0){
                            $gravado = true;
                            $ds = pg_fetch_array($sqlds);
                            if($ds['Nota'] > 0){
                                $editavel = false;
                            }
                        } 
                        //echo "editavel: $editavel, gravado: $gravado";
                        
                        ?>
			<table width="500" class="tbformtbformQuest">
			<?php
                          if($editavel){
                              //echo "é editável<br>";
                        ?>
                            <tr>
			<td width="10px" align="left"></td>
			<td align="left">
                            <textarea rows="6" cols="54" name="<?php echo $r['Chave']?>" onchange="gravaedicao(this,<?php echo $r['Chave'].", $user"?>)"><?php echo $sel ?></textarea> 
                       	</td>
			</tr>
                        <tr>
                            <td align="left">
                                <button type="submit"  name="SalvarRascunho" value="<?php echo $r['Chave']?>" title="Salvar Rascunho">Salvar</button>                               
                            </td>
                            <td align="right">
                                <?php
                                 if (($r['Min'] > 0) || ($r['Max'] > 0 )) 
                                 if (strlen($sel) > 0) {
                                    $fc = new funcs();
                                    $str = $fc->contapalavras($sel, $r['Min'], $r['Max']);
                                    echo $str;
                                  }
                                ?>                                
                            </td>
                        </tr>
                        <?php
                        } else { //não editável
                            $str = $sel;
                            $q = substr_count($str, chr(13)) + 2;
                            ?>
                            <tr>
                                <td width="20"></td> 
                                <td>
                                    <textarea rows="<?php echo $q ?>" cols="54" name="<?php echo $r['Chave']?>" readonly="readonly"><?php echo $sel ?></textarea>                                 
                                </td>
                                <td width="20px"></td>
                            </tr>
                            <tr>
                                <td width="20"></td> 
                                <td  style="text-align: right;">
                                    <font color="blue"><b>
                                    <?php if($ds['Nota'] > 4){
                                            echo "Nota:". $ds['Nota'];
                                          }else{
                                              switch ( $ds['Nota'] ){
                                                  case 1: echo "Conceito: Insatisfatório"; break;
                                                  case 2: echo "Conceito: Satisfatório"; break;
                                                  case 3: echo "Conceito: Bom"; break;
                                                  case 4: echo "Conceito: Excelente"; break;
                                              }

                                          }
                                          ?>                                    
                                    </b></font>    
                                </td>
                                <td width="20px"></td>
                            </tr>                            
                            <?php
                        }
                        ?>
			</table>
			<?php
			break;
                    case 'SM' : 				
                        //exibe as alternativas
			//echo "<br> SELECT * FROM aulas_avaliacoes_alternativas WHERE CodAula = $codaula AND CodAvaliacao = $r[0] ORDER BY IndexAlternativa"; 
			if ($sql2 = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAula\" = $codaula AND \"CodAvaliacao\" = $r[0] ORDER BY \"IndexAlternativa\"") ){
                            while($r2 = pg_fetch_array($sql2)) {
                                $letra = $this->Num_to_letra($r2[3]);
				$alternativa = preg_replace("/(\\r)?\\n/i", "<br/>", $r2['Alternativa']);
				$sel = false;
				if ($val != '') { 
                                    reset($val);
                                    foreach($val as $c => $i) {
                                        if ($c == $r2[2]) {
                                            foreach ($i as $k => $v) {
                                                if($v == $r2[0]){
                                                    $sel = true;
                                                }
                                            }  
					}
                                    }
				}
                                
				?>
				<table width="500" class="tbformQuest">
				<tr>
				<td width="10" align="left"><?php  echo $letra ?></td>
                       		<td width="20" valign="middle" align="center" >
				<input type="checkbox" name="<?php echo $r[0]."[]" ?>" value="<?php echo $r2[0]?>" 
                                    <?php echo ($sel)?'Checked':'' ?>></td>
                       		<td align="left" ><b><?php echo $alternativa ?></b></td>
				</tr>
				</table>
				<?php
                            }
			}
			//uma linha
			echo "<br>";
			break;
		}//switch
							
            }//WHILE
        }//IF
	?>	
	<table class="tablemenuAluno">
	<tr>
	<td> </td>
	</tr>
	</table>
	<table width="500" >
	<tr>
	<td align="right">
            <?php
            if (!$ds){
            ?>
            <input type="submit" name="Enviarespostas" value="Enviar Respostas" onclick="return checkaDS('Questionario',<?php echo "$min, $verifica" ?>)">
            <?php } ?>
	</td>
	</tr>
	</table>
	</form>
	</div>
	<?php	
    }//questionario
    
    function GeraSequencia($num){
        $seq = array();
        while (count($seq) < $num){
            $n = mt_rand(1, $num);
            if (!in_array($n, $seq)){
                $seq[] = $n;
            }
        }
        return $seq;        
    }
    
    function put_resultado($cod_Curso, $CodAula, $user){
        $sql = pg_query("SELECT \"Nome\", \"DataLimite\" FROM aulas WHERE \"Chave\" = $CodAula");
	$r = pg_fetch_array($sql);
        ?>
        <div align="center">		
	<table class="tablemenuAluno">
	<tr>
	<td align="left"><b><?php echo $r[0] ?></b></td>
        <td align="right"><?php echo "Data Limite: $r[1]" ?> </td>
	</tr>
	</table>
	</div>
	<br />
	<?php
	$pontuacao = 0;
	$total = 0;
        $checadtAvaliacao = FALSE;

        //exibe a questão
	//echo "<br>SELECT * FROM aulas_avaliacoes WHERE CodCurso = $cod_Curso AND CodAula = $CodAula ORDER BY IndexQuestao";
	$sql = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodCurso\" = $cod_Curso AND \"CodAula\" = $CodAula ORDER BY \"IndexQuestao\"");
	while ($r = pg_fetch_array($sql)) {
            $questao = preg_replace("/(\\r)?\\n/i", "<br/>", $r['Questao']);
            ?>
            <div align="center">		
            <table width="500" class="tbform">
            <tr>
            <td width="20" valign="top" align="left"><b><?php  echo $r['IndexQuestao'] ?>:</b></td>
            <td align="left" ><b><?php  echo $questao; ?></b></td>
            </tr>
            </table>
            </div>
            <?php
            //exibe a resposta do aluno
            //echo "SELECT CodAlternativa, Discursiva, Nota FROM aulas_avaliacoes_alunos_respostas WHERE CodAvaliacao = ".$r['Chave']." AND CodAluno = $user<br>";
            $resp = pg_query("SELECT \"CodAlternativa\", \"Discursiva\", \"Nota\" FROM aulas_avaliacoes_alunos_respostas WHERE \"CodAvaliacao\" = ".$r['Chave']." AND \"CodAluno\" = $user");
            $r2 = pg_fetch_array($resp);
            $total = $total + $r['Peso'];

            //caso múltipla escolha ou Somatória
            //**********************************************************
            if(empty($r['Tipo'])) $r['Tipo'] = 'MP';
            //echo $r['Tipo']."<br>";
            $t= $r['Tipo'];
            switch($t){
                case 'MP' :
                if ($r2[0] != -1 ) { // múltipla escolha ou Somat[é]oria
                    //exibe a alternativa correta
                    //echo "<br> SELECT * FROM aulas_avaliacoes_alternativas WHERE CodAula = $CodAula AND CodAvaliacao = ".$r['Chave']." AND Resposta = 1";
                    $alt = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAula\" = $CodAula AND \"CodAvaliacao\" = ".$r['Chave']." AND \"Resposta\" = 1");
                    $a = pg_fetch_array($alt);
                    $letra = $this->Num_to_letra($a[3]);
                    $alternativa = preg_replace("/(\\r)?\\n/i", "<br/>", $a['Alternativa']);
                    ?>
                    <div align="center">	
                    <table width="500" class="tbResposta">
                    <tr>
                    <td width="5" align="left"></td>
                    <td><b>Alternativa Correta:<?php //echo " (".$letra ?></b></td>
                    <td width="5"></td>
                    </tr>
                    <tr>
                    <td width="5" align="left"></td>
                    <td><?php echo $alternativa?></td>
                    <td width="5"></td>
                    </tr>
                    <?php
                    if ($r2[0] == $a[0]){
                        $pontuacao = $pontuacao + $r['Peso'];
                       ?>
                        <tr>
                        <td width="5" align="left"></td>
                        <td align="right"><font color="#0000FF"> <b>CORRETA</b></font> </td>
                        <td width="5"></td>
                        </tr>
                        </table> 
                        <?php
                        echo "<br>";
                    } else { //exibe a alternativa incorreta selecionada pelo aluno
                        //echo "<br> SELECT * FROM aulas_avaliacoes_alternativas WHERE Chave = ".$r2[0];
                        if ($r2[0]> 0 ) {
                        $sqlerr = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"Chave\" = ".$r2[0]);
                            if (pg_num_rows($sqlerr) == 1) {
                                $err = pg_fetch_row($sqlerr);
                                $letra = $this->Num_to_letra($err[3]);				
                                $alternativa = preg_replace("/(\\r)?\\n/i", "<br/>", $err[4]);				
                                ?>
                                <tr>
                                    <td width="5" align="left"></td>
                                <td><font color="#FF0000"><b>Sua resposta:</b></font> </td>
                                <td width="5"> </td>
                                </tr>
                                <tr>
                                <td width="5" align="left"></td>
                                <td> <?php echo $alternativa ?> </font> </td>
                                <td width="5"></td>
                                </tr>
                                <?php
                            }
                        }else{
                            ?>
                                <tr>
                                    <td width="5" align="left"></td>
                                <td><font color="#FF0000"><b>Sua resposta:</b></font> </td>
                                <td width="5"> </td>
                                </tr>
                                <tr>
                                <td width="5" align="left"></td>
                                <td> <?php echo "Sem resposta" ?> </font> </td>
                                <td width="5"></td>
                                </tr>
                                
                            <?php    
                        }
                        ?>
                        </table>
                        </div>
                        <?php
                        echo "<br>";
                    }
                }
                break;
                case 'SM' :
                    //if ($r2[0] != -1 ) { // múltipla escolha ou Somat[é]oria
                    //exibe a alternativa correta
                    //echo "<br> SELECT * FROM aulas_avaliacoes_alternativas WHERE CodAula = $CodAula AND CodAvaliacao = ".$r['Chave']." AND Resposta = 1";
                    $alt = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAula\" = 
                            $CodAula AND \"CodAvaliacao\" = ".$r['Chave']." AND \"Resposta\" = 1");
                    ?>
                    <div align="center">	
                    <table width="500" class="tbResposta">
                    <?php
                    $SMcorr = array();
                    while ($a = pg_fetch_array($alt)) {
                        $letra = $this->Num_to_letra($a[3]);
                        $alternativa = preg_replace("/(\\r)?\\n/i", "<br/>", $a['Alternativa']);
                        ?>
                        <tr>
                        <td width="5" align="left"></td>
                        <td><b> Alternativa Correta:<?php //echo " (".$letra ?></b></td>
                        <td width="5"></td>
                        </tr>
                        <tr>
                        <td width="5" align="left"></td>
                        <td><?php echo $alternativa?></td>
                        <td width="5"></td>
                        </tr>
                        <?php
                        
                        $SMcorr[] = $a[0];
                        
                    }
                    //verfica se as respostas conferem
                    $qrSM = pg_query("SELECT \"CodAlternativa\"  FROM aulas_avaliacoes_alunos_respostas WHERE 
                        \"CodAvaliacao\" = ".$r['Chave']." AND \"CodAluno\" = $user GROUP BY \"CodAlternativa\"" );
                    $parc = 0;
                    $x = False;
                    while ($SM = pg_fetch_array($qrSM)){
                        reset($SMcorr);
                        $x = FALSE;
                        foreach ($SMcorr as $v) {
                            if ($SM[0]== $v) {
                                $parc++;
                                $x = TRUE;
                            }
                        }
                        if (!$x) break;
                    }
                    
                    $msg = "RESPOSTA INCORRETA";
                   // echo count($SMcorr)."...".$parc."<br>";
                    if ($x && count($SMcorr)== $parc){
                        $pontuacao = $pontuacao + $r['Peso'];
                        $msg = "RESPOSTA CORRETA";
                    } else if ($x){
                        $pontuacao = $pontuacao + $r['Peso'] / 2;
                        $msg = "RESPOSTA PARCIAL";
                    } 
                    
                    //coloca a td da tabela dentro do if;
                    ?>
                    <tr>
                    <td width="5" align="left"></td>
                    
                    <td align="right"><font color="#0000FF"> <b> <?php echo $msg ?></b></font> </td>
                    <td width="5"></td>
                    </tr>
                    </table> 
                    <?php
                    echo "<br>";
                //}
                break;
            
		//caso discursiva
                
                default :  //Discursiva
                    //echo "entrou na DS<br>";
                    $checadtAvaliacao = TRUE;
                    //$Discur = preg_replace("/(\\r)?\\n/i", "<br/>", $r2['Discursiva']);
                    $str = $r2['Discursiva'];
                    $q = substr_count($str, chr(13)) + 2;
                    
                    $nota = '';
                    if ($r2['Nota'] != NULL) {
                        $nota = $r2['Nota'];
                        $pontuacao = $pontuacao + $nota;
                    }
                    $pesot = $r['Peso'];
                    ?>
                    <div align="center">
                    <table width="500" class="tbResposta">
                    <tr>
                    <td width="5" align="left"></td>
                    <td align="left">
                        <textarea rows="<?php echo $q ?>" cols="54" readonly="readonly" style="color: blue;"><?php echo $str ?></textarea>
                    </td>
                    <td width="5"></td>
                    </tr>
                    <tr>
                    <td width="5" align="left"></td>
                    <td align="right"><font color="black"><b>
                    <?php 
                    if($nota > 4){
                        echo "Nota:". $nota;
                    }else{
                        switch ( $nota ){
                            case 1: echo "Conceito: Insatisfatório"; break;
                            case 2: echo "Conceito: Satisfatório"; break;
                            case 3: echo "Conceito: Bom"; break;
                            case 4: echo "Conceito: Excelente"; break;
                        }
                    }
                    ?>                                    
                        </b></font> </td>
                    <td width="5"></td>
                    </tr>
                    </table> 
                    </div>
                    <?php
                    echo "<br>";
                break;
            
            } 
        }
	//exibe a pontuação total
	$perc = round(($pontuacao / $total ) * 100, 2);
	?>
	<div align="center">
	<table width="500" class="tbform">
	<tr>
	<td width="5" align="left"></td>
        <?php
           // echo "será que não entrou no checa? Por quê?";
            if ($checadtAvaliacao){
                //echo "por que não está subindo?<br>";
               // echo "SELECT DataLimite FROM aulas WHERE Chave = $CodAula<br>";
                $qdt = pg_query("SELECT \"DataLimite\" FROM aulas WHERE \"Chave\" = $CodAula");
                $dt = pg_fetch_array($qdt);
                $dtav = new DateTime($dt[0]);
                $dtatu = new DateTime(date('y-m-d'));
                //$dif = $dtav ->diff($dtatu);
                //$dif = date_diff($dtav, $dtatu, TRUE);
                $data = date('d/m/Y',strtotime($dt[0]));
               // echo $data ."->". $dt[0];
                if((($dt[0]!= '')&&($dt != '0000-00-00 ')) && ($dtatu<$dtav) ){
                    ?>
                    <td align="center"><b> Avalia&ccedil;&atilde;o dispon&iacute;vel a partir de: <?php  echo " $data" ?></b></td>
                    <?php    
                }else {
                    ?>
                    <td align="center"><b> Seu aproveitamento: <?php  echo "$perc % "?></b></td>
                    <?php
                } 
            }else{
                ?>
                <td align="center"><b> Seu aproveitamento: <?php  echo "$perc % "?></b></td>
                <?php
            }
            ?>
        <td width="5"></td>
	</tr>
	</table>
	<br />
	<table class="tablemenuAluno">
	<tr>
	<td> </td>
	</tr>
	</table>
	</div>
	<?php
	//*/	
    }//put_resultado($cod_Curso, $codAula, $user);
    
    
    function Num_to_letra($num){
        switch($num){
            case 1: $l = 'a)'; break;
            case 2: $l = 'b)'; break;
            case 3: $l = 'c)'; break;
            case 4: $l = 'd)'; break;
            case 5: $l = 'e)'; break;
            case 6: $l = 'f)'; break;
            case 7: $l = 'g)'; break;
            case 8: $l = 'h)'; break;
            case 9: $l = 'i)'; break;
            case 10: $l = 'j)'; break;
            case 11: $l = 'k)'; break;
            case 12: $l = 'l)'; break;
            case 13: $l = 'm)'; break;
            case 14: $l = 'n)'; break;
            case 15: $l = 'o)'; break;
            case 16: $l = 'p)'; break;
            case 17: $l = 'q)'; break;
            case 18: $l = 'r)'; break;
            case 19: $l = 's)'; break;
            case 20: $l = 't)'; break;
            case 21: $l = 'u)'; break;
            case 22: $l = 'v)'; break;
            case 23: $l = 'x)'; break;
            case 24: $l = 'z)'; break;
            default: $l = $num; break;
        }
	return $l;
    }
    
}
?>
