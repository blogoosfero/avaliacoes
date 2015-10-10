<?php
    class reladm {
        public function addgrupo($vars){
            $db = new avdb;
            $db->Conn_av();
            $this->put_menu($vars);
            $this->put_newgrupo($vars);
            
        }
        
        public function capa($vars){
            $db = new avdb;
            $db->Conn_av();
            $this->put_menu($vars);
            $this->lista_alunos($vars);
            
        }

        function put_menu($vars){
        ?>
            <div align="center">
                <table class="tablemenuAdm"> 
		<tr>					  
                <td align="left"  class="tdmenuAdm"><b> Bem vindo, 
                    <?php echo($vars['nomeuser']) ?> </b>
                </td>
						
		<td width="100" align="center" valign="middle">&nbsp;
		</td>
                </table>
                <table class="tblinha"></table>
           </div>    
	<?php
        
        }
        
        function lista_alunos($vars){
            $aluno = 0;
            if (isset($vars['chkaluno']))
                $aluno = $vars['chkaluno'];
            //conta alunos
            $conta = pg_query("SELECT COUNT(DISTINCT(curso_alunos.\"CodUser\")) as contauser 
                FROM curso_alunos INNER JOIN usuarios ON usuarios.\"Chave\" = curso_alunos.\"CodUser\" 
                WHERE \"CodCurso\" IN (SELECT \"CodCursos\" FROM grup_curso WHERE \"CodGrup\" = ".$vars['codgrupo'].") ");
            $co = pg_fetch_array($conta);
            ?>
            <div align="center">
            <table class="tblinha">
                <tr><td></td></tr>
                <tr>
                    <td align="left">
                        <?php echo "Disciplina: ".$vars['disciplina'].", ".$co['contauser']." Alunos." ?>
                    </td>
                </tr>
            </table>
            </div>
            <?php
            $c1 = "#FFFFFF";
            $c2 = "#CCCCCC"; 
            $c = 0;
            ?>
            <div align="center">
            <table class="tablemenuAdm">
                <tr>
                    <td align="center" class="tdmenuAdm"><b>Aluno</b></td>
                    <td width="80" align="center" class="tdmenuAdm"><b>Nota</b></td>
                    <td width="80" align="center"  class="tdmenuAdm"><b>Freq.</b></td>
                    <td width="20" align="center"  class="tdmenuAdm"><img src="images/visto.gif"/></td>
                    <td width="20" align="center" class="tdmenuAdm"></td>
                </tr>
            </table>
            <table class="tabValores">
            <?php

            /*$sql = pg_query("SELECT DISTINCT(curso_alunos.\"CodUser\") as coduser, (usuarios.\"Nome\") as Nome  
                FROM curso_alunos INNER JOIN usuarios ON usuarios.\"Chave\" = curso_alunos.\"CodUser\" 
                WHERE \"CodCurso\" IN (SELECT \"CodCursos\" FROM grup_curso WHERE \"CodGrup\" = ".$vars['codgrupo'].") 
                ORDER BY usuarios.\"Nome\""); */
            $grupo = $vars['codgrupo'];
            //echo 'grupo:'.$grupo;
            $sql = pg_query("SELECT * FROM notas_frequencia_alunos($grupo)");
            while($r = pg_fetch_array($sql)){
                ?>
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td align="left"><?php echo $r['nome']?></td>
                    <td width="80" align="right"><?php echo $r['somanota'] ?></td>
                    <td width="80" align="right"><?php echo $r['freq'].'/'.$r['totfreq'] ?></td>
                    <td wirth="20" align="center"><img src="<?php echo (($r['registros'] >= $r['totfreq'])? 'images/visto.gif': 'images/NaoVisto.gif') ?>" /></td>
                    <form id="checaaluno" method="post" onsubmit="gettopo()">
                    <td width="20" align="center">
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                            <input type="hidden" name="codAluno" value="<?php echo $r['coduser'] ?>">
                            <input type="submit" class="btnEdita" name="checaAluno" title="Avalia&ccedil;&atilde;o "  value="" >
                    </td>
                    </form>
                </tr>
                <?php
                if($aluno == ($r['coduser'])){
                    ?><tr><td colspan="5"><?php
                    $this->putcursos($vars);
                    ?></td></tr><?php
                }
            }
            ?>
            </table>
            </div>    
            <?php
        }
        
        function putcursos($vars){
            $curso = 0;
            if(isset($vars['chkcurso']))
                $curso = $vars['chkcurso'];
            $c1 = "#F0E68C";
            $c2 = "#eae6bf"; 
            $c = 0;
            ?>
            
            <table class="tabValores">
                <tr bgcolor="gray">
                    <td width="10px" bgcolor="white"></td>
                    <td align="center">Unidade</td>
                    <td width="100px" align="center">Nota</td>
                    <td width="100px" align="center">Freq.</td> 
                    <td width="20px" align="center"><img src="images/visto.gif" /> </td>
                    <td width="20px"></td>
                    <td width="150px" bgcolor="white"></td>
                </tr>
            
            <?php
            //sql união com os cursos e notas e frequencia para cada curso
            //echo "SELECT * FROM grup_curso WHERE \"CodGrup\" = ".$vars['codgrupo']." ORDER BY \"CodCursos\"<br/>";
            //$sql = pg_query("SELECT * FROM grup_curso WHERE \"CodGrup\" = ".$vars['codgrupo']." ORDER BY \"CodCursos\"");
            $codGrupo = $vars['codgrupo'];
            $aluno = $vars['chkaluno'];
            //var_dump( $vars);
            //echo "SELECT * FROM notas_frequencia_cursos($codGrupo, $aluno)";
            $sql = pg_query("SELECT * FROM notas_frequencia_cursos($codGrupo, $aluno)");
            while($r = pg_fetch_array($sql)){
            ?>
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td width="10px" bgcolor="white"></td>
                    <td align="left"><?php echo $r['nomecurso'] ?></td>
                    <td align="right" width="100px"><?php echo $r['somanota'] ?></td>
                    <td align="right" width="100px"><?php echo $r['freq'].'/'.$r['totfreq'] ?></td>
                    <td wirth="20" align="center"><img src="<?php echo (($r['registros'] >= $r['totfreq'])? 'images/visto.gif': 'images/NaoVisto.gif') ?>" /></td>
                    <form id="checacurso" method="post" onsubmit="gettopo()">
                    <td width="20px">    
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                            <input type="hidden" name="codCurso" value="<?php echo $r['codcursos'] ?>">
                            <input type="submit" class="btnEdita" name="checaCursos" title="Avalia&ccedil;&atilde;o "  value="" >                    
                    </td>
                    </form> 
                    <td width="150px" bgcolor="white"></td>
                </tr>
                <?php
                if($curso == $r['codcursos']){
                    ?><tr><td colspan="7"> <?php
                    $this->putAulas($vars);
                    ?></td></tr><?php
                }
                
            }
            ?>
            </table>
               
            <?php
        }
        
        function putAulas($vars){
            $aula = 0;
            if(isset($vars['chkaula']))
                $aula = $vars['chkaula'];
            $c1 = "#a5eda4";
            $c2 = "#cbeecb"; 
            $c = 0;
            
            ?>
            <table class="tabValores">
                <tr bgcolor="gray">
                    <td width="20px" bgcolor="white"></td>
                    <td align="center">Aula</td>
                    <td width="60px" align="center">Nota</td>
                    <td width="60px" align="center">Freq.</td>
                    <td width="20px" align="center"><img src="images/visto.gif" /> </td>
                    <td width="20px"></td>
                    
                </tr>
            <?php
            //echo "SELECT \"Chave\", \"Nome\" FROM aulas WHERE \"CodCurso\" = ".$vars['chkcurso']." ORDER BY \"Nome\" <br/>";
            //$sql = pg_query("SELECT \"Chave\", \"Nome\" FROM aulas WHERE \"CodCurso\" = ".$vars['chkcurso']." ORDER BY \"Nome\"");
            $curso = $vars['chkcurso'];
            $aluno = $vars['chkaluno'];
            //echo "SELECT * FROM notas_frequencia_aulas($curso, $aluno)";
            $sql = pg_query("SELECT * FROM notas_frequencia_aulas($curso, $aluno)");
            while($r = pg_fetch_array($sql)){
                ?>
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td width="20px" bgcolor="white"></td>
                    <td align="left"><?php echo $r['nome'] ?></td>
                    <td align="right" width="60px"><?php echo $r['somanota']?></td>
                    <td align="right" width="60px"><?php echo $r['freq'].'/'.$r['totfreq'] ?></td>
                    <td wirth="20" align="center"><img src="<?php echo (($r['registros'] >= $r['totfreq'])? 'images/visto.gif': 'images/NaoVisto.gif') ?>" /></td>
                    <form id="checacurso" method="post" onsubmit="gettopo()">
                    <td width="20px">    
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                            <input type="hidden" name="codaula" value="<?php echo $r['chave'] ?>">
                            <input type="submit" class="btnEdita" name="checaAula" title="Avalia&ccedil;&atilde;o "  value="" >                    
                    </td>
                    </form> 
                    
                </tr>
                <?php
                if($aula == $r['chave']){
                    ?><tr><td colspan="6"> <?php
                    $this->putQuestoes($vars);
                    ?></td></tr><?php
                }
                
                
            }
            ?>
            </table>
            <?php           
        }
        
        function putQuestoes($vars){
            $freq = 0;
            ?><div align="center"><?php
            $sql = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodAula\" = ".$vars['chkaula']." ORDER BY \"IndexQuestao\"");
            while($r = pg_fetch_array($sql)){
                //insere a questão
                ?>
                <table class="tbform">
                    <tr>
                        <td align="left">
                            <b>Quest&atilde;o <?php echo $r['IndexQuestao'] ?></b>
                        </td>
                        <td align="center">
                            <b>Tipo: <?php echo $r['Tipo'] ?></b>
                        </td>
                        <td align="right">
                            <b><?php echo (($r['Tipo'] == 'DS')? $r['Min'].' e '.$r['Max'].' Palavras':'') ?></b>                            
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?php echo $r['Questao'] ?>
                        </td>
                    </tr>
                </table>              
                <?php
                $resposta = '';               
                $sql2 = pg_query("SELECT * FROM aulas_avaliacoes_alunos_respostas WHERE \"CodAvaliacao\" = ".$r['Chave']." AND \"CodAluno\" = ".$vars['chkaluno']);
                $r2 = pg_fetch_array($sql2);
                if(pg_num_rows($sql2) == 0 ){
                    $resposta = 'Pendente';                    
                }
                if (($r['Tipo'] == 'DS') && ($resposta != 'Pendente')){
                    $resp = preg_replace("/(\\r)?\\n/i", "<br/>",$r2['Discursiva']);
                    ?>
                    <table class="tbform">
                        <tr>
                            <td colspan="3">
                                <?php echo ($r2['Discursiva'] != ''? $resp: 'Pendente' )  ?>
                            </td>                            
                        </tr>
                        <tr>
                            <td align="left"><b>Avalia&ccedil;&atilde;o preliminar: <?php echo $r2['Nota'] ?></b></td>
                            <td></td>
                            <td align="right">
                                <b>
                                <?php 
                                include_once 'funcs.php';
                                $funcs = new funcs;
                                echo $funcs->contapalavras($r2['Discursiva'], $r['Min'], $r['Max']);
                                ?>
                                </b>
                            </td>
                        </tr>
                    </table>
                    <?php
                } else if ((($r['Tipo'] == 'MP') || ($r['Tipo'] == '')) && ($resposta != 'Pendente')){ 
                    $sqlAlt = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"Chave\" = ".$r2['CodAlternativa']);
                    $alt = pg_fetch_array($sqlAlt);
                    $resp = preg_replace("/(\\r)?\\n/i", "<br/>",$alt['Alternativa']);
                    ?>
                    <table class="tbform">
                        <tr>
                            <td align="left"><b>Alternativa:</b></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php echo $resp ?></td>
                        </tr>
                        <tr>
                            <td align="left"><b>Avalia&ccedil;&atilde;o preliminar: <?php echo (($alt['Resposta'] == 1)? $r['Peso'] : '0' ) ?></b></td>
                            <td></td>
                            <td align="right">
                               <b><?php echo (($alt['Resposta'] == 1) ? 'Correta':'Incorreta') ?> </b>
                            </td>
                        </tr>
                    </table>
                    <?php
                }
                if($resposta == 'Pendente'){
                    ?>
                    <table class="tbform">
                        <tr>
                            <td colspan="3" align="center">
                                <font color="Red"><b>Avalia&ccedil;&atilde;o Pendente</b></font>
                            </td>
                        </tr>
                    </table>
                    <?php
                }
                $f = 0; 
                $conceito = 0;
                $nota = 0;
                if($resposta != 'Pendente'){
                    $f = 1;
                    $conceito = 0;
                    if($r2['Nota'] != NULL)
                        $nota = $r2['Nota'];    
                    if($r['Tipo']== 'DS'){
                        if($r['Peso'] == 4){
                            $conceito = $r2['Nota'];
                        }
                    }else{
                        $conceito = 0;
                        $nota = (($alt['Resposta'] == 1)? $r['Peso'] : '0' );
                    }
                }
                

                        

                //verifica se já houve lançamento para a aula. Se não, lança
                $sqlFreq = pg_query("SELECT * FROM notas_frequencia WHERE \"CodGrupo\" = ".$vars['codgrupo']." AND \"CodCurso\" = ".
                        $vars['chkcurso']." AND \"CodAula\" = ".$vars['chkaula']." AND \"CodAluno\" =".$vars['chkaluno'].
                        " AND \"CodAvaliacao\" = ".$r['Chave']);
                if(pg_num_rows($sqlFreq) == 0){//insere o novo registro
                    $sqlins = pg_query("INSERT INTO notas_frequencia (\"CodGrupo\", \"NomeGrupo\", \"CodCurso\", \"CodAula\", 
                        \"CodAvaliacao\", \"CodAluno\", \"Frequencia\", \"Conceito\", \"Nota\") 
                        VALUES (".$vars['codgrupo'].", '".$vars['disciplina']."', ".$vars['chkcurso'].", ".$vars['chkaula'].
                        ", ".$r['Chave'].", ".$vars['chkaluno'].", ".$f.", ".$conceito.", ".$nota.")");
                    //executa a sql novamente para recuperar os dados
                    $sqlFreq = pg_query("SELECT * FROM notas_frequencia WHERE \"CodGrupo\" = ".$vars['codgrupo']." AND \"CodCurso\" = ".
                        $vars['chkcurso']." AND \"CodAula\" = ".$vars['chkaula']." AND \"CodAluno\" =".$vars['chkaluno'].
                        " AND \"CodAvaliacao\" = ".$r['Chave']);
                }
                $freq = pg_fetch_array($sqlFreq);
                //formulário de lançamento 
                ?>
                <form id="lancanota" method="post">
                <table class="tbform" style="background-color:#cbeecb;">
                    <tr>
                        <td align="left">Frequ&ecirc;ncia:</td>
                        <td align="left">Conceito:</td>
                        <td align="left">Nota:</td>                        
                    </tr>                       
                    <tr>
                        <td align="left">
                            <input type="checkbox" name="chFreq" value="<?php echo $freq['Frequencia'] ?>" <?php echo ($freq['Frequencia'] == 1 ? 'checked' : '') ?> >
                            
                        </td>
                        <td align="left"> 
                            <select size="1" name="conceito">
                                <option value="0" <?php echo ($freq['Conceito'] == 0 ? 'selected ':'') ?> ></option>
                                <option value="1" <?php echo ($freq['Conceito'] == 1 ? 'selected ':'') ?> >Insatisfat&oacute;rio</option>
                                <option value="2" <?php echo ($freq['Conceito'] == 2 ? 'selected ':'') ?> >Satisfat&oacute;rio</option>
                                <option value="3" <?php echo ($freq['Conceito'] == 3 ? 'selected ':'') ?> >Bom</option>
                                <option value="4" <?php echo ($freq['Conceito'] == 4 ? 'selected ':'') ?> >&Oacute;timo</option>
                            </select>
                        </td>
                        <td align="left">
                            <input type="text" size="20" name="nota" value="<?php echo $freq['Nota'] ?>">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                            <input type="hidden" name="codavaliacao" value="<?php echo $r['Chave'] ?>" >
                            <input type="hidden" name="chavefreq" value="<?php echo $freq['Chave'] ?>" >
                            <input type="submit" name="GravaNotaFreq" title="grava "  value="Grava" onclick="gettopo();">                            
                        </td>
                    </tr>
                </table>
                </form>
                
                <br/>
                <?php
            }
            
            ?></div><?php
        }
                
        function put_newgrupo($vars){
        ?>
            <div align="center">
                <form id="additgrupo" method="post" onsubmit="gettopo()">
                <table class="tabValores">
                    <tr>
                        <td width="120px">ID</td>
                        <td width="100px">CodGrupo</td>
                        <td width="100px">CodCursos</td>
                        <td>Curso</td>
                        <td width="50px"></td>
                    </tr>
                    <?php
                    if (isset($vars['chaves'])){
                      foreach($vars['chaves'] as $c => $v ){
                        //echo '****'.$c.'--'.$v.'*****<br/>';
                    ?>
                        <tr>
                            <td width="120px"><?php echo $vars['disciplina']?></td>
                            <td width="100px"><?php echo $vars['novocod']?></td>
                            <td width="100px"><?php echo $vars['chaves'][$c]['cod']  ?></td>
                            <td> <?php echo $vars['chaves'][$c]['nome'] ?></td>
                            <td width="50px"></td>
                        </tr>
                    <?php    
                      }
                    }
                    //exibe as UAs já gravadas para o grupo
                    $sql = pg_query("SELECT * FROM grup_curso WHERE \"CodGrup\" = ".$vars['novocod']." ORDER BY \"CodCursos\"");
                    while ($r = pg_fetch_array($sql)){
                        ?>
                        <tr>
                            <td width="120px"><?php echo $r['ID']?></td>
                            <td width="100px"><?php echo $r['CodGrup']?></td>
                            <td width="100px"><?php echo $r['CodCursos']?></td>
                            <td><?php echo $r['NomeCurso']?></td>
                            <td></td>
                        </tr>
                        <?php
                    }
                    
                    
                    $filtro = '%'.$vars['grupo'].'%';          
                    //echo "SELECT \"Chave\", \"Nome\" FROM cursos WHERE \"Nome\" ILIKE('$filtro') ORDER BY \"Nome\"";
                    
                    ?>
                    <tr style="color: #666">
                        <td width="120px"><?php echo $vars['disciplina']?></td>
                        <td width="100px"><?php echo $vars['novocod']?></td>
                        <td width="100px"></td>
                        <td>
                            <select name="Curso">
                                <option value=""></option>
                            <?php   
                              
                              $cursos = pg_query("SELECT \"Chave\", \"Nome\" FROM cursos WHERE \"Nome\" ILIKE('$filtro') ORDER BY \"Nome\"");
                              while($cur = pg_fetch_array($cursos)){
                              ?>
                                <option value="<?php echo $cur['Nome']?>"><?php echo $cur['Nome']?></option>
                              <?php      
                              }                              
                            ?>
                            </select>
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                        </td>
                        <td align="right">
                            <input type="submit" name="maisum" value="+">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right">
                            <input type="submit" name="GravaGrupo" value="Grava">
                        </td>
                    </tr>
                    
            </table>
            </form>
            </div> 
            
       <?php
       //var_dump($vars);
        }
    
        
        
  }
    
    
    
?>
