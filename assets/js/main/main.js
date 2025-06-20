document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const appSidebar = document.getElementById('appSidebar');
    
    if (menuToggle && appSidebar) {
        menuToggle.addEventListener('click', function() {
            appSidebar.classList.toggle('open');
        });
    }
    
    // Toggle submenus
    const navItems = document.querySelectorAll('.has-submenu');
    
    navItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        link.addEventListener('click', function(e) {
            e.preventDefault();
            item.classList.toggle('open');
        });
    });
    
    // Toggle notifications dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.style.display = 
                notificationDropdown.style.display === 'block' ? 'none' : 'block';
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        if (notificationDropdown) {
            notificationDropdown.style.display = 'none';
        }
    });
    
    // Toggle password visibility
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Handle form submission
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                

                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 5000);
            }
        });
    });

    // Aparece el nombre del usuario cuando se da click en el icono
    const userProfile = document.querySelector(".user-profile");
    const userDropdown = document.getElementById("userDropdown");

    userProfile.addEventListener("click", function(e) {
        userDropdown.style.display = userDropdown.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", function(e) {
        if (!userProfile.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.style.display = "none";
        }
    });

    flatpickr("#FromDate, #ToDate", {
        locale: "es",
        dateFormat: "d/m/Y h:i K",
        enableTime: true,
        time_24hr: false,
        minDate: "today",
        defaultHour: 8,
        defaultMinute: 0
    });
});

