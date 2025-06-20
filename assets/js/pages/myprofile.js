const rol = localStorage.getItem('rol');

if (rol == 3 || rol == 2) {
    showElement('checkboxes-departmens-profile');
    hideElement('singleDepartmentProfile');
} else if (rol == 1) {
    showElement('singleDepartmentProfile');
    hideElement('checkboxes-departmens-profile');
}

function showElement(id) {
    document.getElementById(id).style.display = 'block';
}

function hideElement(id) {
    document.getElementById(id).style.display = 'none';
}