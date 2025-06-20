// Configuración general de Chart.js
Chart.defaults.font.family = "'Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.color = '#666';

// Colores para gráficas
const chartColors = {
    primary: '#4e73df',
    success: '#1cc88a',
    info: '#36b9cc',
    warning: '#f6c23e',
    danger: '#e74a3b',
    secondary: '#858796',
    light: '#f8f9fc',
    dark: '#5a5c69',
    // Colores adicionales para gráficas con muchas categorías
    colors: [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#fd7e14', '#6f42c1', '#20c9a6', '#27a844', '#e83e8c',
        '#5a5c69', '#2c9faf', '#3c54b4', '#13855c', '#1d6fa5'
    ]
};

// Almacenar referencias a los gráficos para poder actualizarlos
let charts = {};

// Iniciar el dashboard al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar filtros de fecha
    initDateFilters();
    
    // Cargar datos iniciales del dashboard
    loadDashboardData();
    
    // Event listeners para filtros
    document.getElementById('yearFilter').addEventListener('change', loadDashboardData);
    document.getElementById('monthFilter').addEventListener('change', loadDashboardData);
    document.getElementById('dayFilter').addEventListener('change', loadDashboardData);
    document.getElementById('sedeFilter').addEventListener('change', loadDashboardData);
    document.getElementById('departmentFilter').addEventListener('change', loadDashboardData);
});

// Inicializar los filtros de fecha
function initDateFilters() {
    const currentYear = new Date().getFullYear();
    const yearSelect = document.getElementById('yearFilter');
    
    // Poblar años (desde 2020 hasta el actual)
    for (let year = 2020; year <= currentYear; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) option.selected = true;
        yearSelect.appendChild(option);
    }
}

// Función principal para cargar todos los datos del dashboard
function loadDashboardData() {
    const year = document.getElementById('yearFilter').value;
    const month = document.getElementById('monthFilter').value;
    const day = document.getElementById('dayFilter').value;
    const sede = document.getElementById('sedeFilter').value;
    const department = document.getElementById('departmentFilter').value;
    
    const params = new URLSearchParams();
    if (year) params.append('year', year);
    if (month && month !== '0') params.append('month', month);
    if (day && day !== '0') params.append('day', day);
    if (sede && sede !== '0') params.append('sede', sede);
    if (department && department !== '0') params.append('department', department);

    initDepartments();
    toggleDepartmentCharts();

    // Cargar resumen del dashboard
    loadEstadoDashboard(params);
    
    // Cargar gráficas principales
    if (sede == '0' || sede === '1') {
        loadSolicitudesPorDepartamento(params);
        loadTopDepartamentosPermisos(params);
        loadTopDepartamentosDias(params);
        loadPromedioDiasDepartamento(params);
    }

    loadSolicitudesPorEstado(params);
    loadTendenciasTiempo(params, 'month');
    loadTipologiaPermisos(params);
    loadDistribucionRolPermisos(params);
    
    // Cargar top empleados (inicialmente sin filtro de departamento)
    loadTopEmpleadosDepartamento(params);
}

function initDepartments() {
    const sede = document.getElementById('sedeFilter').value;
    fetchDataByParams('../api/api.php', {'sede_id' : sede})
    .then(departments => {
      createOptionsDepartments(departments);
    })
}

function fetchDataByParams(url, params = {}) {
    const searchParams = new URLSearchParams(params).toString();
    const fullUrl = `${url}?${searchParams}`;
    return fetch(fullUrl)
        .then(response => response.json());
}

function createOptionsDepartments(departments) {
    const selectDepartment = document.getElementById('departmentFilter');

    // Elimina todas las opciones excepto la seleccionada (si hay alguna seleccionada)
    const selectedOption = selectDepartment.querySelector('option:checked');
    selectDepartment.innerHTML = '';
    if (selectedOption) {
        selectDepartment.appendChild(selectedOption);
    }

    // Opción por defecto
    const defaultOption = document.createElement('option');
    defaultOption.value = "0";
    defaultOption.text = 'Todos';
    selectDepartment.appendChild(defaultOption);

    departments?.forEach(department => {
        const option = document.createElement('option');
        option.value = department.department_id;
        option.text = department.DepartmentName;
        selectDepartment.appendChild(option);
    });
}

function toggleDepartmentCharts() {
    const sede = document.getElementById('sedeFilter').value;
    const department = document.getElementById('departmentFilter').value;

    const chartDisplays = {
        'container-top': 'flex',
        'apps-by-dept': 'block',
        'avg-days-dept': 'block'
    };

    const isCentral = sede === '0' || sede === '1';
    const existDepartment = department === '0';

    Object.entries(chartDisplays).forEach(([id, display]) => {
        if (isCentral && existDepartment) {
            showElement(id, display);
            moveElementToPosition({elementId: 'total-requests-by-type', targetContainerId: 'penultima-fila', position: 'first'})
            moveElementToPosition({elementId: 'temporal-trends', targetContainerId: 'secondary-charts'})
        } else {
            hideElement(id);
            moveElementToPosition({elementId: 'total-requests-by-type', targetContainerId: 'main-charts-container'})
            moveElementToPosition({elementId: 'temporal-trends', targetContainerId: 'penultima-fila'})
        }
    });
}

function moveElementToPosition({ elementId, targetContainerId, position = 'last' }) {
    const elementToMove = document.getElementById(elementId);
    const targetContainer = document.getElementById(targetContainerId);

    if (!elementToMove || !targetContainer) return;

    if (position === 'last') {
        targetContainer.appendChild(elementToMove);
    } else if (position === 'first') {
        targetContainer.prepend(elementToMove);
    }
}

function showElement(id, display = 'block') {
    const element = document.getElementById(id);
    if (element) element.style.display = display;
}

function hideElement(id) {
    const element = document.getElementById(id);
    if (element) element.style.display = 'none';
}

// 1. Cargar estadísticas generales del dashboard
function loadEstadoDashboard(params) {
    const url = `../api/api_dashboard.php?action=estadoDashboard&${params.toString()}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalSolicitudes').textContent = data.total_solicitudes || 0;
            document.getElementById('totalDias').textContent = data.total_dias || 0;
            console.log(data);
            // Llenar estadísticas por estado
              const estadosContainer = document.getElementById('estadosSolicitudes');
            estadosContainer.innerHTML = '';
            
            if (data.por_estado && data.por_estado.length > 0) {
                data.por_estado.forEach(estado => {
                    const estadoItem = document.createElement('div');
                    estadoItem.classList.add('col-md-4', 'mb-3');
                    
                    let badgeClass = 'badge-primary';
                    switch(estado.status.toLowerCase()) {
                        case 'pendiente': badgeClass = 'badge-warning'; break;
                        case 'aprobado': badgeClass = 'badge-success'; break;
                        case 'no aprobado': badgeClass = 'badge-danger'; break;
                        case 'cancelado': badgeClass = 'badge-secondary'; break;
                        case 'anulado': badgeClass = 'badge-dark'; break;
                    }
                    
                    estadoItem.innerHTML = `
                        <div class="card border-left-${badgeClass.replace('badge-', '')} shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-${badgeClass.replace('badge-', '')} text-uppercase mb-1">
                                            ${estado.status}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">${estado.total}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    estadosContainer.appendChild(estadoItem);
                });
            }
        })
        .catch(error => console.error('Error cargando estadísticas del dashboard:', error));
}

// 2. Cargar gráfica de solicitudes por departamento
function loadSolicitudesPorDepartamento(params) {
    const url = `../api/api_dashboard.php?action=solicitudesPorDepartamento&${params.toString()}`;
    const ctx = document.getElementById('chartSolicitudesDepartamento').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.DepartmentName);
            const values = data.map(item => item.total_solicitudes);
            
            if (charts.solicitudesDepartamento) {
                charts.solicitudesDepartamento.destroy();
            }
            
            charts.solicitudesDepartamento = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Solicitudes',
                        data: values,
                        backgroundColor: chartColors.colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Solicitudes por Departamento'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando solicitudes por departamento:', error));
}

// 3. Cargar gráfica de solicitudes por estado
function loadSolicitudesPorEstado(params) {
    const url = `../api/api_dashboard.php?action=solicitudesPorEstado&${params.toString()}`;
    const ctx = document.getElementById('chartSolicitudesEstado').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.status);
            const values = data.map(item => item.total);
            console.log(data);
            
            // Colores específicos para cada estado
            const backgroundColors = [];
            labels.forEach(label => {
                switch(label.toLowerCase()) {
                    case 'pendiente': backgroundColors.push(chartColors.warning); break;
                    case 'aprobado': backgroundColors.push(chartColors.success); break;
                    case 'no aprobado': backgroundColors.push(chartColors.danger); break;
                    case 'cancelado': backgroundColors.push(chartColors.secondary); break;
                    case 'anulado': backgroundColors.push(chartColors.dark); break;
                    default: backgroundColors.push(chartColors.primary);
                }
            });
            
            if (charts.solicitudesEstado) {
                charts.solicitudesEstado.destroy();
            }
            
            charts.solicitudesEstado = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Solicitudes por Estado'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando solicitudes por estado:', error));
}

// 4. Cargar top empleados por departamento
function loadTopEmpleadosDepartamento(params, departmentId = null) {
    let url = `../api/api_dashboard.php?action=topEmpleadosPorDepartamento&${params.toString()}`;
    if (departmentId) {
        url += `&department=${departmentId}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('topEmpleadosList');
            container.innerHTML = '';
            
            // Agrupar por departamento
            const deptGroups = {};
            data.forEach(item => {
                if (!deptGroups[item.DepartmentName]) {
                    deptGroups[item.DepartmentName] = [];
                }
                deptGroups[item.DepartmentName].push(item);
            });
            
            // Crear listas por departamento
            for (const [deptName, employees] of Object.entries(deptGroups)) {
                const deptCard = document.createElement('div');
                deptCard.classList.add('card', 'shadow', 'mb-4');
                
                deptCard.innerHTML = `
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">${deptName}</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            ${employees.map((emp, idx) => `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-primary badge-circle">${idx + 1}</span>
                                        ${emp.employee_name}
                                    </div>
                                    <span class="badge badge-primary badge-pill">${emp.total_leaves}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
                
                container.appendChild(deptCard);
            }
        })
        .catch(error => console.error('Error cargando top empleados:', error));
}

// 5. Cargar top 5 departamentos con más permisos
function loadTopDepartamentosPermisos(params) {
    const url = `../api/api_dashboard.php?action=topDepartamentosConMasPermisos&${params.toString()}`;
    const ctx = document.getElementById('chartTopDepartamentosPermisos').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.DepartmentName);
            const values = data.map(item => item.total_permisos);
            
            if (charts.topDepartamentosPermisos) {
                charts.topDepartamentosPermisos.destroy();
            }
            
            charts.topDepartamentosPermisos = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Permisos',
                        data: values,
                        backgroundColor: chartColors.primary,
                        borderColor: chartColors.primary,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Departamentos con Más Permisos'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando top departamentos permisos:', error));
}

// 6. Cargar top 5 departamentos con más días acumulados
function loadTopDepartamentosDias(params) {
    const url = `../api/api_dashboard.php?action=topDepartamentosConMasDias&${params.toString()}`;
    const ctx = document.getElementById('chartTopDepartamentosDias').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.DepartmentName);
            const values = data.map(item => parseFloat(item.total_dias));
            
            if (charts.topDepartamentosDias) {
                charts.topDepartamentosDias.destroy();
            }
            
            charts.topDepartamentosDias = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Días',
                        data: values,
                        backgroundColor: chartColors.success,
                        borderColor: chartColors.success,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Departamentos con Más Días Acumulados'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Días totales'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando top departamentos días:', error));
}

// 7. Cargar gráfica de promedio de días por departamento
function loadPromedioDiasDepartamento(params) {
    const url = `../api/api_dashboard.php?action=promedioDiasPorDepartamento&${params.toString()}`;
    const ctx = document.getElementById('chartPromedioDiasDepartamento').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.DepartmentName);
            const values = data.map(item => parseFloat(item.promedio_dias).toFixed(1));
            
            if (charts.promedioDiasDepartamento) {
                charts.promedioDiasDepartamento.destroy();
            }
            
            charts.promedioDiasDepartamento = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Promedio de días',
                        data: values,
                        backgroundColor: chartColors.info,
                        borderColor: chartColors.info,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Promedio de Días por Departamento'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Días promedio'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando promedio días por departamento:', error));
}

// 8. Cargar gráfica de tendencias temporales
function loadTendenciasTiempo(params, groupBy = 'month') {
    const url = `../api/api_dashboard.php?action=tendenciasPorTiempo&groupBy=${groupBy}&${params.toString()}`;
    const ctx = document.getElementById('chartTendenciasTiempo').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Mapear los periodos de tiempo a nombres legibles
            let labels = [];
            if (groupBy === 'month') {
                const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                labels = data.map(item => monthNames[item.time_period - 1]);
            } else if (groupBy === 'quarter') {
                labels = data.map(item => `Q${item.time_period}`);
            } else {
                labels = data.map(item => item.time_period);
            }
            
            const values = data.map(item => item.total_solicitudes);
            
            if (charts.tendenciasTiempo) {
                charts.tendenciasTiempo.destroy();
            }
            
            charts.tendenciasTiempo = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Solicitudes',
                        data: values,
                        backgroundColor: chartColors.primary,
                        borderColor: chartColors.primary,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: groupBy === 'month' ? 'Tendencias Mensuales' : 
                                  groupBy === 'quarter' ? 'Tendencias Trimestrales' : 'Tendencias Anuales'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando tendencias temporales:', error));
}

// 9. Cargar gráfica de tipología de permisos
function loadTipologiaPermisos(params) {
    const url = `../api/api_dashboard.php?action=tipologiaPermisos&${params.toString()}`;
    const ctx = document.getElementById('chartTipologiaPermisos').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.LeaveType);
            const values = data.map(item => item.total);
            
            if (charts.tipologiaPermisos) {
                charts.tipologiaPermisos.destroy();
            }
            
            charts.tipologiaPermisos = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: chartColors.colors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Distribución por Tipo de Permiso'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando tipología de permisos:', error));
}

// 10. Cargar gráfica de distribución de permisos por rol
function loadDistribucionRolPermisos(params) {
    const url = `../api/api_dashboard.php?action=distribucionRolPorPermisos&${params.toString()}`;
    const ctx = document.getElementById('chartDistribucionRolPermisos').getContext('2d');
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.rol);
            const values = data.map(item => item.total_permisos);
            
            if (charts.distribucionRolPermisos) {
                charts.distribucionRolPermisos.destroy();
            }
            
            charts.distribucionRolPermisos = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.success,
                            chartColors.warning,
                            chartColors.danger,
                            chartColors.info
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Permisos por Rol'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando distribución por rol:', error));
}

function showCategory(id) {
    const category = document.getElementById(id);
    const contentsCategory = document.querySelectorAll('.content-category');
    contentsCategory.forEach(contentCategory => contentCategory.classList.remove('active'));

    const buttonsCategories = document.querySelectorAll('.btn-category');
    buttonsCategories.forEach(buttonCategory => buttonCategory.classList.remove('active'))

    category.classList.add('active');
    event.target.classList.add('active');
    
}

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    // Mostrar/Ocultar al hacer clic en el botón
    sidebarToggle.addEventListener('click', (e) => {
        e.stopPropagation(); // Evita que dispare el evento de "click fuera"
        sidebar.classList.toggle('show');
    });

    // Cerrar el sidebar al hacer clic fuera de él en pantallas pequeñas
    document.addEventListener('click', function (event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = sidebarToggle.contains(event.target);

        if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 768) {
            sidebar.classList.remove('show');
        }
    });

    // Cerrar el sidebar si la pantalla se redimensiona a mayor tamaño
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('show');
        }
    });