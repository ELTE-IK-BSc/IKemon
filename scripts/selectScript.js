const cardList = document.querySelector("#card-list");

cardList.addEventListener("click", function name(e) {
    if (!e.target.matches("#card-list") || !e.target.matches("a")) {

        const inpId = "#" + e.target.dataset.cardname;
        const rb = document.querySelector(inpId);
        rb.checked = true;

        const cards = cardList.children;
        for (let index = 0; index < cards.length; index++) {
            if (cards[index].id === "selectedCard") {
                cards[index].id = "";
            }
        }

        rb.previousElementSibling.id = "selectedCard";
    }

})

