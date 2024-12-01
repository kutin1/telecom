document.addEventListener('DOMContentLoaded', function() {
    var modalPayment = document.getElementById('paymentModal');
    var buttonPayment = document.getElementById('openPaymentModal');
    var closePayment = document.getElementsByClassName('close-payment')[0];

    buttonPayment.addEventListener('click', function(event) {
        event.preventDefault();
        modalPayment.style.display = 'block';
    });

    closePayment.addEventListener('click', function() {
        modalPayment.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target ==  modalPayment) {
            modalPayment.style.display = 'none';
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    var modalPayment = document.getElementById('paymentModal');
    var buttonPayment = document.getElementById('openPaymentModalHeader');
    var closePayment = document.getElementsByClassName('close-payment')[0];

    buttonPayment.addEventListener('click', function(event) {
        event.preventDefault();
        modalPayment.style.display = 'block';
    });

    closePayment.addEventListener('click', function() {
        modalPayment.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target ==  modalPayment) {
            modalPayment.style.display = 'none';
        }
    });
});


// Форматирование номера карты по 4 цифры и пробелы
// Ограничение количества символов для номера карты (максимум 19 символов)
document.getElementById('card-number').addEventListener('input', function (e) {
    var target = e.target;
    var input = target.value.replace(/\s/g, '').replace(/(\d{4})(?!$)/g, '$1 ');
    target.value = input.substring(0, 19); // Ограничение до 19 символов
});

// Ограничение количества символов для срока действия карты (максимум 5 символов)
document.getElementById('expiry-date').addEventListener('input', function (e) {
    var target = e.target;
    var input = target.value.replace(/\D/g, '').replace(/(\d{2})(\d{2})/, '$1/$2');
    target.value = input.substring(0, 5); // Ограничение до 5 символов
});

// Ограничение количества символов для CVV (максимум 3 цифры)
document.getElementById('cvv').addEventListener('input', function (e) {
    var target = e.target;
    var input = target.value.replace(/\D/g, '');
    target.value = input.substring(0, 3); // Ограничение до 3 цифр
});

// иммитация оплаты 
// Обработчик для имитации оплаты
document.getElementById('paymentModal').addEventListener('submit', function (event) {
    event.preventDefault(); // Предотвращаем стандартное действие отправки формы
    
    // Получаем данные из формы
    var cardNumber = document.getElementById('card-number').value;
    var expiryDate = document.getElementById('expiry-date').value;
    var cvv = document.getElementById('cvv').value;
    var cardholderName = document.getElementById('cardholder-name').value;
    var paymentAmount = document.getElementById('payment-amount').value;

    // Простая имитация оплаты
    setTimeout(function () {
        var success = Math.random() < 0.5; // Случайная имитация успешной или неудачной оплаты
        if (success) {
            alert('Оплата успешно выполнена!');
        } else {
            alert('Ошибка при оплате. Пожалуйста, попробуйте еще раз.');
        }
    }, 1000); // Задержка в 1 секунду для имитации работы с сервером
});

/* платежный модуль на доработке
document.getElementById('paymentModal').addEventListener('submit', function (event) {
    event.preventDefault(); // Предотвращаем стандартное действие отправки формы
    
    // Получаем данные из формы
    var cardNumber = document.getElementById('card-number').value;
    var expiryDate = document.getElementById('expiry-date').value;
    var cvv = document.getElementById('cvv').value;
    var cardholderName = document.getElementById('cardholder-name').value;
    var paymentAmount = parseFloat(document.getElementById('payment-amount').value);

    // Проверяем корректность данных
    if (!cardNumber || !expiryDate || !cvv || !cardholderName || isNaN(paymentAmount) || paymentAmount <= 0) {
        alert('Пожалуйста, заполните все поля корректно.');
        return;
    }

    // Отправляем запрос на сервер для обновления баланса счета
    fetch('../config/update-invoice-balance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `invoiceId=${invoiceId}&paymentAmount=${paymentAmount}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Оплата успешно выполнена!');
            // Обновляем баланс счета на странице
            updateInvoiceBalance(invoiceId, paymentAmount);
        } else {
            alert(`Ошибка при оплате: ${data.error}`);
        }
    })
    .catch(error => {
        console.error('Ошибка при обновлении баланса счета:', error);
        alert('Произошла ошибка при оплате. Пожалуйста, попробуйте еще раз.');
    });
});

function updateInvoiceBalance(invoiceId, paymentAmount) {
    // Функция для обновления баланса счета на странице
    // Например, вы можете обновить содержимое элемента с id "invoice-balance"
    var invoiceBalanceElement = document.getElementById('invoice-balance');
    var newBalance = parseFloat(invoiceBalanceElement.textContent) + paymentAmount;
    invoiceBalanceElement.textContent = newBalance.toFixed(2);
}
*/