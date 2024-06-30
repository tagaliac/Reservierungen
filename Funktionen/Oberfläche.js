const GLOBALE_VARIABLE_LINK ="./Globale_Variablen.json";

async function initSprache(Sprache){
    if(Sprache==null){
        Sprache = await ladeSprache();
    }

    setSprache(Sprache);
}

async function initKundenSprache(Sprache){
    if(Sprache==null){
        await ladeSprache().then(data => {
            Sprache = data
        }).catch();
    }
    Sprache += "Kunde";

    setSprache(Sprache);
}

function ladeSprache(){
    return new Promise((resolve,reject) => {
        fetch(GLOBALE_VARIABLE_LINK).then((response) => response.json()).then(data => {
            resolve(data['Sprache']);
        }).catch(error => reject(error))
    }) 
}

async function setSprache(Sprache){
    switch (Sprache){
        case "Deutsch":
            await fetch("./Sprachen/Deutsch.json").then((response) => response.json()).then(data => {
                setLanguage(data)
            }).catch(error => console.log(error))
            break;
        case "Griechisch":
            await fetch("./Sprachen/Griechisch.json").then((response) => response.json()).then(data => {
                setLanguage(data);
            }).catch(error => console.log(error))
            break;
        default:
            await fetch("./Sprachen/"+Sprache+".json").then((response) => response.json()).then(data => {
                setLanguage(data)
            }).catch(error => {console.log("Sprache kann nicht geladen werden "+error)})
    }
}

async function getTranslationFromAusgabe(key, Sprache){
    value = "confirm";

    switch (Sprache){
        case "Deutsch":
            await fetch("./Sprachen/DeutschAusgabe.json").then((response) => response.json()).then(data => {
                value = data[key];
            }).catch(error => console.log(error))
            break;
        case "Griechisch":
            await fetch("./Sprachen/GriechischAusgabe.json").then((response) => response.json()).then(data => {
                value = data[key];
            }).catch(error => console.log(error))
            break;
        default:
            console.log("Sprache kann nicht geladen werden ");
    }
    return value;
}

function setWord(id, text){
    try{
        document.getElementById(id).innerHTML = text;
    }catch{}
}

function setLanguage(translater){
    for(let value in translater){
        setWord(value,translater[value]);
    }
}