const msgButton = document.querySelector("#msgbutton");
const messages = document.querySelector("#messages");

let clicked = false;
msgButton.addEventListener("click", function (e) {

    console.log(e.target);

    if (!clicked) {
        messages.hidden = false;
        clicked = true;

    } else {
        messages.hidden = true;
        clicked = false;

    }


})


