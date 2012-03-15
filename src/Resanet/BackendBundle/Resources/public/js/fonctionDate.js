jQuery(function($){
	$.datepicker.regional['fr'] = {
		closeText: 'Fermer',
		prevText: '&#x3c;Préc',
		nextText: 'Suiv&#x3e;',
		currentText: 'Courant',
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
		'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
		'Jul','Aoû','Sep','Oct','Nov','Déc'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
                isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['fr']);
       
        
        
});
    /**
     * transforme une date format dd/mm/yyyy en yyyy-mm-dd
     * @return string
     */
function changeFormat(date){
    return (date.substr(6,4)+'-'+date.substr(3,2)+'-'+date.substr(0,2));
}


    /**
     * transforme une date format dd/mm/yyyy ou en dd-mm-yyyy en Objet Date
     * @return Date d
     */

function creerDate(date){
d = new Date();
d.setFullYear(date.substr(6,4));
d.setMonth(parseInt(date.substr(3,2),10)-1);
d.setDate(parseInt(date.substr(0,2),10));
if(date.substr(11,2)!="") d.setHours(parseInt(date.substr(11,2),10)) 
else d.setHours(0);
if(date.substr(14,2)!="") d.setMinutes(parseInt(date.substr(14,2),10));
else d.setMinutes(0);
return d;
}

    /**
     * passage de 2 strings au formats dd/mm/yyyy ou dd-mm-yyyy puis les compare et renvoie un entier pour donner le sens de l'égalité
     * @return int 
     */

function compareDates(date1, date2) {
    d1=creerDate(date1);
    d2=creerDate(date2);
    if (d1>d2) return 1;
    else if (d1<d2) return -1;
     else return 0;
}

     /**
     * transforme un Objet Date to String yyyy-mm-dd
     * @return string
     */

function changeFormatDate(d){
    annee=d.getFullYear();
    mois=d.getMonth()+1;
    jour=d.getDate()
    return (annee+'-'+mois+'-'+jour);
     
}

/**
     * passe un selector type input et vérifie si la date est bien remplie et si le format est correct
     * @param selector Jquery, texte pour personnaliser le message
     * @return boolean
     */

function verifInputDate(selector, texte){
    var regex = /^\d{2}\/\d{2}\/\d{4}$/;
    if (selector.val()==''){
            alerte('Vous devez choisir une date '+texte)
            return false;
    }
    
    if (!(selector.val().match(regex))){
          alerte('Le format de date '+texte+' est non correct jj/mm/aaaa');
          return false;
    }
    
    return true;
}