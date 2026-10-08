function getCurrentLocation(callback) {
    if (!navigator.geolocation) {
        callback(null, 'Geolocation tidak didukung browser.');
        return;
    }
    navigator.geolocation.getCurrentPosition(function(position) {
        callback(position.coords, null);
    }, function(error) {
        callback(null, error.message);
    }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    });
}
