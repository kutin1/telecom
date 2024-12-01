// открытие и закрытие модального окна выбора города

document.addEventListener('DOMContentLoaded', function() {
    var modalCity = document.getElementById('cityModal');
    var linkCity = document.getElementById('cityModalLink');
    var spanCity = document.getElementsByClassName('close-city')[0];

    linkCity.addEventListener('click', function(event) {
        event.preventDefault(); // Отменяем стандартное действие ссылки

        modalCity.style.display = 'block'; // Открываем модальное окно
    });

    spanCity.addEventListener('click', function() {
        modalCity.style.display = 'none'; // Закрываем модальное окно
    });

    window.addEventListener('click', function(event) {
        if (event.target == modalCity) {
            modalCity.style.display = 'none'; // Закрываем модальное окно при клике вне его области
        }
    });

    // Обработка выбора города и изменение текста ссылки
    var cityItems = document.querySelectorAll('.city-item');
    cityItems.forEach(function(cityItem) {
        cityItem.addEventListener('click', function() {
            var cityName = cityItem.textContent;
            var icon = '<svg class="location-icon" xmlns="http://www.w3.org/2000/svg" width="25" height="34" viewBox="0 0 25 34" fill="none"><path d="M18.1764 33.4934H6.82402C6.20297 33.4835 5.70413 32.9815 5.70413 32.3653C5.70413 31.7476 6.20297 31.2456 6.82402 31.2371H18.1764C18.7975 31.2456 19.2963 31.7476 19.2963 32.3653C19.2963 32.9815 18.7975 33.4836 18.1764 33.4934ZM12.5 30.0765C12.1817 30.0765 11.879 29.9454 11.6629 29.7127L3.31901 20.7214C-4.47627 11.898 2.63685 -0.336943 12.4983 0.00709871C22.4096 -0.328528 29.4572 11.9121 21.6805 20.7214L13.3365 29.7127C13.1205 29.9454 12.8178 30.0765 12.4994 30.0765H12.5ZM12.5015 2.26333C7.77757 2.26333 2.5687 5.42779 2.29183 12.3759V12.3773C2.29894 14.9044 3.26251 17.3369 4.9921 19.1928L12.5003 27.2845L20.0086 19.1943V19.1928C21.7466 17.3243 22.7102 14.8748 22.7074 12.3322C22.7415 9.64159 21.6756 7.05247 19.7527 5.15429C17.8299 3.25759 15.213 2.21398 12.5015 2.26333ZM12.5 18.5413C10.8401 18.5568 9.24407 17.9053 8.07445 16.7362C6.90483 15.5672 6.261 13.9793 6.288 12.3323C6.32921 4.39007 18.3509 3.85578 18.7562 12.3323C18.7548 13.978 18.0954 15.556 16.9215 16.7207C15.749 17.8841 14.1584 18.5384 12.5 18.5413ZM12.517 8.38066C11.4597 8.38066 10.445 8.79806 9.69884 9.54123C8.95129 10.2844 8.53347 11.2913 8.53489 12.3419C8.53773 13.391 8.96126 14.3965 9.71164 15.1355C10.4606 15.8758 11.4768 16.289 12.5355 16.2848C14.7313 16.2763 16.5063 14.5037 16.5019 12.3234C16.4963 10.1433 14.7141 8.37922 12.517 8.38066Z" fill="black"/></svg>';
            linkCity.innerHTML = icon + ' ' + cityName; // Добавляем иконку перед текстом города
            modalCity.style.display = 'none'; // Закрываем модальное окно
        });
    });
});

/* личный кабинет 
document.addEventListener('DOMContentLoaded', function() {
    var modalLogin = document.getElementById('loginModal');
    var linkLogin = document.getElementById('loginModalLink');
    var spanLogin = document.getElementsByClassName('close-login')[0];

    linkLogin.addEventListener('click', function(event) {
        event.preventDefault();
        modalLogin.style.display = 'block';
    });

    spanLogin.addEventListener('click', function() {
        modalLogin.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == modalLogin) {
            modalLogin.style.display = 'none';
        }
    });
});
*/
/*личный кабинет с авториазацией */
document.addEventListener('DOMContentLoaded', function() {
    var modalLogin = document.getElementById('loginModal');
    var linkLogin = document.getElementById('loginModalLink');
    var spanLogin = document.getElementsByClassName('close-login')[0];
    var loginForm = document.getElementById('loginForm'); // ID вашей формы

    linkLogin.addEventListener('click', function(event) {
        event.preventDefault();
        modalLogin.style.display = 'block';
    });

    spanLogin.addEventListener('click', function() {
        modalLogin.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == modalLogin) {
            modalLogin.style.display = 'none';
        }
    });

    // Добавляем обработчик для отправки формы
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Предотвращаем стандартное действие отправки формы
        
        // Собираем данные формы
        var formData = new FormData(loginForm);

        // Отправляем данные на сервер
        fetch('config/auth.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                // Переходим на соответствующую страницу в зависимости от роли пользователя
                window.location.href = response.url;
            } else {
                console.error('Произошла ошибка при авторизации');
            }
        }).catch(error => {
            console.error('Произошла ошибка при отправке данных:', error);
        });
    });
});
/* скрытие названия полей в модальном окне авторизации */
document.addEventListener('DOMContentLoaded', function() {
    const formInputs = document.querySelectorAll('.login-field-input');
    formInputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.previousElementSibling.classList.add('hidden');
        });
        input.addEventListener('blur', () => {
            if (input.value === '') {
                input.previousElementSibling.classList.remove('hidden');
            }
        });
    });
});
/* модальное окно кнопки подключиться */
// Get the modal element
var connectModal = document.getElementById('connectModal');
// Get the button that opens the modal
var connectBtn = document.getElementById('connectBtn');
// Get the <span> element that closes the modal
var closeBtnConnect = document.querySelector('.close-connect');

// Function to open the modal
function openConnectModal() {
    connectModal.style.display = 'block';
}

// Function to close the modal
function closeConnectModal() {
    connectModal.style.display = 'none';
}

// Event listener to open the modal when the button is clicked
connectBtn.addEventListener('click', openConnectModal);

// Event listener to close the modal when the close button is clicked
closeBtnConnect.addEventListener('click', closeConnectModal);

// Event listener to close the modal when clicking outside of it
window.addEventListener('click', function(event) {
    if (event.target == connectModal) {
        closeConnectModal();
    }
});

// Form submit event listener (you can add your form handling logic here)
document.getElementById('modal-connect-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission for this example
    // Add your form handling logic here, e.g., send data to server via AJAX
    // No need to close the modal automatically here
});
/* поля формы */
document.addEventListener('DOMContentLoaded', function() {
    const connectModalInputs = document.querySelectorAll('#connectModal .modal-form-field-input');
    
    connectModalInputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.querySelector('.modal-form-field-label').classList.add('hidden');
        });

        input.addEventListener('blur', () => {
            if (input.value === '') {
                input.parentElement.querySelector('.modal-form-field-label').classList.remove('hidden');
            }
        });
    });
});


/* отключение перехода на другую страницу при отправке формы */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.feedback-form');
    const button = form.querySelector('.form-button');

    form.addEventListener('submit', function(event) {
        // Сохраняем введенные данные в полях
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Отключаем все поля формы
        form.querySelectorAll('input, textarea, select').forEach(function(element) {
            element.disabled = true;
        });

        // Меняем текст кнопки на "Отправлено"
        button.textContent = 'Отправлено';
        button.classList.add('sent'); // Add the 'sent' class to apply styles


        // Отправляем данные на сервер
        fetch('config/submit_form.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                console.log('Данные успешно отправлены');
            } else {
                console.error('Произошла ошибка при отправке данных');
            }
        }).catch(error => {
            console.error('Произошла ошибка при отправке данных:', error);
        });

        // Предотвращаем стандартное поведение отправки формы
        event.preventDefault();
    });
});

/* отправка формы модального окна подключиться*/
document.addEventListener('DOMContentLoaded', function() {
    const modalForm = document.getElementById('modal-connect-form');
    const modalButton = modalForm.querySelector('.modal-form-button');

    modalForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission
        
        // Collect form data
        const formData = new FormData(modalForm);

        // Send data to server using fetch
        fetch('config/submit_form_connect.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                console.log('Данные успешно отправлены');
                // Disable form fields and change button text after successful submission
                modalForm.querySelectorAll('input, textarea, select').forEach(function(element) {
                    element.disabled = true;
                });
                modalButton.textContent = 'Отправлено';
                modalButton.classList.add('sent'); // Apply styles for sent state
            } else {
                console.error('Произошла ошибка при отправке данных');
            }
        }).catch(error => {
            console.error('Произошла ошибка при отправке данных:', error);
        });
    });
});

/* авторизация пользователя */
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const loginButton = loginForm.querySelector('.login-button');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission

        // Collect form data
        const formData = new FormData(loginForm);

        // Disable form fields
        loginForm.querySelectorAll('input').forEach(function(element) {
            element.disabled = true;
        });

        // Change button text to "Вход..."
        loginButton.textContent = 'Вход...';

        // Send data to server using fetch
        fetch('config/auth.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                return response.json(); // Parse response as JSON
            } else {
                throw new Error('Произошла ошибка при отправке данных');
            }
        }).then(data => {
            if (data.success) {
                window.location.href = data.redirectUrl; // Redirect on successful login
            } else {
                alert('Ошибка: ' + data.message); // Display error message
                resetForm(); // Reset the form
            }
        }).catch(error => {
            console.error('Произошла ошибка при обработке данных:', error);
            resetForm(); // Reset the form
        });
    });

    // Function to reset the form after submission
    function resetForm() {
        loginForm.reset();
        loginForm.querySelectorAll('input').forEach(function(element) {
            element.disabled = false;
        });
        loginButton.textContent = 'Войти';
    }
});