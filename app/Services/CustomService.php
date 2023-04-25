<?php


namespace App\Services;

use App\Models\Content;

class CustomService
{
    /**
     * this method returns en_Latn if locale is en
     * @return string
     */
    public function getLocale(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en') {
            $locale = 'en_Latn';
        }
        return $locale;
    }

    public function correctPhoneNumber($phone)
    {
        return match (true) {
            strlen($phone) == 12 => '+' . $phone,
            strlen($phone) > 13 => substr($phone, 0, 13),
            default => $phone,
        };
    }
}
