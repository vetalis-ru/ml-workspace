<?php

class MBLTranslator
{
    private static $path_po;
    private static $path_mo;

    private static $admin_path_po;
    private static $admin_path_mo;

    public static $ru_locales = [
        'ru_RU',
        'bel',
        'uk',
    ];

    private static $language = 'ru';

    private static $db_values = [];

    /**
     * @var MBLTranslationPoIterator
     */
    private static $iterator;

    public static function getIterator()
    {
        self::_initIterator();

        return self::$iterator;
    }

    private static function _init()
    {
        $lang = self::getCurrentLanguage();

        // Синхронизируем статическую переменную для обратной совместимости
        self::$language = $lang;

        self::$path_po = WP_PLUGIN_DIR . "/member-luxe/languages/mbl_{$lang}.po";
        self::$path_mo = WP_PLUGIN_DIR . "/member-luxe/languages/mbl_{$lang}.mo";

        // Fallback на русский язык только для путей к файлам (если файлы не найдены)
        // НЕ меняем self::$language, чтобы сохранение работало для правильного языка
        if (!file_exists(self::$path_po)) {
            self::$path_po = WP_PLUGIN_DIR . '/member-luxe/languages/mbl_ru.po';
        }
        if (!file_exists(self::$path_mo)) {
            self::$path_mo = WP_PLUGIN_DIR . '/member-luxe/languages/mbl_ru.mo';
        }

        if (in_array(get_locale(), self::$ru_locales)) {
            self::$admin_path_po = WP_PLUGIN_DIR . '/member-luxe/languages/mbl_admin_ru.po';
            self::$admin_path_mo = WP_PLUGIN_DIR . '/member-luxe/languages/mbl_admin_ru.mo';
        } else {
            self::$admin_path_po = WP_PLUGIN_DIR . '/member-luxe/languages/mbl_admin_en.po';
            self::$admin_path_mo = WP_PLUGIN_DIR . '/member-luxe/languages/mbl_admin_en.mo';
        }
    }

    private static function _initIterator()
    {
        self::_init();

        if (!isset(self::$iterator)) {
            self::$iterator = new MBLTranslationPoIterator(MBLTranslationParser::parse(file_get_contents(self::$path_po)));
        }
    }

    public static function save()
    {
        if (isset(self::$iterator)) {
            $lang = self::$language;
            
            // Используем прямые пути без fallback, чтобы создать файлы для нужного языка
            $path_po = WP_PLUGIN_DIR . "/member-luxe/languages/mbl_{$lang}.po";
            $path_mo = WP_PLUGIN_DIR . "/member-luxe/languages/mbl_{$lang}.mo";
            
            //writing po
            file_put_contents(
                $path_po,
                self::$iterator->msgcat()
            );

            //writing mo
            file_put_contents(
                $path_mo,
                self::$iterator->msgfmt()
            );
        }
    }

    public static function updateByMD5($key, $value)
    {
        self::_initIterator();

        self::$iterator->setByMD5($key, $value);
    }


    public static function load()
    {
        self::_init();
        self::_initDBValues();
        load_textdomain('mbl', self::$path_mo);

        if (wpm_is_interface_2_0()) {
            load_textdomain('mbl_admin', self::$admin_path_mo);
        }
    }

    public static function saveTranslations($values)
    {
        self::_initIterator();

        foreach ($values as $hash => $value) {
            self::updateByMD5($hash, $value);
            self::_updateDBByMD5($hash, $value);
        }

        self::save();

        unload_textdomain('mbl');
        self::load();
    }

    public static function updateAll()
    {
        global $wpdb;
        self::load();

        foreach (self::$db_values as $hash => $row) {
            self::updateByMD5($hash, $row->msgstr);
        }

        self::save();
        unload_textdomain('mbl');
        self::load();

        $wpdb->query('START TRANSACTION');
        foreach (self::getIterator() as $translationRow) {
            self::_updateDBByMD5($translationRow->getMD5Hash(), __($translationRow->getHash(), 'mbl'));
        }
        $wpdb->query('COMMIT');

        self::save();
        unload_textdomain('mbl');
        self::load();
    }

    private static function _initDBValues()
    {
        global $wpdb;

        $table = self::_getTable();
        $lang = self::$language;

        if (version_compare(get_option('wpm_last_db_update', '0.1'), '1.3', '>=')) {
            $values = $wpdb->get_results("SELECT t.* FROM {$table} t WHERE `language_code`='{$lang}'", OBJECT);

            foreach ($values as $value) {
                self::$db_values[$value->hash] = $value;
            }
        }
    }

    private static function _updateDBByMD5($hash, $value)
    {
        global $wpdb;
        $table = self::_getTable();
        $lang = self::$language;
        $iterator = clone self::$iterator;
        $msg = $iterator->getByMD5($hash);

        if (isset(self::$db_values[$hash]) && self::$db_values[$hash]->msgstr != $value) {
            $row = self::$db_values[$hash];
            $id = $row->id;
            $sql = $wpdb->prepare("UPDATE {$table} SET `msgstr` = %s WHERE `id` = {$id};", $value);

            return $wpdb->query($sql);
        } elseif (!isset(self::$db_values[$hash]) && $msg && $msg->getHash()) {
            $sql = $wpdb->prepare("INSERT INTO {$table} (`language_code`, `hash`, `msgid`, `msgstr`) VALUES (%s, %s, %s, %s);",
                $lang,
                $hash,
                $msg->getHash(),
                $value
            );

            return $wpdb->query($sql);
        }

        return false;
    }

    private static function _getTable()
    {
        global $wpdb;

        return $wpdb->prefix . "memberlux_translations";
    }

    public static function getDBValues()
    {
        return self::$db_values;
    }

    public static function replaceByMsgIdAndValue($msgId, $msgStr, $newValue)
    {
        global $wpdb;
        $table = self::_getTable();

        $sql = $wpdb->prepare("UPDATE {$table} SET `msgstr` = %s WHERE `msgid` = %s AND `msgstr` = %s;", $newValue, $msgId, $msgStr);

        return $wpdb->query($sql);
    }

    /**
     * Список доступных языков (задаётся разработчиком плагина)
     */
    private static $available_languages = [
        'ru' => 'Русский',
        'en' => 'English',
        'ua' => 'Українська',
        'kz' => 'Қазақша',
    ];

    /**
     * Получить текущий выбранный язык
     */
    public static function getCurrentLanguage()
    {
        return get_option('mbl_current_language', 'ru');
    }

    /**
     * Установить текущий язык
     */
    public static function setCurrentLanguage($lang_code)
    {
        update_option('mbl_current_language', $lang_code);
        self::$language = $lang_code;
        
        // Сбрасываем итератор, чтобы он переинициализировался для нового языка
        self::$iterator = null;
        self::$db_values = [];
    }

    /**
     * Получить список доступных языков
     */
    public static function getAvailableLanguages()
    {
        $languages = [];

        foreach (self::$available_languages as $code => $name) {
            $languages[] = [
                'code' => $code,
                'name' => $name
            ];
        }

        return $languages;
    }

    /**
     * Загрузить дефолтные переводы для языка из .po файла
     */
    public static function loadDefaultTranslations($lang_code)
    {
        $po_file = WP_PLUGIN_DIR . "/member-luxe/languages/mbl_{$lang_code}.po";

        if (file_exists($po_file)) {
            return MBLTranslationParser::parse(file_get_contents($po_file));
        }

        return null;
    }

    /**
     * Получить переводы для языка (дефолты + пользовательские изменения)
     */
    public static function getTranslationsForLanguage($lang_code)
    {
        $defaults_parsed = self::loadDefaultTranslations($lang_code);

        if (!$defaults_parsed) {
            return [];
        }

        $defaults_iterator = new MBLTranslationPoIterator($defaults_parsed);

        global $wpdb;
        $table = self::_getTable();
        $custom = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE language_code = %s", $lang_code),
            OBJECT_K
        );

        $result = [];
        foreach ($defaults_iterator as $msg) {
            $hash = $msg->getMD5Hash();
            $msgid = $msg->getHash();

            $row = new stdClass();
            $row->hash = $hash;
            $row->msgid = $msgid;
            $row->default_msgstr = $msg->target;

            if (isset($custom[$hash])) {
                $row->msgstr = $custom[$hash]->msgstr;
            } else {
                $row->msgstr = $msg->target;
            }

            $result[] = $row;
        }

        return $result;
    }
}