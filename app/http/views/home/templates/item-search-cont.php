<form class="item-zoek-form" onsubmit="event.preventDefault()">
    <label class="item-zoek-lab" for="item-zoek-inp">Item Zoeken:</label>
        <div class="search-opt-cont">
            
            <div class="search-opt-naam-cont">
                <label class="search-opt-naam-lab" for="item-zoek-naam-inp">Naam:</label> <br>
                <label class="item-zoek-naam" for="item-zoek-naam-inp">
                    <input class="item-zoek-naam-inp" id="item-zoek-naam-inp" type="checkbox" />
                    <span class="item-zoek-naam-slider"> </span>
                </label>
            </div>

            <div class="search-opt-itemNr-cont">
                <label class="search-opt-itemNr-lab" for="item-zoek-nr-inp">Item Nr:</label> <br>
                <label class="item-zoek-nr" for="item-zoek-nr-inp">
                    <input class="item-zoek-nr-inp" id="item-zoek-nr-inp" type="checkbox" />
                    <span class="item-zoek-nr-slider"></span>
                </label>
            </div>

            <div class="search-opt-isbn-cont">
                <label class="search-opt-isbn-lab" for="item-zoek-isbn-inp">Item Isbn:</label> <br>
                <label class="item-zoek-isbn" for="item-zoek-isbn-inp">
                    <input class="item-zoek-isbn-inp" id="item-zoek-isbn-inp" type="checkbox" />
                    <span class="item-zoek-isbn-slider"></span>
                </label>
            </div>
        </div>

    <label class="modal-form-label" for="item-zoek-inp">
        <input class="modal-form-input" id="item-zoek-inp" type="text" placeholder="" />
        <span class="modal-form-span" id="item-zoek-span">Zoek naar items..</span>
    </label>
</form>

<style>
    .item-zoek-form {
        display: grid;
        justify-items: center;
        margin: 0.1em 0.2em;
    }
    
    .item-zoek-lab {
        font-weight: bold;
    }
    
    .search-opt-cont {
        display: flex;
        margin-bottom: 0.4em;
    }
    
    .search-opt-naam-cont, .search-opt-itemNr-cont, .search-opt-isbn-cont {
        font-size: 0.7em;
        border: var(--main-border-body);
        border-radius: var(--main-border-rad);
        margin: 0.1em;
        width: 6em;
        height: 2.7em;
    }
    
    .item-zoek-naam, .item-zoek-nr, .item-zoek-isbn {
        position: relative;
        display: inline-block;
        width: 3em;
        height: 1.5em;
        font-size: 0.7em;
    }
    
    .item-zoek-naam-inp, .item-zoek-nr-inp, .item-zoek-isbn-inp {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .item-zoek-naam-slider, .item-zoek-nr-slider, .item-zoek-isbn-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: var(--main-slid-backgr);
        border-radius: 1.5em;
        -webkit-transition: .4s;
        transition: .4s;
    }
    
    .item-zoek-naam-slider:before, .item-zoek-nr-slider:before, .item-zoek-isbn-slider:before {
        position: absolute;
        content: '';
        height: 1.5em;
        width: 1.5em;
        left: 0.01em;
        background-color: var(--main-knob-col);
        border-radius: var(--main-border-rad);
        -webkit-transition: .4s;
        transition: .4s;
    }

    .item-zoek-naam-inp:checked + .item-zoek-naam-slider, .item-zoek-nr-inp:checked + .item-zoek-nr-slider, .item-zoek-isbn-inp:checked + .item-zoek-isbn-slider {
        background-color: var(--main-slid-backgr-2);
    }

    .item-zoek-naam-inp:checked + .item-zoek-naam-slider:before, .item-zoek-nr-inp:checked + .item-zoek-nr-slider:before, .item-zoek-isbn-inp:checked + .item-zoek-isbn-slider:before {
        -webkit-transform: translateX(1.5em);
        -ms-transform: translateX(1.5em);
        transform: translateX(1.5em);
    }
</style>

<script>
    /* Listen Event for the item search input, and a input state disabled by default. */
    const itemZoekInp = document.getElementById('item-zoek-inp');
    itemZoekInp.addEventListener('input', itemZoek);
    
    if(itemZoekInp) {
        itemZoekInp.disabled = true;
    }

    /* All checkboxes and there listen events. */
    let chb1 = document.getElementById('item-zoek-naam-inp');
    chb1.addEventListener('change', checkBoxSearch);
    let chb2 = document.getElementById('item-zoek-nr-inp');
    chb2.addEventListener('change', checkBoxSearch);
    let chb3 = document.getElementById('item-zoek-isbn-inp');
    chb3.addEventListener('change', checkBoxSearch);

    /* The span that is the text in side the search input. */
    let span = document.getElementById('item-zoek-span');

    if(localStorage.checkState) {
        /* Store keys & values to reduce code clutter. */
        const stateKey = JSON.parse(localStorage.checkState)[0];
        const stateVal = JSON.parse(localStorage.checkState)[1];
        /* Get the correct checkbox, and set its state using the stored value. */
        const checkbox = document.getElementById(stateKey);
        checkbox.checked = stateVal;

        /* The span text loop, changing depending on what checkbox was checked. */
        if(checkbox === 'item-zoek-naam-inp' && stateVal) {
            span.innerHTML = 'Zoek op item naam';
        } else if(checkbox === 'item-zoek-nr-inp' && stateVal) {
            span.innerHTML = 'Zoek op item nummer';
        } else if(checkbox === 'item-zoek-isbn-inp' && stateVal) {
            span.innerHTML = 'Zoek op item isbn';
        }

        /* What todo if a checkbox was unselected. */
        if(!stateVal) {
            span.innerHTML = 'Selecteer een zoek optie ..';
            itemZoekInp.disabled = true;
        }

        /* End logic by removing the stored data. */
        localStorage.removeItem('checkState');
    }

    /*  checkBoxSearch(event):
            This function makes sure the search option checkboxes, cant all be active at the same time, and changes the inner input text.
            If there is an input value when changing search options, it will dispatch an input event, so the search code is triggered/updated.
                checkArr    (JS Array)                  - An new JS Array, that stored the element id and checked state of the checkbox that was changed.
                inputEvent  (JS Event)                  - An new JS Event, that can be used to trigger a input event used for the search function.
                checkState  (JS Array -> JSON String)   - The checkArr converted with JSON Stringify, and stored in the local browser storage for later use.
            
            Return Value: None.
     */
    function checkBoxSearch(event) {
        /* Create a array from the checkbox id and state, and store that inside the browser. */
        const checkArr = new Array(event.target.id, event.target.checked);
        localStorage.setItem('checkState', JSON.stringify(checkArr));
        
        /* If the checkbox was checked, */
        if( event.target.checked === true ) {
            /* Create a new input event, */
            let inputEvent = new Event ('input', {
                'bubbles': true,
                'cancelable': false
            });

            /* If the input was disabled, i enable it for the user. */
            if(itemZoekInp.disabled) {
                itemZoekInp.disabled = false;
            }

            /* If the name checkbox was used, change the span and make sure the other checkboxes are off. */
            if(event.target.id === 'item-zoek-naam-inp') {
                span.innerHTML = 'Zoek op item naam', chb2.checked = false, chb3.checked = false;
            } else if(event.target.id === 'item-zoek-nr-inp') {
                span.innerHTML = 'Zoek op item nummer', chb1.checked = false, chb3.checked = false;
            } else if(event.target.id === 'item-zoek-isbn-inp') {
                span.innerHTML = 'Zoek op item isbn', chb1.checked = false, chb2.checked = false;
            }

            /* If there is a input value, we dispatch the input event. */
            if(itemZoekInp.value) {
                return itemZoekInp.dispatchEvent(inputEvent);
            }
        } else {
            span.innerHTML = 'Selecteer een zoek optie ..';
            return itemZoekInp.disabled = true;
        }
    }

    /*  itemZoek(event):
            Searches the items displayed on the page, matching them on a letter by letter basis.
     */
    function itemZoek(e) {
        const filter = itemZoekInp.value.toUpperCase();
        const tafelRows = document.querySelectorAll('#items-table-content');

        /* We are searching on a name basis */
        if(chb1.checked === true) {
            tafelRows.forEach((item, index) => {
                const itemNaam = item.children[1].innerHTML;

                if(itemNaam.toUpperCase().indexOf(filter) > -1) {
                    return tafelRows[index].style.display = '';
                } else {
                    return tafelRows[index].style.display = 'none';
                }
            });
        /* We are searching on a album nr basis */
        } else if(chb2.checked === true) {
            tafelRows.forEach((item, index) => {
                const itemNr = item.children[2].innerHTML;

                if(itemNr.toUpperCase().indexOf(filter) > -1) {
                    return tafelRows[index].style.display = '';
                } else {
                    return tafelRows[index].style.display = 'none';
                }
            });
        /* We are searching on a album isbn basis */
        } else if(chb3.checked === true) {
            tafelRows.forEach((item, index) => {
                const itemIsbn = item.children[6].innerHTML;

                if(itemIsbn.toUpperCase().indexOf(filter) > -1) {
                    return tafelRows[index].style.display = '';
                } else {
                    return tafelRows[index].style.display = 'none';
                }
            });
        }
    }
</script>