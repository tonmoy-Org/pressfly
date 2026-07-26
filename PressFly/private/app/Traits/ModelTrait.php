<?php

namespace App\Traits;

trait ModelTrait
{
    /**
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string $slug
     * @param int|null $id
     * @return null|string|string[]
     */
    public static function createSlug($model, $text, $id = null)
    {
        /*
        $slug = str_slug($text);

        if (empty($slug)) {
            $slug = self::slugGenerate($text);
        }
        */

        //$slug = self::slugGenerate($text);
        //$slug = self::cakeSlug($text);
        //$slug = \Str::slug($text, '-', 'en');
        //$slug = \Str::slug($text, '-', \app()->getLocale());

        $text = self::removeAccents($text);
        $slug = \Str::slug($text, '-', null);

        // check how WordPress generate slugs

        $i = 0;

        $conditions = [];

        $conditions[0] = ['slug', '=', $slug];

        if (!is_null($id)) {
            $conditions[1] = ['id', '<>', $id];
        }

        // https://stackoverflow.com/a/51302803/1794834
        while ($model::query()->where($conditions)->count()) {
            if (!preg_match('/-{1}[0-9]+$/', $slug)) {
                $slug .= '-' . ++$i;
            } else {
                $slug = preg_replace('/[0-9]+$/', ++$i, $slug);
            }
            $conditions[0] = ['slug', '=', $slug];
        }

        return $slug;
    }

    /**
     *
     * https://gist.github.com/evaisse/169594?permalink_comment_id=2023870#gistcomment-2023870
     * https://github.com/WordPress/WordPress/blob/6.1.1/wp-includes/formatting.php#L1595
     *
     * @param $string
     * @return string
     */
    public static function removeAccents($string): string
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = [
            // Decompositions for Latin-1 Supplement
            chr(194) . chr(170) => 'a',
            chr(194) . chr(186) => 'o',
            chr(195) . chr(128) => 'A',
            chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A',
            chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A',
            chr(195) . chr(133) => 'A',
            chr(195) . chr(134) => 'AE',
            chr(195) . chr(135) => 'C',
            chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E',
            chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E',
            chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I',
            chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I',
            chr(195) . chr(144) => 'D',
            chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O',
            chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O',
            chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O',
            chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U',
            chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U',
            chr(195) . chr(157) => 'Y',
            chr(195) . chr(158) => 'TH',
            chr(195) . chr(159) => 's',
            chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a',
            chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a',
            chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a',
            chr(195) . chr(166) => 'ae',
            chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e',
            chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e',
            chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i',
            chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i',
            chr(195) . chr(175) => 'i',
            chr(195) . chr(176) => 'd',
            chr(195) . chr(177) => 'n',
            chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o',
            chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o',
            chr(195) . chr(182) => 'o',
            chr(195) . chr(184) => 'o',
            chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u',
            chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u',
            chr(195) . chr(189) => 'y',
            chr(195) . chr(190) => 'th',
            chr(195) . chr(191) => 'y',
            chr(195) . chr(152) => 'O',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A',
            chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A',
            chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A',
            chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C',
            chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C',
            chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C',
            chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C',
            chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D',
            chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D',
            chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E',
            chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E',
            chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E',
            chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E',
            chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E',
            chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G',
            chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G',
            chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G',
            chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G',
            chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H',
            chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H',
            chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I',
            chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I',
            chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I',
            chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I',
            chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I',
            chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ',
            chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J',
            chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K',
            chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k',
            chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l',
            chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l',
            chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l',
            chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l',
            chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l',
            chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n',
            chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n',
            chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n',
            chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n',
            chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O',
            chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O',
            chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O',
            chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE',
            chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R',
            chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R',
            chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R',
            chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S',
            chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S',
            chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S',
            chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S',
            chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T',
            chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T',
            chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T',
            chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U',
            chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U',
            chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U',
            chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U',
            chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U',
            chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U',
            chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W',
            chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y',
            chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y',
            chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z',
            chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z',
            chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z',
            chr(197) . chr(191) => 's',
            // Decompositions for Latin Extended-B
            chr(200) . chr(152) => 'S',
            chr(200) . chr(153) => 's',
            chr(200) . chr(154) => 'T',
            chr(200) . chr(155) => 't',
            // Euro Sign
            chr(226) . chr(130) . chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194) . chr(163) => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            chr(198) . chr(160) => 'O',
            chr(198) . chr(161) => 'o',
            chr(198) . chr(175) => 'U',
            chr(198) . chr(176) => 'u',
            // grave accent
            chr(225) . chr(186) . chr(166) => 'A',
            chr(225) . chr(186) . chr(167) => 'a',
            chr(225) . chr(186) . chr(176) => 'A',
            chr(225) . chr(186) . chr(177) => 'a',
            chr(225) . chr(187) . chr(128) => 'E',
            chr(225) . chr(187) . chr(129) => 'e',
            chr(225) . chr(187) . chr(146) => 'O',
            chr(225) . chr(187) . chr(147) => 'o',
            chr(225) . chr(187) . chr(156) => 'O',
            chr(225) . chr(187) . chr(157) => 'o',
            chr(225) . chr(187) . chr(170) => 'U',
            chr(225) . chr(187) . chr(171) => 'u',
            chr(225) . chr(187) . chr(178) => 'Y',
            chr(225) . chr(187) . chr(179) => 'y',
            // hook
            chr(225) . chr(186) . chr(162) => 'A',
            chr(225) . chr(186) . chr(163) => 'a',
            chr(225) . chr(186) . chr(168) => 'A',
            chr(225) . chr(186) . chr(169) => 'a',
            chr(225) . chr(186) . chr(178) => 'A',
            chr(225) . chr(186) . chr(179) => 'a',
            chr(225) . chr(186) . chr(186) => 'E',
            chr(225) . chr(186) . chr(187) => 'e',
            chr(225) . chr(187) . chr(130) => 'E',
            chr(225) . chr(187) . chr(131) => 'e',
            chr(225) . chr(187) . chr(136) => 'I',
            chr(225) . chr(187) . chr(137) => 'i',
            chr(225) . chr(187) . chr(142) => 'O',
            chr(225) . chr(187) . chr(143) => 'o',
            chr(225) . chr(187) . chr(148) => 'O',
            chr(225) . chr(187) . chr(149) => 'o',
            chr(225) . chr(187) . chr(158) => 'O',
            chr(225) . chr(187) . chr(159) => 'o',
            chr(225) . chr(187) . chr(166) => 'U',
            chr(225) . chr(187) . chr(167) => 'u',
            chr(225) . chr(187) . chr(172) => 'U',
            chr(225) . chr(187) . chr(173) => 'u',
            chr(225) . chr(187) . chr(182) => 'Y',
            chr(225) . chr(187) . chr(183) => 'y',
            // tilde
            chr(225) . chr(186) . chr(170) => 'A',
            chr(225) . chr(186) . chr(171) => 'a',
            chr(225) . chr(186) . chr(180) => 'A',
            chr(225) . chr(186) . chr(181) => 'a',
            chr(225) . chr(186) . chr(188) => 'E',
            chr(225) . chr(186) . chr(189) => 'e',
            chr(225) . chr(187) . chr(132) => 'E',
            chr(225) . chr(187) . chr(133) => 'e',
            chr(225) . chr(187) . chr(150) => 'O',
            chr(225) . chr(187) . chr(151) => 'o',
            chr(225) . chr(187) . chr(160) => 'O',
            chr(225) . chr(187) . chr(161) => 'o',
            chr(225) . chr(187) . chr(174) => 'U',
            chr(225) . chr(187) . chr(175) => 'u',
            chr(225) . chr(187) . chr(184) => 'Y',
            chr(225) . chr(187) . chr(185) => 'y',
            // acute accent
            chr(225) . chr(186) . chr(164) => 'A',
            chr(225) . chr(186) . chr(165) => 'a',
            chr(225) . chr(186) . chr(174) => 'A',
            chr(225) . chr(186) . chr(175) => 'a',
            chr(225) . chr(186) . chr(190) => 'E',
            chr(225) . chr(186) . chr(191) => 'e',
            chr(225) . chr(187) . chr(144) => 'O',
            chr(225) . chr(187) . chr(145) => 'o',
            chr(225) . chr(187) . chr(154) => 'O',
            chr(225) . chr(187) . chr(155) => 'o',
            chr(225) . chr(187) . chr(168) => 'U',
            chr(225) . chr(187) . chr(169) => 'u',
            // dot below
            chr(225) . chr(186) . chr(160) => 'A',
            chr(225) . chr(186) . chr(161) => 'a',
            chr(225) . chr(186) . chr(172) => 'A',
            chr(225) . chr(186) . chr(173) => 'a',
            chr(225) . chr(186) . chr(182) => 'A',
            chr(225) . chr(186) . chr(183) => 'a',
            chr(225) . chr(186) . chr(184) => 'E',
            chr(225) . chr(186) . chr(185) => 'e',
            chr(225) . chr(187) . chr(134) => 'E',
            chr(225) . chr(187) . chr(135) => 'e',
            chr(225) . chr(187) . chr(138) => 'I',
            chr(225) . chr(187) . chr(139) => 'i',
            chr(225) . chr(187) . chr(140) => 'O',
            chr(225) . chr(187) . chr(141) => 'o',
            chr(225) . chr(187) . chr(152) => 'O',
            chr(225) . chr(187) . chr(153) => 'o',
            chr(225) . chr(187) . chr(162) => 'O',
            chr(225) . chr(187) . chr(163) => 'o',
            chr(225) . chr(187) . chr(164) => 'U',
            chr(225) . chr(187) . chr(165) => 'u',
            chr(225) . chr(187) . chr(176) => 'U',
            chr(225) . chr(187) . chr(177) => 'u',
            chr(225) . chr(187) . chr(180) => 'Y',
            chr(225) . chr(187) . chr(181) => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin)
            chr(201) . chr(145) => 'a',
            // macron
            chr(199) . chr(149) => 'U',
            chr(199) . chr(150) => 'u',
            // acute accent
            chr(199) . chr(151) => 'U',
            chr(199) . chr(152) => 'u',
            // caron
            chr(199) . chr(141) => 'A',
            chr(199) . chr(142) => 'a',
            chr(199) . chr(143) => 'I',
            chr(199) . chr(144) => 'i',
            chr(199) . chr(145) => 'O',
            chr(199) . chr(146) => 'o',
            chr(199) . chr(147) => 'U',
            chr(199) . chr(148) => 'u',
            chr(199) . chr(153) => 'U',
            chr(199) . chr(154) => 'u',
            // grave accent
            chr(199) . chr(155) => 'U',
            chr(199) . chr(156) => 'u',
        ];

        return strtr($string, $chars);
    }

    /**
     * @see https://stackoverflow.com/a/2955878/1794834
     */
    public static function slugGenerate($text)
    {
        $text = filter_var($text, FILTER_SANITIZE_STRING);

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        /*
        if (function_exists('iconv')) {
            // transliterate
            // https://github.com/symfony/polyfill-iconv
            $text = @iconv('utf-8', 'utf-8//TRANSLIT', $text);
        } else {
            //$encoding = mb_detect_encoding($text, "auto");
            //$text     = mb_convert_encoding($text, 'UTF-8', $encoding);
            @ini_set('mbstring.substitute_character', "none");
            $text     = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }
        */

        // remove unwanted characters
        //$text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = mb_strtolower($text);

        if (empty($text)) {
            return '';
        }

        return $text;
    }

    /**
     * Returns a string with all spaces converted to dashes (by default),
     * and non word characters removed.
     *
     * ### Options:
     *
     * - `replacement`: Replacement string. Default '-'.
     * - `preserve`: Specific non-word character to preserve. Default `null`.
     *   For e.g. this option can be set to '.' to generate clean file names.
     *
     * @param string $string the string you want to slug
     * @param array $options If string it will be use as replacement character
     *   or an array of options.
     *
     * @return string
     *
     * @see https://github.com/cakephp/cakephp/blob/3.6.11/src/Utility/Text.php#L1109
     */
    public static function cakeSlug($string, $options = [])
    {
        if (is_string($options)) {
            $options = ['replacement' => $options];
        }
        $options += [
            'replacement' => '-',
            'preserve' => null,
        ];

        $regex = '^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}';
        if ($options['preserve']) {
            $regex .= preg_quote($options['preserve'], '/');
        }
        $quotedReplacement = preg_quote($options['replacement'], '/');

        $map = [
            '/[' . $regex . ']/mu' => ' ',

            '/[\s]+/mu' => $options['replacement'],

            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        ];

        $string = preg_replace(array_keys($map), $map, $string);

        return mb_strtolower($string);
    }

    public static function purify($content = null, $configs = [])
    {
        if (!$content) {
            return $content;
        }

        $config = \HTMLPurifier_Config::createDefault();

        // Core
        $config->set('Core.Encoding', 'utf-8');

        // Cache
        $config->set('Cache.DefinitionImpl', null);

        // HTML
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // valid XML output (?)
        //$config->set('HTML.AllowedElements', ['p', 'div', 'a', 'br', 'ul', 'ol', 'li', 'b', 'i', 'img']);
        // remove all attributes except a.href
        //$config->set('HTML.AllowedAttributes', ['a.href', 'a.title', 'img.alt', 'img.src']);
        $config->set('Attr.EnableID', true);
        $config->set(
            'HTML.Allowed',
            '*[style|id|name|class|dir],p,h1,h2,h3,h4,h5,h6,pre,div,blockquote,strong,em,span,' .
            'sup,sub,ul,ol,li,a[href|title|rel|target],br,b,i,img[style|alt|title|src|width|height],code,
            table[border|cellspacing|cellpadding|width|align|summary|style],tr,tbody,' .
            'td[colspan|rowspan|align|valign|abbr],' .
            'th[colspan|rowspan|align|valign|abbr]'
        );

        // CSS
        $config->set(
            'CSS.AllowedProperties',
            'font,font-size,font-weight,font-style,font-family,text-decoration,color,background-color,' .
            'text-align,float,margin,margin-left,margin-right,display,padding,padding-left,padding-right'
        );
        $config->set('CSS.AllowTricky', true);
        //

        // Format
        $config->set('AutoFormat.AutoParagraph', true);
        $config->set('AutoFormat.RemoveEmpty', true);

        foreach ($configs as $config_key => $config_value) {
            $config->set($config_key, $config_value);
        }

        /**
         * @see https://gist.github.com/lluchs/3303693
         */
        $def = $config->getHTMLDefinition(true);
        $def->addAttribute('a', 'target', 'Enum#_blank');
        $def->addAttribute('table', 'align', 'Enum#left,right,center');
        //$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
        //$def->addElement('figcaption', 'Inline', 'Flow', 'Common');

        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($content);
    }

    public static function purifyStripHtml($content = null, $configs = [])
    {
        if (!$content) {
            return $content;
        }

        $config = \HTMLPurifier_Config::createDefault();

        // Core
        $config->set('Core.Encoding', 'utf-8');

        // Cache
        $config->set('Cache.DefinitionImpl', null);

        // HTML
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $config->set('HTML.Allowed', '');

        // Format
        $config->set('AutoFormat.RemoveEmpty', true);

        foreach ($configs as $config_key => $config_value) {
            $config->set($config_key, $config_value);
        }

        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($content);
    }

    /**
     * @see https://wordpress.stackexchange.com/a/66719/28527
     *
     * @param $content
     *
     * @return null|string|string[]
     */
    public static function stripSomeParagraphs($content)
    {
        /*
        $content = preg_replace(
            '/<p>(([\s]*)|[\s]*(<img[^>]*>|\[[^\]]*\])[\s]*)<\/p>/',
            '$3',
            $content
        );
        */

        $content = preg_replace(
            '/<p>(\[.*\])<\/p>/',
            '$1',
            $content
        );

        return $content;
    }

    public function getTextWords($text = null, $length = null, $strip = false, $leading = true)
    {
        if (!$text) {
            return '';
        }

        if (!$length) {
            return $text;
        }

        if ($strip) {
            $text = strip_tags($text);
        }

        $text_array = explode(' ', $text);

        if (count($text_array) > $length) {
            array_splice($text_array, $length);
            $text = implode(' ', $text_array);

            if ($leading) {
                $text .= '...';
            }
        }

        return $text;
    }

    /**
     * @param null $text
     * @param null $length
     * @param bool $strip
     * @param bool $leading
     * @return mixed|string
     *
     * @see https://stackoverflow.com/a/8286140/1794834
     */
    public function getTextChars($text = null, $length = null, bool $strip = false, bool $leading = true)
    {
        if (!$text) {
            return '';
        }

        if (!$length) {
            return $text;
        }

        if ($strip) {
            $text = strip_tags($text);
        }

        if (mb_strlen($text) > $length) {
            $token = strtok($text, ' ');

            $text = '';

            while ($token !== false && mb_strlen($text) < $length) {
                if (mb_strlen($text) + mb_strlen($token) <= $length) {
                    $text .= $token . ' ';
                } else {
                    break;
                }
                $token = strtok(' ');
            }

            $text = trim($text);

            if ($leading) {
                $text .= '...';
            }
        }

        return $text;
    }
}
