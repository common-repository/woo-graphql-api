<?php

namespace WCGQL\Helpers;

class Country
{
    public static function getCountryByID($country_id)
    {
        if (!isset(WC()->countries->countries[$country_id])) {
            throw new \Exception("Invalid country id ($country_id)");
        }

        $country_name = WC()->countries->countries[$country_id];
        $country = [
            'country_id' => $country_id,
            'name' => $country_name,
            'iso_code_2' => $country_id,
            'iso_code_3' => self::getCountryIso3($country_id),
            'states' => array(),
            'calling_code' => WC()->countries->get_country_calling_code($country_id)
        ];

        if (!isset(WC()->countries->states[$country_id])) {
            return $country;
        }

        return $country;
    }

    public static function getCountryIso3($countryKey)
    {
        $jsonedCountriesCodes = '{"AF":"AFG","AL":"ALB","DZ":"DZA","AS":"ASM","AD":"AND","AO":"AGO","AI":"AIA","AQ":"ATA","AG":"ATG","AR":"ARG","AM":"ARM","AW":"ABW","AU":"AUS","AT":"AUT","AZ":"AZE","BS":"BHS","BH":"BHR","BD":"BGD","BB":"BRB","BY":"BLR","BE":"BEL","BZ":"BLZ","BJ":"BEN","BM":"BMU","BT":"BTN","BO":"BOL","BA":"BIH","BW":"BWA","BV":"BVT","BR":"BRA","IO":"IOT","VG":"VGB","BN":"BRN","BG":"BGR","BF":"BFA","BI":"BDI","KH":"KHM","CM":"CMR","CA":"CAN","CV":"CPV","KY":"CYM","CF":"CAF","TD":"TCD","CL":"CHL","CN":"CHN","CX":"CXR","CC":"CCK","CO":"COL","KM":"COM","CD":"COD","CG":"COG","CK":"COK","CR":"CRI","CI":"CIV","CU":"CUB","CY":"CYP","CZ":"CZE","DK":"DNK","DJ":"DJI","DM":"DMA","DO":"DOM","EC":"ECU","EG":"EGY","SV":"SLV","GQ":"GNQ","ER":"ERI","EE":"EST","ET":"ETH","FO":"FRO","FK":"FLK","FJ":"FJI","FI":"FIN","FR":"FRA","GF":"GUF","PF":"PYF","TF":"ATF","GA":"GAB","GM":"GMB","GE":"GEO","DE":"DEU","GH":"GHA","GI":"GIB","GR":"GRC","GL":"GRL","GD":"GRD","GP":"GLP","GU":"GUM","GT":"GTM","GN":"GIN","GW":"GNB","GY":"GUY","HT":"HTI","HM":"HMD","VA":"VAT","HN":"HND","HK":"HKG","HR":"HRV","HU":"HUN","IS":"ISL","IN":"IND","ID":"IDN","IR":"IRN","IQ":"IRQ","IE":"IRL","IL":"ISR","IT":"ITA","JM":"JAM","JP":"JPN","JO":"JOR","KZ":"KAZ","KE":"KEN","KI":"KIR","KP":"PRK","KR":"KOR","KW":"KWT","KG":"KGZ","LA":"LAO","LV":"LVA","LB":"LBN","LS":"LSO","LR":"LBR","LY":"LBY","LI":"LIE","LT":"LTU","LU":"LUX","MO":"MAC","MK":"MKD","MG":"MDG","MW":"MWI","MY":"MYS","MV":"MDV","ML":"MLI","MT":"MLT","MH":"MHL","MQ":"MTQ","MR":"MRT","MU":"MUS","YT":"MYT","MX":"MEX","FM":"FSM","MD":"MDA","MC":"MCO","MN":"MNG","MS":"MSR","MA":"MAR","MZ":"MOZ","MM":"MMR","NA":"NAM","NR":"NRU","NP":"NPL","AN":"ANT","NL":"NLD","NC":"NCL","NZ":"NZL","NI":"NIC","NE":"NER","NG":"NGA","NU":"NIU","NF":"NFK","MP":"MNP","NO":"NOR","OM":"OMN","PK":"PAK","PW":"PLW","PS":"PSE","PA":"PAN","PG":"PNG","PY":"PRY","PE":"PER","PH":"PHL","PN":"PCN","PL":"POL","PT":"PRT","PR":"PRI","QA":"QAT","RE":"REU","RO":"ROU","RU":"RUS","RW":"RWA","SH":"SHN","KN":"KNA","LC":"LCA","PM":"SPM","VC":"VCT","WS":"WSM","SM":"SMR","ST":"STP","SA":"SAU","SN":"SEN","CS":"SCG","SC":"SYC","SL":"SLE","SG":"SGP","SK":"SVK","SI":"SVN","SB":"SLB","SO":"SOM","ZA":"ZAF","GS":"SGS","ES":"ESP","LK":"LKA","SD":"SDN","SR":"SUR","SJ":"SJM","SZ":"SWZ","SE":"SWE","CH":"CHE","SY":"SYR","TW":"TWN","TJ":"TJK","TZ":"TZA","TH":"THA","TL":"TLS","TG":"TGO","TK":"TKL","TO":"TON","TT":"TTO","TN":"TUN","TR":"TUR","TM":"TKM","TC":"TCA","TV":"TUV","VI":"VIR","UG":"UGA","UA":"UKR","AE":"ARE","GB":"GBR","UM":"UMI","US":"USA","UY":"URY","UZ":"UZB","VU":"VUT","VE":"VEN","VN":"VNM","WF":"WLF","EH":"ESH","YE":"YEM","ZM":"ZMB","ZW":"ZWE"}
';
        $arrayCountriesCode = json_decode($jsonedCountriesCodes, true);
        return isset($arrayCountriesCode[$countryKey])? $arrayCountriesCode[$countryKey] : null;
    }
}
