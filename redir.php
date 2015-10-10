<?php
//header("Location: http://skora.com.br/avaliacoes3/Manutencao.html");
if (empty($_GET)){
    header("Location: http://skora.com.br/avaliacoes3/main.php");
}else{
    header("Location: http://skora.com.br/avaliacoes3/main.php?email=".$_GET['email']."&identifier=".$_GET['identifier']."&name=".$_GET['name']."&curso=".$_GET['curso']);
}
?>
