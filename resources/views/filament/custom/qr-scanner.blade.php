<div>
    <video id="preview"></video>
</div>

<script type="text/javascript">
    function initializeScanner() {
        // Request camera permission
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
                scanner.addListener('scan', function (content) {
                    console.log(content);
                });
                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        console.error('No cameras found.');
                    }
                }).catch(function (e) {
                    console.error(e);
                });
            })
            .catch(function (error) {
                console.error('Camera permission denied:', error);
            });
    }

    // Prompt for camera permission
    if (typeof navigator.mediaDevices.getUserMedia === 'function') {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                // Permission granted, initialize scanner
                initializeScanner();
            })
            .catch(function (error) {
                console.error('Camera permission denied:', error);
            });
    } else {
        console.error('getUserMedia is not supported in this browser.');
    }
</script>
