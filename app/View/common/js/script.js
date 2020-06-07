/**
 * Методы.
 *
 * @constructor
 */
function Utils () {
    const self = this;

    /**
     * Отправка запроса на сервер.
     *
     * @param requestType
     * @param url
     * @param params
     *
     * @return {Promise}
     */
    this.request = function (requestType, url, params = {}) {
        return new Promise(function (resolve, reject) {
            $[requestType](url, params).done(response => {
                if (!('success' in response)) {
                    return resolve(response);
                }

                if (!response.success) {
                    return reject(response);
                }

                return resolve(response.data);
            }).fail(response => {
                return reject(response);
            });
        });
    };

    /**
     * Собираем url запроса.
     *
     * @param controller
     * @param method
     * @param params
     *
     * @return {string}
     */
    this.getUrl = function (controller, method, params = {}) {
        const requestParams = $.param(params);

        return `/${controller}/${method}?` + (requestParams ? requestParams : '');
    };

    /**
     * POST запрос.
     *
     * @param url
     * @param params
     *
     * @return {Promise}
     */
    this.post = function (url, params = {}) {
        return self.request('post', url, params);
    };

    /**
     * Убираем лишие пробелы в строке.
     *
     * @param string
     *
     * @return {string}
     */
    this.trimValue = function (string) {
        return string.replace(/\s+/g, ' ').trim();
    };

    /**
     * Валидация e-mail.
     *
     * @param email
     *
     * @return {boolean}
     */
    this.validateEmail = function (email) {
        const re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        return re.test(String(email).toLowerCase());
    };

    /**
     * Сбор и проверка данных формы перед отправкой на сервер.
     *
     * @param formId
     *
     * @return {{data:{}, is_invalid:boolean}}
     */
    this.dataCollect = function (formId) {
        let result = {
            data       : {},
            is_invalid : true,
        };

        if (formId === '') {
            return result;
        }

        let isInvalid = false;

        $(`#${formId}`).find ('input, select, textarea').each(function() {
            let $field = $(this);
            let value  = utils.trimValue($field.val());
            let name   = this.name;
            let error  = value === '';

            if (name === 'email') {
                error = !utils.validateEmail(value);
            }

            if (error) {
                isInvalid = true;
                $field.addClass('is-invalid');
            } else {
                $field.removeClass('is-invalid');
            }

            result.data[name] = value;
        });

        result.is_invalid = isInvalid;

        return result;
    };

    /**
     * Получаем данные cookie.
     *
     * @param name
     *
     * @return {any}
     */
    this.getCookie = function(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));

        return matches ? decodeURIComponent(matches[1]) : undefined;
    }
}

let utils = new Utils();

/**
 * Авторизуем админа.
 */
$('#send').click(function () {
    let data = utils.dataCollect('login');

    if (data.is_invalid) {
        return;
    }

    utils.post(utils.getUrl('auth', 'login'), data.data).then(response => {
        document.location.reload(true);
    }).catch(error => {
        $('#modalTitle').html('Пфф...');
        $('#modalMessage').html('Не верные реквизиты.');
        $('#modal').modal();
    });
});

/**
 * Выбираем другую сортировку.
 */
$(document).on('change', '#sortingType', function () {
    let value = $('#sortingType').val();
    document.cookie = "sort=" + value;
    let data = {
       sort : value,
       page : utils.getCookie('page')
    };

    utils.post(utils.getUrl('task', 'sort'), data).then(response => {
        $('#tasks').html(response.list);
    }).catch(error => {
        console.log(error);
    });
});

/**
 * Обновляем задачу.
 */
$('#editTask').click(function () {
    let data = utils.dataCollect('editForm');

    if (data.is_invalid) {
        return;
    }

    utils.post(utils.getUrl('task', 'edit'), data.data).then(response => {
        $('#modalTitle').html('Результат обновления');
        $('#modalMessage').html(`Задача успешно обновлена`);
        $('#modal').modal();
    }).catch(error => {
        if (error.error) {
            $('#modalTitle').html('Ошибка обновления');
            $('#modalMessage').html(error.error);
            $('#modal').modal();
        }
    });
});

/**
 * Создаём задачу.
 */
$('#createTask').click(function () {
    let data = utils.dataCollect('addTask');

    if (data.is_invalid) {
        return;
    }

    utils.post(utils.getUrl('task', 'add'), data.data).then(response => {
        $('#modalTitle').html('Задача добавлена');
        $('#modalMessage').html(`ID новой задачи ${response.task_id.$oid}`);
        $('#modal').modal();
        $('#tasks').html(response.list);
    }).catch(error => {
        console.log(error);
    });
});
