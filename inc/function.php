<?php

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
{
    $url = "https";
}
else
{
    $url = "http"; 
}  
$url .= "://"; 
$url .= $_SERVER['HTTP_HOST']; 

$listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
function genererChaineAleatoire($longueur, $listeCar)
{
    $chaine = '';
    $max = mb_strlen($listeCar, '8bit') - 1;
    for ($i = 0; $i < $longueur; ++$i) {
    $chaine .= $listeCar[random_int(0, $max)];
    }
    return $chaine;
}

function compteurReqPrepare($maReq){
    require dirname(__FILE__).'/bdd.php';
    $VarReq = $bdd->prepare($maReq);
    $VarReq->execute();
    return $CompteurMaReq = $VarReq->rowCount();
}

function compteurReqPrepareArray($maReq, $array){
    require dirname(__FILE__).'/bdd.php';
    $VarReq = $bdd->prepare($maReq);
    $VarReq->execute($array);
    return $CompteurMaReq = $VarReq->rowCount();
}

function ReqPrepareArray($maReq, $array){
    require dirname(__FILE__).'/bdd.php';
    $VarReq = $bdd->prepare($maReq);
    return $VarReq->execute($array);
}



function dislike($color){ 
return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" fill="' .  $color . '">

            <path d="M117.333,10.667h-64C23.936,10.667,0,34.603,0,64v170.667C0,264.064,23.936,288,53.333,288h96V21.461
                C140.395,14.72,129.344,10.667,117.333,10.667z"/>

            <path d="M512,208c0-18.496-10.581-34.731-26.347-42.667c3.285-6.549,5.013-13.803,5.013-21.333
                c0-18.517-10.603-34.752-26.368-42.688c4.885-9.728,6.315-20.928,3.861-32.043C463.381,47.659,443.051,32,419.819,32H224
                c-13.995,0-35.968,4.416-53.333,12.608v228.651l2.56,1.301l61.44,133.12V480c0,3.243,1.472,6.315,3.989,8.341
                c0.683,0.512,16.512,12.992,38.677,12.992c24.683,0,64-39.061,64-85.333c0-29.184-10.453-65.515-16.981-85.333h131.776
                c28.715,0,53.141-21.248,55.637-48.363c1.387-15.211-3.691-29.824-13.653-40.725C506.923,232.768,512,220.821,512,208z"/>
    </svg>';
}


function like($color){ 
  
return '<svg  version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" fill="' .  $color .'">
		<path d="M53.333,224C23.936,224,0,247.936,0,277.333V448c0,29.397,23.936,53.333,53.333,53.333h64
			c12.011,0,23.061-4.053,32-10.795V224H53.333z" />

		<path d="M512,304c0-12.821-5.077-24.768-13.888-33.579c9.963-10.901,15.04-25.515,13.653-40.725
			c-2.496-27.115-26.923-48.363-55.637-48.363H324.352c6.528-19.819,16.981-56.149,16.981-85.333c0-46.272-39.317-85.333-64-85.333
			c-22.165,0-37.995,12.48-38.677,12.992c-2.517,2.027-3.989,5.099-3.989,8.341v72.341l-61.44,133.099l-2.56,1.301v228.651
			C188.032,475.584,210.005,480,224,480h195.819c23.232,0,43.563-15.659,48.341-37.269c2.453-11.115,1.024-22.315-3.861-32.043
			c15.765-7.936,26.368-24.171,26.368-42.688c0-7.552-1.728-14.784-5.013-21.333C501.419,338.731,512,322.496,512,304z"/>
</svg>';
}

function page($numberOfThingByPage, $maReq){

    require dirname(__FILE__).'/bdd.php';
    $numberOfThingByPage;
    $req = $bdd->prepare($maReq);
    $req->execute();
    $countMyReq = $req->rowCount();
    $pagesTotales = ceil($countMyReq/$numberOfThingByPage);

    if(isset($_GET['page']) AND !empty($_GET['page']) AND $_GET['page'] > 0 AND $_GET['page'] <= $countMyReq){
            $_GET['page'] = intval($_GET['page']);
            $pageCourante = $_GET['page'];
    
    }else{
        $pageCourante = 1;
    
    }

    return array($pagesTotales, $pageCourante);

}



function pageArrayEtCompteur($numberOfThingByPage, $maReq, $myArray){

    require dirname(__FILE__).'/bdd.php';
    $numberOfThingByPage;
    $req = $bdd->prepare($maReq);
    $req->execute($myArray);
    $countMyReq = $req->rowCount();
    $pagesTotales = ceil($countMyReq/$numberOfThingByPage);

    if(isset($_GET['page']) AND !empty($_GET['page']) AND $_GET['page'] > 0 AND $_GET['page'] <= $countMyReq){
            $_GET['page'] = intval($_GET['page']);
            $pageCourante = $_GET['page'];
    
    }else{
        $pageCourante = 1;
    
    }

    return array($pagesTotales, $pageCourante);

}


function pageEtCompteur($numberOfThingByPage, $maReq){

    require dirname(__FILE__).'/bdd.php';
    $numberOfThingByPage;
    $req = $bdd->query($maReq);
    $countMyReq = $req->rowCount();
    $pagesTotales = ceil($countMyReq/$numberOfThingByPage);

    if(isset($_GET['page']) AND !empty($_GET['page']) AND $_GET['page'] > 0 AND $_GET['page'] <= $countMyReq){
            $_GET['page'] = intval($_GET['page']);
            $pageCourante = $_GET['page'];
    
    }else{
        $pageCourante = 1;
    
    }

    return array($pagesTotales, $pageCourante, $countMyReq);

}



?>