document.addEventListener('DOMContentLoaded', function () {
    const inputPassword = document.getElementById("inputPassword");
    const radioButtons = document.querySelectorAll('input[name="passwordVisibility"]');

    // Función para actualizar el tipo de la contraseña
    function updatePasswordVisibility() {
        const checkedRadio = document.querySelector('input[name="passwordVisibility"]:checked');

        if (checkedRadio && checkedRadio.value === "ViewPassword" && inputPassword.type === "password") {
            inputPassword.type = "text";  // Mostrar la contraseña
        } else if (checkedRadio && checkedRadio.value === "HiddenPassword" && inputPassword.type === "text") {
            inputPassword.type = "password";  // Ocultar la contraseña
        }
    }
    function removeChecked() {
        radioButtons.forEach(radio => {
            radio.removeAttribute('name');
        })
    }

    // Llamar a la función de actualización cuando la página se cargue
    updatePasswordVisibility();

    // Agregar un listener para cuando cambie el radio
    radioButtons.forEach(radio => {
        radio.addEventListener('change', (event) => {
            if (event.target.checked) {
                updatePasswordVisibility();  // Actualizar tipo de la contraseña
            }
        });
    });

    // Asignar el evento al botón de envío
    const submitButton = document.getElementById("submitButton");
    submitButton.addEventListener('click', function (event) {
        removeChecked();  // Llamar a removeChecked cuando se haga clic en el botón
    });
});
