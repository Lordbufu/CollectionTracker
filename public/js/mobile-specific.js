/* Unless i add a different view details option, to the user and admin pages, this file will not be used and can be removed. */

// Test code to view album details as a user.
async function fetchRequest( uri, method, data ) {
    const url = uri;                                                            // Set url path.

    try {                                                                       // Try the following,
        const response = await fetch( url, {                                    // Store fetch in response using await,
            method: method,                                                     // use the method agrument,
            body: data                                                          // use the data parameter/
        } );

        if( !response.ok ) {                                                    // If the respone is not ok,
            throw new Error( `Response status: ${response.status}` );           // throw a new error.
        }

        return response;                                                        // return the reponse otherwhise.

    } catch( error ) {                                                          // If and error was cought,
        console.error( error.message );                                         // console log that for now.
    }

    return;                                                                     // return just in case.
}

// Test code: To send data and load the pop-in.
function viewDetails( e ) {
    data = new FormData();                                                      // Create new formdata,
    data.append( "album-index", e.target.id );                                  // add album-index.

    fetchRequest( "/details", "POST", data )                                    // Send request to PhP,
        .then( ( resp ) => resp.text() )                                        // set response to text(),
        .then( (text) => {                                                      // pull the value out of text(),
            if( text === "display" ) {                                          // if its display,
                location.replace( "#" + "more-info-album" );                    // and then unhide the pop-in,
                location.reload()                                               // reload the page ¿,
            } else {                                                            // if its not display,
                displayMessage( text );                                         // give user feedback.
            }
        });
}