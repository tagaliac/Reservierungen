/**definiert die Anzahl an Sitzplätzen */
function setSitzplätze(Tischreihen, TischeProReihe, SitzeProTische){
    Sitzreihen = Tischreihen *2;
    SitzeProReihe = TischeProReihe * (SitzeProTische/2);
    SpeichertSitzplätzeGlobal(Sitzreihen, SitzeProReihe, SitzeProTische);
    setSitzeDB(Sitzreihen, SitzeProReihe);
}
/**Speichert die Anzahl an Sitzen als Gloable Variablen */
function SpeichertSitzplätzeGlobal(Sitzreihen, SitzeProReihe, SitzeProTische){
    $.ajax({
        url: "Funktionen/Sitzplanerstellung.php",
        type: "POST",
        data: {Action:"set",Sitzreihe:Sitzreihen,Laenge:SitzeProReihe, SitzeProTische:SitzeProTische},
        success: function(data){
            console.log("->", data);
        },
        error: function(data){
            console.error("error", data);
        }
    });
}
            /**definiert die Anzahl an Sitzplätzen in der Datenbank */
            function setSitzeDB(Sitzplätze, Länge){
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:" ",Sitzreihe:Sitzplätze,Laenge:Länge},
                    success: function(data){
                        console.log("->", data);
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }
            
            function displaySitze(debug){
                readSitze().then((data) => {
                    displaySitzeDB(debug,data)
                }).catch(e => console.log(e));
            }

            
            /**Stellt die Sitze und deren Belegungen grafisch dar
             * debug=false:die Plätze werden in Bildern angezeigt
             * debug=true:die Belegungen werden als Text dargestellt
             */
            function displaySitzeDB(debug, sitze){
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:"display",Sitzreihe:sitze[0],Laenge:sitze[1]},
                    success: function(data){
                        console.log("->", data);
                        if(debug){
                            document.getElementById('übersichtSitze').innerHTML = getStringForDisplay(data,parseInt(sitze[0]),parseInt(sitze[1]),);
                        }else{
                            document.getElementById('übersichtSitze').innerHTML = zeigeSitze(data,parseInt(sitze[0]),
                                                                                            parseInt(sitze[1]),parseInt(sitze[2]));
                        }
                        
                    },
                    error: function(data){
                        console.error("error", data);
                    }
                });
            }
            function readSitze(){
                return new Promise((resolve,reject) => {
                    $.ajax({
                        url: "Funktionen/Sitzplanerstellung.php",
                        type: "POST",
                        data: {Action:"get"},
                        success: function(data){
                            console.log(data);
                            resolve(data.split("|"))
                        },
                        error: function(data){
                            console.error("error", data);
                            reject("fehlschlag")
                        }
                    });
                })
            }

            /**Erstellt den HTML String für die geschriebene Platzdarstellung */
            function getStringForDisplay(data,Sitzreihen,Länge){
                result = "";
                arrayInfo = data.split('|');
                console.log(arrayInfo.length);
                if(arrayInfo.length==(Sitzreihen*Länge)+1){
                    for(let i = 0;i<arrayInfo.length-1;i++){
                        if(arrayInfo[i]==='1'){
                            console.log(result);
                            result= result + " Sitzplatz an Reihe " + Math.floor(i/Länge) + " und an Stelle " + i%Länge + " begelgt<br>";
                        }else{
                            result= result + " Sitzplatz an Reihe " + Math.floor(i/Länge) + " und an Stelle " + i%Länge + " nicht begelgt<br>";
                        }
                    }
                    return result;
                }else{
                    return "Array könnte nicht verarbeitet werden<br>"
                }
            }

            /**Erstellt den HTML Code für die  grafische Darstellung der Plätze*/
            function zeigeSitze(data,Sitzreihen,Länge,SitzeProTische){
                result = '<table class="anzeige" style="width:80%">';
                arrayInfo = data.split('|');
                OFFSETWIDTH= 5;
                if(arrayInfo.length==(Sitzreihen*Länge)+1){
                    result = result + '<tr>';
                    widthTische=Math.floor(100/Länge);
                    console.log(SitzeProTische)
                    for(let i=0;i<Sitzreihen;i++){
                        for(let j=0;j<Länge;j++){
                            switch (j%(SitzeProTische/2)){
                                case 0:
                                    result= result + erstelleHtmlRechtecke(arrayInfo[i*Länge+j]);
                                    break;
                                case (SitzeProTische/2)-1:
                                    result=result + erstelleHtmlRechtecke(arrayInfo[i*Länge+j]) +'<svg style="width:'+widthTische/OFFSETWIDTH+'%"><rect class="emptyRectangle" width="100%" height="100%"/></svg>';
                                    break;
                                default:
                                    result=result +erstelleHtmlRechtecke(arrayInfo[i*Länge+j])
                            }
                        }
                        if(i%2==1){
                            result=result+"<br><br><br>";
                        }
                        result = result + '</tr>';
                    }
                    result = result + "</table>";
                    return result;
                }else{
                    return "Sitze könnten nicht verarbeitet werden<br>"
                }
            }

function erstelleHtmlRechtecke(Belegung){
    if(Belegung==='1'){
        return '<svg style="width:'+widthTische+'%"><rect class="redRectangle" width="100%" height="100%"></svg>';
    }else{
        return '<svg style="width:'+widthTische+'%"><rect class="greenRectangle" width="100%" height="100%"/></svg>';
    }
}