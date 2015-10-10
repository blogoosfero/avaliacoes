<?php
        ini_set('default_charset','UTF-8');
        
        if(!isset($_SESSION)){
        
        session_start();
       /*
        echo "sessão inicializada";
        echo "SESSION<br>";
        var_dump($_SESSION);
        
        echo "POST<br>";
        var_dump($_POST);
        echo "GET";
        var_dump($_GET);
        $data = date('Y-m-d');
        echo "<br>$data<br>";
        
        /*echo "LOG";
        //include_once 'funcs.php';
        //$fc = new funcs();
        //$log = $fc->getLog($_GET['email']);
        //var_dump($log);
        
       //  */
    }
    
   //http://localhost/Avaliacoes3/main.php?email=polaco_doido@skora.com.br&name=luiz%20skora&identifier=skora&curso=difusao-macro-osasco-embu-1 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Avalia&ccedil;&otilde;es</title>

<link rel="stylesheet" type="text/css" href="style.css" />


<script type="text/javascript" src="script.js"></script>

</head>

    <body onload="posscroll();">
        <!--
        Gravar resposta ainda não enviada em um cookie e carrega-la quando e se necessário.<br/>
        
        EXPORTAR RELATóRIO
        penso:
            caixa de seleção com a opção separado por vírgula e rtf
            o usuário seleciona, clica, o sistema gera o documento e disponibilza para download<br/>
        Inserir visto na listagem de alunos da turma?<br/>

        Mostrar em odim que se existem questionários marcados para refazer. [FEITO]<br/>
        Mostrar observações do monitor no questionário respondido pelo aluno.{FEITO] <br/>
        mostrar para o aluno que o questionário foi marcado para refazer.[FEITO]<br/>
    -->
<?php

require 'database.php';

/****************************************************************************
 *                          FUNÇÕES DE LOGIN
 ***************************************************************************/
//echo $_GET['name']."<br/>";
if (!empty($_GET['name'])){
    $_GET['name'] = pg_escape_string($_GET['name']);
}
//echo $_GET['name']."<br/>";
if (empty($_POST) && empty($_GET)){//login Odim
    //var_dump($_POST);
    include('login.php');
}else if (empty($_POST) && !empty($_GET) /*&& (!isset($_POST['logon'])=='Entrar')*/ ) {//login de admin ou aluno
   // echo "entrou 2º if<br/>";
    //solicita o curso caso não exista
    if (isset($_GET['disciplina'])){
        $db = new avdb;
        $db->Conn_av(); 
        
        $vars = array();
        $vars['nomeuser'] = $_GET['name'];
        
        $codusr = pg_query("SELECT * FROM usuarios WHERE \"ID\" = '".$_GET['identifier']."'");
        $usr = pg_fetch_array($codusr);
        $vars['codusr'] = $usr['Chave'];
        $vars['disciplina'] = $_GET['disciplina'];
        $vars['ID'] = $_GET['identifier'];
        //localiza a disciplina na tabela grupo
        $grupo = pg_query("SELECT * FROM grup_curso WHERE \"ID\" = '".$_GET['disciplina']."'");
        //$grp = pg_fetch_array($grupo);
            //se usuário blogoosfero ou odim, chama função de criação de grupo
            if (($usr['ID'] == 'blogoosfero') || ($usr['ID'] == 'skora')){               
                //obtém o índice do grupo
            //verifica se já existem unidades lançadas para a disciplina selecionada
            if (pg_num_rows($grupo)== 0){
                $scod = pg_query("SELECT MAX(\"CodGrup\")as \"cod\" FROM grup_curso");
                $cod = pg_fetch_array($scod);
                $vars['novocod'] = $cod['cod']+1;
            } else {
                $scod = pg_query("SELECT DISTINCT(\"CodGrup\") FROM grup_curso WHERE \"ID\" = '".$_GET['disciplina']."'");
                $cod = pg_fetch_array($scod);
                $vars['novocod'] = $cod['CodGrup'];
            }
            $vars['grupo'] = $_GET['grupo'];
            
            include_once 'reladm.php';
            $rel = new reladm;
            $rel->addgrupo($vars);
        }
        if(pg_num_rows($grupo)== 0){
            echo "Sem lan&ccedil;amentos para a disciplina".$_GET['disciplina']."<br/>";
        }else{
            $tipousr = pg_query("SELECT DISTINCT \"Responsavel\" FROM cursos WHERE \"Chave\" IN (SELECT \"CodCursos\" FROM grup_curso WHERE \"ID\" = '".$_GET['disciplina']."')");
            $monitor = false;
            while ($tipo = pg_fetch_array($tipousr)){
                if ($tipo['Responsavel'] == $usr['Chave'])
                    $monitor = true;
            }
            if ($monitor) {
                include 'reladm.php';
                $gr = pg_fetch_array($grupo);
                $vars['codgrupo']=$gr['CodGrup'];
                $rel = new reladm();
                $rel->capa($vars);
            }else{
                include 'relaluno.php';
                $gr = pg_fetch_array($grupo);
                $vars['codgrupo']=$gr['CodGrup'];
                $rel = new relaluno();
                $rel->capa($vars);
            }
        }
        
    } else {
    //echo "<br> Entrou login";
    //global $db;
    $db = new avdb;
    $db->Conn_av(); 
    //obtém o código do curso
    //if(isset($_GET['email'])) {
    if(isset($_GET['identifier'])) {
        include_once 'funcs.php';
        //echo "\"Chave\" FROM cursos WHERE \"Nome\" = '".$_GET['curso']."'"."<br/'>";
        $cur = $db->Select("\"Chave\" FROM cursos WHERE \"Nome\" = '".$_GET['curso']."'");
        //echo "<br> Cur=== $cur <br> SELECT \"Chave\" FROM cursos WHERE \"Nome\" = '".$_GET['curso']."'";
        //verifica se o usuário é cadastrado.
        //se sim, atualiza seus dados. se não, cadastra-o
        $chusr = $db->Select("\"Chave\" FROM usuarios WHERE \"ID\" = '".$_GET['identifier']."'");
        if ($chusr >= 1) {
            //edita o usuario
            $db->runUpSQL("usuarios SET \"Nome\" = '". $_GET['name']."' , \"Mail\" = '".$_GET['email']."' WHERE \"Chave\" = $chusr");
        } else {
            $db->runINS("usuarios (\"Nome\", \"Mail\", \"ID\") VALUES('".  $_GET['name']."', '".$_GET['email']."', '".$_GET['identifier']."' )");
            $chusr = $db->Select("\"Chave\" FROM usuarios WHERE \"ID\" = '".$_GET['identifier']."'");
        }
        
        
        $fc = new funcs;
        //$fc->setCurso($_GET['email'],$cur);
        $fc->setCurso($chusr,$cur);
        
        //$login = $_GET['email'];
        $login = $_GET['identifier'];
        //$res = $db->runSEL("* FROM usuarios WHERE Mail = '$login'");
        $res = $db->Select("* FROM usuarios WHERE \"ID\" = '$login'");
        $adm = $db->Select("\"Responsavel\" FROM cursos WHERE \"Chave\" = $cur");
        if(empty($res)){
           
            include_once 'funcs.php';
            $fc = new funcs;
            $r = $fc->add_new_user($_GET['email'], $_GET['name'],$_GET['identifier'],$_GET['curso']);
            //$fc->setUser($_GET['email']);
            //echo 'setUser';
            //$fc->setUser($_GET['identifier']);
            //$fc->setNomeUser($_GET['email']);
            $fc->setNomeUser($_GET['identifier']);
            //$log = $fc->getLog($_GET['email']); 
           // echo 'getLog';
            $log = $fc->getLog($_GET['identifier']); 
            //$_SESSION['nomeuser'] = $_GET['name'];
            //$_SESSION['user'] = $r['idUser'];
            include_once 'testes.php';
            $testes = new Testes;
            $testes->Capa($log['user'], $log['nomeuser'], $log['CodCurso']);		             
        } else {
            //echo "SELECT Chave, Responsavel FROM cursos WHERE Nome = '".$_GET['curso']."'<br>";
            $codAdm = $db->Select("\"Chave\", \"Responsavel\" FROM cursos WHERE \"Nome\" = '".$_GET['curso']."'");
            $fc->setUser($_GET['identifier']);
            //$fc->setNomeUser($_GET['email']);
            $log = $fc->getLog($chusr); 
            //$_SESSION['nomeuser'] = $_GET['name'];
            //$_SESSION['user'] = $res[0]['Chave'];
            
           // echo "SESSION['user'] === codAdm[0]['Responsavel']".$_SESSION['user']."->".$codAdm[0]['Responsavel'];
          
            if($log['user'] === $codAdm[0]['Responsavel']) {
                include_once 'admin.php';
                $admin = new Admin;
                $admin->Capa($log['user'], $log['nomeuser'], $log['CodCurso']);		  
            
            }else {
               // echo "cadastra usuário no curso<br>";
                $al = $db->Select("\"CodUser\" FROM curso_alunos WHERE \"CodCurso\" =".$log['CodCurso']." AND \"CodUser\" = 
                    ".$log['user']);
                if(empty($al)){
                    //echo "É aqui?<br>";
                    $db->runINS("curso_alunos (\"CodCurso\", \"CodUser\", \"Visto\") VALUES(".$log['CodCurso'].", ".$log['user'].", 0)");
                }
                include_once 'testes.php';
                $testes = new Testes;
                $testes->Capa($log['user'], $log['nomeuser'], $log['CodCurso']);		  
            }
        }
    }
}
}
/*******************************************************************************
 *          FUNÇÕES DE ODIM
 ******************************************************************************/
else {
    //var_dump($_POST);
    if(isset($_POST['logon'])== 'Entrar'){
        //echo "Entrou <br>";
        include('odim.php');        
        $od = new odim;
        $id = $od->login($_POST['User'], $_POST['Pass']);
        //echo "<br>id->".var_dump($id)."<br>";
        if ($id > 0){            
            $_SESSION['nomeuser'] = $_POST['User'];
            $_SESSION['user']=$id;
            $od = new odim;
            $od->capa($_POST['User'], $id);
        } else {
            unset($_POST);
           header("location: main.php");
        }
    }else if(isset($_POST['OdimCursos'])){					
        //session_start('aval');
        $nomeuser = $_SESSION['nomeuser'];
        $iduser = $_SESSION['user'];

        include_once('odim.php');
        $odim = new Odim;
                       //$login,$coduser, $NomeCurso, $nomeresponsavel, $msg
        $odim->new_curso($nomeuser,$iduser, '', '', '');
            
    }else if(isset($_POST['OdimSalvarCurso'])){
	$nomeuser = $_SESSION['nomeuser'];
        $iduser = $_SESSION['user'];
					
	$msg = "";
	$NomeCurso = trim($_POST['NomeCurso']);
	$nomeresponsavel = $_POST['nomeresponsavel']; 
	$salvar = true;
	if (($NomeCurso == '') || ($nomeresponsavel == 0)) {
            $msg = "Todos o campos devem ser preenchidos.";
            $salvar = false;
	} 
	//verifica se o curso já foi cadastrado
	if ($salvar) {
            global $db;
            $db = new avdb;
            $db->Conn_av();
            //echo "<br>Verifica se já foi cadastrado.";
            $sql = pg_query("SELECT * FROM cursos WHERE (\"Nome\" = '$NomeCurso')");
            if (pg_num_rows($sql) >= 1) {
                $salvar = false;
		$msg = "O curso: '$NomeCurso', já foi cadatrado.";
            } 
	}
	
        
        include_once('odim.php');
        $odim = new Odim;

	if ($salvar) {
            
            $db = new avdb;
            $db->Conn_av();
            $dt = date("Y-m-d H:i:s");
            //echo "<br>INSERT INTO cursos (Nome, Responsavel, Usuario, DataHora) VALUES ('$NomeCurso', $nomeresponsavel, $iduser, '$dt')";
            if ($sql = pg_query("INSERT INTO cursos (\"Nome\", \"Responsavel\", \"Usuario\", \"DataHora\") VALUES ('$NomeCurso', $nomeresponsavel, $iduser, '$dt')")){
                   // $db->runINS("cursos (\"Nome\", \"Responsavel\", \"Usuario\", \"DataHora\") VALUES ('$NomeCurso', $nomeresponsavel, $iduser, '$dt')") or die(mysql_error())) {
                $odim->Capa( $nomeuser, $iduser, 0);
            }
	} else {
            		//$login,$coduser, $NomeCurso, $nomeresponsavel, $msg
            $odim->new_curso($nomeuser,$iduser, $NomeCurso, $nomeresponsavel, $msg);					
	}			
    }else if(isset($_POST['OdimVoltar'])){
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
	include_once('odim.php');
	$odim = new Odim;
	$odim->Capa( $NomeUser, $iduser, 0);
    }else if(isset($_POST['OdimUsuarios'])){
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
	include_once('odim.php');
	$odim = new Odim;		
			//$login,$coduser, $NomeNewUser, $mailUser, $nivel, $senha, $altSenha, $msg	
	$odim->new_usuario($NomeUser,$iduser, '', '', '', '', '', '');
    } else if(isset($_POST['EditaCurso'])) {
        $funcao = 'EditaCurso';
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
        $CodCurso = $_POST['EditaCurso'];
        $find = $_POST['filtro'];
        $var = array();
	include_once('odim.php');
        $odim = new Odim;
                            //$login, $coduser, $codCurso, $NomeCurso, $nomeresponsavel, $msg
        //$odim->Edita_Curso($NomeUser, $iduser, $CodCurso, $s['Nome'], $s['Responsavel'], '');
        $odim->findcurso($NomeUser, $iduser, $find, $CodCurso, $funcao, '', $var);
    
    }else if(isset($_POST['ClonaCurso'])) {
        $funcao = 'ClonaCurso';
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
        $CodCurso = $_POST['codcurso'];
        $find = $_POST['filtro'];
	include_once('odim.php');
        $var = array('', '', '');
        $odim = new Odim;
                            //$login, $coduser, $codCurso, $NomeCurso, $nomeresponsavel, $msg
        //$odim->Edita_Curso($NomeUser, $iduser, $CodCurso, $s['Nome'], $s['Responsavel'], '');
        $odim->findcurso($NomeUser, $iduser, $find, $CodCurso, $funcao, '', $var);
        
    }else if(isset($_POST['OdimClonarCurso'])) {
        $funcao = 'ClonaCurso';
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
        $CodCurso = $_POST['CodCurso'];
        $find = $_POST['filtro'];
        $NovoCurso = trim($_POST['NomeCurso']);
        $responsavel = $_POST['responsavel'];
        $DataLimite = $_POST['DataLimite'];
        $msg = '';
        $var = array($NovoCurso, $responsavel, $DataLimite );
        
        $db = new avdb;
        $db->Conn_av();
        

        //Verifica se o nome do novo curso é válido e único
        if(strlen($NovoCurso) == 0 ){
            $msg = "O Nome n&atilde;o deve ser vazio";
        } else {
            $sql = pg_query("SELECT \"Nome\" FROM cursos WHERE \"Nome\" LIKE '$NovoCurso'");
           // echo pg_num_rows($sql)."<br/>";
            if(pg_num_rows($sql)>= 1){
                $msg = "Curso $NovoCurso j&aacute; foi cadastrado, escolha um nome diferente.";
            }
        }
        if($DataLimite == ''){
            $msg .= '<br/> O valor de Data Limite deve ser informado';
        }
        if (strlen($msg) == 0){
            //grava o novo curso
            //echo "INSERT INTO cursos (\"Nome\", \"Responsavel\") VALUES('$NovoCurso', ".($responsavel != ''? "'$responsavel'": "0").")<br/>";
            pg_query("INSERT INTO cursos (\"Nome\", \"Responsavel\") VALUES('$NovoCurso', ".($responsavel != ''? "'$responsavel'": "0").")");
            $sql = pg_query("SELECT \"Chave\" FROM cursos WHERE \"Nome\"= '$NovoCurso'");
            $r = pg_fetch_array($sql);
            $ChaveNovoCurso = $r['Chave'];
            //cria as aulas            
            if (strlen($DataLimite)==10){
                $dia = substr($DataLimite,0, 2);
                $mes = substr($DataLimite,3, 2);
                $ano = substr($DataLimite,6, 4);
                $DataLimite = $ano.'-'.$mes.'-'.$dia;
                //echo $DataLimite;
            }else
                $DataLimite = NULL;
            
           // echo "SELECT * FROM aulas WHERE \"CodCurso\" = $CodCurso<br/>";
            $sql1 = pg_query("SELECT * FROM aulas WHERE \"CodCurso\" = $CodCurso");
            //$r2=  pg_fetch_array($sql1);
            //var_dump($r2);
            while($r1=  pg_fetch_array($sql1)){
                //echo "INSERT INTO aulas (\"CodCurso\", \"Nome\", \"DataLimite\", \"MinimoMP\") VALUES($ChaveNovoCurso, '".$r2['Nome']."', '$DataLimite', ".($r2['MinimoMP']== null ? 0 :$r2['MinimoMP'])." )<br/>";
                pg_query("INSERT INTO aulas (\"CodCurso\", \"Nome\", \"DataLimite\", \"MinimoMP\") VALUES($ChaveNovoCurso, '".$r1['Nome']."', '$DataLimite', ".($r1['MinimoMP']== null ? 0 :$r1['MinimoMP'])." )");
                $chaveaula = pg_fetch_array(pg_query("SELECT CURRVAL('aulas_chave')"));
                echo $chaveaula[0]."<br/>";
                //insere questões e alternativas
                $sql2 = pg_query("SELECT * FROM aulas_avaliacoes WHERE \"CodAula\" = ".$r1['Chave']." ORDER BY \"IndexQuestao\"");
                while ($r2 = pg_fetch_array($sql2)) {
                    pg_query("INSERT INTO aulas_avaliacoes (\"CodCurso\", \"CodAula\", \"IndexQuestao\", \"Questao\", \"Peso\", \"Tipo\", 
                        \"Min\", \"Max\") VALUES($ChaveNovoCurso, ".$chaveaula[0].", ".$r2['IndexQuestao'].", '".pg_escape_string($r2['Questao'])."', "
                       .$r2['Peso'].", '".$r2['Tipo']."', ".($r2['Min']== null? 0 : $r2['Min']).", ".($r2['Max']== null? 0 : $r2['Max']).")");
                    $chaveavaliacao = pg_fetch_array(pg_query("SELECT CURRVAL('aulas_avaliacoes_chave')"));
                    //localisa e insere alternativas, caso existam
                    $sql3 = pg_query("SELECT * FROM aulas_avaliacoes_alternativas WHERE \"CodAvaliacao\" = ".$r2['Chave']);
                    while($r3 = pg_fetch_array($sql3)){
                        pg_query("INSERT INTO aulas_avaliacoes_alternativas (\"CodAula\", \"CodAvaliacao\", \"IndexAlternativa\", \"Alternativa\", \"Resposta\")
                            VALUES(".$chaveaula[0].", ".$chaveavaliacao[0].", ".$r3['IndexAlternativa'].", '".pg_escape_string($r3['Alternativa'])."', ".$r3['Resposta'].")");
                    }
                }
            }
            $msg = 'Curso clonado com sucesso'; 
        }
        include_once('odim.php');
        $odim = new Odim;        
        $odim->findcurso($NomeUser, $iduser, $find, $CodCurso, $funcao, $msg, $var);
    } else if(isset($_POST['OdimUPDATECurso'])) {
        $msg = "";
        global $db;
        $db = new avdb;
        $db->Conn_av();
        //testa as variáveis
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];

        $NomeCurso = trim($_POST['NomeCurso']);
	$nomeresponsavel = $_POST['nomeresponsavel']; 
        $CodCurso = $_POST['codCurso'];
        $var = array();
	$salvar = true;
	if (($NomeCurso == '') || ($nomeresponsavel == 0)) {
            $msg .= "Todos o campos devem ser preenchidos.<br>";
            $salvar = false;
	} 
	//verifica se o curso já foi cadastrado
	if ($salvar) {
            //echo "<br>Verifica se já foi cadastrado.";
            $sql = pg_query("SELECT * FROM cursos WHERE (\"Nome\" = '$NomeCurso')");
            if (pg_num_rows($sql) >= 1) {
                $salvar = false;
                $s = pg_fetch_array($sql);
                if ($s['Chave']== $CodCurso) {
                    $salvar = true;
                } else {
                    $msg .= "O curso: '$NomeCurso', j&aacute; foi cadatrado.<br>";
                }
            } 
	}
        include_once('odim.php');
        $odim = new Odim;
        if ($salvar){
            //grava
            //echo "UPDATE cursos SET Nome = '$NomeCurso', Responsavel = $nomeresponsavel WHERE Chave = $CodCurso<br>";
            $sql = pg_query("UPDATE cursos SET \"Nome\" = '$NomeCurso', \"Responsavel\" = $nomeresponsavel WHERE \"Chave\" = $CodCurso");
            //$odim->Capa( $NomeUser, $iduser, 0);
            $odim->findcurso($NomeUser, $iduser, $find , $CodCurso, 'EditaCurso', 'Curso alterado com sucesso.', $var);
        }else{
            //$odim->Edita_Curso($NomeUser, $iduser, $CodCurso, $NomeCurso, $nomeresponsavel, $msg);  
            $odim->findcurso($NomeUser, $iduser, $find , $CodCurso, 'EditaCurso', $msg, $var);
        }
    }else if(isset($_POST['LocalizaCurso'])){
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
	include_once('odim.php');
	$odim = new Odim;
        $find = $_POST['find'];
        $var = array();
        $odim->findcurso($NomeUser, $iduser, $find , 0, '', '', $var);
	
        
        
    } else if(isset($_POST['LocalizaUsuario'])) {
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
	include_once('odim.php');
	$odim = new Odim;
        $usfind = $_POST['usfind'];
        $var = array();
	include_once('odim.php');
	$odim = new Odim;
        $odim->findusers($nomeuser, $iduser, $usfind, '', '', '', $var);
    } else if(isset($_POST['AlteraNivel'])){
        $iduser = $_SESSION['user'];
	$NomeUser = $_SESSION['nomeuser'];
        $usfind = $_POST['usfind'];
        $var = array();
        $ID = $_POST['ID'];
        $nivel = $_POST['nivel'];
        
        global $db;
        $db = new avdb;
        $db->Conn_av();
        pg_query("UPDATE usuarios SET \"Nivel\" = $nivel WHERE \"Chave\" = $ID");
        
	include_once('odim.php');
	$odim = new Odim;
        $odim->findusers($nomeuser, $iduser, $usfind, '', '', '', $var);
        
    }
    
    /******************************************************************
     *                  FUNÇÔES DE ADMIN
     ******************************************************************/ 
    //insrir campo: Mínimo de acertos nas questões de múltipla escolha para enviar o questionário
    
    else if(isset($_POST['voltarAdm'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        //$iduser = $_SESSION['user'];
	//$nomeuser = $_SESSION['nomeuser'];
	include_once 'admin.php';
        $admin = new Admin();
	$admin->Capa($log['user'], $log['nomeuser'], $log['CodCurso']);	
    } else if(isset($_POST['voltarChkAulas'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        //$iduser = $_SESSION['user'];
	//$nomeuser = $_SESSION['nomeuser'];
	//$codcurso =$_SESSION['codcurso'];
	include_once 'admin.php';
        $admin = new Admin();
	$admin->checa_Aulas($log['user'], $log['nomeuser'], $log['CodCurso'], 0, '');					
    }if(isset($_POST['F_ChecaAulas'])) {
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';       
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
	include_once 'admin.php';
        $admin = new Admin();
        $admin->checa_Aulas($log['user'], $log['nomeuser'], $_POST['F_ChecaAulas'], 0, '');
    }else if(isset($_POST['EditaAula'])) {
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $fc->setAula($chusr, $_POST['EditaAula']);
        $log = $fc->getLog($chusr); 
	
        
        //$_SESSION['CodAula'] = $_POST['EditaAula'];
        include_once 'admin.php';
        $admin = new Admin();
        $admin->Edita_Aula($log['user'], $log['nomeuser'], $log['CodAula'], 0, '');
    }else if(isset($_POST['EditaQuestao'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$cod_quest = $_POST['EditaQuestao'];
	//$nomeuser, $codcurso, $cod_aula, $cod_quest, $cod_Alternativa, ''
	$admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $cod_quest, 0, '');
    } else if(isset($_POST['UpdateQuestao'])){				
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$cod_quest = $_POST['UpdateQuestao'];
	$Indice = $_POST['Indice'];
	$texto = $_POST['texto'];
	$Peso = $_POST['peso'];
        $Min = $_POST['min'];
        $Max = $_POST['max'];
	if(empty($_POST['Tipo']))
            $_POST['Tipo'] = 'MP';//$tipo = 'MP';
	if (isset($_POST['tipo']) == 'DS' || isset($_POST['tipo']) == 'MP' || isset($_POST['tipo']) == 'SM') {
            $tipo = $_POST['tipo'];
	}
					
	$msg = '';
        $admin->UpdateQuestao($log['nomeuser'], $log['CodCurso'], $log['CodAula'], 
                $cod_quest, $msg, $Indice, $texto, $Peso, $tipo, $Min, $Max);
    } else if(isset($_POST['SalvarUpdateQuestao'])){	
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
        $indice = $_POST['Indice'];
	$texto = $_POST['Quest'];
	$peso  = $_POST['Peso'];;
	$Cod_quest = $_POST['codQuest'];
        $Min = $_POST['Min'];
        $Max = $_POST['Max'];
					
	if(empty($_POST['Tipo']))
            $_POST['Tipo'] = 'MP';//$tipo = 'MP';
	if (isset($_POST['Tipo']) == 'DS' || isset($_POST['tipo']) == 'MP' || isset($_POST['tipo']) == 'SM') {
            $tipo = $_POST['Tipo'];
	}
	//echo "<b><br>TIPO: $tipo</b><br>";
        $msg = $admin->Salva_Update_Questao($log['CodAula'], $log['CodCurso'], $Cod_quest, $indice, $texto, 
               $peso, $Cod_quest, $tipo, $Min, $Max);
	if (strlen($msg) == 0 ) {
            //echo "<br> entrou msg == 0 ";
            $admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $Cod_quest, 0, $msg); 
	} else {
            //echo "<br> entrou msg != 0 ";
            $admin->UpdateQuestao($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $Cod_quest, $msg, 
                    $indice, $texto, $peso, $tipo, $Min, $Max);
	}
    }else if(isset($_POST['EditaAlternativa'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
				//$nomeuser, $codcurso, $cod_aula, $cod_quest, $cod_Alternativa	
	$admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $_POST['cod_quest'], $_POST['EditaAlternativa'], '');				
    } else if(isset($_POST['Salvar_Edicoes_Alternativa'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$texto = $_POST['Quest'];
	$valor = 0;
	if (isset($_POST['Valor'])) {$valor = 1;}
	$Cod_quest = $_POST['codquest'];
					
	$msg = $admin->Update_Alternativa($log['CodAula'], $log['CodCurso'], $_POST['codquest'], $_POST['codAlternativa'], $_POST['Indice'], $_POST['Quest'], $valor);
	//echo "<br> main $valor - MSG: $msg";
	if (strlen($msg) == 0 ) {
            //echo "<br> entrou msg == 0 ";
            $admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $_POST['codquest'], 0, $msg); 
	} else {
            //echo "<br> entrou msg != 0 ";
            $admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $_POST['codquest'], $_POST['codAlternativa'], $msg); 
	}
    } else if(isset($_POST['NovaQuestao'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$Indice = '';$Quest = ''; $Peso = ''; $tipo = ''; $Min = ''; $Max = '';
	$admin->Nova_Questao($log['user'], $log['nomeuser'], $log['CodCurso'], $log['CodAula'], '', 
                $Indice, $Quest, $Peso, $tipo, $Min, $Max);
    } else if(isset($_POST['SalvarNovaQuestao'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	if(empty($_POST['Tipo']))
            $_POST['Tipo'] = 'MP';//$tipo = 'MP';
	if(isset($_POST['Tipo']) == 'DS' || isset($_POST['Tipo']) == 'MP' || isset($_POST['Tipo']) == 'SM')  {
            $tipo = $_POST['Tipo'];
	}					
	//echo "Salva nova<br>";
        $msg = $admin->SalvarNovaQuestao($log['CodCurso'], $log['CodAula'], $_POST['Indice'], $_POST['Quest'],
                $_POST['Peso'], $tipo, $_POST['Min'], $_POST['Max']);
	if (strlen($msg) == 0) {
            
            $cod_quest = $admin->get_last_quest($log['CodCurso'], $log['CodAula']);
            //$nomeuser, $codcurso, $cod_aula, $cod_quest, $cod_Alternativa, ''
            
            $admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], 
                    $cod_quest, 0, '');
            //echo "<br> abre form alternativas";
	} else {
                               // $iduser, $nomeuser, $cod_curso, $cod_aula, $msg, $Indice, $Quest, $Peso, $tipo, $Min, $Max
            $admin->Nova_Questao($log['user'], $log['nomeuser'], $log['CodCurso'], $log['CodAula'], 
                    $msg, $_POST['Indice'], $_POST['Quest'], $_POST['Peso'], $tipo, $_POST['Min'], $_POST['Max']);
	}
    } else if(isset($_POST['novaAlternativa'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
        			   //$nomeuser, $codcurso, $cod_aula, $cod_quest, $msg, $ind,   $alt,    $val
	$admin->Nova_alternativa($log['nomeuser'], $log['CodCurso'], $log['CodAula'], $_POST['CodQuestao'],
                '', '', '', 0);
    } else if(isset($_POST['SalvarNovaAlternativa'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
        $codAvaliacao = $_POST['codquest'];
	$Indice=$_POST['Indice']; 
	$Quest=$_POST['Quest']; 
	$valor = 0;
	if(isset($_POST['Valor'])) {$valor = 1; }
	$msg = '';
	$msg = $admin->SalvarNovaAlternativa($log['CodAula'], $_POST['codquest'], $_POST['Indice'], $_POST['Quest'],
                $valor);
	//echo "<br>Salvou";
	if (strlen($msg) == 0) {
            //echo "<br> Exibe";
            $admin->Exibe_alternativas($log['nomeuser'], $log['CodCurso'], $log['CodAula'], 
                    $_POST['codquest'], 0, '' );
            //echo "<br> abre form alternativas";
	} else {				   //$nomeuser, $codcurso, $cod_aula, $cod_quest,    $msg, $ind,   $alt,    $val	
            $admin->Nova_alternativa($log['nomeuser'], $log['CodCurso'], $log['CodAula'], 
                    $_POST['codquest'], $msg, $_POST['Indice'], $_POST['Quest'], $valor);
        }
    } else if(isset($_POST['NovaAula'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$admin->Nova_Aula($log['user'], $log['nomeuser'], $log['CodCurso'], '');
    }  else if(isset($_POST['SalvarNovaAula'])== 'Salvar'){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$nome = $_POST['NomeAula'];
        $msg = '';
        if (strlen($_POST['DataLimite'])==10){
            $dia = substr($_POST['DataLimite'],0, 2);
            $mes = substr($_POST['DataLimite'],3, 2);
            $ano = substr($_POST['DataLimite'],6, 4);
            $DataLimite = $ano.'-'.$mes.'-'.$dia;
            //echo $DataLimite;
        }else
            $DataLimite = NULL;
	if (strlen(trim($nome)) <= 4) {
            $msg = "O nome da Nova Aula deve conter uma cadeia de 5 ou mais caracteres."; 
            $admin->Nova_Aula($log['user'], $log['nomeuser'], $log['CodCurso'], $msg);
	} else {
            $db = new avdb;
            $db->Conn_av();
            if(!is_numeric($_POST['MimMP'])){
              $_POST['MimMP'] = 0; 
            }
            //echo "INSERT INTO aulas (CodCurso, Nome, DataLimite, MinimoMP) VALUES(". $log['CodCurso'].", '$nome', '$DataLimite', ".$_POST['MimMP'].")<br>"; 
            if ($DataLimite == null){
                $str = "INSERT INTO aulas (\"CodCurso\", \"Nome\", \"MinimoMP\") VALUES(". $log['CodCurso'].", '$nome', ".$_POST['MimMP'].")";
            }  else {
                $str = "INSERT INTO aulas (\"CodCurso\", \"Nome\", \"DataLimite\", \"MinimoMP\") VALUES(". $log['CodCurso'].", '$nome', '$DataLimite', ".$_POST['MimMP'].")";
            }
            if ($sql = pg_query($str)) {						
                $admin->checa_Aulas($log['user'], $log['nomeuser'], $log['CodCurso'], 0, '');	
            }else{
                $admin->Nova_Aula($log['user'], $log['nomeuser'], $log['CodCurso'], $msg);
            }						
	}
    }else if(isset($_POST['SalvarEditaAula'])){
        //testa nome da aula e data
        $salva = true;
        $msg = '';
        $nomeAula = $_POST['NomeAula'];
        $codCurso = $_POST['CodCurso'];
        $codAula = $_POST['CodAula'];
        $MimMP = $_POST['MimMP'];
        if(strlen(trim($_POST['NomeAula'])) == 0 ){
            $salva = false;
            $msg = "Informe um nome para a aula.<br>" ;
        }//checa se o nome não foi duplicado
        $db = new avdb;
        $db->Conn_av();
        $sql = pg_query("SELECT * FROM aulas WHERE \"Nome\" = '$nomeAula' AND \"CodCurso\" = $codCurso");
        if(pg_num_rows($sql)>= 1){
            $r = pg_fetch_array($sql);
            if($r['Chave'] <> $codAula){
                $msg .= "J&aacute; existe uma aula cadastrada com este o nome $nomeAula.<Br>Escolha um nome diferente.";
                $salva = false;
            }
        }
        //testa a data caso o prenchimento não tenha sido vazio
        if($_POST['DataLimite']<> '') { 
            $ano = substr($_POST['DataLimite'], 6,4);
            $mes = substr($_POST['DataLimite'], 3,2);
            $dia = substr($_POST['DataLimite'], 0,2);
            if(checkdate($mes, $dia, $ano)){
                $data = $ano.'-'.$mes.'-'.$dia;
            }else{
                $salva = False;
                $msg .= $_POST['DataLimite'].", n&atilde;o &eacute; uma data v&aacute;lida.";
            }
        }else{
            $data = 'NULL';
        }
        if(!is_numeric($MimMP)){
            $MimMP = 0;
        }
        if ($salva){           
                
            if($data == 'NULL'){
                //echo "UPDATE aulas SET Nome = '$nomeAula', DataLimite = $data, MinimoMP = $MimMP WHERE Chave = $codAula<br>";
                $sql = pg_query("UPDATE aulas SET \"Nome\" = '$nomeAula', \"DataLimite\" = $data, \"MinimoMP\" = $MimMP WHERE \"Chave\" = $codAula");
            }else {
               // echo "UPDATE aulas SET Nome = '$nomeAula', DataLimite = '$data', MinimoMP = $MimMP WHERE Chave = $codAula<br>";
                $sql = pg_query("UPDATE aulas SET \"Nome\" = '$nomeAula', \"DataLimite\" = '$data', \"MinimoMP\" = $MimMP WHERE \"Chave\" = $codAula");
            }
        }
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $fc->setAula($chusr, $_POST['CodAula']);
        $log = $fc->getLog($chusr); 

        include_once 'admin.php';
        $admin = new Admin();
        $admin->Edita_Aula($log['user'], $log['nomeuser'], $codAula, 0, $msg);
    }
    
    /********************** ALUNOS **********************
    *		Seleção de alunos por curso             *
    *		Inclusão  de novos Alunos		*
    *		Avaliação de questionários		*
    *****************************************************/		
    else if(isset($_POST['ChecaAlunos'])){	
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        //$fc->setCurso($_GET['email'], $_POST['ChecaAlunos']);
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	//$_SESSION['CodCurso'] = $_POST['ChecaAlunos'];

	$admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], 0, '', 0, 0);
    }  else if(isset($_POST['VerDesempenhoAluno'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$aluno = $_POST['VerDesempenhoAluno'];
	//$admin->DesenpenhoAluno($log['nomeuser'], $log['CodCurso'], $aluno, '', 0);
        $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', 0, 0);
    } else if(isset($_POST['ChecaAula'])) {
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$aluno = $_POST['Aluno'];
        $aula = $_POST['Aula'];
	//$admin->DesenpenhoAluno($log['nomeuser'], $log['CodCurso'], $aluno, '', 0);
        $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', $aula, 0);
        
    } else if(isset($_POST['editanota'])) {
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$msg = '';
	$aluno = $_POST['Aluno'];
        $aula = $_POST['Aula'];
        $id = $_POST['id'];
        $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', $aula, $id);
	//$admin->DesenpenhoAluno($log['nomeuser'], $_POST['CodCurso'], $_POST['Aluno'], $msg, $_POST['codresp']);
    }else if(isset($_POST['lancanota'])) {			
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
	$aluno = $_POST['Aluno'];
        $aula = $_POST['Aula'];
	$nota = $_POST['nota'];
	$peso = $_POST['peso'];
	$chave = $_POST['id'];
	//verifica se o valor é número e está no intervalo entre 0 eo peso máximo da questão
	$msg = '';
	if(!is_numeric($nota) || $nota < 0 || $nota > $peso) {
            $msg = "$nota n&atilde;o &eacute um n&uacute;mero v&aacute;lido.<br/> Nota deve ser um n&uacute;mero maior ou igual a zero e menor que $peso";
            $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, $msg, $aula, $id);
            //$admin->DesenpenhoAluno($log['nomeuser'], $_POST['CodCurso'], $aluno, $msg, $chave);
	} else {
            $db = new avdb;
            $db->Conn_av();
            //grava o valor
            //echo "<br> UPDATE aulas_avaliacoes_alunos_respostas SET Nota = $nota WHERE Chave = $chave";
            pg_query("UPDATE aulas_avaliacoes_alunos_respostas SET \"Nota\" = $nota WHERE \"Chave\" = $chave");
            //echo "<br>". mysql_errno() . ":" . mysql_error();
            $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', $aula, 0);
	}
    } else if (isset($_POST['GravaObservacao'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
        $aluno = $_POST['Aluno'];
        $aula = $_POST['Aula'];
        $obs = $_POST['obs'];
        
        
        if (strlen(($_POST['Chaveobs'])) == 0){
           $ch = pg_query("SELECT * FROM new_avaliacao_aluno_obs(".$log['CodCurso'].", $aula, $aluno, '$obs',".$log['user'].")" );
        } else{
           //echo "<br>SELECT * FROM upd_avaliacao_aluno_obs(".$_POST['Chaveobs'].", '$obs', .".$log['user'].")<br>";
            $ch = pg_query("SELECT * FROM upd_avaliacao_aluno_obs(".$_POST['Chaveobs'].", '$obs', ".$log['user'].")"); 
        }
        $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', $aula, 0);        
    }else if(isset($_POST['refazer'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
        $aluno = $_POST['Aluno'];
        $aula = $_POST['Aula'];
        //echo "<br>SELECT * FROM refazer_avaliacao_aluno_obs(".$log['CodCurso'].", $aula, $aluno, ".$log['user'];
        $ch = pg_query("SELECT * FROM refazer_avaliacao_aluno_obs(".$log['CodCurso'].", $aula, $aluno, ".$log['user'].")");
        $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', $aula, 0); 
   } else if(isset($_POST['desfazer'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'admin.php';
        $admin = new Admin();
        $aluno = $_POST['Aluno'];
        $aula = $_POST['Aula'];
        //echo "<br>DELETE FROM public.aulas_avaliacoes_obs WHERE \"CodCurso\" = ".$log['CodCurso']." AND \"CodAula\" = $aula AND \"CodAluno\" = $aluno AND \"Refazer\"= True";
        $ch = pg_query("DELETE FROM public.aulas_avaliacoes_obs WHERE \"CodCurso\" = ".$log['CodCurso']." AND \"CodAula\" = $aula AND \"CodAluno\" = $aluno AND \"Refazer\"= True");
        $admin->Lista_Alunos_Curso($log['nomeuser'], $log['CodCurso'], $aluno, '', $aula, 0);        
   } else if(isset($_POST['Exportar'])) {
        include_once 'admin.php';
        $admin = new Admin();
	$admin->Exporta_DesempenhoAluno($_POST['CodCurso'], $_POST['Aluno']);
	//$admin->DesenpenhoAluno($nomeuser, $codCurso, $aluno, '', 0);
    }
    /******************************************************************
     *                  FUNÇÔES DE TESTES
     ******************************************************************/ 
    else if(isset($_POST['T_ChecaAulas'])){	
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'testes.php';
        $testes = new Testes;
	$voltar = "CapaAluno";
	$pos = 0;
	$testes->Put_Aulas($log['CodCurso'], $log['user'], $log['nomeuser'], $voltar, $pos); 
    }else if(isset($_POST['CapaAluno'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'testes.php';
	$testes = new Testes;
	$testes->Capa($log['user'], $log['nomeuser'], $log['CodCurso']);		
    }else if(isset($_POST['Questoes'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $fc->setAula($chusr, $_POST['Questoes']);
        $log = $fc->getLog($chusr); 
        //var_dump($log);
	//session_start('aval');
	//$_SESSION['CodAula'] = $_POST['Questoes'];
	$voltar = 'Put_aulas';
        include_once 'testes.php';
	$testes = new Testes;
	$testes->put_questao($log['CodCurso'], $log['nomeuser'], $log['user'], $log['CodAula'],
            0, $voltar, '', '');
    }else if(isset($_POST['Put_aulas'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
	$voltar = "CapaAluno";
	$pos = 0;
	include_once 'testes.php';
        $testes = new Testes;
	$testes->Put_Aulas($log['CodCurso'], $log['user'], $log['nomeuser'], $voltar, $pos); 
				
    }else if(isset($_POST['SalvarRascunho'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);        
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        $resposta = $_POST[$_POST['SalvarRascunho']];
        $msg = '';
        //verifica a contagem de palavras se necessário
        /*$conta = pg_query("SELECT \"Min\", \"Max\" FROM aulas_avaliacoes WHERE \"Chave\" = ".$_POST['SalvarRascunho'] );
        $con = pg_fetch_array($conta);
        if(($con['Min'] > 0) || ($con['Max'] > 0)){
            //conta palavras
            $msg = $fc->contapalavras($resposta, $con['Min'], $con['Max']);
        }*/
        //verifica se o aluno já postou a questão 
        $sql = pg_query("SELECT * FROM aulas_avaliacoes_alunos_respostas WHERE \"CodAvaliacao\"= ".$_POST['SalvarRascunho']." AND \"CodAluno\"=".$log['user']);
        if (pg_num_rows($sql) == 0 ){
            //insere
            pg_query("INSERT INTO aulas_avaliacoes_alunos_respostas (\"CodCurso\", \"CodAula\", \"CodAvaliacao\", \"CodAlternativa\", \"CodAluno\", \"Discursiva\") 
                    VALUES (".$log['CodCurso'].", ".$log['CodAula'].", ".$_POST['SalvarRascunho'].", -2, ".$log['user'].", '".pg_escape_string($resposta)."')");
            //$msg = "Rascunho salvo";
            
        }else{
            $s =  pg_fetch_array($sql);
            //echo "UPDATE aulas_avaliacoes_alunos_respostas SET \"CodAlternativa\" = -2, \"Discursiva\"= '".pg_escape_string($resposta)."' WHERE \"Chave\" = ".$s['Chave']."<br/>";
            pg_query("UPDATE aulas_avaliacoes_alunos_respostas SET \"CodAlternativa\" = -2, \"Discursiva\"= '".pg_escape_string($resposta)."' WHERE \"Chave\" = ".$s['Chave']);
            //$msg = "Rascunho salvo";
        }
        include_once 'testes.php';
        $testes = new Testes;
        $voltar = 'Put_aulas';
        
        $testes->put_questao($log['CodCurso'], $log['nomeuser'], $log['user'], $log['CodAula'], 0, $voltar, $msg, '');
        ?>
    <script>
        var data = new Date();
        alert('Resposta gravada  '+data.getDate()+'/'+ (data.getMonth()+1) +'/'+data.getFullYear()+' '+data.getHours()+':'+data.getMinutes() );
    </script>
    <?php            
        
    }else if(isset($_POST['Enviarespostas'])){
        $db = new avdb;
        $chusr = $db->chuser($_GET['identifier']);        
        include_once 'funcs.php';
        $fc = new funcs();
        $log = $fc->getLog($chusr); 
        include_once 'testes.php';

        $db->Conn_av();
        $voltar = 'Put_aulas';
        
        //verifica se o aluno já respondeu o quastionário
        $sql = pg_query("SELECT COUNT(\"CodAluno\") FROM aulas_avaliacoes_alunos_respostas WHERE \"CodCurso\" = ".$log['CodCurso'].
                " AND \"CodAula\" = ".$log['CodAula']." AND \"CodAluno\" = ".$log['user']);
        $r = pg_fetch_array($sql);
        $sql2 = pg_query("SELECT COUNT (\"CodAula\") FROM aulas_avaliacoes WHERE \"CodAula\" = ".$log['CodAula']);
        $r2 = pg_fetch_array($sql2);
                
        //echo $r[0].'<br>';
         $sqlmin = pg_query("SELECT \"MinimoMP\" FROM aulas WHERE \"Chave\" =".$log['CodAula'] );
        $min = pg_fetch_array($sqlmin);

        //echo "$r[0] e $r2[0]";
        if ($r[0] > $r2[0] ) {
            $msg = "Questionário já respondido.";
            $grava = false;
        } else {
            $msg = '';
            $sql = pg_query("SELECT \"Chave\", \"IndexQuestao\", \"Tipo\", \"Min\", \"Max\" FROM aulas_avaliacoes WHERE \"CodAula\" = ".$log['CodAula'].
              " ORDER BY \"Chave\""  );
            while ($r = pg_fetch_array($sql)) {
                reset($_POST);
                $grava = False;
                foreach ($_POST as $k => $v) {
                        if($k == $r[0] ){
                        $grava = TRUE;
                        if(($r[2] == 'DS' || $r[2] == '') && (strlen($v) >= 2)) {
                            //verifica se a questão tem número mínimo e/ou máximo de caracteres
                            if($r['Min'] > 0 || $r['Max'] > 0){
                                $conta = $fc->contapalavras($v,$r['Min'],$r['Max'] );
                                if ($conta != ''){
                                    $msg .= "Resposta $r[1] $conta \n";
                                }
                            } else if(trim($v)== '' ){
                                $grava = FALSE;
                            }  
                        }
                        //sai do laço
                        break;
                    }
                }
                if(!$grava && ($min[0] < 1)){
                    $msg .= "Informe a resposta para a quest&atilde;o $r[1].\n";
                }
            }
        }
        //verifica se o usuário acertou o mínimo estabelecido para as questões 
        $sqlmin = pg_query("SELECT \"MinimoMP\" FROM aulas WHERE \"Chave\" =".$log['CodAula'] );
        //echo "<br> min = $sqlmin";
        $min = pg_fetch_array($sqlmin);
        $checamin = true;
        $countmin = 0;
        if($min[0]>0){
            $checamin = false;
            foreach ($_POST as $q=>$alt){
                //echo "Q= $q  alt= $alt<br/>";
                if ($q != 'Enviarespostas'){
                    $sqlquest = pg_query("SELECT \"Tipo\" FROM aulas_avaliacoes WHERE \"Chave\" = $q");
                    $quest = pg_fetch_array($sqlquest);
                    if(($quest['Tipo']=='') ||($quest['Tipo']=='MP') ){
                        $sqlValt = pg_query("SELECT \"Chave\" FROM aulas_avaliacoes_alternativas WHERE \"CodAvaliacao\" = $q AND \"Resposta\" =1");
                        $Valt = pg_fetch_array($sqlValt);
                        if($Valt['Chave']==$alt){
                            $countmin = $countmin+1;
                            //$msg .= "<br>***********<br>".$Valt['Chave']."==$alt ->countmin: $countmin<br>**********<br>";
                        }
                    }else if($quest['Tipo']== 'DS'){
                       if($alt <> ''){
                           $countmin = $countmin+1;
                       } 
                    }
                }
            }
            if($countmin >= $min[0]){
                $checamin = true;
            }else{
                if($quest['Tipo']=='MP'){
                    $msg .= "Prezado Participante!\n Voc&ecirc; N&Atilde;O atingiu o n&uacute;mero m&iacute;nimo de acertos para este teste (".$min[0].").\n Por favor, reveja as alternativas e seus erros, releia o ARTIGO proposto com aten&ccedil;&atilde;o e responda um novo question&aacute;rio.\n Grato,\n Equipe de Trabalho";
                }
                if($quest['Tipo']=='DS'){
                    $msg .= "Prezado Participante!\n Voc&ecirc; deve responder ao menos $min[0] quest&otilde; para enviar suas respostas.";
                }
            }
        }
        //$msg .= "<br>************<br>verifica Min: $countmin >= ".$min[0]."<br>*************<br>";
        if ($msg == '') {
             //echo "<br> Grava Respostas";
            //var_dump($_POST);
            foreach($_POST as $q=>$alt){
                if ($q != 'Enviarespostas'){
                    //diferência o insert das questões de múltipla escolha e discursivas
                    $tsql = pg_query("SELECT \"Tipo\" FROM aulas_avaliacoes WHERE \"Chave\" = $q");
                    $tipoq = pg_fetch_array($tsql);
                    if ($tipoq['Tipo']== '') $tipoq['Tipo'] = 'MP';
                        switch($tipoq['Tipo']) {
                            case 'MP' :
                                //echo "<br> INSERT INTO aulas_avaliacoes_alunos_respostas (\"CodCurso\", \"CodAula\", \"CodAvaliacao\", \"CodAlternativa\", \"CodAluno\") VALUES(".$log['CodCurso'].", ".$log['CodAula'].", $q, $alt, ".$log['user'].")";
				$sql= pg_query("INSERT INTO aulas_avaliacoes_alunos_respostas 
                                    (\"CodCurso\", \"CodAula\", \"CodAvaliacao\", \"CodAlternativa\", \"CodAluno\")
                                    VALUES(".$log['CodCurso'].", ".$log['CodAula'].", $q, $alt, ".$log['user'].")");
				break;
                            case 'DS' :
                                $alta = pg_escape_string($alt);
                                //echo "<br> INSERT INTO aulas_avaliacoes_alunos_respostas VALUES('NULL', $cod_Curso, $codAula, $q, -1, $user, '$alt'";
				
                                $sql= pg_query("INSERT INTO aulas_avaliacoes_alunos_respostas 
                                    (\"CodCurso\", \"CodAula\", \"CodAvaliacao\", \"CodAlternativa\", \"CodAluno\", \"Discursiva\")
                                    VALUES(".$log['CodCurso'].", ".$log['CodAula'].", $q, -1, ".
                                    $log['user'].", '$alta')");		
				break;
                            case 'SM' :
                                //echo "grava somatória";
                                foreach ($alt as $k => $v) {
                                    //echo "$k ->$v<br>";
                                    $sql= pg_query("INSERT INTO aulas_avaliacoes_alunos_respostas 
                                    (\"CodCurso\", \"CodAula\", \"CodAvaliacao\", \"CodAlternativa\", \"CodAluno\")    
                                    VALUES(".$log['CodCurso'].", ".$log['CodAula'].", $q, $v, ".$log['user'].")");
                                }
                                break;
			}							
                    }
            }
            $testes = new Testes;
            $testes->Resultado_Teste($log['CodCurso'], $log['CodAula'], $log['user'], $log['nomeuser']);
            //Envia email ao monitor
            $sqlmail = pg_query("SELECT usuarios.\"Mail\" FROM usuarios INNER JOIN cursos ON usuarios.\"Chave\" = 
                cursos.\"Responsavel\" WHERE cursos.\"Chave\" = ".$log['CodCurso']);
            $sm = pg_fetch_array($sqlmail);
            $mail = "<div>
                        Mensagem autom&aacute;tica enviada por Blogoosfero.<br/>
                        Aluno: ".$log['nomeuser']." Respondeu question&aacute;rio da aula ".$log['CodAula'].
                    "</div>";
            //($mailremetente, $nomeremetente, $maildestinatario, $subject, $Msg){
            $fc->sendmail($log['Email'], $log['nomeuser'],$sm['Mail'], $log['nomeuser']." Respondeu ao question&aacute;rio", $mail);
        } else {
            $testes = new Testes;
            $testes->put_questao($log['CodCurso'], $log['nomeuser'], $log['user'], $log['CodAula'], 0, $voltar, $msg, $_POST);
	}
    }
    //*******************************************************
    //		EXPORTAÇÃO DE QUESTIONÁRIO
    //*******************************************************
    else if(isset($_POST['OdimExporta'])) 	{
        include_once('exporta.php');
	$exporta = new exporta;
	//echo "Exporta questionário";
	//var_dump($_POST);
	$exporta->listaAulas($_POST['codCurso'], $_POST['OdimExporta'],'');
    } else if(isset($_POST['ExportarQuest'])) 	{
        include_once('exporta.php');
        $exporta = new exporta;
        if(!empty($_POST['Origem']) && !empty($_POST['Destino'])){
            $exporta->ConfirmaExporta($_POST['Origem'], $_POST['Destino']);
        } else{
            $msg = "Inorme a Aula de origem e o Curso de destino.";
            $exporta->listaAulas($_POST['codCurso'], $_POST['nomeCurso'], $msg);
        }
    }else if(isset($_POST['ConfirmaExportacao'])) 	{
        include_once('exporta.php');
	$exporta = new exporta;
	$exporta->Executa($_POST['Origem'], $_POST['Destino'], $_POST['NomeAula'],$_POST['DataLimite']);
    }
    //testes de envio de email
    else if(isset($_POST['sendmail'])){
        include_once 'funcs.php';
        $fc = new funcs;
        $fc->sendmail($_POST['de'], 'Remetente', $_POST['para'], 'nova menasagem', $_POST['msg']);
        include_once 'odim.php';
        $odim = new Odim;
        $nomeuser = $_SESSION['nomeuser'];
        $iduser = $_SESSION['user'];

        $odim->capa($nomeuser, $iduser);
    }
    /**************************************************************************
     *                          RELATÓRIO
     *                      NOTAS & FREQUENCIA
     *************************************************************************/
    /**************************************************************************
     *                          ADMINISTRADOR
     ***************************************************************************/
    else if(isset($_POST['maisum'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        //obtem a chave do curso selecionado
        $db = new avdb;
        $db->Conn_av();
        $chave = pg_query("SELECT \"Chave\" FROM cursos WHERE \"Nome\" = '".$_POST['Curso']."'");
        $ch = pg_fetch_array($chave);
        if(!isset($vars['chaves'])){
            $vars['chaves'] = array();
        }
        $i = count($vars['chaves']);
        $vars['chaves'][$i]['cod'] = $ch['Chave'];
        $vars['chaves'][$i]['nome'] = $_POST['Curso'];
        //var_dump($vars['chaves']);
        include_once 'reladm.php';
        $rel = new reladm;
        $rel->addgrupo($vars);
    }else if( isset($_POST['GravaGrupo'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        //var_dump($vars);
        $str = '';
        foreach ($vars['chaves'] as $c => $v){
            $str .= "('".$vars['disciplina']."', ".$vars['novocod'].", ".$vars['chaves'][$c]['cod'].", '".$vars['chaves'][$c]['nome']."')" ;
            if ($c < count($vars['chaves'])-1){
                $str .= ',';
            }
            
        }
        
        $db = new avdb();
        $db->Conn_av();
        //echo "INSERT INTO grup_curso(\"ID\",\"CodGrup\", \"CodCursos\", \"NomeCurso\") VALUES $str<br/>";
        pg_query("INSERT INTO grup_curso(\"ID\",\"CodGrup\", \"CodCursos\", \"NomeCurso\") VALUES $str");
    }else if(isset($_POST['checaAluno'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $vars['chkaluno'] = $_POST['codAluno'];
        //limpa os parâmetros do próximo nível
        if(isset($vars['chkcurso'])) 
            unset($vars['chkcurso']);
        if(isset($vars['chkaula']))
            unset($vars['chkaula']);
            
        //var_dump($vars);
        include_once 'reladm.php';
        $rel = new reladm;
        $rel->capa($vars);        
    }else if(isset($_POST['checaCursos'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $vars['chkcurso'] = $_POST['codCurso'];
        //limpa os parametros do próximo nível
        if(isset($vars['chkaula']))
            unset($vars['chkaula']);
        //var_dump($vars);
        include_once 'reladm.php';
        $rel = new reladm;
        $rel->capa($vars);        
    }else if(isset($_POST['checaAula'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $vars['chkaula'] = $_POST['codaula'];
        //var_dump($vars);
        include_once 'reladm.php';
        $rel = new reladm;
        $rel->capa($vars);        
       
    } else if(isset($_POST['GravaNotaFreq'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $Freq = 0;
        if(isset($_POST['chFreq']))
            $Freq = 1;
        //grava
        $db = new avdb();
        $db->Conn_av();
        $sql = pg_query("UPDATE notas_frequencia SET \"Frequencia\" = $Freq, \"Conceito\" = ".$_POST['conceito'].", \"Nota\" =".$_POST['nota']." 
                WHERE \"Chave\" = ".$_POST['chavefreq']);
        //var_dump($vars);
        include_once 'reladm.php';
        $rel = new reladm;
        $rel->capa($vars);        
    }
    /**************************************************************************
     *                          ALUNO
     ***************************************************************************/ 
    else if(isset($_POST['relalunochecadisciplinaatu'])){
        $vars = unserialize(base64_decode($_POST['vars']));  
        $vars['codgrupo'] = $_POST['relalunochecadisciplinaatu'];
        $vars['codcurso'] = 0;
        $vars['acao'] = 'checadisciplinaatu';
        $vars['lev'] = 1;
        include_once 'relaluno.php';
        $rel = new relaluno;
        $rel->capa($vars);
    }
    else if(isset($_POST['relalunochecaunidadeatu'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $vars['codcurso']  = $_POST['relalunochecaunidadeatu'];
        $vars['codaula'] = 0;
        //$vars['acao'] = 'checadisciplinaatu';
        $vars['lev'] = 2;
        include_once 'relaluno.php';
        $rel = new relaluno;
        $rel->capa($vars);
    }
    else if(isset($_POST['relalunochecaulaatu'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $vars['codaula']  = $_POST['relalunochecaulaatu'];
        $vars['codquest'] = 0;
        //$vars['acao'] = 'checadisciplinaatu';
        $vars['lev'] = 3;
        include_once 'relaluno.php';
        $rel = new relaluno;
        $rel->capa($vars);
    }
    else if(isset($_POST['relalunochecquestaoatu'])){
        $vars = unserialize(base64_decode($_POST['vars']));
        $vars['codquest']  = $_POST['relalunochecquestaoatu'];
        //$vars[''] = 0;
        //$vars['acao'] = 'checadisciplinaatu';
        $vars['lev'] = 4;
        include_once 'relaluno.php';
        $rel = new relaluno;
        $rel->capa($vars);
    }
    else if(isset($_POST['relalunochecadisciplina'])){
        $vars = unserialize(base64_decode($_POST['vars']));  
        $vars['codgrupo'] = $_POST['relalunochecadisciplina'];
        $vars['codcurso'] = 0;
        $vars['acao'] = 'checadisciplina';
        $vars['lev'] = 1;
        include_once 'relaluno.php';
        $rel = new relaluno;
        $rel->capa($vars);
    }
}
?>
</body>
</html>