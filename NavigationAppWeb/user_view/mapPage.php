<?php
session_start();
include(__DIR__ . "/../core/connect.php");

// Validar sesión
if (!isset($_SESSION['login'])) {
    header("Location: loginPage.php");
    exit;
}
$user_id = $_SESSION['login'];
?>
<!DOCTYPE html>
<html>
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de viajes</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style> 
    html, body {
     height: 100%;
     width: 100%;
     margin: 0;
     padding: 0;
     overflow: hidden;
     background: #e4ebf4;
}
 #map {
     height: 100vh;
     width: 100vw;
     position: relative;
     z-index: 1;
}
 #sidebar, #instructions {
     position: fixed;
     top: 0;
     height: 100vh;
     background: rgba(255,255,255,0.15);
     border: 2px solid rgba(255,255,255,0.3);
     backdrop-filter: blur(20px);
     box-shadow: 0 0 30px rgba(0,0,0,0.33);
     display: flex;
     flex-direction: column;
     padding-left: 20px;
     padding-right: 20px;
     padding-top: 20px;
     z-index: 1000;
     overflow-y: auto;
}
 #sidebar {
     left: 0;
     width: 90vw;
     max-width: 400px; 
     border-radius: 0 20px 20px 0;
     align-items: center;
     overflow-y: auto;
}
 #instructions {
     right: 0;
     width: 90vw;
     max-width: 400px; 
     border-radius: 20px 0 0 20px;
     align-items: center;
     overflow-y: auto;

}
 #sidebar h3, #sidebar h4, #instructions h4 {
     text-align: center;
     color: #000;
}
 #sidebar input, #sidebar select, #sidebar button {
     width: 90%;
     margin: 8px 0;
     padding: 10px;
     border: none;
     border-radius: 8px;
     font-size: 1em;
}
 #sidebar button {
     background: #162938;
     color: #fff;
     cursor: pointer;
     transition: 0.2s;
}
 #sidebar button:hover {
     background: #0b1a26;
}
 .olcards, .olcards * {
     margin: 0;
     padding: 0;
     box-sizing: border-box;
}
 .olcards {
     list-style: none;
     counter-reset: cardCount;
     font-family: sans-serif;
     display: flex;
     flex-direction: column;
     --cardsGap: 1rem;
     gap: var(--cardsGap);
     padding-bottom: var(--cardsGap);
}
 .olcards li {
     counter-increment: cardCount;
     display: flex;
     color: white;
     --labelOffset: 1rem;
     --arrowClipSize: 1.5rem;
     margin-top: var(--labelOffset);
}
 .olcards li::before {
     content: counter(cardCount, decimal-leading-zero);
     background: white;
     color: var(--cardColor);
     font-size: 1.5em;
     font-weight: 700;
     transform: translateY(calc(-1 * var(--labelOffset)));
     margin-right: calc(-1 * var(--labelOffset));
     z-index: 1;
     display: flex;
     justify-content: center;
     align-items: center;
     padding-inline: 0.5em;
}
 .olcards li .content {
     background-color: var(--cardColor);
     --inlinePadding: 1em;
     --boxPadding: 0.5em;
     display: grid;
     padding: var(--boxPadding) calc(var(--inlinePadding) + var(--arrowClipSize)) var(--boxPadding) calc(var(--inlinePadding) + var(--labelOffset));
     grid-template-areas: "icon title" "icon text";
     gap: 0.25em 1em;
     clip-path: polygon( 0 0, calc(100% - var(--arrowClipSize)) 0, 100% 50%, calc(100% - var(--arrowClipSize)) 100%, calc(100% - var(--arrowClipSize)) calc(100% + var(--cardsGap)), 0 calc(100% + var(--cardsGap)) );
     position: relative;
     min-width: 90%;
     box-sizing: border-box;
}
 .olcards li .content::before {
     content: "";
     position: absolute;
     width: var(--labelOffset);
     height: var(--labelOffset);
     background: var(--cardColor);
     left: 0;
     bottom: 0;
     clip-path: polygon(0 0, 100% 0, 0 100%);
     filter: brightness(0.75);
}
 .olcards li .content::after {
     content: "";
     position: absolute;
     height: var(--cardsGap);
     width: var(--cardsGap);
     background: linear-gradient(to right, rgba(0, 0, 0, 0.25), transparent 50%);
     left: 0;
     top: 100%;
}
 .olcards li .icon {
     grid-area: icon;
     align-self: center;
     font-size: 1.5em;
}
 .olcards li .content .title {
     grid-area: title;
     font-size: 1.0em;
}
 .olcards li .content .text {
     grid-area: text;
}
 #tripList, #directionsList {
     width: 100%;
     max-height: 40vh;
     overflow-y: auto;
     padding-left: 10px;
     border: 1px solid rgba(0,0,0,0.2);
     border-radius: 10px;
     background: rgba(255,255,255,0.3);
     min-height: 40vh;
}
 #tripList li, #directionsList li {
     cursor: pointer;
}
 #tripList li:hover, #directionsList li:hover {
     background: #162938;
}
 .logout-button {
     position: fixed;
     left: 50px;
     top: 10px;
     right: 120px;
     z-index: 1001;
}
 .logout-button button {
     width: 50px;
     height: 50px;
     background: rgba(255,255,255,0.15);
     border: 2px solid rgba(255,255,255,0.3);
     backdrop-filter: blur(20px);
     box-shadow: 0 0 30px rgba(0,0,0,0.33);
     border-radius: 50%;
     cursor: pointer;
     display: flex;
     justify-content: center;
     align-items: center;
     transition: 0.2s;
}
 .logout-button button:hover {
     background: rgba(255,255,255,0.3);
}
 .logout-button ion-icon {
     font-size: 1.5em;
     color: #162938;
}
 .chat-ai {
     width: 100%;
     margin-top: 20px;
     height: 45vh;
     padding: 10px;
     border: 1px solid rgba(0,0,0,0.2);
     border-radius: 10px;
     background: rgba(255,255,255,0.3);
}
 .chat-ai #message {
     width: 94%;
     height: 250px;
     overflow-y: auto;
     border: 1px solid rgba(0,0,0,0.2);
     border-radius: 10px;
     background: rgba(255,255,255,0.15);
     padding: 10px;
     margin-bottom: 10px;
}
 .chat-ai #userInput {
     width: calc(100% - 80px);
     padding: 10px;
     border: 1px solid rgba(0,0,0,0.2);
     border-radius: 10px;
     background: rgba(255,255,255,0.15);
}
 .chat-ai button {
     width: 60px;
     height: 40px;
     margin-left: 10px;
     background: #162938;
     color: #fff;
     border: none;
     border-radius: 10px;
     cursor: pointer;
     transition: 0.2s;
     align-items: center;
     display: flex;
     justify-content: center;
}
 .chat-ai button:hover {
     background: #0b1a26;
}
 .input-ai{
     display: flex;
     align-items: center;
}
.toggle-btn,
.toggle-close-instruction,
.toggle-open-instruction {
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.15);
  border: 2px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(20px);
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.33);
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: 0.2s;
  z-index: 1001;
}

.toggle-btn:hover,
.toggle-close-instruction:hover,
.toggle-open-instruction:hover {
  background: rgba(255, 255, 255, 0.3);
}

.toggle-close-instruction ion-icon,
.toggle-open-instruction ion-icon {
  font-size: 2.2em;
  color: #162938;
}
.toggle-btn ion-icon{
    font-size: 1.8em;
    color: #162938;
}

#open-btn-sidebar{
    font-size: 0.97em !important;
}
.open-btn-div {
  position: fixed;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1001;
}

.close-btn-div {
  position: absolute;
  top: 15px;
  right: 15px;
  z-index: 1002;
}

.open-btn-instruction {
  position: fixed;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1001;
}

.close-btn-instruction {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 1002;
}
#close-btn-sidebar ion-icon{
    color: #162938;;
    margin-right:5px ;
}
#close-btn-sidebar{
    background: transparent !important;
    border-radius: 50% !important;
}
.form-container{
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.2);
    border-radius: 10px;
    background: rgba(255,255,255,0.3);
    padding: 5px;
    
}
    </style>
</head>
<body>
  <div id="sidebar">
  <h2>Navigation App Web <ion-icon name="earth-outline"></ion-icon></h2>
  <div class="form-container">

  Origin: <input id="origin" placeholder="Enter Origin..."><br>
  Destination: <input id="destination" placeholder="Enter Destination..."><br>
  Mode:
  <select id="travelMode">
    <option value="driving-car">Car</option>
    <option value="cycling-regular">Bicycle</option>
    <option value="foot-walking">Walking</option>
  </select><br>
  <button id="calcRoute">Calculate Route</button>
  <button id="exportPDF">Export PDF</button>
  </div>

  <h4>History</h4>
  <ol id="tripList" class="olcards"></ol>

  <div class="close-btn-div">
    <button class="toggle-btn" id="close-btn-sidebar">
      <ion-icon name="close-circle-outline"></ion-icon>
    </button>
  </div>
</div>

<div class="open-btn-div">
  <button class="toggle-btn" id="open-btn-sidebar">
    <ion-icon name="arrow-forward-circle-outline"></ion-icon>
  </button>
</div>

<form id="logout-button" class="logout-button" action="../core/logout.php" method="post">
  <button type="submit">
    <ion-icon name="log-out-outline" title="Logout"></ion-icon>
  </button>
</form>

<div class="instructions" id="instructions">
  <h4>Instructions</h4>
  <ol id="directionsList" class="olcards"></ol>

  <div class="chat-ai">
    <h4>Chat AI</h4>
    <div class="message" id="message"></div>
    <div class="input-ai">
      <input type="text" id="userInput" placeholder="Type your question...">
      <button id="sendBtn"><ion-icon name="send-outline"></ion-icon></button>
    </div>
  </div>

  <div class="close-btn-instruction">
    <button class="toggle-close-instruction" id="close-btn-instruction">
      <ion-icon name="close-circle-outline"></ion-icon>
    </button>
  </div>
</div>

<div class="open-btn-instruction">
  <button class="toggle-open-instruction" id="open-btn-instruction">
    <ion-icon name="arrow-back-circle-outline"></ion-icon>
  </button>
</div>

<div id="map"></div>

<script>
let map = L.map('map').setView([12.15, -86.25], 7); 
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

let currentRoute;
let stepMarkers = [];
let selectedMarker = null;
let selectedStep = null;

const userId = <?= json_encode($user_id) ?>;
document.getElementById('open-btn-sidebar').onclick = function() {
    document.getElementById('sidebar').style.transform = 'translateX(0)';
    this.style.display = 'none';
    document.getElementById('close-btn-sidebar').style.display = 'block';
    document.getElementById('logout-button').style.left = '460px';
};
document.getElementById('close-btn-sidebar').onclick = function() {
    document.getElementById('sidebar').style.transform = 'translateX(-100%)';
    this.style.display = 'none';
    document.getElementById('open-btn-sidebar').style.display = 'block';
    document.getElementById('logout-button').style.left = '50px';
};
document.getElementById('sidebar').style.transform = 'translateX(-100%)';
document.getElementById('close-btn-sidebar').style.display = 'none';
document.getElementById('open-btn-sidebar').style.display = 'block';
document.getElementById('open-btn-instruction').onclick = function() {
    document.getElementById('instructions').style.transform = 'translateX(0)';
    this.style.display = 'none';
    document.getElementById('close-btn-instruction').style.display = 'block';
};
document.getElementById('close-btn-instruction').onclick = function() {
    document.getElementById('instructions').style.transform = 'translateX(100%)';
    this.style.display = 'none';
    document.getElementById('open-btn-instruction').style.display = 'block';
};
document.getElementById('instructions').style.transform = 'translateX(100%)';
document.getElementById('close-btn-instruction').style.display = 'none';
document.getElementById('open-btn-instruction').style.display = 'block';

function getDirectionIcon(instruction) {
  const text = instruction.toLowerCase();
  if (text.includes('destination')) return '<ion-icon name="flag-outline"></ion-icon>';
  if (text.includes('right')) return '<ion-icon name="arrow-forward-outline"></ion-icon>';
  if (text.includes('left')) return '<ion-icon name="arrow-back-outline"></ion-icon>';
  if (text.includes('straight')) return '<ion-icon name="arrow-up-outline"></ion-icon>';
  if (text.includes('around')) return '<ion-icon name="refresh-outline"></ion-icon>';

  return '<ion-icon name="navigate-outline"></ion-icon>';
}

async function loadTrips() {
    try {
        let res = await fetch('../core/load_trip.php');
        let trips = await res.json();
        const list = document.getElementById('tripList');
        list.innerHTML = '';
        trips.forEach(trip => {
            let li = document.createElement('li');
            li.style.setProperty('--cardColor','#162938');
            li.innerHTML = `
                <div class="content">
                    <div class="icon"><ion-icon name="globe-outline"></ion-icon></div>
                    <div class="title">From ${trip.Origin} To ${trip.Destination}</div>
                    <div class="text">Mode: ${trip.Mode}</div>
                </div>
            `;
            li.onclick = () => {
                document.getElementById('origin').value = trip.Origin;
                document.getElementById('destination').value = trip.Destination;
                document.getElementById('travelMode').value = trip.Mode;

                if(currentRoute) map.removeLayer(currentRoute);
                currentRoute = L.geoJSON(JSON.parse(trip.Route)).addTo(map);
                map.fitBounds(currentRoute.getBounds());

                const directionsList = document.getElementById('directionsList');
                directionsList.innerHTML = '';
                stepMarkers.forEach(m => map.removeLayer(m));
                stepMarkers = [];
                
                if(trip.Step){
                        let steps = [];
                        try {
                            steps = typeof trip.Step === 'string' ? JSON.parse(trip.Step) : trip.Step;
                        } catch (err) {
                          console.error("Error al parsear Step:", err, trip.Step);
                        return;
                    }
                    steps.forEach((step,index) => {
                        let liStep = document.createElement('li');
                        const icon = getDirectionIcon(step.instruction);
                        liStep.style.setProperty('--cardColor','#162d59');
                        liStep.innerHTML = `
                            <div class="content">
                                <div class="icon">${icon}</div>
                                <div class="title">Step ${index+1}</div>
                                <div class="text">${step.instruction}</div>
                            </div>
                        `;
                        directionsList.appendChild(liStep);
                   if(step.way_points){
                    const startIndex = step.way_points[0];
                    const [lon, lat] = JSON.parse(trip.Route).coordinates[startIndex];

                    const marker = L.circleMarker([lat, lon], { 
                    radius:5, 
                    color:'red', 
                    fillColor:'red', 
                    fillOpacity:0.7 
                    }).addTo(map);

                    stepMarkers.push(marker);

                    liStep.addEventListener('mouseenter', () => { 
                     marker.setStyle({color:'yellow', fillColor:'yellow', radius:8}); 
                     marker.bringToFront(); 
                    });
                    liStep.addEventListener('mouseleave', () => { 
                   if(marker !== selectedMarker) marker.setStyle({color:'red', fillColor:'red', radius:5}); 
                   });

                   liStep.addEventListener('click', () => { 
                   map.panTo([lat, lon]);
 
                   if(selectedMarker && selectedMarker !== marker){
                      selectedMarker.setStyle({color:'red', fillColor:'red', radius:5});
                      }

                   selectedMarker = marker;

                   marker.setStyle({color:'yellow', fillColor:'yellow', radius:8});
                   marker.bringToFront();

                   marker.bindPopup(step.instruction).openPopup();

                   marker.once('popupclose', () => {
                   marker.setStyle({color:'red', fillColor:'red', radius:5});
                   selectedMarker = null;
        });
    });
}

                    });
                }
            };
            list.appendChild(li);
        });
    } catch(err){ console.error("Error cargando viajes:", err); }
}
loadTrips();

document.getElementById('calcRoute').onclick = async function() {
    const origin = document.getElementById('origin').value;
    const destination = document.getElementById('destination').value;
    const mode = document.getElementById('travelMode').value;
    if(!origin || !destination){ alert("Enter Origin and Destination"); return; }

    try{
        const res = await fetch('../core/calculate_super_route.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({origin,destination,mode})
        });
        const data = await res.json();
        if(data.error){ alert("Error: " + data.error); return; }

        const routeObj = (typeof data.Route==='string')?JSON.parse(data.Route):data.Route;
        if(currentRoute) map.removeLayer(currentRoute);
        currentRoute = L.geoJSON(routeObj).addTo(map);
        const bounds = currentRoute.getBounds();
        if(bounds.isValid()) map.fitBounds(bounds);

        alert(`Distance: ${data.Distance.toFixed(2)} km\nDuration: ${data.Duration} min (${mode})`);

        const directionsList = document.getElementById('directionsList');
        directionsList.innerHTML = '';
        stepMarkers.forEach(m=>map.removeLayer(m));
        stepMarkers = [];

        if(data.Steps && data.Steps.length>0){
            data.Steps.forEach((step,index)=>{
                let liStep = document.createElement('li');
                const icon = getDirectionIcon(step.instruction);
                liStep.style.setProperty('--cardColor','#162938');
                liStep.innerHTML = `
                    <div class="content">
                        <div class="icon">${icon}</div>
                        <div class="title">Step ${index+1}</div>
                        <div class="text">${step.instruction}</div>
                    </div>
                `;
                directionsList.appendChild(liStep);

                    if(step.way_points){
                    const startIndex = step.way_points[0];
                    const [lon, lat] = JSON.parse(trip.Route).coordinates[startIndex];

                    const marker = L.circleMarker([lat, lon], { 
                    radius:5, 
                    color:'red', 
                    fillColor:'red', 
                    fillOpacity:0.7 
                    }).addTo(map);

                    stepMarkers.push(marker);

                    liStep.addEventListener('mouseenter', () => { 
                     marker.setStyle({color:'yellow', fillColor:'yellow', radius:8}); 
                     marker.bringToFront(); 
                    });
                    liStep.addEventListener('mouseleave', () => { 
                    if(marker !== selectedMarker) marker.setStyle({color:'red', fillColor:'red', radius:5}); 
                    });

                   liStep.addEventListener('click', () => { 
                   map.panTo([lat, lon]);
 
                   if(selectedMarker && selectedMarker !== marker){
                      selectedMarker.setStyle({color:'red', fillColor:'red', radius:5});
                      }

                   selectedMarker = marker;

                   marker.setStyle({color:'yellow', fillColor:'yellow', radius:8});
                   marker.bringToFront();

                   marker.bindPopup(step.instruction).openPopup();

                   marker.once('popupclose', () => {
                   marker.setStyle({color:'red', fillColor:'red', radius:5});
                   selectedMarker = null;
        });
    });
}
});
}

        loadTrips();

    }catch(err){ console.error("Error calculando ruta:",err); alert("No se pudo calcular la ruta"); }
};

document.getElementById('exportPDF').onclick = function(){
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF();
    doc.text("Viaje",10,10);
    doc.text("Origen: "+document.getElementById('origin').value,10,20);
    doc.text("Destino: "+document.getElementById('destination').value,10,30);
    doc.text("Modo: "+document.getElementById('travelMode').value,10,40);

    const directionsList = document.getElementById('directionsList');
    if(directionsList.children.length>0){
        doc.text("Instrucciones:",10,50);
        let y=60;
        Array.from(directionsList.children).forEach(li=>{
            doc.text("- " + li.textContent,10,y);
            y+=10;
        });
    }
    doc.save("viaje.pdf");
};
navigator.geolocation.getCurrentPosition(pos=>{
    const {latitude, longitude} = pos.coords;
    map.setView([latitude, longitude], 13);
    L.circleMarker([latitude, longitude], { radius:7, color:'blue', fillColor:'blue', fillOpacity:0.7 }).addTo(map)
        .bindPopup("You are here").openPopup();
    document.getElementById('origin').value = `${latitude}, ${longitude}`;
}, err=>{ console.warn("Geolocation error:", err); });

document.getElementById('sendBtn').onclick = async function() {
    const userInput = document.getElementById('userInput').value;
    if(!userInput) return;
    const messageDiv = document.getElementById('message');
    messageDiv.innerHTML += `<p><strong>You:</strong> ${userInput}</p>`;
    document.getElementById('userInput').value = '';

    try {
        const res = await fetch('../core/chat_ai.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({question: userInput})
        });
        const data = await res.json();
        if(data.error) {
            messageDiv.innerHTML += `<p style="color:red;"><strong>Error:</strong> ${data.error}</p>`;
        } else {
            
            messageDiv.innerHTML += `<p><strong>AI:</strong> ${data.ai_message}</p>`;
        }
        messageDiv.scrollTop = messageDiv.scrollHeight;
    } catch(err) {
        console.error("Chat AI error:", err);
        messageDiv.innerHTML += `<p style="color:red;"><strong>Error:</strong> Could not get response</p>`;
        messageDiv.scrollTop = messageDiv.scrollHeight;
    }
};
</script>
</body>
</html>
