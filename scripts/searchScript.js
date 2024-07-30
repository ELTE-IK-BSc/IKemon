const searchList = document.querySelector("#typeseaech");

searchList.addEventListener("click", function (e) {
    if (e.target.matches("li") || e.target.matches("label")) {
        let checkID = "#type-";
        if (e.target.matches("label")) {
            checkID += e.target.innerText;
        }
        if (e.target.matches("li")) {
            checkID += e.target.querySelector("label").innerText;
        }

        const checkBox = document.querySelector(checkID);

        if (checkBox.checked === false) {
            checkBox.checked = true;
        } else {
            checkBox.checked = false;
        }
    }

})





const buys = document.querySelectorAll(".buy");

for (const div of buys) {
    div.addEventListener("click", function (e) {
        e.target.querySelector("a").click();
    })
}

const exchanges = document.querySelectorAll(".exchange");

for (const div of exchanges) {
    div.addEventListener("click", function (e) {
        e.target.querySelector("a").click();
    })
}