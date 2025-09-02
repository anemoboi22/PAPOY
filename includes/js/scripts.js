document.addEventListener('DOMContentLoaded', function() {
    const signUpButton = document.getElementById('signUp');
    const loginButton = document.getElementById('login');
    const container = document.querySelector('.container');

    // Add event listeners for switching between forms when buttons are clicked
    if (signUpButton) {
        signUpButton.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });
    }

    if (loginButton) {
        loginButton.addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });
    }

    // Check for the 'signup=true' or 'login=true' parameter in the URL on page load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('signup')) {
        // Delay to allow DOM to render, ensuring smooth animation for the sign-up form
        setTimeout(() => {
            container.classList.add('right-panel-active');
        }, 300); // 300ms delay helps make the transition smoother
    } else {
        // Remove the class with a similar delay to ensure the slide-back animation occurs
        setTimeout(() => {
            container.classList.remove('right-panel-active');
        }, 300); // 300ms delay to make sure the slide-back transition occurs smoothly
    }

    // Check for query parameters to display notifications
    const signupStatus = urlParams.get('signup');
    const errorMessage = urlParams.get('error');
    const loginError = urlParams.get('login_error');

    if (signupStatus === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Account created successfully.',
            confirmButtonColor: '#218838',
            confirmButtonText: 'OK'
        });
    } else if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: decodeURIComponent(errorMessage),
            confirmButtonText: 'OK'
        });
    } else if (loginError) {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed!',
            text: decodeURIComponent(loginError),
            confirmButtonText: 'OK'
        });
    }
});

// Separate event listener for the login button animation
document.addEventListener('DOMContentLoaded', function() {
    const lButton = document.getElementById('loginButton');
    if (lButton) {
        lButton.addEventListener('click', function() {
            const icon = document.querySelector('.icon');
            if (icon) {
                icon.classList.add('animate');
            }
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 300);
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const passwordInput = this.closest('.password-container').querySelector('input[type="password"], input[type="text"]');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                this.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    });
});


