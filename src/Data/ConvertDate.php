<?php

namespace App\Data;

class ConvertDate
{

    public function getStartDayFr($date): string {
        $day = $date->format('D');
        switch ($day) {
            case 'Sun':
                return 'Dimanche';
            case 'Mon':
                return 'Lundi';
            case 'Tue':
                return 'Mardi';
            case 'Wed':
                return 'Mercredi';
            case 'Thu' :
                return 'Jeudi';
            case 'Fri':
                return 'Vendredi';
            case 'Sat':
                return 'Samedi';
        }
        return $day;
    }

    public function getStartMoisFr($date): string {
        $mois = $date->format('M');
        switch ($mois) {
            case 'Jan':
                return 'Janvier';
            case 'Feb':
                return 'Février';
            case 'Mar':
                return 'Mars';
            case 'Apr':
                return 'Avril';
            case 'May' :
                return 'Mai';
            case 'Jun':
                return 'Juin';
            case 'Jul':
                return 'Juillet';
            case 'Aug':
                return 'Août';
            case 'Sep':
                return 'Septembre';
            case 'Oct':
                return 'Octobre';
            case 'Nov':
                return 'Novembre';
            case 'Dec':
                return 'Décembre';
        }
        return $mois;
    }
}