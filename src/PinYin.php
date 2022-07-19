<?php

namespace Jundayw\PinYin;

use Jundayw\PinYin\Contracts\DictLoaderInterface;
use Jundayw\PinYin\Support\FileDictLoader;
use Jundayw\PinYin\Traits\Macroable;

class PinYin
{
    use Macroable;

    public const PINYIN_DEFAULT          = 1024;
    public const PINYIN_TONE             = 2;
    public const PINYIN_NO_TONE          = 4;
    public const PINYIN_ASCII_TONE       = 8;
    public const PINYIN_NAME             = 16;
    public const PINYIN_KEEP_NUMBER      = 32;
    public const PINYIN_KEEP_ENGLISH     = 64;
    public const PINYIN_UMLAUT_V         = 128;
    public const PINYIN_KEEP_PUNCTUATION = 256;

    protected array $config = [];

    /**
     * Dict loader.
     *
     * @var DictLoaderInterface
     */
    protected mixed $loader;

    /**
     * Punctuations map.
     *
     * @var array
     */
    protected array $punctuations = [
        '，' => ',',
        '。' => '.',
        '！' => '!',
        '？' => '?',
        '：' => ':',
        '“' => '"',
        '”' => '"',
        '‘' => "'",
        '’' => "'",
        '_' => '_',
    ];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->loader = $config['driver'] ?? FileDictLoader::class;
    }

    /**
     * Convert string to pinyin.
     *
     * @param string $chinese
     * @param int $option
     *
     * @return array
     */
    public function convert(string $chinese, int $option = PinYin::PINYIN_DEFAULT)
    {
        return $this->splitWords($this->romanize($chinese, $option), $option);
    }

    /**
     * Convert string (person name) to pinyin.
     *
     * @param string $chinese
     * @param int $option
     *
     * @return array
     */
    public function name(string $chinese, int $option = PinYin::PINYIN_NAME)
    {
        $option = $option | PinYin::PINYIN_NAME;
        return $this->splitWords($this->romanize($chinese, $option), $option);
    }

    /**
     * Return a pinyin permalink from string.
     *
     * @param string $chinese
     * @param string $delimiter
     * @param int $option
     *
     * @return string
     */
    public function permalink(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
    {
        return implode($delimiter ?? '-', $this->convert($chinese, $option | PinYin::PINYIN_KEEP_NUMBER | PinYin::PINYIN_KEEP_ENGLISH));
    }

    /**
     * Return first letters.
     *
     * @param string $chinese
     * @param string $delimiter
     * @param int $option
     *
     * @return string
     */
    public function abbr(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
    {
        if ($option == PinYin::PINYIN_ASCII_TONE) {
            $option = PinYin::PINYIN_DEFAULT;
        }

        return implode($delimiter ?? '', array_map(function($pinyin) {
            return is_numeric($pinyin) || preg_match('/\d+/', $pinyin) ? $pinyin : mb_substr($pinyin, 0, 1);
        }, $this->convert($chinese, $option | PinYin::PINYIN_NO_TONE)));
    }

    /**
     * Chinese phrase to pinyin.
     *
     * @param string $chinese
     * @param string $delimiter
     * @param int $option
     *
     * @return string
     */
    public function phrase(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_DEFAULT)
    {
        return implode($delimiter ?? ' ', $this->convert($chinese, $option));
    }

    /**
     * Chinese to pinyin sentence.
     *
     * @param string $chinese
     * @param string $delimiter
     * @param int $option
     *
     * @return string
     */
    public function sentence(string $chinese, string $delimiter = null, int $option = PinYin::PINYIN_NO_TONE)
    {
        return implode($delimiter ?? ' ', $this->convert($chinese, $option | PinYin::PINYIN_KEEP_PUNCTUATION | PinYin::PINYIN_KEEP_ENGLISH | PinYin::PINYIN_KEEP_NUMBER));
    }

    /**
     * Setter DictLoader.
     *
     * @param DictLoaderInterface $loader
     * @return PinYin
     */
    public function setLoader(DictLoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }

    /**
     * Return DictLoader.
     *
     * @return DictLoaderInterface
     */
    public function getLoader()
    {
        if (!($this->loader instanceof DictLoaderInterface)) {
            $dataDir      = dirname(__DIR__) . '/resources/';
            $loaderName   = $this->loader;
            $this->loader = new $loaderName($dataDir);
        }

        return $this->loader;
    }

    /**
     * Convert Chinese to pinyin.
     *
     * @param string $chinese
     * @param int $option
     * @return string
     */
    protected function romanize(string $chinese, int $option = PinYin::PINYIN_DEFAULT)
    {
        $chinese    = $this->prepare($chinese, $option);
        $dictLoader = $this->getLoader();

        if ($this->hasOption($option, PinYin::PINYIN_NAME)) {
            $chinese = $this->convertSurname($chinese, $dictLoader);
        }

        foreach ($this->config['words'] ?? [] as $char => $pinyin) {
            $chinese = strtr($chinese, [$char => $pinyin]);
        }

        foreach ($this->config['chars'] ?? [] as $char => $pinyin) {
            $chinese = strtr($chinese, [$char => $pinyin]);
        }

        $dictLoader->map(function($dictionary) use (&$chinese) {
            $chinese = strtr($chinese, $dictionary);
        });

        return $chinese;
    }

    /**
     * Convert Chinese Surname to pinyin.
     *
     * @param string $chinese
     * @param DictLoaderInterface $dictLoader
     *
     * @return string
     */
    protected function convertSurname(string $chinese, DictLoaderInterface $dictLoader)
    {
        foreach ($this->config['surnames'] ?? [] as $surname => $pinyin) {
            if (0 === strpos($chinese, $surname)) {
                $chinese = $pinyin . mb_substr($chinese, mb_strlen($surname, 'UTF-8'), mb_strlen($chinese, 'UTF-8') - 1, 'UTF-8');
                break;
            }
        }

        $dictLoader->mapSurname(function($dictionary) use (&$chinese) {
            foreach ($dictionary as $surname => $pinyin) {
                if (0 === strpos($chinese, $surname)) {
                    $chinese = $pinyin . mb_substr($chinese, mb_strlen($surname, 'UTF-8'), mb_strlen($chinese, 'UTF-8') - 1, 'UTF-8');
                    break;
                }
            }
        });

        return $chinese;
    }

    /**
     * Split pinyin string to words.
     *
     * @param string $pinyin
     * @param int $option
     *
     * @return array
     */
    protected function splitWords(string $pinyin, int $option)
    {
        $split = array_filter(preg_split('/\s+/i', $pinyin));

        if (!$this->hasOption($option, PinYin::PINYIN_TONE)) {
            foreach ($split as $index => $pinyin) {
                $split[$index] = $this->formatTone($pinyin, $option);
            }
        }

        return array_values($split);
    }

    /**
     * @param int $option
     * @param int $check
     *
     * @return bool
     */
    public function hasOption(int $option, int $check)
    {
        return ($option & $check) === $check;
    }

    /**
     * Pre-process.
     *
     * @param string $chinese
     * @param int $option
     *
     * @return string
     */
    protected function prepare(string $chinese, int $option = PinYin::PINYIN_DEFAULT)
    {
        $chinese = preg_replace_callback('~[a-z0-9_-]+~i', function($matches) {
            return "\t" . $matches[0];
        }, $chinese);

        $regex = ['\p{Han}', '\p{Z}', '\p{M}', "\t"];

        if ($this->hasOption($option, PinYin::PINYIN_KEEP_NUMBER)) {
            array_push($regex, '0-9');
        }

        if ($this->hasOption($option, PinYin::PINYIN_KEEP_ENGLISH)) {
            array_push($regex, 'a-zA-Z');
        }

        if ($this->hasOption($option, PinYin::PINYIN_KEEP_PUNCTUATION)) {
            $punctuations = array_merge($this->punctuations, ["\t" => ' ', '  ' => ' ']);
            $chinese      = trim(str_replace(array_keys($punctuations), $punctuations, $chinese));
            array_push($regex, preg_quote(implode(array_merge(array_keys($this->punctuations), $this->punctuations)), '~'));
        }

        return preg_replace(sprintf('~[^%s]~u', implode($regex)), '', $chinese);
    }

    /**
     * Format.
     *
     * @param string $pinyin
     * @param int $option
     *
     * @return string
     */
    protected function formatTone(string $pinyin, int $option = PinYin::PINYIN_NO_TONE)
    {
        $replacements = [
            'üē' => ['ue', 1], 'üé' => ['ue', 2], 'üě' => ['ue', 3], 'üè' => ['ue', 4],
            'ā' => ['a', 1], 'ē' => ['e', 1], 'ī' => ['i', 1], 'ō' => ['o', 1], 'ū' => ['u', 1], 'ǖ' => ['yu', 1],
            'á' => ['a', 2], 'é' => ['e', 2], 'í' => ['i', 2], 'ó' => ['o', 2], 'ú' => ['u', 2], 'ǘ' => ['yu', 2],
            'ǎ' => ['a', 3], 'ě' => ['e', 3], 'ǐ' => ['i', 3], 'ǒ' => ['o', 3], 'ǔ' => ['u', 3], 'ǚ' => ['yu', 3],
            'à' => ['a', 4], 'è' => ['e', 4], 'ì' => ['i', 4], 'ò' => ['o', 4], 'ù' => ['u', 4], 'ǜ' => ['yu', 4],
        ];

        foreach ($replacements as $unicode => $replacement) {
            if (false !== strpos($pinyin, $unicode)) {
                $umlaut = $replacement[0];
                // https://zh.wikipedia.org/wiki/%C3%9C
                if ($this->hasOption($option, PinYin::PINYIN_UMLAUT_V) && 'yu' == $umlaut) {
                    $umlaut = 'v';
                }
                $pinyin = str_replace($unicode, $umlaut, $pinyin) . ($this->hasOption($option, PinYin::PINYIN_ASCII_TONE) ? $replacement[1] : '');
            }
        }

        return $pinyin;
    }
}
