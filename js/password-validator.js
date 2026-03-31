// Validador de Contraseñas
// Valida los requisitos en tiempo real y actualiza los checkmarks

class PasswordValidator {
    constructor() {
        this.passwordInput = document.getElementById('newPassword');
        this.confirmInput = document.getElementById('confirmPassword');
        this.form = document.getElementById('passwordForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.strengthIndicator = document.getElementById('strengthIndicator');
        this.strengthText = document.getElementById('strengthText');

        // Checklist items
        this.checks = {
            length: document.getElementById('check-length'),
            number: document.getElementById('check-number'),
            uppercase: document.getElementById('check-uppercase'),
            special: document.getElementById('check-special')
        };

        this.init();
    }

    init() {
        this.passwordInput.addEventListener('input', () => this.validate());
        this.confirmInput.addEventListener('input', () => this.validate());
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    // Validar longitud (mínimo 8 caracteres)
    validateLength(password) {
        return password.length >= 8;
    }

    // Validar números
    validateNumber(password) {
        return /\d/.test(password);
    }

    // Validar mayúsculas
    validateUppercase(password) {
        return /[A-Z]/.test(password);
    }

    // Validar caracteres especiales
    validateSpecial(password) {
        return /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    }

    // Actualizar el estado visual de un check
    updateCheck(checkElement, isValid) {
        if (isValid) {
            checkElement.textContent = '✓';
            checkElement.className = 'check-icon success';
        } else {
            checkElement.textContent = '✗';
            checkElement.className = 'check-icon error';
        }
    }

    // Determinar la fortaleza de la contraseña
    getPasswordStrength(password) {
        if (password.length === 0) return 'none';

        let strengthScore = 0;

        if (this.validateLength(password)) strengthScore++;
        if (this.validateNumber(password)) strengthScore++;
        if (this.validateUppercase(password)) strengthScore++;
        if (this.validateSpecial(password)) strengthScore++;

        if (strengthScore <= 1) return 'débil';
        if (strengthScore <= 2) return 'media';
        return 'fuerte';
    }

    // Mostrar el indicador de fortaleza
    showStrengthIndicator(password) {
        if (password.length === 0) {
            this.strengthIndicator.style.display = 'none';
            return;
        }

        const strength = this.getPasswordStrength(password);
        this.strengthIndicator.style.display = 'block';

        const strengthMessages = {
            débil: '🔴 Contraseña débil - Cumple 1 o menos requisitos',
            media: '🟡 Contraseña media - Cumple 2 requisitos',
            fuerte: '🟢 Contraseña fuerte - Cumple todos los requisitos'
        };

        // Mapear para clases CSS
        const strengthClass = {
            débil: 'weak',
            media: 'medium',
            fuerte: 'strong'
        }[strength] || 'none';

        this.strengthIndicator.className = `password-strength ${strengthClass}`;
        this.strengthText.textContent = strengthMessages[strength];
    }

    // Validar toda la contraseña
    validate() {
        const password = this.passwordInput.value;
        const confirmPassword = this.confirmInput.value;

        // Actualizar checklist
        this.updateCheck(this.checks.length, this.validateLength(password));
        this.updateCheck(this.checks.number, this.validateNumber(password));
        this.updateCheck(this.checks.uppercase, this.validateUppercase(password));
        this.updateCheck(this.checks.special, this.validateSpecial(password));

        // Mostrar indicador de fortaleza
        this.showStrengthIndicator(password);

        // Verificar si todas las validaciones pasan
        const allValid =
            this.validateLength(password) &&
            this.validateNumber(password) &&
            this.validateUppercase(password) &&
            this.validateSpecial(password) &&
            password === confirmPassword &&
            password.length > 0;

        // Habilitar/deshabilitar botón
        this.submitBtn.disabled = !allValid;

        // Mostrar error si las contraseñas no coinciden
        if (confirmPassword.length > 0 && password !== confirmPassword) {
            this.confirmInput.classList.add('is-danger');
        } else {
            this.confirmInput.classList.remove('is-danger');
        }
    }

    // Manejar el envío del formulario
    async handleSubmit(e) {
        e.preventDefault();

        const password = this.passwordInput.value;
        const confirmPassword = this.confirmInput.value;

        // Validación final en cliente
        if (!this.validateLength(password)) {
            this.showError('La contraseña debe tener al menos 8 caracteres');
            return;
        }

        if (!this.validateNumber(password)) {
            this.showError('La contraseña debe contener números');
            return;
        }

        if (!this.validateUppercase(password)) {
            this.showError('La contraseña debe contener mayúsculas');
            return;
        }

        if (!this.validateSpecial(password)) {
            this.showError('La contraseña debe contener caracteres especiales');
            return;
        }

        if (password !== confirmPassword) {
            this.showError('Las contraseñas no coinciden');
            return;
        }

        // Enviar datos al servidor
        this.submitBtn.disabled = true;
        this.submitBtn.classList.add('is-loading');

        const formData = new FormData();
        formData.append('password', password);
        formData.append('confirm_password', confirmPassword);

        try {
            const response = await fetch('../php/save_password.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.éxito) {
                this.showSuccess(data.mensaje);
                setTimeout(() => {
                    window.location.href = data.redirigir;
                }, 2000);
            } else {
                const erroresTexto = data.errores ? data.errores.join('\n') : data.mensaje;
                this.showError(erroresTexto);
                this.submitBtn.disabled = false;
                this.submitBtn.classList.remove('is-loading');
            }
        } catch (error) {
            this.showError('Error al guardar la contraseña: ' + error.message);
            this.submitBtn.disabled = false;
            this.submitBtn.classList.remove('is-loading');
        }
    }

    // Mostrar mensaje de error
    showError(mensaje) {
        this.removeNotifications();
        const notif = document.createElement('div');
        notif.className = 'notification is-danger';
        notif.innerHTML = `
            <button class="delete"></button>
            ${mensaje.replace(/\n/g, '<br>')}
        `;
        
        const form = this.form.parentElement;
        form.insertBefore(notif, form.firstChild);

        const deleteBtn = notif.querySelector('.delete');
        deleteBtn.addEventListener('click', () => notif.remove());
    }

    // Mostrar mensaje de éxito
    showSuccess(mensaje) {
        this.removeNotifications();
        const notif = document.createElement('div');
        notif.className = 'notification is-success';
        notif.innerHTML = `
            <button class="delete"></button>
            ${mensaje}
        `;
        
        const form = this.form.parentElement;
        form.insertBefore(notif, form.firstChild);

        const deleteBtn = notif.querySelector('.delete');
        deleteBtn.addEventListener('click', () => notif.remove());
    }

    // Remover notificaciones existentes
    removeNotifications() {
        const notifs = this.form.parentElement.querySelectorAll('.notification');
        notifs.forEach(n => n.remove());
    }
}

// Función para toggle de visibilidad de contraseña
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
}

// Inicializar validador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new PasswordValidator();

    // Cerrar notificaciones
    const deleteButtons = document.querySelectorAll('.notification .delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.target.parentElement.remove();
        });
    });

    // ========================================
    // Manejo del toggle light/dark
    // ========================================
    const themeToggle = document.getElementById("themeToggle");
    if (themeToggle) {
        const iconEl = document.getElementById("themeIcon") || themeToggle.querySelector("span");

        const applyTheme = (theme) => {
            document.body.setAttribute("data-theme", theme);
            if (iconEl) iconEl.textContent = theme === "dark" ? "Light" : "Dark";
        };

        const savedTheme = localStorage.getItem("theme");
        const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
        let theme = savedTheme === "light" || savedTheme === "dark" ? savedTheme : prefersDark ? "dark" : "light";

        applyTheme(theme);

        themeToggle.addEventListener("click", () => {
            theme = document.body.getAttribute("data-theme") === "dark" ? "light" : "dark";
            localStorage.setItem("theme", theme);
            applyTheme(theme);
        });
    }
});
