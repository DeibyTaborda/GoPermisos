
$('#fileInput').change(function() {
    const preview = $('#preview');
    preview.empty();
    
    if (this.files.length > 0) {
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo demasiado grande',
                    text: 'El archivo ' + file.name + ' excede el límite de 5MB',
                    confirmButtonColor: '#e74c3c'
                });
                continue;
            }
            
            if (!file.type.match('image.*')) {
                continue;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewItem = $('<div class="preview-item">')
                    .append($('<img>').attr('src', e.target.result))
                    .append($('<div class="preview-item-remove">').html('&times;'));
                
                previewItem.find('.preview-item-remove').click(function() {
                    // Crear un nuevo DataTransfer para eliminar el archivo
                    const dt = new DataTransfer();
                    const input = document.getElementById('fileInput');
                    
                    for (let j = 0; j < input.files.length; j++) {
                        if (j !== i) {
                            dt.items.add(input.files[j]);
                        }
                    }
                    
                    input.files = dt.files;
                    previewItem.remove();
                });
                
                preview.append(previewItem);
            }
            
            reader.readAsDataURL(file);
        }
    }
});