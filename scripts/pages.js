const la = document.querySelector("#leftArrow");
const ra = document.querySelector("#rightArrow");
const ul = document.querySelector("#pages");
const caedList = document.querySelector("#card-list");

let currPage = 1;
ul.addEventListener("click", async function (e) {
    if (e.target.matches("li")) {
        const selectedPage = e.target.innerHTML;

        const fd = new FormData();
        fd.append("selectedPage", selectedPage);
        const response = await fetch("pages.php", {
            method: "POST",
            body: fd
        });

        const data = await response.json();
        const cards = data["cardsData"];

        changeList(cards);

        currPage = parseInt(selectedPage);

    }
});

la.addEventListener("click", async function (e) {
    let selectedPage = currPage;
    if (currPage !== 1) {
        selectedPage = currPage - 1;

    } else {
        return
    }

    const fd = new FormData();
    fd.append("selectedPage", selectedPage);
    const response = await fetch("pages.php", {
        method: "POST",
        body: fd
    });


    const data = await response.json();
    const cards = data["cardsData"];

    changeList(cards);

    currPage = selectedPage;

}
);

ra.addEventListener("click", async function (e) {
    let selectedPage = 0;

    if (currPage != ra.dataset.maxpage) {

        selectedPage = currPage + 1;

    } else {
        return
    }
    console.log(selectedPage);


    console.log(typeof currPage);

    const fd = new FormData();
    fd.append("selectedPage", selectedPage);
    const response = await fetch("pages.php", {
        method: "POST",
        body: fd
    });


    const data = await response.json();
    const cards = data["cardsData"];

    changeList(cards);

    currPage = selectedPage;

}
);

function changeList(cards) {

    html = "";
    for (const key in cards) {

        pokecard = `
        <div class="pokemon-card">
            <div class="image clr-${cards[key].type}">
                <img src="${cards[key].image}" alt="The ${cards[key].name} pokemons picture.">
            </div>
            <div class="details">
                <h2><a href="details.php?id=${cards[key].id}">${cards[key].name}</a></h2>
                <span class="card-type"><span class="icon">🏷</span> ${cards[key].type}</span>
                <span class="attributes">
                    <span class="card-hp"><span class="icon">❤</span> ${cards[key].hp}</span>
                    <span class="card-attack"><span class="icon">⚔</span> ${cards[key].attack}</span>
                    <span class="card-defense"><span class="icon">🛡</span> ${cards[key].defense}</span>
                </span>
            </div>`;


        if (cards[key].buyable) {
            pokecard += `
            <div class="buy">
            <a href="trade.php?trade=buy&id=${cards[key].id}">
               <span class="card-price"><span class="icon">💰</span> ${cards[key].price}</span>
            </a>
         </div>`
        }
        if (cards[key].changeable) {
            pokecard += `<div class="exchange">
            <a href="exchange.php?visitor=giver&id=${cards[key].id}">
               <span class="card-price"><span class="icon">🔄</span>Csere</span>
            </a>
         </div>`
        }


        pokecard += "</div>";
        html += pokecard;

    }
    caedList.innerHTML = html;
}



ul.querySelector("li:nth-child(1)").click();
