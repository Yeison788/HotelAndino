let map, placesService, markers = [], radiusCircle, info;

const CATEGORY_TO_TYPE = {
  tourist_attraction: ['tourist_attraction'],
  museum: ['museum'],
  park: ['park'],
  church: ['church'],
  art_gallery: ['art_gallery']
};

function initMap(){
  map = new google.maps.Map(document.getElementById('map'), {
    center: HOTEL,
    zoom: 15,
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: true
  });

  info = new google.maps.InfoWindow();
  placesService = new google.maps.places.PlacesService(map);

  // Marca del hotel (estrellita dorada)
  new google.maps.Marker({
    position: HOTEL,
    map,
    title: "Hotel Andino",
    icon: { url: "https://maps.google.com/mapfiles/kml/paddle/ylw-stars.png" }
  });

  document.getElementById('btnBuscar').addEventListener('click', buscar);
  buscar(); // primera carga
}

function getSelectedTypes(){
  const checks = [...document.querySelectorAll('#categories input[type=checkbox]:checked')];
  const types = new Set();
  checks.forEach(ch => (CATEGORY_TO_TYPE[ch.value]||[ch.value]).forEach(t=>types.add(t)));
  return [...types];
}

function clearMarkers(){
  markers.forEach(m => m.setMap(null));
  markers = [];
  if (radiusCircle) { radiusCircle.setMap(null); radiusCircle = null; }
}

function buscar(){
  clearMarkers();

  const radius = parseInt(document.getElementById('radius').value, 10);
  const types = getSelectedTypes();
  if (!types.length) types.push('tourist_attraction');

  // círculo de radio
  radiusCircle = new google.maps.Circle({
    center: HOTEL, radius,
    strokeColor: '#b8860b', strokeOpacity: .9, strokeWeight: 1.5,
    fillColor: '#d4af37', fillOpacity: .08, map
  });
  map.fitBounds(radiusCircle.getBounds());

  // una búsqueda por cada tipo (simple y efectivo)
  types.forEach(type => {
    placesService.nearbySearch({
      location: HOTEL,
      radius,
      type
    }, (results, status) => {
      if (status !== google.maps.places.PlacesServiceStatus.OK || !results) return;
      results.forEach(place => addPlaceMarker(place));
    });
  });
}

function addPlaceMarker(place){
  if (!place.geometry || !place.geometry.location) return;
  const m = new google.maps.Marker({
    position: place.geometry.location,
    map,
    title: place.name
  });
  m.addListener('click', () => {
    const rating = place.rating ? `⭐ ${place.rating.toFixed(1)}` : 'Sin calificación';
    const addr = place.vicinity || place.formatted_address || '';
    const link = `https://www.google.com/maps/place/?q=place_id:${place.place_id}`;
    info.setContent(`
      <div style="min-width:220px">
        <div style="font-weight:700;margin-bottom:2px">${escapeHtml(place.name)}</div>
        <div style="font-size:12px;color:#555;margin-bottom:4px">${escapeHtml(addr)}</div>
        <div style="font-size:12px;margin-bottom:6px">${rating}</div>
        <a class="btn btn-sm btn-dark" target="_blank" rel="noopener" href="${link}">Ver en Google Maps</a>
      </div>
    `);
    info.open(map, m);
  });
  markers.push(m);
}

// util
function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

window.initMap = initMap;
