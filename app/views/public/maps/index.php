<?php require APPROOT . '/views/inc/head.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .public-map-frame { height: 700px; width: 100%; background: #000; border: 1px solid #ccc; }
    #public-map { height: 100%; width: 100%; }
</style>

<div class="container-fluid">
    <h2 class="py-2 text-dark">Ars Rosaic Regional Map</h2>
    <div class="public-map-frame">
        <div id="public-map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const pins = <?php echo json_encode($data['pins'] ?? []); ?>;
    const covens = <?php echo json_encode($data['covens'] ?? []); ?>;

    const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { maxZoom: 17 });
    const streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });

    const map = L.map('public-map', {
        center: [39.82, -98.57],
        zoom: 4,
        layers: [topo] 
    });

    const baseMaps = { "Topographic": topo, "Street View": streets };
    L.control.layers(baseMaps).addTo(map);

    const boundsGroup = L.featureGroup();

    /**
     * REGIONAL BOUNDARIES
     * Regions 1 (Pacific) through 4 (Eastern)
     */
    const regions = [
        { color: "#3498db", bounds: [[-90, -125], [90, -114]] }, // Region 1
        { color: "#e67e22", bounds: [[-90, -114], [90, -102]] }, // Region 2
        { color: "#2ecc71", bounds: [[-90, -102], [90, -87]] },  // Region 3
        { color: "#e74c3c", bounds: [[-90, -87], [90, -67]] }    // Region 4
    ];

    regions.forEach(r => {
        L.rectangle(r.bounds, {
            color: r.color,
            weight: 2,
            fillOpacity: 0.07,
            interactive: false
        }).addTo(map);
    });

    /**
     * Plot Members
     */
    pins.forEach(p => {
        const lat = parseFloat(p.lat);
        const lon = parseFloat(p.lon);
        const name = p.chosen_name ?? 'Member';
        const grade = parseInt(p.grade ?? 0);

        if (!isNaN(lat) && !isNaN(lon)) {
            let color = 'rgba(0,0,0,0)'; 
            switch(grade) {
                case 2: color = '#808080'; break;
                case 3: color = '#ffffff'; break;
                case 5: color = '#800080'; break;
                case 6: color = '#ff0000'; break;
                case 8: color = '#ffff00'; break;
                case 9: color = '#0000ff'; break;
            }

            const marker = L.circleMarker([lat, lon], {
                radius: 6, 
                color: '#333', 
                weight: 1, 
                fillColor: color, 
                fillOpacity: 0.8
            }).addTo(map).bindPopup(`<b>Member:</b> ${name}`);
            boundsGroup.addLayer(marker);
        }
    });

    /**
     * Plot Covens
     * Corrected: Using c.name to match the data table
     */
    covens.forEach(c => {
        const cLat = parseFloat(c.lat);
        const cLon = parseFloat(c.lon);
        // Fallback check for 'name' or 'coven_name'
        const cName = c.name ?? c.coven_name ?? 'Unknown Coven';
        const mgrs = c.mgrs_coord ?? 'N/A';
        const email = c.contact_email ?? 'office@arsrosaic.org';
        
        if(!isNaN(cLat) && !isNaN(cLon)) {
            let covenColor = (c.is_active == 1) ? '#6f42c1' : '#6c757d';
            const covenCircle = L.circle([cLat, cLon], {
                color: covenColor, 
                fillOpacity: 0.1, 
                radius: 16093.4 // 10 Miles
            }).addTo(map).bindPopup(`<b>Coven:</b> ${cName}<br><br><b>MGRS:</b> ${mgrs}<br><b>Lat:</b> ${cLat}<br><b>Long:</b> ${cLon}<br><br><b>Inquiries:</b><br> <a href="mailto:${email}">${email}</a>`);
            boundsGroup.addLayer(covenCircle);
        }
    });

    if (boundsGroup.getLayers().length > 0) {
        map.fitBounds(boundsGroup.getBounds(), { padding: [40, 40] });
    }

    setTimeout(() => { map.invalidateSize(); }, 300);
</script>

<?php require APPROOT . '/views/inc/foot.php'; ?>
