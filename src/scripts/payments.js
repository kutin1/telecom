/*document.getElementById('paymentModal').addEventListener('submit', function (event) {
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