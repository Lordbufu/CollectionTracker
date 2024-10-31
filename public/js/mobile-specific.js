/*  fetchRequest(uri, method, data): This function sends the request for album details to PhP, and returns the anwser. */
// async function fetchRequest( uri, method, data ) {
//     /* Try to fetch the requested data. */
//     try {
//         const response = await fetch( uri, {
//             method: method,
//             body: data
//         } );

//         /* Create new Error is the response is not ok. */
//         if( !response.ok ) {
//             throw new Error( `Response status: ${response.status}` );
//         /* Else simple return the response. */
//         } else {
//             return response;
//         }
//     /* console log any errors that are caught. */
//     } catch( error ) {
//         console.error( error.message );
//     }

//     return;
// }

// /*  viewDetails(e):
//         This function creates new form data, and sends a request to the server, so the details of said request can be displayed.
//         To make sure the pop-in is showed properly, i have to add the id to the url anchor, and reload the page.
//         If there is an error, i display that using the displayMessage() function.
//  */
// function viewDetails( e ) {
//     data = new FormData();
//     data.append( "album-index", e.target.id );

//     fetchRequest( "/details", "POST", data )
//         .then( ( resp ) => resp.text() )
//         .then( (text) => {
//             if( text === "display" ) {
//                 location.replace( "#" + "more-info-album" );
//                 location.reload();
//             } else {
//                 displayMessage( text );
//             }
//         });
// }