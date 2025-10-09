<script>
    function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('imagePreview');
        var container = document.getElementById('imagePreviewContainer');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            container.style.display = 'none';
        }
    }
</script>