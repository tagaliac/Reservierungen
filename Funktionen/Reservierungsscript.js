/**fügt die Rerservierung hinzu (Variablen in den Feldern definiert) */
function setReservierung(){
    let Kundenname = document.getElementById('setKundenname').value;
    let Sitz = document.getElementById('setSitz').value;
    setReservierungDB(Kundenname, Sitz);
}
/**fügt die Rerservierung in der Datenbank hinzu */
function setReservierungDB(Kundenname, Sitz){
    $.ajax({
        url: "Funktionen/MachReservierung.php",
        type: "POST",
        data: {Action:"set",Kundenname:Kundenname,Sitz:Sitz},
        success: function(data){
            console.log("->", data);
            document.getElementById('output').innerHTML=data;
        },
        error: function(data){
            console.error("error", data);
        }
    });
}

/**gibt alle Reservierungsdaten zurück (Variablen in den Feldern definiert) */
function getReservierung(){
    let Auswahl = document.getElementById('wahl').value;
    let Inhalt = document.getElementById('inhalt').value;
    getReservierungDB(Auswahl,Inhalt);
}
/**gibt alle Reservierungsdaten aus der Datenbank zurück */
function getReservierungDB(Auswahl, Inhalt){
    $.ajax({
        url: "Funktionen/MachReservierung.php",
        type: "POST",
        data: {Action:"get",Auswahl:Auswahl,Inhalt:Inhalt},
        success: function(data){
            console.log("->", data);
            document.getElementById('output').innerHTML=data;
        },
        error: function(data){
            console.error("error", data);
        }
    });
}

/**löscht die Reservierung (Variablen in den Feldern definiert) */
function deleteReservierung(){
    $.ajax({
        url: "Funktionen/MachReservierung.php",
        type: "POST",
        data: {Action:"delete",Inhalt:document.getElementById('delete').value},
        success: function(data){
            console.log("->", data);
            document.getElementById('output').innerHTML=data;
        },
        error: function(data){
            console.error("error", data);
        }
    });
}