// 3GB En Bytes
document.addEventListener("DOMContentLoaded", function () {
    const MAX_FILE_SIZE = 1 * 1024 * 1024 * 1024; // Límite de 3GB

    document.getElementById("buttonUploadForm").addEventListener("click", function (event) {
        const fileInput = document.getElementById("formInput");

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];

            if (file.size > MAX_FILE_SIZE) {
                // Detener el envío del formulario
                event.preventDefault();
                const url = "http://172.17.0.2:8080/error_size_file.php";
                // Redirigir a la URL
                window.location.href = url;

                return false;
            }
        }
    });
});
