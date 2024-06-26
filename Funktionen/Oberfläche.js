const GLOBALE_VARIABLE_LINK ="./Globale_Variablen.json";
var jsonData;

async function initSprache(Sprache){
    if(Sprache==null){
        await fetch(GLOBALE_VARIABLE_LINK).then((response) => response.json()).then(data => {
            jsonData=data
            Sprache = data['Sprache'];
        }).catch(error => console.log(error))
    }

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
            console.log("Sprache kann nicht geladen werden")
}
}

function setWord(id, text){
    try{
        document.getElementById(id).innerHTML = text;
    }catch{

    }
}

function setLanguage(translater){
    for(let value in translater){
        setWord(value,translater[value]);
    }
}