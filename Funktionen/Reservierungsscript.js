/**Konstanten */
//const GLOBALE_VARIABLE_LINK ="./Globale_Variablen.json";
const URL = "Funktionen/MachReservierung.php";
const VEREINS_EMAIL = ""; //hier gehört Email vom Verein
const KUNDEN_NACHRICHT = "test"; //hier gehört Nachricht an den Kunden
const VEREINS_NAME = "Romania"; //hier gehört Name vom Emailaccount vom Verein
const VEREINS_NACHRICHT = "bestätigt"; //hier gehört Nachricht in der Bestätigungsemail vom Verein
var Sprache = "Deutsch";

/**fügt die Rerservierung hinzu (Variablen in den Feldern definiert) */
async function setReservierung(){
    Sprache= await ladeSprache();
    if(await NichtBestätigt("CONFIRM", "CANCEL",Sprache)){return;}
    let Kundenname = document.getElementById('setKundenname').value;
    let Bezahlort = document.getElementById('wahl').value;
    let Sitze = [];
    if(!bestaetigeEmail('email','BestatigeEmail')){
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe("EMAIL_CON_FAIL",Sprache);
        return;
    }
    let Email = document.getElementById('email').value;
    let AnzahlAutoSitze = 1;
    try{
        AnzahlAutoSitze = document.getElementById('anzahlSitze').value;
    }catch(error){
        AnzahlAutoSitze = 1;
    }
    try{
        Sitze = document.getElementById('speicher').value.split("/");
        Sitze.shift();
    }catch(e){
        console.log(AnzahlAutoSitze);
        await getNächsteFreieSitze(AnzahlAutoSitze).then(data => {
            Sitze = data;
        }).catch(error => {
            if(error==="NO_SEAT_LEFT"){
                AnzahlAutoSitze=-1;
            }
            document.getElementById('output').innerHTML=error;
        });
        if(AnzahlAutoSitze<0){document.getElementById('output').innerHTML=await getTranslationFromAusgabe("NO_SEAT_LEFT",Sprache);}
    }
    for(let i=0;i<Sitze.length;i++){
        await setReservierungDB(Kundenname, Sitze[i], Bezahlort, Email).then(data=>{
            sendMail(Email,Kundenname,KUNDEN_NACHRICHT).then(data1 =>{
                document.getElementById('output').innerHTML=data+". "+data1;
                sendMail(VEREINS_EMAIL, VEREINS_NAME, VEREINS_NACHRICHT)
            }).catch(e => {
                makeCommand("SELECT ReservierungsID FROM reservierung WHERE SitzplatzLabel='"+Sitze[i]+"';").then(data=>{
                    deleteReservierung(data,false,false);
                }).finally(()=>{document.getElementById('output').innerHTML=e;})
            })
        }).catch(e => document.getElementById('output').innerHTML=e);
    }
}
/**fügt die Rerservierung in der Datenbank hinzu */
function setReservierungDB(Kundenname, Sitz, Bezahlort, Email){
    return new Promise((resolve,reject) =>{
        $.ajax({
            url: URL,
            type: "POST",
            data: {Action:"set",Kundenname:Kundenname,Sitz:Sitz,Bezahlort:Bezahlort, Email:Email},
            success: function(data){
                console.log("-> reservierung erfolgreich ", data);
                resolve(data)
            },
            error: function(data){
                reject(data)
                console.error("error", data);
            }
        });
    })
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
        url: URL,
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

/**gibt die nächsten Freien Sitzplätze zurück */
async function getNächsteFreieSitze(anzahl){
    return new Promise((resolve,reject) =>{
        $.ajax({
            url: URL,
            type: "POST",
            data: {Action:"getFreienSitz",anzahl:anzahl},
            success: function(data){
                if(data!=0){
                    resolve(data.split("|"))
                }else{
                    console.log(data)
                    reject("NO_SEAT_LEFT")
                }
            },
            error: function(data){
                reject(data)
            }
        });
    })
}

/**löscht die Reservierung (Variablen in den Feldern definiert) */
async function deleteReservierung(ReservierungsID, bestaetigung, ausgabe){
    Sprache= await ladeSprache();
    if(bestaetigung&& await NichtBestätigt("CONFIRM_DEL", "CANCEL_DEL",Sprache)){return;}
    if(ReservierungsID==null){
        console.log("ID not found")
        return
    }
    $.ajax({
        url: URL,
        type: "POST",
        data: {Action:"delete",Inhalt:ReservierungsID},
        success: function(data){
            console.log("-> löschen erfolgreich", data);
            if(ausgabe){
                document.getElementById('output').innerHTML=data;
            }
        },
        error: function(data){
            console.error("error", data);
        }
    });
}

/**Setzt die Bezahlung fest */
async function setBezahlung(Kundenname, value){
    Sprache= await ladeSprache();
    if(await NichtBestätigt("CONFIRM_PAY", "CANCEL_PAY",Sprache)){return;}
    $.ajax({
        url: URL,
        type: "POST",
        data: {Action:"Bezahlung",Kundenname:Kundenname, Inhalt:(value==="ja")},
        success: function(data){
            console.log("->", data);
            document.getElementById('output').innerHTML=data;
        },
        error: function(data){
            console.error("error", data);
        }
    });
}

/**Bestätigt die Email mit den Angegeben IDs der HTML Objekte */
function bestaetigeEmail(IdOfFirstHTML,IdOfSecondHTML){
    return document.getElementById(IdOfFirstHTML).value===document.getElementById(IdOfSecondHTML).value;
}

/**Sendet eine Email; Work in Progress */
function sendMail(Empfangsadresse,Empfangsname,message){
    return new Promise((resolve,reject) => {
        /*da Email noch nicht implementiert*/
        //resolve("email noch nicht gesendet, weil work in Progress")

        $.ajax({
            url: "sendMail.php",
            type: "POST",
            data: {Empfangsadresse:Empfangsadresse,Empfangsname:Empfangsname,message:message},
            success: function(data){
                console.log("->", data);
                if(data==="Message has been sent"){
                    resolve(data)
                }else{
                    reject(data)
                }
            },
            error: function(data){
                reject(data)
                console.error("error", data);
            }
        });
    })
}

/**interagiert mit der Datenbank direkt. "command" muss als SQL-Code angegeben sein */
function makeCommand(command){
    return new Promise((resolve, reject)=>{
        $.ajax({
            url: URL,
            type: "POST",
            data: {Action:command},
            success: function(data){
                console.log("->", data);
                resolve(data)
            },
            error: function(data){
                console.error("error", data);
                return(null);
            }
        });
    });    
}

function changeLanguage(newLanguage){
    $.ajax({
        url: URL,
        type: "POST",
        data: {Action:"Sprache",newLanguage:newLanguage},
        success: function(data){
            console.log("->", data);
        },
        error: function(data){
            console.error("error", data);
        }
    });
}

async function NichtBestätigt(key, value,Sprache){
    if(!confirm(await getTranslationFromAusgabe(key,Sprache))){
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe(value,Sprache);
        return true;
    }
    return false;
}