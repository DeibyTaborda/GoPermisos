const selectRol = document.getElementById('RolID');
const selectDepartment = document.getElementById('DepartmentID');
const containerCheckboxes = document.getElementById('container-checkboxes');
const form = document.getElementById('form-user');

selectRol.addEventListener('change', () => {
    const rol = parseInt(selectRol.value);

    if (rol === 3) {
        selectDepartment.style.display = 'none';
        containerCheckboxes.style.display = 'grid';
        selectDepartment.value = '';

    } else {
        selectDepartment.style.display = 'flex';
        containerCheckboxes.style.display = 'none';

        const checkboxes = containerCheckboxes.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    }
});

form.addEventListener('submit', (e) => {
    const rol = parseInt(selectRol.value);

    if (rol === 3) {
        selectDepartment.removeAttribute('name');
    } else {
        const checkboxes = containerCheckboxes.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.removeAttribute('name'));
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const userDataElement = document.getElementById('dataUserToEdit');
    if (!userDataElement) return;

    const rol = parseInt(userDataElement.dataset.rol);
    const action = userDataElement.dataset.action;

    if (action === 'edit') {
        if (rol === 3) {
            containerCheckboxes.style.display = 'grid';
            selectDepartment.style.display = 'none';
        } else {
            containerCheckboxes.style.display = 'none';
            selectDepartment.style.display = 'flex';
        }
    }
});

const buttonSubmit = document.getElementById('button-submit');

document.getElementById('form-user').addEventListener('submit', function() {
    const buttonSubmit = document.getElementById('button-submit');
    setTimeout(() => {
        buttonSubmit.disabled = true;
        buttonSubmit.value = "Enviando...";
    }, 0);
});