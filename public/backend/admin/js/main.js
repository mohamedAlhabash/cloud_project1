let tabs = document.querySelectorAll(".tabs li");
let tabsArray = Array.from(tabs);

let divs = document.querySelectorAll(".content > div");
let divsArray = Array.from(divs);



tabsArray.forEach((ele) => {
  ele.addEventListener("click", function (e) {
    tabsArray.forEach((ele) => {
      ele.classList.remove("active");
    });

    e.currentTarget.classList.add("active");

    divsArray.forEach((div) => {
      div.style.display = "none";
    });
    document.querySelector(e.currentTarget.dataset.cont).style.display =
      "block";
  });
});




        var xValues = [" miss rate", "hit rate", "number of items in cache", "total size ofitems in cache",
         "number of requests served per minute", "number of worker"];
        var yValues = [60, 85, 50, 36, 80, 77];
        var barColors = ["blue", "blue", "blue", "blue", "blue", "blue"];

        new Chart("myChart", {
            type: "bar",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                legend: { display: false },
                title: {
                    display: true,
                    text: "chart"
                },
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                            suggestedMax: 100,


                        }
                    }]
                }




            }
        });



var current = 1;
const min = 1;
const max = 8;

function storageAvailable(type) {
    var storage;
    try {
        storage = window[type];
        var x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
    }
    catch(e) {
        return e instanceof DOMException && (
            // everything except Firefox
            e.code === 22 ||
            // Firefox
            e.code === 1014 ||
            // test name field too, because code might not be present
            // everything except Firefox
            e.name === 'QuotaExceededError' ||
            // Firefox
            e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
            // acknowledge QuotaExceededError only if there's something already stored
            (storage && storage.length !== 0);
    }
}

//This function should be called each time you update the progress so the display updates as well
// function updateProgress()
// {
// 	document.querySelector(
//     ".progress-text"
//   ).innerHTML = ` The Memcache Pool Size is <input name="value" value=${current} readonly style="padding: 5px ; background: transparent; border: none; outline: none; font-size:16px; font-weight: bold;color: red"></input>` ;
// 	document.querySelector(".progress-bar").style.width = Math.round((current / max) * 100) + "%"; //this sets the length of the progress-bar to a percentage
// 	document.querySelector(".progress-percentage").innerText = Math.round((current / max) * 100) + "%"; //this displays the progress as a percentage
// }

//this function is called each time the increase button is clicked
function onButtonIncreaseClicked(e)
{
	e.preventDefault(); //prevent the button from doing anything else

	//make sure the current value never goes above the max value
	if (current < max)
		current++;

	//this will set the value in the localStorage as long as it is available and accessible
	if (storageAvailable('localStorage'))
	{
		localStorage.setItem("progress", current); //our "key" is set to contain the value of current
	}

	// updateProgress(); //call to update the display
}

//identical to the increase button, only decreases
function onButtonDecreaseClicked(e)
{
	e.preventDefault();

	//prevent us from going below the minimum value
	if (current > min)
		current--;

	if (storageAvailable('localStorage'))
	{
		localStorage.setItem("progress", current);
	}

	// updateProgress(); //always call to update the display
}

//this function is to register the button click event handlers. This makes clicking the buttons work. You should only call this once per page load.
function registerHandlers()
{
	document.querySelector("#btnIncrease").addEventListener("click", onButtonIncreaseClicked);
	document.querySelector("#btnDecrease").addEventListener("click", onButtonDecreaseClicked);
}

//this function is to unregister the button click event handlers. This prevents the buttons from working. Just incase.
function unregisterHandlers()
{
	document.querySelector("#btnIncrease").removeEventListener("click", onButtonIncreaseClicked);
	document.querySelector("#btnDecrease").removeEventListener("click", onButtonDecreaseClicked);
}

//This is our setup function. You should call this once per page load.
function setup()
{
	//check if localstorage is available and if there's a valid number in it
	if (storageAvailable('localStorage') && localStorage.getItem("progress") != null && !isNaN(localStorage.getItem("progress")))
	{
		current = parseInt(localStorage.getItem("progress")); //load our stored value

		if (current > max) //make sure the stored value is not above our max value
			current = max;
		else if (current < min) //make sure the stored value is not below our min value
			current = min;
	}

	registerHandlers(); //make clicking buttons work
	// updateProgress(); //update the display
}

setup(); //our call to setup everything, should be called once per page load.

// slider



function slider_value() {
  let slider = document.getElementById("cache-capcity");
let value = document.getElementById("value");
  value.innerHTML = `${slider.value} %`;
}



function slider_value1() {
  let slider1 = document.getElementById("cache-capcity1");
let value1 = document.getElementById("value1");
  value1.innerHTML = `${slider1.value} %`;
}
function slider_value2() {
  let slider2 = document.getElementById("cache-capcity2");
let value2 = document.getElementById("value2");
  value2.innerHTML = `${slider2.value} `;
}


function slider_value3() {
  let slider3 = document.getElementById("cache-capcity3");
let value3 = document.getElementById("value3");
  value3.innerHTML = `${slider3.value} `;
}
function slider_value4() {
  let slider4 = document.getElementById("cache-capcity4");
let value4 = document.getElementById("value4");
  value4.innerHTML = `${slider4.value} MB `;
}
