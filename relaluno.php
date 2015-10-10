<?php
    class relaluno{

        public function capa($vars){
            $db = new avdb;
            $db->Conn_av();
            $this->put_menu($vars);
            $this->lista_grupos($vars);
            
            //var_dump($vars);
            
        }
        
        function put_menu($vars){
        ?>
            <div align="center">
                <table class="tablemenuAluno"> 
		<tr>					  
                <td align="left"  class="tdmenuAl"><b> Bem vindo, 
                    <?php echo($vars['nomeuser']) ?> </b>
                </td>
						
                <td width="50%" align="center"><b>Notas & Frequência</b></td>
                </table>
                <table class="tblinha"></table>
           </div>    
	<?php
            
        }
        
        function lista_grupos($vars){
            $codgrupo = 0;
            if(isset($vars['codgrupo']))
                $codgrupo = $vars['codgrupo'];
            
            //primeiro o grupo atual
            $disciplina = $vars['disciplina'];
            $Id = $vars['ID'];
            $sql = pg_query("SELECT * FROM notas_frequencia_disciplina_aluno('$disciplina', '$Id')");
            ?>
            <div align="center">
                <br/>
                <table class="tblinha">
                    <tr>
                        <td align='left'>Disciplina atual: <b><?php echo $disciplina ?></b></td>
                    </tr>
                </table>
                <table class="tablemenuAluno">
                    <tr>
                        <td>Disciplina</td>
                        <td width="50px" align="center">Unidades Avaliadas</td>
                        <td width="100px" align="center">Frequência</td>
                        <td width="100px" align="center">Pts. total</td>
                    </tr>
                </table>
                <table class="tabValores">
            <?php    
                while($r = pg_fetch_array($sql)){
                    ?>

                        <tr bgcolor="CCCCCC">
                        <form id="checadisciplinaatu" method="post" onsubmit="gettopo()">
                            <td><input style="text-align: left;" type='submit' class='celclick' value="<?php echo $r['disciplina'] ?>"></td>
                            <td width="50px" align="center"><input type='submit' class='celclick' value="<?php echo $r['unidades'] ?>"> </td>
                            <td width="100px" align="right"><input style="text-align: right;" type='submit' class='celclick' value="<?php echo $r['frequencia'] ?>"></td>
                            <td width="100px" align="right"><input style="text-align: right;" type='submit' class='celclick' value="<?php echo $r['pontuacaototal'] ?>"></td>
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                            <input type="hidden" name="relalunochecadisciplinaatu" value="<?php echo $r['codgrupo'] ?>" />
                        </form>
                        </tr>                    
                    <?php
                    
                    //echo $vars['acao'];
                    if(isset($vars['acao']) && $vars['acao'] == 'checadisciplinaatu' && $vars['lev'] >= 1) {
                        
                        
                        if($codgrupo == $r['codgrupo']){
                            ?>
                            <tr><td colspan="4">
                            <?php $this->putgrupo($vars); ?>
                            </td></tr>
                            <?php
                        }
                    }
                }
                
                
            
            ?>
                </table>
                <br/>
                <table class="tblinha">
                    <tr>
                        <td align='left'>Todas as disciplinas avaliadas:</b></td>
                    </tr>
                </table>
                
                <table class="tablemenuAluno">
                    <tr>
                        <td>Disciplina</td>
                        <td width="50px" align="center">Unidades Avaliadas</td>
                        <td width="100px" align="center">Frequência</td>
                        <td width="100px" align="center">Pts. total</td>
                    </tr>
                </table>
                
            
                <table class="tabValores">
            <?php
            $c1 = "#FFFFFF";
            $c2 = "#CCCCCC"; 
            $c = 0;
            
            $sql2 = pg_query("SELECT * FROM notas_frequencia_disciplinas_aluno('$disciplina', '$Id')");
                while($r2 = pg_fetch_array($sql2)){
                    ?>

                        <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                        <form id="checadisciplina" method="post" onsubmit="gettopo()">
                            <td><input style="text-align: left;" type='submit' class='celclick'  value="<?php echo $r2['disciplina'] ?>"></td>
                            <td width="50px" align="center"><input type='submit' class='celclick'  value="<?php echo $r2['unidades'] ?>"> </td>
                            <td width="100px" align="right"><input style="text-align: right;" type='submit' class='celclick'  value="<?php echo $r2['frequencia'] ?>"></td>
                            <td width="100px" align="right"><input style="text-align: right;" type='submit' class='celclick' value="<?php echo $r2['pontuacaototal'] ?>"></td>                          
                            <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                            <input type="hidden" name="relalunochecadisciplina" value="<?php echo $r2['codgrupo'] ?>" />
                        </form>

                        </tr>                    
                    <?php
                    
                   if(isset($vars['acao']) && $vars['acao'] == 'checadisciplina' && $vars['lev'] >= 1 ) {
                       //echo "$codgrupo->". $r2['codgrupo']; 
                       if($codgrupo == $r2['codgrupo']){
                            ?>
                            <tr><td colspan="4">
                            <?php $this->putgrupo($vars); ?>
                            </td></tr>
                            <?php
                        }
                    }
                }
                    
                
            ?>
                </table>
            </div>
            <?php
            
        }
        
        function putgrupo($vars){
            //cabecalho
            $codcurso = 0;
            if(isset($vars['codcurso']))
                $codcurso = $vars['codcurso'];
            ?>
            <table class="tabValores" width="100%">
                <tr bgcolor="f9be79">
                    <td width="15px" bgcolor="white"></td>
                    <td align="center">Unidade</td>
                    <td align="center" width="100px">Frequência</td>
                    <td align="center" width="100ps">Pts. total</td>
                    <td width="200px" bgcolor="white"></td>
                </tr>
            </table>
            <table class="tabValores">
            <?php
            $c1 = "#f9d7b0";
            $c2 = "#f9e3ca";
            $c = 0;
            $disciplina = $vars['disciplina'];
            $Id = $vars['ID'];
            $sql = pg_query("SELECT * FROM notas_frequencia_unidades_aluno('$disciplina', '$Id')");
            while($r = pg_fetch_array($sql) ){
                ?>
                <form id="checaunidade" method="post" onsubmit="gettopo()"> 
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td width="15px" bgcolor="white"></td>
                    <td> <input style="text-align: left;" type='submit' class='celclick'  value="<?php echo $r['unidade'] ?>" ></td>
                    <td width="100px" align="right"><input style="text-align: right;" type='submit' class='celclick'  value="<?php echo $r['frequencia'] ?>"></td>
                    <td width="100px" align="right"><input style="text-align: right;" type='submit' class='celclick' value="<?php echo $r['pontuacaototal'] ?>"></td>
                    <td width="200px" bgcolor="white"></td>
                    <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                    <input type="hidden" name="relalunochecaunidadeatu" value="<?php echo $r['codunidade'] ?>" />                    
                </tr>
                </form>
                <?php
                //echo $codcurso. '->'.$r['codunidade'];
                if(isset($vars['acao']) &&  $vars['lev'] >= 2 ) {
                   if($codcurso == $r['codunidade']){
                    ?>
                        <tr><td colspan="5">
                        <?php $this->putunidade($vars); ?>
                        </td></tr>
                        <?php
                    }
                }
            }
            ?>
            </table>
            <?php

        }
        function putunidade($vars){
            $codaula = 0;
            if(isset($vars['codaula']))
                $codaula = $vars['codaula'];
            
            $c1 = "#cdf9b6";
            $c2 = "#e6f9dc";
            $c = 0;

            $sql = pg_query("SELECT aulas.\"Nome\" as aula, notas_frequencia.\"CodAula\" as codaula, "
                    . "SUM(notas_frequencia.\"Frequencia\")||'/'||COUNT(notas_frequencia.\"Frequencia\") as freq, "
                    . "SUM(notas_frequencia.\"Nota\") as nota "
                    . "FROM notas_frequencia INNER JOIN aulas ON notas_frequencia.\"CodAula\" =  aulas.\"Chave\" "
                    . "WHERE notas_frequencia.\"CodCurso\" = ".$vars['codcurso']
                    . "and notas_frequencia.\"CodAluno\" = (SELECT \"Chave\" FROM usuarios WHERE \"ID\" = '".$vars['ID']."') "
                    . "GROUP BY aulas.\"Nome\", notas_frequencia.\"CodAula\" ORDER BY notas_frequencia.\"CodAula\" ");
            
            ?>
            <table class="tabValores">
            <?php
            while ($r = pg_fetch_array($sql)){
                ?>
                <form id="checaunidade" method="post" onsubmit="gettopo()"> 
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td width='30px' bgcolor='white'></td>
                    <td> <input style="text-align: left;" type='submit' class='celclick'  value="<?php echo $r['aula'] ?>"></td>
                    <td width='75px' align="right"><input style="text-align: right;" type='submit' class='celclick'  value="<?php echo $r['freq'] ?>"></td>
                    <td width='75px'align="right"><input style="text-align: right;" type='submit' class='celclick'  value="<?php echo $r['nota'] ?>"></td>
                    <td width='75px' bgcolor='white'></td> 
                    <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                    <input type="hidden" name="relalunochecaulaatu" value="<?php echo $r['codaula'] ?>" />                    
                </tr>
                </form>
                <?php
                if(isset($vars['acao'])  && $vars['lev'] >= 3 ) {
                   if($codaula == $r['codaula']){
                    ?>
                        <tr><td colspan="5">
                        <?php $this->putaula($vars); ?>
                        </td></tr>
                        <?php
                    }
                }
            }
            ?>
            </table>
            <?php
            
        }
        
        function putaula($vars){
            $codquest = 0;
            if(isset($vars['codquest']))
                $codquest = $vars['codquest'];
            
            $c1 = "#cdf9b6";
            $c2 = "#e6f9dc";
            $c = 0;
            
            $codaula = $vars['codaula'];
            $id = $vars['ID'];
            $sql = pg_query("SELECT notas_frequencia.\"CodAvaliacao\" as codquest, "
                    . "('Questão-'||aulas_avaliacoes.\"IndexQuestao\") as Quest, "
                    . "notas_frequencia.\"Frequencia\" as Freq, CASE notas_frequencia.\"Nota\" "
                    . "	WHEN 0 THEN NULL "
                    . "	Else notas_frequencia.\"Nota\" "
                    . " END as nota, "
                    . "CASE notas_frequencia.\"Conceito\" "
                    . "WHEN '0' THEN 'Não Avaliado' "
                    . "WHEN '1' THEN 'Insatisfatório' "
                    . "WHEN '2' THEN 'Satisfatório' "
                    . "WHEN '3' THEN 'Bom' "
                    . "WHEN '4' THEN 'Ótimo' "
                    . "END as Conc "
                    . "FROM  notas_frequencia INNER JOIN  aulas_avaliacoes ON "
                    . "aulas_avaliacoes.\"Chave\" = notas_frequencia.\"CodAvaliacao\" "
                    . "WHERE notas_frequencia.\"CodAula\" = $codaula "
                    . "AND "
                    . "notas_frequencia.\"CodAluno\" = (SELECT \"Chave\" FROM usuarios WHERE \"ID\" = '$id')"
                    . "ORDER BY aulas_avaliacoes.\"IndexQuestao\"" );
            ?>
            <table class="tabValores">
                <tr bgcolor="c0f9a2">
                    <td width="15px" bgcolor="white"></td>
                    <td width="100px">Quest&atilde;o</td>
                    <td width="80px">Frequ&ecirc;ncia</td>
                    <td width="120px">Conceito</td>
                    <td width="80px">Nota</td>
                    <td bgcolor="white"></td>
                </tr>
            </table>
            <table class="tabValores">
            <?php
            while ($r = pg_fetch_array($sql)){
                ?>
                <form id="checaunidade" method="post" onsubmit="gettopo()"> 
                <tr bgcolor="<?php echo (($c++&1)?$c1:$c2)?>">
                    <td width='15px' bgcolor='white'></td>
                    <td width="100px"><input style="text-align: left;" type='submit' class='celclick'  value="<?php echo $r['quest'] ?>"></td>
                    <td width="80px"><input style="text-align: center;" type='submit' class='celclick'  value="<?php echo $r['freq'] ?>"></td>
                    <td width="120px"><input style="text-align: left;" type='submit' class='celclick'  value="<?php echo $r['conc'] ?>"></td>
                    <td width="80px"><input style="text-align: right;" type='submit' class='celclick'  value="<?php echo $r['nota'] ?>"></td>
                    <td bgcolor="white"></td>
                    <input type="hidden" name="vars" value="<?php echo base64_encode(serialize($vars)) ?>">
                    <input type="hidden" name="relalunochecquestaoatu" value="<?php echo $r['codquest'] ?>" />                    
                    
                </tr>
                </form>
            <?php
                if(isset($vars['acao']) && $vars['lev'] >= 4 ) {
                   if($codquest == $r['codquest']){
                    ?>
                        <tr><td colspan="6">
                        <?php $this->putquest($vars); ?>
                        </td></tr>
                        <?php
                    }
                }
            
            }
            ?>
            </table>
            <?php
            
        }
        
        function putquest($vars){
            $quest = $vars['codquest'];
            $id  = $vars['ID'];
            $codusr = $vars['codusr'];
            //echo "SELECT * FROM notas_frequencia_questao_aluno($quest, '$id')";
            $sql=  pg_query("SELECT * FROM notas_frequencia_questao_aluno($quest, '$id')");
            $r = pg_fetch_array($sql);
            $sql2 = pg_query("SELECT \"Conceito\", \"Nota\" FROM notas_frequencia WHERE \"CodAvaliacao\" = $quest AND \"CodAluno\" = $codusr");
            $r2 = pg_fetch_array($sql2);
            ?>
            <table class="tabValores">
                <tr>
                    <td width="50"></td>
                    <td>
                        <div class="Quest">
                            <p>
                            <?php echo $r['questao']?>
                            </p>                           
                        </div>
                        <div class="Resp">
                            <p>
                            <?php echo $r['resposta']?>    
                            </p>
                            <table class="tblinha" style="width: 100%" >
                                <tr>
                                    <td width="60%"></td>
                                    <td width="20%" align="right"><b>Conceito</b></td>
                                    <td width="20%" align="right"><b>Nota</b></td> 
                                </tr>
                                <tr>
                                    <td width="60%"></td>
                                    <td width="20%" align="right">
                                        <?php
                                            switch ($r2['Conceito']){
                                                case 0 : echo '';break;
                                                case 1 : echo 'Insatisfatório'; break;
                                                case 2 : echo 'Satisfatório'; break;
                                                case 3 : echo 'Bom'; break;
                                                case 4 : echo 'Ótimo'; break;                                                  
                                            }
                                        ?>
                                    </td>
                                    <td width="20%" align="right"><?php echo $r2['Nota'] ?></td> 
                                </tr>
                            </table>
                        </div>
                        
                    <td width="50"></td>
                </tr>
            </table>
            <?php
           
        }

    }
    

?>
