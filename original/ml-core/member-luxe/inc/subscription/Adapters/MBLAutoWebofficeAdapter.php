<?php

class MBLAutoWebofficeAdapter extends BaseMBLSubscriptionAdapter implements IMBLSubscriptionAdapter
{
    /**
     * Параметр просит сервер не падать при ошибке
     * Если установлен в true то при ошибке при добавлении множества сущностей, сервер не падает и продолжает добавлять остальные
     *
     * @var bool
     */

    protected $configKey = 'autoweb';

    protected $doNotFallOnError = true;

    protected $apiKeyRead = '';
    protected $apiKeyWrite = '';
    protected $subdomain = '';

    protected $inputData = [];
    protected $inputXml;
    protected $response;
    protected $responseCode;
    protected $errors = [];

    protected $entity;
    protected $params = [];
    protected $search = [];
    protected $queryVars = [];


    /**
     * В массиве можно передать apiKeyRead, apiKeyWrite, subdomain
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct();
        try {
            $this->setKeys([
                'apiKeyRead' => $this->getConfig('apiKeyRead'),
                'apiKeyWrite' => $this->getConfig('apiKeyWrite'),
                'subdomain' => $this->getConfig('subdomain')
            ]);
        } catch (Exception $e) {
            $this->errors = $e->getMessage();
        }
    }

    /**
     * Контакты
     * Метод устанавливает сущность для работы с апи
     *
     * @return $this
     */
    public function contact()
    {
        $this->entity = 'contacts';
        return $this;
    }

    /**
     * Группы подписчиков
     * Метод устанавливает сущность для работы с апи
     *
     * @return $this
     */
    public function subscriberGroup()
    {
        $this->entity = 'newsletter';
        return $this;
    }

    /**
     * Подписчики
     * Метод устанавливает сущность для работы с апи
     *
     * @return $this
     */
    public function subscriber()
    {
        $this->entity = 'contactnewsletterlinks';
        return $this;
    }

    /**
     * Создание подписки
     * Метод устанавливает сущность для работы с апи
     * Данный метод поддерживает только создание подписки
     *
     * @return $this
     */
    public function subscription()
    {
        $this->entity = 'subscription';
        return $this;
    }

    /**
     * Обновляет элемент
     *
     * @param int $id ID элемента, который надо обновить
     * @param array $inputData массив с данными, которые надо обновить в виде название_поля => значение_поля
     * @return array|string массив с полями измененного элемента
     */
    public function update($id, $inputData)
    {
        $inputData['id'] = $id;
        $this->setInputData($inputData);

        return $this->sendRequest('PUT');
    }

    /**
     * Удаляет элемент
     *
     * @param int $id ID элемента, который надо удалить
     * @return string ID удаленного элемнта
     */
    public function delete($id)
    {
        $this->setInputData(['id' => $id]);

        return $this->sendRequest('DELETE');
    }

    /**
     * Достает список элементов по указанным параметрам
     *
     * @param array $searchParams
     * @return array|bool|mixed|string
     */
    public function getAll($searchParams = [])
    {
        if (isset($this->inputData['id'])) {
            unset($this->inputData['id']);
        }
        $this->search = $searchParams;

        return $this->sendRequest('GET');
    }

    /**
     * Достает один элемент по его ID
     *
     * @param int $id
     * @return array|string
     */
    public function get($id)
    {
        $this->setInputData(['id' => $id]);

        return $this->sendRequest('GET');
    }

    /**
     * Создает новый элемент с указанными полями
     *
     * @param array $inputData
     * @return array|string массив со всеми полями нового элемнта
     */
    public function create($inputData)
    {
        $this->setInputData($inputData);
        $this->setInputXml();

        return $this->sendRequest('POST');
    }

    /**
     * @return array массив с ошибками
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * сеттер для ключей и поддомена
     *
     * @param $keys
     */
    public function setKeys($keys) {
        if (!empty($keys['apiKeyRead'])) {
            $this->apiKeyRead = $keys['apiKeyRead'];
        }
        if (!empty($keys['apiKeyWrite'])) {
            $this->apiKeyWrite = $keys['apiKeyWrite'];
        }
        if (!empty($keys['subdomain'])) {
            $this->subdomain = $keys['subdomain'];
        }
    }

    /**
     * Конвертирует массив с данными в XML
     * Этот формат используется при добавлении сущностей
     */
    protected function setInputXml()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<root>';
        foreach ($this->inputData as $item) {
            $xml .= "<item>";

            foreach ($item as $key => $value) {
                $xml .= "<{$key}>{$value}</{$key}>";
            }

            $xml .= "</item>";
        }

        $xml .= '</root>';

        $this->inputXml = $xml;
    }

    /**
     * Устанавливает данные для запроса
     * В будущем возможно будет проводить некоторые проверки валидности
     *
     * @param $data
     */
    protected function setInputData($data)
    {
        if (!is_array($data)) {
            $this->inputData = [];
        }

        $this->inputData = $data;
    }

    /**
     * Подготовка массива данных для запроса на сервер
     *
     * @param $requestType
     */
    protected function prepareQueryVars($requestType) {
        $key = $requestType == 'GET' ? $this->apiKeyRead : $this->apiKeyWrite;

        $queryVars = [
            'key' => $key,
        ];

        if ($this->doNotFallOnError) {
            $queryVars['donotfallonerror'] = 1;
        }

        if ($requestType == 'GET' AND empty($this->inputData['id'])) {
            // при GET запросе, если не указан ID, надо добавить параметры сортировки и поиска
            $queryVars['param'] = $this->params;
            $queryVars['search'] = $this->search;
        }

        if ($requestType != 'POST') {
            $queryVars = array_merge($queryVars, $this->inputData);
        }

        $this->queryVars = $queryVars;
    }

    /**
     * отправка запроса на сервер
     *
     * @param $type
     * @return array|bool|mixed|null|string
     */
    protected function sendRequest($type)
    {
        if (!$this->entity) {
            return 'Please set entity to work with';
        }

        $this->prepareQueryVars($type);

        $curl = curl_init();

        if ($type == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS,  $this->inputXml);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-type: text/xml;charset="utf-8"',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
            ]);
        } else {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
        }

        $url = "https://{$this->subdomain}.autoweboffice.ru/?r=api/rest/{$this->entity}&".http_build_query($this->queryVars);
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, false);

        $this->response = curl_exec($curl);

        $this->responseCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP-код ответа сервера

        return $this->decodeResponse();
    }

    /**
     * Пытаемся рзобрать ответ сервера
     *
     * @return array|bool|mixed|string
     */
    protected function decodeResponse()
    {
        $responseOriginal = $this->response;

        // апи может выдать джейсон. пробуем декодировать
        $responseDecoded = json_decode($responseOriginal);

        if (!$responseDecoded) {
            // апи выдает несколько джейсонов подряд, если добавляет несколько сущностей скопом. пробуем декодировать.

            // сначала убираем скобки в начале и конце строки
            $response = substr($responseOriginal, 1);
            $response = substr($response, 0, -1);

            // разбиваем строку
            $jsonsArray = explode('}{', $response);

            // пробуем декодировать элементы получившегося массива
            $responseDecoded = array_map(function ($item) {
                $decoded = json_decode('{' . $item . '}');
                if ($decoded){
                    return $decoded;
                }

                return null;
            }, $jsonsArray);

            // убираем пустые элементы
            $responseDecoded = array_filter($responseDecoded, function ($val) {
                return $val;
            });
        }

        // если что-то удалось декодировать - возвращаем это
        if ($responseDecoded) {
            return $responseDecoded;
        }

        // не получилось декодировать, возвращаем как есть
        return $responseOriginal;
    }

    /**
     * Коды ошибок с описаниями
     *
     * @return array
     */
    protected function responseCodes()
    {
        return [
            200 => 'OK',
            301 => 'Moved permanently',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];
    }

    private function getApiKeyRead()
    {
        return trim($this->getConfig('apiKeyRead'));
    }

    private function getApiKeyWrite()
    {
        return trim($this->getConfig('apiKeyWrite'));
    }

    private function getSubdomain()
    {
        return trim($this->getConfig('subdomain'));
    }

    public function credentialsFilled()
    {
        return $this->getApiKeyRead() != '' || $this->getApiKeyWrite() != '' || $this->getSubdomain() != '';
    }

    public function getAllGroups($except = array())
    {
        return $this->subscriberGroup()->getAll();
    }

    public function addUser()
    {
        $addTo = $this->getConfig('rid');
        $deleteFromAll = $this->getConfig('del_rid');

        $result = $this->subscription()->create([
            [
                'last_name' => $this->userInfo->last_name, // фамилия
                'name' => $this->userInfo->first_name, // имя
                'middle_name' => $this->userInfo->surname, // отчество
                'email' => $this->userInfo->user_email, // email - Обязательный параметр
                'password' => '', // пароль для фхода в личный кабинет
                'phone_number' => $this->userInfo->phone, // номер телефона
                'skype' => '', // скайп
                'id_partner' => 0, // ID партнера
                'id_newsletter' => $addTo, // ID рассылки, на которую надо подписать -  - Обязательный параметр
                'creation_date' => date('Y-m-d H:i:s'), // дата созданиия записи
                'confirmed' => 1, // 1 или 0. Признак подтверждения подписки
                'confirmed_date' => date('Y-m-d H:i:s'), // Дата подтверждения подписки
            ]
        ]);

        if ($deleteFromAll) {

            // получаем контакт
            $contact = $this->contact()->getAll([
                'email' => $this->userInfo->user_email
            ]);

            if (!is_array($contact) || !$contact) {
                return $result;
            }

            // получаем все группы
            $allGroups = $this->subscriberGroup()->getAll();

            // получаем id контакта
            $idContact = $contact[0]->id_contact;

            foreach ($allGroups as $group) {

                // ищем подписку для этого контакта на указанную группу
                $subscriber = $this->subscriber()->getAll([
                    'id_contact' => $idContact,
                    'id_newsletter' => $group->id_newsletter
                ]);

                if (!is_array($subscriber) || !$subscriber) {
                    continue;
                }

                // удаляем найденную подписку
                $this->subscriber()->delete($subscriber[0]->id_contact_newsletter_links);
            }
            // проверяем контакт в указанной подписке
            $subscriber = $this->subscriber()->getAll([
                'id_contact' => $idContact,
                'id_newsletter' => $addTo,
            ]);

            if (is_array($subscriber) && $subscriber) {
                return $result;
            }

            // создаем новую подписку
            $result = $this->subscriber()->create([
                [
                    'id_contact' => $idContact,
                    'last_name' => $this->userInfo->last_name, // фамилия
                    'name' => $this->userInfo->first_name, // имя
                    'middle_name' => $this->userInfo->surname, // отчество
                    'email' => $this->userInfo->user_email, // email - Обязательный параметр
                    'password' => '', // пароль для фхода в личный кабинет
                    'phone_number' => $this->userInfo->phone, // номер телефона
                    'skype' => '', // скайп
                    'id_partner' => 0, // ID партнера
                    'id_newsletter' => $addTo, // ID рассылки, на которую надо подписать -  - Обязательный параметр
                    'creation_date' => date('Y-m-d H:i:s'), // дата созданиия записи
                    'confirmed' => 1, // 1 или 0. Признак подтверждения подписки
                    'confirmed_date' => date('Y-m-d H:i:s'), // Дата подтверждения подписки
                ]
            ]);
        }

        return $result;
    }

    public function unsubscribe()
    {
        if ($this->getConfig('auto_disable') == 'on') {

            $contact = $this->contact()->getAll([
                'email' => $this->userInfo->user_email,
            ]);

            if (!is_array($contact) || !$contact) {
                echo  __('контакт не найден', 'mbl_admin');
                return;
            }

            $idContact = $contact[0]->id_contact;

            $subscriber = $this->subscriber()->getAll([
                'id_contact' => $idContact,
                'id_newsletter' => $this->getConfig('rid')
            ]);

            if (!is_array($subscriber) || !$subscriber) {
                echo  __('подписка не найдена', 'mbl_admin');
                return;
            }

            $this->subscriber()->delete($subscriber[0]->id_contact_newsletter_links);
        }
    }

    public function updateUser()
    {
    }
}