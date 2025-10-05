<!-- Modal for Image Preview -->
<div class="modal fade" id="imgPreviewModal" tabindex="-1" aria-labelledby="imgPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imgPreviewModalLabel">Preview Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="imgPreviewModalImg" class="img-fluid" alt="Preview" style="max-height:70vh;">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var previewLinks = document.querySelectorAll('.img-preview-link');
        var modalImg = document.getElementById('imgPreviewModalImg');
        previewLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var imgSrc = this.getAttribute('data-img');
                modalImg.src = imgSrc;
            });
        });
        // Clear image on modal close
        var imgModal = document.getElementById('imgPreviewModal');
        imgModal.addEventListener('hidden.bs.modal', function () {
            modalImg.src = '';
        });
    });
</script>