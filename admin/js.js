let divHeader = document.getElementsByTagName('header')[0].clientHeight
divHeader = divHeader - 1

var hauteur = 'calc('+100+'vh' + ' - '+divHeader+'px)'

let recupContainer = document.getElementsByClassName('container')[0]
recupContainer.style.minHeight = hauteur 

