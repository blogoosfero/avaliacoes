<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Avalia&ccedil;&otilde;es</title>

<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript" src="script.js"></script>

</head>

<body>

<div align="center">
    <table class="tabValores">
        <tr>
            <td>
                <b>Aplica as alteraç&otilde;es necessárias no Banco de dados</b>
            </td>
        </tr>
        <tr>
            <td><b>
                1º alteração do campo Tipo em aulas_avaliacoes.
                SET "Múltipla Escolha" e "Discursiva" para:
                SET "MP", "DS" e "SM"
                <br>
                Inclusão dos Campos Min e Max
                </b>
            </td>
        </tr>
        <tr>
            <td>
                ALTER TABLE  `aulas_avaliacoes` CHANGE  `Tipo` SET(  'MP',  'DS',  'SM' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT  'MP - Múltipla Escolha; DS - Discursiva, SM - Somatória'
            </td>
        </tr>
        <tr>
            <td>
                ALTER TABLE `aulas_avaliacoes` ADD `Min` INT NULL , ADD `Max` INT NULL 
            </td>
        </tr>
        <tr>
            <td>
                <b>
                    Criação da Tablela tblog
                </b>
            </td>
        </tr>
        <tr>
            <td>
                CREATE TABLE tblog (
`Chave` INT NOT NULL AUTO_INCREMENT ,
`Email` VARCHAR( 255 ) NULL ,
`user` INT NULL ,
`nomeuser` VARCHAR( 255 ) NULL ,
`CodCurso` INT NULL ,
`CodAula` INT NULL ,
PRIMARY KEY ( `Chave` )
) ENGINE = MYISAM ;
            </td>
        </tr>
        
    </table>
<?php
    require 'database.php';
    if(empty($_POST)) {
        ?>
    <table>
        <tr>
            <td align="center">
                <form name="exec" method="post">
                    <input type="button" name="Executa" valor="Executa"
                </form>
            </td>
        </tr>
    </table>
        <?php
    }else if ($_POST['Executa']){
        $db = new avdb;
        $db->Conn_av();
        mysql_query("ALTER TABLE  aulas_avaliacoes CHANGE  Tipo SET(  'MP',  'DS',  'SM' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT  'MP - Múltipla Escolha; DS - Discursiva, SM - Somatória'");
        
    }
        


?>
