const canvas = document.getElementById("bild");
            const bild = canvas.getContext("2d");
            const WIDTHBOX= 25; 
            const HEIGHTBOX= 25; 
            const TEXTOFFSET_WIDTH = 10;
            const TEXTOFFSET_HEIGHT = 2;

            function toCoX(Stelle){
                return Stelle * WIDTHBOX;
            }
            function toCoY(Stelle){
                return Stelle * HEIGHTBOX;
            }

            function createRectangle(StelleX, StelleY, width, heigth, color){
                bild.beginPath();
                bild.fillStyle = color;
                bild.strokeStyle = "black";
                bild.fillRect(toCoX(StelleX),toCoY(StelleY), width*WIDTHBOX, heigth*HEIGHTBOX);
                bild.strokeRect(toCoX(StelleX),toCoY(StelleY), width*WIDTHBOX, heigth*HEIGHTBOX);
            }
            function createRectangleWithText(StelleX, StelleY, width, heigth, text, color){
                bild.beginPath();
                createRectangle(StelleX,StelleY,width,heigth,color);
                bild.fillStyle = "black";
                bild.fillText(text,toCoX(StelleX)+(WIDTHBOX*width)/TEXTOFFSET_WIDTH,toCoY(StelleY)+(heigth*HEIGHTBOX)/TEXTOFFSET_HEIGHT)
            }

            async function createCircleWithText(StelleX,StelleY,radius,text){
                await interactDatabase("INSERT INTO sitzplatz(SitzplatzLabel) VALUES ('"+text+"')").then((e) => {
                    interactDatabase("SELECT Belegt FROM sitzplatz WHERE SitzplatzLabel='"+text+"';").then((data)=>{
                        bild.beginPath();
                        bild.fillStyle = data[0]==0?"green":"red";
                        bild.strokeStyle = "black";
                        bild.arc(toCoX(StelleX)+WIDTHBOX/2,toCoY(StelleY)+HEIGHTBOX/2,radius*WIDTHBOX/2,0,2*Math.PI)
                        bild.fill()
                        bild.stroke()
                        bild.fillStyle = "black";
                        bild.fillText(text,toCoX(StelleX)+(WIDTHBOX*radius)/TEXTOFFSET_WIDTH,toCoY(StelleY)+(radius*HEIGHTBOX)/TEXTOFFSET_HEIGHT)
                    }).catch((error) => {console.log(error)})
                }).catch((error) => {console.log(error)})
                
            }

            async function createSitzreihe(StelleX, StelleY, SitzeProTisch, Tische, label, anfangsSitznummer, anfangsTischnummer){
                for(let i=0;i<Tische;i++){
                    createRectangleWithText(StelleX+1,StelleY+i*(SitzeProTisch/2),1,SitzeProTisch/2,label+(i+anfangsTischnummer),"green")
                }
                for(let i=0;i<(SitzeProTisch*Tische);i++){
                    await createCircleWithText(i%2==0?StelleX:StelleX+2,
                                            StelleY+Math.floor(i/2),
                                            1,label+(anfangsSitznummer+i))
                }
                
            }


            //datenbank
            function interactDatabase(befehl){
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "./Funktionen/Sitzplanerstellung.php",
                        type: "POST",
                        data: {Action:befehl},
                        success: function(data){
                            resolve(data)
                        },
                        error: function(data){
                            reject(data)
                        }
                    });
                })
            }

            //planerstellung
            let Spalten = []
            for(let i=0;i<4;i++){
                Spalten.push(1+i*16)
            }
            let Reihen = []
            for(let i=0;i<11;i++){
                Reihen.push(2+i*5)
            }

            createSitzreihe(Reihen[0],Spalten[0],6,5,"V",1,1)
            createSitzreihe(Reihen[0],Spalten[1],6,5,"A",1,1)
            createSitzreihe(Reihen[0],Spalten[2],6,5,"A",31,6)
            createSitzreihe(Reihen[0],Spalten[3],6,7,"A",61,11)

            createSitzreihe(Reihen[1],Spalten[1],6,5,"B",1,1)
            createSitzreihe(Reihen[1],Spalten[2],6,5,"B",31,6)
            createSitzreihe(Reihen[1],Spalten[3],6,7,"B",61,11)

            createSitzreihe(Reihen[2],Spalten[1],6,5,"C",1,1)
            createSitzreihe(Reihen[2],Spalten[2],6,5,"C",31,6)
            createSitzreihe(Reihen[2],Spalten[3],6,7,"C",61,11)

            createSitzreihe(Reihen[3],Spalten[1],6,5,"D",1,1)
            createSitzreihe(Reihen[3],Spalten[2],6,5,"D",31,6)
            createSitzreihe(Reihen[3],Spalten[3],6,7,"D",61,11)

            createSitzreihe(Reihen[4],Spalten[1],6,5,"E",1,1)
            createSitzreihe(Reihen[4],Spalten[2],6,5,"E",31,6)
            createSitzreihe(Reihen[4],Spalten[3],6,7,"E",61,11)

            createSitzreihe(Reihen[5],Spalten[1],6,5,"F",1,1)
            createSitzreihe(Reihen[5],Spalten[2],6,5,"F",31,6)
            createSitzreihe(Reihen[5],Spalten[3],6,7,"F",61,11)
            createCircleWithText(Reihen[5]+1,Spalten[3]+3*7,1,"F103","green")

            createSitzreihe(Reihen[6],Spalten[1],6,5,"G",1,1)
            createSitzreihe(Reihen[6],Spalten[2],6,5,"G",31,6)
            createSitzreihe(Reihen[6],Spalten[3],6,7,"G",61,11)

            createSitzreihe(Reihen[7],Spalten[1],6,5,"H",1,1)
            createSitzreihe(Reihen[7],Spalten[2],6,5,"H",31,6)
            createSitzreihe(Reihen[7],Spalten[3],6,7,"H",61,11)

            createSitzreihe(Reihen[8],Spalten[1],6,5,"I",1,1)
            createSitzreihe(Reihen[8],Spalten[2],6,5,"I",31,6)
            createSitzreihe(Reihen[8],Spalten[3],6,7,"I",61,11)

            createSitzreihe(Reihen[9],Spalten[1],6,5,"J",1,1)
            createSitzreihe(Reihen[9],Spalten[2],6,5,"J",31,6)
            createSitzreihe(Reihen[9],Spalten[3],6,7,"J",61,11)

            createSitzreihe(Reihen[10],Spalten[0],6,5,"Z",1,1)
            createSitzreihe(Reihen[10],Spalten[1],6,5,"K",1,1)
            createSitzreihe(Reihen[10],Spalten[2],6,5,"K",31,6)
            createSitzreihe(Reihen[10],Spalten[3],6,5,"K",61,11)
            