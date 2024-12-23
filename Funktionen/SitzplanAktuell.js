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
var ausgewaehlt = []

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
                LoadSitzplan();
                throw "BELEGT";
            }
            return getTranslationFromAusgabe("CHOOSE",Sprache)
        }).then(data => {
            sitzeAuswahl.innerHTML =data;
                if(ausgewaehlt.includes(sitz)){
                    ausgewaehlt.splice(ausgewaehlt.indexOf(sitz),1)
                }else{
                    ausgewaehlt.push(sitz);
                }
                speicherort = speicher.value
                ausgewaehlt.forEach((value) => {
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
function CreateRectangleWithText(StelleX, StelleY, width, heigth, text, color){
    bild.beginPath();
    createRectangle(StelleX,StelleY,width,heigth,color);
    bild.fillStyle = TEXTFARBE;
    bild.fillText(text,toCoX(StelleX)+(WIDTHBOX*width)/TEXTOFFSET_WIDTH,toCoY(StelleY)+(heigth*HEIGHTBOX)/TEXTOFFSET_HEIGHT)
}

async function CreateCircleWithText(StelleX,StelleY,radius,text){
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
    if(ausgewaehlt.length==0){
        return true;
    }
    let number = Number(sitz.substring(1))
    for(let i=0;i<ausgewaehlt.length;i++){
        if((Math.abs(Number(ausgewaehlt[i].substring(1)) - number)<2) &&
           sitz.substring(0,1)==ausgewaehlt[i].substring(0,1)){
            return true;
        }
    }
    return false;
}

async function CreateSitzreihe(StelleX, StelleY, sitzeProTisch, tische, label, anfangsSitznummer, anfangsTischnummer){
    for(let i=0;i<tische;i++){
        CreateRectangleWithText(StelleX+1,StelleY+i*(sitzeProTisch/2),1,sitzeProTisch/2,label+(i+anfangsTischnummer),TISCHFARBE)
    }
    for(let i = 0; i < (sitzeProTisch * tische); i++){
        await CreateCircleWithText(i%2==0?StelleX:StelleX+2,
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
function LoadSitzplan(){
    let spalten = []
    for(let i = 0; i < 4; i++){
        spalten.push( 1 + i * 16)
    }
    let reihen = []
    for(let i = 0; i < 11; i++){
        reihen.push( 2 + i * 5)
    }
    
    CreateSitzreihe(reihen[0],spalten[0],6,5,"V",1,1)
    CreateSitzreihe(reihen[0],spalten[1],6,5,"A",1,1)
    CreateSitzreihe(reihen[0],spalten[2],6,5,"A",31,6)
    CreateSitzreihe(reihen[0],spalten[3],6,7,"A",61,11)

    CreateSitzreihe(reihen[1],spalten[1],6,5,"B",1,1)
    CreateSitzreihe(reihen[1],spalten[2],6,5,"B",31,6)
    CreateSitzreihe(reihen[1],spalten[3],6,7,"B",61,11)

    CreateSitzreihe(reihen[2],spalten[1],6,5,"C",1,1)
    CreateSitzreihe(reihen[2],spalten[2],6,5,"C",31,6)
    CreateSitzreihe(reihen[2],spalten[3],6,7,"C",61,11)

    CreateSitzreihe(reihen[3],spalten[1],6,5,"D",1,1)
    CreateSitzreihe(reihen[3],spalten[2],6,5,"D",31,6)
    CreateSitzreihe(reihen[3],spalten[3],6,7,"D",61,11)

    CreateSitzreihe(reihen[4],spalten[1],6,5,"E",1,1)
    CreateSitzreihe(reihen[4],spalten[2],6,5,"E",31,6)
    CreateSitzreihe(reihen[4],spalten[3],6,7,"E",61,11)

    CreateSitzreihe(reihen[5],spalten[1],6,5,"F",1,1)
    CreateSitzreihe(reihen[5],spalten[2],6,5,"F",31,6)
    CreateSitzreihe(reihen[5],spalten[3],6,7,"F",61,11)
    CreateCircleWithText(reihen[5]+1,spalten[3]+3*7,1,"F103")

    CreateSitzreihe(reihen[6],spalten[1],6,5,"G",1,1)
    CreateSitzreihe(reihen[6],spalten[2],6,5,"G",31,6)
    CreateSitzreihe(reihen[6],spalten[3],6,7,"G",61,11)

    CreateSitzreihe(reihen[7],spalten[1],6,5,"H",1,1)
    CreateSitzreihe(reihen[7],spalten[2],6,5,"H",31,6)
    CreateSitzreihe(reihen[7],spalten[3],6,7,"H",61,11)

    CreateSitzreihe(reihen[8],spalten[1],6,5,"I",1,1)
    CreateSitzreihe(reihen[8],spalten[2],6,5,"I",31,6)
    CreateSitzreihe(reihen[8],spalten[3],6,7,"I",61,11)

    CreateSitzreihe(reihen[9],spalten[1],6,5,"J",1,1)
    CreateSitzreihe(reihen[9],spalten[2],6,5,"J",31,6)
    CreateSitzreihe(reihen[9],spalten[3],6,7,"J",61,11)

    CreateSitzreihe(reihen[10],spalten[0],6,5,"Z",1,1)
    CreateSitzreihe(reihen[10],spalten[1],6,5,"K",1,1)
    CreateSitzreihe(reihen[10],spalten[2],6,5,"K",31,6)
    CreateSitzreihe(reihen[10],spalten[3],6,5,"K",61,11)
}




            