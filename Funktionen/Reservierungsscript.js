/**Konstanten und Variablen */
const URL = "Funktionen/MachReservierung.php";
var vereinsEmail = "";
var kundenNachricht = "test";
const VEREINS_NAME = "Romania"; //hier gehört Name vom Emailaccount vom Verein
const VEREINS_NACHRICHT = "bestätigt"; //hier gehört Nachricht in der Bestätigungsemail vom Verein
var passwort = "";
var sprache = "Deutsch"; //Standardsprache
var DEBUG_MODUS = false;

ladeDebugModus().then(data => {DEBUG_MODUS = data;});
ladeDatenJson().then(data => {
    passwort = data['Bestaetigungspasswort'];
    vereinsEmail = data['Vereinsemail'];
    kundenNachricht = data["KundenNachricht"];
})

/**fügt die Rerservierung nach Passwortüberprüfung hinzu (Variablen in den Feldern definiert) und ladet Sitzplan neu*/
async function setReservierung_passwort(){
    if(CheckPasswort()){
        SetReservierungAndReload();
    }else{
        sprache = await ladeSprache();
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe("PASS_FAIL",sprache);
    }
}

/**fügt die Rerservierung hinzu (Variablen in den Feldern definiert) und ladet Sitzplan neu*/
async function SetReservierungAndReload(){
    await SetReservierung()
    LoadSitzplan();
}

/**fügt die Rerservierung hinzu (Variablen in den Feldern definiert) */
async function SetReservierung(){
    sprache = await ladeSprache();

    if(await NichtBestätigt("CONFIRM", "CANCEL", sprache)){return;}

    let Kundenname = document.getElementById('setKundenname').value;
    let Bezahlort = document.getElementById('wahl').value;
    let Sitze = [];

    if(!bestaetigeEmail('email','BestatigeEmail')){
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe("EMAIL_CON_FAIL",sprache);
        return;
    }
    let Email = document.getElementById('email').value;
    let Telefon = document.getElementById('telefon').value;

    let AnzahlAutoSitze;
    try{
        AnzahlAutoSitze = document.getElementById('anzahlSitze').value;
    }catch(error){
        AnzahlAutoSitze = 1;
    }
    try{
        Sitze = document.getElementById('speicher').value.split("/");
        Sitze.shift();
    }catch(e){
        await GetNächsteFreieSitze(AnzahlAutoSitze).then(data => {
            Sitze = data;
        }).catch(error => {
            if(error === "NO_SEAT_LEFT"){
                AnzahlAutoSitze = -1;
            }
            document.getElementById('output').innerHTML=error;
        });
        if(AnzahlAutoSitze < 0){document.getElementById('output').innerHTML = await getTranslationFromAusgabe("NO_SEAT_LEFT",sprache);}
    }
    for(let i = 0; i < Sitze.length; i++){
        await SetReservierungInDB(Kundenname, Sitze[i], Bezahlort, Email, Telefon).then(data=>{
            sendMail(Email,Kundenname,kundenNachricht).then(data1 =>{
                document.getElementById('output').innerHTML = data + ". " + data1;
                sendMail(vereinsEmail, VEREINS_NAME, VEREINS_NACHRICHT)
            }).catch(e => {
                makeCommand("SELECT ReservierungsID FROM reservierung WHERE SitzplatzLabel='"+Sitze[i]+"';").then(data=>{
                    DeleteReservierungAndReload(data,false,false);
                }).finally(()=>{document.getElementById('output').innerHTML=e;})
            })
        }).catch(e => document.getElementById('output').innerHTML=e);
    }

    ClearAusgewaelt();
}
/**fügt die Rerservierung in der Datenbank hinzu */
function SetReservierungInDB(Kundenname, Sitz, Bezahlort, Email, Telefon){
    return new Promise((resolve,reject) =>{
        $.ajax({
            url: URL,
            type: "POST",
            data: {Action:"set", Kundenname:Kundenname, Sitz:Sitz, Bezahlort:Bezahlort, Email:Email, Telefon:Telefon},
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
function GetReservierung(){
    let Auswahl = document.getElementById('suchOption').value;
    let Inhalt = document.getElementById('inhalt').value;
    GetReservierungDB(Auswahl,Inhalt);
}

/**gibt alle Reservierungsdaten aus der Datenbank zurück */
function GetReservierungDB(Auswahl, Inhalt){
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
async function GetNächsteFreieSitze(anzahl){
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

/**löscht die Reservierung nach Passwortüberprüfung (Variablen in den Feldern definiert) und ladet Sitzplan neu*/
async function DeleteReservierung_passwort(ReservierungsID, bestaetigung, ausgabe){
    if(CheckPasswort()){
        DeleteReservierungAndReload(ReservierungsID, bestaetigung, ausgabe);
    }else{
        sprache = await ladeSprache();
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe("PASS_FAIL",sprache);
    }
}

/**löscht die Reservierung (Variablen in den Feldern definiert) und ladet den Sitzplan neu */
async function DeleteReservierungAndReload(ReservierungsID, bestaetigung, ausgabe){
    await DeleteReservierung(ReservierungsID, bestaetigung, ausgabe);
    LoadSitzplan();
}

/**löscht die Reservierung (Variablen in den Feldern definiert) */
async function DeleteReservierung(ReservierungsID, bestaetigung, ausgabe){
    sprache = await ladeSprache();

    if(bestaetigung && await NichtBestätigt("CONFIRM_DEL", "CANCEL_DEL",sprache)) {return;}
    if(ReservierungsID == ""){
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe("DEL_MISS",sprache);
        return;
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

async function SetBezahlung_passwort(kundenname, value){
    if(CheckPasswort()){
        SetBezahlung(kundenname, value);
    }else{
        sprache = await ladeSprache();
        document.getElementById('output').innerHTML=await getTranslationFromAusgabe("PASS_FAIL",sprache);
    }
}

/**Setzt die Bezahlung fest */
async function SetBezahlung(Kundenname, value){
    if(await NichtBestätigt("CONFIRM_PAY", "CANCEL_PAY",await ladeSprache())){return;}
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
        if(DEBUG_MODUS){
            /*da Email noch nicht implementiert*/
            resolve("email noch nicht gesendet, weil work in Progress")
        }else{
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
        }
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

/**verändert die Sprache der Webseite */
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

/**erstellt ein Bestätigungsbildschirm
 * -key: Die Ausgabe beim Bestätigungsbildschirm
 * -value: Die Abbruchausgabe
 * -Sprache: De gewählte Sprache
 */
async function NichtBestätigt(key,value,Sprache){
    if(!confirm(await getTranslationFromAusgabe(key,Sprache))){
        document.getElementById('output').innerHTML = await getTranslationFromAusgabe(value,Sprache);
        return true;
    }
    return false;
}

/**checkt ob Passwort richtig ist */
function CheckPasswort(){
    return document.getElementById('pass').value === passwort;
}