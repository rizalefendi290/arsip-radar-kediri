document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.getElementById('dropdown-toggle');
    const dropdownMenu = document.getElementById('dropdown-menu');

    dropdownToggle.addEventListener('click', function () {
        dropdownMenu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside of it
    document.addEventListener('click', function (event) {
        if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });
});

var url = '/uploads/'; // Ganti dengan path ke file PDF Anda

// Inisialisasi PDF.js
var pdfjsLib = window['pdfjs-dist/build/pdf'];

// URL worker PDF.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

// Ambil dokumen PDF
var loadingTask = pdfjsLib.getDocument(url);
loadingTask.promise.then(function(pdf) {
    console.log('PDF loaded');

    // Dapatkan halaman pertama
    var pageNumber = 1;
    pdf.getPage(pageNumber).then(function(page) {
        console.log('Page loaded');

        var scale = 1.5;
        var viewport = page.getViewport({ scale: scale });

        // Siapkan elemen canvas
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        document.getElementById('pdf-viewer').appendChild(canvas);

        // Render halaman PDF ke dalam canvas
        var renderContext = {
            canvasContext: context,
            viewport: viewport
        };
        var renderTask = page.render(renderContext);
        renderTask.promise.then(function() {
            console.log('Page rendered');
        });
    });
}, function(reason) {
    console.error(reason);
});