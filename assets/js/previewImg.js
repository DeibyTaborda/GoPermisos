document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const fileInput2 = document.getElementById('fileInput2');
    const submitButton = document.getElementById('submitButton');
    const submitButton2 = document.getElementById('submitButton2');
    

    if (fileInput) {
        fileInput.addEventListener('change', function(event) {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
            const files = event.target.files;
            let hasImages = false;

            for (let file of files) {
                if (file.type.startsWith('image/')) {
                    hasImages = true;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100px';
                        img.style.margin = '5px';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('application/')) {
                    hasImages = true;
                    const img = document.createElement('img');
                    img.src = 'assets/images/pdf_icon.png';
                    img.style.width = '100px';
                    img.style.margin = '5px';
                    preview.appendChild(img);
                }
            }

            submitButton.style.display = hasImages ? 'block' : 'none';

            if (fileInput2) {
                fileInput2.style.display = 'none';
                submitButton2.style.display = 'none';
            }
        });
    }

    if (fileInput2) {
        fileInput2.addEventListener('change', function(event) {
            const preview = document.getElementById('preview2');
            preview.innerHTML = '';
            const files = event.target.files;
            let hasImages = false;

            for (let file of files) {
                if (file.type.startsWith('image/')) {
                    hasImages = true;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100px';
                        img.style.margin = '5px';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('application/')) {
                    hasImages = true;
                    const img = document.createElement('img');
                    img.src = 'assets/images/pdf_icon.png';
                    img.style.width = '100px';
                    img.style.margin = '5px';
                    preview.appendChild(img);
                }
            }

            submitButton2.style.display = hasImages ? 'block' : 'none';
            if (fileInput) {
                fileInput.style.display = 'none';
                submitButton.style.display = 'none';
            }
        });
    }
});
