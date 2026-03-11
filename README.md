# mtg-excel-tool
A tool to attach uuid from [MTGJSON](https://github.com/mtgjson/mtgjson) to Excel card list. From there any additional data can be added.

---

The tool came to be out of necessity: I happened to acquire a *large-ish* collection of MTG cards.
I needed a way to run inventory and check prices.

I know there are online tools, but they were always either too much or not enough for me. Ironically, a standard Excel spreadsheet was flexible enough when my collection was managable and I was trading a couple of cards every few weeks.

Now with boxes of cards I needed something more.

---

First of all, what's the minimal amount of data needed to identify a card?
For a database it would be **Set Code** and **Number**. That's it.  
To keep the spreadsheet useful, **Name**, **Rarity** and **Foil?** were added. 

---

I needed prices specifically from cardmarket.com but their API is not public, which makes sense: I can't imagine allowing people like me access to a tool like that for no reason.

---

Luckily there exists [MTGJSON](https://github.com/mtgjson/mtgjson): an open-source repository of Magic: The Gathering card data.  
Even more luckily, they have current prices of all the cards in all the online stores!