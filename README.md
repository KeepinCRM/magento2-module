# Інтеграція KeepinCRM з Magento
* Миттєве відправлення даних з Magento в [KeepinCRM](https://bit.ly/3KCbyDR) після створення замовлення
* Після зворотного зв'язку на сайті в [KeepinCRM](https://bit.ly/3KCbyDR) створюється задача та лід

## Встановлення
1. Встановити Magento 2 (перевірено на версії 2.3)
1. Зареєструватись в [KeepinCRM](https://bit.ly/3KCbyDR) та створити API key в **Налаштування/Профіль компанії/API**
3. Source ID в **Налаштування/Управління/Джерела**
4. Скопіювати папку "app/code/KeepinCRM" в "app/code" Вашого проєкту
5. Очистити кеш
6. Активувати модуль (див. нижче)
7. В налаштуваннях магазину в розділі Конфігурація KeepinCRM заповнити поля *Api-key* та *Source ID* (джерело в KeepinCRM, до якого будуть прикріплюватись замовлення та клієнти)
8. Додатково в конфігурації можна вказати поля для методу оплати (*Payment field ID*), методу доставки (*Delivery method field ID*) та адреси (*Address field ID*), до яких будуть записувати відповідні значення

## Активація модуля
Активувати модуль можна як мінімум 2-ма шляхами: через "app/etc/config.php" або через консоль розробника

### Через app/etc/config.php
Додати ```'KeepinCRM_Core' => 1``` в масив ```modules``` у файлі "app/etc/config.php"

### Через консоль розробника
Відкрийте консоль з кореневої папки Magento 2 та виконайте наступні дії:
```
php bin/magento setup:upgrade
```

Очистити кеш
```
php bin/magento cache:flush
```

Якщо зникнуть стилі чи js, то виконайте наступне
```
php bin/magento setup:static-content:deploy // додатково може знадобитись вказання локалізацій сайту
```
