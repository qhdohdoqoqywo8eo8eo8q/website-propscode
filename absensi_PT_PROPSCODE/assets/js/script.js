function initWebcam(targetSelector, previewSelector, fieldName) {
    if (typeof Webcam === 'undefined') {
        return;
    }
    Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
    Webcam.attach(targetSelector);
    var button = document.querySelector('[data-capture="' + targetSelector + '"]');
    if (!button) return;
    button.addEventListener('click', function() {
        Webcam.snap(function(data_uri) {
            document.querySelector(previewSelector).innerHTML = '<img src="' + data_uri + '" class="img-fluid rounded border">';
            var input = document.querySelector('input[name="' + fieldName + '"]');
            if (input) {
                input.value = data_uri;
            }
        });
    });
}
