function seconnection(){
    var seconnection = document.getElementById('erreur');
    let msgBtnConnInscri = "Connexion obligatoire <br /><a href='../mon_compte/connexion.php'><input type='submit' value='Connexion' name='btnConnexion'></a> <a href='../mon_compte/inscription.php'><input type='submit' value='Inscription' name='btnInscription'></button></a>"
    seconnection.innerHTML = /* msgConnect + */  msgBtnConnInscri
}

function menuDeroulant(){
  
  var deroulant = document.getElementById("noneTel");
  var burgerMenu = document.getElementById("burgerMenu");
  var MenuBurgerCroix = document.getElementById("MenuBurgerCroix");
  
  if(deroulant.style.display === "inline-block"){
      deroulant.style.display = "none";
      MenuBurgerCroix.innerHTML="<img id='burgerMenu' class='nieke' src='../img/rieeene.png' alt='' onclick='menuDeroulant()'>";

  }else{
    deroulant.style.display = "inline-block";
    MenuBurgerCroix.innerHTML="<img id='burgerMenu' class='nieke' src='../img/croix.png' alt='' onclick='menuDeroulant()'>";
    burgerMenu.style.top="70px";
  }

}


var e = true

function ChangeTypeInputPass(number){
  if(number == 1){
    var a = document.getElementById('input_text-password')
    var b = document.getElementById('img_eyes')
    
    if(e){
      a.setAttribute('type','texte') 
      b.setAttribute('src','../img/eyes.svg') 
      e = false
    }else{
      a.setAttribute('type','password') 
      b.setAttribute('src','../img/eyes-no.svg') 
      e = true
    }
  }

  if(number == 2){
    var a = document.getElementById('input_text-password_verif')
    var b = document.getElementById('img_eyes_verif')
    
    if(e){
      a.setAttribute('type','texte') 
      b.setAttribute('src','../img/eyes.svg') 
      e = false
    }else{
      a.setAttribute('type','password') 
      b.setAttribute('src','../img/eyes-no.svg') 
      e = true
    }
  }



}
