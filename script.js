// JavaScript Document


function gettopo () {
    //alert("chamou o gettopo");
    postopo = window.pageYOffset;	
    //alert("postopo = "+ postopo);
    setCookie(postopo, 90000);
	
}


function setCookie(postopo, expira) {
    var exipires;
    var date;
    //alert ( "entrou no SetCookie"); 
    date = new Date();
    date.setTime(date.getTime() + expira);
    expires = date.toUTCString();
    document.cookie = "RollTopo = " + postopo + "; exipires = "; expires + "; path=/";
    //alert("gravou o cookie, RollTopo = "+postopo);
}

function posscroll() {
    //alert("entrou no posscroll");
    var pos = getCookie('RollTopo');
    //alert("atribuiu pos = "+ pos);
    if (pos != null){
        self.scrollTo(0,pos);
    }
    //exclui o cookies
    setCookie('RollTopo', -1);
    //alert("Executou o posscroll. pos= "+ pos);
}

function getCookie(name) {
    var cookies = document.cookie;
    var prefix = name + "=";
    var begin = cookies.indexOf("; " + prefix); 
    if (begin == -1) { 
        begin = cookies.indexOf(prefix);         
        if (begin != 0) {
            return null;
        } 
    } else {
        begin += 2;
    } 
    var end = cookies.indexOf(";", begin);     
    if (end == -1) {
        end = cookies.length;                        
    } 
    return unescape(cookies.substring(begin + prefix.length, end));
}

function mascaraData(campoData){
    var data = campoData.value;
    var obj_form = campoData.form.id;
    //var obj_cpo = campoData.id;
    //var formulario = document.getElementById(f);
    //alert(getElementsByName(campoData.form.name));
    if (data.length === 2){
        data = data + '/';
        //alert(data+''+obj_form );
        //document.forms[f].DataLimite.value = data;
        document.getElementById(obj_form).DataLimite.value = data;
        return true;              
   }
   if (data.length === 5){
        data = data + '/';
        //alert(data);
        document.getElementById(obj_form).DataLimite.value = data;
        return true;
   }
    if (data.length == 10){ 
            verifica_data(obj_form); 
    }    
}


function verifica_data (obj_form) { 
    dia = (document.getElementById(obj_form).DataLimite.value.substring(0,2)); 
    mes = (document.getElementById(obj_form).DataLimite.value.substring(3,5)); 
    ano = (document.getElementById(obj_form).DataLimite.value.substring(6,10)); 
    situacao = ""; 
    // verifica o dia valido para cada mes 
    if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) { 
        situacao = "falsa"; 
    } 
    // verifica se o mes e valido 
    if (mes < 01 || mes > 12 ) { 
        situacao = "falsa"; 
    } 
    // verifica se e ano bissexto 
    if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) { 
        situacao = "falsa"; 
    } 
    if (document.getElementById(obj_form).DataLimite.value == "") { 
        situacao = "falsa"; 
    } 
    if (situacao == "falsa") { 
        alert("Data inválida!"); 
        document.getElementById(obj_form).DataLimite.focus(); 
    } 
} 

function checkaDS(id, min, ver){
    if ((ver >= 1) && (min > 0)){
        //conta os campos e verifica quantos foram preenchidos
        var frm = document.getElementById(id);
        var conta = 0;
        for(i=0;i<frm.elements.length;i++){
           if(frm.elements[i].type === 'textarea'){
               //alert(frm.elements[i].value.length);
               if (frm.elements[i].value.length > 0 ){
                   conta ++;
               }
           }

	}
	//alert(sAux);
        //alert(conta);
        if(conta < min){
            alert("Deve responder ao menos "+min+" Questões antes de enviar o questionário.\nJá respondeu "+conta+".");
            return false;
        }else{
            return true;
        }
    }else{
        return true;
    }
}

function gravaedicao(edit, idquestao, aluno){
    
}

function GravaRascunho(){
    alert('Gravar resposta');
}

