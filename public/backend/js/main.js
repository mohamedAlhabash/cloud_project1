// let bar = document.querySelector( "nav .bar" );
let nav = document.querySelector( "nav" );
let ul = document.querySelector( "nav ul" );
// let upload = document.getElementById( 'upload' );
let img = document.querySelector( ".image img" );
let card = document.querySelector( ".card" );

let capacity_range = document.getElementById( "range" );
let value = document.querySelector( ".value" );


// bar.onclick = function ()
// {
//     ul.classList.toggle( "active" );
//     bar.style.display = 'none';
//     close.classList.add( "active" );
// };

close.onclick = function ()
{
    ul.classList.toggle( "active" );
    close.classList.remove( "active" );
    bar.style.display = 'initial';


};

function increse ()
{
    value.innerHTML = capacity_range.value;
};

// upload.onchange = function ()
// {
//     let file = new FileReader();
//     file.readAsDataURL( upload.files[ 0 ] );
//     file.onload = function ()
//     {
//         img.src = file.result;
//     }
// }




