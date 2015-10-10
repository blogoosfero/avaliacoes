<?php
                if ($aluno == $r['aluno'] ) {
                    ?>
                    <tr><!--<td colspan="10" width="540"> 
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
                                            $res3 = pg_query("SELECT * FROM aluno_aula($Cod_Curso, $aula, $aluno)");
                                            if(pg_num_rows($res3)== 0 ){
                                                ?>
                                                <tr>
                                                <table class="tbform" style="width: 540px;">
                                               <tr><td width="5"></td>
                                                   <td align="center" style="color: red" ><b>Avalia&ccedil;&atilde;o Pendente.</b></td>
                                                   <td width="5"></td>
                                               </tr>                                                
                                                </table>
                                                </tr>
                                               <?php
                                            } else
                                            while ($r3 = pg_fetch_array($res3)) {
                                                //var_dump($r3);
                                                ?>
                                            <tr><td colspan="7" width="540">  
                                                    <table class="tbform" style="width: 540px;">
                                               <tr><td width="5"></td>
                                                   <td align="left"><b>Quest&atilde;o: <?php echo $r3['indexquestao'] ?></b> </td>
                                                   <td align="right"><b>Peso: <?php echo $r3['peso'] ?></b></td>
                                                   <td width="5"></td>
                                               </tr>
                                               <tr><td width="5"></td>
                                                   <td colspan="2"><?php echo $r3['questao']?></td>
                                                   <td width="5"></td>
                                               </tr>
                                               </table>
                                               <table class="tbform" style="width: 540px;">
                                               <tr><td width="5"></td> 
                                                   <td align="left" colspan="2"><b> 
                                                       <?php echo ($r3['tipo']=='DS' ? "Resposta:" : "Alternativa: ".$r3['alternativa'])?></td>
                                                   </b></td>
                                                   <td width="5"></td>
                                               </tr>
                                               <tr>
                                                   <td width="5"></td>
                                                   <td align="left" colspan="2"><?php echo $r3['resposta']?></td>
                                                   <td width="5"></td>
                                               </tr>
                                               <tr>
                                                   <td width="5"></td>
                                               <?php
                                               if ($r3['tipo']=='DS'){
                                                  ?>
                                                  <form name="lancanota" method="post" onsubmit="gettopo()">
                                                      <td colspan="2" align="right"><b>Nota:</b> 
                                                       
                                                           <input type="text" name="nota" value="<?php echo $r3['nota'] ?>" />
                                                           <input type="hidden" name="id" value="<?php echo $r3['id']?>" >
                                                           <input type="hidden" name="CodCurso" value="<?php echo $Cod_Curso ?>">
                                                           <input type="hidden" name="Aluno" value="<?php echo $aluno ?>">
                                                           <input type="hidden" name="Aula" value="<?php echo $r2['aula'] ?>">
                                                           <input type="submit" name="lancanota" value="Grava" title="Grava">
                                                       
                                                       </td>
                                                   </form>
                                                   
                                                   <?php
                                               }else{
                                                   ?>
                                               <td align="left" colspan="2"><b><?php echo ($r3['alternativa']== 1 ? "Verdadeiro: ".$r3['peso']." pts." : "Falso 0 pts.") ?> </td>
                                                   <?php
                                               }
                                               ?>
                                                 <td width="5"></td>
                                              </tr>
                                              </table> 
                                              <table class="tblinha"></table>
                                              <?php
                                            }
                                            ?>
                                            </tr>
                                            <?php
                                        } // if  aula
                                    }
                                }
                                ?>
                                            </tr><!-- fecha a 1ª tr de aluno        
                    <?php                
                }//if aluno
?>
