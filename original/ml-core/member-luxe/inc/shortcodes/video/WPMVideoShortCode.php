<?php

class WPMVideoShortCode
{
    private static $videoLink;
    private static $autoPlay;
    private static $width;
    private static $height;
    private static $ratio;
    private static $aspectRatio;
    private static $ratioStyle;
    private static $style;
    private static $videoUrl;
    private static $wrapperClass;
    private static $options;
    private static $isLocal;
    private static $camera_protection;
    private static $sources;
    private static $formats;
    /**
     * @var string
     */
    private static $poster;
    private static $player_config;

    public static function parse($options, $content = null)
    {
        $timecodes = [];
        if (!empty($content)) {
            $pattern = '/\[(.*?)]/';
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    preg_match('/time="([0-9]{2}:[0-9]{2}:[0-9]{2})"/', $match, $timeMatches);
                    if (empty($timeMatches[1])) {
                        continue;
                    }
                    $time = $timeMatches[1];
                    $timeInSeconds = strtotime($time) - strtotime('TODAY');
                    preg_match('/title="(.*?)"/', $match, $titleMatches);
                    $title = $titleMatches[1] ?? '';
                    $timecodes[] = ['title' => $title, 'time' => $time, 'seconds' => $timeInSeconds];
                }
            }
        }

        self::_init();
        self::$options = $options;
        self::$options['timecodes'] = $timecodes;

        self::_replaceOptions();
        self::_parseOptions();

        $default_platforms = [
            [
                'check_cb' => fn($args) => self::_isVimeo(),
                'html' => fn($args) => self::_parseVimeo()
            ],
            [
                'check_cb' => fn($args) => self::_isYoutube(),
                'html' => fn($args) => self::_parseYoutube()
            ],
            [
                'check_cb' => fn($args) => self::_isRutube(),
                'html' => fn($args) => self::_parseRutubeIframe()
            ]
        ];

        $platforms = apply_filters('wpm_video_shortcode_platforms', $default_platforms);

        foreach ($platforms as $platform) {
            $cb_args = [
                'videoUrl' => self::$videoUrl,
                'videoLink' => self::$videoLink,
                'width' => self::$width,
                'height' => self::$height,
                'style' => self::$style,
                'poster' => self::$poster,
            ];
            if ($platform['check_cb']($cb_args)) {
                $html = call_user_func($platform['html'], $cb_args);
                break;
            }
        }

        if (!isset($html)) {
            self::$isLocal = true;
            $html = self::_parseLocal();
        }

        if( self::$camera_protection == 'on' ){
        	$html =  '<div data-protect="on">' . $html . '</div>';
        }

        return apply_filters('video_html', $html);
    }

    public static function player($html, $videoId, $type) {
        $timecodes = self::$options['timecodes'];
        $timecodes_html = '';
        foreach ($timecodes as $code) {
            $timecodes_html .= "<div class=\"wpm-timecode\">"
                ."<span class=\"wpm-timecode__time\" data-video=\"$videoId\" data-time=\"{$code['seconds']}\" data-timecode>"
                ."<i class=\"iconmoon icon-play\"></i> {$code['time']}".
                "</span>"
                ." <span class=\"wpm-timecode__label\">{$code['title']}</span></div>";
        }
        if (!empty($timecodes_html)) {
            $html .= '<div class="wpm-timecodes">'
                ."<div class=\"wpm-timecode__title\">Таймкоды</div>"
                . $timecodes_html
                . '</div>';
        }

        return $html;
    }

    private static function _init()
    {
        self::$isLocal = false;
    }

    private static function _replaceOptions()
    {
        self::$options['video'] = wpm_remove_protocol(str_replace(array('www.dropbox.com', 'dropbox.com'), 'dl.dropboxusercontent.com', self::$options['video']));
    }

    private static function _parseOptions()
    {
        self::$videoLink = wpm_array_get(self::$options, 'video');
        self::$autoPlay = wpm_array_get(self::$options, 'autoplay');
        self::$width = wpm_array_get(self::$options, 'width');
        self::$height = wpm_array_get(self::$options, 'height');
        self::$ratio = wpm_array_get(self::$options, 'ratio', '16by9');
        self::$style = wpm_array_get(self::$options, 'style', 'normal');
        self::$camera_protection = (self::$options['protection'] ?? null)? 'on' : 'off';
        self::$sources = explode(',', (self::$options['sources'] ?? ''));
        self::$formats = explode(',', (self::$options['formats'] ?? ''));
        self::$ratioStyle = '';
        self::$poster = wpm_array_get(self::$options, 'poster', '');

        if (!empty(self::$width) && !empty(self::$height)) {
            self::$ratioStyle = '';
            self::$aspectRatio = round(self::$width / self::$height, 2);
        } elseif (self::$ratio == '4by3') {
            self::$aspectRatio = 1.33;
        } else {
            self::$aspectRatio = 1.77;
        }

        if (!empty(self::$width)) {
            self::$width = 'max-width: ' . self::$width . 'px';
        } else {
            self::$width = '';
        }

        self::$videoUrl = parse_url(self::$videoLink);
        self::$wrapperClass = 'embed-responsive';

        if (self::$ratioStyle === '') {
            self::$wrapperClass .= ' embed-responsive-' . self::$ratio;
        }

        self::$player_config = ['invertTime' => false];
        if (!empty(self::$options['timecodes'])) {
            $points = array_map(fn($code) => ['time' => $code['seconds'], 'label' => $code['title']], self::$options['timecodes']);
            self::$player_config['markers'] = ['enabled' => true, 'points' => $points];
        }
    }

    private static function _isRutube() {
        return in_array(self::$videoUrl['host'] ?? '', array('www.rutube.ru', 'rutube.ru'))
            || preg_match('/rutube\.ru/', self::$videoLink);
    }

    private static function _isVimeo()
    {
        return in_array(self::$videoUrl['host'] ?? '', array('www.vimeo.com', 'vimeo.com'))
            || preg_match('/vimeo\.com/', self::$videoLink);
    }

	private static function _parseVimeo()
	{
		if (wpm_option_is('protection.vimeo_standard_player', '1') || is_admin()) {
			$html = self::_parseVimeoPlain();
		} else {
			$videoId = 'vid_id_' . substr(md5(rand(0, 1000)), 0, 20);
            $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';

			if (self::$style != 'normal') {
				$html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="style-video wpm-video-direct wpm-video-vimeo wpjw style-' . self::$style . '" style="' . self::$width . '"><div class="">';
				$html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" controls '.$posterAttr.'></video>';
				$html .= '</div></div></div>';
			} else {
				$html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="wpm-video-direct wpm-video-vimeo no-style video_wrap video_margin_center" style="' . self::$width . '"><div class="" style="' . self::$ratioStyle . '">';
				$html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" autobuffer controls '.$posterAttr.'></video>';
				$html .= '</div></div></div>';
			}

			$html .= sprintf(
                '<script>wpmVideo.initDirect("%s", "%s", %s, %d, false, "%s", JSON.parse(\''.json_encode(self::$player_config).'\'))</script>',
                $videoId,
                'vimeo',
                self::_getCryptedLink(),
                self::$autoPlay == 'on',
                wpm_plyr_version()
            );
            $html = apply_filters('wpm_video_player_html', $html, $videoId, 'vimeo');
		}

		return $html;
	}

    private static function _isYoutube()
    {
        return strpos(self::$videoUrl['host'] ?? '', 'youtu') !== false
            || preg_match('/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i', self::$videoLink);
    }

    private static function _parseYoutube()
    {
        $pattern = '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=‌​(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#';
        preg_match($pattern, self::$videoLink, $matches);
        if (isset($matches[0])) {
            $youtubeId = $matches[0];
        } else {
            parse_str(parse_url(self::$videoLink, PHP_URL_QUERY), $params);
            $youtubeId = isset($params['v']) ? $params['v'] : (isset($params['amp;v']) ? $params['amp;v'] : '0');
        }

        $youtubeId = preg_replace('/\?.*$/', '', $youtubeId);

        if(wpm_option_is('protection.youtube_standard_player', '1') || is_admin()) {
            $html = self::_parseYTPlain($youtubeId);
        } elseif (self::_protectionIsEnabled()) {
            $html = self::_parseYoutubeProtected($youtubeId);
        } else {
            $html = self::_parseYoutubeIframe($youtubeId);
        }

        return $html;
    }

    private static function _parseYTPlain($youtubeId)
    {
        $videoId = 'vid_id_' . substr(md5($youtubeId . rand(0, 1000)), 0, 20);
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';
        $autoplay = (self::$autoPlay == 'on') ? '&autoplay=1' : '';
        $width = self::$width;
        $height = self::$height;
        if(is_admin()) {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="width: 100%; max-width: 100%"><div class="wpm-video-direct no-style video_wrap video_margin_center"><div class="" style="' . self::$ratioStyle . '">'
                    .'<iframe id="' . $videoId . '" width="728px" style="width: 100%; max-width: 100%" height="410px" src="https://www.youtube.com/embed/' . $youtubeId . $autoplay . '" frameborder="0" allowfullscreen '. $posterAttr .'></iframe>'
                    .'</div></div></div>';
        } else {
            $iframe = '<iframe id="' . $videoId . '" width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $youtubeId . $autoplay . '" frameborder="0" allowfullscreen '. $posterAttr .'></iframe>';

            if (self::$style != 'normal') {
                $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . $width . '"><div class="style-video wpm-video-youtube style-' . self::$style . '"  style="' . $width . '"><div class="embed-responsive embed-responsive-16by9">';
                $html .= $iframe;
                $html .= '</div></div></div>';
            } else {
                $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . $width . '"><div class="wpm-video-youtube no-style video_wrap video_margin_center" style="' . $width . '"><div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
                $html .= $iframe;
                $html .= '</div></div></div>';
            }
        }

        return apply_filters('wpm_video_player_html', $html, $videoId, 'youtube');
    }

	private static function _parseVimeoPlain()
	{
		$videoLink = self::$videoLink;
		$width = self::$width;
        $height = self::$height;
        $style = self::$style;

		sscanf(parse_url($videoLink, PHP_URL_PATH), '/%d', $vimeoVideoId);

		preg_match('#http://(?:player\.)?vimeo\.com(?:/video)?.*/(\d+)#', $videoLink, $matches);

		if (isset($matches[1])) {
			$vimeoVideoId = $matches[1];
		}
		$videoId = 'vid_id_' . substr(md5($vimeoVideoId . rand(0, 1000)), 0, 20);
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';
		$autoplay = (self::$autoPlay == 'on') ? '?autoplay=true' : '';

		$link = 'https://player.vimeo.com/video/' . $vimeoVideoId . $autoplay;

		if(is_admin()) {
            return '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="width: 100%; max-width: 100%"><div class="wpm-video-direct no-style video_wrap video_margin_center"><div class="" style="' . self::$ratioStyle . '">'
                    .'<iframe class="no_border" style="margin:0 auto; padding:0; width: 100%; max-width: 100%" src="' . $link . '" width="728px" height="410px" frameborder="0" allowFullScreen '.$posterAttr.'></iframe>'
                    .'</div></div></div>';
        }

		$iframe = '<iframe class="no_border" style="margin:0 auto; padding:0;" src="' . $link . '" width="' . $width . '" height="' . $height . '" frameborder="0" allowFullScreen '.$posterAttr.'></iframe>';

		if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . $width . '"><div class="style-video wpm-video-youtube style-' . self::$style . '"  style="' . $width . '"><div class="embed-responsive embed-responsive-16by9">';
            $html .= $iframe;
            $html .= '</div></div></div>';

            return $html;
        } else {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . $width . '"><div class="wpm-video-youtube no-style video_wrap video_margin_center" style="' . $width . '"><div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
            $html .= $iframe;
            $html .= '</div></div></div>';

            return $html;
        }
	}

    private static function _parseLocal()
    {
        if (self::_protectionIsEnabled()) {
            $html = self::_parseLocalProtected();
        } else {
            $html = self::_parseLocalFile();
        }

        return $html;
    }

    private static function _parseLocalFile()
    {
        $videoId = 'vid_id_' . substr(md5(rand(0, 1000)), 0, 20);
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';

        if(is_admin()) {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="width: 100%; max-width: 100%"><div class="wpm-video-direct no-style video_wrap video_margin_center"><div class="" style="' . self::$ratioStyle . '">';
            $html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="100%" height="' . self::$height . '" autobuffer controls '.$posterAttr.'><source src="' . self::_getCryptedLink() . '" type="video/mp4"></video>';
            $html .= '</div></div></div>';

            return $html;
        } elseif (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="style-video wpm-video-direct wpjw style-' . self::$style . '" style="' . self::$width . '"><div class="">';
            $html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" controls '.$posterAttr.'></video>';
            $html .= '</div></div></div>';
        } else {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="wpm-video-direct no-style video_wrap video_margin_center" style="' . self::$width . '"><div class="" style="' . self::$ratioStyle . '">';
            $html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" autobuffer controls '.$posterAttr.'></video>';
            $html .= '</div></div></div>';
        }

        $html .= sprintf(
            '<script>wpmVideo.initDirect("%s", "%s", %s, %d, %s, "%s", JSON.parse(\''.json_encode(self::$player_config).'\'))</script>',
            $videoId,
            'video',
            self::_getCryptedLink(),
            self::$autoPlay=='on',
            self::_parceSources(),
            wpm_plyr_version()
        );

        return apply_filters('wpm_video_player_html', $html, $videoId, 'video');
    }

    private static function _parseLocalProtected()
    {
        $linkCrypted = self::_getCryptedLink();
        $videoId = 'vid_id_' . substr(md5(rand(0, 1000)), 0, 20);
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';
        $script = '<script>wpmVideo.initYT("%s",%s,"%s",%d, true)</script>';

        if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '">';
            $html .= '<div class="style-video wpjw wpmjw inactive style-' . self::$style . '" style="' . self::$width . '">';
            $html .= '<div class="embed-responsive embed-responsive-16by9">';
            $html .= '<div id="' . $videoId . '" '.$posterAttr.'></div>';
            $html .= '</div></div></div>';
            $html .= sprintf($script, $videoId, $linkCrypted, "16:9", intval(self::$autoPlay == 'on'));
        } else {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '">';
            $html .= '<div class="video_wrap video_margin_center wpmjw inactive" style="' . self::$width . '">';
            $html .= '<div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
            $html .= '<div id="' . $videoId . '" '.$posterAttr.'></div>';
            $html .= '</div></div></div>';
            $html .= sprintf($script, $videoId, $linkCrypted, self::$aspectRatio . ':1', intval(self::$autoPlay == 'on'));
        }

        return $html;
    }

    private static function _protectionIsEnabled()
    {
        return wpm_yt_protection_is_enabled();
    }

    private static function _parseYoutubeProtected($youtubeId)
    {
        $link = '//www.youtube.com/watch?v=' . $youtubeId;
        $linkCrypted = 'window[([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]+([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]]("' . utf8_uri_encode(base64_encode($link)) . '")';
        $videoId = 'vid_id_' . substr(md5($youtubeId . rand(0, 1000)), 0, 20);
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';
        $script = '<script>wpmVideo.initYT("%s",%s,"%s",%d)</script>';
        if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '">';
            $html .= '<div class="style-video wpm-video-youtube wpjw wpmjw inactive style-' . self::$style . '" style="' . self::$width . '">';
            $html .= '<div class="embed-responsive embed-responsive-16by9">';
            $html .= '<div id="' . $videoId . '" '.$posterAttr.'></div>';
            $html .= '</div></div></div>';
            $html .= sprintf($script, $videoId, $linkCrypted, "16:9", intval(self::$autoPlay == 'on'));
        } else {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '">';
            $html .= '<div class="wpm-video-youtube video_wrap video_margin_center wpmjw inactive" style="' . self::$width . '">';
            $html .= '<div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
            $html .= '<div id="' . $videoId . '" '.$posterAttr.'></div>';
            $html .= '</div></div></div>';
            $html .= sprintf($script, $videoId, $linkCrypted, self::$aspectRatio . ':1', intval(self::$autoPlay == 'on'));
        }

        return $html;
    }

    private static function _parseYoutubeIframe($youtubeId)
    {
        $videoId = 'vid_id_' . substr(md5(rand(0, 1000)), 0, 20);
        $protectionClass = wpm_option_is('protection.youtube_hide_controls', '1') ? 'protected' : '';
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';

        if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="style-video wpm-video-direct wpjw style-' . self::$style . ' ' . $protectionClass . '" style="' . self::$width . '"><div class="">';
            $html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" '.$posterAttr.' controls ></video>';
            $html .= '</div></div></div>';
        } else {
            $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="wpm-video-direct no-style video_wrap video_margin_center ' . $protectionClass . '" style="' . self::$width . '"><div class="" style="' . self::$ratioStyle . '">';
            $html .= '<video id="' . $videoId . '" class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" '.$posterAttr.' autobuffer controls></video>';
            $html .= '</div></div></div>';
        }

        $html .= sprintf(
            '<script>wpmVideo.initDirect("%s", "%s", %s, %d, false, "%s", JSON.parse(\''.json_encode(self::$player_config).'\'))</script>',
            $videoId,
            'youtube',
            self::_getCryptedLink($youtubeId),
            self::$autoPlay=='on',
            wpm_plyr_version()
        );

        return apply_filters('wpm_video_player_html', $html, $videoId, 'youtube');
    }

    private static function _parseRutubeIframe()
    {
        $videoId = 'vid_id_' . substr( md5( rand( 0, 1000 ) ), 0, 20 );
        $posterAttr = !empty(self::$poster) ? ('data-poster="' . self::$poster . '"') : '';
        $pattern = '/\/video\/([^\/?]+)/';
        preg_match($pattern, self::$videoLink, $matches);
        $rutubeId = $matches[1] ?? '';

        if (empty($rutubeId)) {
            return '';
        }

        $html = '<div class="wpm-video-size-wrap video-comment" aria-hidden="true" style="' . self::$width . '"><div class="style-video wpm-video-direct wpjw style-' . self::$style . '" style="' . self::$width . '">';
        $html .= '<div class="plyr"><div class="plyr__video-wrapper"><div style="position:relative;width: 100%;height: 100%;aspect-ratio: 16 / 9;">';
        $html .= '<iframe width="100%" height="100%" id="' . $videoId . '" style="position:absolute;border:0" src="https://rutube.ru/play/embed/' . $rutubeId . '" allow="clipboard-write; autoplay" webkitAllowFullScreen mozallowfullscreen allowFullScreen '.$posterAttr.'></iframe>';
        $html .= '</div></div></div>';
        $html .= '</div></div>';

        return $html;
    }

    private static function _getCryptedLink($link = null)
    {
        wpm_init_session();

        $_SESSION["flash"] = $_SERVER["HTTP_HOST"];

        if($link === null) {
            $link = self::$videoLink;
        }
        if(is_admin()) {
            return $link;
        } elseif (self::$isLocal && wpm_option_is('protection.video_url_encoded', 'on') && !self::_protectionIsEnabled()) {
            $link = wpm_array_get(wpm_protected_video_link($link), 'url', $link);
        }

        $encoded = base64_encode(utf8_uri_encode($link));

        $linkCrypted = 'window[([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]+([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]]("' . $encoded . '")';

        return $linkCrypted;
    }

	private static function _parceSources()
	{
		$sourset = '[';
		if ((self::$formats[0] ?? '') != '' && (self::$sources[0] ?? '') != '') {
			foreach ( self::$formats as $key=>$format ) {
				if(!self::_protectionIsEnabled()){
					$sourset .= '{'.
						'src:'. self::_getCryptedLink(self::$sources[$key]).
						', format: "' . $format .
					'"},';
				} else {
					$sourset .= '{'.
						'src: "'. self::$sources[$key].
						'", format: "' . $format .
						'"},';
				}
			}
		}

		$sourset .= ']';

		return $sourset;
	}
}
