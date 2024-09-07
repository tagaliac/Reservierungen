/**Konstanten */
const canvas = document.getElementById("bild");
const bild = canvas.getContext("2d");
const WIDTHBOX= 22; 
const HEIGHTBOX= WIDTHBOX; 
const TEXTOFFSET_WIDTH = 10;
const TEXTOFFSET_HEIGHT = 2;
const sitzeAuswahl= document.getElementById("setSitze");
const speicher = document.getElementById("speicher");
const output = document.getElementById("output");
var Sitze = new Map()
var Ausgewaehlt = []

/**Konstanten Farben */
const TISCHFARBE = "cyan";
const BELEGTFARBE = "red";
const NICHT_BELEGTFARBE = "green";
const TEXTFARBE = "black";
const RANDFARBE = "black";

/**gibt die Mausposition im Canvas zurück */
function getMousePos(canvas, event){
    const rect = canvas.getBoundingClientRect()
    return {
        x: event.clientX - rect.left,
        y: event.clientY - rect.top
    }
}

/**Übersetzt Fehlerausgabe */
async function TranslateError(error,Sprache){
    getTranslationFromAusgabe(error,Sprache).then(data => {output.innerHTML = data;});
}

/**Wenn Maus gedrückt wird */
canvas.addEventListener("mousedown",(e) => {
    WähleSitzeAus(e);
})

/**Wählt sitzt aus und legt es in der Schlange */
async function WähleSitzeAus(event){
    const sitz = getSitz(getMousePos(canvas,event))
    Sprache= await ladeSprache();
    if(sitz!=null){
        if(!NebenAusgewählteSitzplätze(sitz)){
            TranslateError("NEAR_SEAT",Sprache)
            return
        }
        interactDatabase("SELECT belegt FROM sitzplatz WHERE SitzplatzLabel = '"+sitz+"';").then(data => {
            if(data==1){
                loadSitzplan();
                throw "BELEGT";
            }
            return getTranslationFromAusgabe("CHOOSE",Sprache)
        }).then(data => {
            sitzeAuswahl.innerHTML =data;
                if(Ausgewaehlt.includes(sitz)){
                    Ausgewaehlt.splice(Ausgewaehlt.indexOf(sitz),1)
                }else{
                    Ausgewaehlt.push(sitz);
                }
                speicherort = speicher.value
                Ausgewaehlt.forEach((value) => {
                    sitzeAuswahl.innerHTML += " "+value
                    speicher.value = speicherort +"/"+ value;
                })
                output.innerHTML = "";
        }).catch(error => {TranslateError(error,Sprache)})
    } 
}


/**Funktionen zur Erstellung von Sitzen und Tischen */ 
function toCoX(Stelle){
    return Stelle * WIDTHBOX;
}
function toCoY(Stelle){
    return Stelle * HEIGHTBOX;
}
function fromCoX(posX){
    return Math.floor(posX / WIDTHBOX);
}
function fromCoY(posY){
    return Math.floor(posY / HEIGHTBOX);
}

function createRectangle(StelleX, StelleY, width, heigth, color){
    bild.beginPath();
    bild.fillStyle = color;
    bild.strokeStyle = RANDFARBE;
    bild.fillRect(toCoX(StelleX),toCoY(StelleY), width*WIDTHBOX, heigth*HEIGHTBOX);
    bild.strokeRect(toCoX(StelleX),toCoY(StelleY), width*WIDTHBOX, heigth*HEIGHTBOX);
}
function createRectangleWithText(StelleX, StelleY, width, heigth, text, color){
    bild.beginPath();
    createRectangle(StelleX,StelleY,width,heigth,color);
    bild.fillStyle = TEXTFARBE;
    bild.fillText(text,toCoX(StelleX)+(WIDTHBOX*width)/TEXTOFFSET_WIDTH,toCoY(StelleY)+(heigth*HEIGHTBOX)/TEXTOFFSET_HEIGHT)
}

async function createCircleWithText(StelleX,StelleY,radius,text){
    await interactDatabase("INSERT INTO sitzplatz(SitzplatzLabel) VALUES ('"+text+"')").then((e) => {
        interactDatabase("SELECT Belegt FROM sitzplatz WHERE SitzplatzLabel='"+text+"';").then((data)=>{
            bild.beginPath();
            bild.fillStyle = data[0]==0?NICHT_BELEGTFARBE:BELEGTFARBE;
            bild.strokeStyle = RANDFARBE;
            bild.arc(toCoX(StelleX)+WIDTHBOX/2,toCoY(StelleY)+HEIGHTBOX/2,radius*WIDTHBOX/2,0,2*Math.PI)
            bild.fill()
            bild.stroke()
            bild.fillStyle = TEXTFARBE;
            bild.fillText(text,toCoX(StelleX)+(WIDTHBOX*radius)/TEXTOFFSET_WIDTH,toCoY(StelleY)+(radius*HEIGHTBOX)/TEXTOFFSET_HEIGHT)
            Sitze.set(text,{x:StelleX,y:StelleY})
        }).catch((error) => {console.log(error)})
    }).catch((error) => {console.log(error)})
}

function getSitz(pos){
    let x = fromCoX(pos.x)
    let y = fromCoY(pos.y)
    let result = null
    Sitze.forEach(function(values, key){
        if(values.x==x && values.y==y){
            result=key;
            return;
        }
    });
    return result;
}

function NebenAusgewählteSitzplätze(sitz){
    if(Ausgewaehlt.length==0){
        return true;
    }
    let number = Number(sitz.substring(1))
    for(let i=0;i<Ausgewaehlt.length;i++){
        if((Math.abs(Number(Ausgewaehlt[i].substring(1))-number)<2) &&
           sitz.substring(0,1)==Ausgewaehlt[i].substring(0,1)){
            return true;
        }
    }
    return false;
}

async function createSitzreihe(StelleX, StelleY, SitzeProTisch, Tische, label, anfangsSitznummer, anfangsTischnummer){
    for(let i=0;i<Tische;i++){
        createRectangleWithText(StelleX+1,StelleY+i*(SitzeProTisch/2),1,SitzeProTisch/2,label+(i+anfangsTischnummer),TISCHFARBE)
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
            url: URL,
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

//Planerstellung
function loadSitzplan(){
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
    createCircleWithText(Reihen[5]+1,Spalten[3]+3*7,1,"F103")

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
}




            