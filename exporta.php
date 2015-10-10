<?php
class exporta {

    function listaAulas($CodCurso, $nomeCurso, $msg) {
        $db = new avdb;
        $db->Conn_av();
        if($msg != ''){
            $this->put_msg($msg);
        }
        $this->put_curso($nomeCurso);
	$this->put_aulas($CodCurso, $nomeCurso);
    }
	
    function ConfirmaExporta($Origem, $Destino) {
        $db = new avdb;
        $db->Conn_av();
	$this->Put_questoes_origem($Origem, $Destino);
    }//function ConfirmaExporta($Origem, $Destino)
	
    function put_curso($nomeCurso) {
        ?>
        <div align="center">
        <table class="tablemenu">
        <tr>
        <td align="center"><b>Exportar: <?php echo " $nomeCurso" ?> </b></td>
        <td align="center" width="100">
        <form id="Volar" name="Voltar" method="post">
        <input name="OdimVoltar" type="submit" id="OdimVoltar" value="Voltar" class="botaoOdim"/>
        </form>
        </td>
        </tr>
        </table>
        <table border="0">
        <tr> <td> </td> </tr>
        </table>
        </div>
        <?php
    }//function put_curso($nomeCurso) 
	
    function put_aulas($CodCurso,$nomeCurso) {
        //linha título
	?>
	<div align="center">
	<table class="tablemenu">
	<tr>
	<font size="2" color="#FFFFFF">
	<td width="80" align="left" class="tdmenu"><b>Origem</b></td>
	<td align="left" class="tdmenu"><b>Aula</b></td>
        <td align="center" width="120" class="tdmenu"><b>Quest&otilde;es</b></td>
	</font>
	</tr>
	</table>
	</div>
	<form id="Exporta" name="Exporta" method="post">
        <?php
        //echo "<br> entrou sql";
        $res = pg_query("SELECT aulas.\"Chave\", aulas.\"Nome\", Count(aulas_avaliacoes.\"Questao\") AS \"Questoes\" FROM aulas INNER JOIN aulas_avaliacoes ON aulas.\"Chave\" = aulas_avaliacoes.\"CodAula\" WHERE (((aulas.\"CodCurso\")=$CodCurso)) GROUP BY aulas.\"Chave\", aulas.\"Nome\"");
	//echo "SELECT aulas.Chave, aulas.Nome, Count(aulas_avaliacoes.Questao) AS Questoes FROM aulas INNER JOIN aulas_avaliacoes ON aulas.Chave = aulas_avaliacoes.CodAula WHERE (((aulas.CodCurso)=$CodCurso)) GROUP BY aulas.Chave, aulas.Nome <br>";
        //echo "<br> Saiu Sql";
//echo mysql_num_rows($res);
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0; 
	?>	
	<div align="center">
	<table class="tabValores">
	<?php
	while($r = pg_fetch_array($res)) {
            echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\">
            <td width=\"80\" align=\"center\">
            <input type=\"radio\" name=\"Origem\" value=\"".$r[0]."\">
            </td>
            <td align=\"left\">".$r[1]."</td>
            <td width=\"120\" align=\"center\">".$r[2]."</td>
            </tr>";
        }
	?>
	</form>
	</table>
	</div>
	<?php
	//DESTINO
	?>
	<div align="center">
	<table border="0">
	<tr> <td> </td> </tr>
	</table>
        <table class="tablemenu">
	<tr>
	<td align="center"><b>Exportar para </b></td>
	</tr>
	</table>
	<!--
	<table border="0">
	<tr> <td> </td> </tr>
	</table>
	-->
	<table class="tablemenu">
	<tr>
	<font size="2" color="#FFFFFF">
	<td width="80" align="left" class="tdmenu"><b>Destino</b></td>
	<td align="left" class="tdmenu"><b>Curso</b></td>
	<td align="center" width="120" class="tdmenu"><b>Aulas</b></td>
	</font>
	</tr>
	</table>
        </div>
	<?php	
	$res = pg_query("SELECT cursos.\"Chave\", cursos.\"Nome\", Count(aulas.\"Nome\") AS \"Aulas\" FROM cursos LEFT JOIN aulas ON cursos.\"Chave\" = aulas.\"CodCurso\" WHERE (((cursos.\"Chave\")<>$CodCurso)) 
            GROUP BY cursos.\"Chave\", cursos.\"Nome\"
            ORDER BY cursos.\"Chave\" DESC"); 
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0; 
	?>	
	<div align="center">
	<table class="tabValores">
	<?php
	while($r = pg_fetch_array($res)) {
            echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\">
            <td width=\"80\" align=\"center\">
            <input type=\"radio\" name=\"Destino\" value=\"".$r[0]."\">
            </td>
            <td align=\"left\">".$r[1]."</td>
            <td width=\"120\" align=\"center\">".$r[2]."</td>
            </tr>";
	}
	?>
	</table>
	<table class="tablemenu">
	<tr>
	<td align="right">
            <input type="hidden" name="codCurso" value="<?php echo $CodCurso ?>">
            <input type="hidden" name="nomeCurso" value="<?php echo $nomeCurso ?>">
        <input name="ExportarQuest" type="submit" id="ExportarQuest" value="Exportar" class="botaoOdim"/>
	</td>
	<td width="15"></td>
	</tr>
	</table>		
	</div>
	</form>
	<?php
    }//function put_aulas($CodCurso)
	
    function Put_questoes_origem($Origem, $Destino){
        //exibe o nome do curso e da aula
	$res0 = pg_query("SELECT aulas.\"Chave\", cursos.\"Nome\", aulas.\"Nome\" FROM aulas INNER JOIN cursos ON aulas.\"CodCurso\" = cursos.\"Chave\" WHERE (((aulas.\"Chave\")= $Origem))");
	$r0 = pg_fetch_row($res0);
	?>
	<div align="center">
	<table class="tablemenu">
	<tr>
	<td align="center"><b><?php echo $r0[1]." - ".$r0[2] ?></b> </td>
	</tr>
	</table>
	<?php
        //exibe as questões
	$res1 = pg_query("SELECT \"Chave\", \"IndexQuestao\", CONCAT( LEFT( \"Questao\", 80 ),'...') AS \"Questao\" FROM aulas_avaliacoes WHERE \"CodAula\" = $Origem ORDER BY \"IndexQuestao\"");
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0; 
	?>	
	<table class="tabValores">
	<?php
	while($r1 = pg_fetch_array($res1)) {
            echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\">
            <td width=\"40\" align=\"center\">".$r1[1]."</td>
            <td align=\"left\">".$r1[2]."</td>
            </tr>";
	}
	?>
	</table>
	<table>
	<tr><td></td></tr>
	</table>
	<?php
	//exibe as questões já lançadas no destino
	$res2 = pg_query("SELECT cursos.\"Chave\", cursos.\"Nome\", aulas.\"Chave\", aulas.\"Nome\" FROM cursos LEFT JOIN aulas ON cursos.\"Chave\" = aulas.\"CodCurso\" WHERE (((cursos.\"Chave\")= $Destino)) GROUP BY cursos.\"Chave\", cursos.\"Nome\", aulas.\"Chave\", aulas.\"Nome\" ORDER BY aulas.\"Chave\"");
	//título do curso
	$rt = pg_fetch_row($res2)
        ?>
        <table class="tablemenuAluno">
	<tr>
	<td align="center"><b><?php echo $rt[1] ?> - Aulas: </b></td>
	</tr>
	</table>
	<?php
	//aulas e questões
	$res2 = pg_query("SELECT cursos.\"Chave\", cursos.\"Nome\", aulas.\"Chave\", aulas.\"Nome\" FROM cursos INNER JOIN aulas ON cursos.\"Chave\" = aulas.\"CodCurso\" WHERE (((cursos.\"Chave\")= $Destino)) GROUP BY cursos.\"Chave\", cursos.\"Nome\", aulas.\"Chave\", aulas.\"Nome\" ORDER BY aulas.\"Chave\"");
	$countaulas = pg_num_rows($res2);
	while($r2 = pg_fetch_array($res2)) {
            ?>
            <table class="tablemenuAluno">
            <tr>
            <td align="left"><?php echo $r2[3] ?></td>
            </tr>
            </table>
            <?php
            //insere as questõe
            $res3 = pg_query("SELECT aulas_avaliacoes.\"CodAula\", aulas_avaliacoes.\"IndexQuestao\", CONCAT( LEFT( aulas_avaliacoes.\"Questao\", 80 ),'...') AS \"Questao\" FROM aulas_avaliacoes WHERE aulas_avaliacoes.\"CodAula\" = ".$r2[2]); 
            $c1 = "#FFFFFF";
            $c2 = "#CCCCCC";
            $c = 0; 
            ?>	
            <table class="tabValores">
            <?php
            while($r3 = pg_fetch_array($res3)) {
                echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\">
		<td width=\"40\" align=\"center\">".$r3[1]."</td>
		<td align=\"left\">".$r3[2]."</td>
		</tr>";
            }
            ?>
            </table>
            <?PHP
	}
	?>
	</table>
	<table> <tr><td></td></tr></table>
	<?php
	//verifica se alguma das questões já foi cadastrada no destino
        $msg = '';
        $resCheca = pg_query("SELECT aulas_avaliacoes.\"CodAula\", aulas_avaliacoes.\"IndexQuestao\", aulas_avaliacoes.\"Questao\" 
        FROM aulas_avaliacoes WHERE (((aulas_avaliacoes.\"CodAula\")= $Origem))"); 
 	while ($checa = pg_fetch_array($resCheca)) {
            $quest = strtr($checa['Questao'], "\"", "\\\"");   
            $resVer = pg_query("SELECT aulas_avaliacoes.\"CodCurso\", aulas_avaliacoes.\"IndexQuestao\", 
                aulas_avaliacoes.\"Questao\" FROM aulas_avaliacoes WHERE (((aulas_avaliacoes.\"CodCurso\")= $Destino) 
                AND ((aulas_avaliacoes.\"Questao\")='".$quest."'))");
            $ver = pg_num_rows($resVer);
            if($ver >= 1) {
                $msg .= "Quest&atilde;o ".$checa[1]." já foi cadastrasda no curso de destino <br>";
            }
	}
	if ($msg <> '') {
            ?>
            <table> <tr> <td> 
            <font color="#FF0000">	
            <?php echo $msg ?>
            </font>
            </td> </tr> </table>
            <table class="tablemenu">
            <tr>
            <td align="right">
            <form id="VoltarExporta" name="VoltarExporta" method="post">
            <input type="hidden" name="Origem" value="<?php echo $Origem ?>" />
            <input type="hidden" name="Destino" value="<?php echo $Destino ?>"  />
            <input name="OdimVoltar" type="submit" id="OdimVoltar" value="Voltar" class="botaoOdim"/>
            </form>
            </td>
            <td width="15"></td>
            </tr>
            </table>
            <?php
	} else {
            //exibe o meni de exportação
            $cont = $countaulas+1 
            ?>
            <table class="tablemenu">
            <tr>
            <form id="ExportaConfirm" name="ExportaConfirm" method="post">
            <td width="15"></td>
            <td align="left">Nova Aula:<br>
            <input type="text" name="NomeAula" size="45" value="<?php echo "Aula ".$cont ?>">
            <br>Data Avalia&ccedil;&atilde;o:<br>
            <input type="text" name="DataLimite" size="20" value="" OnKeyUp="mascaraData(this, 0);" maxlength="10">
            </td>
            <td align="right" valign="middle">
            <input type="hidden" name="Origem" value="<?php echo $Origem ?>" />
            <input type="hidden" name="Destino" value="<?php echo $Destino ?>"  />
            <input name="ConfirmaExportacao" type="submit" id="ConfirmaExportacao" value="Exportar" class="botaoOdim"/>
            <input name="OdimVoltar" type="submit" id="OdimVoltar" value="Voltar" class="botaoOdim"/>						
            </td>
            <td width="15"></td>
            </form>
            </tr>
            </table>
            <?php
	}
	?>
	</div>
	<?php		
    }//function >Put_questoes_origem($Origem){
    
    function Executa($Origem, $Destino, $nmAula, $DataLimite) {
        $db = new avdb;
        $db->Conn_av();
        if (strlen($DataLimite)==10){
            $dia = substr($DataLimite,0, 2);
            $mes = substr($DataLimite,3, 2);
            $ano = substr($DataLimite,6, 4);
            $DataLimite = $ano.'-'.$mes.'-'.$dia;
            //echo $DataLimite;
        }else
            $DataLimite = NULL;
        
        //obtem o MinimoMP de Origem
        $MinimoMP = $db->Select("\"MinimoMP\" FROM aulas WHERE \"Chave\" = $Origem");    
         //insere a nova aula no curso de destino
	//echo "<br>INSERT INTO aulas (\"CodCurso\", \"Nome\", \"DataLimite\") VALUES($Destino, '$nmAula', '$DataLimite')".$DataLimite;
        if ($DataLimite != NULL ) {
            $sql = pg_query("INSERT INTO aulas (\"CodCurso\", \"Nome\", \"DataLimite\", \"MinimoMP\") VALUES($Destino, '$nmAula', '$DataLimite', $MinimoMP)");
        } else {
            $sql = pg_query("INSERT INTO aulas (\"CodCurso\", \"Nome\", \"MinimoMP\") VALUES($Destino, '$nmAula', $MinimoMP)");
        }
	//echo "INSERT INTO aulas CodCurso, Nome VALUES($Destino, '$nmAula')"."<br>";
	//recupera a Chave da Nova criada
	$sql = pg_query("SELECT \"Chave\" FROM aulas WHERE \"CodCurso\" = $Destino AND \"Nome\" = '$nmAula' ORDER BY \"Chave\" DESC ");
	$res = pg_fetch_array($sql);
	$NovaAula = $res[0];
	//echo "SELECT * FROM aulas_avaliacoes WHERE (((CodAula)=$Origem)) ORDER BY IndexQuestao<br>";
	$SQLorig = pg_query("SELECT * FROM aulas_avaliacoes WHERE (((\"CodAula\")=$Origem)) ORDER BY \"IndexQuestao\"");// or die(mysql_errno());
	//echo "SELECT * FROM aulas_avaliacoes WHERE (((CodAula)=$Origem)) ORDER BY IndexQuestao<br>";
	$fields_orig = pg_query("SELECT column_name FROM information_schema.columns WHERE table_name = 'aulas_avaliacoes'"); 
	$count = pg_num_fields($SQLorig);
	//echo $count."<br>";
	while($orig = pg_fetch_array($SQLorig)) {
            $str = "INSERT INTO aulas_avaliacoes (";
            $i = 1;
            pg_result_seek($fields_orig,0);
            while($fields = pg_fetch_array($fields_orig)) {
                if ($fields[0] <> "Chave") {
                $str .= "\"".$fields[0]."\"";
                    if ($i < $count){
                        $str .= ", ";
                    }
                }
                $i++;
            }
            $str .= ") VALUES(";
            $i = 1;
            pg_result_seek($fields_orig,0);
            while($fields = pg_fetch_array($fields_orig)) {
                //prepara campos especiais
                if($orig['Tipo']=='') $orig['Tipo'] = 'MP';
                if(empty($orig['Min'])) $orig['Min'] = 'NULL';
                if(empty($orig['Max'])) $orig['Max'] = 'NULL';
                //prepara a clausula VALUES
                switch($fields[0]) {
                    case "CodCurso" 	: $str .= $Destino.", "; break;
                    case "CodAula" 	: $str .= $NovaAula.", "; break;
                    case "IndexQuestao" : $str .= $orig['IndexQuestao'].", "; break;
                    case "Questao" 	: $str .= "'".$orig['Questao']."', "; break;
                    case "Peso"		: $str .= $orig['Peso']; break;
                    case "Tipo" 	: $str .= ", '".$orig['Tipo']."', "; break; 
                    case "Min"          : $str .=  $orig['Min'].", "; break;
                    case "Max"          : $str .= $orig['Max']; break;
                }
                $i++;
            }
            $str .= ")";
            //echo "$str<br>";
            //grava	a nova questão
            //echo "<br> $str";
            $res = pg_query($str);
            //insere as alternativas
            $sql = pg_query("SELECT \"Chave\" FROM aulas_avaliacoes WHERE \"CodAula\" = $NovaAula AND \"IndexQuestao\" = ".$orig['IndexQuestao']." ORDER BY \"Chave\" DESC ");
            $res = pg_fetch_array($sql);
            $CodAvaliacao = $res[0];
            $sqlAlts = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAvaliacao\" = ".$orig['Chave']);
            while ($alts = pg_fetch_array($sqlAlts)) {
                $str = "INSERT INTO aulas_avaliacoes_alternativas (\"CodAula\", \"CodAvaliacao\", \"IndexAlternativa\", \"Alternativa\", \"Resposta\") VALUES($NovaAula, $CodAvaliacao, ".$alts['IndexAlternativa'].", '".$alts['Alternativa']."', ".$alts['Resposta'].")";
                //echo $str."<br>";
                $res = pg_query($str);
            }
        }
        $this->showNovaQuestao($Destino, $NovaAula, $nmAula);
    }//Executa($Origem, $Destino) {

    function showNovaQuestao($Destino, $NovaAula, $nmAula) {
        //exibe o nome do curso
	$sql = pg_query("SELECT \"Nome\" FROM cursos WHERE \"Chave\" = $Destino");
	$r = pg_fetch_array($sql);
	?>
	<div align="center">
	<table class="tablemenu">
	<tr>
	<td align="center">
	<?php echo "Curso: ".$r[0] ?>
	</td>
	</tr>
	</table>
	<table> <tr> <td> </td> </tr> </table>
	<?php
	//exibe o nome da nova aula
	?>
	<table class="tablemenu">
	<tr>
	<td align="center">
	<?php echo "Nova Aula: ".$nmAula ?>
	</td>
	</tr>
	</table>
	<table class="tablemenuAdm">
	<tr>
	<td width="20" align="center" class="tdmenuAdm"><b>Id</b></td>
        <td align="left" class="tdmenuAdm"><b>Quest&atilde;o</b></td>
	<td width="40"align="center" class="tdmenuAdm"><b>Peso</b></td>
	<td align="center" width="20" class="tdmenuAdm"></td>
	</tr>
	</table>
	
	<table class="tabValores">
	<?php
	$c1 = "#FFFFFF";
	$c2 = "#CCCCCC";
	$c = 0;
	//echo "<br> Cod Aula: $codaula";
	if ($sql =  pg_query("SELECT \"Chave\", \"IndexQuestao\", \"Questao\", \"Peso\" 
            FROM aulas_avaliacoes 
            WHERE \"CodAula\" = $NovaAula ORDER BY \"IndexQuestao\" ") ) {
            //LIMIT $inicio , $inicio10
            while ($r = pg_fetch_array($sql)){
                echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> <td width=\"20\" align=\"center\">".$r['IndexQuestao']."</td>
                <td  align=\"left\">".$r['Questao']."</td>
                <td align=\"center\">".$r['Peso']."</td>
                <td align=\"center\" width=\"20\"> 
                </td>
                </tr>";
            } //while
	} 	//if
	// botão voltar
	?>
	</table>
	<table class="tablemenu">
	<tr>
	<form id="VoltarCapa" name="VoltarCapa" method="post">
	<td align="right" valign="middle">
	<input name="OdimVoltar" type="submit" id="OdimVoltar" value="Voltar" class="botaoOdim"/>						
	</td>
	<td width="15"></td>
	</form>
	</tr>
	</table>
	
	</div>
	<?php					
    }
    function put_msg($msg){
        ?>    
        <div align="center">
            <table class="tbform" width="550">
            <tr>
            <td align="center"><font color="red">
                <b><?php echo $msg ?></b>
            </td>
            </tr>
            </table>
        </div>
        <br>
        <?php
    }
	
}
?>