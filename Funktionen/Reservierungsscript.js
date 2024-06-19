/**Konstanten */
const URL = "Funktionen/MachReservierung.php";

/**fügt die Rerservierung hinzu (Variablen in den Feldern definiert) */
function setReservierung(){
    let Kundenname = document.getElementById('setKundenname').value;
    let Bezahlort = document.getElementById('wahl').value;
    let Sitze = document.getElementById('speicher').value.split("/");
    let Email = document.getElementById('email').value;
    Sitze.shift();
    Sitze.forEach(Sitz => {
        setReservierungDB(Kundenname, Sitz, Bezahlort, Email).then(data=>{
            sendMail(Email,Kundenname).then(data1 =>{
                document.getElementById('output').innerHTML=data+" "+data1;
            }).catch(e => {
                makeCommand("SELECT ReservierungsID FROM reservierung WHERE SitzplatzLabel='"+Sitz+"';").then(data=>{
                    deleteReservierung(data);
                }).finally(()=>{document.getElementById('output').innerHTML=e;})
            })
        }).catch(e => document.getElementById('output').innerHTML=e);
    });
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

function getNächstenFreienSitz(){
    return new Promise((resolve,reject) =>{
        $.ajax({
            url: URL,
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
function deleteReservierung(ReservierungsID){
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
            document.getElementById('output').innerHTML=data;
        },
        error: function(data){
            console.error("error", data);
        }
    });
}

function setBezahlung(Kundenname, value){
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

function sendMail(Kundenadresse,Kundenname){
    return new Promise((resolve,reject) => {
        /*da Email noch nicht implementiert*/
        resolve("email noch nicht gesendet, weil work in Progress")

        /*$.ajax({
            url: "sendMail.php",
            type: "POST",
            data: {Kundenadresse:Kundenadresse,Kundenname:Kundenname},
            success: function(data){
                console.log("->", data);
                resolve(data)
            },
            error: function(data){
                reject(data)
                console.error("error", data);
            }
        });*/
    })
}

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