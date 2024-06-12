/**definiert die Anzahl an Sitzplätzen */
            function setSitzplätze(Sitzreihen, SitzeProReihe){
                SpeichertSitzplätzeGlobal(Sitzreihen, SitzeProReihe);
                setSitzeDB(Sitzreihen, SitzeProReihe);
            }
            function SpeichertSitzplätzeGlobal(Sitzreihen, SitzeProReihe){
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:"set",Sitzreihe:Sitzreihen,Laenge:SitzeProReihe},
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
                console.log("passed");
                $.ajax({
                    url: "Funktionen/Sitzplanerstellung.php",
                    type: "POST",
                    data: {Action:"display",Sitzreihe:sitze[0],Laenge:sitze[1]},
                    success: function(data){
                        console.log("->", data);
                        if(debug){
                            document.getElementById('übersichtSitze').innerHTML = getStringForDisplay(data,
                                                                                                    parseInt(sitze[0]),parseInt(sitze[1]));
                        }else{
                            document.getElementById('übersichtSitze').innerHTML = zeigeSitze(data,sitze[0],sitze[1]);
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
            function zeigeSitze(data,Sitzreihen,Länge){
                result = "<table style='width:80%;'>";
                arrayInfo = data.split('|');
                OFFSETWIDTH= 5;
                console.log(arrayInfo.length)
                if(arrayInfo.length==(Sitzreihen*Länge)+1){
                    width=Math.floor(100/Länge)-OFFSETWIDTH;
                    for(let i=0;i<Sitzreihen;i++){
                        for(let j=0;j<Länge;j++){
                            if(arrayInfo[i*Länge+j]==='1'){
                                result= result + '<svg style="width:'+width+'%"><rect class="redRectangle" width="100%" height="60"/></svg>';
                            }else{
                                result= result + '<svg style="width:'+width+'%"><rect class="greenRectangle" width="100%" height="60"/></svg>';
                            }
                        }
                        result=result+"<br>";
                    }
                    result = result + "</table>";
                    return result;
                }else{
                    return "Sitze könnten nicht verarbeitet werden<br>"
                }
            }