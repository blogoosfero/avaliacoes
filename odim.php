<?php
class Odim {
    var $db;
    
    public function login($user, $pass){
		global $db;	
		if(strlen($user) >= 3 && strlen($pass) >= 3) {
			$u = $user;
			$p = md5($pass);
                        
	
			$db = new avdb;
			//echo "SELECT Pass FROM usuarios WHERE Nome = '$u'<br>";
			$log = $db->logSQL($u, $p);
                        //echo "<br>LOG-> $log<br>";
			return $log;
		} else {
			return 0;
		}
		 
	
	} //login   
        
        public function capa($nomeuser, $userid){
            global $db;
            $db = new avdb;
            $this->put_testeMail();
            $this->put_menu($nomeuser);
            $this->put_find_cursos('');
            $this->put_find_users('');
            
            //$this->put_lista_cursos();
            //$this->put_lista_usuarios();
        }
        
        
        public function findcurso( $nomeuser, $iduser, $find, $CodCurso, $funcao, $msg, $var){
            global $db;
            $db = new avdb;
            $this->put_menu($nomeuser);
            $this->put_find_cursos($find);
            $this->listacursos($find, $CodCurso, $funcao, $msg, $var);
            $this->put_find_users('');
        }
        
        public function findusers( $nomeuser, $iduser, $usfind, $Coduser, $funcao, $msg, $var){
            global $db;
            $db = new avdb;
            $this->put_menu($nomeuser);
            $this->put_find_cursos('');
            $this->put_find_users($usfind);
            $this->listausers($usfind, $Coduser, $funcao, $msg, $var);
            
        }
        
        function put_testeMail(){
            ?>
            <!--
            <form name="mail" method="post">
                de:<br/>
                <input type="text" name="de" value=""><br/>
                para:<br/>
                <input type="text" name="para" value=""><br/>
                msg:<br/>
                <textarea cols="54" rows="6" name="msg"></textarea><br/>
                <input type="submit" name="sendmail" value="post">
            </form>
            -->
            <?php
        }
        
	function put_menu($nomeuser) {
			//echo "<br>".$nmUser;
            ?>			
            <div align="center">
            <table class="tablemenu"> 
            <tr>					  
            <td width="330" align="left" class="tdmenu" ><b> Bem vindo, <?php echo($nomeuser) ?> </b></td>
            <td align="right" class="tdmenu" >Novo</td>					    
            <form id="Cursos" name="cursos" method="post" action="">
            <td width="70" align="center" valign="middle" class="tdmenu"> 
                <input name="OdimCursos" type="submit" value="Curso" class="botaoOdim"/> </td>
            </form>
            <form id="Usuarios" name="Usuarios" method="post" action="">
            <td width="70" align="center" valign="middle" class="tdmenu">
                <!--
                <input name="OdimUsuarios" type="submit" value="Usuario" class="botaoOdim"/>
                -->
                </td>
                
            </form>
            <form id="Usuarios" name="Usuarios" method="post" action="">
            <td width="50" align="center" valign="middle" class="tdmenu">
                <input name="Sair" type="submit" value="Sair" class="botaoOdim"/> </td>
            </form>
            </tr>			  
            </table>
				
            <table class="tblinha">
            <tr>
            <td>&nbsp;</td>
            </tr>
            </table>
            <table class="tblinha" >
                <?php
                global $db;
                $db->Conn_av();
                $refaz = pg_query("SELECT * FROM aulas_avaliacoes_obs WHERE \"Refazer\"='t' AND \"Delatado\" = 0 ");
                while ($re = pg_fetch_array($refaz)){
                ?>
                    <tr>
                        <td align="right">
                            <font color="red">
                            Marcado(s) para refazer:<?php echo "Aula: ".$re['CodAula']." Aluno:".$re['CodAluno'] ?>
                            </font>
                        </td>
                        <td width="20px">
                            <form name="deletaenvio" method="post" onsubmit="gettopo();">
                                <input type="hidden" name="CodCurso" value="<?php echo $re['Chave'] ?>">
                                <input type="hidden" name="CodCurso" value="<?php echo $re['CodCurso'] ?>">
                                <input type="hidden" name="Aluno" value="<?php echo $re['CodAluno'] ?>">
                                <input type="hidden" name="Aula" value="<?php echo $re['CodAula'] ?>">
                                <input type="hidden" name="monitor" value="<?php echo $re['CodMonitor'] ?>">
                                <input type="submit" class="btnRefaz" name="deleta"  title="Deleta" value=""/>
                            </form>                            
                        </td>
                    
                    </tr>
                <?php
                }
                ?>
            </table>
            <?php
            //exibe mensagens dos monitores
            /*$db = new avdb;
            $db->Conn_av();
            $sql = pg_query("SELECT * FROM aulas_avaliacoes_obs WHERE \"Refazer\" = TRUE");
            while($r = pg_fetch_array($sql){
                
            }*/
            
            ?>
            </div>
				
            <?php
	} // fim do put menu
        
        function put_find_cursos($find){
            //exibe o nÂº de cursos cadastrados
            global $db;
            $db = new avdb;
            $db->Conn_av();
            //$sql = "SELECT COUNT(Chave)FROM cursos";
            $res = pg_query("SELECT count_cursos('Chave')");
            $r = pg_fetch_row($res);
            //echo "<br>contar de cursos=".$r[0]."<br>";
            ?>
            <div align="center">
                <table class="tabTitle">
                    <tr>
                        <td>
                        <b><?php echo($r[0]) ?> cursos cadastrados</b>
                        </td>
                    </tr>
                </table>
                
                <form name="findcurso" method="post">
                <table class="tablemenu">
                  <tr>
                        <td width="250px">
                            Nome do curso:<br/>
                            <input type="search" name="find" value="<?php echo $find ?>" size="60">
                        </td>
                        <td align="left" style="padding-left: 20px;">
                            <input type="submit" class="btnfind" name="LocalizaCurso" title="Localizar Curso" >    
                        </td>
                  </tr>
                </table>
                </form>
    
            </div>


        <?php    
        }
        
        function put_find_users($usfind){
            //exibe o nÂº de cursos cadastrados
            global $db;
            $db = new avdb;
            $db->Conn_av();
            //$sql = "SELECT COUNT(Chave)FROM usuarios";
            $res = pg_query("SELECT count_usuarios('Chave')");
            $r = pg_fetch_row($res);
            //echo "<br>contar de cursos=".$r[0]."<br>";
            ?>
            
            <div align="center">
                <table class="tabTitle">
                    <tr>
                        <td>
                            <b><?php echo($r[0]) ?> usu&aacute;rios cadastrados</b>
                        </td>
                    </tr>
                </table>
                
                <form name="finduser" method="post">
                <table class="tablemenu">
                  <tr>
                        <td width="250px">
                            Nome do usuario:<br/>
                            <input type
                            <input type="search" name="usfind" value="<?php echo $usfind ?>" size="60">
                        </td>
                        <td align="left" style="padding-left: 20px;">
                            <input type="submit" class="btnfind" name="LocalizaUsuario" title="Localizar Usuarios" >    
                        </td>
                  </tr>
                </table>
                </form>
    
            </div>
        <?php    
        }
        
        function listacursos($find, $CodCurso, $funcao, $msg, $var){
            $db = new avdb;
            $db->Conn_av();
            //$sql = "SELECT COUNT(Chave)FROM usuarios";
            ?>
            <div align="center">
            <table class="tablemenu">
		<tr>
		<td width="43" align="center" class="tdmenu"><b><font size="2" color="#FFFFFF">Id</font></b></td>
		<td align="left" class="tdmenu"><b>Curso</b></td>
		<td align="center" width="80" class="tdmenu"><b>Responsavel</b></td>
                <td align="center" width="100" class="tdmenu">&nbsp;</td>
		</tr>
            </table>
                
            <table class="tabValores">			
            <?php
                $c1 = "#FFFFFF";
		$c2 = "#CCCCCC";
                $class1 = "botaobco";
		$class2 = "botaocinza";

		$c = 0;
		$class = 0;
            $res = pg_query("SELECT * FROM cursos WHERE \"Nome\" ILIKE '%$find%' ORDER BY \"Nome\"");
            while($r = pg_fetch_array($res)){
                ?>
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>"> 
                    <td width="43px">
                        <?php echo $r['Chave']?>                        
                    </td>
                    <td>
                        <?php echo $r['Nome']?>                        
                    </td>
                    <td width="80">
                        <?php echo $r['Responsavel'] ?>
                    </td>
                    <form name="edita" method="post" onsubmit="gettopo();">
                    <td width="20">
                        <input type="hidden" name="filtro" value="<?php echo $find ?>">
                        <input type="hidden" name="codcurso" value="<?php echo $r['Chave'] ?>">
                        <input type="submit" class="btnEdita" name="EditaCurso" title="Editar Curso" value="<?php echo $r['Chave'] ?>">
                    </td>
                    </form> 
                    <form name="Clona" method="post" onsubmit="gettopo();">
                    <td width="20">
                        <input type="hidden" name="filtro" value="<?php echo $find ?>">
                        <input type="hidden" name="codcurso" value="<?php echo $r['Chave'] ?>">
                        <input type="submit" class="btnClone" name="ClonaCurso" title="Clonar Curso" value="<?php echo $r['Chave'] ?>">
                    </td>
                    </form> 
                    <form name="adm" method="post" onsubmit="gettopo();">
                    <td width="20">
                        <input type="submit" class="btnadm" name="admCurso" title="Administrar Curso" value="<?php echo $r['Chave'] ?>">
                    </td>
                    </form> 
                    <form name="aluno" method="post" onsubmit="gettopo();">
                    <td width="20">
                        <input type="submit" class="btnaluno" name="AlunoCurso" title="Aluno Curso" value="<?php echo $r['Chave'] ?>">
                    </td>
                    </form> 
                        
                </tr>
            <?php    
                //insere as funções caso tenham sido chamadas
                if ($r['Chave'] == $CodCurso){
                    if ($funcao == 'EditaCurso'){
                        $sql = pg_query("SELECT * FROM cursos WHERE \"Chave\" = $CodCurso");
                        $s = pg_fetch_array($sql);                                               
                        ?><tr><td colspan="7" align="center"> <?php
                        $this->Edita_Curso($CodCurso, $s['Nome'], $s['Responsavel'], $msg, $find);
                        ?></td></tr> <?php
                    }
                    if ($funcao == 'ClonaCurso'){
                        $sql = pg_query("SELECT * FROM cursos WHERE \"Chave\" = $CodCurso");
                        $s = pg_fetch_array($sql);                                               
                        ?><tr><td colspan="7" align="center"> <?php
                        $this->Clona_Curso($CodCurso, $s['Nome'], $s['Responsavel'], $msg, $find, $var);
                        ?></td></tr> <?php                        
                    }
                }
            }
            ?>
            </table>    
            </div>
        <?php    
        } //listacursos
        
        function listausers($usfind, $Coduser, $funcao, $msg, $var){
            $db = new avdb;
            $db->Conn_av();
            //$sql = "SELECT COUNT(Chave)FROM usuarios";
            ?>
            <div align="center">
            <table class="tablemenu">
		<tr>
		<td width="43" align="center" class="tdmenu"><b><font size="2" color="#FFFFFF">Id</font></b></td>
                <td align="left" class="tdmenu"><b>Usu&aacute;rio</b></td>
                <td align="center" width="150" class="tdmenu"><b>N&iacute;vel</b></td>
		</tr>
            </table>
                
            <table class="tabValores">			
            <?php
                $c1 = "#FFFFFF";
		$c2 = "#CCCCCC";
                $class1 = "botaobco";
		$class2 = "botaocinza";

		$c = 0;
		$class = 0;
            $res = pg_query("SELECT * FROM usuarios WHERE \"Nome\" ILIKE '%$usfind%' ORDER BY \"Nome\"");
            while($r = pg_fetch_array($res)){
                ?>
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>"> 
                    <td width="43" align="center">
                        <?php echo $r['Chave'] ?>
                    </td>
                    <td align="left">
                        <a href="<?php echo $_SERVER['REQUEST_URI']."?email=".$r['Mail']."&identifier=".$r['ID']."&name=".$r['Nome']?>" target="_black">
                        <?php echo $r['Nome'] ?>
                        </a>
                    </td>
                    <form name="ChangeStatus" method="post" onsubmit="gettopo();">
                        <td align="center" width="150">
                            <select size="1" name="nivel" >
                                <?php
                                if ($r['Nivel']== 2) {
                                    ?> <option value="2" selected>Aluno</option>
                                       <option value="1">Monitor</option> <?php
                                } else if ($r['Nivel']== 1){ 
                                    ?> <option value="2" >Aluno</option>
                                       <option value="1" selected>Monitor</option> <?php
                                } 
                                ?>
                            </select>
                            <input type="hidden" name="usfind" value="<?php echo $usfind ?>">
                            <input type="hidden" name="ID" value="<?php echo $r['Chave'] ?>">
                            <input type="submit" name="AlteraNivel" value="post">
                                
                        </td>  
                    </form>
                    
                </tr>
            <?php    
            }
            ?>
            </table>
            <?php
        }
        
        function Clona_Curso($CodCurso, $Nome, $responsavel, $msg, $find, $var){
            //lista alulas e questionários do curso selecionado
            ?>
            
            <table class="tabValores"  style="width: 520px">
                <tr bgcolor="#FFFFCC">
                    <td width="30"><b>ID</b></td>
                    <td width="50px"><b>Curso</b></td>
                    <td><b>Aula</b></td>
                    <td width="80px"><b>Limite</b></td>
                    <td width="50px"><b>Min.</b></td>
                </tr>
            <?php    
            $aulas = pg_query("SELECT * FROM aulas WHERE \"CodCurso\" = $CodCurso ORDER BY \"Nome\"");          
            while($a = pg_fetch_array($aulas)){
                ?>
                <tr bgcolor="#ffcc99">
                    <td width="30px"><?php echo $a['Chave'] ?> </td>
                    <td width="50px"><?php echo $a['CodCurso'] ?> </td>
                    <td><?php echo $a['Nome'] ?> </td>
                    <td width="80px" align="center"><?php echo date('d/m/Y',  strtotime($a['DataLimite'])) ?> </td>
                    <td width="50px" align="center"><?php echo $a['MinimoMP'] ?> </td>
                </tr>
                <tr>
                    <td colspan="5" align="center">
                <?php
                $avali = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodAula\"= ".$a['Chave']." ORDER BY \"IndexQuestao\" "); 
                while($av = pg_fetch_array($avali) ){
                    ?> 
                        <table class="tblinha" style="width:500px"><tr><td><b>Quest&atilde;o:</b></td></tr></table>
                        <table class="tbform"  style="width:500px; background-color:#ffcc66;">
                            <tr>
                                <td width="250" align="left"><b><?php echo $av['IndexQuestao'].":" ?></b></td>
                                <td align="right"><b><?php echo "Tipo: ".$av['Tipo'] ?></b></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo nl2br($av['Questao']) ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right"><b><?php echo "Peso: ".$av['Peso'] ?></b></td>
                            </tr>
                        </table>
                    <?php
                    if($av['Tipo'] == 'MP' || $av['Tipo'] == ''){
                        ?>
                        <table class="tblinha" style="width:500px"><tr><td><b>Alternativas:</b></td></tr></table>
                        <?php
                        $alt = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAvaliacao\" =".$av['Chave']." ORDER BY \"IndexAlternativa\"");
                        while($al = pg_fetch_array($alt)){
                            ?>
                            <table class="tbform" style="width:500px">
                                <tr>
                                    <td width="250" align="left"><b><?php echo $al['IndexAlternativa'].":" ?></b></td>
                                    <td align="right"><b><?php echo ($al['Resposta'] == 1 ?"Verdadeiro":"Falso") ?></b></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php echo nl2br($al['Alternativa']) ?></td>
                                </tr>
                            </table>
                            <?php
                        }
                        ?><table class="tblinha" style="width:500px"><tr><td></td></tr></table><?php
                    }
                }
                ?>
                    </td>
                </tr>
                <?php
            }
            $NomeCurso = $var[0];
            $nomeresponsavel = $var[1];
            $dataLimite = $var[2];
            
            ?>
               
                <tr><td colspan="5" align="center">
                        <table class="tblinha" style="width:500px"><tr><td align="center"><b>
                                        Clonar em:
                                    </b></td></tr></table>        
                        <table class="tbform" style="background-color: #ffcc66">
                    <form id="Clonacurso" method="post" onsubmit="gettopo();" >
                        <tr>
                            <td width="10" >&nbsp;</td>
                            <td colspan="2" align="left">Nome do Novo Curso:<br/> 
                            <input type="text" name="NomeCurso" size="45" value="<?php echo $NomeCurso ?>"></td>     
                            <td width="50" >&nbsp;</td> 
                        </tr>
                        <tr>
                        <td width="10" >&nbsp;</td>
                        <td colspan="2" align="left">Responsavel:<br>
                            <select size="1" name="responsavel">
                            <option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                        <?php
                        //insere os valores
                        //echo "<br> SELECT Chave, Nome FROM usuarios WHERE Nivel = 0 or Nivel=1";
                        //SELECT Chave, Nome FROM usuarios WHERE Nivel = 0 OR Nivel=1"
                        if ($res = pg_query("SELECT \"Chave\", \"Nome\" FROM usuarios WHERE \"Nivel\" <= 1 ORDER BY \"Nome\"")){
                            //echo "<br> SQL OK";
                            while ($r = pg_fetch_array($res)) {
                                //echo "<br> $r[1]";
                                if ($nomeresponsavel == $r[0]) {
                                    echo  "<option value=\"$r[0]\" selected>$r[1]</option>";
                                } else { 
                                    echo "<option value=\"$r[0]\">$r[1]</option>";
                                } 
                            }
                        }
                        ?>
                            </select> </td> 
                            
                            <td width="10">&nbsp;</td>
                            </tr>
                        <tr>
                            <td width="10" >&nbsp;</td>
                            <td align="left">
                                Data Limite(dd/mm/aaaa:<br/>
                                <input id="datalimite" type="text" name="DataLimite" value="<?php echo $dataLimite ?>" 
                                       OnKeyUp="mascaraData(this);" maxlength="10">                                
                            </td>
                            <td align="right">
                                <input type="hidden" name="filtro" value="<?php echo $find ?>">
                                <input type="hidden" name="CodCurso" value="<?php echo $CodCurso ?>">
                                <input type="submit" name="OdimClonarCurso" value="Clonar em novo curso">
                            </td>
                            <td width="10" >&nbsp;</td> 
                                        
                        </tr>
                        <tr>
                            <td width="10" >&nbsp;</td> 
                            <td colspan="2"><b><font color="red"> <?php echo $msg ?></font></b></td>
                            <td width="10" >&nbsp;</td>
                        </tr>
                    </form>
                      
                </table> 
                </td></tr> 
                
            
            </table> 
            
            <?php
        }


        function put_lista_cursos() {
            ?>
            <div align="center">
            <table class="tabTitle">
            	<tr>
                <?php
		//exibe o nÂº de cursos cadastrados
                global $db;
                $db = new avdb;
                $db->Conn_av();
		//$sql = "SELECT COUNT(Chave)FROM cursos";
                $res = pg_query("SELECT count_cursos('Chave')");
		$r = pg_fetch_row($res);
                //echo "<br>contar de cursos=".$r[0]."<br>";
		?>
		<td><b><?php echo($r[0]) ?> cursos cadastrados</b></td>
		</tr>
            </table>
            <table class="tablemenu">
		<tr>
		<td width="43" align="center" class="tdmenu"><b><font size="2" color="#FFFFFF">Id</b></td>
		<td align="left" class="tdmenu"><b>Curso</b></td>
		<td align="center" width="80" class="tdmenu"><b>Responsavel</b></td>
                <td align="center" width="40" class="tdmenu">&nbsp;</td>
		</tr>
            </table>
            <table class="tabValores">			
            	<?php
                $c1 = "#FFFFFF";
		$c2 = "#CCCCCC";
                $class1 = "botaobco";
		$class2 = "botaocinza";

		$c = 0;
		$class = 0;
		//$inicio10 = $inicio + 100;
		//echo "<br>  SELECT Chave, Nome, Responsavel FROM cursos ORDER BY Chave DESC LIMIT $inicio ,  $inicio10";
		$res = $db->runSEL('lista_cursos()'); 
               //var_dump($res);
                //echo "<br>".$res;
                foreach ($res as $r) {
                //while($r == pg_fetch_array($res)) {
                    //echo $r; tr bgcolor=".(($c++&1)?$c1:$c2)."
                        echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> <td width=\"43\">".$r['chave']."</td> 
                            <form id=\"Exp\" name=\"Exp\" method=\"post\">
                            <input type=\"hidden\" name=\"codCurso\" value=\"".$r['chave']."\" />
                            <td align=\"left\">
                            <input name=\"OdimExporta\" type=\"submit\" value=\"".$r['nome']."\" class=\"".(($class++&1)?$class1:$class2)."\" />
                            </td>
                            </form>
                            <td width=\"80\" align=\"center\">".$r['responsavel']."</td>
                            <form id=\"EditaCurso\" name=\"EditaCurso\" method=\"post\">
                            <td width=\"40\" align=\"center\">  
                            <input type=\"submit\" class=\"btnEdita\" name=\"EditaCurso\" title=\"Editar Curso\" alt=\"Edita Curso\" value=\"$r[chave]\">    
                            </td>
                            </form>
                            </tr>";
		}
		?>
		</table>
		</div>
					
		<div align="center"> 
		</table>
		<table class="tabTitle">
		<form id="MoreCursos" name="Morecursos" method="post" action="">
				  
		<tr><td align="right">
		<!--
		<input name="MoreCursos" type="submit" id="MoreCursos" value="Mais 100" class="botaoOdim" />	
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

			  
		</div>  
			  
			  
			  
	</div>
	<?php	
	}
        
        
        function put_lista_usuarios() {
            ?>
            <div align="center">
            <table class="tabTitle">
                <tr>
		<?php
		 //exibe o nÂº de cursos cadastrados
                $db = new avdb;
                $db->Conn_av();
		/*$sql = "SELECT COUNT(Chave)FROM usuarios";
		$res = mysql_query($sql)or die(mysql_error());
		$r = mysql_fetch_row($res);*/
		?>
                    <td><b><?php echo($db->count('Chave', 'usuarios')) ?> usu&aacute;rios cadastrados</b></td>
		</tr>
            </table>
            <table class="tablemenu">
                <tr>
		<td width="43" align="center" class="tdmenu"><b><font size="2" color="#FFFFFF">Id</b></td>
		<td align="left" class="tdmenu"><b>Nome</b></td>
		<td align="center" width="80" class="tdmenu"><b>N&iacute;vel</b></td>
		</tr>
            </table>
			
            <table class="tabValores">
            <?php
            //preenche 10 Ãºltimas linhas da tabela Cursos
						
            $c1 = "#FFFFFF";
            $c2 = "#CCCCCC";
            $c = 0;
            
            //echo "<br>  SELECT Chave, Nome, Responsavel FROM cursos ORDER BY Chave DESC LIMIT $inicio ,  $inicio10";
            $db->Conn_av();
            $res = pg_query("SELECT * FROM usuarios ORDER BY \"Nome\" ");
            
            //echo "<br>".$res;
            while($r=pg_fetch_array($res)) {
                //echo $r; tr bgcolor=".(($c++&1)?$c1:$c2)."
 		echo "<tr bgcolor=\"".(($c++&1)?$c1:$c2)."\"> <td width=\"43\">".$r['Chave']."</td>
		<td align=\"left\">".$r['ID']." / ".$r['Nome']."</td>
		<td width=\"80\">".$r['Nivel']."</font></td></tr>";
		//echo "<tr border=\"1\" border color=\"#000080\><td width=\"43\">". $r['Chave']."</td><td>".$r['Nome']."</td><td width=\"226\">".$r['Responsavel']."</td></tr>";
            }
            $db->close_conn_av();
            ?>
            </table>
            </div>
					
            <div align="center"> 
            
            <table class="tabTitle">
            <form id="MoreCursos" name="Morecursos" method="post" action="">
                <td align="right">
                <!--
                <input name="MoreCursos" type="submit" id="MoreCursos" value="Mais 100" class="botaoOdim" />
                -->
                </td></tr>
            </form>
            </table>
            <table class="tblinha">
                <tr>
		<td>&nbsp;</td>
		</tr>
            </table>
            </div>
        <?php	
	}
        
        public function new_curso($login, $coduser, $NomeCurso, $nomeresponsavel, $msg) {
            global $db;
            $db = new avdb;
            $db->Conn_av();
			
            ?>
            <div align="center">
            <table class="tablemenu"> 
                <tr>					  
                <td width="330" align="left"  class="tdmenu"><b> Cadastro de Cursos </b></td>
					    
		<form id="voltar" name="voltar" method="post" action="">
		<td width="70" align="center" valign="middle"><input name="OdimVoltar" type="submit" value="Voltar" class="botaoOdim"/> </td>
		</form>
		</tr>			  
		</table>
				
                <table class="tblinha">
		<tr>
		<td>&nbsp;</td>
		</tr>
		</table>
			
		<table class="tbform">
		<form id="novocurso" name="novocurso" method="post" action="" >
		<tr>
                <td width="50" >&nbsp;</td>
                <td align="left">Nome do Curso:<br> 
                <input type="text" name="NomeCurso" size="45" value="<?php echo $NomeCurso ?>"></td>     
                <td width="50" >&nbsp;</td> 

                </tr>
		<tr>
                <td width="50" >&nbsp;</td>
                <td align="left">Responsavel:<br>
                <select size="1" name="nomeresponsavel">
		<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
            <?php
                //insere os valores
		//echo "<br> SELECT Chave, Nome FROM usuarios WHERE Nivel = 0 or Nivel=1";
                //SELECT Chave, Nome FROM usuarios WHERE Nivel = 0 OR Nivel=1"
		if ($res = pg_query("SELECT \"Chave\", \"Nome\" FROM usuarios WHERE \"Nivel\" <= 1 ORDER BY \"Nome\"") ){
                //echo "<br> SQL OK";
                    while ($r = pg_fetch_array($res)) {
                        //echo "<br> $r[1]";
                        if ($nomeresponsavel == $r[0]) {
                            echo  "<option value=\"$r[0]\" selected>$r[1]</option>";
                        } else { 
                            echo "<option value=\"$r[0]\">$r[1]</option>";
                        } 
                    }
                }
            ?>
                </select> </td> 
                <td width="50">&nbsp;</td>
                </tr>
                <tr>
                <td width="50" >&nbsp;</td>
                <td align="right"><input type="submit" name="OdimSalvarCurso" value="Salvar"></td>
                <td width="50" >&nbsp;</td> 
                                        
                </tr>
		</form>	
                </table> 
		<table class="tblinha">
		<tr>
		<td><font color="#FF0000"><?php echo $msg; ?></font> </td>
		</tr>
		</table>
            <?php
	}//novo curso
        
        public function Edita_Curso($codCurso, $NomeCurso, $nomeresponsavel, $msg, $find){
            global $db;
            $db = new avdb;
            $db->Conn_av();
			
            ?>
            <div align="center">
            
                <!--
                <table class="tablemenu"> 
                    <tr>					  
                        <td width="330" align="left"  class="tdmenu"><b> Cadastro de Cursos </b></td>
                	<form id="voltar" name="voltar" method="post" action="">
                        <td width="70" align="center" valign="middle"><input name="OdimVoltar" type="submit" value="Voltar" class="botaoOdim"/> </td>
                        </form>
                    </tr>			  
                </table>
                -->
				
                <table class="tblinha">
                    <tr> <td></td> </tr>
		</table>
			
		<table class="tbform">
		<form id="novocurso" name="novocurso" method="post" onsubmit="gettopo();" >
		<tr>
                <td width="50" >&nbsp;</td>
                <td align="left">Nome do Curso:<br> 
                <input type="text" name="NomeCurso" size="45" value="<?php echo $NomeCurso ?>"></td>     
                <td width="50" >&nbsp;</td> 

                </tr>
		<tr>
                <td width="50" >&nbsp;</td>
                <td align="left">Responsavel:<br>
                <select size="1" name="nomeresponsavel">
		<option value="0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
            <?php
                //insere os valores
		//echo "<br> SELECT Chave, Nome FROM usuarios WHERE Nivel = 0 or Nivel=1";
		if ($res = pg_query("SELECT \"Chave\", \"Nome\" FROM usuarios WHERE \"Nivel\" <= 1 ORDER BY \"Nome\"")){
                //echo "<br> SQL OK";
                    while ($r = pg_fetch_array($res)) {
                        //echo "<br> $r[1]";
                        if ($nomeresponsavel == $r[0]) {
                            echo  "<option value=\"$r[0]\" selected>$r[1]</option>";
                        } else { 
                            echo "<option value=\"$r[0]\">$r[1]</option>";
                        } 
                    }
                }
            ?>
                </select> </td> 
                <td width="50">&nbsp;</td>
                </tr>
                <tr>
                <td width="50" >&nbsp;
                <input type="hidden" name="codCurso" value="<?php echo $codCurso ?>">
                <input type="hidden" name="find" value="<?php echo $find ?>">
                </td>
                <td align="right"><input type="submit" name="OdimUPDATECurso" value="Salvar"></td>
                <td width="50" >&nbsp;</td> 
                                        
                </tr>
		</form>	
                </table> 
		<table class="tblinha">
		<tr>
		<td><font color="#FF0000"><?php echo $msg; ?></font> </td>
		</tr>
		</table>
            <?php
        }//edita Curso

        
	public function new_usuario($login,$coduser, $NomeNewUser, $mailUser, $nivel, $senha, $altSenha,  $msg) {
	
            ?>
                
		<div align="center">
		<table class="tablemenu"> 
                    <tr>					  
                    <td width="330" align="left"  class="tdmenu"><b> Cadastro de Usuários </b></td>
					    
                    <form id="voltar" name="voltar" method="post" action="">
                    <td width="70" align="center" valign="middle"><input name="OdimVoltar" type="submit" value="Voltar" class="botaoOdim"/> </td>
                    </form>
                    </tr>			  
                    </table>
				
                    <table class="tblinha">
                    <tr>
                    <td>&nbsp;</td>
                    </tr>
                    </table>
				
                    <table class="tbform">
                    <form id="novoUsuario" name="novoUsuario" method="post" action="" >
                    <tr> 
                    <td width="50" >&nbsp;</td>
                    <td align="left">Nome do Usuario:<br> 
                    <input type="text" name="NomeUsuario" size="45" value="<?php echo $NomeNewUser ?>"></td>     
                    <td width="50" >&nbsp;</td> 
                    </tr>
                    <tr>
                    <td width="50" >&nbsp;</td>
                    <td align="left">e-mail:<br> 
                    <input type="text" name="mailUsuario" size="45" value="<?php echo $mailUser ?>"></td>     
                    <td width="50" >&nbsp;</td> 
                    </tr>
                    <tr>
                    <td width="50" >&nbsp;</td>
                    <td align="left">N&iacute;vel:<br>
                    <select size="1" name="Nivel">
                        
          <?php
                //insere os valores
		//if ($nivel == '0') {
		//	echo "<option value=\"0\" selected>Odim</option>";
		//}else {
		//	echo "<option value=\"0\">Odim</option>";
		//}
		echo "<option value=\"2\" selected>Aluno</option>";
		if ($nivel == '1') {
                    echo "<option value=\"1\" selected>Administrador</option>";
		}else {
                    echo "<option value=\"1\">Administrador</option>";
		}
		if ($nivel == '2') {
                    echo "<option value=\"2\" selected>Aluno</option>";
		}else {
                    echo "<option value=\"2\">Aluno</option>";
		}
          ?>
            </select> </td> 
            <td width="50">&nbsp;</td>
            </tr>
            <!--
            <tr>
            <td width="50" >&nbsp;</td>
            <td align="left">
                Senha:<br> 
                <input type="text" name="senhaUser" size="20" value="">
            </td>     
            <td width="50" >&nbsp;</td> 
            </tr>
            <tr>
            <td width="50" >&nbsp;</td>
            <td align="left">
                Aviso para alteração da Senha: 
                <input type="checkbox" name="AltSenha" checked>
            </td>     
            <td width="50" >&nbsp;</td> 
            </tr>
            -->
            <tr>
            <td width="50" >&nbsp;</td>
            <td align="right"><input type="submit" name="OdimSalvarUser" value="Salvar"></td>
            <td width="50" >&nbsp;</td> 
                                        
            </tr>
            </form>	
            </table> 
            <table class="tblinha">
            <tr>
            <td><font color="#FF0000"><?php echo $msg; ?></font> </td>
            </tr>
            </table>
        <?php
	
	}
	
        

    }   
    ?>
