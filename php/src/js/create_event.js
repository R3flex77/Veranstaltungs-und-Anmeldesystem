

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.add('active');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const imageInput = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    imageInput.value = '';
    previewImg.src = '';
    preview.classList.remove('active');
}
