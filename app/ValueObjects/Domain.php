<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Exceptions\DomainNameNotSupportedException;

/**
 * ValueObject для домена, умеет домен нормализовывать, возвращать первую букву домена.
 * Используется для передачи между слоями приложения.
 */
final class Domain
{
    /**
     * Преобразованное имя домена. Кириллица конвертируется в punycode,
     * Латиница остается без изменений, все слеши и поддомены удаляются.
     * Например:
     * http://sub2.sub1.test.com/foo/bar преобразовывается в test.com
     * @var string
     */
    public readonly string $name;

    /**
     * Домен без преобразований, если домен на латинице, он совпадает с $name,
     * домен на кириллице здесь содержит именно кириллический домен, без преобразования в punycode.
     * Необходимо для правильной проверки домен на кириллицу и для правильной подстановки первой буквы
     * в getFirstLetter() для кириллических доменов
     * @var string
     */
    private readonly string $raw;

    /**
     * Кешированное значение доменной зоны (чтобы 2 раза не высчитывать)
     * @var string|null
     */
    private ?string $zone = null;

    /**
     * Кешированное значение первой буквы домена (чтобы 2 раза не высчитывать)
     * @var string|null
     */
    private ?string $firstLetter = null;


    public function __construct(
        string $name,
    ) {
        $normalizedDomainName = $this->normalizeDomain($name);
        if ($normalizedDomainName === null) {
            throw new DomainNameNotSupportedException();
        }
        $this->name = $normalizedDomainName;
    }

    /**
     * Функция возвращает первую букву домена, для кириллических доменов идет маппинг в латиницу
     * @return string
     */
    public function getFirstLetter(): string
    {
        if ($this->firstLetter !== null) {
            return $this->firstLetter;
        }

        if ($this->isNotCyrillic()) {
            if ($this->isPunycode()) {
                return 'x';
            }
            $this->firstLetter = substr($this->raw, 0, 1);
            return $this->firstLetter;
        }

        $domain = $this->getRawRootDomain();

        $mapping = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'g',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'c',
            'ш' => 's',
            'щ' => 's',
            'ъ' => '0',
            'ы' => '0',
            'ь' => '0',
            'э' => '0',
            'ю' => 'y',
            'я' => 'y',
        ];

        $firstLetter = mb_substr($domain, 0, 1, 'UTF-8');
        $this->firstLetter = $mapping[$firstLetter] ?? $firstLetter;
        return $this->firstLetter;
    }

    /**
     * Функция возвращает название доменной зоны (правила обработки рф зон специфичное)
     * @return string
     */
    public function getZone(): string
    {
        if ($this->zone !== null) {
            return $this->zone;
        }
        if (str_ends_with($this->name, '.com.ua')) {
            $this->zone = 'com_ua';
            return 'com_ua';
        }
        $zone = array_reverse(explode('.', $this->name))[0];
        $zone = $this->normalizeZone($zone);
        $this->zone = $zone;
        return $this->zone;
    }

    /**
     * Функция возвращает домен второго уровня, это необходимо например для whois
     * @return string
     */
    public function getRootDomain(): string
    {
        if (mb_substr_count($this->name, '.', 'UTF-8') > 1) {
            $domainParts = array_reverse(explode('.', $this->name));
            $domain = "{$domainParts[1]}.{$domainParts[0]}";
            if ($domain == 'com.ua') {
                return "{$domainParts[2]}.{$domainParts[1]}.{$domainParts[0]}";
            }
            return $domain;
        }
        return $this->name;
    }

    public function getRawRootDomain(): string
    {
        $domain = $this->getRootDomain();
        if ($this->isCyrillic()) {
            return idn_to_utf8($domain);
        }
        return $domain;
    }

    /**
     * Функция проверяет, что переданный домен является кириллическим
     * @return bool
     */
    public function isCyrillic(): bool
    {
        $domain = idn_to_ascii($this->raw);
        if (!str_starts_with($domain, 'xn--')) {
            return false;
        }
        foreach ($this->getCyrillicZones() as $zone) {
            if (str_ends_with($domain, $zone)) {
                return true;
            }
        }
        return false;
    }

    public function isNotCyrillic(): bool
    {
        return !$this->isCyrillic();
    }

    public function isPunycode(): bool
    {
        $domain = idn_to_ascii($this->raw);
        return str_starts_with($domain, 'xn--');
    }

    /**
     * Функция нормализует доменную зону, в частности переводит из пуникода в понятный человеку язык
     * @param string $zone
     * @return string
     */
    private function normalizeZone(string $zone): string
    {
        if ($zone == 'xn--p1ai') {
            return 'rf';
        }
        if ($zone == 'xn--80asehdb') {
            return 'rf_online';
        }
        if ($zone == 'xn--80aswg') {
            return 'rf_site';
        }
        if ($zone == 'xn--d1acj3b') {
            return 'rf_children';
        }
        if ($zone == 'xn--80adxhks') {
            return 'rf_moscow';
        }
        if ($zone == 'xn--p1acf') {
            return 'rf_rus';
        }
        if ($zone == 'xn--j1aef') {
            return 'rf_com';
        }
        if ($zone == 'xn--c1avg') {
            return 'rf_org';
        }
        return $zone;
    }

    private function getCyrillicZones(): array
    {
        return [
            'xn--p1ai',
            'xn--80asehdb',
            'xn--80aswg',
            'xn--d1acj3b',
            'xn--80adxhks',
            'xn--p1acf',
            'xn--j1aef',
            'xn--c1avg',
        ];
    }

    /**
     * Функция нормализует домен, к примеру удаляет все лишнее, преобразовывает кириллический домен в пуникод и так далее
     * @param string $domain
     * @return string|null
     */
    private function normalizeDomain(string $domain): ?string
    {
        $domain = $this->removeExtraCharacters($domain);

        if ($domain === null) {
            return null;
        }

        if (str_starts_with($domain, 'xn--')) {
            $domain = idn_to_utf8($domain);
        }

        $this->raw = $domain;

        if ($this->isPunycode()) {
            $domain = idn_to_ascii($domain);
        }

        return mb_strtolower(trim((string) $domain), 'UTF-8');
    }

    /**
     * Функция удаляет все лишние значения, если после удаления домен кривой, возвращает null
     * @param string $domain
     * @return string|null
     */
    private function removeExtraCharacters(string $domain): ?string
    {
        $domain = trim($domain);
        $domain = trim($domain, '/');
        $domain = mb_strtolower($domain, 'UTF-8');
        $domain = str_replace(['\\', '..', '"', "'"], '', $domain);

        if (str_contains($domain, '/')) {
            $parsedDomain = parse_url($domain, PHP_URL_HOST);

            if ($parsedDomain == null) {
                $parsedDomain = explode('/', $domain)[0] ?? null;
            }

            $domain = $parsedDomain;
        }

        if (!str_contains($domain, '.')) {
            return null;
        }

        return $domain;
    }
}
