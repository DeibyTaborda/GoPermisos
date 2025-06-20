document.addEventListener('DOMContentLoaded', () => {
const form = document.getElementById('userForm');
const roleSelect = document.getElementById("RolID");
const permissionsContainer = document.getElementById("dynamic-permissions");
const departmentDiv = document.getElementById('department');
const inputWithIcon = departmentDiv.querySelector('.input-with-icon');
const addBtn = document.getElementById("add-permission-group");
let permissionIndex = 0;

// 🧱 Crea un <select> con opciones y valor seleccionado
function createSelect(name, options, placeholder, selectedValue = "") {
    const select = document.createElement("select");
    select.name = name;
    select.classList.add("form-control", "mb-2");
    select.required = true;

    let html = `<option value="">${placeholder}</option>`;
    options.forEach(opt => {
        const value = opt.id;
        const label = opt.DepartmentName || opt.sede || opt.rol;
        const selected = value == selectedValue ? "selected" : "";
        html += `<option value="${value}" ${selected}>${label}</option>`;
    });

    select.innerHTML = html;
    return select;
}

// 🧱 Crea un grupo de selects para permiso
function createPermissionGroup(index, data = {}) {
    const group = document.createElement("div");
    group.classList.add("permission-group", "border", "p-3", "mb-2", "position-relative");

    // Selects
    const deptSelect = createSelect(`user_permissions[${index}][DepartmentID]`, departments, 'Seleccione Departamento', data.DepartmentID);
    const sedeSelect = createSelect(`user_permissions[${index}][SedeID]`, sedes, 'Seleccione Sede', data.SedeID);
    const rolSelect = createSelect(`user_permissions[${index}][RoleID]`, roles, 'Seleccione Rol', data.RoleID);

    // Botón eliminar
    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.textContent = "Eliminar";
    removeBtn.classList.add("btn", "btn-danger", "btn-sm", "mt-2");
    removeBtn.onclick = () => group.remove();

    // Agregamos todo al grupo
    group.appendChild(deptSelect);
    group.appendChild(sedeSelect);
    group.appendChild(rolSelect);
    group.appendChild(removeBtn);

    return group;
}

function createTitleUserPermissions(titulo) {
    const title = document.getElementById('title-permissions-department');
    title.textContent = titulo;
    return title;
}

// Función para limpiar el contenedor de departamento
function clearDepartmentContainer() {
    // Remover cualquier select existente en el contenedor de departamento
    const existingSelect = inputWithIcon.querySelector('select');
    if (existingSelect) {
        existingSelect.remove();
    }
}

// 🧠 Mostrar permisos existentes al cargar
window.addEventListener("DOMContentLoaded", () => {
    // Inicialmente ocultar el contenedor de departamento
    departmentDiv.style.display = "none";
    
    if (existingPermissions.length > 0) {
        addBtn.style.display = "inline-block";
        createTitleUserPermissions('Departamentos, Sedes y Roles a Gestionar:')
        existingPermissions.forEach((perm) => {
            permissionsContainer.appendChild(createPermissionGroup(permissionIndex, perm));
            permissionIndex++;
        });

        inputWithIcon.appendChild(deptSelect);
    } else if (collaboratorDepartment && collaboratorDepartment.length > 0) {
        addBtn.style.display = "none";
        departmentDiv.style.display = "block"; // Mostrar el contenedor
        const deptSelect = createSelect(
            `DepartmentID`,
            departments,
            'Seleccione Departamento',
            collaboratorDepartment[0]
        );
        inputWithIcon.appendChild(deptSelect);
    }
});

// 🧪 Manejo según selección de rol (RolID)
roleSelect.addEventListener("change", function () {
    const selected = parseInt(this.value);
    
    // Limpiar contenedores
    permissionsContainer.innerHTML = '';
    clearDepartmentContainer();
    permissionIndex = 0;

    if (selected == 1) {
        // Rol 1 (Colaborador): Mostrar select de departamento simple
        addBtn.style.display = "none";
        departmentDiv.style.display = "block"; // Mostrar todo el contenedor
        
        let deptSelect;
        if (collaboratorDepartment && collaboratorDepartment.length > 0) {
            deptSelect = createSelect(`DepartmentID`, departments, 'Seleccione Departamento', collaboratorDepartment[0]);
        } else {
            deptSelect = createSelect(`DepartmentID`, departments, 'Seleccione Departamento');
        }
        inputWithIcon.appendChild(deptSelect);
        
        createTitleUserPermissions(''); // Limpiar título
        
    } else if (selected == 2) {
        // Rol 2 (Líder): Mostrar select de departamento Y permisos avanzados
        departmentDiv.style.display = "block"; // Mostrar todo el contenedor
        addBtn.style.display = "inline-block";
        
        // Agregar select simple de departamento
        let deptSelect;
        if (collaboratorDepartment && collaboratorDepartment.length > 0) {
            deptSelect = createSelect(`DepartmentID`, departments, 'Seleccione Departamento', collaboratorDepartment[0]);
        } else {
            deptSelect = createSelect(`DepartmentID`, departments, 'Seleccione Departamento');
        }
        inputWithIcon.appendChild(deptSelect);
        
        createTitleUserPermissions('Departamentos, Sedes y Roles a Gestionar:');
        if (existingPermissions.length > 0) {
            existingPermissions.forEach((perm) => {
            permissionsContainer.appendChild(createPermissionGroup(permissionIndex, perm));
            permissionIndex++;
            });
        } else {
            permissionsContainer.appendChild(createPermissionGroup(permissionIndex));
        }
        permissionIndex++;
        
    } else if (selected == 3) {
        // Rol 3 (Admin): Solo permisos avanzados, sin departamento simple
        departmentDiv.style.display = "none"; // Ocultar todo el contenedor
        addBtn.style.display = "inline-block";
        
        createTitleUserPermissions('Departamentos, Sedes y Roles a Gestionar:');
        if (existingPermissions.length > 0) {
            existingPermissions.forEach((perm) => {
            permissionsContainer.appendChild(createPermissionGroup(permissionIndex, perm));
            permissionIndex++;
            });
        } else {
            permissionsContainer.appendChild(createPermissionGroup(permissionIndex));
        }
        permissionIndex++;
        
    } else {
        // Otros roles: ocultar todo
        departmentDiv.style.display = "none"; // Ocultar todo el contenedor
        addBtn.style.display = "none";
        createTitleUserPermissions('');
    }
});

// ➕ Agregar nuevo grupo
addBtn.addEventListener("click", function () {
    permissionsContainer.appendChild(createPermissionGroup(permissionIndex));
    permissionIndex++;
});

    // Validación y manejo del submit del formulario principal
    form?.addEventListener('submit', e => {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + submitBtn.textContent;

        const password = document.getElementById('Password');
        const confirmPassword = document.getElementById('confirmpassword');
        if (password && confirmPassword && password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + 
                (submitBtn.textContent.includes('ACTUALIZAR') ? 'ACTUALIZAR USUARIO' : 'AGREGAR USUARIO');
            return false;
        }
        return true;
    });

    // Activar spinner de envío en otros formularios
    procesandoBtnSubmit('#form-departments');
    procesandoBtnSubmit('#form-leavetype');
    procesandoBtnSubmit('#form-apply-leave');
});

// Spinner de procesamiento para otros formularios
function procesandoBtnSubmit(idForm) {
    $(document).ready(() => {
        $(idForm).submit(function () {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            setTimeout(() => {
                if (submitBtn.prop('disabled')) {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(`<i class="fas fa-save"></i> ${submitBtn.text()}`);
                }
            }, 5000);
        });
    });
}

// Función reutilizable para obtener datos
async function obtenerUsuarios(url) {
    try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);
        return await res.json();
    } catch (error) {
        console.error('Error al obtener departamentos:', error);
        return null;
    }
}