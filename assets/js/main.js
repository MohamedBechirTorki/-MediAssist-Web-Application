

let darkStylsheet = document.getElementById('darkMode');
darkStylsheet.disabled = true;
let lightDarkBtn = document.getElementById('changeColor');
let lightDarkPic = document.querySelector('#changeColor img');
console.log(lightDarkPic)
lightDarkBtn.onclick = () => {
    console.log("clicked");
    darkStylsheet.disabled = !darkStylsheet.disabled;
    if (!darkStylsheet.disabled) {
        lightDarkPic.src = "./assets/images/sun.png"
    }
    else {
        lightDarkPic.src = "./assets/images/moon.jpg"
    }
    console.log(lightDarkPic)
}