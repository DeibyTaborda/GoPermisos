// Mostrar nombres de archivos seleccionados
document.getElementById('fileInput').addEventListener('change', function(e) {
    const files = e.target.files;
    const fileNames = document.getElementById('fileNames');
    const submitButton = document.getElementById('submitButton');
    
    if(files.length > 0) {
        let names = '';
        for(let i = 0; i < files.length; i++) {
            names += '<div><i class="fas fa-file me-2"></i>' + files[i].name + '</div>';
        }
        fileNames.innerHTML = names;
        submitButton.style.display = 'inline-block';
    } else {
        fileNames.innerHTML = '';
        submitButton.style.display = 'none';
    }
});

// Para el formulario de incapacidad
document.getElementById('fileInput2')?.addEventListener('change', function(e) {
    const files = e.target.files;
    const fileNames = document.getElementById('fileNames2');
    const submitButton = document.getElementById('submitButton2');
    
    if(files.length > 0) {
        fileNames.innerHTML = '<div><i class="fas fa-file me-2"></i>' + files[0].name + '</div>';
        submitButton.style.display = 'inline-block';
    } else {
        fileNames.innerHTML = '';
        submitButton.style.display = 'none';
    }
});