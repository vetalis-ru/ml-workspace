<?php


class FontHandler
{
    private static array $fontsMap;
    public static function getFonts(): array
    {
        $fonts = [];
        foreach (glob(mblc_plugin_path('assets/fonts/*'), GLOB_ONLYDIR) as $font_dir) {
            $regular = basename(glob($font_dir . '/*-regular.ttf')[0] ?? '');
            $bold = basename(glob($font_dir . '/*-bold.ttf')[0] ?? '');
            if (!empty($regular)) {
                $fonts[basename($font_dir)]['R'] = $regular;
            }
            if (!empty($bold)) {
                $fonts[basename($font_dir)]['B'] = $bold;
            }
        }
        return $fonts;
    }
    public static function getFontsMap(): array
    {
        if (empty(self::$fontsMap)) {
            self::$fontsMap = [];
            foreach (glob(mblc_plugin_path('assets/fonts/*'), GLOB_ONLYDIR) as $font_dir) {
                $regular = glob($font_dir . '/*-regular.ttf')[0] ?? '';
                $bold = glob($font_dir . '/*-bold.ttf')[0] ?? '';
                if (!empty($regular)) {
                    self::$fontsMap[basename($font_dir)]['R'] = $regular;
                }
                if (!empty($bold)) {
                    self::$fontsMap[basename($font_dir)]['B'] = $bold;
                }
            }
        }

        return self::$fontsMap;
    }

    public static function getDirs()
    {
        return glob(mblc_plugin_path('assets/fonts/*'), GLOB_ONLYDIR);
    }

    public static function getDefault(): string
    {
        return basename(self::getDirs()[0]);
    }

    public static function getFontFamilies(): array
    {
        return array_map(fn ($fontDir) => basename($fontDir), self::getDirs());
    }

    public static function hasWeight($name, $value)
    {
        return count(glob(mblc_plugin_path('assets/fonts/') . $name . '/*-' . $value . '*'));
    }

    public static function generateDynamic()
    {
        ob_start();
        include mblc_plugin_path('assets/fonts/fonts.php');
        return self::compressCssCode(ob_get_clean());
    }

    public static function compressCssCode($code)
    {
        // Remove Comments
        $code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
        // Remove tabs, spaces, newlines, etc.
        return str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);
    }
}
