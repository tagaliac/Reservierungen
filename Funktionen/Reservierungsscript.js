/**fügt die Rerservierung hinzu (Variablen in den Feldern definiert) */
function setReservierung(){
    let Kundenname = document.getElementById('setKundenname').value;
    let Sitz = document.getElementById('setSitz').value;
    let Bezahlort = document.getElementById('wahl').value;
    setReservierungDB(Kundenname, Sitz, Bezahlort);
}
/**fügt die Rerservierung in der Datenbank hinzu */
function setReservierungDB(Kundenname, Sitz, Bezahlort){
    $.ajax({
        url: "Funktionen/MachReservierung.php",
        type: "POST",
        data: {Action:"set",Kundenname:Kundenname,Sitz:Sitz,Bezahlort:Bezahlort},
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
    let Auswahl = document.getElementById('suchOption').value;
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

function getNächstenFreienSitz(){
    return new Promise((resolve,reject) =>{
        $.ajax({
            url: "Funktionen/MachReservierung.php",
            type: "POST",
            data: {Action:"getFreienSitz"},
            success: function(data){
                if(data!=0){
                    resolve(data)
                }else{
                    console.log(data)
                    reject("kein Sitzplatz mehr vorhanden")
                }
            },
            error: function(data){
                reject(data)
            }
        });
    })
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