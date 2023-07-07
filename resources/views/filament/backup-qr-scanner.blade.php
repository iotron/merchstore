<div>
    <div id="preview-container">
        <video id="preview" class="h-full w-full bg-gray-800"></video>
    </div>

    <div id="error-message" style="display: none;">
        <p>No cameras found.</p>
        <label for="qr-image">Upload QR Image:</label>
        <input type="file" id="qr-image" accept="image/*" onchange="processQRImage()">
    </div>
</div>


<script>
    console.log('this is running');

    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    scanner.addListener('scan', function (content) {
        console.log(content);
    });
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error('No cameras found.');
            showErrorMessage();
        }
    }).catch(function (e) {
        console.error(e);
        showErrorMessage();
    });


    function showErrorMessage() {
        document.getElementById('preview-container').style.display = 'none';
        document.getElementById('error-message').style.display = 'block';
    }

    function processQRImage(event) {
        const file = event.target.files[0];
        // Perform QR code scanning logic on the uploaded image
        // ...
        alert("processing");
    }


    document.addEventListener('livewire:load', function () {
        // Your JS here.
        console.log('readdd');
    })
</script>


