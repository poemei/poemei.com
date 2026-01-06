<?php

declare(strict_types=1);

namespace plugins\holidays;

final class holiday
{
    public function get_message(?string $date = null): string
    {
        $date = $date ?? date('Y-m-d');
        $year = substr($date, 0, 4);

        switch ($date) {
            case $year . '-01-01': return 'Happy New Year';
            case $year . '-02-01': return 'Greetings on this fine Imbolc day';
            case $year . '-03-21': return 'Today is Ostara, the Spring Equinox';
            case $year . '-05-01': return 'Happy Beltane (May Day)';
            case $year . '-06-21': return 'Today is Litha, the Summer Solstice';
            case $year . '-08-01': return 'Greetings on this Lughnasadh (Lammas)';
            case $year . '-09-21': return 'Today is Mabon, the Autumn Equinox';
            case $year . '-10-30': return 'The Holidays Plugin is alive!';
            case $year . '-10-31': return 'Happy Halloween — today is Samhain';
            case $year . '-11-11': return 'To my fellow Veterans — I salute you.';
            case $year . '-11-27': return 'Happy Thanksgiving.';
            case $year . '-12-21': return 'Yule, the Winter Solstice begins';
            case $year . '-12-25': return 'A very Merry Christmas to you and yours';
            default: return '';
        }
    }
}

