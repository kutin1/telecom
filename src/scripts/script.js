/* Слайдер */
document.addEventListener("DOMContentLoaded", function () {
    const sliderItems = document.querySelectorAll('.slider-item');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    let currentSlide = 0;
    let autoSlideInterval = null;

    function showSlide(index) {
        sliderItems.forEach((item, i) => {
            if (i === index) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.add('active-dot');
            } else {
                dot.classList.remove('active-dot');
            }
        });
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + sliderItems.length) % sliderItems.length;
        showSlide(currentSlide);
        resetAutoSlide(); // Сбрасываем автопереключение при ручном переключении
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % sliderItems.length;
        showSlide(currentSlide);
        resetAutoSlide(); // Сбрасываем автопереключение при ручном переключении
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval); // Сброс предыдущего интервала автопереключения
        autoSlideInterval = setInterval(nextSlide, 5000); // Запуск нового интервала автопереключения
    }

    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
            resetAutoSlide(); // Сбрасываем автопереключение при ручном переключении
        });
    });

    resetAutoSlide(); // Запускаем автопереключение слайдов при загрузке страницы
});
/* Окончание слайдера


/* Преимущества */
document.addEventListener("DOMContentLoaded", function () {
    const menuLinks = document.querySelectorAll('.features-menu-link');
    const screens = document.querySelectorAll('.features-screen');
    const interval = 5000; // Интервал автосмены в миллисекундах (например, каждые 5 секунд)

    let currentSlide = 0;
    let autoSlideInterval;

    function showSlide(index) {
        // Убираем активный класс у всех экранов
        screens.forEach(screen => {
            screen.classList.remove('active');
        });

        // Добавляем активный класс текущему экрану
        screens[index].classList.add('active');

        // Убираем активный класс у всех ссылок в меню
        menuLinks.forEach(menuLink => {
            menuLink.classList.remove('active');
        });

        // Добавляем активный класс текущей ссылке в меню
        menuLinks[index].classList.add('active');
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % screens.length;
        showSlide(currentSlide);
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, interval);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    // Добавляем обработчики событий для ссылок в меню
    menuLinks.forEach((link, index) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            currentSlide = index;
            showSlide(currentSlide);
        });
    });

    // Запускаем автоматическую смену слайдов при загрузке страницы и показываем первый слайд
    startAutoSlide();
    showSlide(0); // Показываем первый слайд при загрузке страницы
});
/* Окончание преимуществ */


/* Поля формы */
document.addEventListener('DOMContentLoaded', function () {
    const formInputs = document.querySelectorAll('.form-field-input');
  
    formInputs.forEach(input => {
      input.addEventListener('focus', () => {
        input.parentElement.querySelector('.form-field-label').classList.add('hidden');
      });
  
      input.addEventListener('blur', () => {
        if (input.value === '') {
          input.parentElement.querySelector('.form-field-label').classList.remove('hidden');
        }
      });
    });
  });

/* Окончание Поля формы */
/* Изменение фонового цвета карточки в зависимости от потомков с классом .hit*/
  const itemsWithHit = document.querySelectorAll('.tariffs-screen-list .hit');

itemsWithHit.forEach(hitItem => {
    const parentItem = hitItem.closest('.tariff-screen-item');
    if (parentItem) {
        parentItem.style.backgroundColor = '#beb931'; // Изменение фонового цвета
    }
});
/* Окончание Изменения фонового цвета карточки в зависимости от потомков с классом .hit*/

/* Меняем цвет фона и текста кнопки при наведении в хитовой карточки */
document.addEventListener("DOMContentLoaded", function() {
    const tariffItems = document.querySelectorAll('.tariff-screen-item');

    tariffItems.forEach(item => {
        const hitDiv = item.querySelector('.hit');
        const tariffButton = item.querySelector('.tariff-button');

        if (hitDiv) {
            tariffButton.addEventListener('mouseover', function() {
                tariffButton.classList.add('hit-hover');
            });

            tariffButton.addEventListener('mouseout', function() {
                tariffButton.classList.remove('hit-hover');
            });
        }
    });
});
/* Заканчиваем Менять цвет фона и текста кнопки при наведении в хитовой карточки */
/* отзывы */
const slides = document.querySelectorAll('.reviews-item');
const prevBtn = document.querySelector('.review-prev-btn');
const nextBtn = document.querySelector('.review-next-btn');
let currentSlide = 0;
let slideInterval;

// Функция для переключения на следующий слайд
function nextSlide() {
    slides[currentSlide].classList.remove('active-review');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active-review');
}

// Функция для переключения на предыдущий слайд
function prevSlide() {
    slides[currentSlide].classList.remove('active-review');
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    slides[currentSlide].classList.add('active-review');
}

// Добавляем обработчики событий для кнопок "назад" и "вперед"
prevBtn.addEventListener('click', () => {
    clearInterval(slideInterval);
    prevSlide();
    startSlideShow();
});

nextBtn.addEventListener('click', () => {
    clearInterval(slideInterval);
    nextSlide();
    startSlideShow();
});

// Автоматическое переключение слайдов
function startSlideShow() {
    slideInterval = setInterval(nextSlide, 3000); // Интервал в миллисекундах (здесь 3 секунды)
}

startSlideShow(); // Запускаем автопереключение при загрузке страницы
/* оконачание отзывы */
/*начало партнеров без автослайдера \ расскоментировать при смене логики показала партнёров 
const scrollLeftBtn = document.querySelector('.partners-prev-btn');
const scrollRightBtn = document.querySelector('.partners-next-btn');

const partnersList = document.querySelector('.partners-list');
const partnersItems = document.querySelectorAll('.partners-item');

let scrollInterval;
let scrollSpeed = 1; // Initial scroll speed
let scrollDirection = 'right'; // Initial scroll direction

// Function to handle smooth scrolling to the left
const scrollLeft = () => {
    clearInterval(scrollInterval);
    scrollInterval = setInterval(() => {
        partnersList.scrollLeft -= scrollSpeed;
    }, 10);
};

// Function to handle smooth scrolling to the right
const scrollRight = () => {
    clearInterval(scrollInterval);
    scrollInterval = setInterval(() => {
        partnersList.scrollLeft += scrollSpeed;
    }, 10);
};

// Event listeners for scroll buttons
scrollLeftBtn.addEventListener('click', () => {
    scrollDirection = 'left';
    scrollLeft();
});
scrollRightBtn.addEventListener('click', () => {
    scrollDirection = 'right';
    scrollRight();
});

// Function to handle accelerating scrolling when holding down a button
const handleScrollAcceleration = () => {
    clearInterval(scrollInterval);
    scrollInterval = setInterval(() => {
        if (scrollDirection === 'left') {
            partnersList.scrollLeft -= scrollSpeed;
        } else {
            partnersList.scrollLeft += scrollSpeed;
        }
        scrollSpeed += 0.2; // Increase scroll speed gradually
    }, 10);
};

// Event listeners for accelerating scroll
scrollLeftBtn.addEventListener('mousedown', handleScrollAcceleration);
scrollRightBtn.addEventListener('mousedown', handleScrollAcceleration);

// Event listener to stop accelerating scroll on button release
document.addEventListener('mouseup', () => {
    clearInterval(scrollInterval);
    scrollSpeed = 1; // Reset scroll speed
});

// Event listener for changing scroll direction on button click
const changeScrollDirection = () => {
    if (scrollDirection === 'left') {
        scrollDirection = 'right';
    } else {
        scrollDirection = 'left';
    }
};

// Event listeners for changing scroll direction on button click
scrollLeftBtn.addEventListener('click', changeScrollDirection);
scrollRightBtn.addEventListener('click', changeScrollDirection);
/*окончание партнеров */
/* партнеры с автослайдером */
const scrollLeftBtn = document.querySelector('.partners-prev-btn');
const scrollRightBtn = document.querySelector('.partners-next-btn');
const partnersList = document.querySelector('.partners-list');

let scrollDirection = 1; // 1 for right, -1 for left
let scrollSpeed = 1; // Speed multiplier for scrolling

// Smooth autoplay scrolling function
function smoothAutoscroll() {
    partnersList.scrollLeft += scrollDirection * scrollSpeed;
}

// Autoplay scrolling interval
let autoplayInterval = setInterval(smoothAutoscroll, 10);

// Accelerate scrolling on right arrow key press
scrollRightBtn.addEventListener('click', () => {
    scrollSpeed += 1;
});

// Accelerate scrolling on left arrow key press
scrollLeftBtn.addEventListener('click', () => {
    scrollSpeed += 1;
});

// Change scroll direction on left arrow key press
scrollLeftBtn.addEventListener('dblclick', () => {
    scrollDirection = -1;
    scrollSpeed = 1; // Reset scroll speed
});

// Change scroll direction on right arrow key press
scrollRightBtn.addEventListener('dblclick', () => {
    scrollDirection = 1;
    scrollSpeed = 1; // Reset scroll speed
});

// Infinite scrolling functionality
partnersList.addEventListener('scroll', () => {
    if (scrollDirection === 1 && partnersList.scrollLeft >= partnersList.scrollWidth - partnersList.clientWidth) {
        partnersList.scrollLeft = 0; // Reset to the beginning
    } else if (scrollDirection === -1 && partnersList.scrollLeft <= 0) {
        partnersList.scrollLeft = partnersList.scrollWidth - partnersList.clientWidth; // Reset to the end
    }
});
/* партнеры с автослайдером */
/* показ скриншота карты при незагрузке не реализована, реализовать в дальнейшем */
/* проверить почему не запускается, исправить ошибки */
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(function(item) {
        const question = item.querySelector('.question');
        const answer = item.querySelector('.answer');

        question.addEventListener('click', function() {
            item.classList.toggle('open');
        });
    });
});